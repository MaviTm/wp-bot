$(document).ready(
	function(){
		$("div#pageLoad").ajaxStart(function(){
			$(this).slideDown("normal");
			$(this).find(".bar").stop(true,true).animate({width:"100%"},10000);
		});
		
		$("div#pageLoad").ajaxSuccess(function(){
			$(this).slideUp("normal");
			$(this).find(".bar").stop(true,true).animate({width:"0%"},10000);
		});
		
		$("#regAdd").click(function(){
			var tags = $("#regText").val();
			
			if(myempty(tags) == false){ alertCikart('Hata','Tag Eklemediniz'); return;}
			
			var adi = prompt("Bunun icin bir isim ver", "Adi");
			var sc = "type=tagAdd&adi="+adi+"&tag="+tags;
			$.ajax({type: 'POST', url: 'bot.php?islem=ajax', data: sc, success: function(cevap) {
				alertCikart('Bilgi', cevap);
				tagSelectYenile();
				$("#regText").val('');
			}});
		});
		
		$("#regKullan").click(function(){
			var tags = $("#regex").val();
			$("#regexArea").append('<div class="remove"><input type="text" class="scanTags span7" name="scanTags[]" value="'+tags+'" /> <buttun class="close" onclick="kaldirici(this)">X</button></div><div class="clear"></div>');
		});
		
		$("#parseButton").click(function(){
			var urlsi = $("#parseUrl").val();
			if(urlsi.indexOf("http://",0) == -1){ alertCikart('Hata','Url adresinizde yanlışlık var'); return;}
			var urlParse = parse_url(urlsi);
			var sc = "type=kaynakOku&url="+urlsi;
			$.ajax({type: 'POST', url: 'bot.php?islem=ajax', data: sc,  success: function(cevap) {
				//$("#inputa").val(cevap);
				var domcuk = jQuery(cevap);
				$("#doneler").html('');
				$("#imgler").html('');
				$("#baglantilar").html('');
				$(".scanTags").each(
					function(){
						if(myempty($(this).val()) != false){
							jsDomParser(domcuk, $(this).val());
						}
					}
				);
				
				$("#inputb").val($("title",domcuk).text());
				
				$("img",domcuk).each(function(){
					if($(this).attr("name") == 'keywords'){
						
						$("#inputt").val($(this).attr("content"));
						
					}else if($(this).attr("src").indexOf("http://",0) != -1){
						
						$("#imgler").append('<div class="addImg left"><img src="'+$(this).attr("src")+'" title="'+$(this).attr("title")+' - '+$(this).attr("alt")+'" class="img-polaroid" /><div class="p"> <a class="yd">Yazıya Dahil Et</a> <a class="ge">Galeriye Ekle</a> <a class="rb">Resme Bak</a> </div></div>');
							
					}
				});
				galeriIslemler();
				
				$("a",domcuk).each(function(){
					if(myempty($(this).text()) != false && $(this).attr("href").indexOf("http://",0) != -1){
						$("#baglantilar").append('<p><b>'+$(this).text()+'</b> : <a onclick="parseUrlKullan(this)">'+$(this).attr("href")+'</a></p><hr />');
					}else{
						$("#baglantilar").append('<p><b>'+$(this).text()+'</b> : <a onclick="parseUrlKullan(this)">http://'+urlParse.host+'/'+ltrim(ltrim(ltrim($(this).attr("href"),'/'),'./'),'../')+'</a></p><hr />');
					}
				});
				
				
			}});
		});
		
		 $("#regEdit").click(function(){
		 	alertCikart('<i class="icon-tag"></i> Tagları Düzenle <button class="right btn btn-success" onclick="allTagSave()" style="margin-right:20px;"><i class="icon-ok icon-white"></i> Kaydet</button>','');
		 	$("#modalText").html('<form id="regEditForm"></form>');
		 	$("#regEditForm").append('<div class="well">Başlık - Şablon</div>');
		 	$("#regex option").each(function(i){
		 		if(myempty($(this).attr("value")) != false){
		 			$("#regEditForm").append('<div class="well remove"><input type="hidden" name="oldName[]"  value="'+$(this).text().split(':')[0]+'" /><input type="text" name="tagName['+i+']" value="'+$(this).text().split(':')[0]+'" /> <input type="text" name="tagsValue['+i+']" value="'+$(this).attr("value")+'" /><buttun class="close" onclick="kaldirici(this)">X</button></div>');
		 		}
		 	});
		 }); 
		 /*
		 $("#saveButton").click(function(){
		 	var formVeri = $("#saveForm").serialize();
		 	var sc = "type=datainsert&veriler="+formVeri;
			$.ajax({type: 'POST', url: 'bot.php?islem=ajax', data: sc, success: function(cevap) {
				alertCikart('Bilgi', cevap);
			}});
		 });
		 */
		CKEDITOR.replace('inputa');
 		$("#saveForm").bind("submit", dataDbKaydet);
	}
);

