<?php


// 单位换算
///$TYPE_  0  压力   1 口径  2 温度  3 资源  4 泄漏  $LX 0 转为原始单位(非读取)
function DWHS($TYPE_, $U, $LX,$companyid)
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
//TMS-CS 数据导入解析
function  InputData(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$companyid=$Res_Data->cid;
	$filename=$Res_Data->filename;
	$rows=$Res_Data->rows;
	$data=$Res_Data->hang;
	$md5_file=$Res_Data->md5;
	$loginname=$Res_Data->loginname;
	$Dao=M("trapinfo");
	//$db = mysql_connect('192.168.1.109', 'root', 'root') or
	// $db = mysql_connect('localhost', 'root', 'boda2015') or
	$db = mysql_connect('localhost', 'root', '125164') or
		die("Could not connect to database.");//连接数据库
	mysql_query("SET NAMES 'UTF8'");//输出中文
	mysql_select_db('tmsdata'); //选择数据库
	error_reporting(E_ALL ^ E_NOTICE);
	mysql_query("SET AUTOCOMMIT=0");

	$importtime=date("y-m-d",time());

	$query="select md5 from import  where md5 ='".$md5_file."'";
	$flag_=0;
	$result1= mysql_query($query);

	if(mysql_num_rows($result1)){
		echo  '{"res":"0"}';
	}else{
		mysql_query("BEGIN");
		for ($i=0; $i < count($data); $i++) {
			$v = $data[$i];
			$Add_items_str_ = "'".$v->Area."',";
			$Add_items_str_.= "'".$v->TrapNo."',";
			$Add_items_str_.= "'".$v->Location."',";
			$Add_items_str_.="'".$v->TrapName."',";
			$Add_items_str_.="'".$v->TrapType."',";
			$Add_items_str_.="'".$v->UseMTFI."',";
			$Add_items_str_.="'".$v->SPressure."',";
			$Add_items_str_.="'".$v->LineSize."',";
			$Add_items_str_.="'".$v->LinkType."',";
			$Add_items_str_.="'".$v->OutType."',";
			$Add_items_str_.="'".$v->NewTem."',";
			$Add_items_str_.="'".$v->TrapState."',";
			$Add_items_str_.="'".$v->ExLevel."',";
			$Add_items_str_.="'".$v->LevelDesc."',";
			$Add_items_str_.="'".$v->LossAmount."',";
			$Add_items_str_.="'".$v->LossMoneyYear."',";
			$Add_items_str_.="'".$v->DateCheck."'";

			$Dao=M('areainfo');
			$data_select=$Dao->where("AreaName='".$v->Area."' and companyid=".$companyid)->find();
			if($data_select){

			}else{
				$sqlarea = "INSERT INTO areainfo(AreaName,AreaUser,UserTEL,AreaLocation,CompanyID,areainfo) VALUES('".
					$v->Area."','".
					'NONE'."','".
					'12345678'."','".
					''."','".
					$companyid."','".
					''."' )";
				$result=mysql_query($sqlarea);
				if($result){
					mysql_query("COMMIT");
				}else{
					mysql_query("ROLLBACK");
					echo '-1';
					exit;
				}
			}
			$areaid = $Dao->where("AreaName='".$v->Area."' and companyid=".$companyid)->getField('Id');
			$sql = "INSERT INTO trapinfo(CompanyID,AreaId,Area,TrapNo,Location,TrapName,TrapType,UseMTFI,SPressure,LineSize,LinkType,OutType,NewTem,TrapState,Exlevel,LevelDesc,LossAmount,LossMoneyYear,DateCheck)
               VALUES (".$companyid.",".$areaid.",".$Add_items_str_.");";//.$areaid.","

			$res=mysql_query($sql);
			if($res){

			}
			else{
				$flag_++;
			}
		}
		mysql_query("END");
		if($flag_==0){
			$sqlimport = "insert into import(companyid,filename,importPerson,importTime,importNumber,md5)values('".
				$companyid."','".
				$filename."','".
				$loginname."','".
				$importtime."','".
				$rows."','".
				$md5_file."'  );";
			$re = mysql_query($sqlimport);
			mysql_query("COMMIT");
			echo '{"res":"1"}';
		}else{
			mysql_query("ROLLBACK");
			echo '{"res":"-1"}';
		}

	}

}


