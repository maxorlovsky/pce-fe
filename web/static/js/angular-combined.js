var app;!function(){"use strict";app=angular.module("pcesports",["ngResource"])}(),app.factory("notification",function(){"use strict";return{error:function(a){return a.data.message},form:function(a){var b="";return angular.forEach(a.data,function(a,c){"status"!=c&&(b+=a+"\n")}),b}}}),app.factory("query",["$resource",function(a){return a("prod"==g.env?g.siteSecure:g.site,{},{save:{method:"POST",params:{}}})}]),app.controller("Login",["$scope","query","notification",function(a,b,c){a.errorLogin="",a.errorRegistration="",a.successRegistration="",a.buttonLogin="",a.buttonRegistration="",a.login=function(){return!a.buttonLogin&&a.emailLogin&&a.passwordLogin?(a.errorLogin="",a.buttonLogin="alpha",void b.save({ajax:"login",email:a.emailLogin,password:a.passwordLogin},function(a){location.reload()},function(b){a.buttonLogin="",a.errorLogin=c.form(b)})):!1},a.register=function(){return!a.buttonRegistration&&a.emailRegistration&&a.passwordRegistration?(a.errorRegistration="",a.buttonRegistration="alpha",void b.save({ajax:"register",email:a.emailRegistration,password:a.passwordRegistration,captcha:jQuery("#g-recaptcha-response").val()},function(b){a.emailRegistration="",a.passwordRegistration="",a.buttonRegistration="",a.successRegistration=b.message},function(b){a.buttonRegistration="",a.errorRegistration=c.form(b)})):!1},a.showRegistration=function(){jQuery('#login-window .form[name="loginForm"]').slideUp("fast"),jQuery('#login-window .form[name="registrationForm"]').slideDown("fast")},a.showRestore=function(){jQuery('#login-window .form[name="loginForm"]').slideUp("fast"),jQuery('#login-window .form[name="restoreForm"]').slideDown("fast")},a.backStep=function(){jQuery('#login-window .form[name="loginForm"]').slideDown("fast"),jQuery('#login-window .form[name="registrationForm"]').slideUp("fast"),jQuery('#login-window .form[name="restoreForm"]').slideUp("fast")}}]),app.controller("Team",["$scope","query","notification",function(a,b,c){a.error="",a.button="",a.addTeam=function(){return a.button?!1:(a.error="",a.button="alpha",void b.save({ajax:"addTeam",form:$("form").serialize()},function(a){window.location.href=a.url},function(b){a.button="",a.error=c.form(b)}))}}]);