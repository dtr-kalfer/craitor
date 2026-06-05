# Craitor — AI Token Cost Calculator

Craitor is a lightweight, configurable AI token and cost estimation tool built with PHP.
It scans folders, estimates token usage, compares multiple AI models, and visualizes cost projections.

This tool is designed for developers, AI experimenters, and automation builders who want to understand **approximate AI usage cost before running prompts**.

🛠 The tool gives a rough idea how much the AI bill would cost.

## ⚡ The Client-Side JavaScript Way

Instead of forcing PHP to scan your hard drive, JavaScript can scan the folder right inside the user's browser. This is incredibly fast, keeps your files private (they are never uploaded to a server), and completely bypasses PHP memory or file upload limits. JavaScript handles the file picking, calculates the characters and words, and passes those numbers to your PHP backend to handle the pricing matrix and charts.

Perfect for:

* AI assistants (Agents, automation)
* Code refactoring experiments
* Prompt engineering tests
* Repository analysis
* Dataset ingestion planning
* Budget estimation

---

## ✨ Features

* 📂 Folder scanning with recursive file discovery
* 🔎 Configurable file extensions (`extension.txt`)
* 🚫 Ignore folders (`ignore_folder.txt`)
* 🤖 Multi-model comparison (`model_list.csv`)
* 💰 Cost calculation per million tokens
* 📊 Chart.js visualization
* 📄 JSON export for analytics
* ⚡ Lightweight — no database required
* 🛠 Fully configurable without editing PHP code
* 🛠 App is now portable (Client Side Javascript), it can be used on a server locally or on a public setup.

---

## 🧠 How Token Estimation Works

Craitor uses a simple approximation:

```
tokens ≈ characters / 4
```

This provides a practical estimate typically within **±15–25%** of real tokenizer counts, which is sufficient for budgeting and planning purposes.

---

## 📁 Project Structure

```txt
craitor/
├── 📙 CHANGELOG.md
├── 📙 LICENSE
├── 📙 README.md
├── 📙 extension.txt
├── 📙 ignore_folders.txt
├── 📙 index.php
├── 📙 model_list.csv
└── 📙 style.css
```

---

## ⚙️ Configuration Files

### model_list.csv

Defines AI models and pricing (per million tokens).

```
model,input_cost_per_million,output_cost_per_million
GPT-4o,5,15
Claude 3.5 Sonnet,3,15
DeepSeek Chat,0.27,1.10
```

You can customize or add your own providers.

---

### extension.txt

Controls which file types are scanned.

```
txt
php
html
md
json
js
css
py
```

One extension per line. No dot required.

---

### ignore_folder.txt

Folders or paths to exclude from scanning.

```
node_modules
vendor
.git
cache
logs
tmp
.obsidian
```

If a path contains any entry, it will be ignored.

---

## 🚀 Installation

1. Clone the repository:

```
git clone https://github.com/dtr-kalfer/craitor.git
```

2. Place inside your PHP server directory:

```
/var/www/html/craitor
```

3. Ensure PHP has permission to write JSON reports.

4. Open in browser:

```
http://localhost/craitor
```

---

## 🧪 Example Use Cases

### AI Assistant Cost Estimation

Before running prompts:

* Code explanation
* Refactoring
* Documentation generation
* Automation agents

You can estimate total cost.

---

### Repository Analysis

Estimate how expensive it would be to:

* Feed an entire Git repository into an LLM
* Run code summarization
* Perform AI audits

---

### Prompt Engineering Optimization

Compare cost differences between:

* Full context
* Reduced context
* Summarized inputs

---

### AI Budget Planning

Predict operational costs for:

* Daily automation
* Scheduled jobs
* AI pipelines
* Agent workflows

---

## 📊 Output

Craitor produces:

* Model comparison table
* Cost visualization chart
* Downloadable JSON report

![sample output](./readme_assets/output_tokens2.avif)

JSON output can be used with:

* Chart.js
* Dashboards
* Analytics tools
* Automation scripts

---

## 🎯 Why Craitor Exists

AI experimentation often fails because developers do not know the cost beforehand.

Craitor helps answer:

> “How expensive will this prompt or project be?”

before you spend real money.

---

## 🔮 Future Ideas

Possible enhancements:

* Use Real Tokenizers: Instead of dividing characters by 4.

---

## 🙏 Credits & Inspiration

This project was inspired by the powerful AI agent workflow made possible through **Picoclaw**.

Charts and data visualization are implemented using **Chart.js**, an open-source JavaScript charting library.

* Picoclaw: https://github.com/sipeed/picoclaw
* Chart.js: https://www.chartjs.org/

Special thanks to the open-source community for providing amazing tools that make AI projects possible.

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## ❤️ A Note

This project was created to help developers explore AI safely and thoughtfully.

May it help you in your AI journey.

---

Craitor — AI Cost Estimator

Copyright (c) 2026 Ferdinand Tumulak / License: MIT
