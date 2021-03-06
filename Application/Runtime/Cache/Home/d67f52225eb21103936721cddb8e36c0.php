<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="/Public/js/jquery-1.7.1.min.js"></script>
<link rel="stylesheet" href="/Public/css/usermanger.css" type="text/css" media="screen" />
<link href="/Public/css/jquery-fallr-1.3.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/js/jquery-fallr-1.3.pack.js" ></script>
<title><?php echo L('L_AU_UserManager');?></title>
</head>
<script>
 load_dispaly();
$(function(){
   load_dispaly();
});
function load_dispaly(){
  var now_page=GetQueryString("page");
  var all_apge=<?php echo ($page_all); ?>;
  if(now_page==1){
    $("#page1").removeAttr("href");
    $("#page1").attr("class","dis");
    $("#page2").removeAttr("href");
    $("#page2").attr("class","dis");
    $("#page3").attr("href","/home/systemset/usermanager?page=<?php echo ($page_down); ?>");
    $("#page3").attr("class","notdis");
    $("#page4").attr("href","/home/systemset/usermanager?page=<?php echo ($page_all); ?>");
    $("#page4").attr("class","notdis");
  }
  if(now_page>1&&now_page<all_apge){
    $("#page1").attr("href","/home/systemset/usermanager?page=1");
    $("#page1").attr("class","notdis");
    $("#page2").attr("href","/home/systemset/usermanager?page=<?php echo ($page_up); ?>");
    $("#page2").attr("class","notdis");
    $("#page3").attr("href","/home/systemset/usermanager?page=<?php echo ($page_down); ?>");
    $("#page3").attr("class","notdis");
    $("#page4").attr("href","/home/systemset/usermanager?page=<?php echo ($page_all); ?>");
    $("#page4").attr("class","notdis");
  }
  if(now_page==1&&all_apge<1){
    $("#page1").removeAttr("href");
    $("#page1").attr("class","dis");
    $("#page2").removeAttr("href");
    $("#page2").attr("class","dis");
    $("#page3").removeAttr("href");
    $("#page3").attr("class","dis");
    $("#page4").removeAttr("href");
    $("#page4").attr("class","dis");
  }
  if(now_page==all_apge){
    $("#page3").removeAttr("href");
    $("#page3").attr("class","dis");
    $("#page4").removeAttr("href");
    $("#page4").attr("class","dis");
  }
}

function checkAll(uname,lname,npwd,spwd,tel,el){
  if(uname.length>15){
    return "<?php echo L('L_AU_UName_ERROR');?>";
  }
  if(lname.length>15){
    return "<?php echo L('L_AU_LName_ERROR');?>";
  }
  if(npwd!=spwd){
    return "<?php echo L('L_ALERT_UP_Pwd_No');?>";
  }
  if(npwd!=""&&spwd!=""){
    if(!checkPwd(npwd)){
       return "<?php echo L('L_ALERT_UP_PWD_Must');?>";
    }
  }
	if(!checkTel(tel)){
	   return "<?php echo L('L_UPDATE_usertel_err');?>";
	}
	if(!checkEamil(el)){
	   return "<?php echo L('L_UPDATE_usereamil_err');?>";
	}
	return "";
}
function checkPwd(str_pwd){
  var reg= /^[A-Za-z0-9]{6,15}$/;
 return reg.test(str_pwd);
}
function checkEamil(str_pwd){
  var reg = /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/;
  return reg.test(str_pwd);
}
function checkTel(str_pwd){
  var isPhone = /^([0-9]{3,4}-)?[0-9]{7,8}$/;
  var isMob=str_pwd.length;
  if(isMob==11||isPhone.test(str_pwd)){
  	return true;
  }
  else{
  	return false;
   }
}
function GetQueryString(name)
 {
      var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
      var r = window.location.search.substr(1).match(reg);
      if(r!=null)return  unescape(r[2]); return null;
 }
 function disable_user(userid,userstatus){
   var type_=(userstatus=="1")?"0":"1";
   $.get("/home/Systemset/disableuser?oid="+userid+"&type="+type_,function(res){
      if(res=="1"){
         location="/home/systemset/usermanager?l"+GetQueryString("l")+"page="+GetQueryString("page");
      }else{
        alert("<?php echo L('L_ALERT_FAIL');?>");
      }
   });
 }
 function del_user(userid){
   $.get("/home/Systemset/deluser?oid="+userid,function(res){
      if(res=="1"){
          location="/home/systemset/usermanager?l"+GetQueryString("l")+"page="+GetQueryString("page");
      }else{
        alert("<?php echo L('L_ALERT_FAIL');?>");
      }
   });
 }
