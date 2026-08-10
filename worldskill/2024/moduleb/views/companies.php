<?php
/**
 * 公司清單（一般清單會包含已停用的公司，另有「只顯示停用」的獨立清單）
 *
 * @var array<int,array<string,mixed>> $companies
 * @var bool                           $showDeactivatedOnly
 */
$navigationLinks = $showDeactivatedOnly
    ? [
        ['label' => 'all companies', 'url' => urlFor('/companies')],
        ['label' => 'new company', 'url' => urlFor('/companies/new')],
    ]
    : [
        ['label' => 'products', 'url' => urlFor('/products')],
        ['label' => 'deactivate company list', 'url' => urlFor('/companies/deactivated')],
        ['label' => 'new company', 'url' => urlFor('/companies/new')],
        ['label' => 'logout', 'url' => urlFor('/logout')],
    ];
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => $showDeactivatedOnly ? 'Deactivated companies' : 'Companies',
    'navigationLinks' => $navigationLinks,
]) ?>
		<div class="companymain" id="main">
<?php if ($companies === []): ?>
			<div class="sectiontitle">No company record.</div>
<?php endif; ?>
<?php foreach ($companies as $company): ?>
			<div class="companydiv" data-id="<?= h($company['id']) ?>"
				onclick="location.href='<?= h(urlFor('/companies/' . (int) $company['id'])) ?>'">
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
				<div onclick="event.stopPropagation()">
					<a href="<?= h(urlFor('/companies/' . (int) $company['id'] . '/edit')) ?>" class="a">edit</a>
<?php if ((int) $company['is_active'] === 1): ?>
					<form method="post" style="display:inline"
						action="<?= h(urlFor('/companies/' . (int) $company['id'] . '/deactivate')) ?>"
						onsubmit="return confirm('confirm deactivate?')">
						<input type="submit" class="danger" value="deactivate">
					</form>
<?php else: ?>
					<form method="post" style="display:inline"
						action="<?= h(urlFor('/companies/' . (int) $company['id'] . '/activate')) ?>">
						<input type="submit" class="neutral" value="activate">
					</form>
<?php endif; ?>
				</div>
			</div>
<?php endforeach; ?>
		</div>
