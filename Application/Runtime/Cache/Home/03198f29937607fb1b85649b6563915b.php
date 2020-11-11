<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="/Public/js/jquery-1.7.1.min.js"></script>
<script src="/Public/js/uploadify/jquery.uploadify.min.js"></script>
<link href="/Public/js/uploadify/uploadify.css" type="text/css" rel="stylesheet" />

<script>

$(function() {
  $('#file_upload').uploadify({
      'auto'     : true ,//关闭自动上传
      'removeTimeout' : 1,//文件队列上传完成1秒后删除
      'swf'      : '/Public/js/uploadify/uploadify.swf',
      'uploader' : '/home/InputData/saveExcel',
      'method'   : 'post',//方法，服务端可以用$_POST数组获取数据
      'buttonText' : "<?php echo L('L_CHOOSE_FILE');?>",//设置按钮文本
      'multi'    : false,//允许同时上传
      'uploadLimit' : 10,//一次最多只允许上传1
      'queueID'  : 'queue', //默认队列ID
    //  'fileTypeDesc' : 'Image Files',//只允许上传图像14
      'fileTypeExts' : '*.xls;*.xlsx;',//限制允许上传的图片后缀
      'onUploadSuccess' : function(file, data, response) {
       alert(data);
        //每次成功上传后执行的回调函数，从服务端返回数据到前端
    /*  if(data==1){
      alert("<?php echo L("L_ALERT_SUCCESS");?>");
       }else{
         alert("<?php echo L("L_ALERT_FAIL");?>");
       }*/

            },
      'onQueueComplete' : function(queueData) {
        //上传队列全部完成后执行的回调函数
         // alert('成功上传的文件有：'+encodeURIComponent(img_id_upload));
      }

  });
});
</script>
<title>无标题文档</title>
</head>
<body>
  <h3><?php echo L('L_IMPORT_FILE');?></h3>
  <p id="queue"></p>
<input  id='file_upload' type="file"name='file_upload'/>


</body>
</html>