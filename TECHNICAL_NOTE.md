## 📝 Academic Abstract

> **Craitor: A Lightweight, Client-Side Token and Cost Estimation Tool for Large Language Model (LLM) Datasets**
> 
> **Abstract:** 
> As Large Language Model (LLM) deployments scale across research and commercial domains, predicting API transaction expenses remains a critical step in budgetary planning and dataset curation. _Craitor_, an open-source, lightweight web application designed to compute precise text statistics and project inferential costs across a variety of commercial and open-source foundation models. Utilizing HTML5 directory streaming, _Craitor_ processes localized multi-file directories directly within the client browser environment, mitigating privacy vulnerabilities and eliminating the execution overhead typical of server-side data ingestion. By bridging raw textual footprints (character and word distributions) to custom pricing matrix parameters, the utility enables researchers and developers to simulate cost-benefit trade-offs, approximate contextual expansions via dynamic output multipliers, and export standard JSON reports for reproducible computational pipelines.

### Overview

**Craitor** is a lightweight, privacy-focused AI Token and Cost Estimator designed to audit multi-file codebases, textual corpora, or research datasets before running them through Large Language Model (LLM) APIs. By processing file trees directly in the browser runtime, it allows users to safely audit local directories without transferring data to a remote host.

### Key Features

- **Zero-Upload Client-Side Processing:** Integrates HTML5 `webkitdirectory` streaming to parse, tokenize, and count directory files locally, ensuring absolute data privacy.
    
- **Dynamic Multi-Model Analysis:** Imports a standardized pricing matrix (`model_list.csv`) to simultaneously calculate and contrast input/output costs across multiple LLM providers per million tokens.
    
- **Contextual Expansion Simulation:** Features a variable output multiplier to account for anticipated agentic loops, chain-of-thought processing, or generation lengths.
    
- **Visual Cost Mapping:** Renders a responsive comparison chart using Chart.js to help researchers identify the most economically viable architecture at a glance.
    
- **Reproducible Export Artifacts:** Generates unique, cryptographically named JSON evaluation reports for structural auditing and compliance reporting.
    

### Technical Implementation

- **Language:** PHP 8.x (Backend Evaluation & Report Routing), Vanilla JavaScript (Asynchronous Stream Processing)
    
- **Frontend Components:** Tailwind/Modern CSS Foundations, Chart.js CDN
    
- **Data Sources:** Configuration lists via flat text files (`ignore_folder.txt`, `extension.txt`) and CSV matrices (`model_list.csv`).