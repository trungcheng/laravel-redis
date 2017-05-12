<?php

namespace App\Http\Middleware;

use Auth;
use Menu;
use Closure;
use Illuminate\Support\Facades\Lang;

class LeftSidebarMiddleware {

    public $articles_unapprove;

    public function __construct() {
        $this->articles_unapprove = 20;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $index_1st = $this->articles_unapprove;
        if (Auth::check()) {

            Menu::make('left_navbar', function ($menu) use ($index_1st) {
                $menu->style('navigation');


                $menu->add([
                    'title' => trans('menu.main_navi'),
                    'header' => true
                ]);
                $menu->add([
                    'url' => '/home',
                    'title' => 'Trang Chủ',
                    'icon' => 'fa fa-bank'
                ]);

                $menu->add([
                    'url' => 'media/product',
                    'title' => 'Quản Lý Sản Phẩm',
                    'icon' => 'fa fa-database'
                ]);

                $menu->add([
                    'url' => '/article',
                    'title' => 'Quản Lý Bài Viết',
                    'icon' => 'fa fa-database'
                ]);

                $menu->add([
                    'url' => '/member/article',
                    'title' => 'Quản Lý Bài Viết Thành Viên',
                    'icon' => 'fa fa-database'
                ]);

                $menu->add([
                    'url' => '/category',
                    'title' => 'Quản Lý chuyên mục',
                    'icon' => 'fa fa-feed'
                ]);

                $menu->add([
                    'url' => '/events',
                    'title' => 'Quản Lý Sự Kiện',
                    'icon' => 'fa fa-database'
                ]);
                $menu->add([
                    'url' => '/notify',
                    'title' => 'Thông báo',
                    'icon' => 'fa fa-bell'
                ]);
                $menu->add([
                    'url' => '/class-register',
                    'title' => 'Danh sách đăng kí học',
                    'icon' => 'fa fa-database'
                ]);
                if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') :
                    $menu->add([
                        'url' => '/collection',
                        'title' => 'Bộ Sưu Tập',
                        'icon' => 'fa fa-bookmark'
                    ]);
                    $menu->add([
                        'url' => '/config/create',
                        'title' => 'Quản Lý Cấu Hình',
                        'icon' => 'fa fa-cog'
                    ]);
                ENDIF;
                if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') :
                    $menu->add([
                        'url' => '/built-top',
                        'title' => 'Builtop',
                        'icon' => 'fa fa-cubes'
                    ]);
                ENDIF;
                if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') :
                    $menu->add([
                        'url' => '/questions',
                        'title' => 'Danh Sách Bình Luận',
                        'icon' => 'fa fa-comments'
                    ]);
                ENDIF;
                $menu->add([
                    'url' => '/thanh-vien',
                    'title' => 'Thành Viên',
                    'icon' => 'fa fa-users'
                ]);
                $menu->add([
                    'url' => '/filemanager/index.html',
                    'title' => 'Quản Lý album',
                    'icon' => 'fa fa-medium'
                ]);
                $menu->add([
                    'url' => '/quan-tri-vien',
                    'title' => 'Quản Trị Viên',
                    'icon' => 'fa fa-mortar-board'
                ]);

                $menu->add([
                    'url' => '/profile',
                    'title' => 'Trang Cá Nhân',
                    'icon' => 'fa fa-user-plus'
                ]);
            });
        }

        return $next($request);
    }

}
