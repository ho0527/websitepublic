<?php
/**
 * 產品新增／編輯共用表單
 *
 * @var string                         $pageHeading
 * @var string                         $formAction
 * @var array<string,mixed>            $product      表單目前的欄位值（新增時為空陣列）
 * @var array<int,array<string,mixed>> $companies    可選擇的公司
 * @var array<int,string>              $errors
 * @var string|null                    $currentImage 目前已上傳的圖片檔名
 * @var bool                           $isEditing    是否為編輯模式
 */
$fieldValue = static fn (string $fieldName): string => (string) ($product[$fieldName] ?? '');
/** 取得某語系的欄位值 */
$translationValue = static function (string $localeCode, string $fieldName) use ($product): string {
    return (string) ($product['translations'][$localeCode][$fieldName] ?? '');
};
$selectedCompanyId = (int) ($product['company_id'] ?? 0);
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => $pageHeading,
    'navigationLinks' => [
        ['label' => 'back', 'url' => urlFor('/products')],
    ],
]) ?>
		<div class="center">
			<form id="form" method="post" action="<?= h($formAction) ?>" enctype="multipart/form-data">
				<div class="sectiontitle"><?= h($pageHeading) ?></div>
<?php if ($errors !== []): ?>
				<div class="errorbox">
					Please fix the following problems:
					<ul>
<?php foreach ($errors as $errorMessage): ?>
						<li><?= h($errorMessage) ?></li>
<?php endforeach; ?>
					</ul>
				</div>
<?php endif; ?>

				<div class="inputdiv">
					<div class="label">product image</div>
					<img src="<?= h(ImageUploader::publicUrl($currentImage)) ?>" id="filepreview" class="filepreview" alt="product image preview">
					<br>
					<input type="file" id="file" name="image" accept="image/*">
<?php if ($isEditing && $currentImage !== null && $currentImage !== ''): ?>
					<label><input type="checkbox" name="remove_image" value="1"> remove the uploaded image</label>
<?php endif; ?>
				</div>

				<div class="inputdiv">
					<div class="label">company</div>
					<select id="company_id" name="company_id" required>
						<option value="">-- choose a company --</option>
<?php foreach ($companies as $companyOption): ?>
						<option value="<?= h($companyOption['id']) ?>"<?= (int) $companyOption['id'] === $selectedCompanyId ? ' selected' : '' ?>>
							<?= h($companyOption['name']) ?><?= (int) $companyOption['is_active'] === 0 ? ' (deactivated)' : '' ?>
						</option>
<?php endforeach; ?>
					</select>
				</div>

				<div class="inputdiv">
					<div class="label">GTIN (13 or 14 digits)</div>
					<input type="text" id="gtin" name="gtin" value="<?= h($fieldValue('gtin')) ?>" required>
				</div>

				<div class="inputdiv">
					<div class="label">name (English)</div>
					<input type="text" id="name_en" name="name_en" value="<?= h($translationValue('en', 'name')) ?>" required>
				</div>
				<div class="inputdiv">
					<div class="label">name in French</div>
					<input type="text" id="name_fr" name="name_fr" value="<?= h($translationValue('fr', 'name')) ?>" required>
				</div>
				<div class="inputdiv">
					<div class="label">description (English)</div>
					<textarea id="description_en" name="description_en"><?= h($translationValue('en', 'description')) ?></textarea>
				</div>
				<div class="inputdiv">
					<div class="label">description in French</div>
					<textarea id="description_fr" name="description_fr"><?= h($translationValue('fr', 'description')) ?></textarea>
				</div>

				<div class="inputdiv">
					<div class="label">product brand name</div>
					<input type="text" id="brand" name="brand" value="<?= h($fieldValue('brand')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">product country of origin</div>
					<input type="text" id="country_of_origin" name="country_of_origin" value="<?= h($fieldValue('country_of_origin')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">product gross weight (with packaging)</div>
					<input type="number" id="gross_weight" name="gross_weight" min="0" step="0.001" value="<?= h($fieldValue('gross_weight')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">product net content weight</div>
					<input type="number" id="net_weight" name="net_weight" min="0" step="0.001" value="<?= h($fieldValue('net_weight')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">product weight unit</div>
					<input type="text" id="weight_unit" name="weight_unit" value="<?= h($fieldValue('weight_unit') === '' ? 'kg' : $fieldValue('weight_unit')) ?>" required>
				</div>
				<div class="inputdiv">
					<label><input type="checkbox" id="is_hidden" name="is_hidden" value="1"<?= (int) ($product['is_hidden'] ?? 0) === 1 ? ' checked' : '' ?>> mark this product as hidden</label>
				</div>

				<input type="submit" id="submit" value="submit">
			</form>
		</div>

		<script>
			// 選擇檔案後即時預覽，不需要送出表單就能確認選到正確的圖片
			document.getElementById("file").onchange = function () {
				if (this.files.length === 0) {
					return
				}

				const fileReader = new FileReader()
				fileReader.onload = function () {
					document.getElementById("filepreview").src = fileReader.result
				}
				fileReader.readAsDataURL(this.files[0])
			}
		</script>
