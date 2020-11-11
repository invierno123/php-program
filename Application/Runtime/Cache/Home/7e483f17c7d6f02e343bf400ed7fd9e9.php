<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <script type="text/javascript" src="/Public/js/jquery-1.7.1.min.js" ></script>
  <!--<script type="text/javascript" src="../../../../Public/js/changePwd.js" ></script>-->
  <style media="screen">
  .tab_search{
    padding: 5px 5px 15px 5px;
    font-size: 12px;
  }
  .tab_search td{
    padding-left: 5px;
  }
  .input_search{
    width: 150px;
    height: 27px;
    border:1px solid #ccc;
    -moz-border-radius: 5px;      /* Gecko browsers */
   -webkit-border-radius: 5px;   /* Webkit browsers */
   border-radius:5px;            /* W3C syntax */
  }
  input, textarea {
      -moz-transition: border 0.2s linear 0s, box-shadow 0.2s linear 0s;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) inset;
  }

  input:focus, textarea:focus {
      border-color: rgba(82, 168, 236, 0.8);
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) inset, 0 0 8px rgba(82, 168, 236, 0.6);
      outline: 0 none;
  }
  </style>
</head>
<body>
  <div style="width:100%; height:30px; font-size:12px" align="left"><b><?php echo L('L_UPDATE_UserInformation');?></b></div>
  <table class="tab_search">
    <tr>
     <td align="left" ><?php echo L('L_UPDATE_CompanyNo');?>：</td>
    <td align="left" ><input id="CompanyNo" type="text" class="input_search" value="<?php echo ($info_list['companyno']); ?>"/></p></td>
    <td align="left" ><?php echo L('L_UPDATE_CompanyName');?>：</td>
   <td align="left" ><input id="CompanyName" type="text" class="input_search"  value="<?php echo ($info_list['companyname']); ?>" /></p></td>
    </tr>
    <tr>
      <td align="left"><?php echo L('L_AU_UM');?>：</td>
     <td align="left"><input id="UserName" type="text" class="input_search"  value="<?php echo ($info_list['username']); ?>" /></p></td>
      <td align="left"><?php echo L('L_UPDATE_UserTel');?>：</td>
     <td align="left"><input id="UserTel" type="text" class="input_search"  value="<?php echo ($info_list['usertel']); ?>" /></p></td>
    </tr>
    <tr>
      <td align="left"><?php echo L('L_UPDATE_CompanyEmail');?>：</td>
     <td align="left"><input id="CompanyEmail" type="text" class="input_search"   value="<?php echo ($info_list['companyemail']); ?>"/></p></td>
     <td align="left"><?php echo L('L_UPDATE_CompanyFax');?>：</td>
    <td align="left"><input id="CompanyFax" type="text" class="input_search"  value="<?php echo ($info_list['companyfax']); ?>" /></p></td>
    </tr>
    <tr>
      <td align="left"><?php echo L('L_UPDATE_CompanyPower');?>：</td>
     <td align="left"><input id="CompanyPower" type="text" class="input_search"  value="<?php echo ($info_list['companypower']); ?>"/></p></td>
      <td align="left"><?php echo L('L_UPDATE_CompanyAdd');?>：</td>
     <td align="left"><input id="CompanyAdd" type="text"  class="input_search"  value="<?php echo ($info_list['companyadd']); ?>"/></p></td>
    </tr>
</table>
</body>
</html>