function jsDomParser(domcuk,selector){
	var tag = selector.split('->')[0];
	var attr = selector.split('->')[1];
	var process = selector.split('->')[2];
	var attrName = $.trim(attr.replace('[','').replace(']','')).split('=')[0];
	var attrValue = $.trim(attr.replace('[','').replace(']','')).split('=')[1];
	var dugum;
	
	if(attrName == 'id'){
		if(process == 'innerHtml'){
			doneEkle(strip_tags ($(tag+"#"+attrValue,domcuk).html(),'<i><b><strong><p><em><h1><h2><h3><h4><h5><h6><img><br><br/>'));
		}else{
			doneEkle($(tag+"#"+attrValue,domcuk).attr(process));
		}
	}else if(attrName == 'class'){
		if(process == 'innerHtml'){
			$(tag+"."+attrValue,domcuk).each(function(){
				doneEkle(strip_tags($(this).html(),'<i><b><strong><p><em><h1><h2><h3><h4><h5><h6><img><br><br/>'));
			});
		}else{
			$(tag+"."+attrValue,domcuk).each(function(){
				if($(this).attr(attrName) == attrValue){
					doneEkle($(this).attr(process));
				}
			});
		}
	}else{
		if(tag == 'meta'){tag = 'img';}
		$(tag,domcuk).each(function(){
			if($(this).attr(attrName) == attrValue){
				if(process == 'innerHtml'){
					doneEkle(strip_tags($(this).html(),'<i><b><strong><p><em><h1><h2><h3><h4><h5><h6><img><br><br/>'));
				}else{
					doneEkle($(this).attr(process));
				}
			}
		});	
	}
}


function doneEkle(metin){
	var doneSelect = '<select class="doneSelect"><option value="0">Seç</option><option value="1">Başlık ekle</option><option value="2">Açıklama ekle</option><option value="3">Anahtar Kelime ekle</option></select>';
	var doneAb = '<select class="doneAb"><option value="0">Değiştir</option><option value="1">Üstüne Ekle</option><option value="2">Altına Ekle</option></select>';
	
	
	if(metin.length > 200){
		$("#doneler").append('<div class="doneDiv"><textarea class="doneElm" style="width:97%; max-width:97%">'+$.trim(metin)+'</textarea>'+doneSelect+' '+doneAb+' <button class="btn" onclick="doneUygula(this)">Uygula</button> <button class="btn" onclick="charChange(this)">Karakter Değiş</button></div><hr />');
	}else{
		$("#doneler").append('<div class="doneDiv"><input type="text" value="'+$.trim(metin)+'" class="doneElm" style="width:97%" />'+doneSelect+' '+doneAb+' <button class="btn" onclick="doneUygula(this)">Uygula</button></div><hr />');
	}
}

function doneUygula(bu){
	var nereye = $(bu).parent(".doneDiv").find(".doneSelect").val();
	var nesekilde = parseInt($(bu).parent(".doneDiv").find(".doneAb").val());
	var veri = $(bu).parent(".doneDiv").find(".doneElm").val();
	
	var formElm = (nereye == 1 ? 'inputb' : (nereye == 2 ? 'inputa' : (nereye == 3 ? 'inputt' : 'salla'))); 

	if(nesekilde == 1){
		if(nereye != 2){
			$("#"+formElm).val(veri+$("#"+formElm).val());
		}else{
			var editor = CKEDITOR.instances.inputa;
			var icerik = editor.getData();
			editor.setData(veri+icerik);
		}
	}else if(nesekilde == 2){
		if(nereye != 2){
			$("#"+formElm).val($("#"+formElm).val()+veri);
		}else{
			var editor = CKEDITOR.instances.inputa;
			editor.insertHtml(veri);
		}
	}else{
		if(nereye != 2){
			$("#"+formElm).val(veri);
		}else{
			var editor = CKEDITOR.instances.inputa;
			editor.setData(veri);
		}
	}
}

function charChange(bu){
	var veri = $(bu).parent(".doneDiv").find(".doneElm").val();
	$(bu).parent(".doneDiv").find(".doneElm").val(unescape(escape(veri)));
}

function galeriIslemler(){
	$(".rb").unbind('click');
	$(".yd").unbind('click');
	$(".ge").unbind('click');
	
	$(".rb").bind('click', function(){resimGoster(this)});
	$(".yd").bind('click', function(){yaziyaresimDahilEt(this);});
	$(".ge").bind('click', function(){resmiGaleriOlarakEkle(this);});
	
	$(".addImg").bind('mouseover',function(){
		var imgs = $(this).find("img").attr("src");
		if( $('#resimLayer').length == 0 ){
			$('body').append("<div id=\"resimLayer\"><img src=\""+imgs+"\" ></div>");
			$('#resimLayer').css("position","absolute");
		}
	});
	$(".addImg").bind('mouseout',function(){$('#resimLayer').remove();});
	$(".addImg").bind('mousemove',function(e){var x=e.pageX ;var y=e.pageY; $('#resimLayer').css("top", y+15);	$('#resimLayer').css("left", x+15);});
	
}

function parseUrlKullan(bu){
	$("#parseUrl").val($(bu).text());
}

