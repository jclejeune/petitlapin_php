<?php
session_start();

// ✅ Charger le hi-score depuis un fichier
function loadHiScore() {
    $file = __DIR__ . '/hiscore.txt';
    if (file_exists($file)) {
        return (int)file_get_contents($file);
    }
    return 0;
}

// ✅ Sauvegarder le hi-score dans un fichier
function saveHiScore($score) {
    $file = __DIR__ . '/hiscore.txt';
    file_put_contents($file, $score);
}

if (!isset($_SESSION['game'])) {
    $_SESSION['game'] = [
        'rabbit' => ['x' => 3, 'y' => 10],
        'fox' => ['x' => 3, 'y' => 0],
        'miam' => null,
        'score' => 0,
        'hiScore' => loadHiScore(),
        'gameOver' => false,
        'grid' => [
            [0,0,0,0,0,0,0],
            [0,1,0,1,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,0,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,0,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,0,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,1,0,1,0],
            [0,0,0,0,0,0,0],
        ]
    ];
}

function getGame() {
    return $_SESSION['game'] ?? null;
}

function updateGame($data) {
    if (!isset($_SESSION['game'])) {
        $_SESSION['game'] = [];
    }
    $_SESSION['game'] = array_merge($_SESSION['game'], $data);
}

function resetGame() {
    $hiScore = $_SESSION['game']['hiScore'] ?? loadHiScore();
    
    $_SESSION['game'] = [
        'rabbit' => ['x' => 3, 'y' => 10],
        'fox' => ['x' => 3, 'y' => 0],
        'miam' => null,
        'score' => 0,
        'hiScore' => $hiScore,
        'gameOver' => false,
        'grid' => [
            [0,0,0,0,0,0,0],
            [0,1,0,1,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,0,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,0,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,0,0,1,0],
            [0,0,0,1,0,0,0],
            [0,1,0,1,0,1,0],
            [0,0,0,0,0,0,0],
        ]
    ];
}