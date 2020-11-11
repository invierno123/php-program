<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="/Public/css/supervisory.css" type="text/css" media="screen" />
    <script src='/Public/js/jquery.js' ></script>
</head>
<body>

  <table class="tab_list" cellspacing="0" cellpadding="0">
    <tr>
      <td style="text-align: center;color:#222;"><?php echo L("L_AREA_QY");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_AREA_JDBH");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_LOCATION");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_REPAIRSTATE");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_REPAIRTYPE");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_REPAIRTIME");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_REPAIRNUM");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_REPAIRPRICE");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_TRAPSTATE");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_EXLEVEL");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_LEVELDESC");?></td>
      <td style="text-align: center;color:#222;"><?php echo L("L_ALERT_REPAIRDESCRIPTION");?></td>
    </tr>
    <?php echo ($tab_list); ?>
  </table>
  <table class="tab_page">
    <tr>
      <td><a class="page_active" id="a_p_f"  onclick='page(0)'><?php echo L("L_ALERT_PAGE_F");?></a></td>
      <td><a class="page_active" id="a_p_p"  onclick='page(-1)'><?php echo L("L_ALERT_PAGE_P");?></a></td>
      <td><a class="page_active" id="a_p_n"  onclick='page(1)'><?php echo L("L_ALERT_PAGE_N");?></a></td>
      <td><a class="page_active" id="a_p_l"  onclick='page(9)'><?php echo L("L_ALERT_PAGE_L");?></a></td>
      <td><?php echo ($pagedata); ?></td>
    </tr>
  </table>
  <script>
    function show_repairinfo(str_msg){
      alert(str_msg);
    }
    function getUrlParam(name) {
      var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
      var r = window.location.search.substr(1).match(reg);  //匹配目标参数
      if (r != null) return unescape(r[2]); return ""; //返回参数值
    }
    function page(op){
      var pc= parseInt('<?php echo ($pc); ?>');
      var apc= parseInt('<?php echo ($apc); ?>');
      if(op==0){
        pc=1;
      }else if(op==9){
        pc=apc;
      }else{
        pc=pc+op;
      }
      if(pc<=apc && pc>0){
        location="warninginfo?sid="+getUrlParam("sid")+"&tid="+getUrlParam("tid")+"&p="+pc;
      }
    }
    $(function(){
      var pc='<?php echo ($pc); ?>';
      var apc= '<?php echo ($apc); ?>';
      if(pc==apc){
        $("#a_p_l").removeAttr("class");
        $("#a_p_n").removeAttr("class");
        $("#a_p_l").removeAttr("onclick");
        $("#a_p_n").removeAttr("onclick");
      }
      if(pc=="1"){
        $("#a_p_f").removeAttr("class");
        $("#a_p_p").removeAttr("class");
        $("#a_p_f").removeAttr("onclick");
        $("#a_p_p").removeAttr("onclick");
      }
    });
  </script>
</body>
</html>