///TMS-CS 全部节点列表
function GetSupList(){

	$POST_DATA = $_POST["data"];

	$Res_Data = json_decode($POST_DATA);

	$DAO_Trap=M("trapinfo");

	$area_where=$Res_Data->areaid;
	$ts_where=$Res_Data->ts;
	$te_where=$Res_Data->te;
	$tn_where=$Res_Data->tn;
	$tstate_where=$Res_Data->tstate;
	$CompanyID_ = $Res_Data->cid;
	$order = $Res_Data->order;


	if($area_where=="全部" || $area_where=="All"){$area_where="";}
	$tstate_where_w="";
	if($tstate_where=="全部" || $tstate_where=="Reveal All"){
		$tstate_where_w="";
	}else if($tstate_where=="异常" || $tstate_where=="Defect"){
		$tstate_where_w="1";
	}else if($tstate_where=="低温" || $tstate_where=="Cold"){
		$tstate_where_w="-1";
	}else {
		$tstate_where_w="0";
	}

	$Page_Cur=$Res_Data->page;
	$PageSize=$Res_Data->pagesize;
	$AllPage = 1;

	$where_str=" CompanyID=".$CompanyID_." ";
	$Where_Model =" CompanyID=".$CompanyID_." ";
	if($area_where!="" && $area_where!="全部"){
		$where_str.=" and Areaid in ('".$area_where."') ";
		$Where_Model.=" and Areaid in ('".$area_where."') ";
	}
	if($ts_where!=""){
		$where_str.=" and ((DateCheck>='".$ts_where."' ";//DateCheck

		if($te_where==""){
			$te_where=date("Y-m-d")." 23:59:59";
		}
		$where_str.=" and DateCheck<='".($te_where." 23:59:59")."') or DateCheck is null) ";//DateCheck


	}

	if($tn_where!=""){
		$where_str.=" and TrapName like '%".$tn_where."%' ";
		$Where_Model.=" and TrapName like '%".$tn_where."%' ";
	}
	//if($tstate_where!="" && $tstate_where!="全部" && $tstate_where!="All"){ //&& $tstate_where!="0"
	//$where_str.=" and TrapState='".$tstate_where."' ";
	if($tstate_where_w=="1"){
		$where_str.=" and exlevel>0 and temstate=0 ";
	}elseif($tstate_where_w=="0"){
		$where_str.=" and exlevel=0 and temstate=0";
	}elseif($tstate_where_w=="-1"){
		$where_str.=" and temstate=1 ";
	}
	//}

	/*
	符合规则的判断
	*/
	$where_str.=$this->CheckRoleInfo_GET($CompanyID_);

	$List_ALL_array = $DAO_Trap->where($where_str)->group("TrapNo")->select();
	//$List_ALL = $DAO_Trap->query("select * from (select * from trapinfo where ".$where_str." order by datecheck desc) as te group by trapno limit ".((int)$Page_Cur-1)*(int)$PageSize.",".(int)$PageSize."");
	if($order==""){
		$order = "datecheck desc";
	}
	$List_ALL = $DAO_Trap->query("select * from (select * from (select tm.*,ti.trapmodel,ti.newtem as newtem,ti.datecheck as datecheck,ti.description as description,ti.exlevel as exlevel,ti.lossamount as lossamount,ti.temstate as temstate,ti.createtime as createtime,ti.Battery as battery from  (select * from trapmodel where $Where_Model ) as tm  left join  (select * from trapinfo where $Where_Model order by datecheck desc) as ti on tm.trapno = ti.trapno order by datecheck desc) as tt group by trapno) as t2 where $where_str  order by $order  limit ".((int)$Page_Cur-1)*(int)$PageSize.",".(int)$PageSize."");


	$List_Count=$DAO_Trap->where($where_str)-count();
	$show_list_str='{"res":[';//']}}';
	foreach ($List_ALL as $trap_each) {
		$show_list_str.='{"trapno":"'.$trap_each["trapno"].'",';
		$show_list_str.='"trapname":"'.$trap_each["trapname"].'",';
		$show_list_str.='"area":"'.$trap_each["area"].'",';
		$show_list_str.='"areaid":"'.$trap_each["areaid"].'",';
		$show_list_str.='"outtype":"'.$trap_each["outtype"].'",';
		$ZT = ((int)$trap_each["exlevel"])>0?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");
		$show_list_str.='"trapstate":"'.$ZT.'",';
		$show_list_str.='"traptype":"'.$trap_each["traptype"].'",';
		$show_list_str.='"trapmodel":"'.$trap_each["trapmodel"].'",';
		$show_list_str.='"modelname":"'.$trap_each["trapmodel"].'",';
		$show_list_str.='"location":"'.$trap_each["location"].'",';
		$show_list_str.='"temp":"'.$this->DWHS("2",$trap_each["newtem"],1,$CompanyID_).'",';
		$show_list_str.='"spressure":"'.$this->DWHS("0", $trap_each["spressure"],1,$CompanyID_).'",';
		$show_list_str.='"spressureout":"'.$this->DWHS("0",$trap_each["spressureout"],1,$CompanyID_).'",';
		$show_list_str.='"datecheck":"'.date("Y-m-d H:i",strtotime($trap_each["datecheck"])).'",';
		$show_list_str.='"description":"'.$trap_each["description"].'",';
		$show_list_str.='"exlevel":"'.$trap_each["exlevel"].'",';
		$show_list_str.='"usemtfi":"'.$trap_each["usemtfi"].'",';
		$show_list_str.='"linesize":"'.$this->DWHS("1",$trap_each["linesize"],1,$CompanyID_).'",';
		$show_list_str.='"linktype":"'.$trap_each["linktype"].'",';
		$show_list_str.='"lossamount":"'.$this->DWHS("4",$trap_each["lossamount"],1,$CompanyID_).'",';
		$show_list_str.='"tempstate":"'.$trap_each["temstate"].'",';
		$show_list_str.='"battery":"'.$trap_each["battery"].'",';
		$show_list_str.='"maxlk":"'.$trap_each["maxlk"].'",';
		$show_list_str.='"createtime":"'.date("Y-m-d H:i",strtotime($trap_each["createtime"])).'",';
		$show_list_str = rtrim($show_list_str,",");
		$show_list_str.="},";
	}



	$show_list_str = rtrim($show_list_str,",");
	//	$show_list_str.='],"datacount":"'.count($List_ALL_array).'"}';
	$show_list_str.='],"datacount":"'.$List_Count.'"}';
	echo $show_list_str;
}

///TMS-CS某节点列表(详细)
function GetSupListByTrap(){

	$POST_DATA = $_POST["data"];
	if($POST_DATA==""){
		exit;
	}
	$Res_Data = json_decode($POST_DATA);


	$Page_Cur=$Res_Data->page;
	$CompanyID = $Res_Data->cid;
	$PageSize=$Res_Data->pagesize;
	$TrapID = $Res_Data->tid;

	if($Page_Cur==''){
		$Page_Cur=1;
	}

	$PageS=($Page_Cur-1)*$PageSize;

	$DAO_Trap=M("trapinfo");//."' and TrapState='".$_GET["trapstate"].
	/*
	符合规则的判断
	*/

	$WHERE_STR= $this->CheckRoleInfo_GET($CompanyID);

	$List_ALL = $DAO_Trap->where("CompanyID=".$CompanyID." and TrapNo='".$TrapID."'  ".$WHERE_STR)->order("DateCheck desc")->limit($PageS,$PageSize)->select();

	$AllDataCount = $DAO_Trap->where("CompanyID=".$CompanyID." and TrapNo='".$TrapID."'  ".$WHERE_STR)->order("DateCheck desc")->count();

	$AllPageCount=(int)$AllDataCount/$PageSize;
	if((int)$AllDataCount%$PageSize!=0){
		(int)$AllPageCount=(int)$AllPageCount+1;
	}
	if((int)$AllPageCount==0){
		$AllPageCount=1;
	}

	$show_list_str='{"res":{';//']}}';
	foreach ($List_ALL as $trap_each) {
		$show_list_str.="[";
		$show_list_str.='"trapno":"'.$trap_each["trapno"].'",';
		$show_list_str.='"trapname":"'.$trap_each["trapname"].'",';
		$show_list_str.='"area":"'.$trap_each["area"].'",';
		$show_list_str.='"location":"'.$trap_each["location"].'",';
		$show_list_str.='"trapstate":"'.$trap_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK").'",';
		$show_list_str.='"datecheck":"\/Date('.date("Y-m-d H:i",strtotime($trap_each["datecheck"])).')\/",';
		$show_list_str.='"traptype":"'.$trap_each["traptype"].'",';
		$show_list_str.='"usemtfi":"'.$trap_each["usemtfi"].'",';
		$show_list_str.='"spressure":"'.$this->DWHS("0",$trap_each["spressure"],1,$CompanyID).'",';
		$show_list_str.='"linesize":"'.$this->DWHS("1",$trap_each["linesize"],1,$CompanyID).'",';
		$show_list_str.='"linktype":"'.$trap_each["linktype"].'",';
		$show_list_str.='"outtype":"'.$trap_each["outtype"].'",';
		$show_list_str.='"newtem":"'.$this->DWHS("2",$trap_each["newtem"],1,$CompanyID).'",';
		$show_list_str = rtrim($show_list_str,",");
		$show_list_str.="],";
	}
	$show_list_str = rtrim($show_list_str,",");
	$show_list_str.='}}';
	echo $show_list_str;
}

