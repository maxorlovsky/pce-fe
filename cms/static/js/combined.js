if(function(){function a(a){return"string"==typeof a&&(a=j.getElementById(a)),a}function b(a,b,c){if(k.addEventListener)a.addEventListener(b,c,!1);else if(k.attachEvent){var d=function(){c.call(a,k.event)};a.attachEvent("on"+b,d)}}function c(a,b){return a.className.match(new RegExp("(\\s|^)"+b+"(\\s|$)"))}function d(a,b){c(a,b)||(a.className+=" "+b)}function e(a,b){var c=new RegExp("(\\s|^)"+b+"(\\s|$)");a.className=a.className.replace(c," ")}function f(a){var b,c,d,e,f=m(a);return b=f.left,d=f.top,c=b+a.offsetWidth,e=d+a.offsetHeight,{left:b,right:c,top:d,bottom:e}}function g(a){if(!a.pageX&&a.clientX){var b=1,c=document.body;if(c.getBoundingClientRect){var d=c.getBoundingClientRect();b=(d.right-d.left)/c.clientWidth}return{x:a.clientX/b+j.body.scrollLeft+j.documentElement.scrollLeft,y:a.clientY/b+j.body.scrollTop+j.documentElement.scrollTop}}return{x:a.pageX,y:a.pageY}}function h(a){return a.replace(/.*(\/|\\)/,"")}function i(a){return/[.]/.exec(a)?/[^.]+$/.exec(a.toLowerCase()):""}var j=document,k=window,l=function(){var a=j.createElement("div");return function(b){a.innerHTML=b;var c=a.childNodes[0];return a.removeChild(c),c}}();if(document.documentElement.getBoundingClientRect)var m=function(a){var b=a.getBoundingClientRect(),c=a.ownerDocument,d=c.body,e=c.documentElement,f=e.clientTop||d.clientTop||0,g=e.clientLeft||d.clientLeft||0,h=1;if(d.getBoundingClientRect){var i=d.getBoundingClientRect();h=(i.right-i.left)/d.clientWidth}h>1&&(f=0,g=0);var j=b.top/h+(window.pageYOffset||e&&e.scrollTop/h||d.scrollTop/h)-f,k=b.left/h+(window.pageXOffset||e&&e.scrollLeft/h||d.scrollLeft/h)-g;return{top:j,left:k}};else var m=function(a){if(k.jQuery)return jQuery(a).offset();var b=0,c=0;do b+=a.offsetTop||0,c+=a.offsetLeft||0;while(a=a.offsetParent);return{left:c,top:b}};var n=function(){var a=0;return function(){return"ValumsAjaxUpload"+a++}}();Ajax_upload=AjaxUpload=function(b,c){if(b.jquery?b=b[0]:"string"==typeof b&&/^#.*/.test(b)&&(b=b.slice(1)),b=a(b),this._input=null,this._button=b,this._disabled=!1,this._submitting=!1,this._justClicked=!1,this._parentDialog=j.body,window.jQuery&&jQuery.ui&&jQuery.ui.dialog){var d=jQuery(this._button).parents(".ui-dialog");d.length&&(this._parentDialog=d[0])}this._settings={action:"upload.php",name:"userfile",data:{},autoSubmit:!0,responseType:!1,onChange:function(a,b){},onSubmit:function(a,b){},onComplete:function(a,b){}};for(var e in c)this._settings[e]=c[e];this._createInput(),this._rerouteClicks()},AjaxUpload.prototype={setData:function(a){this._settings.data=a},disable:function(){this._disabled=!0},enable:function(){this._disabled=!1},destroy:function(){this._input&&(this._input.parentNode&&this._input.parentNode.removeChild(this._input),this._input=null)},_createInput:function(){var a=this,c=j.createElement("input");c.setAttribute("type","file"),c.setAttribute("name",this._settings.name);var d={position:"absolute",margin:"-5px 0 0 -175px",padding:0,width:"220px",height:"30px",fontSize:"14px",opacity:0,cursor:"pointer",display:"none",zIndex:2147483583};for(var e in d)c.style[e]=d[e];"0"!==c.style.opacity&&(c.style.filter="alpha(opacity=0)"),this._parentDialog.appendChild(c),b(c,"change",function(){var b=h(this.value);0!=a._settings.onChange.call(a,b,i(b))&&a._settings.autoSubmit&&a.submit()}),b(c,"click",function(){a.justClicked=!0,setTimeout(function(){a.justClicked=!1},3e3)}),this._input=c},_rerouteClicks:function(){var a,c=this,h={top:0,left:0},i=!1;b(c._button,"mouseover",function(b){c._input&&!i&&(i=!0,a=f(c._button),c._parentDialog!=j.body&&(h=m(c._parentDialog)))}),b(document,"mousemove",function(b){var f=c._input;if(f&&i){if(c._disabled)return e(c._button,"hover"),void(f.style.display="none");var j=g(b);j.x>=a.left&&j.x<=a.right&&j.y>=a.top&&j.y<=a.bottom?(f.style.top=j.y-h.top+"px",f.style.left=j.x-h.left+"px",f.style.display="block",d(c._button,"hover")):(i=!1,c.justClicked||(f.style.display="none"),e(c._button,"hover"))}})},_createIframe:function(){var a=n(),b=l('<iframe src="javascript:false;" name="'+a+'" />');return b.id=a,b.style.display="none",j.body.appendChild(b),b},submit:function(){var a=this,c=this._settings;if(""!==this._input.value){var d=h(this._input.value);if(0!=c.onSubmit.call(this,d,i(d))){var e=this._createIframe(),f=this._createForm(e);f.appendChild(this._input),f.submit(),j.body.removeChild(f),f=null,this._input=null,this._createInput();var g=!1;b(e,"load",function(b){if("javascript:'%3Chtml%3E%3C/html%3E';"==e.src||"javascript:'<html></html>';"==e.src)return void(g&&setTimeout(function(){j.body.removeChild(e)},0));var f=e.contentDocument?e.contentDocument:frames[e.id].document;if(!(f.readyState&&"complete"!=f.readyState||f.body&&"false"==f.body.innerHTML)){var h;if(f.XMLDocument)h=f.XMLDocument;else if(f.body)h=f.body.innerHTML,c.responseType&&"json"==c.responseType.toLowerCase()&&(f.body.firstChild&&"PRE"==f.body.firstChild.nodeName.toUpperCase()&&(h=f.body.firstChild.firstChild.nodeValue),h=h?window.eval("("+h+")"):{});else var h=f;c.onComplete.call(a,d,h),g=!0,e.src="javascript:'<html></html>';"}})}else j.body.removeChild(this._input),this._input=null,this._createInput()}},_createForm:function(a){var b=this._settings,c=l('<form method="post" enctype="multipart/form-data"></form>');c.style.display="none",c.action=b.action,c.target=a.name,j.body.appendChild(c);for(var d in b.data){var e=j.createElement("input");e.type="hidden",e.name=d,e.value=b.data[d],c.appendChild(e)}return c}}}(),logged_in&&($(window).on("hashchange",function(){page=window.location.hash,TM.showPage(page)}),$(document).on("click","body",function(){TM.runSessionTimeout()}),$(document).on("click","#cmsUpdate",function(){TM.updateCMS()}),$(document).on("click",".submitButton",function(){if(1==TM.formInProgress)return!1;TM.formInProgress=1;var a=$(this).html();$(this).html("Loading..."),tinymce.triggerSave();var b=$(this).parents("table");form={},b.find("input").each(function(){form[$(this).attr("id")]=$(this).val()}),b.find("textarea").each(function(){form[$(this).attr("id")]=$(this).val()}),b.find("select").each(function(){form[$(this).attr("id")]=$(this).val()}),param=window.location.hash.split("/"),param[2]&&(form.param_id=param[2]),TM.showMsg(2,strings.loading);var c={type:"POST",timeout:1e4,data:{control:"submitForm",module:b.attr("name"),action:b.attr("id"),form:form},success:function(c){answer=c.split(";"),TM.cleanMsg(),TM.showMsg(answer[0],answer[1]),$(".submitButton").html(a),TM.formInProgress=0,TM.messageTimer=setTimeout(TM.cleanMsg,3e3),1==answer[0]&&(TM.showMsg(answer[0],answer[1]+(1==redirect?" ("+strings.will_redirect_auto+")":"")),1==redirect&&TM.goDelay("#"+b.attr("name"),3200))},error:function(b,c,d){TM.formInProgress=0,TM.showMsg(0,"Error timeout"),TM.messageTimer=setTimeout(TM.cleanMsg,3e3),$(".submitButton").html(a)}};TM.ajax(c)})),$(".content").length>0){var offset=$(".content").offset();$("#loading").offset({top:offset.top,left:offset.left})}$(document).on("mousemove",".hint",function(a){msg=$(this).attr("name"),$("#hint").offset({top:a.pageY-30,left:a.pageX+10}),$("#hint").html!=msg&&$("#hint").html(msg),1!=TM.blockHint&&$("#hint").is(":hidden")&&$("#hint").show()}).on("mouseout",".hint",function(){$("#hint").offset({top:0,left:0}),$("#hint").hide()}),$(document).on("click","a",function(){TM.blockHint=1,$(".hint").trigger("mouseout"),$("#submenu").is(":visible")&&"site_name_val"!=$(this).attr("id")&&$("#submenu").slideUp("fast")}),$("#menusub").click(function(){$(this).find("#arrows").removeClass("active"),$(this).find("#submenu").is(":hidden")&&$(this).find("#arrows").addClass("active"),$("#submenu").slideToggle("fast")}),$("#submenu").on("click",function(){$("#submenu a").removeClass("active"),$(this).addClass("active")}),$(".hint").mouseover(function(a){msg=$(this).attr("name"),$("#hint").offset({top:a.pageY-20+window.pageYOffset,left:a.pageX+10}),$("#hint").html(msg),$("#hint").show()}),$(".hint").mouseout(function(){$("#hint").offset({top:0,left:0}),$("#hint").hide()});var TM={site:site,lang:lang,blockHint:0,formInProgress:0,messageTimer:0,sessionTimeout:0,editSettingProgress:0,saveSettingId:"",saveSettingInput:"",changeOrder:function(a,b){TM.showMsg(2,strings.loading);var c={type:"POST",timeout:1e4,data:{control:"saveOrder",page:a,ids:b},success:function(a){answer=a.split(";"),TM.cleanMsg(),TM.showMsg(answer[0],answer[1]),TM.messageTimer=setTimeout(TM.cleanMsg,3e3)},error:function(a,b,c){TM.showMsg(0,"Error timeout"),TM.messageTimer=setTimeout(TM.cleanMsg,3e3)}};TM.ajax(c)},cancelInput:function(a,b){0===b?$("#"+a).html(this.saveSettingInput):(this.saveSettingInput="",$("#"+a).html(b)),setTimeout(function(){TM.editSettingProgress=0,TM.saveSettingId=""},500)},addInput:function(a,b){if(1==this.editSettingProgress)return this.saveSettingId!=a&&alert("Please cancel the previous action"),!1;if(this.saveSettingInput=$("#"+a).html(),this.saveSettingId=a,0===b)html='<input id="input_setting_text" class="settings_input" type="text" value="'+this.saveSettingInput+'" /> ',html+='<span class="recycler" onclick="TM.cancelInput(\''+a+"', 0);\">",html+="</span>",$("#"+a).html(html);else{for(html='<select id="input_setting_select" class="chosen">',i=1;i<=4;i++)html+='<option value="'+i+'" '+(TM.saveSettingInput==i?"selected":null)+">"+i+"</option>";html+="</select> ",html+='<span class="recycler for_select" onclick="TM.cancelInput(\''+a+"', 0);\">",html+="</span>",$("#"+a).html(html)}$("#input_setting_text").keypress(function(b){if(13==b.which){TM.showMsg(2,strings.loading);var c={type:"POST",timeout:1e4,data:{control:"saveSetting",param:a,value:$("#input_setting_text").val()},success:function(b){answer=b.split(";"),TM.cleanMsg(),TM.showMsg(answer[0],answer[1]),TM.messageTimer=setTimeout(TM.cleanMsg,3e3),TM.cancelInput(a,$("#input_setting_text").val())},error:function(a,b,c){TM.showMsg(0,"Error timeout"),TM.messageTimer=setTimeout(TM.cleanMsg,3e3)}};TM.ajax(c)}}),$("#input_setting_select").change(function(b){TM.showMsg(2,strings.loading);var c={type:"POST",timeout:1e4,data:{control:"saveSetting",param:a,value:$("#input_setting_select").val()},success:function(b){answer=b.split(";"),TM.cleanMsg(),TM.showMsg(answer[0],answer[1]),TM.messageTimer=setTimeout(TM.cleanMsg,3e3),TM.cancelInput(a,$("#input_setting_select").val())},error:function(a,b,c){TM.showMsg(0,"Error timeout"),TM.messageTimer=setTimeout(TM.cleanMsg,3e3)}};TM.ajax(c)}),1==b&&$(".chosen").chosen({disable_search_threshold:10,no_results_text:"Oops, nothing found!"}),this.editSettingProgress=1},cleanMsg:function(){clearInterval(this.messageTimer),$("#asucmsg").slideUp(),$("#aerrmsg").slideUp(),$("#amsg").slideUp()},showMsg:function(a,b){$("#asucmsg, #aerrmsg, #amsg").stop().hide(),$("#asucmsg, #aerrmsg, #amsg").html(""),a=parseInt(a);var c=$("body")[0].clientWidth;1===a?($("#asucmsg").html(b),$("#asucmsg").css("left",c/2-$("#asucmsg").width()/2+"px"),$("#asucmsg").stop().slideDown()):0===a?($("#aerrmsg").html(b),$("#aerrmsg").css("left",c/2-$("#aerrmsg").width()/2+"px"),$("#aerrmsg").stop().slideDown()):($("#amsg").html(b),$("#amsg").css("left",c/2-$("#amsg").width()/2+"px"),$("#amsg").stop().slideDown())},runSessionTimeout:function(){clearInterval(this.sessionTimeout),this.sessionTimeout=setInterval(function(){go(TM.site+"/admin/#aexit")},18e5)},deletion:function(a){return confirm(strings.sure_to_delete)&&(location.href=a),!1},showPage:function(a){if(TM.loadImg(),window.location.hash!=a)return window.location.hash=a,!1;$("textarea").each(function(){tinymce.EditorManager.execCommand("mceRemoveEditor",!1,$(this).attr("id"))}),param=a.split("/"),activeLink=param[0].substr(1),dataParams={control:"showPage"},$.each(param,function(a,b){0===a?dataParams.page=b:dataParams["var"+a]=b});var b={type:"POST",timeout:1e4,data:dataParams,success:function(a){"#aexit"==param[0]&&go(TM.site+"/admin"),TM.blockHint=0,$("#loading").fadeOut("fast"),$("nav").find("a").removeClass("active"),$(".sublinks_menu").find("a").removeClass("active"),$("#link_"+activeLink).addClass("active"),$(".content").html(a),$(".content textarea:not(.noEditor)").each(function(){tinymce.EditorManager.execCommand("mceAddEditor",!1,$(this).attr("id"))}),$(".chosen").chosen({disable_search_threshold:10,no_results_text:"Oops, nothing found!"})},error:function(a,b,c){$("#loading").fadeOut("fast"),TM.cleanMsg(),TM.showMsg(2,"Error timeout"),setTimeout(TM.cleanMsg,3e3)}};TM.ajax(b)},goDelay:function(a,b){a||(a=""),a=this.site+"/admin/"+a,setTimeout(function(){window.location=a},b)},updateCMS:function(){if(!confirm("WARNING: Be sure to backup your files and database before doing any update, it will fully overwrite existing files!"))return!1;if(1==this.formInProgress)return!1;TM.showMsg(2,"Initializing"),this.formInProgress=1;var a={type:"POST",timeout:12e4,data:{control:"updateCMS"},success:function(a){console.log(a),answer=a.split(";"),TM.cleanMsg(),TM.showMsg(answer[0],answer[1]),TM.formInProgress=0,TM.messageTimer=setTimeout(TM.cleanMsg,15e3),1==answer[0]&&TM.goDelay("",15e3)},error:function(a,b,c){TM.formInProgress=0,TM.showMsg(0,"Error timeout"),TM.messageTimer=setTimeout(TM.cleanMsg,3e3)}};TM.ajax(a)},checkCustomAccess:function(){0===$("#level").val()?$(".customAccess").show():$(".customAccess").hide()},loadImg:function(){$("#loading").hide(),w=$(".content").width()+55,h=$(".content").height()+34,offset=$(".content").offset(),loadingImage=$("#loading").find("img"),$("#loading").width(w),$("#loading").height(h),$("#loading").fadeTo(0,.7),h<10?loadingImage.offset({top:$("body").height()/2+loadingImage.height()/2,left:w/2-loadingImage.width()/2}):loadingImage.offset({top:h/2+loadingImage.height()/2,left:w/2-loadingImage.width()/2})},go:function(a){a?document.location=a:location.reload(!0)},fadeScr:function(){$("#fader").fadeTo(0,.5),$("#fader").height($(document).height())},ajax:function(a){return a.url||(a.url=this.site),a.async||(a.async=!0),a.dataType||(a.dataType=""),a.success||(a.success=function(a){alert(a)}),a.data||(a.data={}),a.type||(a.type="GET"),a.xhrFields||(a.xhrFields={withCredentials:!0}),a.crossDomain||(a.crossDomain=!0),a.cache||(a.cache=!0),a.timeout||(a.timeout=6e4),a.error||(a.error=function(b,c,d){console.log(a.url),console.log(b),console.log(c),console.log(d)}),a.data.language=this.lang,$.ajax({url:a.url,type:a.type,async:a.async,data:a.data,dataType:a.dataType,xhrFields:a.xhrFields,crossDomain:a.crossDomain,cache:a.cache,timeout:a.timeout,success:a.success,error:a.error})}};tinymce.init({selector:"textarea:not(.noEditor)",plugins:"advlist autolink link image lists charmap print preview code "+(allowUpload?"jbimages":null),toolbar:"code undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image "+(allowUpload?"jbimages":null),width:"99%",height:300,forced_root_block:!1,resize:!0,relative_urls:!1,document_base_url:TM.site+"/web/",remove_script_host:!1,external_plugins:{jbimages:TM.site+"/cms/plugins/tinymce-jbimages/plugin.min.js"}});