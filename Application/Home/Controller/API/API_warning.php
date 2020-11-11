<?php

// 单位换算
///$TYPE_  0  压力   1 口径  2 温度  3 资源  4 泄漏  $LX 0 转为原始单位(非读取)
function DWHS_wr($TYPE_, $U, $LX,$companyid)
{
	//$this->WriteLog("log1111111.txt",$TYPE_."*".$U."*".$LX."*".$companyid);
	$TYPE_=$TYPE_."";
	///////////////////////单位换算//////////////////////////////
	$UNITCOMPANYDAO = M("companyinfo");

	$UNITRES = $UNITCOMPANYDAO->where("id=".$companyid)->find();

	$SP_U = $UNITRES["spunit"];//压力
	$TE_U = $UNITRES["temunit"];//温度
	$SIZE_U = $UNITRES["sizeunit"];//口径
	$LOSS_U = $UNITRES["lossunit"];//泄漏
	$PW_U = $UNITRES["pwunit"];//资源
	

	$SP_ARRAY = array("bar"=>1,"kpa"=>100,"kg/cm2"=>1.0197,"mmhg"=>750.06);
	$SIZE_ARRAY = array("mm"=>1,"cm"=>0.1);
	
	$TEM_ARRAY = array("℃"=>1,"℉"=>1.8,"K"=>1);
	$TEM_BC = array("℃"=>0,"℉"=>32,"K"=>273);
	
	$PW_ARRAY = array("ton"=>1,"kg"=>1000,"lb"=>2679.23);
	$LOSS_ARRAY = array("kg/h"=>1,"ton/h"=>0.001);

	$RES_ = 0;
	if($TYPE_=="0")
	{
		if($LX==0){
			$RES_=((int)$U)/((double)$SP_ARRAY[strtolower($UNITRES["spunit"])]);
		}else{
			$RES_=((int)$U)*((double)$SP_ARRAY[strtolower($UNITRES["spunit"])]);
		}
	}
	else if($TYPE_=="1")
	{
		if($LX==0){
			$RES_=((int)$U)/((double)$SIZE_ARRAY[strtolower($UNITRES["sizeunit"])]);
		}else{
			$RES_=((int)$U)*((double)$SIZE_ARRAY[strtolower($UNITRES["sizeunit"])]);
		}
	}
	else if($TYPE_=="2")
	{
		if($LX==0){
			$RES_=(((int)$U) -((double)$TEM_BC[strtolower($UNITRES["temunit"])])) /((double)$TEM_ARRAY[strtolower($UNITRES["temunit"])]);
		}else{
			$RES_=(((int)$U)) *((double)$TEM_ARRAY[strtolower($UNITRES["temunit"])])+((double)$TEM_BC[strtolower($UNITRES["temunit"])]);
		}
	}
	else if($TYPE_=="3")
	{
		if($LX==0){
			$RES_=((int)$U)/((double)$PW_ARRAY[strtolower($UNITRES["pwunit"])]);
		}else{
			$RES_=((int)$U)*((double)$PW_ARRAY[strtolower($UNITRES["pwunit"])]);
		}
		
	}
	else if($TYPE_=="4")
	{
		if($LX==0){
			$RES_=((int)$U)/((double)$LOSS_ARRAY[strtolower($UNITRES["lossunit"])]);
		}else{
			$RES_=((int)$U)*((double)$LOSS_ARRAY[strtolower($UNITRES["lossunit"])]);
		}
	}
	return $RES_;
	///////////////////////////////////////////////////
}

///TMS-CS获取异常节点
function GetAlertTrap(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$companyid = $Res_Data->cid;
	$AlertDao = M("warning");
	$res_alert = $AlertDao->where("companyid=".$companyid." and RepairState=0 and LearnState=0")->select();
	$show_list_str = '{"res":[';
	foreach ($res_alert as $alert) {
		$show_list_str.='{"area":"'.$alert["area"].'","trapno":"'.$alert["trapno"].'","tem":"'.$alert["alerttem"].'","hz":"'.$alert["alerthz"].'","time":"'.$alert["createtime"].'"},';
	}
	$show_list_str=rtrim($show_list_str,",");
	$show_list_str .= ']}';
	echo $show_list_str;
}

