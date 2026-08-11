<?php
/**
 * 使用者管理（僅 admin 角色可存取）
 *
 * @var array      $users
 * @var array|null $editing
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;

$editing = $editing ?? null;
?>
<div class="split-layout">
    <section class="panel">
        <h2 class="panel__title"><?= $editing ? 'Edit user' : 'Add new user' ?></h2>

        <form method="post" action="<?= Html::e(Url::to('admin/users')) ?>">
            <?= Csrf::field() ?>
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <label class="form-label" for="username">Username</label>
                <input class="form-input" type="text" id="username" name="username"
                       value="<?= Html::e($editing['username'] ?? '') ?>"
                       <?= $editing ? 'readonly' : 'required' ?>>
            </div>

            <div class="form-row">
                <label class="form-label" for="display_name">Display name</label>
                <input class="form-input" type="text" id="display_name" name="display_name"
                       value="<?= Html::e($editing['display_name'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <label class="form-label" for="email">Email</label>
                <input class="form-input" type="email" id="email" name="email"
                       value="<?= Html::e($editing['email'] ?? '') ?>">
            </div>

            <div class="form-row">
                <label class="form-label" for="role">Role</label>
                <select class="form-input" id="role" name="role">
                    <option value="admin" <?= ($editing['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin — full access</option>
                    <option value="editor" <?= ($editing['role'] ?? 'editor') === 'editor' ? 'selected' : '' ?>>Editor — content only</option>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password" autocomplete="new-password">
                <?php if ($editing): ?>
                    <span class="form-hint">Leave empty to keep the current password.</span>
                <?php endif; ?>
            </div>

            <button class="button button--primary" type="submit"><?= $editing ? 'Update user' : 'Add user' ?></button>
            <?php if ($editing): ?>
                <a class="button button--ghost button--small" href="<?= Html::e(Url::to('admin/users')) ?>">Cancel</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="table-wrap">
        <table class="table">
            <caption class="screen-reader-text">All users</caption>
            <thead>
                <tr>
                    <th scope="col">User</th>
                    <th scope="col">Role</th>
                    <th scope="col">Created</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <th scope="row">
                            <span class="table__title"><?= Html::e($user['display_name']) ?></span>
                            <span class="table__sub"><?= Html::e($user['username']) ?> · <?= Html::e($user['email']) ?></span>
                        </th>
                        <td><span class="tag tag--<?= Html::e($user['role']) ?>"><?= Html::e($user['role']) ?></span></td>
                        <td><?= Html::e(Html::date($user['created_at'], 'j M Y')) ?></td>
                        <td class="table__actions">
                            <a href="<?= Html::e(Url::to('admin/users/edit/' . $user['id'])) ?>">Edit</a>
                            <form method="post" action="<?= Html::e(Url::to('admin/users/delete/' . $user['id'])) ?>"
                                  onsubmit="return confirm('Delete this user?');">
                                <?= Csrf::field() ?>
                                <button class="link-button link-button--danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
