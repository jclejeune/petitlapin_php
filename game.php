<?php
require_once 'session.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$game = getGame();

// Vérifier si une position est valide
function canMove($x, $y, $grid) {
    return $x >= 0 && $x < 7 && $y >= 0 && $y < 11 && $grid[$y][$x] === 0;
}

// Spawner un miam
function spawnMiam($game) {
    $empty = [];
    foreach ($game['grid'] as $y => $row) {
        foreach ($row as $x => $cell) {
            if ($cell === 0 
                && ['x' => $x, 'y' => $y] != $game['rabbit']
                && ['x' => $x, 'y' => $y] != $game['fox']) {
                $empty[] = ['x' => $x, 'y' => $y];
            }
        }
    }
    return $empty ? $empty[array_rand($empty)] : null;
}

// Distance euclidienne
function distance($p1, $p2) {
    return sqrt(pow($p1['x'] - $p2['x'], 2) + pow($p1['y'] - $p2['y'], 2));
}

// Déplacer le lapin
if ($action === 'move') {
    $direction = $_POST['direction'];
    
    $moves = [
        'left' => [-1, 0],
        'right' => [1, 0],
        'up' => [0, -1],
        'down' => [0, 1]
    ];
    
    [$dx, $dy] = $moves[$direction] ?? [0, 0];
    $newX = $game['rabbit']['x'] + $dx;
    $newY = $game['rabbit']['y'] + $dy;
    
    if (canMove($newX, $newY, $game['grid'])) {
        $game['rabbit'] = ['x' => $newX, 'y' => $newY];
        
        // Vérifier si on mange le miam
        if ($game['rabbit'] === $game['miam']) {
            $game['score'] += 50;
            $game['miam'] = spawnMiam($game);
        }
        
        // Vérifier collision avec renard
        if ($game['rabbit'] === $game['fox']) {
            $game['gameOver'] = true;
        }
        
        updateGame($game);
    }
}

// Déplacer le renard (pathfinding)
if ($action === 'moveFox') {
    $fox = $game['fox'];
    $rabbit = $game['rabbit'];
    
    $possibleMoves = [
        ['x' => $fox['x'] - 1, 'y' => $fox['y']],
        ['x' => $fox['x'] + 1, 'y' => $fox['y']],
        ['x' => $fox['x'], 'y' => $fox['y'] - 1],
        ['x' => $fox['x'], 'y' => $fox['y'] + 1],
    ];
    
    $validMoves = array_filter($possibleMoves, fn($m) => 
        canMove($m['x'], $m['y'], $game['grid'])
    );
    
    if ($validMoves) {
        $bestMove = array_reduce($validMoves, function($best, $move) use ($rabbit) {
            return !$best || distance($move, $rabbit) < distance($best, $rabbit) 
                ? $move 
                : $best;
        });
        
        $game['fox'] = $bestMove;
        
        // Vérifier collision
        if ($game['rabbit'] === $game['fox']) {
            $game['gameOver'] = true;
        }
        
        updateGame($game);
    }
}

// Spawner le premier miam si besoin
if ($action === 'init' && !$game['miam']) {
    $game['miam'] = spawnMiam($game);
    updateGame($game);
}

// Reset
if ($action === 'reset') {
    resetGame();
    $game = getGame();
    $game['miam'] = spawnMiam($game);
    updateGame($game);
}

// Retourner l'état du jeu
echo json_encode(getGame());