function GetSupListByTrap_CS(){

	$POST_DATA = $_POST["data"];
	if($POST_DATA==""){
		exit;
	}
	$Res_Data = json_decode($POST_DATA);


	$Page_Cur=$Res_Data->page;
	$CompanyID = $Res_Data->cid;
	$PageSize=$Res_Data->pagesize;
	$TrapID = $Res_Data->tid;
	$TS = $Res_Data->ts;
	$TE = $Res_Data->te;

	if($Page_Cur==''){
		$Page_Cur=1;
	}

	$PageS=($Page_Cur-1)*$PageSize;

	$DAO_Trap=M("trapinfo");//."' and TrapState='".$_GET["trapstate"].
	/*
	符合规则的判断
	*/

	$WHERE_STR= $this->CheckRoleInfo_GET($CompanyID);

	$List_ALL = $DAO_Trap->where("CompanyID=".$CompanyID." and TrapNo='".$TrapID."' and  DateCheck>='".$TS."' and DateCheck<='".$TE."' ".$WHERE_STR)->order("DateCheck desc")->limit($PageS,$PageSize)->select();

	$AllDataCount = $DAO_Trap->where("CompanyID=".$CompanyID." and TrapNo='".$TrapID."' and  DateCheck>='".$TS."' and DateCheck<='".$TE."' ".$WHERE_STR)->order("DateCheck desc")->count();

	$AllPageCount=(int)$AllDataCount/$PageSize;
	if((int)$AllDataCount%$PageSize!=0){
		(int)$AllPageCount=(int)$AllPageCount+1;
	}
	if((int)$AllPageCount==0){
		$AllPageCount=1;
	}

	$show_list_str='{"res":[';//']}}';
	foreach ($List_ALL as $trap_each) {
		$ST_S=$trap_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");
		$show_list_str.="{";
		$show_list_str.='"trapno":"'.$trap_each["trapno"].'",';
		$show_list_str.='"trapname":"'.$trap_each["trapname"].'",';
		$show_list_str.='"area":"'.$trap_each["area"].'",';
		$show_list_str.='"location":"'.$trap_each["location"].'",';
		$show_list_str.='"trapstate":"'.$ST_S.'",';
		$show_list_str.='"datecheck":"\/Date('.date("Y-m-d H:i",strtotime($trap_each["datecheck"])).')\/",';
		$show_list_str.='"traptype":"'.$trap_each["traptype"].'",';
		$show_list_str.='"usemtfi":"'.$trap_each["usemtfi"].'",';
		$show_list_str.='"spressure":"'.$this->DWHS("0",$trap_each["spressure"],1,$CompanyID).'",';
		$show_list_str.='"linesize":"'.$this->DWHS("1",$trap_each["linesize"],1,$CompanyID).'",';
		$show_list_str.='"linktype":"'.$trap_each["linktype"].'",';
		$show_list_str.='"outtype":"'.$trap_each["outtype"].'",';
		$show_list_str.='"temp":"'.$this->DWHS("2",$trap_each["newtem"],1,$CompanyID).'",';
		$show_list_str.='"exlevel":"'.$trap_each["exlevel"].'",';
		$show_list_str.='"tempstate":"'.$trap_each["temstate"].'",';
		$show_list_str.='"createtime":"'.$trap_each["createtime"].'",';
		$show_list_str.='"lossamount":"'.$this->DWHS("4",$trap_each["lossamount"],1,$CompanyID).'",';
		
		$show_list_str = rtrim($show_list_str,",");
		$show_list_str.="},";
	}
	$show_list_str = rtrim($show_list_str,",");
	$show_list_str.='],"datacount":"'.$AllDataCount.'"}';
	echo $show_list_str;
}


function trapinfoAdd()
{
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);

	$Data_ADD["CompanyID"] = $Res_Data->cid;
	$Data_ADD["AreaId"] = $Res_Data->areaid;
	$Data_ADD["Area"] = $Res_Data->area;
	$Data_ADD["Location"] = $Res_Data->location;
	$Data_ADD["TrapNo"] = $Res_Data->trapno;
	$Data_ADD["TrapName"] = $Res_Data->trapname;
	$Data_ADD["TrapType"] = $Res_Data->traptype;
	$Data_ADD["UseMTFI"] = $Res_Data->usemtfi;
	$Data_ADD["LineSize"] = $Res_Data->linesize;
	$Data_ADD["LinkType"] = $Res_Data->linktype;
	$Data_ADD["OutType"] = $Res_Data->outtype;
	$Data_ADD["NewTem"] = $Res_Data->newtem;
	$Data_ADD["TemState"] = $Res_Data->temstate;
	$Data_ADD["ExLevel"] = $Res_Data->exlevel;
	$Data_ADD["LossAmount"] = $Res_Data->lossamount;
	$Data_ADD["LossMoneyYear"] = $Res_Data->lossmoneyyear;
	$Data_ADD["DateCheck"] = $Res_Data->datecheck;
	$Data_ADD["SetOption"] = $Res_Data->setoption;

	$trapinfoDAO = M("trapinfo");
	$GET_RES = $trapinfoDAO->add($Data_ADD);
	echo "$GET_RES";

}


