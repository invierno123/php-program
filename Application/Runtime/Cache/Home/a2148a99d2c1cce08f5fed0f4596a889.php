<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>信息设置</title>
    <script src="/Public/js/jquery-1.7.1.min.js" type="text/javascript"></script>
</head>
<body>
    <p>
        采集时间:<input id="input_cjsj" /><font style="color: Gray;"> (当前采集间隔为:<label id="lab_cjsj"></label>分钟)</font>
    </p>
    <p>
        物理地址:<input id="input_fmbh" onchange="getleakbase()" />
    </p>
    <p>
        调整基数:<input id="input_tzjs" /><font style="color: Gray;"> (当前基数为:<label id="lab_leakbase"></label>)</font>
    </p>
    <p>
        <a onclick="setfun()" style="color: blue; cursor: pointer;">设置</a>
    </p>
    <script>
        function setfun() {
            var cjsj = $("#input_cjsj").val();
            var wldz = $("#input_fmbh").val();
            var tzjs = $("#input_tzjs").val();

            $.get("setfun?cj=" + cjsj + "&dz=" + wldz + "&js=" + tzjs, function (data) {
                alert("更新完成。");
                location = "?dz=" + wldz;
            });

        }
        function getleakbase() {
            var wldz = $("#input_fmbh").val();
            $.get("getleakbase?dz=" + wldz + "", function (data) {
                $("#lab_leakbase").text(data.split(',')[0]);
                $("#lab_cjsj").text(data.split(',')[1]);
            });
        }
        $(function () {
            $("#input_fmbh").val(getQueryString("dz"));
            getleakbase()
        });
        function getQueryString(name) {
            var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
            var r = window.location.search.substr(1).match(reg);
            if (r != null) {
                return unescape(r[2]);
            }
            return "";
        }
    </script>
</body>
</html>