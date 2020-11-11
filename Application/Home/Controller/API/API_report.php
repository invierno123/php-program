<?php


//获取报表数据(根据口径统计)-list
function GetRepDataSize(){
	//$type=$_GET["type"];//0 列表 1 报表
	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$Point = $SearchInfo->point;//是否为 单点

	$Dao = M();

	$STR_ = "";

	if($AREA!=""){
		$STR_.=" and AreaId=".$AREA;
	}

	$query_str = "select usemtfi,linesize,count(0) as count from trapmodel where CompanyID=".$CID." ".$STR_." group by usemtfi,linesize";
	$list = $Dao->query($query_str);
	if($list){
		echo json_encode($list);
	} else {
		//error
	}

}

//获取报表数据(根据类型-查看类型)-list
function GetRepDataPT(){
	//$type=$_GET["type"];//0 列表 1 报表
	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$Point = $SearchInfo->point;//是否为 单点
	$GZTJ = $SearchInfo->GZTJ;//是否为故障统计

	$Dao = M();

	$STR_ = "";

	if($AREA!=""){
		$STR_.=" and AreaId=".$AREA;
	}

	$query_str = "select usemtfi,traptype,count(0) as count from trapmodel where CompanyID=".$CID." ".$STR_." group by usemtfi,traptype";
	if($GZTJ.""=="1"){
		$query_str = "select trapmodel.usemtfi,trapmodel.traptype,count(0) as count from trapmodel where CompanyID=$CID and trapno in (select trapno from (select id,trapno from trapinfo where companyid=$CID and (exlevel>0 or TemState>0) order by id desc) as tem  group by trapno order by id desc) $STR_ group by usemtfi,traptype";
	}
	$list = $Dao->query($query_str);
	if($list){
		echo json_encode($list);
	} else {
		//error
	}
}

//获取报表数据(根据阀门类型-查看类型)
function GetRepDataPTRP()
{
	//$type=$_GET["type"];//0 列表 1 报表

	$Dao = M();
	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$Point = $SearchInfo->point;//是否为 单点
	
	$GZTJ = $SearchInfo->GZTJ;//是否为 故障

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	//$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";

	$query_str = "select traptype,count(0) as count from trapmodel ".$where_str."  group by traptype";
	
	if($GZTJ.""=="1"){
		$query_str = "select traptype,count(0) as count from trapmodel $where_str and trapno in
(select trapno from (select id,trapno from trapinfo where companyid=$CID and (exlevel>0) order by id desc)
 as tem group by trapno order by id desc)
group by traptype";//or TemState>0
	}
	
	
	$list = $Dao->query($query_str);
	if($list){
		echo json_encode($list);
	} else {
		//error
	}
}

///品牌泄漏
function GetMoneyUseMtfi()
{
	$Dao = M();
	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$traptype = $SearchInfo->traptype;
	$Point = $SearchInfo->point;//是否为 单点

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	if($traptype!=""){
		$where_str.=" and TrapType='".$traptype."'";
	}
	$where_str.=" and datecheck between '$ST' and '$ET' ";
	$query_str = "select MoneyTON,MoneyUnit,UseMTFI,exlevel,id,(datecheck+'') as datecheck,lossamount,traptype,trapno from trapinfo $where_str order by UseMTFI asc,datecheck asc";
	$list = $Dao->query($query_str);
	if($list){
		echo json_encode($list);
	} else {
		//error
	}


}
///类型金钱损失
function GetMoneyType(){
	$Dao = M();
	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$traptype = $SearchInfo->traptype;
	$Point = $SearchInfo->point;//是否为 单点
	
	

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	if($traptype!=""){
		$where_str.=" and TrapType='".$traptype."'";
	}
	$where_str.=" and datecheck between '$ST' and '$ET' ";
	$query_str = "select UseMTFI,exlevel,id,(datecheck+'') as datecheck,lossamount,traptype,trapno from trapinfo $where_str order by traptype asc,datecheck asc";
	$list = $Dao->query($query_str);
	if($list){
		echo json_encode($list);
	} else {
		//error
	}


}

