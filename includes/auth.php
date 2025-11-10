<?php
// includes/auth.php
session_start();

function require_login(){
    if (empty($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

// CSRF token helpers
function csrf_token() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}
function csrf_check($token) {
    return hash_equals($_SESSION['_csrf_token'] ?? '', $token ?? '');
}

function e($val){
    return htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
