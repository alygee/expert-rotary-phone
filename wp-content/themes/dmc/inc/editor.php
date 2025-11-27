<?php
/**
 * Функции для работы с редактором
 */

function my_adds_alls_elements($init) {
  if(current_user_can('unfiltered_html')) {
    $init['extended_valid_elements'] = 'span[*]';
  }
  return $init;
}

