<?php
/**
 * 後台控制器（<host>/admin/）
 *
 * 權限：
 *   admin  — 全部功能
 *   editor — 只能管理博物館、新聞、分類與媒體
 *
 * 所有寫入動作都必須通過 CSRF 驗證與權限檢查，
 * 資料庫存取一律使用 prepared statement。
 */

declare(strict_types=1);

namespace App\Controller;

use App\Core\App;
use App\Core\Csrf;
use App\Core\Html;
use App\Core\Router;
use App\Core\Url;

final class AdminController
{
    private string $viewDir;

    public function __construct(private App $app)
    {
        $this->viewDir = $app->basePath . '/admin-ui';
    }

    public function handle(array $segments): void
    {
        $section = $segments[0] ?? '';
        $action  = $segments[1] ?? '';
        $id      = (int) ($segments[2] ?? 0);

        // 登入／登出不需要既有登入狀態
        if ($section === 'logout') {
            $this->app->auth->logout();
            Router::redirect('admin');
        }

        if (!$this->app->auth->check()) {
            $this->login();

            return;
        }

        match ($section) {
            ''           => $this->dashboard(),
            'museums'    => $this->museums($action, $id),
            'posts'      => $this->posts($action, $id),
            'categories' => $this->categories($action, $id),
            'media'      => $this->media($action),
            'plugins'    => $this->plugins($action),
            'users'      => $this->users($action, $id),
            'security'   => $this->security($action),
            'settings'   => $this->settings(),
            default      => $this->notFound(),
        };
    }

    /* ================================================================ 登入 */

    /**
     * 登入頁（白標）
     *
     * 規格：不顯示任何 CMS 標誌、不出現 CMS 名稱字樣，
     *       背景為滿版的博物館照片（可於後台設定更換）。
     */
    private function login(): void
    {
        $error    = '';
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if (!Csrf::verify($_POST['_token'] ?? null)) {
                $error = 'Your session has expired. Please try again.';
            } elseif ($this->app->auth->isLockedOut()) {
                $error = sprintf(
                    'Too many failed attempts. This address is blocked for %d minutes.',
                    $this->app->auth->lockoutMinutes()
                );
            } elseif ($username === '' || $password === '') {
                $error = 'Please fill in both the username and the password.';
            } elseif ($this->app->auth->attempt($username, $password)) {
                Router::redirect('admin');
            } else {
                $error = 'The username or password is not correct.';
            }
        }

