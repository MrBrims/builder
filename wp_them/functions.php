<?php

// Переменные

$dir = __DIR__ . '/';

define('PATH', $dir);
define('URI', get_template_directory_uri());
define('HOMEPAGE', get_option('page_on_front'));

/** Основные настройки */
require_once PATH . 'assets/General.php';


// Работа формы
require_once PATH . 'assets/Helpers.php';
require_once PATH . 'assets/Ajax.php';
