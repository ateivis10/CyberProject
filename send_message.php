<?php
session_start();
include "connection.php";

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$sender_email = $_SESSION['email'];
$receiver_email = $_POST['receiver_email'] ?? '';
$message = $_POST['message'] ?? '';
$isSecret = isset($_POST['secret']);
$cipher = $_POST['cipher'] ?? '';
$key = $_POST['key'] ?? '';
$secret_message = $_POST['secret_message'] ?? '';

function autokeyEncrypt($plaintext, $key) {
    $plaintext = strtoupper(preg_replace("/[^A-Z]/", "", $plaintext));
    $key = strtoupper(preg_replace("/[^A-Z]/", "", $key));
    $fullKey = $key . $plaintext;
    $ciphertext = "";

    for ($i = 0; $i < strlen($plaintext); $i++) {
        $p = ord($plaintext[$i]) - 65;
        $k = ord($fullKey[$i]) - 65;
        $c = ($p + $k) % 26;
        $ciphertext .= chr($c + 65);
    }

    return $ciphertext;
}

function playfairEncrypt($plaintext, $key) {
    $key = strtoupper(preg_replace("/[^A-Z]/", "", $key));
    $key = str_replace("J", "I", $key);
    $plaintext = strtoupper(preg_replace("/[^A-Z]/", "", $plaintext));
    $plaintext = str_replace("J", "I", $plaintext);

    // Build matrix
    $matrix = [];
    $used = [];
    $combined = $key . "ABCDEFGHIKLMNOPQRSTUVWXYZ";

    foreach (str_split($combined) as $char) {
        if (!isset($used[$char])) {
            $matrix[] = $char;
            $used[$char] = true;
        }
    }

    $grid = array_chunk($matrix, 5);

    function getPos($grid, $char) {
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                if ($grid[$i][$j] === $char) return [$i, $j];
            }
        }
        return [0, 0];
    }

    $pairs = [];
    for ($i = 0; $i < strlen($plaintext); $i += 2) {
        $a = $plaintext[$i];
        $b = ($i + 1 < strlen($plaintext)) ? $plaintext[$i + 1] : 'X';
        if ($a == $b) {
            $b = 'X';
            $i--;
        }
        $pairs[] = [$a, $b];
    }

    $ciphertext = "";
    foreach ($pairs as [$a, $b]) {
        [$ra, $ca] = getPos($grid, $a);
        [$rb, $cb] = getPos($grid, $b);

        if ($ra === $rb) {
            $ciphertext .= $grid[$ra][($ca + 1) % 5];
            $ciphertext .= $grid[$rb][($cb + 1) % 5];
        } elseif ($ca === $cb) {
            $ciphertext .= $grid[($ra + 1) % 5][$ca];
            $ciphertext .= $grid[($rb + 1) % 5][$cb];
        } else {
            $ciphertext .= $grid[$ra][$cb];
            $ciphertext .= $grid[$rb][$ca];
        }
    }

    return $ciphertext;
}

if ($isSecret) {
    if ($cipher === "autokey") {
        $encrypted = autokeyEncrypt($secret_message, $key);
    } elseif ($cipher === "playfair") {
        $encrypted = playfairEncrypt($secret_message, $key);
    } else {
        die("Invalid cipher selected.");
    }
    $finalMessage = "[SECRET]" . $encrypted;
} else {
    $finalMessage = $message;
}

$stmt = $mysqli->prepare("INSERT INTO messages (sender_email, receiver_email, message, timestamp) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $sender_email, $receiver_email, $finalMessage);
$stmt->execute();
$stmt->close();

header("Location: home.php?user=" . urlencode($receiver_email));
exit();
