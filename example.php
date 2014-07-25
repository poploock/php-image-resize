<?php

//Include
require_once dirname(__FILE__) . '/imageresize.class.php';

//init
$image = new ImageResize();

// set source file
$image->__set('file_name' , "https://www.google.com/images/srpr/logo11w.png");

// set upload patch
$image->__set('upload_patch' , "upload/");

// set file prefix
$image->__set('file_prefix' , "prefix_");

// set file suffix
$image->__set('file_suffix' , "_suffix");

// set width of image
$image->__set('new_width' , "70");

// set height of image
$image->__set('new_height' , "70");

// set quality of image
$image->__set('quality' , "100");

// set upload patch and file not exist
$image->check();

// save image
$image->save();

?>
