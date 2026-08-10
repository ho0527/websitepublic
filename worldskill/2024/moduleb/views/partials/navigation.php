<?php
/**
 * 後台共用導覽列
 *
 * @var string $navigationTitle 左側標題文字
 * @var array<int,array{label:string,url:string}> $navigationLinks 右側連結
 */
$navigationTitle = $navigationTitle ?? '';
$navigationLinks = $navigationLinks ?? [];
?>
		<div class="navigationbar">
			<div><?= h($navigationTitle) ?></div>
			<div>
<?php foreach ($navigationLinks as $navigationLink): ?>
				<a href="<?= h($navigationLink['url']) ?>" class="a"><?= h($navigationLink['label']) ?></a>
<?php endforeach; ?>
			</div>
		</div>