//TMS-CS 报警列表误报
function ErrorReport(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$Dao_Warning= M("warning");
	$companyid=$Res_Data->cid;
	$errorid=$Res_Data->errorid;
	$warning_res = $Dao_Warning->where("Id=".$errorid." and companyid=".$companyid)->find();
	$show_list_str='{"res":';
	$show_list_str.='[{"id":"'.$warning_res["id"].'",';
	$show_list_str.='"trapno":"'.$warning_res["trapno"].'",';
	$show_list_str.='"alerttem":"'.$warning_res["alerttem"].'",';
	$show_list_str.='"alerthz":"'.$warning_res["alerthz"].'",';
	$show_list_str.='"standardtem":"'.$warning_res["standardtem"].'",';
	$show_list_str.='"location":"'.$warning_res["location"].'",';
	$show_list_str = rtrim($show_list_str,",");
	$show_list_str.="}]";
	$show_list_str.='}';
	echo $show_list_str;
}

//TMS-CS  报警列表编辑
function WarningEdit(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$Dao_Warning= M("warning");
	$companyid=$Res_Data->cid;
	$repairid=$Res_Data->repairid;
	$warning_res = $Dao_Warning->where("Id=".$repairid." and companyid=".$companyid)->find();
	$show_list_str='{"res":';
	$show_list_str.='[{"id":"'.$warning_res["id"].'",';
	$show_list_str.='"area":"'.$warning_res["area"].'",';
	$show_list_str.='"trapno":"'.$warning_res["trapno"].'",';
	$show_list_str.='"location":"'.$warning_res["location"].'",';
	$RST = $warning_res["repairstate"]=="1"?L("L_ALERT_HANDLED"):L("L_ALERT_HANDLING");
	$show_list_str.='"repairstate":"'.$RST.'",';
	$RRT=$warning_res["repairtype"]=="1"?L("L_ALERT_REPAIR"):L("L_ALERT_REPLACE");
	$show_list_str.='"repairtype":"'.$RRT.'",';

	$show_list_str.='"repairtime":"'.$warning_res["repairtime"].'",';
	//date("Y-m-d",strtotime($warning_each["repairtime"]))
	//"\/Date('.date("Y-m-d H:i",strtotime($warning_each["repairtime"])).')\/",';
	$show_list_str.='"repairnum":"'.$warning_res["repairnum"].'",';
	$show_list_str.='"repairprice":"'.$warning_res["repairprice"].'",';
	$TST = $warning_res["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");
	$show_list_str.='"trapstate":"'.$TST.'",';
	$show_list_str.='"exlevel":"'.$warning_res["exlevel"].'",';
	$show_list_str.='"leveldesc":"'.$warning_res["leveldesc"].'",';
	$show_list_str.='"alerttem":"'.$warning_res["alerttem"].'",';
	$show_list_str.='"alerthz":"'.$warning_res["alerthz"].'",';
	$show_list_str.='"standardtem":"'.$warning_res["standardtem"].'",';
	$show_list_str = rtrim($show_list_str,",");
	$show_list_str.="}]";
	$show_list_str.='}';
	//$lists=json_encode($warning_res);
	//$arr=array("res"=>"1","data"=>$lists);
	echo $show_list_str;
	//echo json_encode($arr);

}

