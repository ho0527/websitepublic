<?php

declare(strict_types=1);

namespace App\Core;

/**
 * 控制器基底類別，集中處理樣板渲染、轉址與 JSON 回應。
 */
abstract class Controller
{
    protected Request $request;

    protected View $view;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view    = new View($request);

        $this->view->share([
            'currentPath'   => $request->path(),
            'isAdminSignedIn' => Auth::check(),
        ]);
    }

    /**
     * 渲染前台頁面。
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = []): void
    {
        $this->view->render($template, $data);
    }

    /**
     * 渲染後台頁面（使用後台版型）。
     *
     * @param array<string, mixed> $data
     */
    protected function renderAdmin(string $template, array $data = []): void
    {
        $this->view->render($template, $data, 'layouts/admin');
    }

    /**
     * 轉址到網站內的路徑。
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . $this->request->basePath() . '/' . ltrim($path, '/'));

        exit;
    }

    /**
     * 帶著錯誤訊息與原輸入值退回指定頁面。
     *
     * @param array<int, string>   $errors
     * @param array<string, mixed> $oldInput
     */
    protected function redirectWithErrors(string $path, array $errors, array $oldInput = []): void
    {
        Session::flash('errors', $errors);
        Session::flash('old', $oldInput);

        $this->redirect($path);
    }

    /**
     * 回應 JSON。
     *
     * @param array<string, mixed> $payload
     */
    protected function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    /**
     * 要求必須為已登入的管理員，否則導回登入頁並顯示錯誤訊息。
     */
    protected function requireAdmin(): void
    {
        if (Auth::check()) {
            return;
        }

        Session::flash('errors', ['請先以管理員帳號登入，才能使用後台功能']);
        $this->redirect('login');
    }
}
