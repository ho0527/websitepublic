<?php
/**
 * C4: 時間衝突解決工具（等級 3）
 * 由表單提交 JSON 陣列（每筆含標題、開始、結束、優先序），
 * 以貪心法保留優先序較高的事件、移除與其衝突者，
 * 最後輸出「依開始時間排序、無衝突的行程表」與「被移除事件的紀錄」，格式為 JSON。
 */

declare(strict_types=1);

/** 讀入 data.json 當作表單的預設內容 */
function defaultInput(): string
{
    $path = __DIR__ . '/data.json';
    if (is_file($path)) {
        return (string) file_get_contents($path);
    }
    return json_encode([
        ['title' => 'Team Sync',      'start' => '09:00', 'end' => '10:00', 'priority' => 2],
        ['title' => 'Client Meeting', 'start' => '09:30', 'end' => '11:00', 'priority' => 5],
        ['title' => 'Code Review',    'start' => '11:00', 'end' => '12:00', 'priority' => 3],
        ['title' => 'Lunch',          'start' => '11:30', 'end' => '12:30', 'priority' => 1],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * 把時間字串轉成可比較的分鐘數，支援 "HH:MM" 與完整日期時間
 */
function toMinutes(string $time): int
{
    if (preg_match('/^(\d{1,2}):(\d{2})$/', trim($time), $matches) === 1) {
        return ((int) $matches[1]) * 60 + (int) $matches[2];
    }
    $timestamp = strtotime($time);
    return $timestamp === false ? 0 : (int) ($timestamp / 60);
}

/**
 * 兩個事件是否時間重疊（相接不算重疊）
 */
function isOverlapping(array $a, array $b): bool
{
    return $a['startMinute'] < $b['endMinute'] && $b['startMinute'] < $a['endMinute'];
}

$rawInput = (string) ($_POST['events'] ?? '');
$output = null;
$error = '';

if ($rawInput !== '') {
    $events = json_decode($rawInput, true);

    if (!is_array($events)) {
        $error = '輸入的內容不是合法的 JSON 陣列。';
    } else {
        // 正規化欄位並換算時間
        $normalized = [];
        foreach ($events as $event) {
            if (!isset($event['title'], $event['start'], $event['end'])) {
                continue;
            }
            $normalized[] = [
                'title'       => (string) $event['title'],
                'start'       => (string) $event['start'],
                'end'         => (string) $event['end'],
                'priority'    => (int) ($event['priority'] ?? 0),
                'startMinute' => toMinutes((string) $event['start']),
                'endMinute'   => toMinutes((string) $event['end']),
            ];
        }

        // 貪心：優先序高的先進場；同優先序時，先開始的先進場
        usort($normalized, static function (array $a, array $b): int {
            if ($a['priority'] !== $b['priority']) {
                return $b['priority'] <=> $a['priority'];
            }
            return $a['startMinute'] <=> $b['startMinute'];
        });

        $kept = [];
        $removed = [];

        foreach ($normalized as $event) {
            $conflictWith = null;
            foreach ($kept as $accepted) {
                if (isOverlapping($event, $accepted)) {
                    $conflictWith = $accepted['title'];
                    break;
                }
            }

            if ($conflictWith === null) {
                $kept[] = $event;
            } else {
                $removed[] = [
                    'title'        => $event['title'],
                    'start'        => $event['start'],
                    'end'          => $event['end'],
                    'priority'     => $event['priority'],
                    'conflictWith' => $conflictWith,
                    'reason'       => '與優先序較高的「' . $conflictWith . '」時間重疊',
                ];
            }
        }

        // 最終行程依開始時間排序
        usort($kept, static function (array $a, array $b): int {
            return $a['startMinute'] <=> $b['startMinute'];
        });

        $schedule = array_map(static function (array $event): array {
            return [
                'title'    => $event['title'],
                'start'    => $event['start'],
                'end'      => $event['end'],
                'priority' => $event['priority'],
            ];
        }, $kept);

        $output = json_encode([
            'schedule' => $schedule,
            'removed'  => $removed,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>C4 時間衝突解決工具</title>
	<style>
		body {
			margin: 0px;
			padding: 34px 20px;
			background: #f6f8fa;
			font-family: "Microsoft JhengHei", Arial, Helvetica, sans-serif;
			color: #1f2328;
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 16px;
		}

		h1 {
			font-size: 20px;
			margin: 0px;
		}

		.hint {
			color: #59636e;
			font-size: 14px;
			margin: 0px;
		}

		form {
			width: min(90vw, 760px);
			display: flex;
			flex-direction: column;
			gap: 12px;
		}

		textarea {
			width: 100%;
			min-height: 220px;
			font-family: Consolas, "Courier New", monospace;
			font-size: 13px;
			line-height: 1.6;
			padding: 12px;
			border: 1px solid #d0d7de;
			border-radius: 8px;
			resize: vertical;
			box-sizing: border-box;
		}

		button {
			align-self: flex-start;
			background: #1f6feb;
			color: #ffffff;
			border: none;
			border-radius: 6px;
			padding: 10px 26px;
			font-size: 15px;
			font-family: inherit;
			cursor: pointer;
		}

		button:hover {
			background: #3b82f6;
		}

		pre {
			width: min(90vw, 760px);
			background: #0f1216;
			color: #e6edf3;
			font-size: 13px;
			line-height: 1.6;
			padding: 16px;
			border-radius: 8px;
			overflow-x: auto;
			box-sizing: border-box;
		}

		.error {
			color: #b42318;
		}
	</style>
</head>

<body>
	<h1>時間衝突解決工具</h1>
	<p class="hint">輸入事件的 JSON 陣列（title / start / end / priority），送出後會保留優先序高的事件並列出被移除的紀錄。</p>

	<form method="post">
		<textarea name="events"><?= htmlspecialchars($rawInput !== '' ? $rawInput : defaultInput(), ENT_QUOTES, 'UTF-8') ?></textarea>
		<button type="submit">處理</button>
	</form>

	<?php if ($error !== ''): ?>
		<p class="hint error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
	<?php endif; ?>

	<?php if ($output !== null): ?>
		<pre><?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?></pre>
	<?php endif; ?>
</body>

</html>
