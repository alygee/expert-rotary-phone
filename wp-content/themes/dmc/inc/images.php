<?php
/**
 * Функции для работы с изображениями
 */

function unset_attach_srcset_attr( $attr ){
	foreach( array('sizes','srcset') as $key )
	    if( isset($attr[ $key ]) ) unset($attr[ $key ]);
	    return $attr;
}