function edit_user(user_id){
  $.fallr("show", {
      content: "<iframe src='/home/Systemset/updatesubuser?oid="+user_id+"' id='iframe_show_edit' name='iframe_show_edit' frameborder='0' width='100%' height='100%'></iframe>",
      position: "center",
      height:"50%",
      width:"63%",
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
              var html_update_key="";
              var html_update_val="";
              var flag=0;
              var newpwd=$(window.frames["iframe_show_edit"].document).find("#PassWord").val();
              var surepwd=$(window.frames["iframe_show_edit"].document).find("#sure_pwd").val();
              var tel_=$(window.frames["iframe_show_edit"].document).find("#UserTel").val();
              var email_=$(window.frames["iframe_show_edit"].document).find("#UserEmail").val();
              var name_=$(window.frames["iframe_show_edit"].document).find("#UserName").val();
              var lname=$(window.frames["iframe_show_edit"].document).find("#LoginName").val();
              if(tel_==""||email_==""||name_==""||lname==""){
                flag=1;
                alert("<?php echo L('L_ALERT_UP_Info_Per');?>");
                return;
              }else{
                var str_msg=checkAll(name_,lname,newpwd,surepwd,tel_,email_);
                if(str_msg==""){
                  $(window.frames["iframe_show_edit"].document).find("input:text").each(function(){
                        html_update_key+=$(this).attr("id")+",";
                        html_update_val+=$(this).val()+",";
                  });
                  if(newpwd!=""){
                    html_update_key+="PassWord,";
                    html_update_val+=newpwd+",";
                  }
                  $.post("/home/Systemset/subuser_control",{"key":html_update_key,"val":html_update_val,"type":"update","oid":user_id},function(res){
                    if(res=="-1"){
                       alert("<?php echo L('L_AU_LM');?>");
                    }else if(res=="2"){
                       alert("<?php echo L('L_ALERT_FAIL');?>");
                    }else{
                      $.fallr("hide");
                      alert("<?php echo L('L_ALERT_SUCCESS');?>");
                       location="/home/systemset/usermanager?l"+GetQueryString("l")+"page="+GetQueryString("page");
                    }
                  });
                }else{
                  alert(str_msg);
                }
              }
            }
        }
      }
    });
}
function adduser(){
  $.fallr("show", {
      content: "<iframe src='/home/Systemset/addsubuser' id='iframe_show_add' name='iframe_show_add' frameborder='0' width='100%' height='100%'></iframe>",
      position: "center",
      height:"50%",
      width:"63%",
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
              var html_update_key="";
              var html_update_val="";
              var flag=0;
              var newpwd=$(window.frames["iframe_show_add"].document).find("#PassWord").val();
              var surepwd=$(window.frames["iframe_show_add"].document).find("#sure_pwd").val();
              var tel_=$(window.frames["iframe_show_add"].document).find("#UserTel").val();
              var email_=$(window.frames["iframe_show_add"].document).find("#UserEmail").val();
              var name_=$(window.frames["iframe_show_add"].document).find("#UserName").val();
              var lname=$(window.frames["iframe_show_add"].document).find("#LoginName").val();
              if(tel_==""||email_==""||name_==""||lname==""||newpwd==""||surepwd==""){
                flag=1;
                alert("<?php echo L('L_ALERT_UP_Info_Per');?>");
                return;
              }else{
                var str_msg=checkAll(name_,lname,newpwd,surepwd,tel_,email_);
                if(str_msg==""){
                  $(window.frames["iframe_show_add"].document).find("input:text").each(function(){
                        html_update_key+=$(this).attr("id")+",";
                        html_update_val+=$(this).val()+",";
                  });
                    html_update_key+="PassWord,";
                    html_update_val+=newpwd+",";
                  $.post("/home/Systemset/subuser_control",{"key":html_update_key,"val":html_update_val,"type":"add"},function(res){
                    if(res=="-1"){
                       alert("<?php echo L('L_AU_LM');?>");
                    }else if(res=="2"){
                       alert("<?php echo L('L_ALERT_FAIL');?>");
                    }else{
                      $.fallr("hide");
                      alert("<?php echo L('L_ALERT_SUCCESS');?>");
                       location="/home/systemset/usermanager?l"+GetQueryString("l")+"page="+GetQueryString("page");
                    }
                  });
                }else{
                  alert(str_msg);
                }
              }
            }
        }
      }
    });
}
</script>
<body>

   <div style="margin-top:10px;margin-right:10px;"><input type="button" value="<?php echo L('L_AU_AddUser');?>" class="add" id="add_user" onClick="adduser()" /></div>
  <table border="0" cellspacing="0"  cellpadding="0"  class="tab" >
  <tr>
  <td style="text-align: center;color:#222;"><?php echo L('L_AU_UM');?></td>
  <td style="text-align: center;color:#222;"><?php echo L('L_UPDATE_LOGINNAME');?></td>
  <td style="text-align: center;color:#222;"><?php echo L('L_UPDATE_UserTel');?></td>
  <td style="text-align: center;color:#222;"><?php echo L('L_AU_UserEmail');?></td>
  <td style="text-align: center;color:#222;"><?php echo L('L_LIST_SHOW_STATE');?></td>
  <td style="text-align: center;color:#222;"><?php echo L('L_AU_LastLogin_Time');?></td>
  <td style="text-align: center;color:#222;" colspan="3"><?php echo L('L_AREA_CZ');?></td>
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr name="tr_state" >
   <td><?php echo ($vo['username']); ?></td>
   <td><?php echo ($vo['loginname']); ?></td>
   <td><?php echo ($vo["usertel"]); ?></td>
   <td><?php echo ($vo["useremail"]); ?></td>
   <td><?php echo ($vo["state"]); ?></td>
   <td ><?php echo ($vo["lastlogintime"]); ?></td>
   <!--<td style="display:none"><?php echo ($vo['state']); ?></td>-->
   <td><a class="control"  onclick="# Host: localhost  (Version: 5.5.40)
    <td><a class="control" onclick="disable_user(<?php echo ($vo['id']); ?>,<?php echo ($vo['status']); ?>)"><?php echo ($vo["control"]); ?><a></td>
   <td><a class="control"  onclick="del_user(<?php echo ($vo['id']); ?>)"><?php echo L('L_ALERT_DELETE');?><a></td>
   </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>
  <table class="page">
   <tr>
       <td><a id="page1" class="notdis"  href="/home/systemset/usermanager?page=1"} ><?php echo L('L_ALERT_PAGE_F');?></a></td>
       <td><a id="page2" class="notdis" href="/home/systemset/usermanager?page=<?php echo ($page_up); ?>"}><?php echo L('L_ALERT_PAGE_P');?></a></td>
       <td><a id="page3" class="notdis" href="/home/systemset/usermanager?page=<?php echo ($page_down); ?>"} ><?php echo L('L_ALERT_PAGE_N');?></a></td>
       <td><a id="page4"  class="notdis" href="/home/systemset/usermanager?page=<?php echo ($page_all); ?>"><?php echo L('L_ALERT_PAGE_L');?></a></td>
       <td id="td_data"><?php echo L('L_ALERT_PAGE_DI');?> <?php echo ($page); ?>/<?php echo ($page_all); ?> <?php echo L('L_ALERT_PAGE_YE');?>,<?php echo L('L_ALERT_PAGE_GONG');?> <?php echo ($count); ?> <?php echo L('L_ALERT_PAGE_SHUJU');?></td>
   </tr>
  </table>
</body>
</html>