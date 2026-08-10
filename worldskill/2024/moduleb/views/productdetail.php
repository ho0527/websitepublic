<?php
/**
 * 單一產品的管理頁面（網址 /products/[GTIN]）
 *
 * @var array<string,mixed> $product
 */
$gtin     = (string) $product['gtin'];
$isHidden = (int) $product['is_hidden'] === 1;
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => 'Product ' . $gtin,
    'navigationLinks' => [
        ['label' => 'back', 'url' => urlFor('/products')],
        ['label' => 'company', 'url' => urlFor('/companies/' . (int) $product['company_id'])],
        ['label' => 'public page', 'url' => urlFor('/01/' . $gtin)],
        ['label' => 'json', 'url' => urlFor('/products/' . $gtin . '.json')],
        ['label' => 'edit', 'url' => urlFor('/products/' . $gtin . '/edit')],
    ],
]) ?>
		<div class="companymain" id="main">
			<div class="companydiv fill cursor-initial">
				<div><img src="<?= h(ImageUploader::publicUrl($product['image_path'])) ?>" alt="<?= h($product['translations']['en']['name'] ?? '') ?>" class="image filepreview"></div>
				<div>
					name: <?= h($product['translations']['en']['name'] ?? '') ?>
<?php if ($isHidden): ?>
					<span class="statustag hidden-tag">hidden</span>
<?php endif; ?>
				</div>
				<div>name in French: <?= h($product['translations']['fr']['name'] ?? '') ?></div>
				<div>gtin: <?= h($gtin) ?></div>
				<div>description: <?= nl2br(h($product['translations']['en']['description'] ?? '')) ?></div>
				<div>description in French: <?= nl2br(h($product['translations']['fr']['description'] ?? '')) ?></div>
				<div>brand: <?= h($product['brand']) ?></div>
				<div>country of origin: <?= h($product['country_of_origin']) ?></div>
				<div>company: <?= h($product['company_name']) ?></div>
				<div>gross weight: <?= h($product['gross_weight']) ?> <?= h($product['weight_unit']) ?></div>
				<div>net content weight: <?= h($product['net_weight']) ?> <?= h($product['weight_unit']) ?></div>

				<div>
<?php if ($isHidden): ?>
					<form method="post" style="display:inline" action="<?= h(urlFor('/products/' . $gtin . '/unhide')) ?>">
						<input type="submit" class="neutral" value="show this product">
					</form>
					<form method="post" style="display:inline"
						action="<?= h(urlFor('/products/' . $gtin . '/delete')) ?>"
						onsubmit="return confirm('permanently delete this hidden product?')">
						<input type="submit" class="danger" value="delete permanently">
					</form>
<?php else: ?>
					<form method="post" style="display:inline" action="<?= h(urlFor('/products/' . $gtin . '/hide')) ?>">
						<input type="submit" class="danger" value="mark as hidden">
					</form>
					<span>(only hidden products can be permanently deleted)</span>
<?php endif; ?>
				</div>
<?php if ($product['image_path'] !== null && $product['image_path'] !== ''): ?>
				<div>
					<form method="post" action="<?= h(urlFor('/products/' . $gtin . '/image/remove')) ?>"
						onsubmit="return confirm('remove the uploaded image?')">
						<input type="submit" class="neutral" value="remove image">
					</form>
				</div>
<?php endif; ?>
			</div>
		</div>