function InsertData(){

	$DataParam = $_POST["data"];
	$logStr="接收到的数据\n->".$DataParam;
	$this->WriteLog("log.txt",$logStr);
	//$this->WriteLog("11111data",$DataParam);

	$LenFlag="0";
	$comid=$_GET["comid"];
	if($comid==""){
		echo '-22';
		exit;
	}

	//$crypt = new Crypt3Des('1a2b3c4d1a2b3c4d');
	try {
		$Result = $DataParam;//$crypt->decrypt($DataParam);//解密
	} catch (Exception $e) {}
	
	$AN_RESLUT = M("analysis");
	$COMDAO = M("companyinfo");
	$COM_RES = $COMDAO->where("Id=$comid")->find();

	//echo "tp1";
	$AreaDAO = M("trapmodel");
	$TrapinfoDAO = M("trapinfo");
	$arr_all_result = array();
	$PER_STR="";
	if($Result != ""){

		$Result=str_replace("TPCMSCD","",$Result);
		$Result_p_array = split("/",$Result);
		$Index_FLAG=0;
		$parray_time =date('Y-m-d H:i:s');
		foreach ($Result_p_array as $parrayData) {
			$end_res=0;
			$parrayData = str_replace("\n","",$parrayData);
			$parrayData = str_replace("\r","",$parrayData);
			if($parrayData!=""){
				#通过 | 符号分割数据和时间
				if(strpos($parrayData,"|")!=""){
					$parray_arr= explode("|",$parrayData);
					$parrayData=$parray_arr[0];
					$parray_time=$parray_arr[1];
				}
			}

			//通过 ","符号分割每条数据
			$Parray_d_arr=split(",",$parrayData);
			foreach ($Parray_d_arr as $parray) {
				if(strlen($parray)<=0){$end_res=1;continue;}
				if(strlen($parray)<51){

					$LAST2 = substr($parray,strlen($parray)-2,2);
					if($LAST2=="E1"){
						$trouble = M("trouble");
						$trouble_data["CompanyID"]=$comid;
						$trouble_data["TrapNo"]=strtolower(substr($parray,7,4));
						$trouble_data["Type"]="1";
						$trouble_data["Description"]="唤醒失败";
						$trouble->add($trouble_data);
						$end_res=1;
					}elseif ($LAST2=="E2") {
						$trouble = M("trouble");
						$trouble_data["CompanyID"]=$comid;
						$trouble_data["TrapNo"]=strtolower(substr($parray,7,4));
						$trouble_data["Type"]="3";
						$trouble_data["Description"]="省电失败";
						$trouble->add($trouble_data);
						$end_res=1;
					}
					continue;
				}


				if(strpos(strtolower(str_replace(" ","",$parray)),"tpcms")<0){

					continue;
				}

				//2016-12-15 处理频率采集参数配置报文
				if(strpos(strtolower(str_replace(" ","",$parray)),"tpcmsms")>-1){
					$Msg=$parray;
					$trap_no=strtolower(substr($Msg,9,4));
					$mcu_id=strtolower(substr($Msg,7,2));
					$trap_model_info = $AreaDAO->where("CompanyID=".$comid." and MCUID='".$mcu_id."' and trapNo='".$trap_no."'")->find();
					if($trap_model_info["id"]==""){
						continue;
					}

					//$u_array["SetState"]=0;//1：配置  0 :不需要配置
					//$u_array=array();
					//$u_array["SetState"]=0;
					$trap_Dao=M();
					$sql="update trapmodel set SetState=0 where Id=".(int)$trap_model_info["id"];
					$AreaDAO_r=$trap_Dao->execute($sql);//query 查询 execute 增删改
					$end_res=1;
					continue;
				}
				if(strpos(strtolower(str_replace(" ","",$parray)),"tpcmsmf")>-1){
					$end_res=1;
					continue;
				}
				$parray_ = split("tp",strtolower(str_replace(" ","",$parray)));
				$parray = str_replace("sd","",$parray_[1]);



				$Result_array[0]=substr($parray,0,3);

				//$comid = ltrim(strtolower(substr($parray,3,6)),"a");
				//$comid= (int)$comid;

				$Result_array[1]=date("ymdHis"); //substr($parray,3,12);//时间
				if($LenFlag=="1"){ //说明存在省电失败或者唤醒失败的节点
					$Result_array[2]=strtolower(substr($parray,3,4)); //阀门ID
				}else{
					$Result_array[2]=strtolower(substr($parray,5,4)); //阀门ID

					$Result_array[3]=$Result_array[2];//物理地址
					$Result_array[4]=substr($parray,9,5);//温度
					$Result_array[5]=substr($parray,14,4);//最小幅值
					$Result_array[6]=substr($parray,18,5);//最小频率
					$Result_array[7]=substr($parray,23,4);//最大幅值
					$Result_array[8]=substr($parray,27,5);//最大频率
					$Result_array[9]=substr($parray,32,4);//平均幅值
					$Result_array[10]=substr($parray,36,5);//平均频率

					$Result_array[11]=substr($parray,41,2);//预判
					$Result_array[12]=substr($parray,43,1);//电量
					$Result_array[13]=substr($parray,44,3);//校验

					$XL_DATA["maxfz"]=$Result_array[7];
					$XL_DATA["minfz"]=$Result_array[5];
					$XL_DATA["avgfz"]=$Result_array[9];
					$XL_DATA["bl"] = $Result_array[11];
					$XL_DATA["tm"] = $Result_array[4];

					$XL_DATA = $this->CheckRoleInfo_ADD($comid,$parray,$XL_DATA);

					$Result_array[7] = $XL_DATA["maxfz"];
					$Result_array[5] = $XL_DATA["minfz"];
					$Result_array[9] = $XL_DATA["avgfz"];
					$Result_array[11]= $XL_DATA["bl"];

				}


				if(strtolower($Result_array[0])=="cms"){

					$res_date_check =substr($Result_array[1],0,2)."-".substr($Result_array[1],2,2)."-".substr($Result_array[1],4,2)." ".substr($Result_array[1],6,2).":".substr($Result_array[1],8,2).":".substr($Result_array[1],10,2);
					$trap_no = $Result_array[2];
					//$trap_location = $Result_array[3];
					$TEM_STATE=0;
					if($LenFlag=="1"){
						$new_tem = 0;///温度
						$hz_number =0;//峰值
					}else{
						//2016-12-15 添加
						$tem_ne=substr($Result_array[4],0,1)=="0"?"":"-";
						$tem_=$tem_ne.substr($Result_array[4],1,3).".".substr($Result_array[4],4,1);
						//2016-12-15 添加结束
						//$tem_=substr($Result_array[4],0,3).".".substr($Result_array[4],3,2);
						$new_tem = ((float)$tem_);///温度
						$originalTem = ((float)$tem_);///温度
						$tem_SP = $this->getTemByPressure0((double)(((double)$trap_model_info["spressure"])/10.00));
						if(((float)$tem_SP)<$new_tem){
							if(((float)$tem_SP)>0){
								$new_tem = $new_tem-10;//((float)$tem_SP); 20170708温度调整
							}
						}
						if($new_tem<=50){//温度低于50则判断为低温状态
							$TEM_STATE = 1;
						}
						$hz_number = $Result_array[7];//最大幅值
						$hz_DZ =  $Result_array[5];//最小幅值
						$hz_PJ =  $Result_array[9];//平均幅值
						$hz_Max = $Result_array[8];//最大频率
						$hz_Min = $Result_array[6];//最小频率
						$hz_AVG = $Result_array[10];//平均频率
					}
					$trap_model_info = $AreaDAO->where("CompanyID=".$comid." and trapNo='".$trap_no."'")->find();

					//判断是否泄漏以及 泄漏等级
					$LeakBase = (double)$trap_model_info["leakbase"];
					$MaxPercent = (double)$trap_model_info["maxpercent"];
					$AvgPercent = (double)$trap_model_info["avgpercent"];
					$LeakBaseDAO=M("leakbase");
					$DB_DATA_Max = $LeakBase*((double)('0'.$hz_number));
					$DB_DATA_Avg = $LeakBase*((double)('0'.$hz_PJ));
					$DB_DATA_Per = $LeakBase*(((double)('0'.$hz_number))*$MaxPercent+((double)('0'.$hz_PJ))*$AvgPercent);

					$LeakInfo_AVG = $LeakBaseDAO->where("TrapType='".$trap_model_info["traptype"]."' and LeakValue<=".$DB_DATA_Avg." ")->order("TypeDes,LeakValue desc")->limit(2)->select();
					$LeakInfo_PER = $LeakBaseDAO->where("TrapType='".$trap_model_info["traptype"]."' and LeakValue<=".$DB_DATA_Per." ")->order("TypeDes,LeakValue desc")->limit(2)->select();
					$LeakInfo_array = $LeakBaseDAO->where("TrapType='".$trap_model_info["traptype"]."' and LeakValue<=".$DB_DATA_Max." ")->order("TypeDes,LeakValue desc")->limit(2)->select();

					$LeakInfo = $LeakInfo_array[0];
					//if($LeakInfo==null){$LeakInfo["leaklevel"]=0;}

					//$PD_Data = new Alert_Judge();
					//$Res_PDData = $PD_Data->JudgeData($new_tem,$hz_AVG,$trap_no,$comid,$LeakInfo["leaklevel"]);

					//echo $new_tem.",".$hz_number.",".$trap_no.",".$comid."<br />";
					$STATE = $Result_array[11];
					$battery = $Result_array[12];
					$check_sum = $Result_array[13];

					if($trap_model_info["id"]==""){
						//$logStr="ID not find->".$parray;
						//$this->WriteLog("log.txt",$logStr);
						continue;
					}
					//$c_array["DateCheck"]=$res_date_check;
					$c_array["DateCheck"]=$parray_time;
					$c_array["CreateTime"]=date('Y-m-d H:i:s');
					$c_array["TrapNo"]=$trap_no;
					$c_array["TrapModel"]=$trap_model_info["modelname"];


					if($trap_model_info["area"]==""){
						$c_array["Area"]="-";
					}else {
						$c_array["Area"]=$trap_model_info["area"];
					}
					if($trap_model_info["areaid"]==""){
						$c_array["AreaId"]="-";
					}else {
						$c_array["AreaId"]=$trap_model_info["areaid"];
					}
					//$c_array["AreaId"]=$trap_model_info["areaid"];
					$c_array["CompanyID"] = $comid;
					if($trap_model_info["location"]==""){
						$c_array["Location"]="-";
					}else {
						$c_array["Location"]=$trap_model_info["location"];
					}
					if($trap_model_info["trapname"]==""){
						$c_array["TrapName"]="-";
					}else {
						$c_array["TrapName"]=$trap_model_info["trapname"];
					}
					if($trap_model_info["traptype"]==""){
						$c_array["TrapType"]="-";
					}else {
						$c_array["TrapType"]=$trap_model_info["traptype"];
					}
					if($trap_model_info["usemtfi"]==""){
						$c_array["UseMTFI"]="-";
					}else {
						$c_array["UseMTFI"]=$trap_model_info["usemtfi"];
					}
					if($trap_model_info["spressure"]==""){
						$c_array["SPressure"]="-";
					}else {
						$c_array["SPressure"]=$trap_model_info["spressure"];
					}
					if($trap_model_info["linesize"]==""){
						$c_array["LineSize"]="-";
					}else {
						$c_array["LineSize"]=$trap_model_info["linesize"];
					}
					if($trap_model_info["linktype"]==""){
						$c_array["LinkType"]="-";
					}else {
						$c_array["LinkType"]=$trap_model_info["linktype"];
					}
					if($trap_model_info["outtype"]==""){
						$c_array["OutType"]="-";
					}else {

						$c_array["OutType"]=$trap_model_info["outtype"];
					}
					/*
					if($Res_PDData=="0" && $LenFlag=="0"){
						$c_array["TrapState"]="0";//0正常 1异常
					}else {
					$c_array["TrapState"]="1";

										}*/

					$c_array["ExLevel"]="0".$LeakInfo_PER[0]["leaklevel"];//.$LeakInfo["leaklevel"];
					$c_array["PerExleve"] = $c_array["ExLevel"];
					try{
						//偶尔单体泄露数据 隔离////////////////////////////////////////////
						if(((int)$c_array["ExLevel"])>0){
							$SELECTPerExleveDAO = M();
							$sql_str_a="select PerExleve from trapinfo where companyid=".$trap_model_info["companyid"]." and trapno='".$trap_model_info["trapNo"]."' order by id desc limit 1,3";
							//echo $sql_str_a."*";
							$SELECTARRAY = $SELECTPerExleveDAO->query($sql_str_a);
							
							if($SELECTARRAY!=NULL && count($SELECTARRAY)>=3){
								$PER0=((int)($SELECTARRAY[0]["perexleve"]));
								$PER1=((int)($SELECTARRAY[1]["perexleve"]));
								$PER2=((int)($SELECTARRAY[2]["perexleve"]));
								if($PER0>0 && $PER1>0 && $PER2>0){
									//连续四条数据泄露 进行泄露纠偏
									
									$ExMin_ = ((int)$c_array["ExLevel"])-2;
									$ExMax_ = ((int)$c_array["ExLevel"])+1;
									if($ExMin_<1){
										$ExMin_=1;
									}

									if($PER0>$ExMin_ && $ExMax_>=$PER0 && $PER1>$ExMin_ && $ExMax_>=$PER1 && $PER2>$ExMin_ && $ExMax_>=$PER2){
										//若此次泄露在前三次的区间内则不进行操作
									}else{
										//否则取上一次的值
										$c_array["ExLevel"]=$PER0;
									}
									/*----------------*/
								}else{
									//若没有连续四次的泄露数据 则否认泄露
									$c_array["ExLevel"]="0";
								}
							}else{
								//$c_array["ExLevel"]="0";
								//echo "Helo111111";
							}
						}
						//偶尔单体泄露数据 隔离   结束////////////////////////////////////
					}catch(Exception $e) {
						$c_array["ExLevel"]="0";
						$this->WriteLog("log_error.txt","perexlevel");
					}
					
					$c_array["NewTem"]=(double)('0'.$new_tem);

					$c_array["TemState"] = $TEM_STATE;//温度报警
					
					
					$c_array["originalTem"]=(double)('0'.$originalTem);

					$c_array["HZNumber"]=(double)('0'.$hz_number);//最大幅值
					$c_array["HZNumberDZ"]=(double)('0'.$hz_DZ);//最小幅值
					$c_array["HZNumberPJ"]=(double)('0'.$hz_PJ);//平均幅值
					$c_array["HZNumberMAX"]=(double)('0'.$hz_Max);//最大频率值
					$c_array["HZNumberMIN"]=(double)('0'.$hz_Min);//最小频率值
					$c_array["HZNumberAVG"]=(double)('0'.$hz_AVG);//平均频率值
					$c_array["TrapState"]=(double)('0'.$STATE);//预判
					$c_array["Battery"]=(double)('0'.$battery);

					$c_array["SetOption"]=$trap_model_info["zoommultiple"].','.$trap_model_info["criticalvalue"].','.$trap_model_info["collectionnum"].','.$trap_model_info["minhz"].','.$trap_model_info["maxhz"].','.$trap_model_info["revealper"];//当前设置的参数

					$c_array["MoneyTON"] = $COM_RES["moneyton"];
					$c_array["MoneyUnit"] = $COM_RES["moneyunit"]."";

					//$this->WriteLog("log-leak.txt","TrapType='".$trap_model_info["traptype"]."' and LeakValue<=".$DB_DATA."");
					//if($LeakInfo!=null){
					//泄露等级
					//  $c_array["ExLevel"] = $LeakInfo["leaklevel"];
					//}
					$this->WriteLog("log_ll.txt",$hz_Max."*".$trap_model_info["spressure"]."*".$new_tem."*".$comid."*".$trap_model_info["maxlk"]."*".$LeakInfo["leaklevel"]."*".$LeakInfo_PER[0]["leaklevel"]);

					$Loss_res = $this->OutLevel($hz_Max,$trap_model_info["spressure"],$new_tem,$comid,$trap_model_info["maxlk"],$LeakInfo["leaklevel"]);//获取泄露量
					$this->WriteLog("log_ll.txt","**".$Loss_res);
					$Loss_res_avg = $this->OutLevel($hz_Max,$trap_model_info["spressure"],$new_tem,$comid,$trap_model_info["maxlk"],$LeakInfo_AVG[0]["leaklevel"]);//获取泄露量 AVG
					$this->WriteLog("log_ll.txt","*--*".$Loss_res_avg);
					$Loss_res_per = $this->OutLevel($hz_Max,$trap_model_info["spressure"],$new_tem,$comid,$trap_model_info["maxlk"],$LeakInfo_PER[0]["leaklevel"]);//获取泄露量 PER
					$this->WriteLog("log_ll.txt","*-*".$Loss_res_per);
					$Loss_array = explode("*",$Loss_res);
					$Loss_array_avg = explode("*",$Loss_res_avg);
					$Loss_array_per = explode("*",$Loss_res_per);

					$c_array["LossAmount"]="0".$Loss_array_per[0];
					$c_array["LossAmountYear"]="0".$Loss_array_per[1];
					
					
					/*低温状态 所有为*/
					if($c_array["TemState"].""=="1"){
						$c_array["ExLevel"]="0";
						$c_array["LossAmount"]="0";
						$c_array["LossAmountYear"]="0";
					}
					/*----------------*/

					$N_GUID=$this->GUID();
					try{
						$c_array["trapInfoGUID"]=$N_GUID;
						//泄露级别插入 外键表开始
						$M_TrapEx = M("trapinfoexlevel");
						$TrapExInfo["MaxExleve"]='0'.$LeakInfo["leaklevel"];
						$TrapExInfo["MaxLossAmount"]='0'.$Loss_array[0]."";
						$TrapExInfo["MaxLossMoneyYear"]='0'.$Loss_array[1]."";
						$TrapExInfo["AvgExleve"]='0'.$LeakInfo_AVG[0]["leaklevel"];
						$TrapExInfo["AvgLossAmount"]='0'.$Loss_array_avg[0]."";
						$TrapExInfo["AvgLossMoneyYear"]='0'.$Loss_array_avg[1]."";
						$TrapExInfo["PercentExleve"]='0'.$LeakInfo_PER[0]["leaklevel"];
						$TrapExInfo["PercentAvgLossAmount"]='0'.$Loss_array_per[0]."";
						$TrapExInfo["PercentAvgLossMoneyYear"]='0'.$Loss_array_per[1]."";
						$TrapExInfo["LeakBaseType"] = "420";

						$TrapExInfo["trapInfoGUID"]=$N_GUID;

						$res_trap = $M_TrapEx->add($TrapExInfo);


						$Loss_res_max_1 = $this->OutLevel($hz_Max,$trap_model_info["spressure"],$new_tem,$comid,$trap_model_info["maxlk"],$LeakInfo_array[1]["leaklevel"]);//获取泄露量

						$Loss_res_avg_1 = $this->OutLevel($hz_Max,$trap_model_info["spressure"],$new_tem,$comid,$trap_model_info["maxlk"],$LeakInfo_AVG[1]["leaklevel"]);//获取泄露量 AVG

						$Loss_res_per_1 = $this->OutLevel($hz_Max,$trap_model_info["spressure"],$new_tem,$comid,$trap_model_info["maxlk"],$LeakInfo_PER[1]["leaklevel"]);//获取泄露量 PER

						$Loss_array_1 = explode("*",$Loss_res_max_1);
						$Loss_array_avg_1 = explode("*",$Loss_res_avg_1);
						$Loss_array_per_1 = explode("*",$Loss_res_max_1);

						$TrapExInfo["MaxExleve"]='0'.$LeakInfo_array[1]["leaklevel"];
						$TrapExInfo["MaxLossAmount"]='0'.$Loss_array_1[0];
						$TrapExInfo["MaxLossMoneyYear"]='0'.$Loss_array_1[1];

						$TrapExInfo["AvgExleve"]='0'.$LeakInfo_AVG[1]["leaklevel"];
						$TrapExInfo["AvgLossAmount"]='0'.$Loss_array_avg_1[0];
						$TrapExInfo["AvgLossMoneyYear"]='0'.$Loss_array_avg_1[1];

						$TrapExInfo["PercentExleve"]='0'.$LeakInfo_AVG[1]["leaklevel"];
						$TrapExInfo["PercentAvgLossAmount"]='0'.$Loss_array_per_1[0];
						$TrapExInfo["PercentAvgLossMoneyYear"]='0'.$Loss_array_per_1[1];
						$TrapExInfo["LeakBaseType"] = "608";

						$TrapExInfo["trapInfoGUID"]=$N_GUID;

						$res_trap = $M_TrapEx->add($TrapExInfo);
					}catch(Exception $e) {
						$this->WriteLog("log_error.txt","*insert error*".$N_GUID);
					}

					//泄露级别结束
					/////////////////////数据插入报警管理
					if(($c_array["ExLevel"]!="0" && $c_array["ExLevel"]!="") || $c_array["TemState"]==1){
						//数据插入报警管理
						$WN_DAO = M("warning");
						$W_I_Array["CompanyId"]=$comid;
						$W_I_Array["AreaId"]=$c_array["AreaId"];
						$W_I_Array["Area"]=$c_array["Area"];
						$W_I_Array["TrapNo"]=$c_array["TrapNo"];
						$W_I_Array["Location"]=$c_array["Location"];
						$W_I_Array["RepairState"]="0";
						$W_I_Array["TrapState"]="1";
						$W_I_Array["ExLevel"]=$c_array["ExLevel"];
						$W_I_Array["TemState"]=$c_array["TemState"];
						$W_I_Array["LevelDesc"]=$c_array["ExLevel"];
						$W_I_Array["AlertHZ"]=$c_array["HZNumberAVG"];
						$W_I_Array["AlertTem"]=$c_array["NewTem"];
						$W_I_Array["CreateTime"]=date('Y-m-d H:i:s');
						$W_I_Array["lossAmount"]=$Loss_array[0];

						//判断是否已插入数据
						$WN_SELECT_RES = $WN_DAO->where("CompanyId='$comid' and TrapNo='".$c_array["TrapNo"]."' and RepairState=0")->find();
						if(count($WN_SELECT_RES)<=0){
							$RES_wn_add = $WN_DAO->add($W_I_Array);
						}
					}
					
					/////////////////////////计算泄漏量-Analysis/////////////////////////////////
					$AN_RES_READ = $AN_RESLUT->where(" CompanyID=$comid and TrapNo='".$c_array["TrapNo"]."' ")->find();
					$_exlevel_DATA = $AN_RES_READ["lastlevel"];
					$_ALL_LEAK_DATA = 0;
					if($_exlevel_DATA!="0"){
						$_date_HOUR = floor((strtotime($c_array["DateCheck"])-strtotime($AN_RES_READ["datecheck"]))/3600);
						$_ALL_LEAK_DATA = $_date_HOUR*(double)$AN_RES_READ["lossvalue"] + (double)$AN_RES_READ["allleak"];
					}
					$AN_RES_ARRAY["LastLevel"] = $c_array["ExLevel"];
					$AN_RES_ARRAY["UseMTFI"] = $c_array["UseMTFI"];
					$AN_RES_ARRAY["TrapType"] = $c_array["TrapType"];
					$AN_RES_ARRAY["AreaID"] = $c_array["AreaId"];
					$AN_RES_ARRAY["LossValue"] = $c_array["LossAmount"];
					$AN_RES_ARRAY["AllLeak"] = $_ALL_LEAK_DATA;
					$AN_RES_ARRAY["DateCheck"] = $c_array["DateCheck"];
					
					$AN_RES = $AN_RESLUT->where(" CompanyID=$comid and TrapNo='".$c_array["TrapNo"]."' ")->data($AN_RES_ARRAY)->save();
					/////////////////////////////////////////////////////////////////
					
					
					$arr_all_result[$Index_FLAG]=$c_array;
					$Index_FLAG++;
				}
			}
		}
		//保存全部数据
		if($arr_all_result!=null){
			$end_res_ID = $TrapinfoDAO->addAll($arr_all_result,array(),true);

			/*
			foreach ($arr_all_result as $key_r) {
			  $end_res_ID = $TrapinfoDAO->add($key_r);
			}*/
			if((int)$end_res_ID>0){
				$end_res=1;
			}
		}
		echo $end_res;
	}else{
		echo "-3";
	}
}

