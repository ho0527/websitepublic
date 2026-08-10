<?php
/**
 * 公開產品頁面 /01/[GTIN]
 *
 * 行動裝置優先的單欄版面，使用者可以在英文與法文之間切換，
 * html 的 lang 屬性會跟著切換，個別的英文／法文文字也各自標上 lang。
 *
 * @var array<string,mixed> $product
 * @var string              $localeCode 目前顯示的語系（en 或 fr）
 */
$gtin = (string) $product['gtin'];

/** 各語系的介面文字 */
$uiTexts = [
    'en' => [
        'gtin'   => 'GTIN',
        'weight' => 'weight',
        'net'    => 'net content weight',
    ],
    'fr' => [
        'gtin'   => 'GTIN',
        'weight' => 'poids',
        'net'    => 'poids net',
    ],
];

$productName        = (string) ($product['translations'][$localeCode]['name'] ?? '');
$productDescription = (string) ($product['translations'][$localeCode]['description'] ?? '');
$labels             = $uiTexts[$localeCode];
$weightUnit         = (string) $product['weight_unit'];
?>
<!DOCTYPE html>
<html lang="<?= h($localeCode) ?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= h($productName) ?> - <?= h($product['company_name']) ?></title>
		<link rel="stylesheet" href="<?= h(assetUrl('index.css')) ?>">
	</head>
	<body class="publicpage">
		<div class="publiccard">
			<div class="langswitch">
<?php if ($localeCode === 'en'): ?>
				<span lang="en">EN</span> /
				<a lang="fr" hreflang="fr" href="<?= h(urlFor('/01/' . $gtin, ['lang' => 'fr'])) ?>">FR</a>
<?php else: ?>
				<a lang="en" hreflang="en" href="<?= h(urlFor('/01/' . $gtin, ['lang' => 'en'])) ?>">EN</a> /
				<span lang="fr">FR</span>
<?php endif; ?>
			</div>

			<div class="companyname"><?= h($product['company_name']) ?></div>

			<img class="productimage" src="<?= h(ImageUploader::publicUrl($product['image_path'])) ?>" alt="<?= h($productName) ?>">

			<div class="gtinnumber"><?= h($labels['gtin']) ?>: <?= h($gtin) ?></div>

			<h1 class="productname" lang="<?= h($localeCode) ?>"><?= h($productName) ?></h1>

			<div class="productdescription" lang="<?= h($localeCode) ?>"><?= h($productDescription) ?></div>

			<div class="weightinfo">
				<div><?= h($labels['weight']) ?>: <?= h($product['gross_weight']) ?><?= h($weightUnit) ?></div>
				<div><?= h($labels['net']) ?>: <?= h($product['net_weight']) ?><?= h($weightUnit) ?></div>
			</div>
		</div>
	</body>
</html>
