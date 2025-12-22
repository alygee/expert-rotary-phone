<?php
/**
 * Template Name: Inssmart Form Page
 * 
 * Шаблон страницы для отображения формы Inssmart без хедера и футера
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta content="telephone=no" name="format-detection">
  <meta name="theme-color" content="#F1F1F5">
  <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
  <?php wp_head(); ?>
</head>

<body>
    <?php
    // Выводим содержимое страницы, если оно есть
    if (have_posts()) {
      while (have_posts()) {
        the_post();
        ?>
        <div class="page-content" style="max-width: 1200px; margin: 0 auto;">
          <?php
          // Выводим контент страницы перед формой, если есть
          $content = get_the_content();
          if (!empty($content)) {
            echo '<div class="page-content-text" style="margin-bottom: 30px;">';
            the_content();
            echo '</div>';
          }
          
          // Выводим форму Inssmart через шорткод
          echo '<div class="inssmart-form-wrapper">';
          echo do_shortcode('[inssmart_form]');
          echo '</div>';
          ?>
        </div>
        <?php
      }
    } else {
      // Если нет контента, просто выводим форму
      echo '<div style="max-width: 1200px; margin: 0 auto;">';
      echo do_shortcode('[inssmart_form]');
      echo '</div>';
    }
    ?>
  
  <?php wp_footer(); ?>
</body>
</html>

