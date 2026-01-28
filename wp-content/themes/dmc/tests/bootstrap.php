<?php
/**
 * Bootstrap для unit-тестов темы без полноценного WordPress.
 * Здесь определяем минимальные заглушки WP-функций, которые используются при подключении файлов темы.
 */

// Базовые заглушки WP-хуков
if (!function_exists('add_action')) {
    function add_action(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void {
        // Заглушка: в unit-тестах хуки не выполняем
    }
}

if (!function_exists('add_filter')) {
    function add_filter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void {
        // Заглушка
    }
}

if (!function_exists('remove_action')) {
    function remove_action(string $hook, callable $callback, int $priority = 10): void {
        // Заглушка
    }
}

if (!function_exists('__return_true')) {
    function __return_true(): bool {
        return true;
    }
}

// REST заглушки
if (!function_exists('register_rest_route')) {
    function register_rest_route(string $namespace, string $route, array $args = array()): void {
        // Заглушка
    }
}

// Пути темы
if (!function_exists('get_template_directory')) {
    function get_template_directory(): string {
        return dirname(__DIR__);
    }
}

if (!function_exists('get_bloginfo')) {
    function get_bloginfo(string $show): string {
        // Достаточно для get_insurer_logo()
        if ($show === 'template_url') {
            return 'http://example.test/wp-content/themes/dmc';
        }
        return '';
    }
}

// Простейшие заглушки классов/структур WP, чтобы include не падал
if (!class_exists('WP_Error')) {
    class WP_Error {
        public function __construct(
            public string $code = '',
            public string $message = '',
            public array $data = array()
        ) {}
    }
}

if (!class_exists('WP_REST_Response')) {
    class WP_REST_Response {
        public function __construct(
            public mixed $data = null,
            public int $status = 200
        ) {}
    }
}

