<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>系统设置区域设置</title>
<link href="/Public/css/setmain.css" rel="stylesheet" type="text/css" />
<link href="/Public/css/jquery-fallr-1.3.css" rel="stylesheet" type="text/css" />
<link href="/Public/js/artDialog-5.0.3/skins/simple.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/Public/js/ui/ui.core.min.js"></script>
<script type="text/javascript" src="/Public/js/ui/ui.sortable.min.js"></script>
<script type="text/javascript" src="/Public/js/jquery-fallr-1.3.pack.js"></script>
<script type="text/javascript" src="/Public/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="/Public/js/artDialog-5.0.3/artDialog.min.js"></script>
<script>
var SJCF = '<?php echo L("L_DATA_REP");?>';
var FANGDA='<?php echo L("L_ALERT_MAX");?>';
var SUOXIAO='<?php echo L("L_ALERT_MIN");?>';
var DQBJ = '<?php echo L("L_AREA_LAYOUT_SET");?>';
var QU_ = "<?php echo L('L_AREA_QY');?>";
var QUXIAO = "<?php echo L('L_ALERT_CANCEL');?>";
var QUEDING = "<?php echo L('L_ALERT_OK');?>";
var SHANCHU='<?php echo L("L_ALERT_DELETE");?>';
var FQMC = '<?php echo L("L_AREA_FQMC");?>';
var FQBH = '<?php echo L("L_AREA_FQBH");?>';
var FQWZ = '<?php echo L("L_AREA_FQWZ");?>';
var ZB = '<?php echo L("L_AREA_L_ZB");?>';
var ZJ = '<?php echo L("L_AREA_L_ZJ");?>';
var YB = '<?php echo L("L_AREA_L_YB");?>';
var CZSB = '<?php echo L("L_ALERT_FAIL");?>';
var QDSC = '<?php echo L("L_ALERT_DELETE_CONFIRM");?>';

var BJSZ = '<?php echo L("L_AREA_BJSZ");?>';
var FQSZ = '<?php echo L("L_AREA_FQSZ");?>';
var TJFQ = '<?php echo L("L_AREA_TJFQ");?>';
var BCPZ = '<?php echo L("L_AREA_BPPZ");?>';
var MR = '<?php echo L("L_ALERT_DEFAULT");?>';
var JDXX = '<?php echo L("L_AREA_JDXX");?>';
var BJJD = '<?php echo L("L_AREA_BJJD");?>';
var LMC = <?php echo ($LMC); ?>;
var MMC = <?php echo ($MMC); ?>;
var RMC = <?php echo ($RMC); ?>;
</script>
<script type="text/javascript" src="/Public/js/Jh.js"></script>
<script>
var active_areaid="";
var areaid = "area_<?php echo ($MAXID); ?>";
function show_trapinfo(oid){
	//alert(active_areaid);
	$.fallr("show", {
			content: "<iframe src='/home/Supervisory?t=line&areacode="+active_areaid+"' frameborder='0' width='100%' height='100%'></iframe>",
			position: "center",
			height:"90%",
			width:"90%",
			buttons:{
				button1: {
						text: "<?php echo L('L_ALERT_CANCEL');?>",
						onclick: function () {
							$.fallr("hide");
						}
				}
			}
	});
}
/*添加节点*/
function add_trap(){
	$.fallr("show", {
			content: "<iframe id='ifra_jd' name='ifra_jd' src='/home/Systemset/TrapEdit?areacode="+active_areaid+"' frameborder='0' width='100%' height='100%'></iframe>",
			position: "center",
			height:"90%",
			width:"90%",
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
							var fram_documen = $(window.frames["ifra_jd"].document);
							var areaid =fram_documen.find("#hidden_areaid").val();
							var id_str="";
							var sort_str="";
							fram_documen.find("select[name='select_trap']").each(function(){
								id_str+=$(this).val()+",";
							});
							fram_documen.find("input[name='input_sort']").each(function(){
								sort_str+=$(this).val()+",";
							});
							$.get("/home/Systemset/updatetrap?ida="+id_str+"&aid="+areaid+"&sort="+sort_str,function(res){
								$.fallr("hide");
							});
							//fram_documen.find("input[name='input_sort']")
						}
				}
			}
	});
}
$(document).ready(function () {

	var DATA = {
		'appL' : {
			<?php echo ($Al_L); ?>
		},
		'appM' :{
			<?php echo ($Al_M); ?>
		},
		'appR' : {
			<?php echo ($Al_R); ?>
		}
	}
	Jh.fn.init(DATA);
	Jh.Portal.init(DATA);
});

$(function(){
	$("div .groupItem").mouseover(function(){
		active_areaid=$(this).attr("id");
	});
	$("div .groupItem").mouseout(function(){
		active_areaid="";
	});
	$(".tag-list li a").each(function(){
		$(this).html($(this).text().split("·")[0]);
	});
	$("#img_max").click(function(){
		$(this).hide();
		$("#img_min").show();
		$(window.parent.document.getElementById("fs_1")).attr("rows","0,*");
		$(window.parent.document.getElementById("fs_2")).attr("cols","1,*");
	});
	$("#img_min").click(function(){
		$(this).hide();
		$("#img_max").show();
		$(window.parent.document.getElementById("fs_1")).attr("rows","10%,90%");
		$(window.parent.document.getElementById("fs_2")).attr("cols","20%,80%");
	});
	var Settedstyle="<?php echo ($layoutStyle); ?>";
	$(".layout-list a").each(function(){
		if($(this).attr("rel")==Settedstyle){
			$(this).click();
		}
	});
});

</script>
</head>
<body>
<h1 style="font-size:15px;margin-left:10px;"><?php echo L('L_MENU_SYSTEM_AREA');?></h1>
<img id='img_max' style="position:absolute; top:25px; right:25px; cursor:pointer;" title='<?php echo L("L_ALERT_MAX");?>' src="/Public/images/max_screen.png"/>
<img id='img_min' style="position:absolute; top:25px; right:25px; cursor:pointer; display:none;" title='<?php echo L("L_ALERT_MIN");?>' src="/Public/images/small_screen.png"/>
<div style="text-align:center;margin:50px 0; font:normal 14px/24px 'MicroSoft YaHei';">
</div>


</body>
</html>