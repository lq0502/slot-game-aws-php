<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スロットゲーム</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #121212;
            background-image: url('bg.gif');
            background-size: 405px 720px;
            background-repeat: no-repeat;
            background-position: center;
            color: white;
        }
        .game-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .instructions {
            position: absolute;
            left: 28px;
        }
        .instructions h3 {
            color: #FFD700;
            text-align: center;
            margin-top: 0;
            border-bottom: 1px solid #FFD700;
            padding-bottom: 10px;
        }
        .instructions ul {
            padding-left: 20px;
            margin-bottom: 15px;
        }
        .instructions li {
            margin-bottom: 8px;
        }
        .instructions-right {
            position: absolute;
            right: 15px; 
            height: 351.3px;
            text-align: left;
        }
        .instructions-right h3 {
            color: #FFD700;
            text-align: center;
            margin-top: 0;
            border-bottom: 1px solid #FFD700;
            padding-bottom: 10px;
        }
        .instructions-right ul {
            padding-left: 20px;
            margin-bottom: 15px;
        }
        .instructions-right li {
            margin-bottom: 8px;
        }
        .prize-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .prize-table th, .prize-table td {
            border: 1px solid #FFD700;
            padding: 5px;
            text-align: center;
        }
        .prize-table th {
            background-color: rgba(255, 215, 0, 0.2);
        }
        .controls-info {
            margin-top: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 8px;
            border-radius: 5px;
            text-align: center;
        }
        .slot-machine {
            position: relative;
            width: 340px;
            height: 400px;
            padding: 20px;
            text-align: center;
        }
        .slot-machine h2 {
            font-size: 30px;     
            color: white;          
            text-shadow:
                    -1px -1px 0 #000,
                    1px -1px 0 #000,
                    -1px 1px 0 #000,
                    1px 1px 0 #000;    
        }
        .light-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .light {
            position: absolute;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #444;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }
        .light.active {
            background-color: #FFFF00;
            box-shadow: 0 0 10px rgba(255, 255, 0, 0.8);
        }
        .reels-container {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            background-color: white;
            padding: 10px;
            border-radius: 10px;
            height: 100px;
        }
        .reel {
            width: 100px;
            height: 100px;
            background-color: #f0f0f0;
            border: 2px solid #333;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        .reel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 40%; 
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.3) 60%, transparent 100%);
            pointer-events: none;
        }
        .reel::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.3) 60%, transparent 100%);
            pointer-events: none;
        }
        .reel-items {
            position: absolute;
            width: 100%;
            transition: top 3s cubic-bezier(0.4, 0.0, 0.2, 1);
        }
        .reel-item {
            width: 100%;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 48px;
            box-sizing: border-box;
        }
        .controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        .spin-button {
            padding: 10px 40px;
            font-size: 24px;
            background-color: #FFD700;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            box-shadow: 0 4px 0 #d4af37; 
        }
        .spin-button:hover {
            background-color: #FFC107;
        }
        .spin-button:active {
            transform: translateY(4px); 
            box-shadow: none; 
        }
        .spin-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .message {
            margin-top: 15px;
            height: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #FFD700;
        }
        .cat-gif {
            margin-top: 10px;
            width: 100px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        #visualizer {
            position: fixed;
            bottom: 0;
            left: 437px;
            width: 44.6%;
            height: 70px;
            background: rgba(0, 0, 0, 0);
            z-index: 9999;
        }
        #particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10000;
        }
    </style>
</head>
<body>

<div style="position: fixed; top: 10px; right: 10px; background: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px; z-index: 10001;">
    ようこそ、<?= htmlspecialchars($_SESSION['username']) ?> ｜
    <a href="logout.php" style="color: #FFD700;">ログアウト</a> ｜ 
    <a href="ranking.php" style="color: #00FFFF;">ランキング</a>
</div>


