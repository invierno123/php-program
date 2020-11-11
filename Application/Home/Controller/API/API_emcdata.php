<?php


//TMS-CS  查询数据如果没有数据添加
function findData(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$Dao=M("emcdata");
	$companyid=$Res_Data->cid;
	$condition["CompanyID"]=$companyid;
	$data_select=$Dao->where($condition)->find();
	if($data_select){
		$show_list_str='{"res":';
		$show_list_str.='[{"powerUnit":"'.$data_select["powerunity"].'",';
		$show_list_str.='"PowerName":"'.$data_select["powername"].'",';
		$show_list_str.='"powerUsage":"'.$data_select["powernumber"].'",';
		$show_list_str.='"powerPrice":"'.$data_select["powerprice"].'",';
		$show_list_str.='"currency":"'.$data_select["currency"].'",';
		$show_list_str.='"powerRatio":"'.$data_select["expendscale"].'",';
		//date("Y-m-d",strtotime($warning_each["repairtime"]))
		//"\/Date('.date("Y-m-d H:i",strtotime($warning_each["repairtime"])).')\/",';
		$show_list_str.='"Yproduct":"'.$data_select["fristproductiony"].'",';
		$show_list_str.='"Mproduct":"'.$data_select["fristproductionm"].'",';
		$show_list_str.='"jia":"'.$data_select["jia"].'",';
		$show_list_str.='"yi":"'.$data_select["yi"].'",';
		$show_list_str.='"qita":"'.$data_select["qita"].'",';
		$show_list_str.='"Yconsume":"'.$data_select["fristexpendy"].'",';
		$show_list_str.='"Mconsume":"'.$data_select["fristexpendm"].'",';
		$show_list_str = rtrim($show_list_str,",");
		$show_list_str.="}]";
		$show_list_str.='}';
		echo $show_list_str;
	}else {
		echo '{"res":"0"}';
	}
}


//TMS-CS  保存初年数据
function  savaExcel(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$companyid=$Res_Data->cid;
	$powerUnit=$Res_Data->powerUnit;
	$powerName=$Res_Data->powerName;
	$powerUsage=$Res_Data->powerUsage;
	$powerPrice=$Res_Data->powerPrice;
	$powerRatio=$Res_Data->powerRatio;
	$currency=$Res_Data->currency;
	$jia=$Res_Data->jia;
	$yi=$Res_Data->yi;
	$qita=$Res_Data->qita;
	$Yproduct=$Res_Data->Yproduct;
	$Mproduct=$Res_Data->Mproduct;
	$Yconsume=$Res_Data->Yconsume;
	$Mconsume=$Res_Data->Mconsume;
	$Dao=M("emcdata");
	$data["CompanyID"]=$companyid;
	$data["PowerUnity"]=$powerUnit;
	$data["PowerName"]=$powerName;
	$data["PowerNumber"]=$powerUsage;
	$data["PowerPrice"]=$powerPrice;
	$data["Currency"]=$currency;
	$data["FristExpendY"]=$Yconsume;
	$data["FristExpendM"]=$Mconsume;
	$data["ExpendScale"]=$powerRatio;
	$data["FristProductionY"]=$Yproduct;
	$data["FristProductionM"]=$Mproduct;

	$data["Jia"]=$jia;
	$data["Yi"]=$yi;
	$data["Qita"]=$qita;
	$result=$Dao->add($data);
	if($result){
		echo '{"res":"1"}';
	}
	else{
		echo '{"res":"0"}';
	}
}
