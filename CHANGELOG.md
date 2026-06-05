[1.4.1] - 2026-6-5
- Added javascript (Choose directory..), JavaScript can scan the folder right inside the user's browser.
- improved memory efficiency using $file->getSize() instead.
- strlen() will overcount, use alternate mb_strlen()
- bin2hex(random_bytes(8)) or uniqid() to allow multi user input (LAN)
- RecursiveIteratorIterator::CATCH_EXCEPTION flag so your application handles locked folders gracefully.

[1.0.0] - 2026-02-22
🎉 Initial Stable Release

First public release of Craitor — AI Token Cost Calculator.

✨ Added

- Folder scanning with recursive file discovery
- Token estimation based on:
- Word count
- File size approximation
- Configurable extensions via extension.txt
- Folder ignore system via ignore_folders.txt
- AI model pricing loaded from model_list.csv
- Automatic multi-model comparison table
- Cost calculation using price per million tokens
- JSON export of results for external tools
- Chart visualization using Chart.js
- Styled web interface (PHP + HTML)

Summary statistics:

- Total files scanned
- Total words
- Estimated tokens
- Estimated input/output costs
- GitHub banner and documentation assets

📊 Visualization

- Cost comparison charts across multiple AI models
- Ready-to-use JSON output for dashboards and analytics

⚙️ Configuration

- User-editable model pricing (CSV)
- Custom file extension filtering
- Custom ignore folder rules

🎯 Use Cases

- AI prompt cost estimation before sending to APIs
- Repository cost planning for LLM refactoring
- Token budgeting for AI agents (e.g., Picoclaw workflows)
- Education and experimentation with AI pricing models

🙏 Credits

- Inspired by Picoclaw AI agent workflows
- Visualization powered by Chart.js