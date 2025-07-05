<?php
// router.php - PHP built-in web server router file for Yii1

// Получаем запрошенный URI
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Если запрошенный URI указывает на существующий файл или директорию,
// то PHP-сервер должен его отдать напрямую (например, CSS, JS, изображения).
if ($uri !== '/' && file_exists($_SERVER['DOCUMENT_ROOT'] . $uri)) {
    return false; // Позволяет встроенному серверу обработать запрос как статический файл
}

// Если это не существующий файл, то перенаправляем запрос на index.php
// (аналогично RewriteRule . index.php в .htaccess)
require_once $_SERVER['DOCUMENT_ROOT'] . '/index.php';
