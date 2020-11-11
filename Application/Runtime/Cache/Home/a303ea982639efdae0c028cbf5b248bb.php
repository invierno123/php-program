<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="/Public/css/menu.css">
  <script>
  function gourl(url_str){
    window.parent.frames["fram_main"].location=url_str;
  }
  </script>
</head>
<body>
  <ul class="dropdown">
        <li>
          <a href="#"><?php echo L("L_MENU_DATA");?></a>
          <ul>
            <li><a onclick="gourl('/home/InputData')"><?php echo L("L_MENU_DATA_P");?></a></li>
          </ul>
        </li>
        <li>
          <a href="#"><?php echo L("L_MENU_SUPER");?></a>
          <ul>
            <li><a onclick="gourl('/home/Supervisory?t=list')"><?php echo L("L_MENU_SUPER_LIST");?></a></li>
            <li><a onclick="gourl('/home/Supervisory?t=line')"><?php echo L("L_MENU_SUPER_PIC");?></a></li>
            <li><a onclick="gourl('/home/Supervisory/showmax2d')"><?php echo L("L_MENU_SUPER_2D");?></a></li>
            <li><a onclick="gourl('/home/Supervisory?t=list1')"><?php echo L("L_MENU_SUPER_change");?></a></li>
          </ul>
        </li>
        <li>
          <a onclick="gourl('/home/warning/index?page=1')"><?php echo L("L_MENU_WAR");?></a>
        </li>
        <li>
          <a onclick="gourl('/home/emc/setdata')"><?php echo L("L_MENU_EMC");?></a>
        </li>
        <li>
          <a onclick="gourl('/home/AreaInfo/TrapAnalysis')"><?php echo L("L_MENU_SYSTEM_Analysis");?></a>
        </li>
        <li>
          <a href="#"><?php echo L("L_MENU_SYSTEM");?></a>
          <ul>
            <li><a onclick="gourl('/home/AreaInfo')"><?php echo L("L_MENU_SYSTEM_BASIC");?></a></li>
            <li><a onclick="gourl('/home/Systemset')"><?php echo L("L_MENU_SYSTEM_AREA");?></a></li>
            <li><a onclick="gourl('/home/Systemset/changepwd')"><?php echo L("L_MENU_SYSTEM_PASS");?></a></li>
            <?php echo ($menu_add); ?>
          </ul>
        </li>
      </ul>

      <script src="/Public/js/jquery.js"></script>
      <script src="/Public/js/tendina.js"></script>
      <script>
        $('.dropdown').tendina({
          animate: true,
          speed: 500,
          openCallback: function($clickedEl) {
            console.log($clickedEl);
          },
          closeCallback: function($clickedEl) {
            console.log($clickedEl);
          }
        })
      </script>
</body>
</html>