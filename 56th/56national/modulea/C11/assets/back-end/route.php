<?php

$url = filter_var("/" . rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
$method = $_SERVER["REQUEST_METHOD"];

$routes = [
    [
        "method" => "GET",
        "url" => "/api/get",
        "action" => [Controller::class, "get"],
    ],
    [
        "method" => "POST",
        "url" => "/api/post",
        "action" => [Controller::class, "post"],
    ],
    [
        "method" => "PUT",
        "url" => "/api/put",
        "action" => [Controller::class, "put"],
    ],
    [
        "method" => "DELETE",
        "url" => "/api/delete",
        "action" => [Controller::class, "delete"],
    ],
];

foreach($routes as $route) {
    if($route["method"] === $method && $route["url"] === $url) {
        $route["action"]();

        die;
    }
}

Controller::notFound();