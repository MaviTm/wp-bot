<?php
/*
 * daha fazla bilgi için http://mavitm.com/2Bm6Bblogm5B3Bm6Bviewm5B9Bm6B11.html 
 */
	require_once( dirname(__FILE__) . '/wp-load.php' );
	define('CODER', 'MaviTm');
	/*
	$sor = mysql_query("SELECT * FROM botkontrol");
	while ($a = mysql_fetch_object($sor)){
		echo '<pre>';
			print_r($a);
		echo '</pre>';
	}
	*/
	if(!is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.'mavitmBot'.DIRECTORY_SEPARATOR.'akilli.php')){
		echo 'Dosya eksik'; exit();
	}
	if($_GET['islem'] == 'akilli'){
            include (dirname(__FILE__).DIRECTORY_SEPARATOR.'mavitmBot'.DIRECTORY_SEPARATOR.'akilli.php');
    }elseif($_GET['islem'] == 'ajax'){
            include (dirname(__FILE__).DIRECTORY_SEPARATOR.'mavitmBot'.DIRECTORY_SEPARATOR.'ajax.php');
    }
	///echo get_option('default_ping_status');
	/*
	$wp_upload_dir = wp_upload_dir();
	echo '<pre>';
			print_r($wp_upload_dir);
		echo '</pre>';
	// Create post object
	  $my_post = array(
	     'post_title' => 'My post',
	     'post_content' => 'This is my post.',
	     'post_status' => 'publish',
	     'post_author' => 1,
	     'post_categorys' => array(8,6)
	  );
	
	// Insert the post into the database
	  $al = wp_insert_post( $my_post, $wp_error );
	  echo $wp_error;
	  print_r($al);
	*/
?>