<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>节点编辑</title>
  <link href="/Public/css/trapedit.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="/Public/js/artDialog-5.0.3/artDialog.min.js"></script>
  <script src='/Public/js/jquery.js' ></script>
  <link href="/Public/js/artDialog-5.0.3/skins/simple.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="/Public/js/jquery-1.7.1.min.js"></script>
  <script>

  var QUXIAO = "<?php echo L('L_ALERT_CANCEL');?>";
  var QUEDING = "<?php echo L('L_ALERT_OK');?>";
  var MaxOrder = parseInt("0<?php echo ($MaxOrder); ?>");
  var add_trap_flag=0;
  var selected = "0";
  var load_flag=0;
  $(function(){
    add_select_data();
    MaxOrderSet("input_sort_0");
    $("#hidden_areaid").val(getQueryString("areacode"));
    pageable();
  });
  function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return "";
  }
  function add_select_data(){
    $.get("/home/systemset/GetReTrap?selected="+selected,function(res){
      if(res==""){

        $("#select_trap_"+add_trap_flag).parent().parent().remove();
        add_trap_flag--;
        MaxOrder--;
        if(load_flag==1){
          art.dialog("<?php echo L('L_AREA_AddTRAP_NETSET');?>");
        }
      }else{
        $("#select_trap_"+add_trap_flag).html(res);
      }

      load_flag=1;
    });
  }
  function select_trap(obj){
    var flag_ = $(obj).attr("id").split("select_trap_")[1];
    if($(obj).val()=="" || $(obj).val()=="0"){
      $("#label_trap_name_"+flag_).html("");
      $("#label_trap_type_"+flag_).html("");
      $("#label_link_type_"+flag_).html("");
      $("#label_trap_size_"+flag_).html("");
      $("#label_out_type_"+flag_).html("");
    }else{
      $.get("/home/systemset/GetTrap?id="+$(obj).val(),function(res){
        var res_array = res.split(",");
        $("#label_trap_name_"+flag_).html(res_array[0]);
        $("#label_trap_type_"+flag_).html(res_array[1]);
        $("#label_link_type_"+flag_).html(res_array[2]);
        $("#label_trap_size_"+flag_).html(res_array[3]);
        $("#label_out_type_"+flag_).html(res_array[4]);
      });
    }
    OPSELECT();
  }
  function OPSELECT(){
    selected="";
    for (var i = 0; i <= add_trap_flag; i++) {
      selected+= $("#select_trap_"+i).val()+",";
    }
    if(selected==""){
      selected="0";
    }
  }
  function number_keyup(obj){
    var re = /^[0-9]+?[0-9]*$/;   //判断字符串是否为数字     //判断正整数 /^[1-9]+[0-9]*]*$/
    var nubmer = $(obj).val();
    if (!re.test(nubmer))
    {
        $(obj).val("");
        return false;
     }
  }
  function add_trap_(){
    if($("#select_trap_"+add_trap_flag).val()==0||$("#select_trap_"+add_trap_flag).val()==""){
      return;
    }
    add_trap_flag++;
    var tr_html='<tr><td><select name="select_trap" id="select_trap_'+add_trap_flag+'" onchange="select_trap(this)"></select></td>  <td><label id="label_trap_name_'+add_trap_flag+'"></label></td><td><label id="label_trap_type_'+add_trap_flag+'"></label></td><td><label id="label_link_type_'+add_trap_flag+'"></label></td><td><label id="label_trap_size_'+add_trap_flag+'"></label></td><td><label id="label_out_type_'+add_trap_flag+'"></label></td><td><?php echo L("L_AREA_AddTRAP_NUM");?><input style="width:40px;" onkeyup="number_keyup(this)" name="input_sort" id="input_sort_'+add_trap_flag+'"/></td><td><a style="cursor:pointer;" onclick="removetrap_JS(this)"><?php echo L("L_AREA_REMOVE");?></a></a></td></tr>';
    $("#tab_list_trap").append(tr_html);
    add_select_data();
    MaxOrder++;
    MaxOrderSet("input_sort_"+add_trap_flag);
  }
  function MaxOrderSet(id_flag){
    $("#"+id_flag).val(MaxOrder+1);
  }
  function removetrap(obj) {
    art.dialog({
            content:"<?php echo L('L_ALERT_DELETE_CONFIRM');?>",
            okValue: QUEDING,
            ok: function () {
              $.get("/home/systemset/deltrap?aid="+$("#hidden_areaid").val()+"&tid="+$(obj).attr("tid"),function(res){
                $(obj).parent().parent().remove();
                return true;
              });
            },
            cancelValue:QUXIAO ,
            cancel: function () {
                return true;
            }
        });
  }
  function removetrap_JS(obj){
    add_trap_flag--;
    MaxOrder--;
    $(obj).parent().parent().remove();
    OPSELECT();
  }
  function PageData(opp){
    var page=parseInt($("#lab_c_p").text());
    if(opp==0){
      page=1;
    }else if(opp==9){
      page=parseInt($("#lab_t_p").text()) ;
    }else{
      page=page+parseInt(opp);
    }

    location="/home/systemset/TrapEdit?areacode="+$("#hidden_areaid").val()+"&page="+page;

  }
  function pageable(){
    if($("#lab_c_p").text()=="1"){
      $("#page_f").removeAttr("class");
      $("#page_p").removeAttr("class");

      $("#page_f").attr("class","unpage");
      $("#page_p").attr("class","unpage");

      $("#page_f").removeAttr("onclick");
      $("#page_p").removeAttr("onclick");
    }
    if($("#lab_c_p").text()==$("#lab_t_p").text()){
      $("#page_n").removeAttr("class");
      $("#page_l").removeAttr("class");

      $("#page_n").attr("class","unpage");
      $("#page_l").attr("class","unpage");

      $("#page_n").removeAttr("onclick");
      $("#page_l").removeAttr("onclick");
    }
  }
  </script>