//获取报表数据(根据阀门品牌-查看类型)
function GetRepDataUSEmtfiRP()
{
	//$type=$_GET["type"];//0 列表 1 报表

	$Dao = M();
	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$Point = $SearchInfo->point;//是否为 单点

	$GZTJ = $SearchInfo->GZTJ;//是否为 故障

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	//$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";

	$query_str = "select usemtfi,count(0) as count from trapmodel ".$where_str."  group by usemtfi";
	if($GZTJ.""=="1"){
		$query_str = "select usemtfi,count(0) as count from trapmodel $where_str and trapno in
(select trapno from (select id,trapno from trapinfo where companyid=$CID and (exlevel>0) order by id desc)
 as tem group by trapno order by id desc)
group by usemtfi";//or TemState>0
	}
	$list = $Dao->query($query_str);
	if($list){
		echo json_encode($list);
	} else {
		//error
		echo "{\"res\":\"0\"}";
	}
}

//获取报表数据(根据类型-查看状态)-list
function GetRepDataTT()
{
	$Dao = M();

	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$Point = $SearchInfo->point;//是否为 单点

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	if($Point.""!="1"){
		$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";
	}
	$QUERY_STR ="select traptype,if(TemState=1,-1,exlevel) as exlevel,count(0) as count from trapinfo ".$where_str." group by traptype,trapstate";

	if($Point.""=="1"){
		$QUERY_STR="select traptype,if(TemState=1,-1,exlevel) as exlevel,1 as count from trapinfo ".$where_str." group by trapno order by createtime desc";
	}
	$list = $Dao->query($QUERY_STR);
	if($list){
		echo json_encode($list);
	} else {
		//error
		echo "{\"res\":\"0\"}";
	}
}

//获取报表数据(根据区域-查看状态)-list
function GetRepDataArea()
{
	$Dao = M();

	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$Point = $SearchInfo->point;//是否为 单点

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		//$where_str.=" and area='".$AREA."'";
	}
	if($Point.""!="1"){
		$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";
	}
	$QUERY_STR ="select area,if(TemState=1,-1,exlevel) as exlevel,count(0) as count from trapinfo ".$where_str." group by area,trapstate";

	if($Point.""=="1"){
		$QUERY_STR="select area,if(TemState=1,-1,exlevel) as exlevel,1 as count from trapinfo ".$where_str." group by trapno order by createtime desc";
	}
	$list = $Dao->query($QUERY_STR);
	if($list){
		echo json_encode($list);
	} else {
		//error
		echo "{\"res\":\"0\"}";
	}
}

//获取报表数据(根据类型-查看状态)
function GetRepDataTTRP()
{
	$Dao = M();

	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$POINT = $SearchInfo->point;

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	if($POINT.""!="1"){
		$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";
	}
	$QUERY_STR = "select if(exlevel=0,'Good','Leak') as exlevels,count(0) as count from trapinfo ".$where_str." group by exlevels";

	if($POINT.""=="1"){
		$QUERY_STR = "select tem.exlevels,count(0) as count from (select if(exlevel=0,'Good','Leak') as exlevels,1 as count from trapinfo $where_str group by exlevels,trapno) as tem group by tem.exlevels";
	}
	$list = $Dao->query($QUERY_STR);

	if($list){
		echo json_encode($list);
	} else {
		//error
		echo "{\"res\":\"0\"}";
	}
}

//获取报表数据(根据类型-查看状态)
function GetRepDataTTRPInfo()
{
	$Dao = M();

	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$POINT = $SearchInfo->point;

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	if($POINT.""!="1"){
		$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";
	}

	$QUERY_STR = "select if(exlevel=0,'Good','Leak') as exlevels,count(0) as count,traptype from trapinfo ".$where_str." group by traptype,exlevels";

	if($POINT.""=="1"){
		$QUERY_STR ="select tem.exlevels,traptype,count(0) as count from  (select if(exlevel=0,'Good','Leak') as exlevels,1 as count,traptype from trapinfo $where_str group by traptype,exlevels,trapno) as tem group by exlevels,traptype";
	}

	$list = $Dao->query($QUERY_STR);
	if($list){
		echo json_encode($list);
	} else {
		//error
		echo "{\"res\":\"0\"}";
	}
}