<div class="game-container">
    <!--
    <div class="instructions">
        <h3>制作情報</h3>
        <ul>
            <li>プログラミング：リキ</li>
            <li>デザイン：リキ</li>
            <li>原曲：Gelato - Penguin's Game</li>
            <li>8-bitアレンジ：リキ</li>
            <li>効果音：リキ</li>
            <li>波形編集：リキ</li>
            <li>使用ソフト：FL STUDIO、Photoshop</li>
            <li>使用プラグイン：3x Osc</li>
            <li>使用トラック：Square、Triangle、Noise、DPCM</li>
        </ul>
        <h3></h3>
    </div>
    <div class="instructions-right">
        <h3>ハードウェア情報</h3>
        <ul>
            <li>モニタースピーカー：YAMAHA HS4</li>
            <li>モニターヘッドホン：オーディオテクニカ ATH-M50x</li>
            <li>MIDIキーボード：M-Audio Keystation</li>
            <li>オーディオインターフェース：M-Track Solo</li>
            <li>DAWコントローラー：KORG nanoKONTROL2</li>
            <li>使用PC：自作デスクトップ</li>
            <br>
            <br>
            <br>
            <br>
        </ul>
        <h3></h3>
    </div>
    -->
    <div class="slot-machine">
        <h2>LQのスロット</h2>
        <div class="reels-container">
            <div class="reel" id="reel1">
                <div class="reel-items" id="reel-items1"></div>
            </div>
            <div class="reel" id="reel2">
                <div class="reel-items" id="reel-items2"></div>
            </div>
            <div class="reel" id="reel3">
                <div class="reel-items" id="reel-items3"></div>
            </div>
        </div>
        <div class="controls">
            <button class="spin-button" id="spin-button">PUSH</button>
            <div class="message" id="message"></div>
        </div>
        <img src="cat.gif" alt="gif猫" class="cat-gif">
        <div class="light-container" id="light-container"></div>
    </div>