//TMS-CS  报警列表处理
function WarningUpdate(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$Dao_Warning= M("warning");
	$companyid=$Res_Data->cid;
	$repairid=$Res_Data->repairid;
	$area=$Res_Data->area;
	$trapno=$Res_Data->trapno;
	$location=$Res_Data->location;
	$repairtime=$Res_Data->repairtime;
	$repairtype=$Res_Data->repairtype;
	$repairstate=$Res_Data->repairstate;
	$trapstate=$Res_Data->trapstate;
	$repairnum=$Res_Data->repairnum;
	$repairprice=$Res_Data->repairprice;
	$exlevel=$Res_Data->exlevel;
	$leveldesc=$Res_Data->leveldesc;
	$alerttem=$Res_Data->alerttem;
	$alerthz=$Res_Data->alerthz;
	$standardtem=$Res_Data->standardtem;
	$repairdescription=$Res_Data->repairdescription;
	$condition["Id"]=$repairid;

	$data["CompanyId"]=$companyid;
	//  $data['AreaId']=$_POST['areaid'];
	$data['Area']=$area;
	$data['TrapNo']=$trapno;
	$data['Location']=$location;
	$data['RepairState']=$repairstate;
	$data['RepairType']=$repairtype;
	$data['RepairTime']=$repairtime;
	$data['RepairNum']=$repairnum;
	$data['RepairPrice']=$repairprice;
	$data['TrapState']=$trapstate;
	$data['ExLevel']=$exlevel;
	$data['LevelDesc']=$leveldesc;
	$data['AlertTem']=$alerttem;
	$data['AlertHZ']=$alerthz;
	$data['StandardTem']=$standardtem;
	$data['RepairDescription']=$repairdescription;
	$result =$Dao_Warning->where($condition)->save($data);
	if($result!=false && $result>0){
		echo '{"res":"1"}';
	}else if($result==0){
		echo '{"res":"2"}';
	}else {
		echo '{"res":"0"}';
	}

}

