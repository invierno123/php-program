<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="/Public/js/jquery.1.4.2-min.js"></script>
    <link href="/Public/css/tab_css.css" type="text/css" rel="stylesheet" />
    <link href="/Public/css/trapedit.css" type="text/css" rel="stylesheet" />
    <link href="/Public/css/jquery-fallr-1.3.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/Public/js/jquery-fallr-1.3.pack.js"></script>
      <script language="javascript" type="text/javascript" src="/Public/js/My97DatePicker/WdatePicker.js"></script>
    <title>基本信息设置</title>
    <script>
    $(function(){
      areabind();
      LoadArea(1);
      LoadTrap(1);
      $("btn_esc").hide();
      $("#leftcon .subbox .sublist").attr("style","width:"+($(window).width()-35)+"px;");
    });
    // <!--区域-->
  //  var page_area=1;
    function LoadArea(page){
      var frist_node = $("#tab_list_area").find("tr:first");
      $("#tab_list_area").html("");
      $("#tab_list_area").append(frist_node);
      $.get("/home/AreaInfo/getarealist?page="+page,function(res){

        var res_array=res.split('○');
        $("#tab_list_area").append(res_array[0]);
        $("#lab_c_p_area").text(res_array[1].split(',')[1]);
        $("#lab_t_p_area").text(res_array[1].split(',')[0]);
        PageEable_area();
      });
    }
    function PageEable_area(){
      $("#page_f_area").attr("class","page");
      $("#page_p_area").attr("class","page");
      $("#page_n_area").attr("class","page");
      $("#page_l_area").attr("class","page");
      $("#page_f_area").unbind("click");
      $("#page_p_area").unbind("click");
      $("#page_n_area").unbind("click");
      $("#page_l_area").unbind("click");

      $("#page_f_area").click(function(){PageData_area(0);});
      $("#page_p_area").click(function(){PageData_area(-1);});
      $("#page_n_area").click(function(){PageData_area(1);});
      $("#page_l_area").click(function(){PageData_area(9);});

      if($("#lab_c_p_area").text()=="1"){
        $("#page_f_area").removeAttr("class");
        $("#page_p_area").removeAttr("class");

        $("#page_f_area").attr("class","unpage");
        $("#page_p_area").attr("class","unpage");

        $("#page_f_area").unbind("click");
        $("#page_p_area").unbind("click");
      }
      if($("#lab_c_p_area").text()==$("#lab_t_p_area").text()){
        $("#page_n_area").removeAttr("class");
        $("#page_l_area").removeAttr("class");

        $("#page_n_area").attr("class","unpage");
        $("#page_l_area").attr("class","unpage");

        $("#page_n_area").unbind("click");
        $("#page_l_area").unbind("click");
      }
    }
    function PageData_area(opp){
      if(opp==0){
        $("#lab_c_p_trap").text("1");
      }else if (opp==9) {
        $("#lab_c_p_trap").text($("#lab_t_p_trap").text());

      }else{
          $("#lab_c_p_trap").text(parseInt($("#lab_c_p_trap").text())+opp);
      }
      LoadArea(parseInt($("#lab_c_p_trap").text()));
    }
    // <!--区域结束-->
    // <!--节点-->
  //  var page_trap=1;
  //检查是否数字
  function isNum(str){
    var z= /^[0-9]*$/;
    return z.test(str)
  }
  function isChong(str){
    var z=/^[\u4E00-\u9FA5]+$/;
    return z.test(str)
  }
  function checkInfo(h_bianhao,h_size,h_yali,h_tmax,h_tmin,h_num){
    var h_str="";
    if(isChong(h_bianhao)){
       h_str="节点编号不能使用中文";
       return h_str;
    }
    if(!isNum(h_size)){
      h_str="<?php echo L('L_AREA_trap_guandao');?>"
      return h_str;
    }
    if(!isNum(h_yali)){
      h_str="<?php echo L('L_AREA_trap_yali');?>"
      return h_str;
    }
    if(!isNum(h_tmax)){
      h_str="<?php echo L('L_AREA_trap_gaowen');?>";
      return h_str;
    }
    if(isNaN(h_tmin)){
      h_str="<?php echo L('L_AREA_trap_diwen');?>";
      return h_str;
    }
    if(!isNum(h_num)){
      h_str="<?php echo L('L_AREA_trap_paixu');?>";
      return h_str;
    }
    return h_str;
  }
    function LoadTrap(page){
      var frist_node = $("#tab_list_trap").find("tr:first");
      $("#tab_list_trap").html("");
      $("#tab_list_trap").append(frist_node);
      var area=$("#select_area_in_trap").val();
      var trapno=$("#input_trapno").val();
      var traptype=$("#select_trap_type").val();
      var keys=$("#input_keys").val();
      $.get("/home/AreaInfo/gettraplist?page="+page+"&area="+area+"&trap="+trapno+"&trapty="+traptype+"&keys="+keys,function(res){
        var res_array=res.split('○');
        $("#tab_list_trap").append(res_array[0]);
        $("#lab_c_p_trap").text(res_array[1].split(',')[1]);
        $("#lab_t_p_trap").text(res_array[1].split(',')[0]);
        PageEable_trap();
      });
    }
    function PageEable_trap(){
      $("#page_f_trap").attr("class","page");
      $("#page_p_trap").attr("class","page");
      $("#page_n_trap").attr("class","page");
      $("#page_l_trap").attr("class","page");
      $("#page_f_trap").unbind("click");
      $("#page_p_trap").unbind("click");
      $("#page_n_trap").unbind("click");
      $("#page_l_trap").unbind("click");

      $("#page_f_trap").click(function(){PageData_trap(0);});
      $("#page_p_trap").click(function(){PageData_trap(-1);});
      $("#page_n_trap").click(function(){PageData_trap(1);});
      $("#page_l_trap").click(function(){PageData_trap(9);});

      if($("#lab_c_p_trap").text()=="1"){
        $("#page_f_trap").removeAttr("class");
        $("#page_p_trap").removeAttr("class");

        $("#page_f_trap").attr("class","unpage");
        $("#page_p_trap").attr("class","unpage");

        $("#page_f_trap").unbind("click");
        $("#page_p_trap").unbind("click");
      }
      if($("#lab_c_p_trap").text()==$("#lab_t_p_trap").text()){
        $("#page_n_trap").removeAttr("class");
        $("#page_l_trap").removeAttr("class");

        $("#page_n_trap").attr("class","unpage");
        $("#page_l_trap").attr("class","unpage");

        $("#page_n_trap").unbind("click");
        $("#page_l_trap").unbind("click");
      }
    }
    function PageData_trap(opp){
      if(opp==0){
        $("#lab_c_p_trap").text("1");
      }else if (opp==9) {
        $("#lab_c_p_trap").text($("#lab_t_p_trap").text());

      }else{
          $("#lab_c_p_trap").text(parseInt($("#lab_c_p_trap").text())+opp);
      }
      LoadTrap(parseInt($("#lab_c_p_trap").text()));
    }
    // <!--节点结束-->

    function areabind(){
      $.get("/home/supervisory/getareas",function(res){
        var area_array_=res.split('/');
        for(var i=0;i<area_array_.length;i++){
          if(area_array_[i]==""){break;}
          $("#select_area_in_trap").append("<option value='"+area_array_[i].split(",")[0]+"'>"+area_array_[i].split(",")[1]+"</option>");
        }
      });
    }

    function edit_area(oid) {
      $.fallr("show", {
          content: "<iframe src='/home/AreaInfo/UpdateArea?oid="+oid+"' id='iframe_show_update' name='iframe_show_edit' frameborder='0' width='100%' height='100%'></iframe>",
          position: "center",
          height:"60%",
          width:"50%",
          buttons:{
            button1: {
                text: "<?php echo L('L_ALERT_CANCEL');?>",
                onclick: function () {
                  $.fallr("hide");
                }
            },
            button2: {
                text: "<?php echo L('L_ALERT_OK');?>",
                onclick: function () {
                  var flag=1;
                  var html_updatearea_val = "";
                  var html_updatearea_key = "";
               $(window.frames["iframe_show_edit"].document).find("input:text").each(function(){
                 if($(this).val()==""){
                   flag=0;
                   alert("<?php echo L('L_ALERT_UP_Info_Per');?>");
                   return false;
                 }else{
                        html_updatearea_key+=$(this).attr("id")+",";
                        html_updatearea_val+=$(this).val()+",";
                      //  alert(html_updatearea_key);
                   }
                });
                $(window.frames["iframe_show_edit"].document).find("textarea").each(function(){
                         html_updatearea_key+=$(this).attr("id")+",";
                         html_updatearea_val+=$(this).val()+",";
                 });
               var usertel=$(window.frames["iframe_show_edit"].document).find("#usertel").val();

                var re=/^[0-9]*$/;
                if(!re.test(usertel)){
                 alert("<?php echo L('L_UPDATE_usertel_err');?>");
                  return ;
               }

                if(flag==1){
                 html_updatearea_key+="";

                 html_updatearea_val+=$(window.frames["iframe_show_edit"].document).find("id").text();

                 $.post("/home/AreaInfo/update",{"key":html_updatearea_key,"val":html_updatearea_val,"id":oid},function(res){
                   if(res=="ok"){
                     $.fallr("hide");
                     alert("<?php echo L('L_ALERT_SUCCESS');?>");
                   }else{
                     alert("<?php echo L('L_ALERT_FAIL');?>")
                   };
                  });
                }
                }
              }
      }
    });

    }
    function edit_trap(oid) {
      $.fallr("show", {
          content: "<iframe src='/home/AreaInfo/UpdateTrap?oid="+oid+"' id='iframe_trap_update' name='iframe_trap_update' frameborder='0' width='100%' height='100%'></iframe>",
          position: "center",
          height:"70%",
          width:"60%",
          buttons:{
            button1: {
                text: "<?php echo L('L_ALERT_CANCEL');?>",
                onclick: function () {
                  $.fallr("hide");
                }
            },
            button2: {
                text: "<?php echo L('L_ALERT_OK');?>",
                onclick: function () {
                  var flag=1;
                  var html_addarea_val = "";
                  var html_addarea_key = "";
                  $(window.frames["iframe_trap_update"].document).find("input:text").each(function(){
                    if($(this).val()==""){
                      flag=0;
                      alert("<?php echo L('L_ALERT_UP_Info_Per');?>");
                      return false;
                    }else{
                           html_addarea_key+=$(this).attr("id")+",";
                           html_addarea_val+=$(this).val()+",";
                      }
                   });
                   $(window.frames["iframe_trap_update"].document).find("select").each(function(){
                            html_addarea_key+=$(this).attr("id")+",";
                            html_addarea_val+=$(this).val()+",";
                    });
                var line_size=$(window.frames["iframe_trap_update"].document).find("#LineSize").val();
                var yali=$(window.frames["iframe_trap_update"].document).find("#SPressure").val();
                var temp_max=$(window.frames["iframe_trap_update"].document).find("#STempTop").val();
                var temp_min=$(window.frames["iframe_trap_update"].document).find("#STempLow").val();
                var order_num=$(window.frames["iframe_trap_update"].document).find("#OrderNum").val();
                var trap_num=$(window.frames["iframe_trap_update"].document).find("#trapNo").val();
                if(flag==1){
                  var errmsg=checkInfo(trap_num,line_size,yali,temp_max,temp_min,order_num);
                  if(errmsg==""){
                    //  alert(oid);
                      html_addarea_key+="Area,";
                    //  alert(html_addarea_key);
                      html_addarea_val+=$(window.frames["iframe_trap_update"].document).find("#AreaId").find("option:selected").text()+",";
                    //  alert(html_addarea_val);
                       $.post("/home/AreaInfo/settrap",{"key":html_addarea_key,"val":html_addarea_val,"oid":oid,"type":"update"},function(res){
                          if(res=="0"){
                            alert("<?php echo L('L_ALERT_UP_OperFail_Again');?>");
                          }else if(res=="-2"){
                            alert("<?php echo L('L_AREAINFO_AreaNo_Exist');?>");
                          }else if(res=="-3"){
                            alert("<?php echo L('L_AREAINFO_OrderNum_Exist');?>");
                          }else{
                            $.fallr("hide");
                            alert("<?php echo L('L_ALERT_SUCCESS');?>");
                          }
                      });
                  }else{
                    alert(errmsg);
                  }
                }
          }
        }
      }
    });
    }
    function add_trap(){
      $.fallr("show", {
          content: "<iframe src='/home/AreaInfo/InsertTrap' id='iframe_show_edit' name='iframe_show_edit' frameborder='0' width='100%' height='100%'></iframe>",
          position: "center",
          height:"70%",
          width:"60%",
          buttons:{
            button1: {
                text: "<?php echo L('L_ALERT_CANCEL');?>",
                onclick: function () {
                  $.fallr("hide");
                }
            },
            button2: {
                text: "<?php echo L('L_ALERT_OK');?>",
                onclick: function () {
                  var flag=1;
                  var html_addarea_val = "";
                  var html_addarea_key = "";
               $(window.frames["iframe_show_edit"].document).find("input:text").each(function(){
                 if($(this).val()==""){
                   flag=0;
                   alert("<?php echo L('L_ALERT_UP_Info_Per');?>");
                   return false;
                 }else{
                        html_addarea_key+=$(this).attr("id")+",";
                        html_addarea_val+=$(this).val()+",";
                   }
                });
                $(window.frames["iframe_show_edit"].document).find("select").each(function(){
                         html_addarea_key+=$(this).attr("id")+",";
                         html_addarea_val+=$(this).val()+",";
                 });
                var line_size=$(window.frames["iframe_show_edit"].document).find("#LineSize").val();
                var yali=$(window.frames["iframe_show_edit"].document).find("#SPressure").val();
                var temp_max=$(window.frames["iframe_show_edit"].document).find("#STempTop").val();
                var temp_min=$(window.frames["iframe_show_edit"].document).find("#STempLow").val();
                var order_num=$(window.frames["iframe_show_edit"].document).find("#OrderNum").val();
                var trap_num=$(window.frames["iframe_show_edit"].document).find("#trapNo").val();
                if(flag==1){
                var errmsg=checkInfo(trap_num,line_size,yali,temp_max,temp_min,order_num);
                if(errmsg==""){
                    html_addarea_key+="Area,";
                    html_addarea_val+=$(window.frames["iframe_show_edit"].document).find("#AreaId").find("option:selected").text()+",";
                     $.post("/home/AreaInfo/settrap",{"key":html_addarea_key,"val":html_addarea_val,"type":"add","oid":"0"},function(res){
                        if(res=="0"){
                          alert("<?php echo L('L_ALERT_UP_OperFail_Again');?>");
                        }else if(res=="-2"){
                          alert("<?php echo L('L_AREAINFO_AreaNo_Exist');?>");
                        }else if(res=="-3"){
                          alert("<?php echo L('L_AREAINFO_OrderNum_Exist');?>");
                        }else{
                          $.fallr("hide");
                          alert("<?php echo L('L_ALERT_SUCCESS');?>");
                        }
                    });
              }else{
                alert(errmsg);
              }

            }
			}
		}
		}
		});
    }
    var check_flag=1;
    function checkPwd(str_pwd){
      var reg= /^[A-Za-z0-9]{6,15}$/;
     return reg.test(str_pwd);
    }
    function checkEamil(str_pwd){
      var reg = /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/;
      return reg.test(str_pwd);
    }
    function checkFax(str_pwd){
    //  var reg=/^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/;
    var reg=/^((\+?[0-9]{2,4}\-[0-9]{3,4}\-)|([0-9]{3,4}\-))?([0-9]{7,8})(\-[0-9]+)?$/;
     return reg.test(str_pwd);
    }
    function checkCompanyNo(str_pwd){
       var reg=/^\d{13}$/;
       return reg.test(str_pwd);
    //  return isNaN(str_pwd);
    }
    function checkTel(str_pwd){
    var isPhone = /^([0-9]{3,4}-)?[0-9]{7,8}$/;
    //var isMob=/^((\+?86)|(\(\+86\)))?(13[012356789][0-9]{8}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}|1349[0-9]{7})$/;
  //  var isMob=/^(13[012356789][0-9]{8}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}|1349[0-9]{7})$/;
   var isMob=str_pwd.length;
    if(isMob==11||isPhone.test(str_pwd)){
    	return true;
    }
    else{
    	return false;
     }
    }
    function checkAll(tel,fax,el,comno,h_hours,h_day,h_cname,h_name){
      if(tel==""||fax==""||el==""||comno==""){
        check_flag=0;
        //alert("<?php echo L('L_ALERT_UP_Info_Per');?>");
        return "<?php echo L('L_ALERT_UP_Info_Per');?>";
      }else{
        if(!checkCompanyNo(comno)){
          check_flag=0;
          return "<?php echo L('L_UPDATE_CompanyNo_err');?>";
        }
        if(h_cname.length>20){
          check_flag=0;
           return "<?php echo L('L_UPDATE_CompanyName_err');?>";
        }
        if(h_name.length>15){
          check_flag=0;
           return "<?php echo L('L_AU_UName_ERROR');?>";
        }
        if(!checkEamil(el)){
           check_flag=0;
           return "<?php echo L('L_UPDATE_usereamil_err');?>";
        }
        if(!checkFax(fax)){
          check_flag=0;
          return "<?php echo L('L_UPDATE_userfax_err');?>";
        }
        if(!checkTel(tel)){
           check_flag=0;
           return "<?php echo L('L_UPDATE_usertel_err');?>";
        }
        if(!isNum(h_hours)){
          check_flag=0;
          return "<?php echo L('L_Day_hours_err');?>";
        }
          if(h_hours>24||h_hours<=0){
              check_flag=0;
           return  "<?php echo L('L_Day_hours_err');?>";
          }


        if(!isNum(h_day)){
          check_flag=0;
          return "<?php echo L('L_Year_Day_err');?>";
        }
        if(h_day>365||h_day<=0){
          check_flag=0;
          return "<?php echo L('L_Year_Day_err');?>";
        }
        check_flag=1;
        return "";
      }
    }
    function tijiao(){
      var html_update_val = "";
      var html_update_key = "";
      var flag_html=1;
      var html_tel=$("#company_info").find("#UserTel").val();
      var html_fax=$("#company_info").find("#CompanyFax").val();
      var html_el=$("#company_info").find("#CompanyEmail").val();
      var html_comno=$("#company_info").find("#CompanyNo").val();
      var html_hours=$("#company_info").find("#WorkingHours").val();
      var html_day=$("#company_info").find("#WorkingDays").val();
      var html_cname=$("#company_info").find("#CompanyName").val();
      var html_name=$("#company_info").find("#UserName").val();
      var ch_html_str=checkAll(html_tel,html_fax,html_el,html_comno,html_hours,html_day,html_cname,html_name);
      if(check_flag==1){
      $("#company_info input[type='text']").each(function(){
           if($(this).val()==""){
             flag_html=0;
              alert("<?php echo L('L_ALERT_UP_Info_Per');?>");
             return false;
           }else{
                  html_update_key+=$(this).attr("id")+",";
                  html_update_val+=$(this).val()+",";
             }

      });
      if(flag_html==1&&check_flag==1){
      $.post("/home/Systemset/updateuserinfo",{"key":html_update_key,"val":html_update_val},function(res){
         if(res=="0"){
              alert("<?php echo L('L_ALERT_UP_OperFail_Again');?>");
         }else{
          edit_esc();
          alert("<?php echo L('L_ALERT_SUCCESS');?>");
         }
      });
      }
      }else{
      html_state=1;
      alert(ch_html_str);
      }
    }
    var html_state=0;
    function edit_cominfo(){
    var btn_val=$.trim($("#btn_edit").val());
    if(html_state==0)
    {
      html_state=1;
      $("#btn_edit").val("<?php echo L('L_ALERT_SUBMIT');?>");
      $("#btn_esc").show();
      $("#RegisterDate").click(function(){WdatePicker();});
      $("#company_info input[type='text']").each(function(){
        $(this).attr("readonly",false);
      });
     $("#WorkingDays").attr("placeholder","<?php echo L('L_Day');?>");
     $("#WorkingHours").attr("placeholder","<?php echo L('L_Hours');?>");
    }else{
        //
        html_state=0;
        tijiao();
        //
    }
  }
  function edit_esc(){
	   html_state=0;
    $("#btn_edit").val("<?php echo L('L_AREA_EDIT');?>");
    $("#btn_esc").hide();
  $("#RegisterDate").unbind("click");
    $("#company_info input[type='text']").each(function(){
    $(this).attr("readonly",true);
    });
  $("#WorkingDays").attr("placeholder","");
  $("#WorkingHours").attr("placeholder","");
}
    </script>
  </head>
  <body>
  <div class="demo">
  	<ul class="tabbtn" id="move-animate-left">
  		<li class="current"><a style="cursor:pointer;"><?php echo L("L_AREA_FQSZ");?></a></li>
  		<li><a style="cursor:pointer;"><?php echo L("L_Basic_TRAPSET");?></a></li>
      <li><a style="cursor:pointer;"><?php echo L("L_Company_Info");?></a></li>
  	</ul><!--tabbtn end-->
  	<div class="tabcon" id="leftcon">
  		<div class="subbox">
  			<div class="sublist">
  				<ul>
  					<li>
              <table class="tab_list" id="tab_list_area" cellspadding="0" cellspacing="0" style="margin-top:5px;">
                <tr>
                  <td><?php echo L("L_AREA_FQBH");?></td>
                  <td><?php echo L("L_AREA_FQMC");?></td>
                  <td><?php echo L("L_Basic_USER");?></td>
                  <td><?php echo L("L_Basic_USER_TEL");?></td>
                  <td><?php echo L("L_AREA_FQWZ");?></td>
                  <td><?php echo L("L_Basic_USER_DESCRIPT");?></td>
                  <td><?php echo L("L_AREA_CZ");?></td>
                </tr>
              </table>
              <table class="tab_page">
                <tr>
                  <td><a class="page" id="page_f_area"><?php echo L("L_ALERT_PAGE_F");?></a></td>
                  <td><a class="page" id="page_p_area"><?php echo L("L_ALERT_PAGE_P");?></a></td>
                  <td><a class="page" id="page_n_area"><?php echo L("L_ALERT_PAGE_N");?></a></td>
                  <td><a class="page" id="page_l_area"><?php echo L("L_ALERT_PAGE_L");?></a></td>
                  <td><label id="lab_c_p_area"></label>/<label id="lab_t_p_area"></label></td>
                </tr>
              </table>
            </li>
  				</ul>
  			</div><!--tabcon end-->
  			<div class="sublist">
  				<ul>
            <li>
              <table class="tab_list" cellspadding="0" cellspacing="0" style="border-style:none; ">
                <tr>
                  <td style="border-style:none;"><?php echo L("L_AREA_QY");?>：&nbsp;<select class="input_search" id="select_area_in_trap"><option value=""><?php echo L("L_ALERT_TJ_ALL");?></option></select></td>
                  <td style="border-style:none;"><?php echo L("L_AREA_JDBH");?>：&nbsp;<input id="input_trapno" class="input_search" /></td>
                  <td style="border-style:none;"><?php echo L("L_AREA_FMLX");?>：&nbsp;<input id="select_trap_type" class="input_search" /></td>
                  <td style="border-style:none;"><?php echo L("L_AREA_GJZ");?>：&nbsp;<input id="input_keys" class="input_search" /></td>
                  <td style="border-style:none;"><input type="button" onclick="LoadTrap(1)" class="btn_search" value=" <?php echo L('L_SEARCH_SEARCH');?> "/></td>
                  <td style="border-style:none;"><input type="button" class="btn_search" value=" <?php echo L('L_AREA_ADD');?> " onclick="add_trap()"/></td>
                </tr>
              </table>
            </li>
            <li>&nbsp;</li>
  					<li>
              <table class="tab_list" id="tab_list_trap" cellspadding="0" cellspacing="0" style="margin-top:5px;">
                <tr>
                  <td><?php echo L("L_AREA_JDBH");?></td>
                  <td><?php echo L("L_AREA_QY");?></td>
                  <td><?php echo L("L_AREA_JDMC");?></td>
                  <td><?php echo L("L_AREA_FMLX");?></td>
                  <td><?php echo L("L_AREA_LJLX");?></td>
                  <td><?php echo L("L_AREA_GDKJ");?></td>
                  <td><?php echo L("L_AREA_LCLX");?></td>
                  <!--<td><?php echo L("L_AREA_DQZT");?></td>-->
                  <td><?php echo L("L_AREA_SORT");?></td>
                  <td><?php echo L("L_AREA_CZ");?></td>
                </tr>
              </table>
              <table class="tab_page">
                <tr>
                  <td><a class="page" id="page_f_trap"><?php echo L("L_ALERT_PAGE_F");?></a></td>
                  <td><a class="page" id="page_p_trap"><?php echo L("L_ALERT_PAGE_P");?></a></td>
                  <td><a class="page" id="page_n_trap"><?php echo L("L_ALERT_PAGE_N");?></a></td>
                  <td><a class="page" id="page_l_trap"><?php echo L("L_ALERT_PAGE_L");?></a></td>
                  <td><label id="lab_c_p_trap"></label>/<label id="lab_t_p_trap"></label></td>
                </tr>
              </table>
            </li>
  				</ul>
  			</div><!--tabcon end-->
        <div class="sublist">
          <div id="company_info" style="width:100%; height:30px; font-size:12px" align="left">
            <table class="tab_search">
              <tr>
               <td align="right" ><?php echo L('L_UPDATE_CompanyNo');?>：</td>
              <td align="right" ><input id="CompanyNo" type="text" value="<?php echo ($list['companyno']); ?>" class="input_search_c"  readonly="true"/></p></td>
              <td align="right" ><?php echo L('L_Company_Reg');?>：</td>
    <td align="right" ><input type="text" value="<?php echo ($list['registerdate']); ?>" id="RegisterDate" class="input_search_c"  readonly="true" /></p></td>
              </tr>
              <tr><td height="10px"></td></tr>
              <tr>
                <td align="right"><?php echo L('L_AU_UM');?>：</td>
               <td align="right"><input id="UserName" value="<?php echo ($list['username']); ?>" type="text" class="input_search_c"  readonly="true"/></p></td>
               <td align="right" ><?php echo L('L_UPDATE_CompanyName');?>：</td>
              <td align="right" ><input id="CompanyName" value="<?php echo ($list['companyname']); ?>" type="text" class="input_search_c"  readonly="true"/></p></td>
              </tr>
              <tr><td height="10px"></td></tr>
              <tr>
                <td align="right"><?php echo L('L_UPDATE_CompanyEmail');?>：</td>
               <td align="right"><input id="CompanyEmail" value="<?php echo ($list['companyemail']); ?>" type="text" class="input_search_c"  readonly="true"/></p></td>
               <td align="right"><?php echo L('L_UPDATE_CompanyFax');?>：</td>
              <td align="right"><input id="CompanyFax" value="<?php echo ($list['companyfax']); ?>" type="text" class="input_search_c"  readonly="true"/></p></td>
              </tr>
             <tr><td height="10px"></td></tr>
             <tr>
               <td align="right"><?php echo L('L_UPDATE_UserTel');?>：</td>
              <td align="right"><input id="UserTel" value="<?php echo ($list['usertel']); ?>" type="text" class="input_search_c"  readonly="true"/></p></td>
              <td align="right"><?php echo L('L_UPDATE_CompanyAdd');?>：</td>
             <td align="right"><input id="CompanyAdd" value="<?php echo ($list['companyadd']); ?>" type="text"  class="input_search_c"  readonly="true"/></p></td>
             </tr>
            <tr><td height="10px"></td></tr>
              <tr>
                <td align="right" ><?php echo L('L_Day_hours');?>：</td>
               <td align="right" ><input id="WorkingHours" value="<?php echo ($list['workinghours']); ?>" type="text" class="input_search_c" readonly="true"  /></td>
                <td align="right"><?php echo L('L_Year_Day');?>：</td>
               <td align="right"><input id="WorkingDays" value="<?php echo ($list['workingdays']); ?>" type="text" class="input_search_c"  readonly="true" /></p></td>
              </tr>
              <tr><td height="15px"></td></tr>
              <tr>
                <td align="right" colspan="4">
                  <input type="button" value="<?php echo L('L_ALERT_CANCEL');?>" id="btn_esc" class="btn_search_c" style="display:none" onClick="edit_esc()" />
                  &nbsp;
                  <input id="btn_edit" type="button" value="<?php echo L('L_AREA_EDIT');?>"  onclick="edit_cominfo()"  class="btn_search_c" />
                  </td>
              </tr>
          </table>
          </div>
        </div>
      </div><!--tabcon end-->
  	</div>
    </div><!--tabbox end-->

    <script type="text/javascript" src="/Public/js/jquery.tabso_yeso.js"></script>
    <script type="text/javascript">
    $(document).ready(function($){

      $(".tabcon").css("height",$(document).height()-45);
    	//上下滑动选项卡切换
    	$("#move-animate-top").tabso({
    		cntSelect:"#topcon",
    		tabEvent:"mouseover",
    		tabStyle:"move-animate",
    		direction : "top"
    	});

    	//左右滑动选项卡切换
    	$("#move-animate-left").tabso({
    		cntSelect:"#leftcon",
    		tabEvent:"mouseover",
    		tabStyle:"move-animate",
    		direction : "left"
    	});
    	//淡隐淡现选项卡切换
    	$("#fadetab").tabso({
    		cntSelect:"#fadecon",
    		tabEvent:"mouseover",
    		tabStyle:"fade"
    	});

    	//默认选项卡切换
    	$("#normaltab").tabso({
    		cntSelect:"#normalcon",
    		tabEvent:"mouseover",
    		tabStyle:"normal"
    	});

    });
    </script>
  </body>
</html>