//TMS-CS 统计分析
function getTraoInfoByAnalysis(){
	$area_where=$_POST["area"];
	$ty_where=$_POST["ty"];
	$tn_where=$_POST["trapno"];
	$cty_where=$_POST["cty"];
	$sql_head="SELECT MONTH(DateCheck) AS 'time' FROM trapinfo";
	$sql_end="GROUP BY MONTH(DateCheck) ASC";
	$sql_zheng="select COUNT(*)as count from trapinfo ";
	$sql_yi="select COUNT(*)as count from trapinfo ";
	$sql_zheng_e="and TrapState<=70 and MONTH(DateCheck)='";
	$sql_yi_e="and TrapState>70 and MONTH(DateCheck)='";
	$where_str=" CompanyID=".cookie("oid")." ";
	//$where_str=" CompanyID='".$this->comid."' ";
	$aa=date('Y-');
	$flag="0";
	if($area_where!=""){
		$where_str.=" and AreaId = '".$area_where."' ";
	}
	if($tn_where!=""){
		$where_str.=" and TrapNo like '%".$tn_where."%' ";
	}
	if($ty_where!=""){
		$jidu_s="";
		$jidu_e="";
		if($ty_where=="0"){
			$jidu_s=date('Y-m-01');
			$jidu_e=$this->getNextMonthDays($jidu_s);
			$sql_head="SELECT DAY(DateCheck) AS 'time' FROM trapinfo";
			$sql_end="GROUP BY DAY(DateCheck) ASC";
			$sql_zheng_e="and TrapState<=70 and DAY(DateCheck)='";
			$sql_yi_e="and TrapState>70 and DAY(DateCheck)='";
			$aa=date('m-');
		}else if($ty_where=="1"){
			$dangyue=date('m');
			if($dangyue>=1&&$dangyue<=3){
				$jidu_s=date('Y-01-01');
				$jidu_e=date('Y-04-01');
			}
			if($dangyue>=4&&$dangyue<=6){
				$jidu_s=date('Y-04-01');
				$jidu_e=date('Y-07-01');
			}
			if($dangyue>=7&&$dangyue<=9){
				$jidu_s=date('Y-07-01');
				$jidu_e=date('Y-10-01');
			}
			if($dangyue>=10&&$dangyue<=12){
				$jidu_s=date('Y-10-01');
				$jidu_e=(string)(date('Y')+1)."-01-01";
			}
		}else{

			$flag="1";
			$jidu_s=(string)(date('Y')-1);//date('Y-01-01');
			$jidu_e=(string)(date('Y'))."-12-31";
		}
		$where_str.="and DateCheck BETWEEN '".$jidu_s."' and '".$jidu_e."'";
	}
	$DAO_Trap=M("trapinfo");
	if($cty_where=="pie"){
		$select_normal=$DAO_Trap->where($where_str."and TrapState<=70")->count();
		$select_notnormal=$DAO_Trap->where($where_str."and TrapState>70")->count();
		echo $select_normal.",".$select_notnormal;
	}else{
		$result_zheng=array();
		$result_yi=array();
		$result_time=array();
		$model = M();
		$sql=$sql_head." where ".$where_str." ".$sql_end;
		$model_time=$model->query($sql);
		if($flag=="1"){
			$sql_head="SELECT YEAR(DateCheck) AS 'time' FROM trapinfo";
			$sql=$sql_head." where ".$where_str." ".$sql_end;
			$model_time_need=$model->query($sql);
		}
		for($i=0;$i<count($model_time);$i++){
			$riqi=$model_time[$i]["time"];
			if($flag=="1"){
				if($aa!=$model_time_need[$i]["time"]){
					$aa=$model_time_need[$i]["time"]."-";
				}
			}
			array_push($result_time,$aa.$riqi);
			$jieguo=$model->query($sql_zheng." where ".$where_str.$sql_zheng_e.$riqi."'");
			array_push($result_zheng,$jieguo[0]["count"]);
			$jieguo=$model->query($sql_yi." where ".$where_str.$sql_yi_e.$riqi."'");
			array_push($result_yi,$jieguo[0]["count"]);
		}
		$result["time"]=$result_time;
		$result["zheng"]=$result_zheng;
		$result["yi"]=$result_yi;
		echo json_encode($result);
	}

}

