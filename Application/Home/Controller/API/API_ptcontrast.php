<?php


///TMS-cs 节点信息读取
function getTemByPressure ()
{
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$pressure = $Res_Data->pressure ;//压力 单位 Mpa = bar / 10
	$ptcontrast_DAO =  M("ptcontrast");
	$result = $ptcontrast_DAO->where("pressure=".$pressure)->find();
	echo '{"res":"'.$result["temperature"].'"}';
}

function getTemByPressure0 ($pressure)
{
	//$this->WriteLog("log.txt",$pressure."*");
	//压力 单位 Mpa = bar / 10
	if($pressure.""=='' || $pressure.""=='0'){
		$pressure='0.4';
	}
	//$this->WriteLog("log.txt",$pressure."--");
	$ptcontrast_DAO =  M("ptcontrast");
	$result = $ptcontrast_DAO->where("pressure>=".$pressure)->find();
	return $result["temperature"];
}