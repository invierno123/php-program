<?php if (!defined('THINK_PATH')) exit();?>﻿<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title></title>
  <style type="text/css">
img.x
{
position:absolute;
left:0px;
top:0px;
z-index:-1;
width:100%;
}
</style>
</head>
<body style="overflow:hidden;">
   <img class="x" src="/Public/images/bg_main.png" />
  <script>
    function getCookie(name)
    {
      var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
      if(arr=document.cookie.match(reg))
          return unescape(arr[2]);
        else
          return "zh-cn";
    }
    var Lan=getCookie("think_language");
    var iframe = document.createElement('iframe');
    iframe.src = "http://www.thinkpage.cn/weather/weather.aspx?uid=&cid=&l="+Lan+"&p=SMART&a=1&u=C&s=3&m=0&x=2&d=3&fc=&bgc=&bc=&ti=2&in=0&li=0&ct=iframe";
    iframe.setAttribute("style","float:right;width:450;height:260;");
    iframe.scrooling="no";
    iframe.frameBorder="0";
    iframe.id="iframe_wea";
    iframe.name="iframe_wea";
    //document.body.appendChild(iframe);
  </script>
</body>
</html>