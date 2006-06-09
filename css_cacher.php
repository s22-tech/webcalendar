<?php
/* $Id$ */
include_once 'includes/init.php';
//we will cache css as default, but override from admin and pref
//by incrementing the webcalendar_csscache cookie value


header( 'Content-type: text/css' ); 
header('Expires: ' . date( 'D, j M Y H:i:s', time() +  86400 ) . ' UTC');
header('Cache-Control: Public');
header('Pragma: Public');

if ( ini_get ( 'zlib.output_compression' ) != 1 ) 
  ob_start( 'ob_gzhandler' );


include_once ( 'includes/styles.php' );

?>