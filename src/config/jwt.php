<?php
return [

    //加密密钥
    'jwt_secret' => MD5('JWT-TEST-AUTHER-SECRET'),

    //加密的有效期
    'jwt_ttl' => 180,

    //过期多久以内允许刷新
    'jwt_allow_ttl' => 360,

    //密文传输方式 header or body
    'jwt_request_type' => 'header',

    //密文传输的字段名称
    'jwt_request_field' => 'Authorization',

    //加密方式 'HS256','HS384','HS512','RS256','RS384','RS512'
    'jwt_alg' => 'HS512'

];