<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
function csrf_token() { if (empty($_SESSION["csrf_token"])) $_SESSION["csrf_token"] = bin2hex(random_bytes(32)); return $_SESSION["csrf_token"]; }
function check_csrf() { if (empty($_SESSION["csrf_token"]) || !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"] ?? "")) { http_response_code(403); exit("Invalid form token. Go back and try again."); } }
function require_login() { if (empty($_SESSION["user_id"])) { header("Location: login.php"); exit; } }
function require_role($role) { require_login(); if (($_SESSION["role"] ?? "") !== $role) { http_response_code(403); exit("You do not have permission to view this page."); } }
?>