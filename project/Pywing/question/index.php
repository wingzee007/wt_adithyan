<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Python Universe | Interactive Learning</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.min.css">
  <script src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/prismjs/components/prism-python.min.js"></script>

  <style>
    /* ===============================
        CYBER GALAXY THEME
        =============================== */
    :root {
      --space-black: #02080e;
      --neon-blue: #00d2ff;
      --neon-gold: #ffaa00;
      --glass-bg: rgba(13, 20, 30, 0.7);
      --border-glow: rgba(0, 210, 255, 0.3);
    }

    body {
      margin: 0;
      height: 100vh;
      color: #e0f2ff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow: hidden;
      background-color: var(--space-black);
    }

    /* Starfield Background Canvas */
    #starfield {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: radial-gradient(circle at center, #0a192f 0%, #02080e 100%);
    }

    .app {
      display: flex;
      height: 100vh;
      width: 100%;
      background: transparent;
    }

    /* ===============================
        PANELS (Glassmorphism)
        =============================== */
    .left-panel {
      width: 55%;
      padding: 30px;
      overflow-y: auto;
      border-right: 1px solid var(--border-glow);
      background: rgba(2, 8, 14, 0.4);
      backdrop-filter: blur(4px);
    }

    .right-panel {
      width: 45%;
      padding: 25px;
      backdrop-filter: blur(15px);
      display: flex;
      flex-direction: column;
      box-shadow: -10px 0 40px rgba(0,0,0,0.7);
      background: var(--glass-bg);
    }

    h1 {
      font-size: 1.8rem;
      text-transform: uppercase;
      letter-spacing: 4px;
      color: var(--neon-blue);
      text-shadow: 0 0 15px rgba(0, 210, 255, 0.6);
      margin-bottom: 25px;
      text-align: center;
    }

    /* ===============================
        COMPONENTS
        =============================== */
    .search-container {
      margin-bottom: 25px;
    }

    #searchBox {
      width: 100%;
      box-sizing: border-box;
      padding: 14px 20px;
      background: rgba(0, 0, 0, 0.6);
      border: 1px solid var(--border-glow);
      border-radius: 50px;
      color: white;
      outline: none;
      font-size: 1rem;
      transition: 0.3s;
    }

    #searchBox:focus {
      border-color: var(--neon-blue);
      box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
    }

    details {
      background: rgba(0, 0, 0, 0.6);
      border: 1px solid rgba(253, 121, 5, 0.5);
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 15px;
      transition: 0.3s;
    }

    details[open] {
      border-color: var(--neon-blue);
      background: rgba(0, 210, 255, 0.1);
      box-shadow: 0 0 20px rgba(0, 210, 255, 0.1);
    }

    summary {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--neon-gold);
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      list-style: none;
    }

    .topic-count {
      opacity: 0.4;
      font-family: monospace;
      margin-right: 10px;
    }

    /* ===============================
        BUTTONS
        =============================== */
    .desc-btn {
      background: rgba(0, 210, 255, 0.1);
      border: 1px solid var(--neon-blue);
      color: var(--neon-blue);
      padding: 6px 14px;
      border-radius: 20px;
      cursor: pointer;
      font-size: 0.75rem;
      font-weight: bold;
      transition: 0.2s;
    }

    .desc-btn:hover {
      background: var(--neon-blue);
      color: black;
      box-shadow: 0 0 15px var(--neon-blue);
    }

    .run-btn {
      margin: 15px 0;
      padding: 14px;
      background: linear-gradient(45deg, #03cc21ff, #0072ff);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      text-transform: uppercase;
      cursor: pointer;
      letter-spacing: 2px;
      transition: 0.3s;
    }

    .run-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 25px rgba(0, 210, 255, 0.5);
    }

    /* ===============================
        EDITOR & TERMINAL
        =============================== */
    #code {
      flex: 1;
      background: rgba(5, 10, 15, 0.8);
      color: #00ffcc;
      border: 2px solid var(--neon-blue);
      border-radius: 10px;
      padding: 18px;
      font-family: 'Fira Code', Consolas, monospace;
      font-size: 15px;
      outline: none;
      resize: none;
    }

    #output {
      height: 160px;
      background: rgba(0, 0, 0, 0.8);
      border-radius: 10px;
      padding: 15px;
      font-family: 'Fira Code', monospace;
      color: #00ff41;
      border-left: 4px solid var(--neon-blue);
      overflow-y: auto;
      white-space: pre-wrap;
    }

    .copy-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: var(--neon-blue);
      color: black;
      border: none;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.7rem;
      font-weight: bold;
      cursor: pointer;
      z-index: 5;
    }

    /* ===============================
        MODAL
        =============================== */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.8);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(8px);
    }

    .modal-content {
      background: #0a1018;
      padding: 30px;
      width: 90%;
      max-width: 500px;
      border-radius: 20px;
      border: 1px solid var(--neon-blue);
      box-shadow: 0 0 50px rgba(0, 210, 255, 0.4);
    }

    .close-btn {
      float: right;
      color: #ff4d4d;
      font-size: 1.5rem;
      cursor: pointer;
    }

    .resizer {
      width: 5px;
      background: rgba(255,255,255,0.05);
      cursor: col-resize;
      transition: 0.3s;
    }
    .resizer:hover { background: var(--neon-blue); }

    /* ===============================
        BACK BUTTON
        =============================== */
    .back-btn {
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 10000;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 20px;
      background: rgba(2, 8, 14, 0.8);
      backdrop-filter: blur(12px);
      border: 1px solid var(--neon-blue);
      border-radius: 50px;
      color: var(--neon-blue);
      text-decoration: none;
      font-weight: bold;
      font-size: 0.8rem;
      letter-spacing: 1.5px;
      transition: 0.4s;
      animation: pulseGlow 2s infinite;
    }

    .back-btn:hover {
      background: var(--neon-blue);
      color: #000;
      box-shadow: 0 0 25px rgba(0, 210, 255, 0.6);
    }

    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 10px rgba(0, 210, 255, 0.2); }
      50% { box-shadow: 0 0 20px rgba(0, 210, 255, 0.5); }
    }

    @media (max-width: 768px) {
      .app { flex-direction: column; }
      .left-panel, .right-panel { width: 100% !important; height: 50%; }
      .resizer { display: none; }
    }
  </style>