//获取报表数据(根据类型-查看状态)
function GetRepDataRMInfo(){
	$Dao = M();

	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$POINT = $SearchInfo->point;

	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}

	if($POINT.""!="1"){
		$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";
	}

	$QUERY_STR = "select if(exlevel=0,'Good',if(exlevel<=3,'Small',if(exlevel<=6,'Medium',if(exlevel<=9,'Large','Blow')))) as exlevels,count(0) as count from trapinfo ".$where_str." group by exlevels";

	if($POINT.""!="1"){
		$QUERY_STR = "select tem.exlevels,count(0) as count from (select if(exlevel=0,'Good',if(exlevel<=3,'Small',if(exlevel<=6,'Medium',if(exlevel<=9,'Large','Blow')))) as exlevels,1 as count from trapinfo $where_str group by exlevels,trapno) as tem group by exlevels";
	}

	$list = $Dao->query($QUERY_STR);
	if($list){
		echo json_encode($list);
	} else {
		//error
		echo "{\"res\":\"0\"}";
	}
}

//获取报表数据(损失金钱)
function GetRepDataRBInfo(){
	$Dao = M();

	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$POINT = $SearchInfo->point;

	$WhereStr="where companyid=".$CID;
	if($AREA!=""){
		$WhereStr.=" and area='".$AREA."'";
	}
	if($POINT.""!="1"){
		$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";
	}

	$QUERY_STR = "select leveldesc as exlevel,(TO_DAYS(repairtime)-TO_DAYS(createtime)) as days,lossamount,(''+createtime) as createtime from warning ".$WhereStr;

	if($POINT.""=="1"){
		$QUERY_STR = "select leveldesc as exlevel,(TO_DAYS(repairtime)-TO_DAYS(createtime)) as days,lossamount,(''+createtime) as createtime from warning where 1<0";
	}

	$list = $Dao->query($QUERY_STR);

	echo json_encode($list);
}

//获取报表数据(最近一次维修)
function GetRepDataLL_SX()
{
	$JsonData = $_POST["data"];
	if($JsonData==""){
		echo '{"res":"0"}';
		exit;
	}
	$Res_Data = json_decode($JsonData);
	$ST = $Res_Data->st;
	$ET = $Res_Data->et;
	$TN = $Res_Data->tn;

	$Dao = M();
	$list = $Dao->query("select id from warning where trapno ='".$TN."' and repairtime>'".$ST."' and repairtime<'".$ET."'");
	if($list){
		echo json_encode($list);
	} else {
		//error
	}
}

//获取报表数据(损失量)
function GetRepDataLL(){
	$Comid=$_GET["ci"];
	if($Comid==""){
		echo "";
		exit;
	}
	$PostData=$_POST["data"];
	$PostData_json=json_decode($PostData);
	$et=$PostData_json->et;
	$st=$PostData_json->st;
	$tn=$PostData_json->tn;
	$CID=$PostData_json->cmid;
	$Area = $PostData_json->area;
	$Dao = M();
	$TimeSearch="";
	if($et.""!=""){
		if($st=="00"){
			$TRNO =$tn;

			$sqlstr="select repairtime from warning where CompanyId=".$Comid." and trapno ='".$TRNO."' and repairtime is not null and repairtime<'".$et."'  order by repairtime desc";
			$RE_DATA = $Dao->query($sqlstr);
			//$DaoWarning=M("warning");
			//$RE_DATA=$DaoWarning->where("CompanyId=".$Comid." and trapno='".$TRNO."' and repairtime is not null and repairtime<'".$et."'")->order("repairtime desc")->select();
			if($RE_DATA!=null){
				$TimeSearch="and createtime between '".$RE_DATA[0]["repairtime"]."' and '".$et."' ";
			}
		}else {
			$TimeSearch="and createtime between '".$st."' and '".$et."' ";
		}
	}
	if($Area!=""){
		$TimeSearch.=" and AreaId=".$Area;
	}
	$list = $Dao->query("select trapno,exlevel,lossamount,(''+createtime) as createtime from trapinfo  where CompanyID=".$CID." and exlevel<>0 ".$TimeSearch." order by createtime asc");
	if($list){
		echo json_encode($list);
	} else {
		//error
		echo "";
	}
}