</head>
<body>

  <font style="color:gray; padding-left:25px;"><?php echo ($AreaINFO); ?></font>
  <input id="hidden_areaid" type="hidden" />
  <table class="tab_list" id="tab_list_trap" cellspadding="0" cellspacing="0" style="margin-top:15px;">
    <tr>
      <td><?php echo L("L_AREA_JDBH");?></td>
      <td><?php echo L("L_AREA_JDMC");?></td>
      <td><?php echo L("L_AREA_FMLX");?></td>
      <td><?php echo L("L_AREA_LJLX");?></td>
      <td><?php echo L("L_AREA_GDKJ");?></td>
      <td><?php echo L("L_AREA_LCLX");?></td>
      <!--<td><?php echo L("L_AREA_DQZT");?></td>-->
      <td><?php echo L("L_AREA_SORT");?></td>
      <td><?php echo L("L_AREA_CZ");?></td>
    </tr>
    <?php if(is_array($tlist)): $i = 0; $__LIST__ = $tlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><tr>
        <td><?php echo ($data["trapno"]); ?><select name="select_trap" style="display:none;"><option value="<?php echo ($data["id"]); ?>" checked="true"><?php echo ($data["id"]); ?></option></select></td>
        <td><?php echo ($data["trapname"]); ?></td>
        <td><?php echo ($data["traptype"]); ?></td>
        <td><?php echo ($data["linktype"]); ?></td>
        <td><?php echo ($data["linesize"]); ?></td>
        <td><?php echo ($data["outtype"]); ?></td>
        <td><?php echo L("L_AREA_AddTRAP_NUM");?><input style="width:40px;" name="input_sort" value="<?php echo ($data["ordernum"]); ?>" onkeyup="number_keyup(this)" /></td>
        <!--<td><?php echo ($data["trapstate"]); ?></td>-->
        <td><a style="cursor:pointer;" tid="<?php echo ($data["id"]); ?>" onclick="removetrap(this)"><?php echo L("L_AREA_REMOVE");?></a></td>
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr>
      <td><select name="select_trap" id="select_trap_0" onchange="select_trap(this)"></select></td>
      <td><label id="label_trap_name_0"></label></td>
      <td><label id="label_trap_type_0"></label></td>
      <td><label id="label_link_type_0"></label></td>
      <td><label id="label_trap_size_0"></label></td>
      <td><label id="label_out_type_0"></label></td>
      <td><?php echo L("L_AREA_AddTRAP_NUM");?><input style="width:40px;" name="input_sort" onkeyup="number_keyup(this)" id="input_sort_0"/></td>
      <td><a style="cursor:pointer;" onclick="removetrap_JS(this)"><?php echo L("L_AREA_REMOVE");?></a></a></td>
    </tr>
  </table>
  <table class="tab_page">
    <tr>
      <td><a onclick="PageData(0)" class="page" id="page_f"><?php echo L("L_ALERT_PAGE_F");?></a></td>
      <td><a onclick="PageData(-1)" class="page" id="page_p"><?php echo L("L_ALERT_PAGE_P");?></a></td>
      <td><a onclick="PageData(1)" class="page" id="page_n"><?php echo L("L_ALERT_PAGE_N");?></a></td>
      <td><a onclick="PageData(9)" class="page" id="page_l"><?php echo L("L_ALERT_PAGE_L");?></a></td>
      <td><label id="lab_c_p"><?php echo ($CP); ?></label>/<label id="lab_t_p"><?php echo ($TP); ?></label></td>
      <td><a class="lin_add_" onclick="add_trap_()"><?php echo L("L_AREA_AddTRAP");?></a></td>
    </tr>
  </table>

</body>
</html>