        $this->renderRaw('login', [
            'error'      => $error,
            'username'   => $username,
            'background' => $this->app->setting('login_background'),
            'lockedOut'  => $this->app->auth->isLockedOut(),
        ]);
    }

    /* ============================================================ 儀表板 */

    /**
     * 儀表板
     *
     * 規格：只保留 At a Glance、Activity、Quick Draft 三個小工具，
     *       並可由管理者在「Screen Options」中設定顯示哪些。
     */
    private function dashboard(): void
    {
        // Quick Draft：直接建立一篇草稿
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'quick_draft') {
            $this->guard('manage_posts');

            $title = trim((string) ($_POST['title'] ?? ''));
            if ($title === '') {
                $this->flash('error', 'A draft needs a title.');
            } else {
                $categories = $this->app->categories->all();
                $this->app->posts->create([
                    'title'          => $title,
                    'slug'           => $this->uniquePostSlug(Html::slugify($title)),
                    'excerpt'        => Html::excerpt((string) ($_POST['content'] ?? ''), 180),
                    'content'        => trim((string) ($_POST['content'] ?? '')),
                    'featured_image' => '',
                    'category_id'    => (int) ($_POST['category_id'] ?? ($categories[0]['id'] ?? 1)),
                    'author_id'      => (int) $this->app->auth->user()['id'],
                    'status'         => 'draft',
                    'published_at'   => date('Y-m-d H:i:s'),
                ]);
                $this->flash('success', 'Draft saved. You can finish it in the News section.');
            }

            Router::redirect('admin');
        }

        // Screen Options：設定顯示哪些小工具
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'screen_options') {
            $this->guard('manage_settings');

            $allowed = ['at_a_glance', 'activity', 'quick_draft'];
            $chosen  = array_values(array_intersect($allowed, (array) ($_POST['widgets'] ?? [])));
            $this->app->settings->set('dashboard_widgets', implode(',', $chosen));
            $this->flash('success', 'Dashboard widgets updated.');

            Router::redirect('admin');
        }

        $widgets = array_filter(explode(',', $this->app->setting('dashboard_widgets', 'at_a_glance,activity,quick_draft')));

        $this->render('dashboard', 'Dashboard', [
            'widgets'    => $widgets,
            'counts'     => [
                'museums'    => $this->app->museums->count(),
                'published'  => $this->app->posts->countByStatus('published'),
                'drafts'     => $this->app->posts->countByStatus('draft'),
                'categories' => $this->app->categories->count(),
                'media'      => $this->app->media->count(),
                'plugins'    => $this->app->plugins->countActive(),
                'admins'     => $this->app->users->countByRole('admin'),
                'editors'    => $this->app->users->countByRole('editor'),
            ],
            'activity'   => $this->app->posts->recentActivity(6),
            'categories' => $this->app->categories->all(),
            'failures'   => $this->app->loginAttempts->countFailures(),
        ]);
    }

    /* ============================================================ 博物館 */

    private function museums(string $action, int $id): void
    {
        $this->guard('manage_museums');

        if ($action === 'delete') {
            $this->verifyCsrf();
            $this->app->museums->delete($id);
            $this->flash('success', 'Museum deleted.');
            Router::redirect('admin/museums');
        }

        if ($action === 'new' || $action === 'edit') {
            $museum = $action === 'edit' ? $this->app->museums->find($id) : null;
            if ($action === 'edit' && $museum === null) {
                $this->notFound();

                return;
            }

            $errors = [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->verifyCsrf();

                $data = [
                    'title'          => trim((string) ($_POST['title'] ?? '')),
                    'slug'           => Html::slugify((string) ($_POST['slug'] ?? '')),
                    'excerpt'        => trim((string) ($_POST['excerpt'] ?? '')),
                    'content'        => trim((string) ($_POST['content'] ?? '')),
                    'featured_image' => trim((string) ($_POST['featured_image'] ?? '')),
                    'gallery'        => trim((string) ($_POST['gallery'] ?? '')),
                    'address'        => trim((string) ($_POST['address'] ?? '')),
                    'opening_hours'  => trim((string) ($_POST['opening_hours'] ?? '')),
                    'is_selected'    => isset($_POST['is_selected']) ? 1 : 0,
                    'status'         => ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published',
                    'sort_order'     => (int) ($_POST['sort_order'] ?? 0),
                    'category_id'    => ($_POST['category_id'] ?? '') === '' ? null : (int) $_POST['category_id'],
                ];

                if ($data['title'] === '') {
                    $errors['title'] = 'Please enter the museum name.';
                }
                if ($data['slug'] === '') {
                    $data['slug'] = Html::slugify($data['title']);
                }
                if ($data['slug'] === '') {
                    $errors['slug'] = 'Please enter a URL slug.';
                } elseif ($this->app->museums->slugExists($data['slug'], $museum['id'] ?? null)) {
                    $errors['slug'] = 'This URL slug is already used by another museum.';
                }
                if ($data['content'] === '') {
                    $errors['content'] = 'Please write the museum description.';
                }

                if (empty($errors)) {
                    if ($museum === null) {
                        $newId = $this->app->museums->create($data);
                        $this->flash('success', 'Museum created.');
                        Router::redirect('admin/museums/edit/' . $newId);
                    }

                    $this->app->museums->update((int) $museum['id'], $data);
                    $this->flash('success', 'Museum updated.');
                    Router::redirect('admin/museums/edit/' . $museum['id']);
                }

                // 有錯誤時把使用者輸入回填表單
                $museum = array_merge($museum ?? [], $data);
            }

            $this->render('museum-form', $action === 'new' ? 'Add new museum' : 'Edit museum', [
                'museum'     => $museum,
                'errors'     => $errors,
                'categories' => $this->app->categories->all(),
                'images'     => $this->app->media->images(),
            ]);

            return;
        }

        $this->render('museums', 'Museums', ['museums' => $this->app->museums->all()]);
    }

    /* ============================================================== 新聞 */

    private function posts(string $action, int $id): void
    {
        $this->guard('manage_posts');

        if ($action === 'delete') {
            $this->verifyCsrf();
            $this->app->posts->delete($id);
            $this->flash('success', 'Post deleted.');
            Router::redirect('admin/posts');
        }

        if ($action === 'new' || $action === 'edit') {
            $post = $action === 'edit' ? $this->app->posts->find($id) : null;
            if ($action === 'edit' && $post === null) {
                $this->notFound();

                return;
            }

            $errors = [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->verifyCsrf();

                $data = [
                    'title'          => trim((string) ($_POST['title'] ?? '')),
                    'slug'           => Html::slugify((string) ($_POST['slug'] ?? '')),
                    'excerpt'        => trim((string) ($_POST['excerpt'] ?? '')),
                    'content'        => trim((string) ($_POST['content'] ?? '')),
                    'featured_image' => trim((string) ($_POST['featured_image'] ?? '')),
                    'category_id'    => (int) ($_POST['category_id'] ?? 0),
                    'status'         => ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published',
                    'published_at'   => ($_POST['published_at'] ?? '') !== ''
                        ? str_replace('T', ' ', (string) $_POST['published_at']) . ':00'
                        : date('Y-m-d H:i:s'),
                ];

                if ($data['title'] === '') {
                    $errors['title'] = 'Please enter a title.';
                }
                if ($data['slug'] === '') {
                    $data['slug'] = Html::slugify($data['title']);
                }
                if ($data['slug'] === '') {
                    $errors['slug'] = 'Please enter a URL slug.';
                } elseif ($this->app->posts->slugExists($data['slug'], $post['id'] ?? null)) {
                    $errors['slug'] = 'This URL slug is already used by another post.';
                }
                if ($data['category_id'] <= 0) {
                    $errors['category_id'] = 'Please choose a category.';
                }
                if ($data['content'] === '') {
                    $errors['content'] = 'Please write the post content.';
                }
                if ($data['excerpt'] === '') {
                    $data['excerpt'] = Html::excerpt($data['content'], 180);
                }

                if (empty($errors)) {
                    if ($post === null) {
                        $data['author_id'] = (int) $this->app->auth->user()['id'];
                        $newId = $this->app->posts->create($data);
                        $this->flash('success', 'Post created.');
                        Router::redirect('admin/posts/edit/' . $newId);
                    }

                    $this->app->posts->update((int) $post['id'], $data);
                    $this->flash('success', 'Post updated.');
                    Router::redirect('admin/posts/edit/' . $post['id']);
                }

                $post = array_merge($post ?? [], $data);
            }

            $this->render('post-form', $action === 'new' ? 'Add new post' : 'Edit post', [
                'post'       => $post,
                'errors'     => $errors,
                'categories' => $this->app->categories->all(),
                'images'     => $this->app->media->images(),
            ]);

            return;
        }

        $this->render('posts', 'News posts', ['posts' => $this->app->posts->all()]);
    }

    /* ============================================================== 分類 */

    private function categories(string $action, int $id): void
    {
        $this->guard('manage_terms');

        if ($action === 'delete') {
            $this->verifyCsrf();
            try {
                $this->app->categories->delete($id);
                $this->flash('success', 'Category deleted.');
            } catch (\PDOException) {
                // 外鍵限制：分類底下還有文章時不允許刪除
                $this->flash('error', 'This category still has posts and cannot be deleted.');
            }
            Router::redirect('admin/categories');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $data = [
                'name'        => trim((string) ($_POST['name'] ?? '')),
                'slug'        => Html::slugify((string) ($_POST['slug'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
            ];

            if ($data['slug'] === '') {
                $data['slug'] = Html::slugify($data['name']);
            }

            if ($data['name'] === '' || $data['slug'] === '') {
                $this->flash('error', 'A category needs both a name and a URL slug.');
            } else {
                $editId = (int) ($_POST['id'] ?? 0);
                if ($editId > 0) {
                    $this->app->categories->update($editId, $data);
                    $this->flash('success', 'Category updated.');
                } else {
                    $this->app->categories->create($data);
                    $this->flash('success', 'Category created.');
                }
            }

            Router::redirect('admin/categories');
        }

        $this->render('categories', 'Categories', [
            'categories' => $this->app->categories->allWithCounts(),
            'editing'    => $action === 'edit' ? $this->app->categories->find($id) : null,
        ]);
    }

    /* ============================================================== 媒體 */

    private function media(string $action): void
    {
        $this->guard('manage_media');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $stored = isset($_FILES['image'])
                ? $this->app->media->store($_FILES['image'], (string) ($_POST['folder'] ?? 'museums'))
                : null;

            if ($stored === null) {
                $this->flash('error', 'The file could not be uploaded. Only JPG, PNG, GIF, WEBP and SVG are allowed.');
            } else {
                $this->flash('success', 'Uploaded to ' . $stored);
            }

            Router::redirect('admin/media');
        }

        $this->render('media', 'Media library', ['images' => $this->app->media->images()]);
    }

    /* ============================================================== 外掛 */

    private function plugins(string $action): void
    {
        $this->guard('manage_plugins');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $slug   = (string) ($_POST['slug'] ?? '');
            $active = ($_POST['state'] ?? '') === 'activate';

            if ($this->app->plugins->find($slug) !== null) {
                $this->app->plugins->setActive($slug, $active);
                $this->flash('success', 'Plugin ' . ($active ? 'activated' : 'deactivated') . '.');
            }

            Router::redirect('admin/plugins');
        }

        $this->render('plugins', 'Plugins', ['plugins' => $this->app->plugins->all()]);
    }

    /* ============================================================ 使用者 */

    private function users(string $action, int $id): void
    {
        $this->guard('manage_users');

        if ($action === 'delete') {
            $this->verifyCsrf();

            if ($id === (int) $this->app->auth->user()['id']) {
                $this->flash('error', 'You cannot delete the account you are signed in with.');
            } else {
                $this->app->users->delete($id);
                $this->flash('success', 'User deleted.');
            }

            Router::redirect('admin/users');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $editId = (int) ($_POST['id'] ?? 0);
            $data   = [
                'username'     => trim((string) ($_POST['username'] ?? '')),
                'display_name' => trim((string) ($_POST['display_name'] ?? '')),
                'email'        => trim((string) ($_POST['email'] ?? '')),
                'role'         => ($_POST['role'] ?? 'editor') === 'admin' ? 'admin' : 'editor',
                'password'     => (string) ($_POST['password'] ?? ''),
            ];

            if ($editId > 0) {
                $this->app->users->update($editId, $data);
                $this->flash('success', 'User updated.');
            } elseif ($data['username'] === '' || $data['password'] === '') {
                $this->flash('error', 'A new user needs a username and a password.');
            } elseif ($this->app->users->findByUsername($data['username']) !== null) {
                $this->flash('error', 'That username is already taken.');
            } else {
                $this->app->users->create($data);
                $this->flash('success', 'User created.');
            }

            Router::redirect('admin/users');
        }

        $this->render('users', 'Users', [
            'users'   => $this->app->users->all(),
            'editing' => $action === 'edit' ? $this->app->users->find($id) : null,
        ]);
    }

    /* ============================================================ 安全性 */

    private function security(string $action): void
    {
        $this->guard('view_security');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $this->app->loginAttempts->clear();
            $this->flash('success', 'Login log cleared.');
            Router::redirect('admin/security');
        }

        $this->render('security', 'Security', [
            'attempts'    => $this->app->loginAttempts->recent(60),
            'maxAttempts' => (int) $this->app->setting('security_max_attempts', '5'),
            'lockout'     => (int) $this->app->setting('security_lockout_min', '15'),
        ]);
    }

    /* ================================================================ 設定 */

    private function settings(): void
    {
        $this->guard('manage_settings');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            // 只接受白名單內的設定鍵
            $keys = [
                'site_title', 'site_tagline', 'site_description', 'target_audience', 'copyright_owner',
                'social_twitter', 'social_facebook', 'social_instagram',
                'contact_form_action', 'contact_email', 'contact_intro_text',
                'contact_success_text', 'contact_error_text',
                'login_background', 'home_cover_image', 'home_gallery',
                'security_max_attempts', 'security_lockout_min', 'posts_per_page',
            ];

            $values = [];
            foreach ($keys as $key) {
                if (array_key_exists($key, $_POST)) {
                    $values[$key] = trim((string) $_POST[$key]);
                }
            }

            $this->app->settings->setMany($values);
            $this->flash('success', 'Settings saved.');
            Router::redirect('admin/settings');
        }

        $this->render('settings', 'Settings', [
            'values' => $this->app->settings->all(),
            'images' => $this->app->media->images(),
        ]);
    }

    /* ================================================================ 工具 */

    /** 沒有權限就導回儀表板 */
    private function guard(string $capability): void
    {
        if (!$this->app->auth->can($capability)) {
            $this->flash('error', 'Your role does not have access to that section.');
            Router::redirect('admin');
        }
    }

    private function verifyCsrf(): void
    {
        if (!Csrf::verify($_POST['_token'] ?? ($_GET['_token'] ?? null))) {
            http_response_code(400);
            echo 'Invalid or expired security token. Please go back and try again.';
            exit;
        }
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    /** 產生不重複的文章 slug */
    private function uniquePostSlug(string $slug): string
    {
        $slug = $slug !== '' ? $slug : 'draft';
        $candidate = $slug;
        $counter   = 2;

        while ($this->app->posts->slugExists($candidate)) {
            $candidate = $slug . '-' . $counter++;
        }

        return $candidate;
    }

    private function notFound(): void
    {
        http_response_code(404);
        $this->render('not-found', 'Not found', []);
    }

    /** 渲染後台頁面（套用後台版面） */
    private function render(string $view, string $title, array $data): void
    {
        $app    = $this->app;
        $flash  = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        extract($data, EXTR_SKIP);

        ob_start();
        require $this->viewDir . '/' . $view . '.php';
        $content = (string) ob_get_clean();

        require $this->viewDir . '/layout.php';
    }

    /** 渲染不套版面的頁面（登入頁） */
    private function renderRaw(string $view, array $data): void
    {
        $app = $this->app;
        extract($data, EXTR_SKIP);

        require $this->viewDir . '/' . $view . '.php';
    }
}
