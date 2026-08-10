<?php
/**
 * 管理員登入頁
 *
 * @var string $errorMessage 密碼錯誤時的提示訊息
 */
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => 'Made-in-France Products Management',
    'navigationLinks' => [
        ['label' => 'GTIN verification', 'url' => urlFor('/gtin')],
    ],
]) ?>
		<div class="center">
			<form id="form" method="post" action="<?= h(urlFor('/login')) ?>">
				<div class="sectiontitle">Admin login</div>
<?php if ($errorMessage !== ''): ?>
				<div class="errorbox"><?= h($errorMessage) ?></div>
<?php endif; ?>
				<div class="inputdiv">
					<div class="label">enter passphrase</div>
					<input type="password" id="passphrase" name="passphrase" autocomplete="current-password" autofocus required>
				</div>

				<input type="submit" id="submit" value="submit">
			</form>
		</div>
