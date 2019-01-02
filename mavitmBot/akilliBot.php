<?php
/* SINIF : html dom parser
 * YAZAR : Ayhan ERASLAN 
 * SITE  : www.mavitm.com
 * daha fazla bilgi için http://mavitm.com/2Bm6Bblogm5B3Bm6Bviewm5B9Bm6B11.html 
 */
    final class akilliBot{
        private static $_instances = array();
        private $url = null;
        private $deleteTag = array("script", "embed", "style", "object", "hr", "link", "iframe","blockquote", "code", "strike"), 
                $scanTag = array("div"),
                $addTag = array("title","meta->[name=description]->content","meta->[name=keywords]->content");
        public $dataArr = array();      
        public $tags = array(); 
         
        public function __construct(){}
        
        ############### SET METODLAR ###############
        
        public function deleteTagSet($name, $value, $all = false){
            if($all && is_array($value)){
                $this->deleteTag = array();
                $this->deleteTag = $value;
            }else{
                $this->deleteTag[$name] = $value;
            }
            return $this;
        }
        
        public function scanTagSet($name, $value, $all = false){
            if($all && is_array($value)){
                $this->scanTag = array();
                $this->scanTag = $value;
            }else{
                $this->scanTag[$name] = $value;
            }
            return $this;
        }
        
        /**
         * Haricen istenilen tag
         */
        public function addTagSet($value, $all = false){
            if($all && is_array($value)){
                $this->addTag = array();
                $this->addTag = $value;
            }else{
                $this->addTag[] = $value;
            }
            return $this;
        }
        
        ############### SET METODLAR ###############
        
        
        
        ############### GET METODLAR ###############
        public function getAll(){
            return $this->dataArr;
        }
        ############### GET METODLAR ###############
        
        public function scanTag($tag = false){
            $dom =& self::loads('DOMDocument');
            
            if(count($this->deleteTag) > 0){
                foreach($this->deleteTag as $remove){
                    $cl = $dom->getElementsByTagName($remove); 
                    $forlength = $cl->length; 
                    for ($i = 0; $i < $forlength ; $i++) { 
                      $cl->item(0)->parentNode->removeChild($cl->item(0)); 
                    }
                    
                } 
            }
            /**/
            if(count($this->scanTag) > 0){
                $x = 200;
                foreach ($this->scanTag as $value) {
                    $tags = $dom->getElementsByTagName($value);
                    for ($i=0; $i < $tags->length; $i++) {
                        
                    }
                }
            }else{
                $this->dataArr['textContent'] = $dom->textContent;
            }
            
            if(count($this->addTag) > 0){
                foreach ($this->addTag as $value) {
                    
                    if(strpos($value, '->') !== false){
                        
                        list($tag, $attr, $val) = explode('->', $value);
                        $attr = explode('=', str_replace(array("[","]"), array("",""), $attr));
                        $nod = $dom->getElementsByTagName($tag);
                        for ($i = 0; $i  < $nod->length; $i ++) { 

                           if($nod->item($i)->getAttribute($attr[0]) == $attr[1]){
                               if($val == "innerHtml"){
                                   $this->dataArr[$tag.'_'.$attr[1]] = $this->textClear($nod->item($i)->nodeValue);
                               }else{
                                   $this->dataArr[$tag.'_'.$attr[1]] = $this->textClear($nod->item($i)->getAttribute($val));
                               }
                           }
                        }
                        
                    }else{
                        $nod =  $dom->getElementsByTagName($value);
                        $this->dataArr[$value] = $nod->item(0)->nodeValue;
                    }
                }
            }
        }
        
        public function imgAll($imgs){
            if(is_object($imgs)){
                foreach ($imgs as $subNodes) {
                    $kontrol = $subNodes->getAttribute("src");
                    if(strpos($kontrol, 'http://') === false && !empty($this->url)){
                        $url = parse_url($this->url);
                        $this->dataArr['oldsrc'][] = 'http://'.$url['host'].dirname($url['path']).'/'.str_replace(array(dirname($url['path']).'/',dirname($url['path'])), array("",""), $kontrol);
                    }elseif(strpos($kontrol, 'http://') !== false){
                        $this->dataArr['oldsrc'][] = $kontrol;
                    }
                }
            }
        }
        
        public function sourceAdd($url){
            $dom =& self::loads('DOMDocument');
            if(strpos($url, "http://") !== false){
                $this->url = $url;
                $source = $this->kaynakOku($url, extension_loaded('curl'));
            }elseif(is_file($url) && is_readable($url)){
                $source = file_get_contents($url,true);
            }else{
                $source = $url;
            }
            
            @$dom->loadHTML($source);
            return $this;
        }
        
        private function kaynakOku($url, $ex){
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
                  curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
                  curl_setopt($curl, CURLOPT_HEADER, 1);
                  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                  curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
                  curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                  curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                  $html = curl_exec($curl); // execute the curl command
                  curl_close($curl); // close the connection
                  return $html; // and finally, return $html
            }else{
                return file_get_contents($url);
            }
        }

        public function textClear($text){
            return $text;
            return strip_tags(
                        preg_replace(
                                    array('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '/\r\n|\r|\n|\t|\s\s+/'), 
                                    '', 
                                    $text
                        ),
                        '<strong>,<em>,<i>,<b>,<span>,<br>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<p>'
                    );
        }

        public static function loads( $sinif, array $param = array() ){   
            $p = false;
            $toplamParametre = count($param);
            
            if($toplamParametre >= 1){
                $func = create_function('&$val', 'return (string) $val;');
                $param = array_map($func, $param); 
                $paramS = array_fill(0,$toplamParametre,'%s'); 
                $olusacakParametre = implode(',',$paramS);
                $p = true;
            }
            
            if (array_key_exists($sinif, self::$_instances)) {
                 if($p){
                     if(method_exists(self::$_instances[$sinif], "__construct")){
                         call_user_func_array(array(self::$_instances[$sinif], "__construct"), $param);
                         return self::$_instances[$sinif];
                     }elseif(method_exists(self::$_instances[$sinif], $sinif)){
                         call_user_func_array(array(self::$_instances[$sinif], $sinif), $param);
                         return self::$_instances[$sinif];
                     }else{
                         return self::$_instances[$sinif];
                     }
                 }else{ 
                    return self::$_instances[$sinif];
                 }
            } 
            
            if($p == true){
                $string = '$start = new $sinif('.vsprintf($olusacakParametre,$param).');';
                @eval($string);
            }else{
                $start = new $sinif();
            }
            
            self::$_instances[$sinif] = $start;
            return $start;
        }
        
    }
?>
