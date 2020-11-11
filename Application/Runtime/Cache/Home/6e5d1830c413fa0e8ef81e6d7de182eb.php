<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//Dinput HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head></head>
<title>添加报警管理信息</title>
<link rel="stylesheet" href="/Public/css/warningadd.css" type="text/css" media="screen" />
<script language="javascript" type="text/javascript" src="/Public/js/My97DatePicker/WdatePicker.js"></script>
<script src="/Public/js/jquery.min.js" type="text/javascript"> </script>
<script type="text/javascript">
function trim(str){ //删除左右两端的空格
return str.replace(/(^\s*)|(\s*$)/g, "");
}
function ltrim(str){ //删除左边的空格
return str.replace(/(^\s*)/g,"");
}
function rtrim(str){ //删除右边的空格
return str.replace(/(\s*$)/g,"");
}
  $(function(){
     $("#add").click(function(){
        var number=  /^[0-9]*[1-9][0-9]*$/;//正整数

        if(trim($("#trapno").val())==""){
          alert("<?php echo L('L_ALERT_INPUT_TRAPNO');?>");
          return;
        }
        if(trim($("#location").val())==""){
          alert("<?php echo L('L_ALERT_INPUT_LOCATION');?>");
          return;
        }

       var DATE_FORMAT = /^[0-9]{4}-[0-1]?[0-9]{1}-[0-3]?[0-9]{1}$/;
       var date =trim($("#repairtime").val());
       if(!DATE_FORMAT.test(date)){
          alert("<?php echo L('L_ALERT_TIME_INPUT_ERRORS');?>");
          return;
         }
         if(!number.test(trim($("#repairnum").val()))){
         alert("<?php echo L('L_ALERT_REPAIRNUM_INPUT_ERRORS');?>");
         return;
         }

         var re =/^\d+(?=\.{0,1}\d+$|$)/;
         if(!re.test(trim($("#repairprice").val()))){
         alert("<?php echo L('L_ALERT_REPAIRPRICE_INPUT_ERRORS');?>");
         return;
         }
          if(trim($("#exlevel").val())==""){
            alert("<?php echo L('L_ALERT_INPUT_EXLEVEL');?>");
            return;
          }
          if(!number.test(trim($("#leveldesc").val()))){
          alert("<?php echo L('L_ALERT_LEVELDESC_INPUT_ERRORS');?>");
          return;
          }
            var re=  /^[+-]?\d+\.?\d*$/;//验证数字
            if(trim($("#alerttem").val())==""){
              alert("<?php echo L('L_ALERT_ALERTTEM_NOT_EMPTY');?>");
              return;
            }
            if(!re.test(trim($("#alerttem").val()))){
            alert("<?php echo L('L_ALERT_ALERTTEM_INPUT_ERRORS');?>");
            return;
            }
            if(trim($("#alerthz").val())==""){
              alert("<?php echo L('L_ALERT_ALERTHZ_NOT_EMPTY');?>");
              return;
            }
            if(!re.test(trim($("#alerthz").val()))){
            alert("<?php echo L('L_ALERT_ALERTHZ_INPUT_ERRORS');?>");
            return;
            }
            if(trim($("#standardtem").val())==""){
              alert("<?php echo L('L_ALERT_STANDARDTEM_NOT_EMPTY');?>");
              return;
            }
            if(!re.test(trim($("#standardtem").val()))){
            alert("<?php echo L('L_ALERT_STANDARDTEM_INPUT_ERRORS');?>");
            return;
            }
     $.post("/home/warning/addMessage",
     {
      id:$("#id").val(),
      areaid:$("#AreaId").val(),
      area:$("#AreaId option:selected").text(),
      trapno:$("#trapno").val(),
      location:$("#location").val(),
      repairstate:$("#repairstate").val()=="1"?1:0,
      repairtype:$("#repairtype").val()=="1"?1:0,
      repairtime:$("#repairtime").val(),
      repairnum:$("#repairnum").val(),
      repairprice:$("#repairprice").val(),
      trapstate:$("#trapstate").val()=="0"?0:1,
      exlevel:$("#exlevel").val(),
      leveldesc:$("#leveldesc").val(),
      alerttem:$("#alerttem").val(),
      alerthz:$("#alerthz").val(),
      standardtem:$("#standardtem").val(),
      repairdescription:$("#repairdescription").val(),

    },function(res){
      if(res=="ok"){
      alert("<?php echo L("L_ALERT_SUCCESS");?>");
      location="/home/warning/index?page="+GetQueryString("page");
      } else{
        alert("<?php echo L("L_ALERT_FAIL");?>");
      }
    });
     });
  });
  function btn_backwarning(){
    location="/home/warning/index?page="+GetQueryString("page");
  }
  function GetQueryString(name)
   {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
   }
   $(function(){
       $.get("/home/warning/getareas",function(res){
         var area_array_=res.split('/');
         for(var i=0;i<area_array_.length;i++){
           if(area_array_[i]==""){break;}
           $("#AreaId").append("<option value='"+area_array_[i].split(",")[0]+"'>"+area_array_[i].split(",")[1]+"</option>");
         }
       });
     });
