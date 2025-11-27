<?php
/**
 * Диагностика проблемы с пустыми медиафайлами в админ-панели WordPress
 * 
 * Использование:
 * http://your-site.com/wp-content/themes/dmc/debug_media.php
 * 
 * ВАЖНО: Удалите этот файл после диагностики!
 */

// Подключаем WordPress
require_once('../../../wp-load.php');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Диагностика медиафайлов</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>🔍 Диагностика медиафайлов WordPress</h1>

    <div class="section">
        <h2>1. Настройки загрузки файлов</h2>
        <?php
        $upload_dir = wp_upload_dir();
        echo '<p><strong>Путь к загрузкам:</strong> <code>' . esc_html($upload_dir['basedir']) . '</code></p>';
        echo '<p><strong>URL загрузок:</strong> <code>' . esc_html($upload_dir['baseurl']) . '</code></p>';
        echo '<p><strong>Существует ли директория:</strong> ' . (is_dir($upload_dir['basedir']) ? '<span class="success">✅ Да</span>' : '<span class="error">❌ Нет</span>') . '</p>';
        echo '<p><strong>Доступна для записи:</strong> ' . (is_writable($upload_dir['basedir']) ? '<span class="success">✅ Да</span>' : '<span class="error">❌ Нет</span>') . '</p>';
        ?>
    </div>

    <div class="section">
        <h2>2. Проверка файлов в директории uploads</h2>
        <?php
        $upload_dir = wp_upload_dir();
        $files = [];
        if (is_dir($upload_dir['basedir'])) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($upload_dir['basedir'], RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            $count = 0;
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = [
                        'path' => $file->getPathname(),
                        'size' => $file->getSize(),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime())
                    ];
                    $count++;
                    if ($count >= 20) break; // Ограничиваем вывод
                }
            }
        }
        
        if (empty($files)) {
            echo '<p class="warning">⚠️ Файлы не найдены в директории uploads</p>';
        } else {
            echo '<p class="success">✅ Найдено файлов: ' . count($files) . ' (показано первые 20)</p>';
            echo '<table>';
            echo '<tr><th>Путь</th><th>Размер</th><th>Изменен</th></tr>';
            foreach ($files as $file) {
                echo '<tr>';
                echo '<td>' . esc_html(str_replace($upload_dir['basedir'], '', $file['path'])) . '</td>';
                echo '<td>' . size_format($file['size']) . '</td>';
                echo '<td>' . $file['modified'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Проверка записей в базе данных</h2>
        <?php
        global $wpdb;
        
        // Подсчитываем все вложения
        $total_attachments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment'");
        echo '<p><strong>Всего вложений в БД:</strong> ' . $total_attachments . '</p>';
        
        // Подсчитываем по типам
        $by_mime = $wpdb->get_results("
            SELECT post_mime_type, COUNT(*) as count 
            FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            GROUP BY post_mime_type
        ");
        
        if (!empty($by_mime)) {
            echo '<p><strong>По типам MIME:</strong></p>';
            echo '<table>';
            echo '<tr><th>Тип MIME</th><th>Количество</th></tr>';
            foreach ($by_mime as $row) {
                echo '<tr><td>' . esc_html($row->post_mime_type ?: 'не указан') . '</td><td>' . $row->count . '</td></tr>';
            }
            echo '</table>';
        }
        
        // Получаем последние 10 вложений
        $recent_attachments = $wpdb->get_results("
            SELECT ID, post_title, post_mime_type, post_date, guid
            FROM {$wpdb->posts}
            WHERE post_type = 'attachment'
            ORDER BY post_date DESC
            LIMIT 10
        ");
        
        if (!empty($recent_attachments)) {
            echo '<p><strong>Последние 10 вложений:</strong></p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Название</th><th>Тип</th><th>Дата</th><th>GUID</th><th>Файл существует?</th></tr>';
            foreach ($recent_attachments as $att) {
                $file_path = get_attached_file($att->ID);
                $file_exists = $file_path && file_exists($file_path);
                echo '<tr>';
                echo '<td>' . $att->ID . '</td>';
                echo '<td>' . esc_html($att->post_title ?: '(без названия)') . '</td>';
                echo '<td>' . esc_html($att->post_mime_type ?: 'не указан') . '</td>';
                echo '<td>' . $att->post_date . '</td>';
                echo '<td>' . esc_html($att->guid) . '</td>';
                echo '<td>' . ($file_exists ? '<span class="success">✅</span>' : '<span class="error">❌</span>') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="warning">⚠️ В базе данных нет записей о вложениях</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Проверка прав доступа</h2>
        <?php
        $upload_dir = wp_upload_dir();
        if (is_dir($upload_dir['basedir'])) {
            $perms = substr(sprintf('%o', fileperms($upload_dir['basedir'])), -4);
            echo '<p><strong>Права на директорию uploads:</strong> ' . $perms . '</p>';
            
            $owner = posix_getpwuid(fileowner($upload_dir['basedir']));
            $group = posix_getgrgid(filegroup($upload_dir['basedir']));
            echo '<p><strong>Владелец:</strong> ' . ($owner ? $owner['name'] : 'неизвестно') . '</p>';
            echo '<p><strong>Группа:</strong> ' . ($group ? $group['name'] : 'неизвестно') . '</p>';
            
            // Проверяем текущего пользователя PHP
            $current_user = get_current_user();
            echo '<p><strong>Текущий пользователь PHP:</strong> ' . $current_user . '</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>5. Проверка настроек WordPress</h2>
        <?php
        echo '<p><strong>WP_DEBUG:</strong> ' . (defined('WP_DEBUG') && WP_DEBUG ? 'включен' : 'выключен') . '</p>';
        echo '<p><strong>Версия WordPress:</strong> ' . get_bloginfo('version') . '</p>';
        echo '<p><strong>Максимальный размер загрузки:</strong> ' . size_format(wp_max_upload_size()) . '</p>';
        echo '<p><strong>Память PHP:</strong> ' . ini_get('memory_limit') . '</p>';
        echo '<p><strong>Максимальный размер POST:</strong> ' . ini_get('post_max_size') . '</p>';
        echo '<p><strong>Максимальный размер загрузки:</strong> ' . ini_get('upload_max_filesize') . '</p>';
        ?>
    </div>

    <div class="section">
        <h2>6. Проверка фильтров и хуков</h2>
        <?php
        global $wp_filter;
        $media_filters = [
            'upload_dir',
            'upload_mimes',
            'wp_get_attachment_url',
            'wp_get_attachment_image_attributes',
            'attachment_fields_to_edit',
            'media_library_show_upload_form'
        ];
        
        echo '<table>';
        echo '<tr><th>Фильтр/Хук</th><th>Зарегистрирован</th><th>Количество функций</th></tr>';
        foreach ($media_filters as $filter) {
            $registered = isset($wp_filter[$filter]);
            $count = $registered ? count($wp_filter[$filter]->callbacks) : 0;
            echo '<tr>';
            echo '<td><code>' . $filter . '</code></td>';
            echo '<td>' . ($registered ? '<span class="success">✅</span>' : '<span class="warning">⚠️</span>') . '</td>';
            echo '<td>' . $count . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
    </div>

    <div class="section">
        <h2>7. Возможные проблемы и решения</h2>
        <h3>Если файлы есть в директории, но не отображаются в админке:</h3>
        <ul>
            <li>Проверьте права доступа к файлам (должны быть 644 для файлов, 755 для директорий)</li>
            <li>Проверьте, что файлы принадлежат правильному пользователю/группе</li>
            <li>Проверьте настройки базы данных (таблица wp_posts)</li>
        </ul>
        
        <h3>Если в БД есть записи, но файлы отсутствуют:</h3>
        <ul>
            <li>Файлы могли быть удалены вручную</li>
            <li>Проблемы с правами доступа</li>
            <li>Неправильный путь к файлам</li>
        </ul>
        
        <h3>Если ничего нет ни в БД, ни в файлах:</h3>
        <ul>
            <li>Медиафайлы никогда не загружались</li>
            <li>Проблемы с правами доступа при загрузке</li>
            <li>Ограничения PHP (upload_max_filesize, post_max_size)</li>
        </ul>
        
        <h3>Быстрое решение:</h3>
        <pre>
# Проверьте права доступа
sudo chown -R www-data:www-data /var/www/kubiki.ai/wp-content/uploads
sudo chmod -R 755 /var/www/kubiki.ai/wp-content/uploads
sudo find /var/www/kubiki.ai/wp-content/uploads -type f -exec chmod 644 {} \;

# Проверьте логи ошибок
tail -f /var/log/php-fpm/error.log
# или
tail -f /var/www/kubiki.ai/wp-content/debug.log
        </pre>
    </div>
</body>
</html>

