<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head></head>
<title>误报添加</title>
<script src="/Public/js/jquery.min.js" type="text/javascript"> </script>
  <link href="/Public/css/warningerror.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    function trim(str){ //删除左右两端的空格
    return str.replace(/(^\s*)|(\s*$)/g,"");
    }
    function ltrim(str){ //删除左边的空格
    return str.replace(/(^\s*)/g,"");
    }
    function rtrim(str){ //删除右边的空格
    return str.replace(/(\s*$)/g,"");
    }
    function back_warning(){
      location="/home/warning/index?page="+GetQueryString("page");
      }
      function GetQueryString(name)
      {
           var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
           var r = window.location.search.substr(1).match(reg);
           if(r!=null)return  unescape(r[2]); return null;
      }
$(function(){
  $("#tianjia").click(function(){
      var re=  /^[+-]?\d+\.?\d*$/;//验证数字
if(trim($("#tpvalue").val())==""){
  alert("<?php echo L('L_ALERT_PRESSURE_NOT_EMPTY');?>");
  return;
}
if(!re.test(trim($("#tpvalue").val()))){
alert("<?php echo L('L_ALERT_PRESSURE_INPUT_ERRORS');?>");
return;
}
if(trim($("#mintem").val())==""){
  alert("<?php echo L('L_ALERT_MINTEM_NOT_EMPTY');?>");
    return;
}
if(!re.test(trim($("#mintem").val()))){
alert("<?php echo L('L_ALERT_MINTEM_INPUT_ERRORS');?>");
return;
}
if(trim($("#maxtem").val())==""){
  alert("<?php echo L('L_ALERT_MAXTEM_NOT_EMPTY');?>");
    return;
}
if(!re.test(trim($("#maxtem").val()))){
alert("<?php echo L('L_ALERT_MAXTEM_INPUT_ERRORS');?>");
return;
}

 var  alerttype=$("#alerttype").val();
 if(alerttype=="1"){
   alerttype=1;
 }else if(alerttype=="3"){
   alerttype=3;
 }else if(alerttype=="5"){
   alerttype=5;
 }
 var  fluidtype=$("#fluidtype").val();
 if(fluidtype=="1"){
  fluidtype="<?php echo L('L_ALERT_WATER');?>";

 }else if(fluidtype=="3"){
  fluidtype="<?php echo L('L_ALERT_GAS');?>";
}else if(fluidtype=="4"){
   fluidtype="<?php echo L('L_ALERT_HZABNORMAL');?>";
 }

  $.post("/home/warning/adderror",{
   trapno:$("#trapno").text(),
   alerttem:$("#alerttem").text(),
   alerthz:$("#alerttem").text(),
   standardtem:$("#standardtem").text(),
   alerttype:alerttype,
   fluidtype:fluidtype,
   tpvalue:$("#tpvalue").val(),
   mintem:$("#mintem").val(),
   maxtem:$("#maxtem").val()

 },function(res){
    if(res=="ok"){
      alert("<?php echo L("L_ALERT_SUCCESS");?>");
      location="/home/warning/index";
    }else{
      alert("<?php echo L("L_ALERT_FAIL");?>");
    }
    });
  });
})

</script>
 <body class="div_all">
   <div>
  <div class="div_left">
    <p><span><?php echo L('L_ALERT_TRAPNO');?>:</span><label id="trapno"><?php echo ($list['trapno']); ?></label></p>
    <p><span><?php echo L('L_ALERT_TEM');?>:</span><label id="alerttem"><?php echo ($list['alerttem']); ?></label></p>
    <p><span><?php echo L('L_ALERT_HZ');?>:</span><label id="alerthz"><?php echo ($list['alerthz']); ?></label></p>
    <p><span><?php echo L('L_ALERT_STANDARD');?>:</span><label id="standardtem"><?php echo ($list['standardtem']); ?></label></p>
    <p><span><?php echo L('L_ALERT_TYPE');?>:</span>
      <select id="alerttype">
      <option value="1"><?php echo L('L_ALERT_LOWTEM');?></option>
      <option value="3"><?php echo L('L_ALERT_HIGHTEM');?></option>
      <option value="5"><?php echo L('L_ALERT_HZABNORMAL');?></option>
      </select>
    </p>
  </div>
  <div class="div_right">

   <p><span><?php echo L('L_ALERT_FLUIDTYPE');?>:</span>
     <select id="fluidtype">
      <option value="1"><?php echo L('L_ALERT_WATER');?></option>
      <option value="3"><?php echo L('L_ALERT_GAS');?></option>
      <option value="4"><?php echo L('L_ALERT_WATERANDGAS');?></option>
     </select>
   </p>
  <p><span><?php echo L('L_ALERT_PRESSURE');?>:</span><input type="text" id="tpvalue"></p>
  <p><span><?php echo L('L_ALERT_MINTEM');?>:</span><input type="text" id="mintem"></p>
  <p><span><?php echo L('L_ALERT_MAXTEM');?>:</span><input type="text" id="maxtem"></p>
  <input id="btn_cencel" class="btn" type="submit" value ="<?php echo L('L_ALERT_CANCEL');?>" onclick="back_warning()" />
  <input type="button"  id="tianjia" value="<?php echo L('L_ALERT_OK');?>" />
  </div>
</div>
 </body>
</html>