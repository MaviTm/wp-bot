<?php
/*
 * daha fazla bilgi için http://mavitm.com/2Bm6Bblogm5B3Bm6Bviewm5B9Bm6B11.html 
 */
    session_start();
    $user = "admin";
    $pass = "asdf123";
    
    function loginForm($mesaj){
        ?>
        <div class="span4" style="margin:50px 120px">
            <h3>Mavi<sup>Tm</sup> Login Form</h3>
            <form method="post" action="" class="well">
                <h6><?=$mesaj;?></h6>
                <div class="control-group">
                      <label class="control-label" for="inputEmail">User</label>
                      <div class="controls">
                            <input id="inputEmail" placeholder="User" name="name" type="text">
                      </div>
                </div>
                <div class="control-group">
                      <label class="control-label" for="inputPassword">Password</label>
                      <div class="controls">
                            <input id="inputPassword" placeholder="Password" name="sifre" type="password">
                      </div>
                </div>
                <div class="control-group">
                      <div class="controls">
                            <button type="submit" class="btn">Sign in</button>
                      </div>
                </div>
             </form> 
         </div>
        <?php
    }
    
    class tools{
        public $list = array();
        
        public function selectTag(){
            $wp_upload_dir = wp_upload_dir();
            $uplDir = str_replace(array("/","\\"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $wp_upload_dir['basedir']).DIRECTORY_SEPARATOR;
            if(is_file($uplDir.'botTags.txt')){
                $tut = file($uplDir.'botTags.txt');
                foreach ($tut as $value) {
                    $ex = explode('|', $value);
                    $node = empty($ex[1]) ? $ex[0] : $ex[1];
                    if(empty($node)){continue;}
                    echo '<option value="'.$node.'">'.str_replace('|', ': ', $value).'</option>';
                }
            }else{
                echo '<option value="">Hiç data yok</option>';
            }
        }
        public function addTags(){
            $wp_upload_dir = wp_upload_dir();
            $uplDir = str_replace(array("/","\\"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $wp_upload_dir['basedir']).DIRECTORY_SEPARATOR;
            
            $adi = "\n".$_POST['adi'].'|'.$_POST['tag'];
            $file = fopen($uplDir.'botTags.txt', 'a');
            if(!$file){echo  'Data Dosyası erişimi kısıtlı. Ekleme yapılmadı'; fclose($file); return;}
            if(fputs($file, $adi)){
                echo 'Kayıt işlemi başarılı';
            }else{
                echo 'Kayıt işlemi başarısız';
            }
            fclose($file);
        }
        
        public function tagEdit(){
            $wp_upload_dir = wp_upload_dir();
            $uplDir = str_replace(array("/","\\"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $wp_upload_dir['basedir']).DIRECTORY_SEPARATOR;
            
            foreach ($_POST['tagsValue'] as $key => $value) {
                //if(empty($value)){continue;}
                $veriler .= (empty($_POST['tagName'][$key]) ? 'Adsız' : $_POST['tagName'][$key])."|".$_POST['tagsValue'][$key]."\n";
            }
            $newFile = $uplDir.'botTags_yedek_'.date('U').'.txt';
            
            
            copy($uplDir.'botTags.txt', $newFile);
            $file = fopen($uplDir.'botTags.txt', 'w');
            if(!$file){echo  'Data Dosyası erişimi kısıtlı. Ekleme yapılmadı'; fclose($file); return;}
            if(fputs($file, $veriler)){
                echo 'Taglarınız kayıt altına alındı. O iş <sub>t</sub>a<sup>m</sup>a<sub>m</sub>';
            }else{
                echo 'Kayıt işlemi başarısız.<br /><b>Veri kaybınız oldu ise işlem yapmadan önce '.$newFile.' şeklinde yedek aldım.</b>';
            }
            fclose($file);
            
        }

        public function datainsert(){
           
           $aciklama = $_POST['aciklama'];
           if(count($_POST['galeri']) > 0){
               $aciklama .= '[gallery orderby="title"]';
           }
           
           $aciklama = stripslashes($aciklama);
           $aciklama = str_replace('\"', '"', $aciklama);
           $aciklama = str_replace(
            array("rnrn",     "rn ",    " rn ",   " rn",     "<br>rn",   "<br>"), 
            array("<br />",  "<br />", "<br />", "<br />",  "<br />",   "<br />"), 
            $aciklama
          );
           
           $baslik = stripslashes($_POST['baslik']);
           
            $wpPost = array(
                 'post_title' => mysql_real_escape_string($baslik),
                 'post_content' => mysql_real_escape_string($aciklama),
                 'post_status' => 'publish',
                 'post_author' => 1,
                 //'post_type' => 'gallery',
                 //'post-formats' => 'gallery',
                 'ping_status' => get_option('default_ping_status'),
                 'post_category' => array($_POST['catid']),
                 'tags_input' => mysql_real_escape_string($_POST['tags'])
            );
                  
            $this->wpDataInsert($wpPost);
        }
        
        public function wpDataInsert($param){
            $postidno = wp_insert_post( $param, $wp_error );
            //echo $postidno;
            if($wp_error == false){
                echo 'Ekleme işlemi başarılı<br />';
                if(count($_POST['galeri']) > 0){
                    $this->myWpInsertAttachment($postidno);
                }
            }else{
                echo 'Ekleme işlemi başarısız.!';
            }
        }
        
        public function myWpInsertAttachment($postidno){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $wp_upload_dir = wp_upload_dir();
            
            foreach ($_POST['galeri'] as $cop){
                $p = explode('/',$cop);
                $res[] = end($p);
                copy($cop,'wp-content/uploads'.$wp_upload_dir['subdir'].'/'.end($p));
                $filename = 'wp-content/uploads'.$wp_upload_dir['subdir'].'/'.end($p);
                
                $wp_filetype = wp_check_filetype(basename($filename), null );
                $attachment = array(
                 'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path($filename), 
                 'post_mime_type' => $wp_filetype['type'],
                 'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                 'post_content' => '',
                 'post_status' => 'inherit'
              );
              $attach_id = wp_insert_attachment( $attachment, $filename, $postidno );
              $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
              wp_update_attachment_metadata( $attach_id, $attach_data );
            }
        }
        
         /** 
         * $_POST dizisini guvenli bir yapida duzenler. yardimci metod postGuv 
         * 
         * @param array $post
         * @return array
         */
        private function postGetAl(array $verim){
                $post = null;
                if(!is_array($verim)){ return array();}
                foreach ($verim as $veri=>$deger){

                    if(!is_array($deger)){
                        if($deger == ""){continue;}
                        $post[$veri] = $this->requestGuv($deger);
                    }else {
                        foreach ($deger as $dveri=>$dval){
                            $post[$veri][] = $this->requestGuv($dval);
                        }
                    }
                    
                }//for
            return $post;   
        }
        
        public function kaynakOku($url, $ex){
            if($ex){
                  $curl = curl_init();
                  $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
                  $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
                  $header[] = "Cache-Control: max-age=0";
                  $header[] = "Connection: keep-alive";
                  $header[] = "Keep-Alive: 300";
                  $header[] = "Accept-Charset: ISO-8859-9,utf-8;q=0.7,*;q=0.7";
                  $header[] = "Accept-Language: en-us,en;q=0.5";
                  $header[] = "Pragma: ";
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                  curl_setopt($curl, CURLOPT_REFERER, $url);
                  curl_setopt($curl, CURLOPT_HEADER, 1);
                  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                  curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
                  curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                  curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                  $html = curl_exec($curl); // execute the curl command
                  curl_close($curl); // close the connection
                  $al = $html; // and finally, return $html
            }else{
                $al = file_get_contents($url);
            }
            
            if(strpos($al, 'charset=ISO-8859-9') !== false || strpos($al, 'charset=windows-1254') !== false){
                $al = iconv('ISO-8859-1', 'UTF-8', $al);
                $change = array("ý","þ","ð","Ý","Þ","Ð");
                $duzgun = array("ı","ş","ğ","İ","Ş","Ğ");//i,s,g,I,S,G
                $al = str_replace($change, $duzgun, $al);
            }
            
            $al = preg_replace('#<script type=\'text\/javascript\'>(.*?)</script>#si', '', $al);
            $al = preg_replace('#<script[\w|\W]*?>(.*?)</script>#si', '', $al);
            $al = preg_replace('#<body[\w|\W]*?>#si', '', $al);
            $al = preg_replace('#<!--(.*?)-->#si', '', $al);
            $al = str_replace(array("<body>","<head>","</head>","<meta"), array("","<body><div class=\"img\">","</div>","<img src=\"\" "), $al);
            return str_replace("<body>", "<head></head><body>", $al);
            return preg_replace(array('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '/\r\n|\r|\n|\t|\s\s+/'), '', $al);
            
        }
        
        public function wpKategori(){
            $kategoriler = get_categories( "hide_empty=0" );
            $returnCat = array( );
            foreach ($kategoriler as $kat){
                //$returnCat[$kat->cat_ID] = $kat->cat_name.' ('.$kat->cat_ID.')';
                echo '<option value="'.$kat->cat_ID.'">'.$kat->cat_name.' (ID: '.$kat->cat_ID.')</option>';
            }
        }
        
        /**
         * veri suzgeci 
         *
         * @param mixed $data
         * @return mixed
         */
        public function requestGuv($data){
            if (is_null($data)) return null;
            if (is_numeric($data)) return $data;
            if (get_magic_quotes_gpc()){$data = stripslashes($data);}
            $bul = array("eval","include_once","include","require_once","require","drop","alter","<?php","<?","?>", "\"", "'", "\\");
            $sil = array(    "",            "",       "",            "",       "",    "",     "",     "",  "",  "",   "",  "",   "");
            $data = str_ireplace($bul,$sil,strip_tags($data));
            return $data; 
        }
        
        public function redirect($url){
            if (!headers_sent()){ 
                @header('Location: '.$url); exit;
            }else{
                echo '<script type="text/javascript">';
                echo 'window.location.href="'.$url.'";';
                echo '</script>';
                echo '<noscript>';
                echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
                echo '</noscript>'; exit;
            }
        }
    }



?>