//TMS-CS  报警列表
function GetWarningList(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$Dao_Warning= M("warning");
	$area_where=$Res_Data->areaid;
	$ts_where=$Res_Data->ts;
	$te_where=$Res_Data->te;
	$tn_where=$Res_Data->tn;
	$repairstate_where=$Res_Data->repairstate;
	$companyid=$Res_Data->cid;
	$PrintF = $Res_Data->pf;
	$where_str="";//"CompanyId=".$companyid." ";
	$currentPage=$Res_Data->page;
	$pagesize=$Res_Data->pagesize;
	$ORDER = $Res_Data->order;

	$AllPage = 1;
	if($area_where=="全部" || "All" == $area_where){
		$area_where="";
	}
	if($repairstate_where=="全部" || $repairstate_where=="Reveal All"){
		$repairstate_where="";
	}
	else if($repairstate_where=="已处理" || $repairstate_where=="Handled"){
		$repairstate_where="1";
	}
	else{
		$repairstate_where="0";
	}
	if($area_where!=""){
		$where_str.=" and warning.AreaId = '".$area_where."' ";
	}
	if($ts_where!=""){
		//$where_str.=" and RepairTime>='".$ts_where."' ";
		$where_str.=" and CreateTime>='".$ts_where."' ";
	}
	if($te_where!=""){
		//$where_str.=" and RepairTime<='".$te_where."' ";
		$where_str.=" and CreateTime<='".$te_where."' ";
	}
	if($tn_where!=""){
		$where_str.=" and trapname like '%$tn_where%' ";
	}

	$RT_NULL="warning.repairtime is null  and ";
	if($repairstate_where!=""){
		$where_str.=" and RepairState='$repairstate_where' ";
		if($repairstate_where=="1"){
			$RT_NULL="warning.repairtime is not null  and ";
		}
	}
	$List_ALLCount= $Dao_Warning->query("select * from (select trapname,modelname,traptype,usemtfi,linktype,linesize,spressure,spressureout,outtype,warning.* from trapmodel left join warning on $RT_NULL warning.companyid=$companyid and trapmodel.trapno=warning.trapno and
      trapmodel.companyid=warning.companyid $where_str order by createtime desc) as te  where id is not null  group by trapno");

	if($ORDER==""){
		$ORDER = "createtime desc";
	}
	//$List_ALL = $Dao_Warning->query("select trapname,modelname,warning.* from warning,trapmodel where warning.companyid=".$companyid." and trapmodel.trapno=warning.trapno and trapmodel.companyid=warning.companyid ".$where_str." and RepairTime is null order by $ORDER limit ".($currentPage-1)*$pagesize.",".$pagesize."");// and RepairTime is null 已处理的不显示, 删除则为都显示
	$SQLT="select * from (select trapname,modelname,traptype,usemtfi,linktype,linesize,spressure,spressureout,outtype,warning.* from trapmodel left join warning on $RT_NULL warning.companyid=$companyid and trapmodel.trapno=warning.trapno and
      trapmodel.companyid=warning.companyid $where_str order by createtime desc) as te  where id is not null  group by trapno  order by $ORDER  limit ".($currentPage-1)*$pagesize.",".$pagesize."";
	if($PrintF.""=="1"){
		$SQLT= "select * from (select trapname,modelname,traptype,usemtfi,linktype,linesize,spressure,spressureout,outtype,warning.* from trapmodel left join warning on $RT_NULL warning.companyid=$companyid and trapmodel.trapno=warning.trapno and
        trapmodel.companyid=warning.companyid $where_str order by createtime desc) as te  where id is not null  group by trapno  order by $ORDER";
	}

	$List_ALL = $Dao_Warning->query($SQLT);// and RepairTime is null 已处理的不显示, 删除则为都显示


	$show_list_str='{"res":[';
	foreach ($List_ALL as $warning_each) {
		$show_list_str.='{"id":"'.$warning_each["id"].'",';
		$show_list_str.='"areaid":"'.$warning_each["areaid"].'",';
		$show_list_str.='"area":"'.$warning_each["area"].'",';
		$show_list_str.='"modelname":"'.$warning_each["modelname"].'",';
		$show_list_str.='"trapno":"'.$warning_each["trapno"].'",';
		$show_list_str.='"trapname":"'.$warning_each["trapname"].'",';
		$show_list_str.='"location":"'.$warning_each["location"].'",';
		$RST = $warning_each["repairstate"]=="1"?L("L_ALERT_HANDLED"):L("L_ALERT_HANDLING");
		$show_list_str.='"repairstate":"'.$RST.'",';
		$RRT=$warning_each["repairtype"]=="1"?L("L_ALERT_REPAIR"):L("L_ALERT_REPLACE");
		$show_list_str.='"repairtype":"'.$RRT.'",';

		$show_list_str.='"repairtime":"'.$warning_each["repairtime"].'",';
		$show_list_str.='"linktype":"'.$warning_each["linktype"].'",';
		$show_list_str.='"linesize":"'.$this->DWHS_wr("1",$warning_each["linesize"],1,$companyid).'",';
		$show_list_str.='"spressure":"'.$this->DWHS_wr("0",$warning_each["spressure"],1,$companyid).'",';
		$show_list_str.='"spressureout":"'.$this->DWHS_wr("0",$warning_each["spressureout"],1,$companyid).'",';
		$show_list_str.='"outtype":"'.$warning_each["outtype"].'",';
		
		//date("Y-m-d",strtotime($warning_each["repairtime"]))
		//"\/Date('.date("Y-m-d H:i",strtotime($warning_each["repairtime"])).')\/",';
		$show_list_str.='"repairnum":"'.$warning_each["repairnum"].'",';
		$show_list_str.='"repairprice":"'.$warning_each["repairprice"].'",';
		$TST = $warning_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");
		$show_list_str.='"trapstate":"'.$TST.'",';
		$show_list_str.='"exlevel":"'.$warning_each["exlevel"].'",';
		$show_list_str.='"leveldesc":"'.$warning_each["leveldesc"].'",';
		$show_list_str.='"alerttem":"'.$this->DWHS_wr("2",$warning_each["alerttem"],1,$companyid).'",';
		$show_list_str.='"alerthz":"'.$warning_each["alerthz"].'",';
		$show_list_str.='"standardtem":"'.$warning_each["standardtem"].'",';
		$show_list_str.='"tempstate":"'.$warning_each["temstate"].'",';
		$show_list_str.='"traptype":"'.$warning_each["traptype"].'",';
		$show_list_str.='"battery":"'.$warning_each["battery"].'",';
		$show_list_str.='"usemtfi":"'.$warning_each["usemtfi"].'",';
		$show_list_str.='"description":"'.mb_substr($warning_each["repairdescription"],0,200,'utf-8').'",';
		$show_list_str.='"learnstate":"'.$warning_each["learnstate"].'",';
		$show_list_str.='"createtime":"'.$warning_each["createtime"].'",';
		$show_list_str.='"lossamount":"'.$warning_each["lossamount"].'",';
		
		$show_list_str = rtrim($show_list_str,",");

		//  $state=$this->queryLearnState();learnstate
		$show_list_str.="},";
	}
	$show_list_str = rtrim($show_list_str,",");
	$show_list_str.='],"datacount":"'.count($List_ALLCount).'"}';

	echo $show_list_str;
}