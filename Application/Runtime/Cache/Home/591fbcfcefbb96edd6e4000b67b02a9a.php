<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>设置初年数据</title>
<link rel="stylesheet" href="/Public/css/emc.css" type="text/css" media="screen" />
<link rel="stylesheet" href="/Public/js/artDialog-5.0.3/skins/simple.css" type="text/css" media="screen" />
<script src="/Public/js/jquery.min.js" type="text/javascript"> </script>
<script src="/Public/js/artDialog-5.0.3/artDialog.min.js" type="text/javascript"> </script>
<script>


</script>
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
  //$("#yproduction").attr("checked","checked");
  //  $("#yexpend").attr("checked","checked");
    $("#mproduction").click(function(){
      $("#yearproduction").hide(300);
      $("#tab_yearp input").val("");

      $("#monthproduction").show(300);
      $("#monthExpend").show(300);
      $("#tab_year input").val("");
      $("#yearExpend").hide(300);
      $("#mexpend").attr("checked","checked");
    });
    $("#yproduction").click(function(){
      $("#yearproduction").show(300);
      $("#tab_monthp input").val("");
      $("#monthproduction").hide(300);
      $("#yearExpend").show(300);
      $("#tab_month input").val("");
      $("#monthExpend").hide(300);
      $("#yexpend").attr("checked","checked");
    });
    $("#mexpend").click(function(){
      $("#yearExpend").hide(300);
      $("#tab_year input").val("");
      $("#monthExpend").show(300);
      $("#yearproduction").hide(300);
      $("#tab_yearp input").val("");
      $("#monthproduction").show(300);
     $("#mproduction").attr("checked","checked");
    });
    $("#yexpend").click(function(){
      $("#yearExpend").show(300);
      $("#tab_month input").val("");
      $("#monthExpend").hide(300);
      $("#yearproduction").show(300);
      $("#tab_monthp input").val("");
      $("#monthproduction").hide(300);
      $("#yproduction").attr("checked","checked");
    });