//获取报表数据(损失量)
function GetRepDataLL2()
{
	$Comid=$_GET["ci"];
	if($Comid==""){
		echo "";
		exit;
	}
	$PostData=$_POST["data"];
	$PostData_json=json_decode($PostData);
	$et=$PostData_json->et;
	$st=$PostData_json->st;
	$tn=$PostData_json->tn;
	$CID=$PostData_json->cmid;
	$Area = $PostData_json->area;
	$Point = $PostData_json->point;

	$Dao = M();
	$TimeSearch="";

	$sqlstr="select id,trapno,leveldesc,lossamount,(''+createtime) as createtime,(''+repairtime) as repairtime,exlevel from warning where CompanyId=".$Comid." and createtime between '".$st."' and '".$et."' and leveldesc>0 order by createtime desc";

	if($Point.""=="1"){
		$sqlstr="select id,trapno,exlevel,lossamount from trapinfo where CompanyId=".$Comid." and exlevel>0 group by trapno order by createtime desc";
	}

	$RE_DATA = $Dao->query($sqlstr);

	if($RE_DATA){
		echo json_encode($RE_DATA);
	} else {
		//error
		echo "";
	}
}

//TMS-Android 统计分析 故障
function GetTrapEXA(){
	$Dao = M();
	$DATA_Post=$_POST["data"];
	$SearchInfo=json_decode($DATA_Post);
	$CID=$SearchInfo->cmid;
	$AREA=$SearchInfo->area;
	$ST=$SearchInfo->st;
	$ET=$SearchInfo->et;
	$Point = $SearchInfo->point;//是否为 单点
	$GetType=$SearchInfo->type;//type==1 为品牌  0 为形式 默认0
	
	if($GetType=="1"){
		$GetType="UseMTFI";
	}else{
		$GetType="traptype";
	}
	
	$where_str="where CompanyID=".$CID;
	if($AREA!=""){
		$where_str.=" and area='".$AREA."'";
	}
	if($Point.""!="1"){
		$where_str.=" and CreateTime between '".$ST."' and '".$ET."' ";
	}
	$QUERY_STR ="select *,count(0) as count from (select ".$GetType.",if(TemState=1,-1,exlevel) as exlevel from 
trapinfo  ".$where_str."  and (trapno in (select trapno from trapmodel where companyid=".$CID."))) as te group by exlevel,".$GetType."";

	//"select ".$GetType.",if(TemState=1,-1,exlevel) as exlevel,count(0) as count from trapinfo ".$where_str." and (trapno in (select trapno from trapmodel where companyid=".$CID.")) group by traptype,trapstate";
	
	if($Point.""=="1"){
		//$QUERY_STR="select ".$GetType.",if(TemState=1,-1,exlevel) as exlevel,1 as count from trapinfo ".$where_str." group by trapno order by createtime desc";
		//$QUERY_STR ="select ".$GetType.",exlevel,count from (select trapno,".$GetType.",if(TemState=1,-1,exlevel) as exlevel,1 as count from trapinfo ".$where_str." order by id desc) as te group by trapno";
		$QUERY_STR="select ".$GetType.",exlevel,count from (select trapno,".$GetType.",if(TemState=1,-1,exlevel) as exlevel,1 as count from trapinfo ".$where_str." and (trapno in (select trapno from trapmodel where companyid=".$CID.")) order by id desc) as te group by trapno";
	}
	$list = $Dao->query($QUERY_STR);
	if($list){
		echo json_encode($list);
	} else {
		//error
	}
}

