<?php
add_filter( 'wpcf7_form_class_attr', 'wildli_custom_form_class_attr' );
function wildli_custom_form_class_attr( $class ) {
	$class .= ' row contato';
	return $class;
}

function maisPortfolio(){
	get_template_part('mais_portfolio');
	die();
}
	//Adiciona a funcao extra votos aos hooks ajax do WordPress.
add_action('wp_ajax_maisPortfolio', 'maisPortfolio');
add_action('wp_ajax_nopriv_maisPortfolio', 'maisPortfolio');


function getMaisNoticias(){
	get_template_part('mais_noticias');
	die();
}
	//Adiciona a funcao extra votos aos hooks ajax do WordPress.
add_action('wp_ajax_getMaisNoticias', 'getMaisNoticias');
add_action('wp_ajax_nopriv_getMaisNoticias', 'getMaisNoticias');

function image_resize_crop ( $src, $w, $h, $dest = null, $override = false, $createNewIfExists = false ) {
	$ext = array_pop ( explode ('.', $src) );
	$filenameSrc = str_replace (".$ext", '', basename($src) );
	$filename = "{$filenameSrc}-{$w}X{$h}";
	$arrayUploadPath = wp_upload_dir();
	$fileUploadSubDir = str_replace(basename($src),'', str_replace($arrayUploadPath['baseurl'], '', $src));
	$fileUploadDir = $arrayUploadPath['basedir'] . $fileUploadSubDir;

	if(is_null($dest)) $dest = $fileUploadDir;

	$i = null;
	if( ! $override && $createNewIfExists ) {
		$i = 0;
		while ( file_exists("$dest$filename-$i.png") ) $i++;
		$i = '-' . $i;
	}

	$fileFullPath = "$dest$filename$i.png";
	$fileFullUrl = $arrayUploadPath['baseurl'] . $fileUploadSubDir . $filename.$i .'.png';

    	//return cached file if $override == false and file's already there
	if( ! $override && file_exists($fileFullPath) ) return $fileFullUrl;

	if( $override ) @unlink($fileFullPath);

	switch ($ext) {
		case 'jpg':
		case 'jpeg' : $image = imagecreatefromjpeg($src); break;
		case 'gif' : $image = imagecreatefromgif($src); break;
		case 'png' : $image = imagecreatefrompng($src); break;
		case 'wbmp' :
		case 'bmp': $image = imagecreatefromwbmp($src); break;
		default: $image = imagecreatefromgd2($src);
	}
	$width = imagesx($image);
	$height = imagesy($image);

	$original_aspect = $width / $height;
	$thumb_aspect = $w / $h;

	if ( $original_aspect >= $thumb_aspect ) {
		if( $width > $w ) {
			$new_height = $h;
			$new_width = $width / ($height / $h);
		}else{
			$new_height = $height;
			$new_width = $width;
		}

	} else {
		if ( $width > $w ) {
			$new_width = $w;
			$new_height = $height / ($width / $w);
		} else {
			$new_width = $width;
			$new_height = $height;
		}
	}

	$thumb = imagecreatetruecolor($w, $h);
	$bg = imagecolorallocate($thumb, 255, 255, 255);
	imagefill($thumb, 0, 0, $bg);

	imagecopyresampled($thumb,
		$image,
		0 - ($new_width - $w) / 2,
		0 - ($new_height - $h) / 2,
		0, 0,
		$new_width, $new_height,
		$width, $height);

	imagepng($thumb, $fileFullPath, 9);
	imagedestroy($image);

	return $fileFullUrl;
}

?>