function tagSelectYenile(){
	var sc = "type=tagreload";
	$.ajax({type: 'POST', url: 'bot.php?islem=ajax', data: sc, success: function(cevap) {
		$("#regex").html(cevap);		
	}});
}

function allTagSave(){
	var veri = $("#regEditForm").serialize();
	var sc = "type=tagEdit&taglar="+veri;
	$.ajax({type: 'POST', url: 'bot.php?islem=ajax', data: sc, success: function(cevap) {
		alertCikart('Bilgi', cevap);
		tagSelectYenile();
		$("#regText").val('');
	}});
		
}

function resimGoster(bu){
	var img = $(bu).parents(".addImg").find("img");
	alertCikart('Resim','<img src="'+img.attr("src")+'" />')
}

function yaziyaresimDahilEt(bu){
	var img = $(bu).parents(".addImg").find("img");
	var editor = CKEDITOR.instances.inputa;
	var aciklama = editor.getData();
	editor.setData('<img src="'+img.attr("src")+'" alt="" class="alignleft size-medium" /> '+aciklama);
}

function resmiGaleriOlarakEkle(bu){
	var img = $(bu).parents(".addImg").find("img");
	$("#galArea").append('<div class="remove left addImg"><img src="'+img.attr("src")+'"  class="img-polaroid" /><input type="text" value="'+img.attr("src")+'" name="galeri[]" /><div class="p"> <a class="btn mr5" onclick="kaldirici(this)"><i class="icon-remove"></i></a> <a class="rb btn mr5"><i class="icon-eye-open"></i></a></div></div>');
	galeriIslemler();
}

function aciklamaView(){
	var editor = CKEDITOR.instances.inputa;
	var icerik = editor.getData();
	var winP = window.open("", "", "letf=50,top=50,width=640,height=480,toolbar=0,scrollbars=0,status=0");
	winP.document.write('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
	winP.document.write('<link href="mavitmBot/css/bootstrap.min.css" rel="stylesheet" type="text/css" />');
	winP.document.write('</head><body>');
	winP.document.write(icerik);
	winP.document.write('</body></html>');
	winP.document.close();
}

function dataDbKaydet(){
	alertCikart('Bilgi','Lütfen Bekleyin...');
	$("#saveForm").attr("target","saveData"); // formu uplFrame frame post yap
	$("#saveData").bind("load", dataDbOk); //frame yuklendiginde uplOk() tetikle
}
function dataDbOk(){
	var message = $("#saveData").contents().find("body").text(); //frame icindeki sadece text leri al
	alertCikart('Bilgi', message);
}

function kaldirici(bu){
	$(bu).parents(".remove").remove();
}
function kaybedici(bu){
	$(bu).parents(".kaybet").fadeOut("normal");
}

function myempty(val){
	if(
		val == "" || 
		val == 0 || 
		val == '0' || 
		val == 'undefined' || 
		val == null || 
		val == false
	)
	{return false;}else{return true;}
}

function strip_tags (input, allowed) {
  allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
  });
}

function parse_url (str, component) {
  var key = ['source', 'scheme', 'authority', 'userInfo', 'user', 'pass', 'host', 'port',
            'relative', 'path', 'directory', 'file', 'query', 'fragment'],
    ini = (this.php_js && this.php_js.ini) || {},
    mode = (ini['phpjs.parse_url.mode'] &&
      ini['phpjs.parse_url.mode'].local_value) || 'php',
    parser = {
      php: /^(?:([^:\/?#]+):)?(?:\/\/()(?:(?:()(?:([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?()(?:(()(?:(?:[^?#\/]*\/)*)()(?:[^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-scheme to catch file:/// (should restrict this)
    };

  var m = parser[mode].exec(str),
    uri = {},
    i = 14;
  while (i--) {
    if (m[i]) {
      uri[key[i]] = m[i];
    }
  }

  if (component) {
    return uri[component.replace('PHP_URL_', '').toLowerCase()];
  }
  if (mode !== 'php') {
    var name = (ini['phpjs.parse_url.queryKey'] &&
        ini['phpjs.parse_url.queryKey'].local_value) || 'queryKey';
    parser = /(?:^|&)([^&=]*)=?([^&]*)/g;
    uri[name] = {};
    uri[key[12]].replace(parser, function ($0, $1, $2) {
      if ($1) {uri[name][$1] = $2;}
    });
  }
  delete uri.source;
  return uri;
  //parse_url('http://username:password@hostname/path?arg=value#anchor');
  //{scheme: 'http', host: 'hostname', user: 'username', pass: 'password', path: '/path', query: 'arg=value', fragment: 'anchor'}
}
function ltrim (str, charlist) {
  charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
  var re = new RegExp('^[' + charlist + ']+', 'g');
  return (str + '').replace(re, '');
}

function alertCikart(baslik,mesaj){
	$("#modalHead").html(baslik);
	$("#modalText").html(mesaj);
	$("#myModal").modal({ keyboard: false });
	$('#myModal').modal('show');
}
