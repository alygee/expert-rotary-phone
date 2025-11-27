<?php
/**
 * Функции для работы с загрузкой файлов
 */

function upload_allow_types( $mimes ) {
  $mimes['svg']  =  'image/svg+xml';
  return $mimes;
}