//当年表格
    $("#input_yproduction").click(function(){
    $("#thisyearProduction").show(300);
    $("#table_monthp input").val("");
    $("#table_month input").val("");
    $("#thismonthProduction").hide(300);
    $("#input_yexpend").attr("checked","checked");
    $("#thisyearExpend").show(300);
    $("#thismonthExpend").hide(300);
    });
    $("#input_mproduction").click(function(){
    $("#thisyearProduction").hide(300);
    $("#table_yearp input").val("");
    $("#table_year input").val("");
    $("#thismonthProduction").show(300);
    $("#input_mexpend").attr("checked","checked");
    $("#thismonthExpend").show(300);
    $("#thisyearExpend").hide(300);
    });
    $("#input_mexpend").click(function(){
      $("#thismonthExpend").show(300);
      $("#table_yearp input").val("");
      $("#table_year input").val("");
      $("#thisyearExpend").hide(300);
      $("#thisyearProduction").hide(300);
      $("#thismonthProduction").show(300);
      $("#input_mproduction").attr("checked","checked");
    });
    $("#input_yexpend").click(function(){
      $("#thisyearExpend").show(300);
      $("#table_monthp input").val("");
      $("#table_month input").val("");
      $("#thismonthExpend").hide(300);
      $("#thisyearProduction").show(300);
      $("#thismonthProduction").hide(300);
      $("#input_yproduction").attr("checked","checked");
    });

    var dataId='<?php echo ($data["id"]); ?>';
    if(dataId!="" && parseInt(dataId)>0){

//当年不为空时
      var data_yearp='<?php echo ($data["fristproductiony"]); ?>';
      if(data_yearp!=""){
        var data_yearp_array=data_yearp.split('/');
        var array_lenth=data_yearp_array.length;
        for (var i = 0; i < array_lenth; i++) {
          $("#input_yearp_"+(i+1)).val(data_yearp_array[i]);
          if($("#input_yearp_"+(i+1)).val()==""){
          //$("#input_year_"+(i+1)).attr("readonly","readonly");
          //$("#input_year_"+(i+1)).attr("disabled",true);
          $("#input_tyearp_"+(i+1)).attr("readonly","readonly");
          $("#input_tyearp_"+(i+1)).attr("disabled",true);

          }

        }
        $("#monthproduction").hide();
        $("#yearproduction").show();
        $("#yearExpend").show();
        $("#monthExpend").hide();
        $("#yexpend").attr("checked","checked");
        $("#input_yexpend").attr("checked","checked");
        $("#input_yproduction").attr("checked","checked");
        $("#yproduction").attr("checked","checked");
        $("#thisyearExpend").show();
        $("#thismonthExpend").hide();
        $("#thisyearProduction").show();
        $("#thismonthProduction").hide();
      }

      var data_year='<?php echo ($data["fristexpendy"]); ?>';
      if(data_year!=""){
        var data_year_array=data_year.split('/');
        var array_lenth=data_year_array.length;
        for (var i = 0; i < array_lenth; i++) {
          $("#input_year_"+(i+1)).val(data_year_array[i]);
          if($("#input_year_"+(i+1)).val()==""){
          $("#input_tyear_"+(i+1)).attr("readonly","readonly");
          $("#input_tyear_"+(i+1)).attr("disabled",true);
          }
        }
        $("#monthproduction").hide();
        $("#yearproduction").show();
        $("#yearExpend").show();
        $("#monthExpend").hide();
        $("#yproduction").attr("checked","checked");
        $("#thisyearExpend").show();
        $("#thismonthExpend").hide();
        $("#thisyearProduction").show();
        $("#thismonthProduction").hide();
      }

//当月不为空时
      var data_monthp='<?php echo ($data["fristproductionm"]); ?>';
      if(data_monthp!=""){
        var data_monthp_array=data_monthp.split('/');
        var array_lenth=data_monthp_array.length;
        for (var i = 0; i < array_lenth; i++) {
          $("#input_monthp_"+(i+1)).val(data_monthp_array[i]);
        }
        $("#monthproduction").show();
        $("#yearproduction").hide();
        $("#yearExpend").hide();
        $("#monthExpend").show();
        $("#mproduction").attr("checked","checked");
        $("#thisyearExpend").hide();
        $("#thismonthExpend").show();
        $("#thisyearProduction").hide();
        $("#thismonthProduction").show();
      }
      var data_month='<?php echo ($data["fristexpendm"]); ?>';
      if(data_month!=""){
      var data_month_array=data_month.split('/');
      var array_len=data_month_array.length;
        for (var i = 0; i < array_len; i++) {
          $("#input_month_"+(i+1)).val(data_month_array[i]);
        }
        $("#monthproduction").show();
        $("#yearproduction").hide();
        $("#yearExpend").hide();
        $("#monthExpend").show();
        $("#mexpend").attr("checked","checked");
        $("#input_mexpend").attr("checked","checked");
        $("#input_mproduction").attr("checked","checked");
        $("#thisyearExpend").hide();
        $("#thismonthExpend").show();
        $("#thisyearProduction").hide();
        $("#thismonthProduction").show();
      }
      $("#div_input input").attr("readonly","readonly");
      $("#currency").attr("disabled","disabled");
      $("#sub").hide();
  }
});
//判断输入的内容格式问题
$(function(){
  $("#sub").click(function(){
     var re =/^\d+(?=\.{0,1}\d+$|$)/;//验证正数
  if(trim($("#powerUnity").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_UNIT_NOT_EMPTY');?>");
  return ;
  }
  if(trim($("#powerName").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_NAME_NOT_EMPTY');?>");
  return ;
  }
  if(trim($("#powerNumber").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_USE_NOT_EMPTY');?>");
  return ;
  }
  if(!re.test(trim($("#powerNumber").val()))){
  art.dialog("<?php echo L('L_EMC_ENERGY_USE_INPUT_ERRORS');?>")
   return;
  }
  if(trim($("#powerPrice").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_PRICE_NOT_EMPTY');?>");
  return ;
  }
  if(!re.test(trim($("#powerPrice").val()))){
  art.dialog("<?php echo L('L_EMC_ENERGY_PRICE_INPUT_ERRORS');?>");
   return;
  }
  var firstProductionY=trim($("#input_yearp_1").val())+"/"+trim($("#input_yearp_2").val())+"/"+trim($("#input_yearp_3").val())+"/"+
  trim($("#input_yearp_4").val())+"/"+trim($("#input_yearp_5").val())+"/"+trim($("#input_yearp_6").val())+"/"+trim($("#input_yearp_7").val())+"/"+trim($("#input_yearp_8").val())+"/"+trim($("#input_yearp_9").val())+"/"+
  trim($("#input_yearp_10").val())+"/"+trim($("#input_yearp_11").val())+"/"+trim($("#input_yearp_12").val());

  var firstProductionM= trim($("#input_monthp_1").val())+"/"+trim($("#input_monthp_2").val())+"/"+trim($("#input_monthp_3").val())+"/"+
  trim($("#input_monthp_4").val())+"/"+trim($("#input_monthp_5").val())+"/"+trim($("#input_monthp_6").val())+"/"+trim($("#input_monthp_7").val())+"/"+trim($("#input_monthp_8").val())+"/"+trim($("#input_monthp_9").val())+"/"+
  trim($("#input_monthp_10").val())+"/"+trim($("#input_monthp_11").val())+"/"+trim($("#input_monthp_12").val());

  if(firstProductionM=="///////////"&&firstProductionY=="///////////"){
   art.dialog("<?php echo L('L_EMC_ENERGY_PRODUCE_NOT_EMPTY');?>");
   return ;
  }
  if(firstProductionY!="///////////"){
    var i=0;
    var flag=false;
    $("#tab_yearp input").each(function(){
      if(trim($(this).val())==""){
          i=i+1;
       }
     });
     if(i>=10){
     art.dialog("<?php echo L('L_EMC_ENERGY_QUARTER_PRODUCE_NOT_EMPTY');?>");
     return ;
     }
     $("#tab_yearp input").each(function(){
       if(trim($(this).val())!=""){
         if(!re.test(trim($(this).val()))){
             flag=true;
         }
       }
     });
     if(flag){
       art.dialog("<?php echo L('L_EMC_ENERGY_YEAR_PRODUCE_INPUT_ERRORS');?>");
       return;
     }
     var  firstProductionM="";
  }else  if(firstProductionM!="///////////"){
        var flag=false;
        $("#tab_monthp input").each(function(){
          if(trim($(this).val())==""){
              flag=true;
          }
        });
        if(flag){
          art.dialog("<?php echo L('L_EMC_ENERGY_MONTH_PRODUCE_NOT_EMPTY');?>");
          return;
        }
        $("#tab_monthp input").each(function(){
          if(!re.test(trim($(this).val()))){
              flag=true;
          }
        });
        if(flag){
          art.dialog("<?php echo L('L_EMC_ENERGY_MONTH_PRODUCE_INPUT_ERRORS');?>");
          return;
        }
   var  firstProductionY="";
  }
  for(var i=1;i<=12;i++){
    if(trim($("#input_yearp_"+i).val())==""){
      $("#input_year_"+i).val("");
    }
  }

  var firstExpendY= trim($("#input_year_1").val())+"/"+trim($("#input_year_2").val())+"/"+trim($("#input_year_3").val())+"/"+
  trim($("#input_year_4").val())+"/"+trim($("#input_year_5").val())+"/"+trim($("#input_year_6").val())+"/"+trim($("#input_year_7").val())+"/"+trim($("#input_year_8").val())+"/"+trim($("#input_year_9").val())+"/"+
  trim($("#input_year_10").val())+"/"+trim($("#input_year_11").val())+"/"+trim($("#input_year_12").val());
  var firstExpendM= trim($("#input_month_1").val())+"/"+trim($("#input_month_2").val())+"/"+trim($("#input_month_3").val())+"/"+
  trim($("#input_month_4").val())+"/"+trim($("#input_month_5").val())+"/"+trim($("#input_month_6").val())+"/"+trim($("#input_month_7").val())+"/"+trim($("#input_month_8").val())+"/"+trim($("#input_month_9").val())+"/"+
  trim($("#input_month_10").val())+"/"+trim($("#input_month_11").val())+"/"+trim($("#input_month_12").val());
  if(firstExpendY=="///////////"&&firstExpendM=="///////////"){
    art.dialog("<?php echo L('L_EMC_ENERGY_EXPEND_NOT_EMPTY');?>");
    return ;
  }
  if(firstExpendY!="///////////"){
    var i=0;
    $("#tab_year input").each(function(){
      if(trim($(this).val())==""){
          i=i+1;
       }
     });
     if(i>=10){
     art.dialog("<?php echo L('L_EMC_ENERGY_QUARTER_EXPEND_NOT_EMPTY');?>");
     return ;
     }
     //判断是否为数字
     $("#tab_year input").each(function(){
       if(trim($(this).val())!=""){
         if(!re.test(trim($(this).val()))){
             flag=true;
         }
       }

     });
     if(flag){
       art.dialog("<?php echo L('L_EMC_ENERGY_YEAR_EXPEND_INPUT_ERRORS');?>");
       return;
     }
     var  firstExpendM="";
  }else{
        if(firstProductionM!="///////////"){
        var flag=false;
            $("#tab_month input").each(function(){
              if(trim($(this).val())==""){
                  flag=true;
              }
            });
            if(flag){
              art.dialog("<?php echo L('L_EMC_ENERGY_MONTH_EXPEND_NOT_EMPTY');?>");
              return;
            }
            $("#tab_month input").each(function(){
              if(!re.test(trim($(this).val()))){
                  flag=true;
              }
            });
            if(flag){
              art.dialog("<?php echo L('L_EMC_ENERGY_MONTH_EXPEND_INPUT_ERRORS');?>");
              return;
            }
               var  firstExpendY="";
        }
  }


  if(trim($("#expendScale").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_EXPENDSCALE_NOT_EMPTY');?>");
  return;
  }
  if(!re.test(trim($("#expendScale").val()))){
  art.dialog("<?php echo L('L_EMC_ENERGY_EXPENDSCALE_INPUT_ERRORS');?>");
   return;
  }
  if(trim($("#input_jia").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EMPTY');?>");
  return;
  }
  if(!re.test(trim($("#input_jia").val()))){
  art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_INPUT_ERRORS');?>");
   return;
  }
  if(trim($("#input_yi").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EMPTY');?>");
  return;
  }
  if(!re.test(trim($("#input_yi").val()))){
  art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_INPUT_ERRORS');?>");
   return;
  }
  if(trim($("#input_qita").val())==""){
  art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EMPTY');?>");
  return;
  }
  if(!re.test(trim($("#input_qita").val()))){
  art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_INPUT_ERRORS');?>");
   return;
  }

  var jia=parseFloat($("#input_jia").val());
  var yi=parseFloat($("#input_yi").val());
  var qita=parseFloat($("#input_qita").val());

  var sum= (jia+ yi +qita).toFixed(2);

  if(jia>1||yi>1||qita>1){
    art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_GREATER_THAN_ONE');?>");
    return;
  }
  if(sum>1||sum<1){
    art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EQUAL_TO_ONE');?>")
  return ;
  }
    $.post("/home/emc/add",
    {
      powerUnity:$("#powerUnity").val(),
      powerName:$("#powerName").val(),
      powerNumber:$("#powerNumber").val(),
      powerPrice:$("#powerPrice").val(),
      currency:$("#currency").val(),
      firstExpendY:firstExpendY,
      firstExpendM:firstExpendM,
      firstProductionY:firstProductionY,
      firstProductionM:firstProductionM,
      expendScale:$("#expendScale").val(),
      jia:$("#input_jia").val(),
      yi:$("#input_yi").val(),
      qita:$("#input_qita").val()
    },
    function(res){
      if(res=="ok"){
       $("#div_input input").attr("readonly","readonly");
       $("#currency").attr("disabled","disabled");
       $("#sub").hide();
      art.dialog('<?php echo L("L_ALERT_SUCCESS");?>');
    }else {
      art.dialog('<?php echo L("L_ALERT_FAIL");?>');
    }
    });
  });
});
</script>
</head>
<body>

  <h1 style="font-size:20px">EMC</h1>
  <div id="div_input">
  <h2><?php echo L('L_EMC_FDATA_SET');?></h2>
  <p><span><?php echo L('L_EMC_ENERGY_UNIT');?>:</span><input type="text" name="powerUnity" id="powerUnity" value="<?php echo ($data['powerunity']); ?>" ></p>
  <p><span><?php echo L('L_EMC_ENERGY_NAME');?>:</span><input type="text" name="powerName" id="powerName" value="<?php echo ($data['powername']); ?>"></p>
  <p><span><?php echo L('L_EMC_ENERGY_USE');?>:</span><input type="text" name="powerNumber" id="powerNumber" value="<?php echo ($data['powernumber']); ?>"></p>
  <p><span><?php echo L('L_EMC_ENERGY_PRICE');?>:</span><input type="text" name="powerPrice" id="powerPrice" value="<?php echo ($data['powerprice']); ?>">
   <?php echo L('L_EMC_CURRENCY_UNIT');?>:<select id="currency" class="select_input">
              <option value="￥" >￥</option>
              <option value="$">$</option>
              <option value="€" >€</option>
            </select>
  </p>
    <p>
    <table class="tab_radio">
        <tr>
        <td><input name="production" type="radio" value=""  checked="checked" id="yproduction"/></td>
        <td><?php echo L('L_EMC_YPRODUCE_ENERGY');?></td>
        <td><input name="production" type="radio" value="" id="mproduction" /></td>
        <td><?php echo L('L_EMC_MPRODUCE_ENERGY');?></td>
        </tr>
    </table>
    </p>

    <div id="yearproduction" class="div_p">
    <table id="tab_yearp" cellspacing="0" cellpadding="0" class="tab_month" >
    <tr>
    <td><?php echo L('L_EMC_January');?></td>
    <td><?php echo L('L_EMC_February');?></td>
    <td><?php echo L('L_EMC_March');?></td>
    <td><?php echo L('L_EMC_April');?></td>
    <td><?php echo L('L_EMC_May');?></td>
    <td><?php echo L('L_EMC_June');?></td>
    </tr>
    <tr >
    <td ><input type="text" id="input_yearp_1"></td>
    <td ><input type="text" id="input_yearp_2"></td>
    <td ><input type="text" id="input_yearp_3"></td>
    <td ><input type="text" id="input_yearp_4"></td>
    <td ><input type="text" id="input_yearp_5"></td>
    <td ><input type="text" id="input_yearp_6"></td>
    </tr>
    <tr>
      <td><?php echo L('L_EMC_July');?></td>
      <td><?php echo L('L_EMC_August');?></td>
      <td><?php echo L('L_EMC_September');?></td>
      <td><?php echo L('L_EMC_October');?></td>
      <td><?php echo L('L_EMC_November');?></td>
      <td><?php echo L('L_EMC_December');?></td>
    </tr>
    <tr>
    <td><input type="text" id="input_yearp_7"></td>
    <td><input type="text" id="input_yearp_8"></td>
    <td><input type="text" id="input_yearp_9"></td>
    <td><input type="text" id="input_yearp_10"></td>
    <td><input type="text" id="input_yearp_11"></td>
    <td><input type="text" id="input_yearp_12"></td>
    </tr>
    </table>
  </div>
    <div id="monthproduction" style="display:none">
    <table id="tab_monthp" cellspacing="0" cellpadding="0" class="tab_month" >
    <tr>
    <td><?php echo L('L_EMC_January');?></td>
    <td><?php echo L('L_EMC_February');?></td>
    <td><?php echo L('L_EMC_March');?></td>
    <td><?php echo L('L_EMC_April');?></td>
    <td><?php echo L('L_EMC_May');?></td>
    <td><?php echo L('L_EMC_June');?></td>
    </tr>
    <tr >
    <td ><input type="text" id="input_monthp_1"></td>
    <td ><input type="text" id="input_monthp_2"></td>
    <td ><input type="text" id="input_monthp_3"></td>
    <td ><input type="text" id="input_monthp_4"></td>
    <td ><input type="text" id="input_monthp_5"></td>
    <td ><input type="text" id="input_monthp_6"></td>
    </tr>
    <tr>
      <td><?php echo L('L_EMC_July');?></td>
      <td><?php echo L('L_EMC_August');?></td>
      <td><?php echo L('L_EMC_September');?></td>
      <td><?php echo L('L_EMC_October');?></td>
      <td><?php echo L('L_EMC_November');?></td>
      <td><?php echo L('L_EMC_December');?></td>
    </tr>
    <tr>
    <td><input type="text" id="input_monthp_7"></td>
    <td><input type="text" id="input_monthp_8"></td>
    <td><input type="text" id="input_monthp_9"></td>
    <td><input type="text" id="input_monthp_10"></td>
    <td><input type="text" id="input_monthp_11"></td>
    <td><input type="text" id="input_monthp_12"></td>
    </tr>
    </table>

    </div>
  <p>
    <table class="tab_radio"><tr><td>
  <input name="expend" type="radio" value=""  checked="checked" id="yexpend"/></td><td><?php echo L('L_EMC_YCONSUME_ENERGY');?></td><td>
  <input name="expend" type="radio" value="" id="mexpend" /></td><td><?php echo L('L_EMC_MCONSUME_ENERGY');?></td></tr></table>
  </p>

  <div id="yearExpend" class="div_p">
  <table id="tab_year" cellspacing="0" cellpadding="0" class="tab_month" >
  <tr>
  <td><?php echo L('L_EMC_January');?></td>
  <td><?php echo L('L_EMC_February');?></td>
  <td><?php echo L('L_EMC_March');?></td>
  <td><?php echo L('L_EMC_April');?></td>
  <td><?php echo L('L_EMC_May');?></td>
  <td><?php echo L('L_EMC_June');?></td>
  </tr>
  <tr >
  <td ><input type="text" id="input_year_1"></td>
  <td ><input type="text" id="input_year_2"></td>
  <td ><input type="text" id="input_year_3"></td>
  <td ><input type="text" id="input_year_4"></td>
  <td ><input type="text" id="input_year_5"></td>
  <td ><input type="text" id="input_year_6"></td>
  </tr>
  <tr>
    <td><?php echo L('L_EMC_July');?></td>
    <td><?php echo L('L_EMC_August');?></td>
    <td><?php echo L('L_EMC_September');?></td>
    <td><?php echo L('L_EMC_October');?></td>
    <td><?php echo L('L_EMC_November');?></td>
    <td><?php echo L('L_EMC_December');?></td>
  </tr>
  <tr>
  <td><input type="text" id="input_year_7"></td>
  <td><input type="text" id="input_year_8"></td>
  <td><input type="text" id="input_year_9"></td>
  <td><input type="text" id="input_year_10"></td>
  <td><input type="text" id="input_year_11"></td>
  <td><input type="text" id="input_year_12"></td>
  </tr>
  </table>
  </div>
    <div id="monthExpend" style="display:none">
    <table id="tab_month" cellspacing="0" cellpadding="0" class="tab_month" >
    <tr>
    <td><?php echo L('L_EMC_January');?></td>
    <td><?php echo L('L_EMC_February');?></td>
    <td><?php echo L('L_EMC_March');?></td>
    <td><?php echo L('L_EMC_April');?></td>
    <td><?php echo L('L_EMC_May');?></td>
    <td><?php echo L('L_EMC_June');?></td>
    </tr>
    <tr >
    <td ><input type="text" id="input_month_1"></td>
    <td ><input type="text" id="input_month_2"></td>
    <td ><input type="text" id="input_month_3"></td>
    <td ><input type="text" id="input_month_4"></td>
    <td ><input type="text" id="input_month_5"></td>
    <td ><input type="text" id="input_month_6"></td>
    </tr>
    <tr>
      <td><?php echo L('L_EMC_July');?></td>
      <td><?php echo L('L_EMC_August');?></td>
      <td><?php echo L('L_EMC_September');?></td>
      <td><?php echo L('L_EMC_October');?></td>
      <td><?php echo L('L_EMC_November');?></td>
      <td><?php echo L('L_EMC_December');?></td>
    </tr>
    <tr>
    <td><input type="text" id="input_month_7"></td>
    <td><input type="text" id="input_month_8"></td>
    <td><input type="text" id="input_month_9"></td>
    <td><input type="text" id="input_month_10"></td>
    <td><input type="text" id="input_month_11"></td>
    <td><input type="text" id="input_month_12"></td>
    </tr>
    </table>
    </div>
<p><span><?php echo L('L_EMC_ENERGY_USE_PROPORTION');?>:</span><input  type="text"   value="1"  name="expendScale" id="expendScale" value="<?php echo ($data['expendscale']); ?>"></p>
</div>
<h2><?php echo L('L_EMC_JSFC_PROPORTION');?></h2>
<p><span><?php echo L('L_EMC_JIA');?>:</span><input  type ="text" id="input_jia"  value="<?php echo ($data['jia']); ?>"></P>
<p><span><?php echo L('L_EMC_YI');?>:</span><input  type="text"  id="input_yi" value="<?php echo ($data['yi']); ?>"></p>
<p><span><?php echo L('L_EMC_OTHER');?>:</span><input  type="text" id="input_qita" value="<?php echo ($data['qita']); ?>"></p>
  <div class="div_button">
  <input type="button" id="sub" value="<?php echo L('L_EMC_SUBMIT');?>" class="save"  name="submit">
  <div>
<hr/>
    <script type="text/javascript">
    $(function(){
      $("#pro").click(function(){EX_EXCEL();});
         });
        function EX_EXCEL(){
         var re =/^\d+(?=\.{0,1}\d+$|$)/;
         if(trim($("#input_jia").val())==""){
         art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EMPTY');?>");
         return;
         }
         if(!re.test(trim($("#input_jia").val()))){
         art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_INPUT_ERRORS');?>");
          return;
         }
         if(trim($("#input_yi").val())==""){
         art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EMPTY');?>");
         return;
         }
         if(!re.test(trim($("#input_yi").val()))){
         art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_INPUT_ERRORS');?>");
          return;
         }
         if(trim($("#input_qita").val())==""){
         art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EMPTY');?>");
         return;
         }
         if(!re.test(trim($("#input_qita").val()))){
         art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_INPUT_ERRORS');?>");
          return;
         }
         var jia=parseFloat($("#input_jia").val());
         var yi=parseFloat($("#input_yi").val());
         var qita=parseFloat($("#input_qita").val());

         var sum= (jia+ yi +qita).toFixed(2);

         if(jia>1||yi>1||qita>1){
           art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_GREATER_THAN_ONE');?>");
           return;
         }
         if(sum>1||sum<1){
         art.dialog("<?php echo L('L_EMC_ENERGY_PROPORTION_NOT_EQUAL_TO_ONE');?>")
         return ;
         }
        if(trim($("#input_Unity").val())==""){
        art.dialog("<?php echo L('L_EMC_ENERGY_UNIT_NOT_EMPTY');?>");
        return ;
        }
        if(trim($("#input_Price").val())==""){
        art.dialog("<?php echo L('L_EMC_ENERGY_PRICE_NOT_EMPTY');?>");
        return ;
        }
        if(!re.test(trim($("#input_Price").val()))){
        art.dialog("<?php echo L('L_EMC_ENERGY_PRICE_INPUT_ERRORS');?>");
         return;
        }
        var productionY= trim($("#input_tyearp_1").val())+"/"+trim($("#input_tyearp_2").val())+"/"+trim($("#input_tyearp_3").val())+"/"+
        trim($("#input_tyearp_4").val())+"/"+trim($("#input_tyearp_5").val())+"/"+trim($("#input_tyearp_6").val())+"/"+trim($("#input_tyearp_7").val())+"/"+trim($("#input_tyearp_8").val())+"/"+trim($("#input_tyearp_9").val())+"/"+
        trim($("#input_tyearp_10").val())+"/"+trim($("#input_tyearp_11").val())+"/"+trim($("#input_tyearp_12").val());
        var productionM= trim($("#input_tmonthp_1").val())+"/"+trim($("#input_tmonthp_2").val())+"/"+trim($("#input_tmonthp_3").val())+"/"+
        trim($("#input_tmonthp_4").val())+"/"+trim($("#input_tmonthp_5").val())+"/"+trim($("#input_tmonthp_6").val())+"/"+trim($("#input_tmonthp_7").val())+"/"+trim($("#input_tmonthp_8").val())+"/"+trim($("#input_tmonthp_9").val())+"/"+
        trim($("#input_tmonthp_10").val())+"/"+trim($("#input_tmonthp_11").val())+"/"+trim($("#input_tmonthp_12").val());

        if(productionY=="///////////"&&productionM=="///////////"){
        art.dialog("<?php echo L('L_EMC_ENERGY_THISYEAR_PRODUCE_NOT_EMPTY');?>");
        return;
        }
        if(productionY!="///////////"){
          var flag=false;
        $('#table_yearp input:not(:disabled)').each(function(){
             if(trim($(this).val())==""){
               flag=true;
             }
        });
        if(flag){
          art.dialog("<?php echo L('L_EMC_ENERGY_THISYEAR_PRODUCE_NOT_EMPTY');?>");
          return;
        }
        $("#table_yearp input").each(function(){
           if(trim($(this).val())!=""){
             if(!re.test(trim($(this).val()))){
                 flag=true;
             }
           }
         });
         if(flag){
           art.dialog("<?php echo L('L_EMC_ENERGY_THISYEAR_PRODUCE_INPUT_ERRORS');?>");
           return;
         }
        productionM="";

        }else  if (productionM!="///////////") {
            var flag=false;
            $("#table_monthp input").each(function(){
              if(trim($(this).val())==""){
                  flag=true;
              }
            });
            if(flag){
              art.dialog("<?php echo L('L_EMC_ENERGY_THISMONTH_PRODUCE_NOT_EMPTY');?>");
              return;
            }
            $("#table_monthp input").each(function(){
              if(!re.test(trim($(this).val()))){
                  flag=true;
              }
            });
            if(flag){
              art.dialog("<?php echo L('L_EMC_ENERGY_THISMONTH_PRODUCE_INPUT_ERRORS');?>");
              return;
            }
            productionY="";
        }
        var expendY= trim($("#input_tyear_1").val())+"/"+trim($("#input_tyear_2").val())+"/"+trim($("#input_tyear_3").val())+"/"+
        trim($("#input_tyear_4").val())+"/"+trim($("#input_tyear_5").val())+"/"+trim($("#input_tyear_6").val())+"/"+trim($("#input_tyear_7").val())+"/"+trim($("#input_tyear_8").val())+"/"+trim($("#input_tyear_9").val())+"/"+
        trim($("#input_tyear_10").val())+"/"+trim($("#input_tyear_11").val())+"/"+trim($("#input_tyear_12").val());
        var expendM= trim($("#input_tmonth_1").val())+"/"+trim($("#input_tmonth_2").val())+"/"+trim($("#input_tmonth_3").val())+"/"+
        trim($("#input_tmonth_4").val())+"/"+trim($("#input_tmonth_5").val())+"/"+trim($("#input_tmonth_6").val())+"/"+trim($("#input_tmonth_7").val())+"/"+trim($("#input_tmonth_8").val())+"/"+trim($("#input_tmonth_9").val())+"/"+
        trim($("#input_tmonth_10").val())+"/"+trim($("#input_tmonth_11").val())+"/"+trim($("#input_tmonth_12").val());
        if(expendY=="///////////"&& expendM=="///////////"){

        art.dialog("<?php echo L('L_EMC_ENERGY_THISYEAR_EXPEND_NOT_EMPTY');?>");
        return;
        }
        if(expendY!="///////////"){
          var flag=false;
          $('#table_year input:not(:disabled)').each(function(){
               if(trim($(this).val())==""){
                 flag=true;
               }
          });
          if(flag){
              alert(1);
            art.dialog("<?php echo L('L_EMC_ENERGY_THISYEAR_EXPEND_NOT_EMPTY');?>");
            return;
          }


          $("#table_year input").each(function(){
             if(trim($(this).val())!=""){
               if(!re.test(trim($(this).val()))){
                   flag=true;
               }
             }
           });
           if(flag){
             art.dialog("<?php echo L('L_EMC_ENERGY_THISYEAR_EXPEND_INPUT_ERRORS');?>");
             return;
           }
        expendM="";
        }else{
            var flag=false;
            $("#table_month input").each(function(){
              if(trim($(this).val())==""){
                  flag=true;
              }
            });
            if(flag){
              art.dialog("<?php echo L('L_EMC_ENERGY_THISMONTH_EXPEND_NOT_EMPTY');?>");
              return;
            }
            $("#table_month input").each(function(){
              if(!re.test(trim($(this).val()))){
                  flag=true;
              }
            });
            if(flag){
              art.dialog("<?php echo L('L_EMC_ENERGY_THISMONTH_EXPEND_INPUT_ERRORS');?>");
              return;
            }
                expendY="";
        }
        if(trim($("#thisyearexpendScale").val())==""){
        art.dialog("<?php echo L('L_EMC_ENERGY_EXPENDSCALE_NOT_EMPTY');?>");
        return;
        }
        if(!re.test(trim($("#thisyearexpendScale").val()))){
        art.dialog("<?php echo L('L_EMC_ENERGY_EXPENDSCALE_INPUT_ERRORS');?>");
         return;
        }
        var post_data ='jia='+trim($("#input_jia").val())+'&yi='+trim($("#input_yi").val())+'&qita='+trim($("#input_qita").val())+'&powerUnity='+trim($("#input_Unity").val())+'&powerPrice='+trim($("#input_Price").val())+'&productionY='+productionY
      +'&productionM='+productionM+'&expendY='+expendY+'&expendM='+expendM;

          post_data+='thisyearexpendScale='+trim($("#thisyearexpendScale").val());
          //location="/home/emc/excel?"+post_data;
          //$("#if_ex_excel").attr("src","/home/emc/excel?"+post_data);
          location="/home/emc/excel?"+post_data;

      }

  </script>

<div id="div_year">
  <!--<iframe id='if_ex_excel'></iframe>-->
  <h2><?php echo L('L_EMC_THISYEAR_DATASET');?></h2>
  <p><span><?php echo L('L_EMC_ENERGY_UNIT');?>:</span><input  type ="text" id="input_Unity"  value="<?php echo ($data['powerunity']); ?>"></P>
  <p><span><?php echo L('L_EMC_ENERGY_PRICE');?>:</span><input  type ="text" id="input_Price"  value=""></P>
    <p>
    <table class="tab_radio"><tr><td>
    <input name="tproduction" type="radio" value=""  id="input_yproduction"/></td><td><?php echo L('L_EMC_YPRODUCE_ENERGY');?></td><td>
    <input name="tproduction" type="radio" value="" id="input_mproduction" /></td><td><?php echo L('L_EMC_MPRODUCE_ENERGY');?></td></tr></table>
    </p>

    <div id="thisyearProduction" class="div_p">
      <table id="table_yearp" cellspacing="0" cellpadding="0" class="tab_month" >
      <tr>
      <td><?php echo L('L_EMC_January');?></td>
      <td><?php echo L('L_EMC_February');?></td>
      <td><?php echo L('L_EMC_March');?></td>
      <td><?php echo L('L_EMC_April');?></td>
      <td><?php echo L('L_EMC_May');?></td>
      <td><?php echo L('L_EMC_June');?></td>
      </tr>
      <tr >
      <td ><input type="text" id="input_tyearp_1"></td>
      <td ><input type="text" id="input_tyearp_2"></td>
      <td ><input type="text" id="input_tyearp_3"></td>
      <td ><input type="text" id="input_tyearp_4"></td>
      <td ><input type="text" id="input_tyearp_5"></td>
      <td ><input type="text" id="input_tyearp_6"></td>
      </tr>
      <tr>
        <td><?php echo L('L_EMC_July');?></td>
        <td><?php echo L('L_EMC_August');?></td>
        <td><?php echo L('L_EMC_September');?></td>
        <td><?php echo L('L_EMC_October');?></td>
        <td><?php echo L('L_EMC_November');?></td>
        <td><?php echo L('L_EMC_December');?></td>
      </tr>
      <tr>
      <td><input type="text" id="input_tyearp_7"></td>
      <td><input type="text" id="input_tyearp_8"></td>
      <td><input type="text" id="input_tyearp_9"></td>
      <td><input type="text" id="input_tyearp_10"></td>
      <td><input type="text" id="input_tyearp_11"></td>
      <td><input type="text" id="input_tyearp_12"></td>
      </tr>
      </table>
    </div>


    <div id="thismonthProduction" style="display:none">
    <table id="table_monthp" cellspacing="0" cellpadding="0" class="tab_month" >
    <tr>
    <td><?php echo L('L_EMC_January');?></td>
    <td><?php echo L('L_EMC_February');?></td>
    <td><?php echo L('L_EMC_March');?></td>
    <td><?php echo L('L_EMC_April');?></td>
    <td><?php echo L('L_EMC_May');?></td>
    <td><?php echo L('L_EMC_June');?></td>
    </tr>
    <tr >
    <td ><input type="text" id="input_tmonthp_1"></td>
    <td ><input type="text" id="input_tmonthp_2"></td>
    <td ><input type="text" id="input_tmonthp_3"></td>
    <td ><input type="text" id="input_tmonthp_4"></td>
    <td ><input type="text" id="input_tmonthp_5"></td>
    <td ><input type="text" id="input_tmonthp_6"></td>
    </tr>
    <tr>
      <td><?php echo L('L_EMC_July');?></td>
      <td><?php echo L('L_EMC_August');?></td>
      <td><?php echo L('L_EMC_September');?></td>
      <td><?php echo L('L_EMC_October');?></td>
      <td><?php echo L('L_EMC_November');?></td>
      <td><?php echo L('L_EMC_December');?></td>
    </tr>
    <tr>
    <td><input type="text" id="input_tmonthp_7"></td>
    <td><input type="text" id="input_tmonthp_8"></td>
    <td><input type="text" id="input_tmonthp_9"></td>
    <td><input type="text" id="input_tmonthp_10"></td>
    <td><input type="text" id="input_tmonthp_11"></td>
    <td><input type="text" id="input_tmonthp_12"></td>
    </tr>
    </table>
    </div>
    <p>
    <table class="tab_radio"><tr><td>
    <input name="texpend" type="radio" value="" id="input_yexpend"/></td><td><?php echo L('L_EMC_YCONSUME_ENERGY');?></td><td>
    <input name="texpend" type="radio" value="" id="input_mexpend" /></td><td><?php echo L('L_EMC_MCONSUME_ENERGY');?></td></tr></table>
    </p>
      <div id="thisyearExpend" class="div_p">
        <table id="table_year" cellspacing="0" cellpadding="0" class="tab_month" >
        <tr>
        <td><?php echo L('L_EMC_January');?></td>
        <td><?php echo L('L_EMC_February');?></td>
        <td><?php echo L('L_EMC_March');?></td>
        <td><?php echo L('L_EMC_April');?></td>
        <td><?php echo L('L_EMC_May');?></td>
        <td><?php echo L('L_EMC_June');?></td>
        </tr>
        <tr >
        <td ><input type="text" id="input_tyear_1"></td>
        <td ><input type="text" id="input_tyear_2"></td>
        <td ><input type="text" id="input_tyear_3"></td>
        <td ><input type="text" id="input_tyear_4"></td>
        <td ><input type="text" id="input_tyear_5"></td>
        <td ><input type="text" id="input_tyear_6"></td>
        </tr>
        <tr>
          <td><?php echo L('L_EMC_July');?></td>
          <td><?php echo L('L_EMC_August');?></td>
          <td><?php echo L('L_EMC_September');?></td>
          <td><?php echo L('L_EMC_October');?></td>
          <td><?php echo L('L_EMC_November');?></td>
          <td><?php echo L('L_EMC_December');?></td>
        </tr>
        <tr>
        <td><input type="text" id="input_tyear_7"></td>
        <td><input type="text" id="input_tyear_8"></td>
        <td><input type="text" id="input_tyear_9"></td>
        <td><input type="text" id="input_tyear_10"></td>
        <td><input type="text" id="input_tyear_11"></td>
        <td><input type="text" id="input_tyear_12"></td>
        </tr>
        </table>

      </div>
      <div id="thismonthExpend" style="display:none">
      <table id="table_month" cellspacing="0" cellpadding="0" class="tab_month" >
      <tr>
      <td><?php echo L('L_EMC_January');?></td>
      <td><?php echo L('L_EMC_February');?></td>
      <td><?php echo L('L_EMC_March');?></td>
      <td><?php echo L('L_EMC_April');?></td>
      <td><?php echo L('L_EMC_May');?></td>
      <td><?php echo L('L_EMC_June');?></td>
      </tr>
      <tr >
      <td ><input type="text" id="input_tmonth_1"></td>
      <td ><input type="text" id="input_tmonth_2"></td>
      <td ><input type="text" id="input_tmonth_3"></td>
      <td ><input type="text" id="input_tmonth_4"></td>
      <td ><input type="text" id="input_tmonth_5"></td>
      <td ><input type="text" id="input_tmonth_6"></td>
      </tr>
      <tr>
        <td><?php echo L('L_EMC_July');?></td>
        <td><?php echo L('L_EMC_August');?></td>
        <td><?php echo L('L_EMC_September');?></td>
        <td><?php echo L('L_EMC_October');?></td>
        <td><?php echo L('L_EMC_November');?></td>
        <td><?php echo L('L_EMC_December');?></td>
      </tr>
      <tr>
      <td><input type="text" id="input_tmonth_7"></td>
      <td><input type="text" id="input_tmonth_8"></td>
      <td><input type="text" id="input_tmonth_9"></td>
      <td><input type="text" id="input_tmonth_10"></td>
      <td><input type="text" id="input_tmonth_11"></td>
      <td><input type="text" id="input_tmonth_12"></td>
      </tr>
      </table>
      </div>
    <p><span><?php echo L('L_EMC_ENERGY_USE_PROPORTION');?>:</span><input  type="text"   value="1"  name="thisyearexpendScale" id="thisyearexpendScale" ></p>
  <div class="div_button"><input type="button" id="pro" value="<?php echo L('L_EMC_PRODUCT');?>" class="save"  name="product"></div>
</div>

</body>

</html>