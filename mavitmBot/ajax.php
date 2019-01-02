<?php
/*
 * daha fazla bilgi için http://mavitm.com/2Bm6Bblogm5B3Bm6Bviewm5B9Bm6B11.html 
 */
 
 	if(!$_POST){echo 'istek bulunmuyor.';exit();} //post islemi olmadan calismaz

	if(empty($_POST['type'])){echo 'Eksik istek'; exit(); }
	if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
	    echo ' Http protokol ihlali.'; exit();
	}
	
	$host = str_replace(array("http://","www"), array("",""), $_SERVER['HTTP_HOST']);
	$ref = str_replace(array("http://","www"), array("",""), $_SERVER['HTTP_REFERER']);
	
	if(strpos($ref, $host) === false){
	    echo 'Hata'; exit();
	}
	
	
    include(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib.php');  
    if(empty($_SESSION['MaviTm'])){echo "yetki hatasi"; exit();}
    include(dirname(__FILE__).DIRECTORY_SEPARATOR.'akilliBot.php');
    $tools = akilliBot::loads('tools');
    
    switch ($_POST['type']) {
        case 'tagAdd': $tools->addTags();   break;
        case 'kaynakOku' : echo $tools->kaynakOku($_POST['url'], extension_loaded('curl'));  break;
        case 'tagreload': '<option value="">Seçiniz</option>'.$tools->selectTag(); break;
        case 'tagEdit':     $tools->tagEdit(); break;
        case 'datainsert':  $tools->datainsert(); break;
        default: echo 'Yanlis istek';   break;
    }
?>