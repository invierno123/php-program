<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head></head>
<title>编辑</title>
<link rel="stylesheet" href="/Public/css/innit.css" type="text/css" media="screen" />
<script language="javascript" type="text/javascript" src="/Public/js/My97DatePicker/WdatePicker.js"></script>
<script src="/Public/js/jquery.min.js" type="text/javascript"> </script>
<script  type="text/javascript">
function trim(str){ //删除左右两端的空格
return str.replace(/(^\s*)|(\s*$)/g, "");
}
function ltrim(str){ //删除左边的空格
return str.replace(/(^\s*)/g,"");
}
function rtrim(str){ //删除右边的空格
return str.replace(/(\s*$)/g,"");
}
function back_supervisory(){
location="/home/supervisory/bill?page="+GetQueryString("page");
}
function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}
$(function(){
      $("#edit").click(function(){

        var outtype=$("#outtype").val();
      var linktype=$("#linktype").val();

     
      $.post("/home/supervisory/update",
      {
        id:$("#id").val(),
    
      spressure:$("#spressure").val(),
      area:$("#area").val(),
      linesize:$("#linesize").val(),
      traptype:$("#traptype").val(),
      outtype:$("#outtype").val(),

      modelname:$("#modelname").val(),
      linktype:$("#linktype").val(),
      location:$("#location").val(),
      usemtfi:$("#usemtfi").val(),



      },
      function(res){
        if(res=="ok"){
        alert("<?php echo L("L_ALERT_SUCCESS");?>");
        location="/home/supervisory/bill";
        } else{
          alert("<?php echo L("L_ALERT_FAIL");?>");
        }
      });
     });
});
</script>

<body>
  <div class="all">
    <input id="id" type="hidden" name="id"  value="<?php echo ($trapdata['id']); ?>">
    <div class="left">
      <p><span><?php echo L('L_AREA_QY');?>:</span><input id="area" name="area"  type="text" value="<?php echo ($trapdata['area']); ?>"></p>
      <p><span><?php echo L('L_AREA_inpressure');?>:</span><input id="spressure" name="spressure"  type="text" value="<?php echo ($trapdata['spressure']); ?>"></p>
      <p><span><?php echo L('L_AREA_LINkS');?>:</span><input id="linesize" name="linesize"  type="linesize" value="<?php echo ($trapdata['linesize']); ?>"></p>
      <p><span><?php echo L('L_AREA_FMLX');?>:</span><input id="traptype" name="traptype"  type="text" value="<?php echo ($trapdata['traptype']); ?>"></p>
      
      <p><span><?php echo L('L_AREA_outtype');?>:</span>

        <select id="outtype">
           <option value="1"><?php echo L('L_AREA_open');?></option>
           <option value="0"><?php echo L('L_AREA_down');?></option>
        </select>
        </p>
      <!-- <p><span><?php echo L('L_AREA_outtype');?>:</span><input id="outtype" name="outtype"  type="text" value="<?php echo ($trapdata['outtype']); ?>"></p> -->

    </div>
    <div class="right">
      <p><span><?php echo L('L_ALERT_TJ_num');?>:</span><input id="modelname" name="modelname"  type="text" value="<?php echo ($trapdata['modelname']); ?>"></p>
      <p><span><?php echo L('L_AREA_LINkT');?>:</span>
        <select id="linktype">
           <option value="1"><?php echo L('L_AREA_FLANGE');?></option>
           <option value="0"><?php echo L('L_AREA_SFO');?></option>
        </select>
        </p>
      <!-- <p><span><?php echo L('L_AREA_LINkT');?>:</span><input id="linktype" name="linktype"  type="text" value="<?php echo ($trapdata['linktype']); ?>"></p> -->
      <p><span><?php echo L('L_ALERT_TJ_location');?>:</span><input id="location" name="location"  type="text" value="<?php echo ($trapdata['location']); ?>"></p>
      
      <p><span> <?php echo L('L_AREA_PINPAI');?>:</span><input id="usemtfi" name="usemtfi"  type="text" value="<?php echo ($trapdata['usemtfi']); ?>"> 
      </p>
    </div>
      <div class="button">
    <input id="btn_cencel" class="btn" type="submit" value ="<?php echo L('L_ALERT_CANCEL');?>" onclick="back_supervisory()" />
    <input id="edit" type="submit" value ="<?php echo L('L_ALERT_SUBMIT');?>" />
     </div>
      </div>
  </div>

    
</body>
</html>