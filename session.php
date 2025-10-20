<?php
session_start();

// ✅ Initialiser le jeu si nécessaire
if (!isset($_SESSION['game'])) {
    $_SESSION['game'] = [
        'rabbit' => ['x' => 3, 'y' => 10],
        'fox' => ['x' => 3, 'y' => 0],
        'miam' => null,
        'score' => 0,
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
    return $_SESSION['game'] ?? null;  // ✅ Protection si null
}

function updateGame($data) {
    // ✅ FIX : Vérifier que $_SESSION['game'] existe
    if (!isset($_SESSION['game'])) {
        $_SESSION['game'] = [];
    }
    $_SESSION['game'] = array_merge($_SESSION['game'], $data);
}

function resetGame() {
    // ✅ FIX : Ne pas appeler session_start() à nouveau !
    $_SESSION['game'] = [
        'rabbit' => ['x' => 3, 'y' => 10],
        'fox' => ['x' => 3, 'y' => 0],
        'miam' => null,
        'score' => 0,
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