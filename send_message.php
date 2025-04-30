<?php
session_start();
include "connection.php";

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

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

    // Build 5x5 matrix
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

    // Prepare plaintext into digraphs
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

function getPos($grid, $char) {
    for ($row = 0; $row < 5; $row++) {
        for ($col = 0; $col < 5; $col++) {
            if ($grid[$row][$col] == $char) {
                return [$row, $col];
            }
        }
    }
    return [0, 0];
}

$sender = $_SESSION['email'];
$receiver = $_POST['receiver_email'];
$timestamp = date('Y-m-d H:i:s');

if (isset($_POST['secret'])) {
    $originalMessage = $_POST['secret_message'];
    $cipher = $_POST['cipher'];
    $key = $_POST['key'];

    if ($cipher === "autokey") {
        $encrypted = autokeyEncrypt($originalMessage, $key);
    } elseif ($cipher === "playfair") {
        $encrypted = playfairEncrypt($originalMessage, $key);
    } else {
        die("Unsupported cipher selected.");
    }

    $finalMessage = "[SECRET]" . $encrypted;
} else {
    $finalMessage = $_POST['message'];
}

$stmt = $mysqli->prepare("INSERT INTO messages (sender_email, receiver_email, message, timestamp) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $sender, $receiver, $finalMessage, $timestamp);

if ($stmt->execute()) {
    header("Location: home.php?user=" . urlencode($receiver));
} else {
    echo "Error sending message: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