//TMS-android 获取节点数据
function GetTrapData(){
	$comid=$_GET["ci"];
	$lang=$_GET["lang"];
	$data_post=$_POST["data"];
	if($comid==""||$data_post==""){
		echo "0";
		exit;
	}
	$order="trapno asc";
	$data_arr=json_decode($data_post);
	$username=$data_arr->userName;
	$status=$data_arr->status;
	$order_G=$data_arr->order;
	if($order_G!=""){
		$order=$order_G;
	}


	if($username=="" ||$status==""){
		echo "0";
		exit;
	}
	$wherestr="CompanyID=".$comid;
	$Trap_Info=M();
	//$Trap_Data_All=array();
	if($status=="user"){
		$areaid_post=$data_arr->areaid;
		if($areaid_post==""){
			echo "0";
			exit;
		}
		str_replace("*",",",$areaid_post);
		//$Area_Data=explode("*",$areaid_post);#分割获取负责的区域Id
		$wherestr.=" and Areaid in ('".$areaid_post."')";
	}
	/*
	 符合规则的判断
	 */
	$where_str=$wherestr;
	$where_str.=$this->CheckRoleInfo_GET($comid);
	$Trap_Data_All=$Trap_Info->query("select * from (select tm.*,ti.exlevel as exlevel,ti.temstate as temstate,ti.datecheck as datecheck ,ti.newtem as newtem ,ti.lossamount as lossamount from  (select * from trapmodel where $wherestr ) as tm  left join  (select * from trapinfo where $where_str order by datecheck desc) as ti on tm.trapno = ti.trapno order by datecheck desc) as tt group by trapno order by $order");
	$dataAll=array();
	$Index=0;
	foreach ($Trap_Data_All as $key) {
		$data["id"]=$key["id"];
		$data["trapno"]=$key["trapno"];
		$data["trapname"]=$key["trapname"];
		$data["area"]=$key["area"];
		$data["areaid"]=$key["areaid"];
		$data["location"]=$key["location"];
		//20170828 销售版使用(勿删)
		$data["traptype"]=$key["traptype"];//形式
		$data["usemtfi"]=$key["usemtfi"];//品牌
		$data["spressure"]=$key["spressure"];//入口压力
		$data["spressureout"]=$key["spressureout"];//出口压力
		$data["linesize"]=$key["linesize"];//口径
		$data["outtype"]=$key["outtype"];//排放方式
		$data["linktype"]=$key["linktype"];//连接方式
		$data["modelname"]=$key["modelname"];//型号
		$data["maxlk"]=$key["maxlk"];//阀嘴口径
		$data["stemptop"]=$key["stemptop"];//高温
		$data["stemplow"]=$key["stemplow"];//低温
		$data["ordernum"]=$key["ordernum"];//排序
		//20180129 TMS-APP 升级添加 勿动
		$data["newtem"]=$key["newtem"];//温度
		$data["lossamount"]=sprintf("%.2f", (float)$key["lossamount"]);//$key["lossamount"];//每小时泄漏量
		$data["datecheck"]=$key["datecheck"];//时间
		$data["exlevel"]=$key["exlevel"];//泄漏等级
		///
		if($key["datecheck"]!=NULL ||$key["datecheck"]!=""){
			$hour=floor((strtotime(date("Y-m-d H:i:s"))-strtotime($key["datecheck"]))/3600);
			if($hour>1){
				$data["trapstate"]="-1";//断线
				$dataAll[$Index]=$data;
				$Index++;
				continue;
			}
		}
		if($key["exlevel"]!=NULL || $key["exlevel"]!=""){
			$data["trapstate"]=((int)$key["exlevel"])>0?"1":"0";
			$data["trapstate"]=((int)$key["temstate"])>0?"-2":$data["trapstate"];
		}else{
			$data["trapstate"]="-3";//没有数据
		}

		$dataAll[$Index]=$data;
		$Index++;
	}
	echo json_encode($dataAll);
}


