<?php 

namespace WordAdmin\Login;

require_once $_project_dir.'/loader.php';



function logIn(){
    global $_username;
    global $_userpassword;

    $username = $_POST['username']; 
    $password = $_POST['password'];
    
    if($username === $_username && password_verify ($password , $_userpassword )){
        $_SESSION['login'] = true; 
    } else {
        
        $_SESSION['login'] = false; 
    }
    
    
    $template = array(
        'was_loggedin' => $_SESSION['login'],
        'step' => 'logging', 
    );
    
    if (!$_SESSION['login']) {
        \WordAdmin\Loader\loadTemplate('login', $template);
    }
}

session_start();



$mode = filter_var($_GET['modo'], FILTER_SANITIZE_STRING);

if (!in_array($mode, array('login', 'logout'))) {
    $mode = "login";
}


if($mode == "login"){
    if( isset($_POST['logmein'])){
        logIn();
    }

    // if logged and enter here, redirect to root
    if ($_SESSION['login']) {
        if(isset($_SESSION['login'])) {
          header('LOCATION:/'); 
          die();
        }
    }
    
    $template = array(
        'was_loggedin' => false,
        'step'         => 'login', 
    );
    
    
    \WordAdmin\Loader\loadTemplate('login', $template);
}


if ($mode == "logout") {
    // Remember to Always session_start()

    // This also happens automatically when the browser is closed
    session_destroy();
    header('LOCATION:/'); 
    die();
}
