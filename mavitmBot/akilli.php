<?php
/*
 * daha fazla bilgi için http://mavitm.com/2Bm6Bblogm5B3Bm6Bviewm5B9Bm6B11.html 
 */
    include(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib.php');  
    include(dirname(__FILE__).DIRECTORY_SEPARATOR.'akilliBot.php');
    $tools = akilliBot::loads('tools');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="tr">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex">
        <title>MaviTm Bot</title>  
        <link href="mavitmBot/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="mavitmBot/js/jquery.js" charset="utf-8"></script>
        <script type="text/javascript" src="mavitmBot/js/bootstrap.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="mavitmBot/htmlEditor/ckeditor.js" charset="utf-8"></script>
        <script type="text/javascript" src="mavitmBot/js/mavitm.js" charset="utf-8"></script>
</head>
    <body>
        <?php
        if(empty($_SESSION['MaviTm'])){
            if(isset($_POST['name'])){
                if($_POST['name'] == $user && $_POST['sifre'] == $pass){
                    $_SESSION['MaviTm'] = "Ayhan";
                    $tools->redirect("bot.php?islem=akilli");
                }else{
                    loginForm("Hatalı Kullanıcı"); exit();
                }
             }else{
                    loginForm(empty($_POST) ? '' : 'Boş alan bırakmayın'); exit();
             } 
        }else{
            ?>
                <div class="container-fluid border">
                        <h3>Mavi<sup>Tm</sup> Bot - Jquery Dom <small>(BETA)</small></h3>
                        <hr />
                        <div class="well">
                                <div style="float:right;">
                                    <div class="input-append">
                                        <input placeholder="Tag" type="text" id="regText" style="width:208px;" />
                                        <div class="btn-group">
                                            <button class="btn" id="regAdd">Ekle</button>
                                        </div>
                                    </div>
                                    <select id="regex" style="width:275px;">
                                        <option value="">Seçiniz</option>
                                        <?php $tools->selectTag();?>
                                    </select>
                                    <div class="clear"></div>
                                    <button id="regKullan" class="btn right">Kullan</button>
                                    <button id="regEdit" class="btn right" style="margin-right: 10px;">Tagları Düzenle</button>
                                </div>                            
                            
                                <div class="left">
                                    <form id="parseUrlForm">
                                        <div class="input-append">
                                            <input class="span7"  placeholder="Url" type="text" id="parseUrl" />
                                            <div class="btn-group">
                                                <a class="btn" id="parseButton">Parçala</a>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                            <div class="progress progress-striped active none" id="pageLoad">
                                                <div class="bar" style="width:0%;"></div>
                                            </div>
                                        <div id="regexArea"></div>
                                    </form>
                                </div>
                                
                                <div class="clear"></div>
                        </div>
                    <div class="well" style="float:left; margin-right:20px; width:600px;">
                        <form id="saveForm" method="post" action="bot.php?islem=ajax">
                            <input type="hidden" name="formGonder" value="true" />
                            <input type="hidden" name="formGonder2" value="1" />
                            <input type="hidden" name="type" value="datainsert" />
                            <h5>Ekleme Formu</h5>
                            <div class="control-group">
                                  <label class="control-label" for="inputb">Başlık</label>
                                  <div class="controls">
                                        <input id="inputb" placeholder="Başlık" name="baslik" type="text" style="max-width:580px; min-width:580px;" />
                                  </div>
                            </div>
                            <div class="control-group">
                                  <label class="control-label" for="inputa">Açıklama</label>
                                  <div class="controls">
                                        <textarea name="aciklama" id="inputa" style="max-width:580px; min-width:580px;"></textarea>
                                        <button type="button" class="btn right" onclick="aciklamaView()"><i class="icon-eye-open"></i> Ön İzleme</button>
                                        <div class="clear"></div>
                                  </div>
                            </div>
                            <div class="control-group">
                                  <label class="control-label" for="inputt">Anahtar Kelime</label>
                                  <div class="controls">
                                        <input id="inputt" placeholder="Anahtar Kelime" name="tags" type="text" style="max-width:580px; min-width:580px;" />
                                  </div>
                            </div>
                            <div class="control-group">
                                  <label class="control-label" for="inputt">Wp Kategorin</label>
                                  <div class="controls">
                                        <select name="catid" style="max-width:580px; min-width:580px;">
                                            <?php $tools->wpKategori();?>
                                        </select>
                                  </div>
                            </div>
                            <div class="control-group" id="galArea">
                                
                            </div>
                            <div class="clear"></div>
                            <div class="control-group">
                                  <div class="controls">
                                        <button type="submit" id="saveButton" class="btn btn-primary" style="float:right;"><i class="icon-ok icon-white"></i> Kaydet</button>
                                  </div>
                            </div>
                        </form>
                    </div>
                    <div class="well left" style="width:500px;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#doneler" data-toggle="tab">Text</a></li>
                            <li><a href="#imgler" data-toggle="tab">İmage</a></li>
                            <li><a href="#baglantilar" data-toggle="tab">Bağlantılar</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="doneler"><p>Veri Alanı</p></div>
                            <div class="tab-pane" id="imgler" style="max-height:640px; overflow:auto;"><p>Resim Alanı</p></div>
                            <div class="tab-pane" id="baglantilar" style="max-height:640px; overflow:auto;"><p>Url Alanı</p></div>
                        </div>
                    </div>
             </div>   
            <?php
        }
        ?>
     <div id="myModal" class="modal none kaybet">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="kaybedici(this)">x</button>
            <h3 id="modalHead">Baslik</h3>
        </div>
        <div class="modal-body">
            <p id="modalText">Mesaj</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="kaybedici(this)"><i class="icon-remove icon-white"></i> Close</a>
        </div>
    </div>
    <div class="none"><iframe name="saveData" id="saveData" style="border:none; height:1px; width: 1px;"></iframe></div>
    </body>
</html>