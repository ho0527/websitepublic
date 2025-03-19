<?php

function getUser(): array
{
    return [
        "id" => 128,
        "email" => "user@skill.com",
        "password" => "PassworD!1",
        "token" => "==test_token==",
    ];
}

function checkUser($email, $password): bool
{
    $user = getUser();

    return (
        $email === $user["email"] &&
        $password === $user["password"]
    );
}

function checkToken($token): bool
{
    $user = getUser();

    return $token === $user["token"];
}

function getAuthorizationHeader(): ?string
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function getBearerToken(): ?string
{
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}