function GetAnlyLine(){
	$Comid=$_GET["ci"];
	if($Comid==""){
		echo "";
		exit;
	}
	$PostData=$_POST["data"];
	$PostData_json=json_decode($PostData);
	$et=$PostData_json->et;
	$st=$PostData_json->st;
	$tn=$PostData_json->tn;
	$CID=$PostData_json->cmid;
	$AreaID = $PostData_json->areaid;
	$Type = $PostData_json->type;//0 全部  1 分区域 
	
	if($Comid!=$CID){
		echo "{\"res\":\"4001\"}";
		exit;
	}
	
	
	$Dao = M();
	
	$sqlstr="select id,companyid,area,areaid,sum(lossamount) as lossamount,DATE_FORMAT(datecheck,'%Y-%m-%d %H:%i') as datecheck from  trapinfo where companyid=$Comid and exlevel>0 and datecheck between '$st' and '$et' ";
	if($AreaID!=""){
		$sqlstr.=" and areaid=$AreaID ";
	}
	if($tn!=""){
		$sqlstr.=" and trapno='$tn' ";
	}
	
	
	if($Type=="1"){
		$sqlstr.= "  group by areaid,datecheck";
	}else if($Type=="0"){
		$sqlstr.= "  group by datecheck";
	}
	
	
	
	$RE_DATA = $Dao->query($sqlstr);
	if($Type=="0"){
		$data_index=0;
		$EXDATA=0.0;
		//foreach($RE_DATA as $item){
		//if((int)$item["exlevel"]>0){
		//	$item_2=$RE_DATA[$data_index+1];
		//	$Min_c = floor((strtotime($item_2["datecheck"])-strtotime($item["datecheck"]))%86400/60);
		//	$EXDATA=$EXDATA+((int)$item["lossamount"])/60*$Min_c;
		//	
		//}
		//$data_index=$data_index+1;
		//$RES_ARRAY["id"]=$item["id"];
		//$RES_ARRAY["area"]=$item["area"];
		//$RES_ARRAY["areaid"]=$item["areaid"];
		//$RES_ARRAY["trapno"]=$item["trapno"];
		//$RES_ARRAY["exlevel"]=$item["exlevel"];
		//$RES_ARRAY["lossamount"]=$EXDATA;
		//$RES_ARRAY["datecheck"]=$item["datecheck"];
		
		//}
		foreach($RE_DATA as $item){
			$datait = $RES_ARRAY_["all&0"];
			$datait[$item["datecheck"]]=$item["lossamount"];
			$RES_ARRAY_["all&0"]=$datait;
			
		}
		
		foreach($RES_ARRAY_ as $k=>$r_array){
			$k_array =explode("&", $k);
			$RES_ARRAY2["area"] = $k_array[0];
			$RES_ARRAY2["areaid"] = $k_array[1];
			$RES_ARRAY2["data"]=$r_array;
			$RES_ARRAY[$data_index]=$RES_ARRAY2;
			$data_index++;
		}
		
	}else{
		foreach($RE_DATA as $item){
			$datait = $RES_ARRAY_[$item["area"]."&".$item["areaid"]];
			$datait[$item["datecheck"]]=$item["lossamount"];
			$RES_ARRAY_[$item["area"]."&".$item["areaid"]]=$datait;
			
		}
		$data_index=0;
		foreach($RES_ARRAY_ as $k=>$r_array){
			$k_array =explode("&", $k);
			$RES_ARRAY2["area"] = $k_array[0];
			$RES_ARRAY2["areaid"] = $k_array[1];
			$RES_ARRAY2["data"]=$r_array;
			$RES_ARRAY[$data_index]=$RES_ARRAY2;
			$data_index++;
		}
	}
	
	if($RE_DATA){
		echo json_encode($RES_ARRAY);
	} else {
		//error
		echo "{\"res\":\"0\"}";
	}
}