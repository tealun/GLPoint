<?php
return [
    'apps' => [
        'addon.ueditor' => [
            'type'                 => 'session',
            'session_key'          => 'login',
            'model'                => 'Admin',
            'allow_login_model' => [
                'Admin' => ['withJoin' => ['AdminGroup','Department']],
                'User' => ['withJoin' => ['UserGroup']] //如果以后希望前台也可以使用就打开它
            ],
            'response_mode'        => 'json',
            'allow_from_all'       => false,
        ],
    ],
];