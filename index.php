<?php require_once 'session.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Petit Lapin</title>
    <style>
        body { 
            background: #1a1a1a; 
            color: white; 
            font-family: Arial;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container { position: relative; }
        .grid {
            display: grid;
            grid-template-columns: repeat(7, 50px);
            gap: 0;
            border: 2px solid #333;
        }
        .cell {
            width: 50px;
            height: 50px;
            background: #323232;
            border: 1px solid #000;
            position: relative;
        }
        .cell.wall { background: #dd2e44; }
        .entity {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .rabbit { background: #ffeb3b; }
        .fox { background: #00bcd4; }
        .miam { background: #e91e63; }
        .score {
            text-align: center;
            font-size: 24px;
            margin: 20px 0;
        }
        .game-over {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .game-over.active { display: flex; }
        .game-over h1 { color: #f44336; font-size: 48px; }
        .game-over button {
            padding: 15px 40px;
            font-size: 20px;
            background: #4caf50;
            border: none;
            color: white;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="score">Score: <span id="score">0</span></div>
        
        <div class="grid" id="grid" tabindex="0">
            <!-- Généré par JS -->
        </div>
        
        <div class="game-over" id="gameOver">
            <h1>GAME OVER</h1>
            <p class="score">Score: <span id="finalScore">0</span></p>
            <button onclick="resetGame()">RESET</button>
        </div>
    </div>

    <script>
        let gameState = null;

        // Fetch l'état du jeu
        async function fetchGame(action, data = {}) {
            const formData = new FormData();
            formData.append('action', action);
            Object.entries(data).forEach(([k, v]) => formData.append(k, v));
            
            const res = await fetch('game.php', { 
                method: 'POST', 
                body: formData 
            });
            gameState = await res.json();
            render();
        }

        // Render la grille
        function render() {
            const grid = document.getElementById('grid');
            grid.innerHTML = '';
            
            gameState.grid.forEach((row, y) => {
                row.forEach((cell, x) => {
                    const div = document.createElement('div');
                    div.className = 'cell' + (cell === 1 ? ' wall' : '');
                    
                    // Rabbit
                    if (gameState.rabbit.x === x && gameState.rabbit.y === y) {
                        div.innerHTML = '<div class="entity rabbit"></div>';
                    }
                    
                    // Fox
                    if (gameState.fox.x === x && gameState.fox.y === y) {
                        div.innerHTML += '<div class="entity fox"></div>';
                    }
                    
                    // Miam
                    if (gameState.miam?.x === x && gameState.miam?.y === y) {
                        div.innerHTML += '<div class="entity miam"></div>';
                    }
                    
                    grid.appendChild(div);
                });
            });
            
            document.getElementById('score').textContent = gameState.score;
            document.getElementById('finalScore').textContent = gameState.score;
            document.getElementById('gameOver').className = 
                'game-over' + (gameState.gameOver ? ' active' : '');
        }

        // Contrôles clavier
        document.addEventListener('keydown', (e) => {
            // ✅ FIX 1 : Ajouter preventDefault pour l'espace
            if (gameState.gameOver && (e.key === ' ' || e.code === 'Space')) {
                e.preventDefault(); // ← IMPORTANT : empêche le scroll
                resetGame();
                return;
            }
            
            const keys = {
                'ArrowLeft': 'left',
                'ArrowRight': 'right',
                'ArrowUp': 'up',
                'ArrowDown': 'down'
            };
            
            if (keys[e.key]) {
                e.preventDefault();
                fetchGame('move', { direction: keys[e.key] });
            }
        });

        // Boucle du renard (300ms)
        setInterval(() => {
            if (!gameState?.gameOver) {
                fetchGame('moveFox');
            }
        }, 300);

        // Reset
        function resetGame() {
            fetchGame('reset');
        }

        // Init
        fetchGame('init');
    </script>
</body>
</html>