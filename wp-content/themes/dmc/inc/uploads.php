<?php
/**
 * Функции для работы с загрузкой файлов
 */

function upload_allow_types( $mimes ) {
  // Разрешаем SVG
  $mimes['svg']  =  'image/svg+xml';
  
  // Разрешаем CSV файлы
  $mimes['csv']  =  'text/csv';
  
  // Разрешаем Excel файлы
  $mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
  $mimes['xls']  = 'application/vnd.ms-excel';
  
  return $mimes;
}

