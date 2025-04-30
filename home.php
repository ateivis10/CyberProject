<?php
session_start();
include "connection.php";

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$loggedInUser = $_SESSION['email'];

$userList = $mysqli->query("
    SELECT email FROM users 
    WHERE email != '$loggedInUser'
");

$selectedUser = isset($_GET['user']) ? $_GET['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Chat Home</title>
  <style>
    body {
      margin: 0;
      font-family: 'Arial', sans-serif;
      background: #f4f4f4;
    }

    nav {
      background-color: #333;
      padding: 10px 20px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    nav ul {
      list-style: none;
      margin: 0;
      padding-right: 30px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }

    nav ul li {
      margin-left: 20px;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      padding: 8px 15px;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    nav ul li a:hover {
      background-color: #575757;
    }

    .container {
      display: flex;
      margin-top: 70px;
    }

    .user-list {
      width: 25%;
      background: white;
      padding: 20px;
      border-right: 1px solid #ccc;
      height: calc(100vh - 70px);
      overflow-y: auto;
    }

    .user-list h3 {
      margin-top: 0;
    }

    .user-list a {
      display: block;
      padding: 10px;
      margin-bottom: 10px;
      background: #eee;
      border-radius: 5px;
      text-decoration: none;
      color: #333;
      transition: background 0.3s;
    }

    .user-list a:hover {
      background: #ddd;
    }

    .chat-section {
      width: 75%;
      padding: 20px;
      background: #fafafa;
      height: calc(100vh - 70px);
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .messages {
      flex-grow: 1;
      overflow-y: auto;
    }

    .messages h3 {
      margin-top: 0;
    }

    .message {
      background: #e1f5fe;
      padding: 10px;
      margin: 10px;
      border-radius: 8px;
      max-width: 60%;
      word-wrap: break-word;
    }

    .message.self {
      background: #c8e6c9;
      align-self: flex-end;
    }

    .send-message-form {
      margin-top: 20px;
      display: flex;
    }

    .send-message-form textarea {
      flex-grow: 1;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      resize: none;
    }

    .send-message-form button {
      padding: 10px 20px;
      margin-left: 10px;
      background: #4CAF50;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    .send-message-form button:hover {
      background: #45a049;
    }
  </style>
</head>

<body>

  <nav>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <div class="container">
    <div class="user-list">
      <h3>Chats</h3>
      <?php while ($row = $userList->fetch_assoc()): ?>
        <a href="home.php?user=<?php echo urlencode($row['email']); ?>">
          <?php echo htmlspecialchars($row['email']); ?>
        </a>
      <?php endwhile; ?>

    </div>

    <div class="chat-section">
      <?php if ($selectedUser): ?>
        <div class="messages">
          <h3>Chat with <?php echo htmlspecialchars($selectedUser); ?></h3>
          <?php
          $chatMessages = $mysqli->query("
            SELECT * FROM messages 
            WHERE (sender_email='$loggedInUser' AND receiver_email='$selectedUser') 
               OR (sender_email='$selectedUser' AND receiver_email='$loggedInUser')
            ORDER BY timestamp ASC
        ");

          while ($msg = $chatMessages->fetch_assoc()):
            $isSecret = str_starts_with($msg['message'], '[SECRET]');
            $displayMsg = $isSecret ? substr($msg['message'], 8) : $msg['message'];
            ?>
            <div class="message <?php echo ($msg['sender_email'] == $loggedInUser) ? 'self' : ''; ?>">
              <strong><?php echo htmlspecialchars($msg['sender_email']); ?>:</strong><br>
              <?php echo htmlspecialchars($displayMsg); ?>
              <?php if ($isSecret): ?>
                <br>
                <button onclick="decryptMessage(this, '<?php echo $displayMsg; ?>')">Decrypt</button>
                <div class="decrypted" style="margin-top:5px; font-style: italic; color: #444;"></div>
              <?php endif; ?>
              <br><small><?php echo $msg['timestamp']; ?></small>
            </div>
          <?php endwhile; ?>
        </div>

        <!-- Message Form -->
        <form class="send-message-form" action="send_message.php" method="POST" id="messageForm">
          <input type="hidden" name="receiver_email" value="<?php echo htmlspecialchars($selectedUser); ?>">
          <textarea name="message" id="message" required placeholder="Type your message..."></textarea>
          <button type="submit">Send</button>
          <label style="margin-left:10px;">
            <input type="checkbox" id="secretToggle" onchange="toggleSecret()"> Secret
          </label>
        </form>

        <!-- Secret Message UI -->
        <div id="secretOptions" style="display:none; margin-top: 10px;">
          <form class="send-message-form" action="send_message.php" method="POST">
            <input type="hidden" name="receiver_email" value="<?php echo htmlspecialchars($selectedUser); ?>">
            <input type="hidden" name="secret" value="1">

            <textarea name="secret_message" placeholder="Type your secret message..." required></textarea>
            <select name="cipher" required>
              <option value="">Select Cipher</option>
              <option value="autokey">Autokey Cipher</option>
              <option value="playfair">Playfair Cipher</option>
            </select>
            <input type="text" name="key" placeholder="Enter key/keyword" required>
            <button type="submit">Send Secret</button>
          </form>
        </div>
      <?php else: ?>
        <h3 style="text-align:center;">Select a user to start chatting!</h3>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleSecret() {
      const isChecked = document.getElementById("secretToggle").checked;
      document.getElementById("secretOptions").style.display = isChecked ? "block" : "none";
      document.getElementById("message").disabled = isChecked;
    }

    // Autokey Decryption
    function autokeyDecrypt(ciphertext, key) {
      ciphertext = ciphertext.toUpperCase().replace(/[^A-Z]/g, '');
      key = key.toUpperCase().replace(/[^A-Z]/g, '');
      let plaintext = '';
      for (let i = 0; i < ciphertext.length; i++) {
        let keyChar = (i < key.length) ? key[i] : plaintext[i - key.length];
        let p = (ciphertext.charCodeAt(i) - keyChar.charCodeAt(0) + 26) % 26;
        plaintext += String.fromCharCode(p + 65);
      }
      return plaintext;
    }

    // Playfair Decryption
    function playfairDecrypt(ciphertext, key) {
      function generateMatrix(key) {
        key = key.toUpperCase().replace(/[^A-Z]/g, '').replace(/J/g, 'I');
        const matrix = [];
        const used = {};
        let combined = key + "ABCDEFGHIKLMNOPQRSTUVWXYZ";
        for (let char of combined) {
          if (!used[char]) {
            used[char] = true;
            matrix.push(char);
          }
        }
        return [...Array(5)].map((_, i) => matrix.slice(i * 5, i * 5 + 5));
      }

      function getPos(matrix, char) {
        for (let r = 0; r < 5; r++)
          for (let c = 0; c < 5; c++)
            if (matrix[r][c] === char) return [r, c];
        return null;
      }

      ciphertext = ciphertext.toUpperCase().replace(/[^A-Z]/g, '').replace(/J/g, 'I');
      const matrix = generateMatrix(key);
      let result = '';

      for (let i = 0; i < ciphertext.length; i += 2) {
        const a = ciphertext[i], b = ciphertext[i + 1];
        const [ra, ca] = getPos(matrix, a);
        const [rb, cb] = getPos(matrix, b);

        if (ra === rb) {
          result += matrix[ra][(ca + 4) % 5];
          result += matrix[rb][(cb + 4) % 5];
        } else if (ca === cb) {
          result += matrix[(ra + 4) % 5][ca];
          result += matrix[(rb + 4) % 5][cb];
        } else {
          result += matrix[ra][cb];
          result += matrix[rb][ca];
        }
      }

      // Optional: Remove padding 'X' between duplicate letters
      result = result.replace(/([A-Z])X(?=\1)/g, '$1');

      // Optional: Remove trailing X if original length was odd
      if (result.endsWith('X')) result = result.slice(0, -1);

      return result;
    }

    // Decryption Handler
    function decryptMessage(button, ciphertext) {
      const cipher = prompt("Enter cipher type (autokey/playfair):");
      const key = prompt("Enter key:");
      let decrypted = "";

      if (!cipher || !key) return;

      if (cipher.toLowerCase() === "autokey") {
        decrypted = autokeyDecrypt(ciphertext, key);
      } else if (cipher.toLowerCase() === "playfair") {
        decrypted = playfairDecrypt(ciphertext, key);
      } else {
        alert("Unsupported cipher.");
        return;
      }

      const div = button.nextElementSibling;
      div.innerText = "Decrypted: " + decrypted;
    }
  </script>
</body>

</html>