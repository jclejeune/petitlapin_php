<?php require_once 'session.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petit Lapin</title>
    <style>
        /* ✅ Font 1 : Pour GAME OVER */
        @font-face {
            font-family: 'OurFriendElectric';
            src: url('fonts/OurFriendElectric.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        /* ✅ Font 2 : Pour tout le reste */
        @font-face {
            font-family: 'SuiGeneris';
            src: url('fonts/SuiGenerisRg.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        body { 
            background: #1a1a1a; 
            color: white; 
            font-family: 'SuiGeneris', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .container { 
            position: relative; 
        }
        
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
        
        .cell.wall { 
            background: #dd2e44; 
        }
        
        .entity {
            width: 40px;
            height: 40px;
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 35px;
            line-height: 40px;
            text-align: center;
        }
        
        .score {
            font-family: 'SuiGeneris', Arial, sans-serif;
            text-align: center;
            font-size: 24px;
            margin: 20px 0;
        }
        
        .game-over {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        
        .game-over.active { 
            display: flex; 
        }
        
        .game-over h1 { 
            font-family: 'OurFriendElectric', monospace;
            color: #f44336; 
            font-size: 48px;
            margin: 0;
        }
        
        .game-over .score {
            font-family: 'SuiGeneris', Arial, sans-serif;
            color: #00bcd4;
            font-size: 28px;
            margin: 20px 0;
        }
        
        .game-over button {
            font-family: 'SuiGeneris', Arial, sans-serif;
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
        <div class="score"><span id="score">0</span></div>
        
        <div class="grid" id="grid" tabindex="0">
            <!-- Généré par JavaScript -->
        </div>
        
        <div class="game-over" id="gameOver">
            <h1>GAME OVER</h1>
            <p class="score">Score: <span id="finalScore">0</span></p>
            <button onclick="resetGame(); return false;">RESTART</button>
        </div>
    </div>

    <script>
        let gameState = null;
        let isGameOver = false;

        // Fetch l'état du jeu
        async function fetchGame(action, data = {}) {
            if (isGameOver && action !== 'reset' && action !== 'init') {
                console.log('Game over, action bloquée:', action);
                return;
            }
            
            const formData = new FormData();
            formData.append('action', action);
            Object.entries(data).forEach(([k, v]) => formData.append(k, v));
            
            try {
                const res = await fetch('game.php', { 
                    method: 'POST', 
                    body: formData 
                });
                gameState = await res.json();
                isGameOver = gameState.gameOver;
                render();
            } catch (e) {
                console.error('Fetch error:', e);
            }
        }

        // Render la grille
        function render() {
            if (!gameState) return;
            
            const grid = document.getElementById('grid');
            grid.innerHTML = '';
            
            gameState.grid.forEach((row, y) => {
                row.forEach((cell, x) => {
                    const div = document.createElement('div');
                    div.className = 'cell' + (cell === 1 ? ' wall' : '');
                    
                    // Rabbit - NE PAS afficher si game over
                    if (gameState.rabbit.x === x && gameState.rabbit.y === y && !gameState.gameOver) {
                        div.innerHTML += '<div class="entity rabbit">🐰</div>';
                    }
                     // Miam
                    if (gameState.miam?.x === x && gameState.miam?.y === y) {
                        div.innerHTML += '<div class="entity miam">🥕</div>';
                    }
                    
                    // Fox
                    if (gameState.fox.x === x && gameState.fox.y === y) {
                        div.innerHTML += '<div class="entity fox">🦊</div>';
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
            if (!gameState) return;
            
            // Reset avec ESPACE
            if (isGameOver && (e.key === ' ' || e.code === 'Space')) {
                e.preventDefault();
                resetGame();
                return;
            }
            
            // Bloquer tout si game over
            if (isGameOver) {
                e.preventDefault();
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
            if (gameState && !isGameOver) {
                fetchGame('moveFox');
            }
        }, 300);

        // Reset
        async function resetGame() {
            console.log('Resetting game...');
            isGameOver = false;
            await fetchGame('reset');
        }

        // Init
        fetchGame('init');
    </script>
</body>
</html>