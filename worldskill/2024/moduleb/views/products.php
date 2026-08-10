<?php
/**
 * 產品清單（全部產品 / 只顯示隱藏產品）
 *
 * @var array<int,array<string,mixed>> $products
 * @var string                         $pageHeading
 * @var bool                           $showHiddenOnly
 */
$navigationLinks = $showHiddenOnly
    ? [
        ['label' => 'all products', 'url' => urlFor('/products')],
        ['label' => 'companies', 'url' => urlFor('/companies')],
    ]
    : [
        ['label' => 'companies', 'url' => urlFor('/companies')],
        ['label' => 'hidden products', 'url' => urlFor('/products/hidden')],
        ['label' => 'new product', 'url' => urlFor('/products/new')],
        ['label' => 'products.json', 'url' => urlFor('/products.json')],
        ['label' => 'logout', 'url' => urlFor('/logout')],
    ];
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => $pageHeading,
    'navigationLinks' => $navigationLinks,
]) ?>
		<div class="companymain" id="main">
<?php if ($products === []): ?>
			<div class="sectiontitle">No product record.</div>
<?php endif; ?>
<?php foreach ($products as $product): ?>
			<div class="companydiv" onclick="location.href='<?= h(urlFor('/products/' . $product['gtin'])) ?>'">
				<div><img src="<?= h(ImageUploader::publicUrl($product['image_path'])) ?>" alt="<?= h($product['translations']['en']['name'] ?? '') ?>" class="image"></div>
				<div>
					name: <?= h($product['translations']['en']['name'] ?? '') ?>
<?php if ((int) $product['is_hidden'] === 1): ?>
					<span class="statustag hidden-tag">hidden</span>
<?php endif; ?>
				</div>
				<div>name (fr): <?= h($product['translations']['fr']['name'] ?? '') ?></div>
				<div>gtin: <?= h($product['gtin']) ?></div>
				<div>brand: <?= h($product['brand']) ?></div>
				<div>country of origin: <?= h($product['country_of_origin']) ?></div>
				<div>company: <?= h($product['company_name']) ?></div>
				<div>gross weight: <?= h($product['gross_weight']) ?> <?= h($product['weight_unit']) ?></div>
				<div>net content weight: <?= h($product['net_weight']) ?> <?= h($product['weight_unit']) ?></div>
				<div onclick="event.stopPropagation()">
					<a href="<?= h(urlFor('/products/' . $product['gtin'] . '/edit')) ?>" class="a">edit</a>
<?php if ($showHiddenOnly): ?>
					<form method="post" style="display:inline"
						action="<?= h(urlFor('/products/' . $product['gtin'] . '/delete')) ?>"
						onsubmit="return confirm('permanently delete this hidden product?')">
						<input type="submit" class="danger" value="delete permanently">
					</form>
<?php endif; ?>
				</div>
			</div>
<?php endforeach; ?>
		</div>
