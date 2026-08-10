<?php
/**
 * A17smart - Tic-Tac-Toe 後端（WorldSkills 2022 Module A / D3）
 *
 * 依試題要求，所有資料都保存在後端，前端以非同步方式取得：
 *   GET  api.php              取得目前盤面
 *   POST api.php {cell:0~8}   玩家下 X，接著電腦下 O
 *   POST api.php {reset:true} 重新開始
 *
 * 與原版的差別：電腦不是隨機下子，而是以 minimax 選擇最佳解（smart）。
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

const PLAYER = 'X';
const ROBOT  = 'O';

/** 建立一局全新的遊戲 */
function createGame(): array
{
    return [
        'board'   => array_fill(0, 9, ''),
        'turn'    => PLAYER,
        'winner'  => '',      // 'X' / 'O' / 'draw' / ''
        'over'    => false,
    ];
}

/**
 * 判斷勝負
 * @return string 'X'、'O'、'draw' 或空字串(未結束)
 */
function judge(array $board): string
{
    $lines = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],   // 橫
        [0, 3, 6], [1, 4, 7], [2, 5, 8],   // 直
        [0, 4, 8], [2, 4, 6],              // 斜
    ];
    foreach ($lines as $line) {
        [$a, $b, $c] = $line;
        if ($board[$a] !== '' && $board[$a] === $board[$b] && $board[$b] === $board[$c]) {
            return $board[$a];
        }
    }
    return in_array('', $board, true) ? '' : 'draw';
}

/**
 * minimax：回傳這個盤面對 $me 而言的分數
 * 越快獲勝分數越高，越慢落敗扣分越少
 */
function minimax(array $board, string $me, int $depth): int
{
    $result = judge($board);
    if ($result === ROBOT) {
        return 10 - $depth;
    }
    if ($result === PLAYER) {
        return $depth - 10;
    }
    if ($result === 'draw') {
        return 0;
    }

    $scores = [];
    foreach ($board as $index => $cell) {
        if ($cell !== '') {
            continue;
        }
        $next = $board;
        $next[$index] = $me;
        $scores[] = minimax($next, $me === ROBOT ? PLAYER : ROBOT, $depth + 1);
    }
    return $me === ROBOT ? max($scores) : min($scores);
}

/**
 * 電腦選擇最佳的一步
 * @return int 格子索引，沒有可下的位置時回傳 -1
 */
function bestMove(array $board): int
{
    $bestScore = PHP_INT_MIN;
    $bestIndex = -1;
    foreach ($board as $index => $cell) {
        if ($cell !== '') {
            continue;
        }
        $next = $board;
        $next[$index] = ROBOT;
        $score = minimax($next, PLAYER, 0);
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestIndex = $index;
        }
    }
    return $bestIndex;
}

/** 重新整理頁面時沿用 session 中的盤面，達成「重新整理不會重置遊戲」 */
if (!isset($_SESSION['game'])) {
    $_SESSION['game'] = createGame();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true) ?: [];

    if (!empty($payload['reset'])) {
        $_SESSION['game'] = createGame();
    } else {
        $game = $_SESSION['game'];
        $cell = isset($payload['cell']) ? (int) $payload['cell'] : -1;

        // 只有在遊戲進行中、位置合法且該格為空時才接受落子
        if (!$game['over'] && $cell >= 0 && $cell <= 8 && $game['board'][$cell] === '') {
            $game['board'][$cell] = PLAYER;
            $result = judge($game['board']);

            if ($result === '') {
                $robotCell = bestMove($game['board']);
                if ($robotCell >= 0) {
                    $game['board'][$robotCell] = ROBOT;
                }
                $result = judge($game['board']);
            }

            if ($result !== '') {
                $game['winner'] = $result;
                $game['over'] = true;
            }
            $_SESSION['game'] = $game;
        }
    }
}

echo json_encode($_SESSION['game'], JSON_UNESCAPED_UNICODE);
