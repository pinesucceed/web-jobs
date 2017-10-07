<?php 
if(session_id() == ''){
        session_start(); 
}
$text = rand(10000,99999); 
$_SESSION["captcha"] = $text; 
$height = 61; 
$width = 124; 

$image_p = imagecreate($width, $height); 
$black = imagecolorallocate($image_p, 0, 0, 0); 
$white = imagecolorallocate($image_p, 255, 255, 255); 
$font_size = 16; 

imagestring($image_p, $font_size, 40, 20, $text, $white); 
imagejpeg($image_p, null, 80); 