<?php
session_start();

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
    return $_SESSION['game'];
}

function updateGame($data) {
    $_SESSION['game'] = array_merge($_SESSION['game'], $data);
}

function resetGame() {
    unset($_SESSION['game']);
    session_start();
}