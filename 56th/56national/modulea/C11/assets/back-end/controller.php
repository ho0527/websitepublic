<?php

include_once "testHelper.php";

class Controller
{
    static public function get(): void
    {
        $user = getUser();

        self::response([
            "email" => $user["email"],
            "password" => $user["password"],
            "message" => "success get",
        ]);
    }

    static public function post(): void
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        if(
            !isset($_POST["email"]) ||
            !isset($_POST["password"]) ||
            !checkUser($_POST["email"], $_POST["password"])
        ) {
            self::response([
                "message" => "missing or invalid login value",
            ], 400);
            return;
        }

        $user = getUser();

        self::response([
            "user_id" => $user["id"],
            "message" => "success post",
        ]);
    }

    static public function put(): void
    {
        $user = getUser();
        if(!isset($_GET["user_id"]) || +$_GET["user_id"] !== $user["id"]) {
            self::response([
                "message" => "missing or invalid user id",
            ], 400);
            return;
        }

        self::response([
            "token" => $user["token"],
            "message" => "success put",
        ]);
    }

    static public function delete(): void
    {
        $token = getBearerToken();

        if(!checkToken($token)) {
            self::response([
                "message" => "missing or invalid token",
            ], 401);
            return;
        }

        self::response([
            "message" => "success delete",
        ]);
    }

    static public function notFound(): void
    {
        self::response([
            "message" => "Not Found",
        ], 404);
    }

    static private function response(array $json, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode($json);
    }
}