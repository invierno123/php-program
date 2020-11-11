<?php


//获取泄漏统计信息
function GetAnlyData(){
	$comid = $_GET["ci"]."";
	$AreaID = $_POST["aid"]."";
	$tn= $_POST["tn"]."";
	
	$WHERE_STR = "CompanyID=".$comid;
	if($AreaID!=""){
		$WHERE_STR.=" and AreaID=".$AreaID;
	}
	if($tn!=""){
		$WHERE_STR.=" and TrapNo='".$tn."'";
	}
	
	$AN_RES_DAO = M("analysis");
	$AN_RES_ARRAY = $AN_RES_DAO->where($WHERE_STR)->select();
	$RESJson='{"res":[';
	foreach ($AN_RES_ARRAY as $anres) {
		$RESJson.="{";
		$RESJson.='"id":"'.$anres["id"].'",';
		$RESJson.='"companyid":"'.$anres["companyid"].'",';
		$RESJson.='"trapno":"'.$anres["trapno"].'",';
		$RESJson.='"lastlevel":"'.$anres["lastlevel"].'",';
		$RESJson.='"usemtfi":"'.$anres["usemtfi"].'",';
		$RESJson.='"traptype":"'.$anres["traptype"].'",';
		$RESJson.='"areaid":"'.$anres["areaid"].'",';
		$RESJson.='"area":"'.$anres["area"].'",';
		$RESJson.='"lossvalue":"'.$anres["lossvalue"].'",';
		$RESJson.='"allleak":"'.$anres["allleak"].'",';
		$RESJson.='"datecheck":"'.$anres["datecheck"].'",';
		$RESJson.='"createtime":"'.$anres["createtime"].'"';
		$RESJson.="},";
	}
	$RESJson = rtrim($RESJson,",");
	$RESJson.=']}';
	echo $RESJson;
}
