<?php

declare(strict_types=1);

namespace App\Core;

/**
 * 路由器。
 *
 * 以「/train-info/{code}」這種樣式註冊路由，讓對外網址維持乾淨、
 * 符合搜尋引擎最佳化（SEO）的形式，而不是帶一長串查詢字串。
 */
final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: array{0: string, 1: string}}> */
    private array $routes = [];

    /**
     * 註冊 GET 路由。
     *
     * @param array{0: class-string, 1: string} $handler [控制器類別, 方法名稱]
     */
    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /**
     * 註冊 POST 路由。
     *
     * @param array{0: class-string, 1: string} $handler
     */
    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    private function add(string $method, string $pattern, array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => '/' . trim($pattern, '/'),
            'handler' => $handler,
        ];
    }

    /**
     * 比對請求並執行對應的控制器方法。
     */
    public function dispatch(Request $request): void
    {
        $path = $request->path();

        foreach ($this->routes as $route) {
            $parameters = $this->match($route['pattern'], $path);

            if ($parameters === null) {
                continue;
            }

            if ($route['method'] !== $request->method()) {
                continue;
            }

            [$controllerClass, $action] = $route['handler'];
            $controller                 = new $controllerClass($request);
            $controller->{$action}(...array_values($parameters));

            return;
        }

        $this->renderNotFound($request);
    }

    /**
     * 比對單一路由樣式，成功時回傳擷取到的參數。
     *
     * @return array<string, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#u';

        if (preg_match($regex, $path, $matches) !== 1) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    /**
     * 找不到路由時顯示 404 頁面。
     */
    private function renderNotFound(Request $request): void
    {
        http_response_code(404);

        $view = new View($request);
        // 404 頁同樣要套用共用版型，因此補上版型需要的共用資料
        $view->share([
            'currentPath'     => $request->path(),
            'isAdminSignedIn' => Auth::check(),
        ]);

        $view->render('errors/not-found', ['title' => '找不到頁面']);
    }
}