//TMS-android 获取节点详细信息
function GetTrapInfoData(){
	//echo "hello";
	$comid=$_GET["ci"];
	$lang=$_GET["lang"];
	$page=$_GET["page"];
	$pageSize=20;
	$data_post=$_POST["data"];
	if($comid==""||$data_post==""||$page==""){
		echo "0";
		exit;
	}
	$page=(int)$page;
	if($page==0){$page=1;}
	$pageCount=($page-1)*$pageSize;
	//echo "*".$pageCount."*";
	$data_arr=json_decode($data_post);
	$trapno=$data_arr->trapno;
	$area=$data_arr->area;
	$order=$data_arr->order;
	if($trapno=="" ||$area=="" || $order==""){
		echo "0";
		exit;
	}
	$index=0;
	$Area_Info=M("areainfo");
	$dataAll=array();
	$Area_Data=$Area_Info->where("CompanyID=".$comid." and AreaName='".$area."'")->select();
	if(count($Area_Data)>0){
		$PersonName=$Area_Data[0]["areauser"];
		$PersonTel=$Area_Data[0]["usertel"];
		$Trap_Info=M("trapinfo");
		$trap_sql="CompanyID=".$comid." and TrapNo='".$trapno."'"." and Area='".$area."'";
		$Trap_Count=$Trap_Info->where($trap_sql)->count();
		if($pageCount>=$Trap_Count){
			echo "1";//表示到底了
			exit;
		}
		$Trap_Data=$Trap_Info->where($trap_sql)->order($order)->limit($pageCount,$pageSize)->select();
		//$Warn_Info=M("warning");
		//$Warn_Data=$Warn_Info->where("CompanyID=".$comid." and Area='".$area."'"." and TrapNo='".$trap."'")->select();
		$dataAll=array();
		foreach ($Trap_Data as $key) {
			#$data_["createtime"]=date("Y-m-d H:i", strtotime($key["createtime"]));
			$data_["createtime"]=date("Y-m-d", strtotime($key["datecheck"]));
			$data_["createtime2"]=date("H:i", strtotime($key["datecheck"]));
			//$data_["createtime"]=$key["createtime"];
			if((int)($key["temstate"])>0){
				$data_["trapstate"]="-1";//低温
			}else{
				$data_["trapstate"]=$key["exlevel"];
			}
			// if($lang=="en_US"){
			//   $data_["trapstate"]=(int)($key["exlevel"])>0?"Defect":"Normal";
			// }else{
			//   $data_["trapstate"]=(int)($key["exlevel"])>0?"异常":"正常";
			// }
			$data_["newtem"]=$key["newtem"];
			$data_["exlevel"]=$key["exlevel"];
			$data_["lossamount"]=$key["lossamount"]=="0"?$key["lossamount"]:sprintf("%.2f", (float)$key["lossamount"]);
			//$data_["lossamount"]=$key["lossamount"];
			$data_["areauser"]=$PersonName;
			$data_["usertel"]=$PersonTel;
			$dataAll[$index]=$data_;
			$index++;
		}
		echo json_encode($dataAll);
	}
	else{
		echo "0";
	}

}