<?php
/**
 * 單一公司頁面：公司資料與旗下所有產品
 *
 * @var array<string,mixed>            $company
 * @var array<int,array<string,mixed>> $products
 */
$companyId = (int) $company['id'];
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => 'Company detail',
    'navigationLinks' => [
        ['label' => 'back', 'url' => urlFor('/companies')],
        ['label' => 'new product', 'url' => urlFor('/products/new', ['company_id' => $companyId])],
    ],
]) ?>
		<div class="companymain" id="main">
			<div class="companydiv fill cursor-initial">
				<div>
					name: <?= h($company['name']) ?>
<?php if ((int) $company['is_active'] === 1): ?>
					<span class="statustag active-tag">active</span>
<?php else: ?>
					<span class="statustag">deactivated</span>
<?php endif; ?>
				</div>
				<div>address: <?= h($company['address']) ?></div>
				<div>telephone: <?= h($company['telephone']) ?></div>
				<div>email: <?= h($company['email']) ?></div>
				<div>
					owner:
					<div>name: <?= h($company['owner_name']) ?></div>
					<div>mobile number: <?= h($company['owner_mobile']) ?></div>
					<div>email: <?= h($company['owner_email']) ?></div>
				</div>
				<div>
					contact:
					<div>name: <?= h($company['contact_name']) ?></div>
					<div>mobile number: <?= h($company['contact_mobile']) ?></div>
					<div>email: <?= h($company['contact_email']) ?></div>
				</div>
				<div>
					<a href="<?= h(urlFor('/companies/' . $companyId . '/edit')) ?>" class="a">edit</a>
<?php if ((int) $company['is_active'] === 1): ?>
					<form method="post" style="display:inline"
						action="<?= h(urlFor('/companies/' . $companyId . '/deactivate')) ?>"
						onsubmit="return confirm('confirm deactivate? all products of this company will be hidden.')">
						<input type="submit" class="danger" value="deactivate">
					</form>
<?php else: ?>
					<form method="post" style="display:inline"
						action="<?= h(urlFor('/companies/' . $companyId . '/activate')) ?>">
						<input type="submit" class="neutral" value="activate">
					</form>
<?php endif; ?>
				</div>
			</div>

			<div class="sectiontitle">Products of this company (<?= count($products) ?>)</div>

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
				<div>gross weight: <?= h($product['gross_weight']) ?> <?= h($product['weight_unit']) ?></div>
				<div>net content weight: <?= h($product['net_weight']) ?> <?= h($product['weight_unit']) ?></div>
				<div onclick="event.stopPropagation()">
					<a href="<?= h(urlFor('/products/' . $product['gtin'] . '/edit')) ?>" class="a">edit</a>
				</div>
			</div>
<?php endforeach; ?>
		</div>
