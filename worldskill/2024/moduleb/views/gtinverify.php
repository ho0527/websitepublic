<?php
/**
 * 公開的 GTIN 批量驗證頁面
 *
 * @var string                                                                          $submittedText
 * @var array<int,array{gtin:string,isValid:bool,reason:string,productName:string}>|null $results 尚未送出時為 null
 * @var bool                                                                            $allValid
 */
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => 'GTIN bulk verification',
    'navigationLinks' => [
        ['label' => 'admin', 'url' => urlFor('/login')],
    ],
]) ?>
		<div class="center">
			<form id="form" method="post" action="<?= h(urlFor('/gtin')) ?>">
				<div class="sectiontitle">GTIN bulk verification</div>
				<div class="inputdiv">
					<div class="label">Paste the GTIN numbers, one per line</div>
					<textarea id="gtin_list" name="gtin_list" rows="8" placeholder="03000123456789&#10;3000123456790"><?= h($submittedText) ?></textarea>
				</div>
				<input type="submit" id="submit" value="submit">
			</form>

<?php if ($results !== null): ?>
			<div class="allvalid<?= $allValid ? ' show' : '' ?>" id="allvalid">
				<span class="greentick">&#10004;</span> All valid
			</div>

			<div class="form">
				<div class="sectiontitle">Verification result</div>
<?php if ($results === []): ?>
				<div>No GTIN number was submitted.</div>
<?php else: ?>
				<table class="resulttable">
					<thead>
						<tr>
							<th>GTIN</th>
							<th>Result</th>
							<th>Detail</th>
							<th>Product</th>
						</tr>
					</thead>
					<tbody>
<?php foreach ($results as $result): ?>
						<tr>
							<td><?= h($result['gtin']) ?></td>
							<td class="<?= $result['isValid'] ? 'valid-text' : 'invalid-text' ?>">
								<?= $result['isValid'] ? '&#10004; valid' : '&#10008; invalid' ?>
							</td>
							<td><?= h($result['reason']) ?></td>
							<td>
<?php if ($result['isValid']): ?>
								<a href="<?= h(urlFor('/01/' . $result['gtin'])) ?>"><?= h($result['productName']) ?></a>
<?php endif; ?>
							</td>
						</tr>
<?php endforeach; ?>
					</tbody>
				</table>
<?php endif; ?>
			</div>
<?php endif; ?>
		</div>
