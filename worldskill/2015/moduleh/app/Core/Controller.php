<?php
namespace App\Core;

/**
 * 控制器基底類別，提供樣板算繪與轉址等共用行為。
 */
abstract class Controller
{
    /** @var Request 目前請求 */
    protected Request $request;

    /** @var View 樣板引擎 */
    protected View $view;

    public function __construct(Request $request, View $view)
    {
        $this->request = $request;
        $this->view    = $view;
    }

    /**
     * 算繪整頁（含版面）並輸出
     */
    protected function render(string $template, array $data = []): string
    {
        return $this->view->render($template, $data);
    }

    /**
     * 轉址到指定路由並結束程式
     */
    protected function redirect(string $route): void
    {
        header('Location: ' . Url::to($route));
        exit;
    }

    /**
     * 轉址到指定絕對網址並結束程式
     */
    protected function redirectTo(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
