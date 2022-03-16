<?php 
function imgToHex($filename){
	$handle = fopen($filename, "rb");
	$binContents = fread($handle, filesize($filename));
	fclose($handle);
	$hexContents = bin2hex($binContents);
	return $hexContents;
}
function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}
function text_align($text, $length = 0, $align = ''){
	$list_align = array(
		'left' 		=> STR_PAD_RIGHT,
		'right' 	=> STR_PAD_LEFT,
		'center' 	=> STR_PAD_BOTH
	);
	$align = !empty($align) ? $list_align[$align] : STR_PAD_RIGHT;
	return str_pad($text, $length, ' ', $align);
}