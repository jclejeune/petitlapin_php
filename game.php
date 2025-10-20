<?php
require_once 'session.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$game = getGame();

// ✅ FIX : Vérification globale du gameOver
if ($game['gameOver'] && $action !== 'reset' && $action !== 'init') {
    // Si le jeu est fini, on refuse TOUTE action sauf reset
    echo json_encode($game);
    exit;
}

// ✅ Fonction de comparaison fiable
function samePosition($pos1, $pos2) {
    if (!$pos1 || !$pos2) return false;
    return $pos1['x'] === $pos2['x'] && $pos1['y'] === $pos2['y'];
}

// Vérifier si une position est valide
function canMove($x, $y, $grid) {
    return $x >= 0 && $x < 7 && $y >= 0 && $y < 11 && $grid[$y][$x] === 0;
}

// Spawner un miam
function spawnMiam($game) {
    $empty = [];
    foreach ($game['grid'] as $y => $row) {
        foreach ($row as $x => $cell) {
            $pos = ['x' => $x, 'y' => $y];
            if ($cell === 0 
                && !samePosition($pos, $game['rabbit'])
                && !samePosition($pos, $game['fox'])) {
                $empty[] = $pos;
            }
        }
    }
    return $empty ? $empty[array_rand($empty)] : null;
}

// Distance euclidienne
function distance($p1, $p2) {
    return sqrt(pow($p1['x'] - $p2['x'], 2) + pow($p1['y'] - $p2['y'], 2));
}

// ✅ Déplacer le lapin - AVEC DOUBLE VÉRIFICATION
if ($action === 'move') {
    // ✅ PROTECTION 1 : Vérifier gameOver
    if ($game['gameOver']) {
        echo json_encode($game);
        exit;
    }
    
    $direction = $_POST['direction'] ?? '';
    
    $moves = [
        'left' => [-1, 0],
        'right' => [1, 0],
        'up' => [0, -1],
        'down' => [0, 1]
    ];
    
    if (!isset($moves[$direction])) {
        echo json_encode($game);
        exit;
    }
    
    [$dx, $dy] = $moves[$direction];
    $newX = $game['rabbit']['x'] + $dx;
    $newY = $game['rabbit']['y'] + $dy;
    
    if (canMove($newX, $newY, $game['grid'])) {
        $game['rabbit'] = ['x' => $newX, 'y' => $newY];
        
        // ✅ Vérifier collision AVANT de manger le miam
        if (samePosition($game['rabbit'], $game['fox'])) {
            $game['gameOver'] = true;
            updateGame($game);
            echo json_encode($game);
            exit;  // ✅ EXIT immédiatement, pas de points !
        }
        
        // ✅ Vérifier si on mange le miam (seulement si pas mort)
        if (samePosition($game['rabbit'], $game['miam'])) {
            $game['score'] += 50;
            $game['miam'] = spawnMiam($game);
        }
        
        updateGame($game);
    }
}

// ✅ Déplacer le renard
if ($action === 'moveFox') {
    // ✅ PROTECTION : Ne pas bouger si gameOver
    if ($game['gameOver']) {
        echo json_encode($game);
        exit;
    }
    
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
        
        // ✅ Vérifier collision
        if (samePosition($game['rabbit'], $game['fox'])) {
            $game['gameOver'] = true;
        }
        
        updateGame($game);
    }
}

// Spawner le premier miam si besoin
if ($action === 'init') {
    if (!$game['miam']) {
        $game['miam'] = spawnMiam($game);
        updateGame($game);
    }
}

// ✅ Reset
if ($action === 'reset') {
    resetGame();
    $game = getGame();
    $game['miam'] = spawnMiam($game);
    updateGame($game);
}

// Retourner l'état du jeu
echo json_encode($game);