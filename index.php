<?php

require_once 'loader.php';


$request = $_SERVER['REQUEST_URI'];
$current_folder = __DIR__;
$current_folder = str_replace(dirname(__DIR__),"",$current_folder) . "/";

$uri = $request;



if ($uri === '/' or $uri === $current_folder ) {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/index.php';
} 
elseif ($uri === '/added' || preg_match('/^\/added\/*/i', $uri) )  {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/index.php';
} 
elseif ($uri === '/login' ) {
    $_GET['modo'] = "login";
    require __DIR__.'/user-session.php';
} 
elseif ($uri === '/logout' ) {
    $_GET['modo'] = "logout";
    require __DIR__.'/user-session.php';
} 
elseif ($uri === '/admin' ) {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/admin.php';
} 
elseif ($uri === '/admin/liststudy' ) {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/liststudy.php';
} 
elseif ($uri === '/review' || preg_match('/^\/review\/*/i', $uri) )  {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/review.php';
} 
elseif (preg_match('/^\/admin\/\?view=\d{1,6}$/i', $uri)) {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/admin.php';
} 
elseif (preg_match('/^\/admin\/\?log=\d{1,}/i', $uri)) {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/admin.php';
} 
elseif (preg_match('/^\/admin\/\?ankied=.+?$/i', $uri)) {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/admin.php';
} 
elseif (preg_match('/^\/admin\/\?softdeleted=.+?$/i', $uri)) {
    \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/admin.php';
} 
elseif (preg_match('/^\/api\//i', $uri)) {
    // \WordAdmin\Loader\redirectToLogin();
    require __DIR__.'/pages/api.php';
} /*else if (preg_match('/^\/join(\/(\d+))?$/, $uri, vars)) {
    // Sign up page, with an optional referral code in $vars[2]
} else if (preg_match('/^\/admin\//i', $uri)) {
    // Hands off to a secret admin router
} */
else {
    die("ooops");
}


