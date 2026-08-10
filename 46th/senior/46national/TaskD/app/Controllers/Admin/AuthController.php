<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Train;
use App\Models\TrainType;

/**
 * 後台登入與功能選單。
 */
final class AuthController extends Controller
{
    /**
     * 登入表單。
     */
    public function showLoginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('admin');
        }

        $this->render('front/login', [
            'title'  => '登入後台',
            'errors' => Session::pullFlash('errors', []),
        ]);
    }

    /**
     * 驗證帳號密碼。
     */
    public function login(): void
    {
        $username = $this->request->input('username', '') ?? '';
        $password = $this->request->input('password', '') ?? '';

        if ($username === '' || $password === '') {
            $this->redirectWithErrors('login', ['請輸入帳號與密碼']);
        }

        if (!Auth::attempt($username, $password)) {
            // 不分別提示「帳號不存在」或「密碼錯誤」，避免洩漏帳號是否存在
            $this->redirectWithErrors('login', ['帳號或密碼錯誤，請重新輸入']);
        }

        $this->redirect('admin');
    }

    /**
     * 登出。
     */
    public function logout(): void
    {
        Auth::logout();
        Session::flash('notice', '已登出後台');

        $this->redirect('login');
    }

    /**
     * 後台首頁：各管理功能的快速連結與概況。
     */
    public function dashboard(): void
    {
        $this->requireAdmin();

        $now = new \DateTimeImmutable();

        $this->renderAdmin('admin/dashboard', [
            'title'            => '後台管理',
            'trainTypeCount'   => TrainType::query()->count(),
            'trainCount'       => Train::active()->count(),
            'activeBookings'   => Booking::active()->count(),
            'upcomingBookings' => Booking::active()
                ->where('depart_at', '>', $now->format('Y-m-d H:i:s'))
                ->count(),
        ]);
    }
}
