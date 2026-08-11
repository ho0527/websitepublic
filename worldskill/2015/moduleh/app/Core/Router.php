<?php
namespace App\Core;

/**
 * 極簡路由器：把「路由字串」對應到「控制器類別 + 方法」。
 */
class Router
{
    /** @var array<string, array{0:string,1:string}> 路由表 */
    private array $routes = [];

    /** @var Request */
    private Request $request;

    /** @var View */
    private View $view;

    public function __construct(Request $request, View $view)
    {
        $this->request = $request;
        $this->view    = $view;
    }

    /**
     * 註冊路由
     *
     * @param string $route      路由字串，例如 booking/individual
     * @param string $controller 控制器完整類別名稱
     * @param string $action     方法名稱
     */
    public function add(string $route, string $controller, string $action): self
    {
        $this->routes[trim($route, '/')] = [$controller, $action];

        return $this;
    }

    /**
     * 依目前請求分派到對應控制器，回傳輸出內容
     */
    public function dispatch(): string
    {
        $route = $this->request->route();

        if (!isset($this->routes[$route])) {
            http_response_code(404);
            return $this->view->render('error', [
                'pageTitle' => 'Page not found',
                'title'     => 'Page not found',
                'message'   => 'The requested page "' . $route . '" does not exist.',
            ]);
        }

        [$controllerClass, $action] = $this->routes[$route];

        /** @var Controller $controller */
        $controller = new $controllerClass($this->request, $this->view);

        return (string) $controller->{$action}();
    }
}