</head>
<body>
  <canvas id="starfield"></canvas>

  <a href="../index.html" class="back-btn">
    <span class="text">RETURN TO HUB</span>
  </a>

  <div class="app">
    <div class="left-panel">
      <h1>Python Universe</h1>
      <div class="search-container">
        <input type="text" id="searchBox" placeholder="🔍 Search the knowledge base...">
      </div>
      <div id="topics"></div>
    </div>

    <div class="resizer" id="resizer"></div>

    <div class="right-panel">
      <h2 style="color:var(--neon-blue); margin:0 0 15px 0; font-size:1.2rem;">KODER CONSOLE</h2>
      <textarea id="code"># Type your Python code here
print("Welcome to the Python Learning Universe!")</textarea>
      <button class="run-btn" onclick="runCode()">Run Script</button>
      <div id="output">Terminal Ready...</div>
    </div>
  </div>

  <div id="descModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">×</span>
      <h3 style="color:var(--neon-blue); margin-top:0;">Description</h3>
      <p id="modalDesc" style="line-height:1.6;"></p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/pyodide/v0.25.0/full/pyodide.js"></script>

  <script>
    /* ===============================
        GALAXY ANIMATION ENGINE
        =============================== */
    const canvas = document.getElementById('starfield');
    const ctx = canvas.getContext('2d');
    let stars = [];

    function initStars() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      stars = [];
      for (let i = 0; i < 200; i++) {
        stars.push({
          x: Math.random() * canvas.width,
          y: Math.random() * canvas.height,
          size: Math.random() * 1.5,
          speed: Math.random() * 0.5
        });
      }
    }

    function drawStars() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = "white";
      stars.forEach(star => {
        ctx.beginPath();
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
        ctx.fill();
        star.y += star.speed;
        if (star.y > canvas.height) star.y = 0;
      });
      requestAnimationFrame(drawStars);
    }

    window.addEventListener('resize', initStars);
    initStars();
    drawStars();

    /* ===============================
        DATA & LOGIC (PRESERVED)
        =============================== */
    let allTopics = [];

    fetch("get_topics.php")
    .then(res => res.json())
    .then(data => {
      allTopics = data;
      renderTopics(allTopics);
    }).catch(err => {
      console.log("PHP Fetch failed, loading demo data.");
      allTopics = [{title: "Demo Topic", description: "This is what a description looks like.", code: "print('Hello Galaxy!')"}];
      renderTopics(allTopics);
    });

    function renderTopics(topics) {
      const container = document.getElementById("topics");
      container.innerHTML = "";

      topics.forEach((topic, index) => {
        const details = document.createElement("details");
        const summary = document.createElement("summary");
        
        const safeDesc = (topic.description || "No description provided.")
                          .replace(/`/g, "\\`").replace(/\$/g, "\\$");

        summary.innerHTML = `
          <span><span class="topic-count">${String(index+1).padStart(2, '0')}</span> ${topic.title}</span>
          <button class="desc-btn" onclick="openModal(event, \`${safeDesc}\`)">INFO</button>
        `;

        const pre = document.createElement("pre");
        pre.style.position = "relative";
        const codeElement = document.createElement("code");
        codeElement.className = "language-python";
        codeElement.textContent = topic.code;

        const copyBtn = document.createElement("button");
        copyBtn.className = "copy-btn";
        copyBtn.textContent = "COPY";
        copyBtn.onclick = (e) => {
          e.preventDefault();
          navigator.clipboard.writeText(topic.code);
          copyBtn.textContent = "DONE";
          setTimeout(() => copyBtn.textContent = "COPY", 1000);
        };

        pre.appendChild(copyBtn);
        pre.appendChild(codeElement);
        details.appendChild(summary);
        details.appendChild(pre);
        container.appendChild(details);
      });
      Prism.highlightAll();
    }

    function openModal(event, text) {
      event.preventDefault(); 
      event.stopPropagation();
      document.getElementById("modalDesc").textContent = text;
      document.getElementById("descModal").style.display = "flex";
    }

    function closeModal() {
      document.getElementById("descModal").style.display = "none";
    }

    let pyodideReady = false;
    let pyodide;
    async function loadPy() {
      pyodide = await loadPyodide();
      pyodideReady = true;
    }
    loadPy();

    async function runCode() {
      const output = document.getElementById("output");
      if(!pyodideReady) { output.textContent = "Engine warming up..."; return; }
      output.textContent = "Running code in the vacuum...";
      try {
        pyodide.runPython(`import sys, io; sys.stdout = io.StringIO()`);
        await pyodide.runPythonAsync(document.getElementById("code").value);
        output.textContent = pyodide.runPython("sys.stdout.getvalue()") || "> Success (No output)";
      } catch (err) {
        output.textContent = "SYSTEM ERROR:\n" + err;
      }
    }

    document.getElementById("searchBox").addEventListener("input", function() {
      const query = this.value.toLowerCase();
      const filtered = allTopics.filter(t => t.title.toLowerCase().includes(query));
      renderTopics(filtered);
    });

    const resizer = document.getElementById("resizer");
    const left = document.querySelector(".left-panel");
    const right = document.querySelector(".right-panel");
    let isDragging = false;

    resizer.addEventListener("mousedown", () => isDragging = true);
    document.addEventListener("mouseup", () => isDragging = false);
    document.addEventListener("mousemove", e => {
      if (!isDragging) return;
      const percent = (e.clientX / window.innerWidth) * 100;
      if (percent > 20 && percent < 80) {
        left.style.width = percent + "%";
        right.style.width = (100 - percent) + "%";
      }
    });
  </script>
</body>
</html>
