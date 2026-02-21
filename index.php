<!DOCTYPE html>
<html>
<head>
<title>Craitor — AI Token Calculator</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body {
    font-family: Arial, sans-serif;
    background:#f4f6f8;
    margin:0;
}

.container {
    max-width:1100px;
    margin:auto;
    padding:20px;
}

.card {
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
    margin-bottom:20px;
}

h1 { margin-top:0; }

input, button {
    padding:10px;
    margin-top:5px;
    width:100%;
    max-width:400px;
}

button {
    background:#2563eb;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

button:hover {
    background:#1d4ed8;
}

table {
    border-collapse:collapse;
    width:100%;
    margin-top:15px;
}

th, td {
    padding:10px;
    border-bottom:1px solid #ddd;
    text-align:left;
}

th {
    background:#f1f5f9;
}

.stat {
    font-size:14px;
    margin:4px 0;
}

canvas {
    margin-top:20px;
}

.footer {
    text-align:center;
    font-size:12px;
    color:#777;
    margin-top:30px;
}

</style>
</head>
<body>

<div class="container">

<div class="card">
<h1>Craitor</h1>
<p>AI Token Cost Calculator</p>

<form method="post">

Folder Path:<br>
<input type="text" name="path" required><br>

Output Multiplier (Approximate):<br>
<input type="number" step="0.1" name="multiplier" value="1.5">

<br><br>
<button type="submit">Analyze Folder</button>

</form>
</div>

<?php

function loadModels($csvFile) {

    $models = [];

    if (!file_exists($csvFile)) return $models;

    $handle = fopen($csvFile, "r");
    fgetcsv($handle);

    while (($row = fgetcsv($handle)) !== false) {
        $models[] = [
            "name"=>$row[0],
            "input"=>floatval($row[1]),
            "output"=>floatval($row[2])
        ];
    }

    fclose($handle);
    return $models;
}

function loadIgnoreList($file) {
    if (!file_exists($file)) return [];
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function scanFolder($dir, $ignoreList, $allowedExt) {

    $files = [];

		

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );

    foreach ($iterator as $file) {

        if (!$file->isFile()) continue;

        $filePath = $file->getPathname();

        // Ignore folders
        foreach ($ignoreList as $ignore) {
            if (stripos($filePath, $ignore) !== false) {
                continue 2;
            }
        }

        $ext = strtolower($file->getExtension());

        if (in_array($ext, $allowedExt)) {
            $files[] = $filePath;
        }
    }

    return $files;
}

function loadExtensions($file) {

    if (!file_exists($file)) return [];

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $exts = [];

    foreach ($lines as $line) {
        $line = strtolower(trim($line));
        $line = ltrim($line, '.');
        if ($line !== '') {
            $exts[] = $line;
        }
    }

    return $exts;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $path = $_POST['path'];
    $multiplier = floatval($_POST['multiplier']);

    if (!is_dir($path)) {
        echo "<div class='card'>Invalid path</div>";
        exit;
    }

    $ignoreList = loadIgnoreList("ignore_folder.txt");
		$allowedExt = loadExtensions("extension.txt");

		if (!$allowedExt) {
				echo "<div class='card'>No extensions defined.</div>";
				exit;
		}
		
		echo "<div class='stat'>Scanned extensions: ".implode(', ', $allowedExt)."</div>";
		
		$files = scanFolder($path, $ignoreList, $allowedExt);
		
    $totalChars = 0;
    $totalWords = 0;

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $totalChars += strlen($content);
        $totalWords += str_word_count($content);
    }

    $inputTokens = $totalChars / 4;
    $outputTokens = $inputTokens * $multiplier;

    echo "<div class='card'>";
    echo "<h3>Folder Statistics</h3>";
    echo "<div class='stat'>Files scanned: ".count($files)."</div>";
    echo "<div class='stat'>Words: ".number_format($totalWords)."</div>";
    echo "<div class='stat'>Input tokens: ".number_format($inputTokens)."</div>";
    echo "<div class='stat'>Output tokens: ".number_format($outputTokens)."</div>";
    echo "</div>";

    $models = loadModels("model_list.csv");

    if (!$models) {
        echo "<div class='card'>No models found.</div>";
        exit;
    }

    $results = [];
    $labels = [];
    $data = [];

    echo "<div class='card'>";
    echo "<h3>Model Comparison</h3>";
		echo "<table>";
    echo "<tr><th>Model</th><th>Total Cost ($)</th></tr>";

    foreach ($models as $m) {

        $costIn = ($inputTokens / 1000000) * $m['input'];
        $costOut = ($outputTokens / 1000000) * $m['output'];
        $total = $costIn + $costOut;

        echo "<tr><td>{$m['name']}</td><td>$".number_format($total,4)."</td></tr>";

        $results[] = ["model"=>$m['name'], "total_cost"=>$total];
        $labels[] = $m['name'];
        $data[] = $total;
    }

    echo "</table>";

    // JSON export
    $export = [
        "folder"=>$path,
        "input_tokens"=>$inputTokens,
        "output_tokens"=>$outputTokens,
        "models"=>$results
    ];

    $file = "./result_json/report_".time().".json";
    file_put_contents($file, json_encode($export, JSON_PRETTY_PRINT));

    echo "<br><a href='$file'>⬇ Download JSON Report</a>";

    echo "<canvas id='chart'></canvas>";

    echo "</div>";

    ?>

<script>
new Chart(document.getElementById('chart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Cost ($)',
            data: <?php echo json_encode($data); ?>
        }]
    }
});
</script>

<?php } ?>

<div class="footer">
Craitor — AI Cost Estimator<br>
dtr-kalfer 2026 © License: MIT
</div>

</div>
</body>
</html>