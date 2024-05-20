<?php
session_start();

if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 9, '');
    $_SESSION['turn'] = 'X';
    $_SESSION['winner'] = null;
    $_SESSION['draw'] = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['move'])) {
        $move = (int)$_POST['move'];

        if ($_SESSION['board'][$move] === '' && !$_SESSION['winner']) {
            $_SESSION['board'][$move] = $_SESSION['turn'];
            $_SESSION['turn'] = $_SESSION['turn'] === 'X' ? 'O' : 'X';
            $_SESSION['winner'] = check_winner($_SESSION['board']);
            $_SESSION['draw'] = !in_array('', $_SESSION['board']) && !$_SESSION['winner'];
        }
    }

    if (isset($_POST['reset'])) {
        $_SESSION['board'] = array_fill(0, 9, '');
        $_SESSION['turn'] = 'X';
        $_SESSION['winner'] = null;
        $_SESSION['draw'] = false;
    }
}

function check_winner($board) {
    $winning_combinations = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6]
    ];

    foreach ($winning_combinations as $combo) {
        if ($board[$combo[0]] && $board[$combo[0]] === $board[$combo[1]] && $board[$combo[1]] === $board[$combo[2]]) {
            return $board[$combo[0]];
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Tic-Tac-Toe</h1>
    <div class="board">
        <?php for ($i = 0; $i < 9; $i++): ?>
            <form method="POST" action="index.php" class="cell">
                <input type="hidden" name="move" value="<?php echo $i; ?>">
                <button type="submit" onclick="playClickSound()" <?php echo $_SESSION['board'][$i] !== '' || $_SESSION['winner'] ? 'disabled' : ''; ?>>
                    <?php echo $_SESSION['board'][$i]; ?>
                </button>
            </form>
        <?php endfor; ?>
    </div>
    <div class="message">
        <?php
            if ($_SESSION['winner']) {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('modal').style.display = 'block';
                            document.getElementById('modal-text').innerText = '{$_SESSION['winner']} wins!';
                            setTimeout(function() { document.getElementById('reset-form').submit(); }, 3000);
                        });
                      </script>";
            } elseif ($_SESSION['draw']) {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('modal').style.display = 'block';
                            document.getElementById('modal-text').innerText = 'It\'s a draw!';
                            setTimeout(function() { document.getElementById('reset-form').submit(); }, 3000);
                        });
                      </script>";
            }
        ?>
    </div>
    <form method="POST" action="index.php" id="reset-form" style="display: none;">
        <input type="hidden" name="reset" value="1">
    </form>
    <form method="POST" action="index.php">
        <button type="submit" name="reset" class="reset">Reset Game</button>
    </form>

   <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('modal').style.display='none'">&times;</span>
            <p id="modal-text"></p>
        </div>
    </div>

    <audio id="click-sound" src="click-buttons-ui-menu-sounds-effects-button-7-203601.mp3" preload="auto"></audio>

    <script>
        function playClickSound() {
            document.getElementById('click-sound').play();
        }
    </script>
</body>
</html>
