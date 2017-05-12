<?php

return [
    'lang_support' => [
        'vi' => 'Tiếng Việt',
        'en' => 'English',
        'la' => 'Laos'
    ],
    'user_type' => [
        'type_1' => 'Admin',
        'type_2' => 'Editor',
        'type_3' => 'Normal',
        'type_4' => 'User_Vip',
        'type_5' => 'User_Normal',
        'type_6' => 'User_Api',
        'type_7' => 'ViewRegister'
    ],
    'type_article' => [
        'recipe',
        'review',
        'blog'
    ],
    'user_type_check' => [
        'Admin',
        'Editor',
        'Normal',
        'Guest',
    ],
    'type_category' => [
        'restaurant' => ['Nhà Hàng', 'review'],//nhà hàng
        'food_1' => ['Ăn Gì', 'review'],//món ăn
        'food_2' => ['Món Ăn', 'recipe'],//món ăn
        'cuisine' => ['Ẩm Thực', 'recipe'], //ẩm thực
        'food_type' => ["Loại Món", 'recipe'], //loại món
        'helper' => ['Cách Chế Biến', 'recipe'],//hướng dẫn
        'season' => ['Mùa và Dịp Lễ', 'recipe'], //mùa
        'blog_general' => ['Chuyên Mục Blog', 'blog'], //blog chung
        'tin_tuc' => ['Tin Tức', 'tin_tuc'],
    ],
    'rule_route' => [
        'review' => 'detail-review',
        'recipe' => 'detail-recipe',
        'blog' => 'detail-blog',
        'tin_tuc' => 'detail-tin-tuc'
    ],
    'rule_route_detail' => [
        'Review' => 'detail-review',
        'Recipe' => 'detail-recipe',
        'Blogs' => 'detail-blog'
    ],


];