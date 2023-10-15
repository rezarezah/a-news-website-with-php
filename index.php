<?php

session_start();

use Auth\Auth;

define('BASE_PATH', __DIR__);
define('CURRENT_DOMAIN', currentDomain() . '/project');
define('DISPLAY_ERROR', true);
define('DB_HOST', 'localhost');
define('DB_NAME', 'project');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', '*****************');
define('MAIL_PASSWORD', '*****************');
define('SMTP_AUTH', true);
define('MAIL_PORT', 587);
define('SENDER_MAIL', '*******************');
define('SENDER_NAME', 'PHP COURSE');

require_once 'database/DataBase.php';
require_once 'database/CreateDB.php';
require_once 'activities/Admin/Admin.php';
require_once 'activities/Admin/Category.php';
require_once 'activities/Admin/post.php';
require_once 'activities/Admin/banner.php';
require_once 'activities/Admin/user.php';
require_once 'activities/Admin/comment.php';
require_once 'activities/Admin/menu.php';
require_once 'activities/Admin/setting.php';
require_once 'activities/Auth/Auth.php';
require_once 'activities/admin/dashboard.php';
require_once 'activities/app/home.php';

// $db = new database\Database();
// $db = new database\CreateDB();
// $db->run();

spl_autoload_register(function ($className) {
    $path = BASE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
    include $path . $className . '.php';
});
function jalaliDate($date)
{
    return \Parsidev\Jalali\jDate::forge($date)->format('datetime');
}

function uri($reservedUrl, $class, $method, $requestMethod = 'GET')
{

    //current url array
    $currentUrl = explode('?', currentUrl())[0];
    $currentUrl = str_replace(CURRENT_DOMAIN, '', $currentUrl);
    $currentUrl = trim($currentUrl, '/');
    $currentUrlArray = explode('/', $currentUrl);
    $currentUrlArray = array_filter($currentUrlArray);

    //reserved Url array
    $reservedUrl = trim($reservedUrl, '/');
    $reservedUrlArray = explode('/', $reservedUrl);
    $reservedUrlArray = array_filter($reservedUrlArray);


    if (sizeof($currentUrlArray) != sizeof($reservedUrlArray) || methodField() != $requestMethod) {
        return false;
    }

    $parameters = [];
    for ($key = 0; $key < sizeof($currentUrlArray); $key++) {
        if ($reservedUrlArray[$key][0] == "{" && $reservedUrlArray[$key][strlen($reservedUrlArray[$key]) - 1] == "}") {
            array_push($parameters, $currentUrlArray[$key]);
        } elseif ($currentUrlArray[$key] !== $reservedUrlArray[$key]) {
            return false;
        }
    }

    if (methodField() == 'POST') {
        $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        $parameters = array_merge([$request], $parameters);
    }

    $object = new $class;
    call_user_func_array(array($object, $method), $parameters);
    exit();
}

function protocol()
{
    return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
}
function currentDomain()
{
    return protocol() . $_SERVER['HTTP_HOST'];
}

function assets($src)
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $src = $domain . '/' . trim($src, '/');
    return $src;
}
function url($url)
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $url = $domain . '/' . trim($url, '/');
    return $url;
}
function currentUrl()
{
    return currentDomain() . $_SERVER['REQUEST_URI'];
}
function methodField()
{
    return $_SERVER['REQUEST_METHOD'];
}
function displayError()
{
    if (DISPLAY_ERROR) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }
}
displayError(DISPLAY_ERROR);

global $flashMessage;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
function flash($name, $value = null)
{
    if ($value === null) {
        global $flashMessage;
        $message = isset($flashMessage[$name]) ? $flashMessage[$name] : '';
        return $message;
    } else {
        $_SESSION['flash_message'][$name] = $value;
    }
}
// dashboard reserved urls
uri('admin', 'Admin\dashboard', 'index');

