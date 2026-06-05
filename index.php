<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Craitor — AI Token Calculator</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php
// Load configurations on page load so JavaScript can use them
function loadIgnoreList($file) {
    if (!file_exists($file)) return [];
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function loadExtensions($file) {
    if (!file_exists($file)) return ['txt', 'php', 'js', 'html', 'py']; // defaults
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $exts = [];
    foreach ($lines as $line) {
        $line = strtolower(trim($line));
        $line = ltrim($line, '.');
        if ($line !== '') $exts[] = $line;
    }
    return $exts;
}

function loadModels($csvFile) {
    $models = [];
    if (!file_exists($csvFile)) return $models;
    if (($handle = fopen($csvFile, "r")) !== false) {
        fgetcsv($handle); 
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $models[] = ["name" => $row[0], "input" => floatval($row[1]), "output" => floatval($row[2])];
            }
        }
        fclose($handle);
    }
    return $models;
}

$ignoreList = loadIgnoreList("ignore_folder.txt");
$allowedExt = loadExtensions("extension.txt");
?>

<div class="container">
    <div class="card">
        <h1>Craitor</h1>
        <p>AI Token Cost Calculator</p>

        <form method="post" id="analyzerForm">
            Select Folder:<br>
            <label class="file-label">
                📁 Choose Directory...
                <input type="file" id="folderPicker" webkitdirectory directory multiple style="display:none;" required>
            </label>
            <div id="statusMessage"></div>
            <br>

            Output Multiplier (Approximate):<br>
            <input type="number" step="0.1" name="multiplier" id="multiplier" value="<?php echo isset($_POST['multiplier']) ? htmlspecialchars($_POST['multiplier']) : '1.5'; ?>"><br><br>

            <input type="hidden" name="js_total_chars" id="js_total_chars">
            <input type="hidden" name="js_total_words" id="js_total_words">
            <input type="hidden" name="js_file_count" id="js_file_count">
            <input type="hidden" name="js_folder_name" id="js_folder_name">

            <button type="submit" id="submitBtn" disabled style="background:#94a3b8; cursor:not-allowed;">Analyze Folder</button>
        </form>
    </div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['js_total_chars'])) {
    $totalChars = intval($_POST['js_total_chars']);
    $totalWords = intval($_POST['js_total_words']);
    $fileCount  = intval($_POST['js_file_count']);
    $folderName = htmlspecialchars($_POST['js_folder_name']);
    $multiplier = floatval($_POST['multiplier']);

    $inputTokens = $totalChars / 4;
    $outputTokens = $inputTokens * $multiplier;

    echo "<div class='card'>";
    echo "<h3>Folder Statistics ({$folderName})</h3>";
    echo "<div class='stat'>Files scanned: " . number_format($fileCount) . "</div>";
    echo "<div class='stat'>Words: " . number_format($totalWords) . "</div>";
    echo "<div class='stat'>Est. Input tokens: " . number_format($inputTokens) . "</div>";
    echo "<div class='stat'>Est. Output tokens: " . number_format($outputTokens) . "</div>";
    echo "</div>";

    $models = loadModels("model_list.csv");

    if (!$models) {
        echo "<div class='card error'>No models found or model_list.csv missing.</div>";
    } else {
        $results = []; $labels = []; $data = [];

        echo "<div class='card'>";
        echo "<h3>Model Comparison</h3>";
        echo "<table><tr><th>Model</th><th>Total Cost ($)</th></tr>";

        foreach ($models as $m) {
            $costIn = ($inputTokens / 1000000) * $m['input'];
            $costOut = ($outputTokens / 1000000) * $m['output'];
            $total = $costIn + $costOut;

            echo "<tr><td>" . htmlspecialchars($m['name']) . "</td><td>$" . number_format($total, 4) . "</td></tr>";
            $results[] = ["model" => $m['name'], "total_cost" => $total];
            $labels[] = $m['name'];
            $data[] = $total;
        }
        echo "</table>";

        $exportDir = "./result_json";
        if (!is_dir($exportDir)) mkdir($exportDir, 0755, true);

        $export = ["folder" => $folderName, "input_tokens" => $inputTokens, "output_tokens" => $outputTokens, "models" => $results];
        $exportFile = $exportDir . "/report_" . bin2hex(random_bytes(8)) . ".json";
        file_put_contents($exportFile, json_encode($export, JSON_PRETTY_PRINT));

        echo "<br><a href='" . htmlspecialchars($exportFile) . "' download>⬇ Download JSON Report</a>";
        echo "<canvas id='chart'></canvas>";
        echo "</div>";
        ?>
        <script>
        new Chart(document.getElementById('chart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{ label: 'Cost ($)', data: <?php echo json_encode($data); ?>, backgroundColor: '#2563eb', borderRadius: 4 }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
        </script>
        <?php
    }
}
?>

    <div class="footer">Craitor — AI Cost Estimator<br>dtr-kalfer 2026 © License: MIT</div>
</div>

<script>
// Pass PHP configurations safely into JavaScript arrays
const allowedExtensions = <?php echo json_encode($allowedExt); ?>;
const ignoreList = <?php echo json_encode($ignoreList); ?>;

document.getElementById('folderPicker').addEventListener('change', async function(e) {
    const files = e.target.files;
    if (files.length === 0) return;

    const statusMsg = document.getElementById('statusMessage');
    const submitBtn = document.getElementById('submitBtn');
    
    statusMsg.innerText = "Processing folder metrics client-side...";
    submitBtn.disabled = true;
    submitBtn.style.backgroundColor = "#94a3b8";
    submitBtn.style.cursor = "not-allowed";

    let totalChars = 0;
    let totalWords = 0;
    let matchedFileCount = 0;
    let rootFolderName = files[0].webkitRelativePath.split('/')[0];

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const relativePath = file.webkitRelativePath;

        // 1. Check Ignore List
        let shouldIgnore = false;
        for (let ignore of ignoreList) {
            if (relativePath.toLowerCase().includes(ignore.toLowerCase())) {
                shouldIgnore = true;
                break;
            }
        }
        if (shouldIgnore) continue;

        // 2. Check Extension Matching
        const ext = relativePath.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(ext)) continue;

        matchedFileCount++;
        totalChars += file.size; // 1 byte rough proxy for 1 char in text files

        // 3. Count words via text reading
        const text = await file.text();
        const words = text.trim().split(/\s+/);
        totalWords += (words[0] === "") ? 0 : words.length;
    }

    // Populate hidden forms
    document.getElementById('js_total_chars').value = totalChars;
    document.getElementById('js_total_words').value = totalWords;
    document.getElementById('js_file_count').value = matchedFileCount;
    document.getElementById('js_folder_name').value = rootFolderName;

    statusMsg.innerHTML = `✅ Loaded <strong>${rootFolderName}</strong> (${matchedFileCount} valid files out of ${files.length} found).`;
    submitBtn.disabled = false;
    submitBtn.style.backgroundColor = "#2563eb";
    submitBtn.style.cursor = "pointer";
});
</script>
</body>
</html>