</div>
<canvas id="visualizer"></canvas>
<canvas id="particle-canvas"></canvas>
<script>
    const symbols = ['🍒', '🍋', '🍊', '🍉', '🔔', '💎', '7️⃣'];
    function initializeReels() {
        for (let i = 1; i <= 3; i++) {
            const reelItems = document.getElementById(`reel-items${i}`);
            reelItems.innerHTML = '';
            for (let j = 0; j < 1000; j++) {
                const symbolIndex = Math.floor(Math.random() * symbols.length);
                const reelItem = document.createElement('div');
                reelItem.className = 'reel-item';
                reelItem.textContent = symbols[symbolIndex];
                reelItems.appendChild(reelItem);
            }
            reelItems.style.top = '0px';
        }
    }
    function createLights() {
        const lightContainer = document.getElementById('light-container');
        const lights = [];
        const horizontalLights = 16;  
        const verticalLights = 6;    
        const rectWidth = 333, rectHeight = 115.7, offsetX = 15, offsetY = 110;
        for (let i = 0; i < horizontalLights; i++) {
            const light = document.createElement('div');
            light.className = 'light';
            light.style.left = `${offsetX + (rectWidth / horizontalLights) * i}px`;
            light.style.top = `${offsetY}px`;
            lightContainer.appendChild(light);
            lights.push(light);
        }
        for (let i = 0; i < verticalLights; i++) {
            const light = document.createElement('div');
            light.className = 'light';
            light.style.left = `${offsetX + rectWidth}px`;
            light.style.top = `${offsetY + (rectHeight / verticalLights) * i}px`;
            lightContainer.appendChild(light);
            lights.push(light);
        }
        for (let i = 0; i < horizontalLights; i++) {
            const light = document.createElement('div');
            light.className = 'light';
            light.style.left = `${offsetX + rectWidth - (rectWidth / horizontalLights) * i}px`;
            light.style.top = `${offsetY + rectHeight}px`;
            lightContainer.appendChild(light);
            lights.push(light);
        }
        for (let i = 0; i < verticalLights; i++) {
            const light = document.createElement('div');
            light.className = 'light';
            light.style.left = `${offsetX}px`;
            light.style.top = `${offsetY + rectHeight - (rectHeight / verticalLights) * i}px`;
            lightContainer.appendChild(light);
            lights.push(light);
        }
    }
    let currentLightIndex = 0;
    let lightInterval;
    function startLightChase() {
        const lights = document.querySelectorAll('.light');
        if (lightInterval) clearInterval(lightInterval);
        lights.forEach(light => light.classList.remove('active'));
        const activeCount = 10;  
        lightInterval = setInterval(() => {
            lights.forEach(light => light.classList.remove('active'));
            for (let i = 0; i < activeCount; i++) {
                lights[(currentLightIndex + i) % lights.length].classList.add('active');
            }
            currentLightIndex = (currentLightIndex + 1) % lights.length;
        }, 40);
    }
    function stopLightChase() {
        if (lightInterval) {
            clearInterval(lightInterval);
            lightInterval = null;
        }
    }
    function secondPrizeLights() {
        const lights = document.querySelectorAll('.light');
        stopLightChase();
        let counter = 0;
        const blinkInterval = setInterval(() => {
            lights.forEach((light, index) => {
                light.classList.toggle('active', index % 2 === counter % 2);
            });
            counter++;
            if (counter > 10) {
                clearInterval(blinkInterval);
                resetLights();
            }
        }, 300);
    }
    function firstPrizeLights() {
        const lights = document.querySelectorAll('.light');
        stopLightChase();
        let counter = 0;
        const excitingInterval = setInterval(() => {
            lights.forEach(light => light.classList.toggle('active', counter % 2 === 0));
            counter++;
            if (counter > 20) {
                clearInterval(excitingInterval);
                let secondCounter = 0, activeLightIndex = 0;
                const chaseInterval = setInterval(() => {
                    lights.forEach(light => light.classList.remove('active'));
                    for (let i = 0; i < 5; i++) {
                        lights[(activeLightIndex + i) % lights.length].classList.add('active');
                    }
                    activeLightIndex = (activeLightIndex + 1) % lights.length;
                    secondCounter++;
                    if (secondCounter > 30) {
                        clearInterval(chaseInterval);
                        resetLights();
                    }
                }, 50);
            }
        }, 150);
    }
    function resetLights() {
        document.querySelectorAll('.light').forEach(light => light.classList.remove('active'));
    }
    const particleCanvas = document.getElementById('particle-canvas');
    const pCtx = particleCanvas.getContext('2d');
    particleCanvas.width = window.innerWidth;
    particleCanvas.height = window.innerHeight;
    function Particle(x, y, dx, dy, size, color, life) {
        this.x = x;
        this.y = y;
        this.dx = dx;
        this.dy = dy;
        this.size = size;
        this.color = color;
        this.life = life; 
        this.opacity = 1;
    }
    Particle.prototype.update = function() {
        this.x += this.dx;
        this.y += this.dy;
        this.life -= 1;
        this.opacity = this.life / 100;
    };
    Particle.prototype.draw = function(ctx) {
        ctx.save();
        ctx.globalAlpha = this.opacity;
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
    };
    let particles = [];
    let particleCooldown = 0; 
    function spawnParticles(x, y) {
        const count = 30;
        for (let i = 0; i < count; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = Math.random() * 4 + 1;
            const dx = Math.cos(angle) * speed;
            const dy = Math.sin(angle) * speed;
            const size = Math.random() * 3 + 1;
            const life = 200;
            const color = 'hsl(' + Math.floor(Math.random() * 360) + ', 100%, 50%)';
            particles.push(new Particle(x, y, dx, dy, size, color, life));
        }
    }
    function updateParticles() {
        pCtx.clearRect(0, 0, particleCanvas.width, particleCanvas.height);
        for (let i = particles.length - 1; i >= 0; i--) {
            const p = particles[i];
            p.update();
            p.draw(pCtx);
            if (p.life <= 0) {
                particles.splice(i, 1);
            }
        }
    }
    function setupVisualizerWithParticles() {
    try {
        const audio = document.getElementById('background-music');
        const canvas = document.getElementById('visualizer');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = 100;

        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const analyser = audioCtx.createAnalyser();
        analyser.fftSize = 256;
        const bufferLength = analyser.frequencyBinCount;
        const dataArray = new Uint8Array(bufferLength);

        const source = audioCtx.createMediaElementSource(audio);
        source.connect(analyser);
        analyser.connect(audioCtx.destination);

        function draw() {
            requestAnimationFrame(draw);
            analyser.getByteFrequencyData(dataArray);

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const barWidth = canvas.width / bufferLength;
            let barHeight, x = 0, sum = 0;
            for (let i = 0; i < bufferLength; i++) {
                barHeight = dataArray[i] / 2;
                ctx.fillStyle = 'lime';
                ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
                x += barWidth;
                sum += dataArray[i];
            }
            const average = sum / bufferLength;
            if (particleCooldown <= 0 && average > 92) {
                const spawnX = Math.random() * particleCanvas.width;
                const spawnY = Math.random() * (particleCanvas.height / 2);
                spawnParticles(spawnX, spawnY);
                particleCooldown = 20;
            }
            if (particleCooldown > 0) {
                particleCooldown--;
            }
            updateParticles();
        }
        draw();
    } catch (error) {
        console.error("setupVisualizerWithParticles error: ", error);
    }
}

    function spinReels() {
        const spinButton = document.getElementById('spin-button');
        const message = document.getElementById('message');
        spinButton.disabled = true;  
        message.textContent = '';
        
        //setupVisualizerWithParticles(); // 
        
        document.getElementById('background-music').play();
        document.getElementById('spin-sound').play();
        document.getElementById('reel-spin-sound').play();

        startLightChase();  

        const stopPositions = [];
        const results = [];

        for (let i = 1; i <= 3; i++) {
            const reelItems = document.getElementById(`reel-items${i}`);
            const totalItems = reelItems.children.length;
            const randomPosition = -Math.floor(Math.random() * (totalItems - 3)) * 100;
            stopPositions.push(randomPosition);
            const resultIndex = Math.abs(Math.floor(randomPosition / 100));
            results.push(reelItems.children[resultIndex].textContent);
 
            setTimeout(() => {
                reelItems.style.top = `${stopPositions[i - 1]}px`;
            }, i * 500);
        }

        setTimeout(() => {
            stopLightChase();
            checkResults(results);
            spinButton.disabled = false;
        }, 4000);
    }

    function checkResults(results) {
        const message = document.getElementById('message');

        if (results[0] === results[1] && results[1] === results[2]) {
            document.getElementById('win-sound').play();
   
            if (results[0] === '💎' || results[0] === '7️⃣') {
                message.textContent = 'おめでとうございます！特等賞です！';
                message.style.color = '#FF0000';
                uploadScore(100);
            } else {
                message.textContent = 'おめでとう！一等賞です！';
                message.style.color = '#FFD700';
                uploadScore(100);
            }
            firstPrizeLights();
        }

        else if (results[0] === results[1] || results[1] === results[2] || results[0] === results[2]) {
            document.getElementById('win-alert-sound').play();
            message.textContent = 'おめでとう！二等賞をゲット！';
            message.style.color = '#FFA500';
            secondPrizeLights();
            uploadScore(50);
        }

        else {
            document.getElementById('lose-beeps-sound').play();
            message.textContent = '残念！またチャレンジしてね！';
            message.style.color = '#FFFFFF';
            resetLights();
        }
    }

    window.onload = function () {
        initializeReels();   
        createLights();    

        document.getElementById('spin-button').addEventListener('click', spinReels);
        document.addEventListener('keydown', function (e) {
            if (e.code === 'Space') {
                e.preventDefault();
                if (!document.getElementById('spin-button').disabled) spinReels();
            }
        });
    };
    function uploadScore(score) {
        fetch("save_score.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "score=" + encodeURIComponent(score)
        })
            .then(response => response.text())
            .then(result => {
                console.log("サーバーからの返答：", result);
            })
            .catch(error => {
                console.error("アップロードに失敗しました", error);
            });
    }

</script>

<audio id="background-music" src="background-music.mp3"></audio>
<audio id="spin-sound" src="spin.mp3"></audio>
<audio id="reel-spin-sound" src="reel-spin.mp3"></audio>
<audio id="lose-beeps-sound" src="lose-beeps.wav"></audio>
<audio id="win-alert-sound" src="win-alert.wav"></audio>
<audio id="win-sound" src="win.mp3"></audio>
</body>
</html>
