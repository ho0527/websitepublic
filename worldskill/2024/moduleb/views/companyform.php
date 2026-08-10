<?php
/**
 * 公司新增／編輯共用表單
 *
 * @var string               $pageHeading
 * @var string               $formAction
 * @var array<string,mixed>  $company 表單目前的欄位值（新增時為空陣列）
 * @var array<int,string>    $errors  驗證錯誤訊息
 */
/** 取得欄位值的小工具，避免未設定的索引造成警告 */
$fieldValue = static fn (string $fieldName): string => (string) ($company[$fieldName] ?? '');
?>
<?= View::capture('partials/navigation', [
    'navigationTitle' => $pageHeading,
    'navigationLinks' => [
        ['label' => 'back', 'url' => urlFor('/companies')],
    ],
]) ?>
		<div class="center">
			<form id="form" method="post" action="<?= h($formAction) ?>">
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
					<div class="label">company name</div>
					<input type="text" id="name" name="name" value="<?= h($fieldValue('name')) ?>" required>
				</div>
				<div class="inputdiv">
					<div class="label">company address</div>
					<input type="text" id="address" name="address" value="<?= h($fieldValue('address')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">company telephone number</div>
					<input type="text" id="telephone" name="telephone" value="<?= h($fieldValue('telephone')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">company email address</div>
					<input type="text" id="email" name="email" value="<?= h($fieldValue('email')) ?>">
				</div>

				<div class="sectiontitle">Owner information</div>
				<div class="inputdiv">
					<div class="label">owner's name</div>
					<input type="text" id="owner_name" name="owner_name" value="<?= h($fieldValue('owner_name')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">owner's mobile number</div>
					<input type="text" id="owner_mobile" name="owner_mobile" value="<?= h($fieldValue('owner_mobile')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">owner's email address</div>
					<input type="text" id="owner_email" name="owner_email" value="<?= h($fieldValue('owner_email')) ?>">
				</div>

				<div class="sectiontitle">Contact information</div>
				<div class="inputdiv">
					<div class="label">contact's name</div>
					<input type="text" id="contact_name" name="contact_name" value="<?= h($fieldValue('contact_name')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">contact's mobile number</div>
					<input type="text" id="contact_mobile" name="contact_mobile" value="<?= h($fieldValue('contact_mobile')) ?>">
				</div>
				<div class="inputdiv">
					<div class="label">contact's email address</div>
					<input type="text" id="contact_email" name="contact_email" value="<?= h($fieldValue('contact_email')) ?>">
				</div>

				<input type="submit" id="submit" value="submit">
			</form>
		</div>