// Categories reserved usrls
uri('admin/category', 'Admin\category', 'index');
uri('admin/category/create', 'Admin\category', 'create');
uri('admin/category/store', 'Admin\category', 'store', 'POST');
uri('admin/category/edit/{id}', 'Admin\category', 'edit');
uri('admin/category/update/{id}', 'Admin\category', 'update', 'POST');
uri('admin/category/delete/{id}', 'Admin\category', 'delete');

// posts reserved usrls

uri('admin/post', 'Admin\post', 'index');
uri('admin/post/create', 'Admin\post', 'create');
uri('admin/post/store', 'Admin\post', 'store', 'POST');
uri('admin/post/edit/{id}', 'Admin\post', 'edit');
uri('admin/post/update/{id}', 'Admin\post', 'update', 'POST');
uri('admin/post/delete/{id}', 'Admin\post', 'delete');
uri('admin/post/selected/{id}', 'Admin\post', 'selected');
uri('admin/post/breaking-news/{id}', 'Admin\post', 'breakingNews');

// banners reserved usrls

uri('admin/banner', 'Admin\banner', 'index');
uri('admin/banner/create', 'Admin\banner', 'create');
uri('admin/banner/store', 'Admin\banner', 'store', 'POST');
uri('admin/banner/edit/{id}', 'Admin\banner', 'edit');
uri('admin/banner/update/{id}', 'Admin\banner', 'update', 'POST');
uri('admin/banner/delete/{id}', 'Admin\banner', 'delete');


// users reserved usrls

uri('admin/user', 'Admin\user', 'index');
uri('admin/user/edit/{id}', 'Admin\user', 'edit');
uri('admin/user/update/{id}', 'Admin\user', 'update', 'POST');
uri('admin/user/edit/{id}', 'Admin\user', 'edit');
uri('admin/user/delete/{id}', 'Admin\user', 'delete');
uri('admin/user/permission/{id}', 'Admin\user', 'permission');


// comments reserved usrls

uri('admin/comment', 'Admin\comment', 'index');
uri('admin/comment/show/{id}', 'Admin\comment', 'show');
uri('admin/comment/delete/{id}', 'Admin\comment', 'delete');
uri('admin/comment/change-status/{id}', 'Admin\comment', 'changeStatus');

// menues reserved urls

uri('admin/menu', 'Admin\menu', 'index');
uri('admin/menu/create', 'Admin\menu', 'create');
uri('admin/menu/store', 'Admin\menu', 'store', 'POST');
uri('admin/menu/edit/{id}', 'Admin\menu', 'edit');
uri('admin/menu/update/{id}', 'Admin\menu', 'update', 'POST');
uri('admin/menu/delete/{id}', 'Admin\menu', 'delete');

// menues reserved urls

uri('admin/setting', 'Admin\setting', 'index');
uri('admin/setting/edit/{id}', 'Admin\setting', 'edit');
uri('admin/setting/update/', 'Admin\setting', 'update', 'POST');

// Auth reserved urls

uri('register', 'Auth\Auth', 'register');
uri('register/store', 'Auth\Auth', 'registerStore', 'POST');
uri('activation/{verify_token}', 'Auth\Auth', 'activation');
uri('login', 'Auth\Auth', 'login');
uri('check-login', 'Auth\Auth', 'checkLogin', 'POST');
uri('logout', 'Auth\Auth', 'logout');
uri('forgot', 'Auth\Auth', 'forgot');
uri('forgot/request', 'Auth\Auth', 'forgotRequest', 'POST');
uri('reset-password-form/{forgot_token}', 'Auth\Auth', 'resetPasswordView');
uri('reset-password/{forgot_token}', 'Auth\Auth', 'resetPassword', 'POST');

// Auth reserved urls

uri('/', 'App\Home', 'index');
uri('/home', 'App\Home', 'index');
uri('/show-post/{id}', 'App\Home', 'show');
uri('/show-category/{id}', 'App\Home', 'category');
uri('/comment-store/{post-id}', 'App\Home', 'commentStore', 'POST');


echo '404 - page not found';