</script>
<body>
<div class="all">
  <div class="left">

  <p><span><?php echo L('L_ALERT_AREA');?>:</span><select id="AreaId" class="input_select"></select></p>
  <p><span><?php echo L('L_ALERT_TRAPNO');?>:</span><input type="text"  id="trapno"></p>
  <p><span><?php echo L('L_ALERT_LOCATION');?>:</span><input type="text" id="location"></p>
    <p><span><?php echo L('L_ALERT_REPAIRTIME');?>:</span><input  type="text" id="repairtime" onclick="WdatePicker()"></p>
  <p><span><?php echo L('L_ALERT_REPAIRSTATE');?>:</span>
    <select id="repairstate">
       <option value="1"><?php echo L('L_ALERT_HANDLED');?></option>
       <option value="0"><?php echo L('L_ALERT_HANDLING');?></option>
    </select>
  </p>

  <p><span><?php echo L('L_ALERT_REPAIRTYPE');?>:</span>
      <select id="repairtype">
       <option value="1"><?php echo L('L_ALERT_REPAIR');?></option>
       <option value="0"><?php echo L('L_ALERT_REPLACE');?></option>
    </select></p>

    <p><span><?php echo L('L_ALERT_TRAPSTATE');?>:</span>

        <select id="trapstate">
           <option value="0"><?php echo L('L_ALERT_TJ_WORK');?></option>
           <option value="1"><?php echo L('L_ALERT_TJ_EXC');?></option>
        </select>
      </p>
  </div>
<div class="right">
<p><span><?php echo L('L_ALERT_REPAIRNUM');?>:</span><input   type="text" id="repairnum"></p>
<p><span><?php echo L('L_ALERT_REPAIRPRICE');?>:</span><input  type="text" id="repairprice" ></p>


<p><span><?php echo L('L_ALERT_EXLEVEL');?>:</span><input  type="text" id="exlevel"></p>
<p><span><?php echo L('L_ALERT_LEVELDESC');?>:</span><input  type="text" id="leveldesc"></p>


<p><span><?php echo L('L_ALERT_TEM');?>:</span><input  type="text" id="alerttem"></p>
<p><span><?php echo L('L_ALERT_HZ');?>:</span><input  type="text" id="alerthz"></p>
<!-- 温度判断标准 -->
<p><span><?php echo L('L_ALERT_STANDARD');?>:</span><input  type="text" id="standardtem"></p>
<!-- 处理 -->
<p><span><?php echo L('L_ALERT_REPAIRDESCRIPTION');?>:</span><textarea   id="repairdescription"></textarea></p>
<input type="button" value="<?php echo L('L_ALERT_CANCEL');?>" id="btn_cencel" onclick="btn_backwarning()" />
<input type="button" value="<?php echo L('L_ALERT_ADD');?>" id="add" />
</div>
<div>
</body>
</html>