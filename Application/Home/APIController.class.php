<?php
namespace Home\Controller;
use Think\Controller;
///首页
class APIController extends Controller {

	// 单位换算
	///$TYPE_  0  压力   1 口径  2 温度  3 资源  4 泄漏  $LX 0 转为原始单位(非读取)
	public function DWHS($TYPE_, $U, $LX,$companyid)
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
		
		$PW_ARRAY = array("ton"=>0.001,"kg"=>1,"lb"=>2679.23);
		$LOSS_ARRAY = array("kg/h"=>1,"ton/h"=>0.001,"ton"=>0.001);

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
				$RES_=((double)$U)/((double)$PW_ARRAY[strtolower($UNITRES["pwunit"])]);
			}else{
				$RES_=((double)$U)*((double)$PW_ARRAY[strtolower($UNITRES["pwunit"])]);
			}
			
		}
		else if($TYPE_=="4")
		{
			if($LX==0){
				$RES_=((double)$U)/((double)$LOSS_ARRAY[strtolower($UNITRES["lossunit"])]);
			}else{
				$RES_=((double)$U)*((double)$LOSS_ARRAY[strtolower($UNITRES["lossunit"])]);
			}
		}
		return $RES_;
		///////////////////////////////////////////////////
	}
	//设置节点
	public function SetEnder()
	{
		$TrapModel = M("trapmodel");
		$CompanyID =  $_GET["cid"];
		if($_GET["type"]=="0"){
			//请求设置的地址
			$TRAP_RES = $TrapModel->where("companyid='".$CompanyID."' and InstallState=0")->select();
			echo $TRAP_RES[0]['trapno'];
		}else{
			//设置成功进行配置
			$MCUID = $_GET["mcuid"];
			$TrapNo = $_GET["trapno"];
			$UD["InstallState"]=1;
			$RES = $TrapModel->where("companyid='".$CompanyID."' and InstallState=0 and trapno='".$TrapNo."' ")->data($UD)->save();
			if($RES){
				echo "ok";
			}else {
				echo "no";
			}
		}

	}

	//TMS-CS 数据导入解析
	public function  InputData(){
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
	///TMS-CS获取异常节点
	public function GetAlertTrap(){
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

	public function DeleteTrapinfo(){
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$companyid = $Res_Data->cid;
		$OPID = $Res_Data->opid;
		$TrapNO = $Res_Data->tno;

		$trapmodelDao = M("trapmodel");
		$RES_DELETE1 = $trapmodelDao->where("Id=$OPID and CompanyID=$companyid and trapNo='$TrapNO'")->delete();
		$trapinfoDao = M("trapinfo");
		$RES_DELETE2 = $trapinfoDao->where("TrapNo='$TrapNO' and CompanyID=$companyid")->delete();

		echo $RES_DELETE1."*".$RES_DELETE2;


	}

	///节点信息列表获取
	public function GetTrapInfo()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$companyid = $Res_Data->cid;
		$areaname = $Res_Data->areaid;
		$wh = $Res_Data->wh;
		$page = $Res_Data->page;
		$pagesize = $Res_Data->pagesize;
		$LIKE = $Res_Data->like;
		$local = $Res_Data->local;
		



		$trapmodelDao = M("trapmodel");

		if($companyid.""==""){ exit; }
		$where_str = " 1=1 ";
		$where_str .= " and companyid=".$companyid;
		if($areaname!=""){
			$where_str .= " and areaid in ('".$areaname."')";
		}
		if($wh!=""){
			if($LIKE.""=="0"){
				$where_str .= " and trapname = '".$wh."'";
			}else{
				$where_str .= " and trapname like '%".$wh."%'";
			}
		}
		if($local!=""){
			if($LIKE.""=="0"){
				$where_str .= " and location = '".$local."'";
			}else{
				$where_str .= " and location like '%".$local."%'";
			}
		}

		$ListData = $trapmodelDao->where($where_str)->limit(((int)$page-1)*(int)$pagesize,(int)$pagesize)->order("ordernum,id")->select();
		$ListCount = $trapmodelDao->where($where_str)->count();
		$show_list_str = '{"res":[';
		foreach ($ListData as $data_trapmodel) {
			
			$data_trapmodel["spressure"] = $this->DWHS("0",$data_trapmodel["spressure"],1,$companyid);
			$data_trapmodel["spressureout"] = $this->DWHS("0",$data_trapmodel["spressureout"],1,$companyid);
			
			$data_trapmodel["linesize"] = $this->DWHS("1",$data_trapmodel["linesize"],1,$companyid);
			
			$show_list_str .= '{';
			$show_list_str .= '"id":"'.$data_trapmodel["id"].'",';
			$show_list_str .= '"trapno":"'.$data_trapmodel["trapno"].'",';
			$show_list_str .= '"area":"'.$data_trapmodel["area"].'",';
			$show_list_str .= '"areaid":"'.$data_trapmodel["areaid"].'",';
			$show_list_str .= '"location":"'.$data_trapmodel["location"].'",';
			$show_list_str .= '"maxlk":"'.$data_trapmodel["maxlk"].' mm",';
			$show_list_str .= '"usemtfi":"'.$data_trapmodel["usemtfi"].'",';
			$show_list_str .= '"trapname":"'.$data_trapmodel["trapname"].'",';
			$show_list_str .= '"traptype":"'.$data_trapmodel["traptype"].'",';
			$show_list_str .= '"linktype":"'.$data_trapmodel["linktype"].'",';
			$show_list_str .= '"linesize":"'.$data_trapmodel["linesize"].'",';
			$show_list_str .= '"outtype":"'.$data_trapmodel["outtype"].'",';
			$show_list_str .= '"spressure":"'.$data_trapmodel["spressure"].'",';
			$show_list_str .= '"spressureout":"'.$data_trapmodel["spressureout"].'",';
			$show_list_str .= '"modelname":"'.$data_trapmodel["modelname"].'",';
			$show_list_str .= '"ordernum":"'.$data_trapmodel["ordernum"].'",';
			$show_list_str .= '"pagecount":"'.$ListCount.'"';
			$show_list_str .= '},';
		}
		$show_list_str=rtrim($show_list_str,",");
		$show_list_str .= ']}';
		echo $show_list_str;
	}
	
	///删除阀门节点
	public function DeleteModel()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$companyid=$Res_Data->cid;
		$trapno = $Res_Data->trapno;
		$dataid = $Res_Data->id;
		
		$sql1="insert into trapmodel_delete(Id,CompanyID,ModelName,AreaId,Area,location,trapNo,houzhui,trapName,TrapType,UseMTFI,SPressure,SPressureOut,STempTop,STempLow,LineSize,LinkType,OutType,OrderNum,DZ,InstallState,SetState,MCUID,MaxLK,CriticalValue,CollectionNum,RevealPer,ZoomMultiple,MinHZ,MaxHZ,ComSwitch,WorkTEM,LeakBase,MaxPercent,AvgPercent,ShellNo) select Id,CompanyID,ModelName,AreaId,Area,location,trapNo,houzhui,trapName,TrapType,UseMTFI,SPressure,SPressureOut,STempTop,STempLow,LineSize,LinkType,OutType,OrderNum,DZ,InstallState,SetState,MCUID,MaxLK,CriticalValue,CollectionNum,RevealPer,ZoomMultiple,MinHZ,MaxHZ,ComSwitch,WorkTEM,LeakBase,MaxPercent,AvgPercent,ShellNo from trapmodel where id=$dataid and CompanyID=".$companyid." and trapNo='".$trapno."';";
		
		$Dao = M();
		$res = $Dao->execute($sql1);
		if($res>0)
		{
			$res = $Dao->execute("delete from trapmodel where id=$dataid and CompanyID=$companyid and trapNo='".$trapno."'");
			echo "{\"res\":\"".$res."\"}";
		}else{
			echo "{\"res\":\"0\"}";
		}
	}
	
	//TMS-CS
	public function UpdateDataByOP()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$companyid=$Res_Data->cid;
		$TableName=$Res_Data->tab;
		$ZDName=$Res_Data->zd;
		$ZDValue=$Res_Data->value;
		$OPID = $Res_Data->id;

		$OTHEROP = $Res_Data->other;

		$Dao=M();
		$res = $Dao->execute("update ".$TableName." set ".$ZDName."='".$ZDValue."'".$OTHEROP." where id=".$OPID." and companyid=".$companyid);
		echo $res;

	}
	//TMS-CS  查询数据如果没有数据添加
	public  function findData(){
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
	public  function  savaExcel(){
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


	//TMS-CS  用户列表展示
	public  function  subAccount(){
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$Dao_User= M("subuser");
		$companyid=$Res_Data->cid;
		$loginname=$Res_Data->loginName;

		$currentPage=$Res_Data->page;
		$pagesize=$Res_Data->pagesize;
		$AllPage = 1;
		$List_ALLCount=$Dao_User->where("AdminName='".$loginname."' and companyid=".$companyid)->select();
		$User=$Dao_User->where("AdminName='".$loginname."' and companyid=".$companyid)->limit(($currentPage-1)*$pagesize,$pagesize)->select();
		$show_list_str='{"res":[';
		foreach ($User as $user_each) {
			$LOGIN_TIME=$user_each["lastlogintime"]==""?L("L_AU_No_Record"):date("Y-m-d H:i:s",strtotime($user_each["lastlogintime"]));
			$show_list_str.='{"id":"'.$user_each["id"].'","username":"'.$user_each["username"].'",';
			$show_list_str.='"loginname":"'.$user_each["loginname"].'",';
			$show_list_str.='"usertel":"'.$user_each["usertel"].'",';
			$show_list_str.='"useremail":"'.$user_each["useremail"].'",';
			$show_list_str.='"areaid":"'.$user_each["areaid"].'",';
			$show_list_str.='"areaname":"'.$user_each["areaname"].'",';
			$st = $user_each["status"]=="1"?"1":"0";
			$show_list_str.='"state":"'.$st.'",';
			//$show_list_str.='"logintime":"\/Date('.date("Y-m-d ",strtotime($user_each["lastlogintime"])).')\/",';
			$show_list_str.='"logintime":"'.$LOGIN_TIME.'",';
			$show_list_str.='"perset":"'.$user_each["permissionset"].'",';
			$show_list_str = rtrim($show_list_str,",");
			$show_list_str.="},";
		}
		$show_list_str = rtrim($show_list_str,",");
		$show_list_str.='],"datacount":"'.count($List_ALLCount).'"}';
		echo $show_list_str;
	}
	
	
	
	///TMS-CS 账户管理-禁/启用户操作
	public function DisableUser(){
		$JsonData=$_POST["data"];
		if($JsonData==""){
			echo '{"res":"0"}';
			exit;
		}
		$JsonData=json_decode($JsonData);
		$uid=$JsonData->oid;
		$changeState=$JsonData->status;
		if($uid==""||$changeState==""){
			echo '{"res":"0"}';
			exit;
		}
		$Dao_Model=M("subuser");
		$data_["Status"]=$changeState;
		$Dao_result=$Dao_Model->where("Id=".$uid)->save($data_);
		if($Dao_result!=false &&$Dao_result>0){
			echo '{"res":"1"}';
		}else{
			echo '{"res":"0"}';
		}
	}
	//TMS-CS 报警列表误报
	public  function ErrorReport(){
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
	//TMS-CS  报警列表误报添加
	public  function AddErrorReport(){
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$Dao_Learningtrap= M("learntrap");
		$companyid=$Res_Data->cid;
		$wariningid=$Res_Data->wid;
		$trapno=$Res_Data->trapno;
		$alerttem=$Res_Data->alerttem;
		$alerthz=$Res_Data->alerthz;
		$standardtem=$Res_Data->standardtem;
		$alerttype=$Res_Data->alerttype;
		$fluidtype=$Res_Data->fluidtype;
		$pressure=$Res_Data->pressure;
		$mintem=$Res_Data->mintem;
		$maxtem=$Res_Data->maxtem;
		$data['CompanyID']=$companyid;
		$data['TrapNO']=$trapno;
		$data['FristValue']=$standardtem;
		$data['AlertValue']=$alerttem;
		$data['AlertHZ']=$alerthz;
		$data['AlertType']= $alerttype;
		$data['TType']=  $fluidtype;
		$data['TPValue']=$pressure;
		$data['MinTem']= $mintem;
		$data['MaxTem']=$maxtem;
		$Trap_MODEL_DAO = M("trapmodel");

		$TRAP_RES = $Trap_MODEL_DAO->where("trapNo='".$data['TrapNO']."' and companyid=".$companyid)->find();

		$data['SPValue']=$TRAP_RES['SPressure'];

		$ALL_DATA_LT=$Dao_Learningtrap->where("CompanyID=".$companyid)->select();
		$AVG_HZ=0.00;
		$AVG_TEM=0.00;
		$MAX_TEM=0.00;
		$MAX_HZ=0.00;
		$ALL_Count=0;
		foreach ($ALL_DATA_LT as $key_LT) {
			$ALL_Count++;
			$AVG_HZ += (double)$key_LT["NewHZ"];
			$AVG_TEM += (double)$key_LT["NewValue"];
			if((double)$key_LT["NewValue"]>$MAX_TEM){
				$MAX_TEM=(double)$key_LT["NewValue"];
			}
			if((double)$key_LT["NewHZ"]>$MAX_HZ){
				$MAX_HZ = (double)$key_LT["NewHZ"];
			}
		}
		$AVG_TEM = $AVG_TEM/(double)count($ALL_DATA_LT);
		$AVG_HZ = $AVG_HZ/(double)count($ALL_DATA_LT);

		if($AVG_TEM>$data['AlertValue']){
			$data['NewValue'] = $AVG_TEM;
		}else {
			if($MAX_TEM>$data['AlertValue']){
				$data['NewValue'] = (double)(((double)$AVG_TEM+(double)$MAX_TEM)/2);
			}else {
				$data['NewValue'] = $data['AlertValue'];
			}
		}
		if($AVG_HZ>$data['AlertHZ']){
			$data['NewHZ'] = $AVG_HZ;
		}else {
			if($MAX_HZ>$data['AlertHZ']){
				$data['NewHZ'] = (double)(((double)$AVG_HZ+(double)$MAX_HZ)/2);
			}else {
				$data['NewHZ'] = $data['AlertHZ'];
			}
		}
		$data['CreateTime'] = date("Y-m-d H:i:s");
		$data['AlertCount'] = $ALL_Count+1;

		$result =$Dao_Learningtrap->add($data);
		if($result>0){
			$Waring_=M("warning");
			$wariningData["LearnState"]="1";
			$model=$Waring_->where("Id=".$wariningid)->save($wariningData);
			if($model!=false && $model>0)
			{
				echo '{"res":"1"}';
			}
			else{
				echo '{"res":"0"}';
			}
		}else {
			echo '{"res":"0"}';
		}
	}

	//TMS-CS  报警列表编辑
	public function WarningEdit(){
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
	public function WarningUpdate(){
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
	public function GetWarningList(){
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
			$show_list_str.='"linesize":"'.$this->DWHS("1",$warning_each["linesize"],1,$companyid).'",';
			$show_list_str.='"spressure":"'.$this->DWHS("0",$warning_each["spressure"],1,$companyid).'",';
			$show_list_str.='"spressureout":"'.$this->DWHS("0",$warning_each["spressureout"],1,$companyid).'",';
			$show_list_str.='"outtype":"'.$warning_each["outtype"].'",';
			
			//date("Y-m-d",strtotime($warning_each["repairtime"]))
			//"\/Date('.date("Y-m-d H:i",strtotime($warning_each["repairtime"])).')\/",';
			$show_list_str.='"repairnum":"'.$warning_each["repairnum"].'",';
			$show_list_str.='"repairprice":"'.$warning_each["repairprice"].'",';
			$TST = $warning_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");
			$show_list_str.='"trapstate":"'.$TST.'",';
			$show_list_str.='"exlevel":"'.$warning_each["exlevel"].'",';
			$show_list_str.='"leveldesc":"'.$warning_each["leveldesc"].'",';
			$show_list_str.='"alerttem":"'.$this->DWHS("2",$warning_each["alerttem"],1,$companyid).'",';
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
	//TMS-CS 账户管理-删除用户
	public function DelUser(){
		$JsonData=$_POST["data"];
		if($JsonData==""){
			echo '{"res":"0"}';
			exit;
		}
		$JsonData=json_decode($JsonData);
		$uid=$JsonData->oid;
		if($uid==""){
			echo '{"res":"0"}';
			exit;
		}
		$Dao_Model=M("subuser");
		$Dao_result=$Dao_Model->where("Id=".$uid)->delete();
		if($Dao_result!=false &&$Dao_result>0){
			echo '{"res":"1"}';
		}else{
			echo '{"res":"0"}';
		}
	}

	//TMS-CS 账户编辑和添加
	public function SubuserControl(){
		$postData=$_POST["data"];
		if($postData==""){
			echo '{"res":"0"}';
			exit;
		}
		$_POST=json_decode($postData);
		$post_key=$_POST->key;
		$post_val=$_POST->val;
		$post_type=$_POST->type;
		if($post_key==""||$post_key==""||$post_type==""){
			echo '{"res":"0"}';
			exit;
		}
		$str_="";
		$post_oid="";
		$user_info=array();
		if($post_type=="update"){
			$post_oid=$_POST->uid;
			$str_.=" and Id !=".$post_oid;
		}
		$user_info["CompanyID"]=$_POST->oid;
		$user_info["AdminName"]=$_POST->admin;
		$add_key=explode(',', $post_key);
		$add_val=explode(',', $post_val);
		$len_=count($add_key);
		for($i=0;$i<$len_;$i++){
			if($add_key[$i]=="PassWord"){
				$user_info[$add_key[$i]]=strtolower(md5($add_val[$i]));
			}else{
				$user_info[$add_key[$i]]=$add_val[$i];
			}
		}
		$where_str="Id=".$user_info["CompanyID"]." and LoginName='".$user_info["LoginName"]."'";
		$dao_model=M("companyinfo");
		$model_result=$dao_model->where($where_str)->find();
		if($model_result!=null && $model_result["id"]>0){
			echo '{"res":"-1"}';
			exit;
		}
		$DAO_Model=M("subuser");
		$where_str="CompanyID=".$user_info["CompanyID"]." and AdminName='".$user_info["AdminName"]."' and LoginName='".$user_info["LoginName"]."'".$str_;
		$result_model=$DAO_Model->where($where_str)->find();
		if($result_model!=null && $result_model["id"]>0){
			echo '{"res":"-1"}';
			exit;
		}
		if($post_type=="update"){
			$result=$DAO_Model->where("Id='".$post_oid."'")->save($user_info);
		}else{
			//$user_info["CompanyName"]=$_POST->cname;
			$Dao_=$dao_model->where("Id=".$user_info["CompanyID"])->find();
			$user_info["CompanyName"]=$Dao_["companyname"];
			$user_info["Status"]="1";
			$result=$DAO_Model->add($user_info);
		}
		if($result!=false&&$result>-1){
			echo '{"res":"1"}';
		}else{
			echo '{"res":"0"}';
		}
	}

	//TMS-CS 区域信息列表获取
	/*2017-02-07 林静添加
	  添加普通用户权限，只能查看自己负责的区域信息
	*/
	public function getAreaListDrapDown()
	{
		$comid=$_GET["ci"];
		$data_=$_POST["data"];
		if($comid==""||$data_==""){
			echo '{"res":[]}';
			exit;
		}
		$Json_Data=json_decode($data_);
		$user_status=$Json_Data->status;
		$user_name=$Json_Data->loginname;
		if($user_status==""||$user_name==""){
			echo '{"res":[]}';
			exit;
		}
		// $user_status=$_GET["status"];
		// $user_name=$_GET["loginname"];
		$wherestr="CompanyID=".$comid;
		if($user_status=="user"){
			//$wherestr.=" and AreaUser='".$user_name."'";
		}
		$areainfoDAO = M("areainfo");
		$AreaList = $areainfoDAO->where($wherestr)->select();
		$areaJson='{"res":[';
		foreach ($AreaList as $area) {
			$areaJson.="{";
			$areaJson.='"id":"'.$area["id"].'",';
			$areaJson.='"name":"'.$area["areaname"].'"';
			$areaJson.="},";
		}
		$areaJson = rtrim($areaJson,",");
		$areaJson.=']}';
		echo $areaJson;
	}
	/*是否符合规则的判断*/
	public function CheckRoleInfo_GET($opCompanyId)
	{
		$res_="";
		try{
			$DAO_ROLE=M("roleadmin");
			$LIST_DAO = $DAO_ROLE->where("(CompanyID=0 or CompanyID=".$opCompanyId.")")->select();
			foreach ($LIST_DAO as $RoleItem) {
				if(((int)$RoleItem["opdesc"])>0){
					$res_ =" and NOW()>=(createtime + INTERVAL ".$RoleItem["opdesc"]." MINUTE)";
				}
			}
		} catch (Exception $e) {
		}
		return $res_;
	}
	/*是否符合规则的判断-数据修改前的插入*/
	public function CheckRoleInfo_ADD($opCompanyId,$DataSTR,$FZBL)
	{
		$DAO_ROLE=M("roleadmin");
		$LIST_DAO = $DAO_ROLE->where("(CompanyID=0 or CompanyID=".$opCompanyId.")")->select();
		$res_="1=1 ";
		foreach ($LIST_DAO as $RoleItem) {
			if(((int)$RoleItem["opdesc"])==0){
				//始终正常
				if(((int)$FZBL["bl"])>30){
					$FZBL["maxfz"]=rand(4,6);
					$FZBL["minfz"]=rand(2,4);
					$FZBL["avgfz"]=rand(4,5);
					$FZBL["bl"] = 0;
					$FZBL["tem"] = 170;
					$DAO_CHANGE_TRAP = M("changetrapinfo");
					$DAO_CHANGE_TRAP->add();
				}
			}else if(((int)$RoleItem["opdesc"])==-1) {
				//始终异常
				if(((int)$FZBL["bl"])<=30){
					$FZBL["maxfz"]=rand(104,126);
					$FZBL["minfz"]=rand(14,16);
					$FZBL["avgfz"]=rand(86,95);
					$FZBL["bl"]= 99;
					$FZBL["tem"] = 170;
					$DAO_CHANGE_TRAP = M("changetrapinfo");

				}
			}
		}
		return $FZBL;
	}
	///TMS-CS 全部节点列表
	public function GetSupList(){

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
		$local = $Res_Data->local;


		if($area_where=="全部" || $area_where=="All" || $area_where=="all"){$area_where="";}
		$tstate_where_w="";
		if($tstate_where=="全部" || $tstate_where=="Reveal All" || $tstate_where=="All" || $tstate_where=="all" ){
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
		if($local!=""){
			$where_str.=" and location like '%".$local."%' ";
			$Where_Model.=" and location like '%".$local."%' ";
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


		$List_Count=count($DAO_Trap->query("select * from (select * from (select tm.*,ti.trapmodel,ti.newtem as newtem,ti.datecheck as datecheck,ti.description as description,ti.exlevel as exlevel,ti.lossamount as lossamount,ti.temstate as temstate,ti.createtime as createtime,ti.Battery as battery from  (select * from trapmodel where $Where_Model ) as tm  left join  (select * from trapinfo where $Where_Model order by datecheck desc) as ti on tm.trapno = ti.trapno order by datecheck desc) as tt group by trapno) as t2 where $where_str  order by $order"));
		$show_list_str='{"res":[';//']}}';
		foreach ($List_ALL as $trap_each) {
			$show_list_str.='{"trapno":"'.$trap_each["trapno"].'",';
			$show_list_str.='"trapname":"'.$trap_each["trapname"].'",';
			$show_list_str.='"batterydate":"'.$trap_each["batterydate"].'",';
			$show_list_str.='"area":"'.$trap_each["area"].'",';
			$show_list_str.='"areaid":"'.$trap_each["areaid"].'",';
			$show_list_str.='"outtype":"'.$trap_each["outtype"].'",';
			$ZT = ((int)$trap_each["exlevel"])>0?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");
			$show_list_str.='"trapstate":"'.$ZT.'",';
			$show_list_str.='"traptype":"'.$trap_each["traptype"].'",';
			$show_list_str.='"trapmodel":"'.$trap_each["modelname"].'",';
			$show_list_str.='"modelname":"'.$trap_each["modelname"].'",';
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
			$LOSS_WD=$this->DWHS("4",$trap_each["lossamount"],1,$CompanyID_);
			$show_list_str.='"lossamount":"'.round($LOSS_WD,2).'",';//$this->DWHS("4",$trap_each["lossamount"],1,$CompanyID_)
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
	public function GetSupListByTrap(){

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

	public function GetSupListByTrap_CS(){

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

	///TMS-CS登录
	public function LoginData(){
		$JsonData = $_POST["data"];
		$flag=$_GET["flag"];
		if($JsonData==""){
			echo '{"res":"0"}';
			exit;
		}
		if($flag==""){
			$flag="0";
		}
		$Res_Data = json_decode($JsonData);
		$UN = $Res_Data->username;
		$PD = $Res_Data->password;
		if($UN==""||$PD==""){
			echo '{"res":"0"}';
			exit;
		}

		$Model_CompanyInfo_Dao = M("companyinfo");
		$res_ = $Model_CompanyInfo_Dao->where("loginname='".$UN."' and loginpwd='".md5($PD)."' and UserFlag='".$flag."'")->find();
		$ComapnyName=$res_["companyname"]."";
		if($res_!=null && (int)$res_["id"]>0){
			$updateD["LastLoginTime"]=date("Y-m-d H:i:s");
			$result_=$Model_CompanyInfo_Dao->where("Id=".$res_["id"])->save($updateD);

			if($result_!=false && $result_>0){
				cookie("oid",$res_['id']);
				$days=-1;//$this->GetDiskSize($res_["id"]);
				echo '{"res":"'.$res_['id'].'","data":"admin","per":"11111111","days":"'.$days.'","comname":"'.$ComapnyName.'","username":"'.$res_['username'].'","regTime":"'.$res_['createtime'].'","areaid":"","SPUnit":"'.$res_['spunit'].'","lossUnit":"'.$res_['lossunit'].'","temUnit":"'.$res_['temunit'].'","sizeUnit":"'.$res_['sizeunit'].'","pwUnit":"'.$res_['pwunit'].'","workinghours":"'.$res_['workinghours'].'","workingdays":"'.$res_['workingdays'].'","addlogo":"'.$res_['addlogo'].'","moneyton":"'.$res_['moneyton'].'","moneyunit":"'.$res_['moneyunit'].'","loginname":"'.$res_['loginname'].'"}';
				exit;
			}
			echo '{"res":"0"}';
		}else {
			$UserModel=M("subuser");
			$res_ = $UserModel->where("loginname='".$UN."' and PassWord='".md5($PD)."' and UserFlag='".$flag."'")->find();
			if($res_!=null && (int)$res_["id"]>0){
				if($res_["status"]=="1"){

					$updateD["LastLoginTime"]=date("Y-m-d H:i:s");
					$result_=$UserModel->where("Id=".$res_["id"])->save($updateD);
					if($result_!=false && $result_>0){
						cookie("oid",$res_['id']);
						$days=-1;//$this->GetDiskSize($res_["companyid"]);
						
						$_sub_com_result_=$Model_CompanyInfo_Dao->where("Id=".$res_["companyid"])->find();
						
						echo '{"res":"'.$res_['companyid'].'","data":"user","per":"'.$res_['permissionset'].'","username":"'.$res_['username'].'","days":"'.$days.'","comname":"'.$res_['companyname'].'","areaid":"'.$res_['areaid'].'","regTime":"'.$res_['areaid'].'","SPUnit":"'.$_sub_com_result_['spunit'].'","lossUnit":"'.$_sub_com_result_['lossunit'].'","temUnit":"'.$_sub_com_result_['temunit'].'","sizeUnit":"'.$_sub_com_result_['sizeunit'].'","pwUnit":"'.$_sub_com_result_['pwunit'].'","workinghours":"'.$_sub_com_result_['workinghours'].'","workingdays":"'.$_sub_com_result_['workingdays'].'","addlogo":"'.$_sub_com_result_['addlogo'].'","moneyton":"'.$res_['moneyton'].'","moneyunit":"'.$res_['moneyunit'].'","loginname":"'.$res_['loginname'].'"}';
						exit;
					}
					echo '{"res":"0"}';
				}else {
					echo '{"res":"-1"}';
				}
			}else{
				echo '{"res":"0"}';
			}
		}

	}
	public function trapinfoAdd()
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
	//更新外壳编号
	public function trapmodel_shell_()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$CID = $Res_Data->id;
		$LoRa = $Res_Data->lora;
		$Shell = $Res_Data->shell;

		$MODEL_DAO = M("trapmodel");
		$DATA["ShellNo"]=$Shell;
		$RES = $MODEL_DAO->where("CompanyID=$CID and trapNo='$LoRa'")->save($DATA);
		echo "$RES";

	}
	///TMS-cs 节点信息添加
	public function trapmodelAddUpdate()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);

		$ID = $Res_Data->id;

		$trapmodelDAO = M("trapmodel");
		$AnyDAO = M("analysis");

		$Data_ADD["CompanyID"] = $Res_Data->cid;
		$Data_ADD["AreaId"] = $Res_Data->areaid;
		$Data_ADD["Area"] = $Res_Data->area;
		$Data_ADD["location"] = $Res_Data->location;
		$Data_ADD["trapNo"] = $Res_Data->trapno;
		$Data_ADD["trapName"] = $Res_Data->trapname;
		$Data_ADD["TrapType"] = $Res_Data->traptype;
		$Data_ADD["UseMTFI"] = $Res_Data->pp;
		$Data_ADD["SPressure"] = $this->DWHS("0",$Res_Data->sp,0,$Data_ADD["CompanyID"]);
		$Data_ADD["SPressureOut"] = $this->DWHS("0",$Res_Data->spout,0,$Data_ADD["CompanyID"]);
		$Data_ADD["STempTop"] = $this->DWHS("2",$Res_Data->temup,0,$Data_ADD["CompanyID"]);
		$Data_ADD["STempLow"] = $this->DWHS("2",$Res_Data->temlow,0,$Data_ADD["CompanyID"]);
		$Data_ADD["LineSize"] = $this->DWHS("1",$Res_Data->linksize,0,$Data_ADD["CompanyID"]);
		$Data_ADD["LinkType"] = $Res_Data->linketype;
		$Data_ADD["OutType"] = $Res_Data->outtype;
		$Data_ADD["OrderNum"] = $Res_Data->ordernum;
		$Data_ADD["MaxLK"] = $Res_Data->maxlk;
		$Data_ADD["ModelName"] = $Res_Data->modelname;
		
		$Data_AN["CompanyID"] = $Res_Data->cid;
		$Data_AN["TrapNo"] = $Res_Data->trapno;
		$Data_AN["AreaID"] = $Res_Data->areaid;
		$Data_AN["Area"] = $Res_Data->area;
		$Data_AN["UseMTFI"] = $Res_Data->pp;
		$Data_AN["TrapType"] = $Res_Data->traptype;
		
		
		if($ID!="0"){
			$Data_ADD["Id"] = $ID;
			$res = $trapmodelDAO->save($Data_ADD);
			$res2 = $AnyDAO->where("CompanyID=".$Data_AN["CompanyID"]." and TrapNo='".$Data_AN["TrapNo"]."'")->save($Data_AN);
			
			
			if($res>0){
				$trapinfoDAO = M();
				$RE = $trapinfoDAO->execute("update trapinfo set trapName='".$Data_ADD["trapName"]."' where  trapNo='".$Data_ADD["trapNo"]."' and CompanyID='".$Data_ADD["CompanyID"]."'");
			}
			echo '{"res":"'.$res.'"}';
			exit;
		}
		$select_res_by_tn = $trapmodelDAO->where("trapNo='".$Data_ADD["trapNo"]."' and CompanyID='".$Data_ADD["CompanyID"]."'")->select();


		if(count($select_res_by_tn)==0 && $ID == "0"){
			$res = $trapmodelDAO->add($Data_ADD);
			$res2 = $AnyDAO->add($Data_AN);
			echo '{"res":"'.$res.'"}';
		}else {
			echo '{"res":"-9"}';//节点已存在
		}
	}

	///TMS-cs 根据节点编号节点信息读取是否存在 大于0则已存在
	public function getTrapmodelByTrapNo()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$TrapNo = $Res_Data->tn;
		$Companyid = $_GET["ci"];
		$trapmodelDAO = M("trapmodel");
		$select_res_by_tn = $trapmodelDAO->where("trapNo='".$TrapNo."' and CompanyID=".$Companyid)->select();
		echo '{"res":"'.count($select_res_by_tn).'"}';
	}

	///TMS-cs 节点信息读取
	public function getTrapmodelByID()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$TrapID = $Res_Data->id;
		$COMID = $Res_Data->cid;
		$trapmodelDAO = M("trapmodel");
		$select_res_by_tn = $trapmodelDAO->where("id='".$TrapID."' and companyid='".$COMID."'")->find();

		$res_json_str = '{"res":[';
		$res_json_str.= '{';
		$res_json_str.='"areaid":"'.$select_res_by_tn["areaid"].'",';
		$res_json_str.='"area":"'.$select_res_by_tn["area"].'",';
		$res_json_str.='"location":"'.$select_res_by_tn["location"].'",';
		$res_json_str.='"trapno":"'.$select_res_by_tn["trapno"].'",';
		$res_json_str.='"trapname":"'.$select_res_by_tn["trapname"].'",';
		$res_json_str.='"traptype":"'.$select_res_by_tn["traptype"].'",';
		$res_json_str.='"usemtfi":"'.$select_res_by_tn["usemtfi"].'",';
		$res_json_str.='"spressure":"'.$this->DWHS(0,$select_res_by_tn["spressure"],1,$COMID).'",';
		$res_json_str.='"spressureout":"'.$this->DWHS(0,$select_res_by_tn["spressureout"],1,$COMID).'",';
		$res_json_str.='"stemptop":"'.$this->DWHS(2,$select_res_by_tn["stemptop"],1,$COMID).'",';
		$res_json_str.='"stemplow":"'.$this->DWHS(2,$select_res_by_tn["stemplow"],1,$COMID).'",';
		$res_json_str.='"linesize":"'.$this->DWHS(1,$select_res_by_tn["linesize"],1,$COMID).'",';
		$res_json_str.='"linktype":"'.$select_res_by_tn["linktype"].'",';
		$res_json_str.='"outtype":"'.$select_res_by_tn["outtype"].'",';
		$res_json_str.='"ordernum":"'.$select_res_by_tn["ordernum"].'",';
		$res_json_str.='"modelname":"'.$select_res_by_tn["modelname"].'",';
		$res_json_str.='"maxlk":"'.$select_res_by_tn["maxlk"].'"';
		$res_json_str.= '}';
		$res_json_str.= ']}';

		echo $res_json_str;
	}
	///TMS-cs 节点信息读取
	public function getTemByPressure ()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$pressure = $Res_Data->pressure ;//压力 单位 Mpa = bar / 10
		$ptcontrast_DAO =  M("ptcontrast");
		$result = $ptcontrast_DAO->where("pressure=".$pressure)->find();
		echo '{"res":"'.$result["temperature"].'"}';
	}
	public function getTemByPressure0 ($pressure)
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

	//泄露量函数
	///根据超声频率判断泄露量，$DataHZOP,频率  $TrapP节点压力 单位kPa $TrapTem温度摄氏度
	public function OutLevel($DataHZOP,$TrapP,$TrapTem,$companyid,$XLKJ,$EXLEVEL){
		/*
		$MD = 0.8035;//蒸汽密度
		if((double)$TrapP<1000){
		$MD = 0.6358+0.00499*(double)$TrapP;
		}else if ((double)$TrapP<1500) {
		$MD = 0.6246+0.00505*(double)$TrapP;
		}else {
		$UsP = (double)$TrapP/1000;
		$MD = 19.44*(double)$UsP/(double)$TrapTem-0.151*(double)$UsP+2.1627;
		    		}

		$LevelSpeed=0.00;//泄露的射流速度 m/s
		$P0=101325.00; //环境绝对气压（默认为标准大气压） 单位Pa
		$Sigma=$P0/(double)$DataHZOP*1000;
		$PsiSigma=0.2588;
		if($Sigma>0.528){
		$PsiSigma=sqrt(pow($Sigma,2/2.646)-pow($Sigma,(1.4-1)/1.4));
		    		}
		$LevelSpeed=2.646*101325*$PsiSigma/sqrt(8.314*$TrapTem);//获得射流速度 m/s
		*/
		$this->WriteLog("log_ll.txt","&&&".$EXLEVEL."&&&");
		if($EXLEVEL.""=="0" || $EXLEVEL.""=="" ){
			return "0*0*0";
		}
		//泄露口径
		//$D = 0;//$LevelSpeed/$DataHZOP/0.012;
		$D = (double)$EXLEVEL*(double)$XLKJ/10;
		//$D = ((double)$DataHZOP[9]-(double)$DataHZOP[5])/((double)$DataHZOP[7]-(double)$DataHZOP[5])*(double)$XLKJ;
		//$WD2YL=M("ptcontrast");
		//$WD_YL_RES = $WD2YL->where(" temperature>='".$TrapTem."' and temperature<='".((double)$TrapTem)+3."' ")->select();
		//蒸汽压力 单位bar
		$ZQYL=(double)$TrapP;//*10000;//((double)$WD_YL_RES[0]["pressure"])*10;;

		//冷凝压力 单位bar
		$ZQLNYL=0.00;//0.01

		//每小时泄露量
		$HoursLossF=0;
		$HoursLossR=0;
		$this->WriteLog("log_ll.txt","(((double)$ZQYL*100+101.3)*(double)$D*(double)$D*0.004123*0.55*0.6*1.5845386)*0.5");
		if((double)$ZQLNYL>0){
			//Fail open
			//if()
			$HoursLossF = (((double)$ZQYL*100+101.3)*(double)$D*(double)$D*0.004123*0.55*0.6*1.5845386)*0.25;
			//Rapid open
			$HoursLossR = (((double)$ZQYL*100+101.3)*(double)$D*(double)$D*0.004123*0.55*0.6*1.5845386)*0.25;
		}else{
			//Fail open
			$HoursLossF = (((double)$ZQYL*100+101.3)*(double)$D*(double)$D*0.004123*0.55*0.6*1.5845386);
			//Rapid open
			$HoursLossR = (((double)$ZQYL*100+101.3)*(double)$D*(double)$D*0.004123*0.55*0.6*1.5845386)*0.5;
		}


		$CompanyInfoDao = M("companyinfo");
		$companyinfo = $CompanyInfoDao->where("id="+$companyid)->find();
		////年泄露量
		$Year_Out=$HoursLossF*(double)$companyinfo["workinghours"]*(double)$companyinfo["workingdays"];
		$YearLoss = $HoursLossF*$Year_Out/1000;
		return $HoursLossF."*".$YearLoss."*".$D;//$HoursLossF+"*"+$HoursLossR;

	}

	///根据超声频率判断泄露量，$DataHZOP,频率  $TrapP节点压力 单位kPa $TrapTem温度摄氏度
	public function OutLevel_OLD($DataHZOP,$TrapP,$TrapTem,$companyid){
		//根据频率计算泄露量
		//f=K*(u/d)  K为范围取值(0.012-0.002)
		//v=KP0Ψ(σ)/sqrt(R*T1) K=sqrt(2k/(k-1)) k=1.4  K=2.646
		//if(0.528<σ<1){Ψ(σ)=sqrt(σ的2/K次方 - σ的(k-1)/k)次方} if(0.528>σ>0){Ψ(σ)=0.2588}
		//σ=P0/P P0=101325Pa  P为管道内压力
		//R为气体常数 8.314

		//饱和蒸汽密度 if(压力为 1000kPa到1500kPa){Y=0.6358+0.00499*压力} if(压力为0到1000kPa){Y=0.6246+0.00505*压力} 压力单位（kPa）
		//过热蒸汽密度 19.44*压力/温度-0.151*压力+2.1627 (压力单位mPa)
		//蒸汽一吨的体积为  1000/密度

		$MD = 0.8035;//蒸汽密度
		if((double)$TrapP<1000){
			$MD = 0.6358+0.00499*(double)$TrapP;
		}else if ((double)$TrapP<1500) {
			$MD = 0.6246+0.00505*(double)$TrapP;
		}else {
			$UsP = (double)$TrapP/1000;
			$MD = 19.44*(double)$UsP/(double)$TrapTem-0.151*(double)$UsP+2.1627;
		}

		$LevelSpeed=0.00;//泄露的射流速度 m/s
		$P0=101325.00; //环境绝对气压（默认为标准大气压） 单位Pa
		$Sigma=$P0/(double)$DataHZOP*1000;
		$PsiSigma=0.2588;
		if($Sigma>0.528){
			$PsiSigma=sqrt(pow($Sigma,2/2.646)-pow($Sigma,(1.4-1)/1.4));
		}
		$LevelSpeed=2.646*101325*$PsiSigma/sqrt(8.314*$TrapTem);//获得射流速度 m/s

		//泄露口径
		$D = $LevelSpeed/$DataHZOP/0.012;

		$All_ = 3.1415926*pow($D/2,2)*$LevelSpeed;//每小时的泄露量
		$All_Ton = $All_/1000/$MD;

		$CompanyInfoDao = M("companyinfo");
		$companyinfo = $CompanyInfoDao->where("id="+$companyid)->find();
		//年泄露量
		$Year_Out=$All_Ton*(double)$companyinfo["workinghours"]*(double)$companyinfo["workingdays"];

	}


	public function index(){
		$this->display();
	}


	public function Demodata(){
		$this->display();
	}
	public function DemoGetData(){
		$TrapinfoDAO = M("trapinfo");
		$CPage = $_GET["cp"];
		if($CPage==""){$CPage=1;}
		$PageS = 20;
		$Start=((int)$CPage-1)*$PageS;
		$DATA_LIST = $TrapinfoDAO->where("companyid="+$_GET["comid"])->limit($Start,$PageS)->order("datecheck desc")->select();
		$AP = 1;
		$AP=$TrapinfoDAO->where("companyid="+$_GET["comid"])->count();
		if($AP%$PageS==0){
			$AP=$AP/$PageS;
		}else {
			$AP=(int)($AP/$PageS)+1;
		}
		$Data_='[';
		foreach ($DATA_LIST as $keyData) {
			$Data_.='{"ID":"'.$keyData["id"].'","TNO":"'.$keyData["trapno"].'","TEM":"'.$keyData["newtem"].'","HZ":"'.$keyData["hznumber"].'","ZF":"'.$keyData["battery"].'","TIME":"'.$keyData["datecheck"].'","DESC":"'.$keyData["description"].'","AP":"'.$AP.'"},';
		}
		$Data_ = rtrim($Data_,",");
		$Data_.=']';

		echo $Data_;
	}
	public function UpdatDemoData(){
		$TrapinfoDAO = M("trapinfo");
		$U_D["Id"]=$_GET["oid"];
		$U_D["Description"]=$_GET["des"];
		echo $TrapinfoDAO->save($U_D);
	}
	//TMS-CS 获取公司信息
	public function GetCompanyInfo(){
		$post_=$_POST["data"];
		if($post_==""){
			echo '{"res":"0"}';
			exit;
		}
		$post_=json_decode($post_);
		$companyID=$post_->oid;
		$loginName=$post_->name;
		$TYPE=$post_->type;
		$TYPE=$TYPE."";
		if($companyID==""||$loginName==""){
			echo '{"res":"0"}';
			exit;
		}

		$Dao_=M("companyinfo");
		$result=$Dao_->where("Id=".$companyID." and LoginName='".$loginName."'")->find();
		if($result!=null && $result["id"]>0){
			$json_list=json_encode($result);
			if($TYPE=="1"){
				echo $json_list;
			}else{
				$arr=array("res"=>"1","data"=>$json_list);
				echo json_encode($arr);
			}
		}else{
			echo '{"res":"0"}';
		}
	}

	//TMS-CS 修改公司信息
	public function UpdateCompanyInfo()
	{
		$post_=$_POST["data"];
		if($post_==""){
			echo '{"res":"00"}';
			exit;
		}
		$post_=json_decode($post_);
		$up_key=$post_->key;
		$up_val=$post_->val;
		$up_oid=$post_->oid;
		$up_name=$post_->name;
		
		if($up_key==""||$up_val==""||$up_oid==""||$up_name==""){
			echo '{"res":"000"}';
			exit;
		}
		$Model_CompanyInfo_Dao= M("companyinfo");
		$up_key=explode(',', $up_key);
		$up_val=explode(',', $up_val);
		$arrdata;
		$co_key=count($up_key);
		$DATA_STR="";
		for($i=0;$i<$co_key;$i++)
		{
			$arrdata[$up_key[$i]]=$up_val[$i];
			
		}
		
		
		$Model_CompanyInfo=$Model_CompanyInfo_Dao->where("Id=".$up_oid." and LoginName='".$up_name."'")->save($arrdata);
		if($Model_CompanyInfo>=0){
			//	cookie("companyloginame",$arrdata["LoginName"]);
			cookie("companyname",$arrdata["CompanyName"]);
			echo '{"res":"1"}';
		}else{
			echo '{"res":"0000"}';
		}

	}
	//TMS-CS 修改密码
	public function UpdatePwd()
	{
		$post_=$_POST["data"];
		if($post_==""){
			echo '{"res":"0"}';
			exit;
		}
		$post_=json_decode($post_);
		$USER_NAME=$post_->name;
		$USER_PWD=$post_->newpwd;
		$USER_OLDPWD=$post_->oldpwd;
		$USER_Status=$post_->status;
		$company_id=$post_->oid;
		if($USER_NAME=="" || $USER_PWD==""||$USER_OLDPWD==""||$USER_Status==""){
			echo '{"res":"0"}';
			exit;
		}
		if($USER_Status=="admin"){
			$Model_CompanyInfo_Dao_up = M("companyinfo");
			$where_str="id=".$company_id." and LoginName='".$USER_NAME."' and LoginPWD='".strtolower(md5($USER_OLDPWD))."'";
		}else{
			$Model_CompanyInfo_Dao_up = M("subuser");
			$where_str="CompanyID=".$company_id." and LoginName='".$USER_NAME."' and PassWord='".strtolower(md5($USER_OLDPWD))."'";
		}
		$Model_CompanyInfo_up=$Model_CompanyInfo_Dao_up->where($where_str)->find();
		if($Model_CompanyInfo_up!=null && $Model_CompanyInfo_up["id"]>0){
			if($USER_Status=="admin"){
				$data['LoginPWD'] = strtolower(md5($USER_PWD));
				$data['pw'] = $USER_PWD;
				$Model_CompanyInfo_up=$Model_CompanyInfo_Dao_up->where("id=".$Model_CompanyInfo_up["id"]." and LoginName='".$USER_NAME."'")->save($data);
			}else{
				$data['PassWord'] = strtolower(md5($USER_PWD));
				$Model_CompanyInfo_up=$Model_CompanyInfo_Dao_up->where("id=".$Model_CompanyInfo_up["id"]." and LoginName='".$USER_NAME."'")->save($data);
			}
			if($Model_CompanyInfo_up!=false && $Model_CompanyInfo_up>0){
				echo '{"res":"1"}';
			}else if($Model_CompanyInfo_up==0){
				echo '{"res":"1"}';
			}
			else{
				echo '{"res":"0"}';
			}
		}else{
			echo '{"res":"-2"}';
		}

	}
	//TMS-CS分区增加
	public function AreaADD()
	{
		$DAO = M("areainfo");
		$post_data=json_decode($_POST["data"]);
		$CompanyID=$post_data->cid;
		$AreaName=$post_data->an;
		$AreaUser=$post_data->au;
		$UserID=$post_data->auid;
		$UserTEL =$post_data->ut;
		$AreaLocation=$post_data->al;
		$AreaInfo=$post_data->ai;

		$DATA_["AreaName"]=$AreaName;
		$DATA_["AreaUser"]=$AreaUser;
		$DATA_["UserID"]=$UserID;
		$DATA_["UserTEL"]=$UserTEL;
		$DATA_["AreaLocation"]=$AreaLocation;
		$DATA_["areainfo"]=$AreaInfo;
		$DATA_["CompanyID"]=$CompanyID;

		$Res = $DAO->data($DATA_)->add();
		echo $Res;
	}
	//TMS-CS 分区编辑
	public function AreaEdit(){
		$DAO = M("areainfo");
		$post_data=$_POST["data"];
		if($post_data==""){
			echo '{"res":"0"}';
			exit;
		}
		$post_data=json_decode($post_data);
		$post_val=$post_data->val;
		$id['Id']=$post_data->areaid;
		$id['CompanyID']=$post_data->oid;
		$post_val=explode(',', $post_val);
		$CHONG=$DAO->where("CompanyID=".$id['CompanyID']." and AreaName='".$post_val[0]."' and Id !=".$id['Id'])->count();
		if($CHONG>0){
			echo '{"res":"-1"}';
			exit;
		}
		$data['AreaName']=$post_val[0];
		$data['AreaUser']=$post_val[1];
		$data['UserTEL']=$post_val[2];
		$data['AreaLocation']=$post_val[3];
		$data['areainfo']=$post_val[4];
		$result=$DAO->where($id)->save($data);
		if($result==0){
			echo '{"res":"2"}';
		}else if($result>0){
			echo '{"res":"1"}';
		}
		else{
			echo '{"res":"00"}';
		}
	}
	public function ErrorData(){
		$comid=$_GET["comid"];
		$DataParam = $_POST["data"];
		if($comid==""||$DataParam==""||strlen($DataParam)<12){
			echo '-2';
			exit;
		}
		$DataParam=strtolower(str_replace(" ","",$DataParam));
		$CompanyModel=M('companyinfo');
		$Result_=$CompanyModel->where("Id=".$comid."")->find();
		if($Result_['id']==""){
			echo '-2';
			exit;
		}
		//echo strpos($DataParam,"﻿tpcmserror");
		if(strpos($DataParam,'tpcmserror')<0){
			echo '-2';
			exit;
		}
		$ErrModel=M('errorinfo');
		$Data_["CompanyID"]=(int)$comid;
		$Data_["MCUID"]=substr($DataParam,10,2);
		$Data_["ErrorMsg"]=substr($DataParam,12,-1);
		$Data_["CreateTime"]=date('Y-m-d H:i:s');
		$Dao_result=$ErrModel->add($Data_);
		if($Dao_result!=false &&$Dao_result>0){
			echo '1';
			exit;
		}
		echo '-2';
		exit;
	}
	function getCity($ip = '')
	{
		if($ip == ''){
		}else{
			$url="http://ip.ws.126.net/ipquery?ip=".$ip;
			$ip=(file_get_contents($url));   
			$ip = iconv("gb2312", "utf-8//IGNORE",$ip); 
			$data = $ip;
		}
		
		return $data;   
	}
	
	///获取是否唤醒TC
	public function GetHX($CID,$mcuid,$ifsecond){
		$res="gdqd";//默认启动
		//查询数据库判断是否启动
		$Cominfo = M("companyinfo");
		$comData = $Cominfo->where("id=$CID")->find();
		$AccessTime=$comData["accesstime"];
		$AccessTimeSpan=$comData["accesstimespan"];
		$F=0;
		$curTime=date("Y-m-d H:i:s");
		
		$ttarray_str = $comData["ttid"];
		$tttimearray_str = $comData["tttime"];
		$UPTIME_STR = "";
		if($ttarray_str!=''){
			$ttarray = explode(',',$ttarray_str);
			$tttimearray = explode(',',$tttimearray_str);
			if($mcuid!=''){
				$index_f=0;
				foreach($ttarray as $ttid){
					if($ttid!=""){
						if ($ttid==$mcuid){
							$AccessTime = $tttimearray[$index_f];
							$F=1;
							$UPTIME_STR .= $curTime.",";
						}else{
							$UPTIME_STR .=$tttimearray[$index_f].",";
						}
					}
					$index_f+=1;
				}
			}
		}else{
			
		}
		
		$SPANTIME = date('Y-m-d H:i:s',strtotime("$AccessTime + $AccessTimeSpan min 0 seconds"));
		
		//if($SPANTIME<=$curTime){
		if(1>0){
			$res="gdqd";
			$TrapModel = M("trapmodel");
			$AllTC_count = $TrapModel->where("CompanyID=$CID")->count();
			$AllTC_count_str = (string)$AllTC_count;
			while(strlen($AllTC_count_str)<4){
				$AllTC_count_str="0".$AllTC_count_str;
			}
			$res.=$AllTC_count_str; //TC数量 四位
			$AccessTimeSpan_str = (string)((int)$AccessTimeSpan);
			if($ifsecond==1){
				// timespan to second
				$AccessTimeSpan_str = (string)(((int)$AccessTimeSpan)*60);
			}
			while(strlen($AccessTimeSpan_str)<4){
				$AccessTimeSpan_str="0".$AccessTimeSpan_str;
			}
			$res.=$AccessTimeSpan_str; //TC工作间隔 四位
			$res.=date("Y-m-d-w-H-i-s");; //当前时间
			
			$dataarray["AccessTime"]=$curTime;
			$dataarray["tttime"] = $UPTIME_STR;
			$res_update = $Cominfo->where("id=$CID")->data($dataarray)->save();
		}else{
			//当前剩余时间
			if($ifsecond==1){
				$NTimeSpan = ((int)(((strtotime($SPANTIME)-strtotime($curTime)))));//second
				$res="gdde-".$NTimeSpan;
			}else{
				$NTimeSpan = ((int)(((strtotime($SPANTIME)-strtotime($curTime)))/60)+1);//round minute
				$res="gdde-".$NTimeSpan;
			}
		}
		
		return $res;
	}
		//数据处理并存入数据库
	public function GetData($GDData,$comid){
		$this->WriteLog("logtt.txt",$GDData);
		//if(preg_match("/TPCMSCD.+ender/",$GDData)||preg_match("/TPCMSCD.+en/",$GDData)||preg_match("/TPCMSCD.+end/",$GDData)){
			//echo("数据验证完成");
			//数据分解插入数据库
		$ReGDData = str_replace("TPCMSCDTPCMSSD","TPCMSSD",explode("end",$GDData)[0]);#str_replace("ender","",str_replace("TPCMSCDTPCMSSD","TPCMSSD",$GDData));
		$dataArray = split(",",$ReGDData);
		$All_data="";
		if(strpos($ReGDData,"|")>-1){
			//TT返回的数据携带时间
			foreach($dataArray as $dataitem){
				if($dataitem!=""){
					try{
						$dataitem_array = explode("|",$dataitem);
						
						$Time_array =explode("-", $dataitem_array[1]);
						if($Time_array[5]>60){
							$Time_array[5]=59;
						}
						
						$Time_str=date("Y-m-d H:i:s");
						if(strlen($dataitem_array[1])<=18){
							$Time_str=$Time_array[0]."-".$Time_array[1]."-".$Time_array[2]." ".$Time_array[3].":".$Time_array[4].":".$Time_array[5];
						}
						
						if((int)$Time_array[0]>2018){
							$dataitem_s =explode("TPCMSSD",$dataitem_array[0]);
							foreach($dataitem_s as $dii){
								$dii="TPCMSSD".$dii;
								if(strlen($dii)==51){
									$All_data .=$dii."|".$Time_str."/";
								}
							}
						}else{
							$dataitem_s =explode("TPCMSSD",$dataitem_array[0]);
							foreach($dataitem_s as $dii){
								$dii="TPCMSSD".$dii;
								if(strlen($dii)==51){
									$All_data .=$dii."|".date("Y-m-d H:i:s")."/";
								}
							}
						}
							
						
					}catch(Exception $e){}
					$this->WriteLog("logtt.txt",$dataitem);
				}
			}
			//$All_data = rtrim($All_data,"/");
		}else{
			//TT返回的数据不带时间
			foreach($dataArray as $dataitem){
				$All_data .= $dataitem.",";
			}
			$All_data = rtrim($All_data,",")."|".date("Y-m-d H:i:s")."/";
			$All_data_array["data"]=$All_data;
		}
		$this->WriteLog("logtt.txt",$All_data);
		$insertdata_res = $this->InsertData($All_data);
		return $insertdata_res;
		//}else{
		//	//echo("数据验证失败");
		//	return "1";
		//}
	}
	
	public function InsertData($TTdata=''){
		$DataParam=$TTdata;
		if($TTdata==''){
			$DataParam = $_POST["data"];
		}
		
		$logStr="接收到的数据\n->".$DataParam;
		$this->WriteLog("log.txt",$logStr);
		//$this->WriteLog("11111data",$DataParam);

		$LenFlag="0";
		$comid=$_GET["comid"];
		
		//if($comid=="11"){
		//	$IP = $_SERVER['REMOTE_ADDR'];
		//	$fw_ = $this->getCity($IP);
		//	if(strpos($fw_,"东营")>0){
		//		$comid="15";
		//	}
		//}
		
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
						$trap_model_info = $AreaDAO->where("CompanyID=".$comid."  and trapNo='".$trap_no."'")->find();//and MCUID='".$mcu_id."'
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
					//////////////////幅值调整///////////
					$offset=470;//偏移量
					$TRPMODEL_DAO=M("trapmodel");
					$trap_model_info = $TRPMODEL_DAO->where("CompanyID=".$comid." and trapNo='".$Result_array[2]."'")->find();//
					$read_offset = $trap_model_info["offset"]."";
					if($read_offset=="")
					{
						$offset=0;
					}else{
						$offset=((int)$read_offset);
					}
					if (((int)$Result_array[5])>$offset){
						$Result_array[5]=((int)$Result_array[5])-$offset;
					}
					//if(((int)$Result_array[7])>$offset){
					$Result_array[7]=((int)$Result_array[7])-$offset;
					//}
					//if(((int)$Result_array[9])>$offset){
					$Result_array[9]=((int)$Result_array[9])-$offset;
					//}
					$trap_Dao=M();
					$sql="select count(0) from trapinfo where companyid=".$comid." and trapno='".$trap_no."'";
					$count_tinfo=$trap_Dao->query($sql);//query 查询 execute 增删改
					if($count_tinfo.""=="0"){
						//添加偏移量
						$Add_offset = ((int)$Result_array[5])-10;
						$sql_up="update trapmodel set offset=".$Add_offset." where Id=".(int)$trap_model_info["id"];
						$add_os_res=$trap_Dao->execute($sql_up);//query 查询 execute 增删改
					}
					
					//////////////////幅值调整结束///////////

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
						$c_array["offset"]=$offset;//幅值偏移量

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
							//偶尔一次正常 也进行隔离
							if(1>0){//if(((int)$c_array["ExLevel"])>0){
								$SELECTPerExleveDAO = M();
								$sql_str_a="select PerExleve from trapinfo where companyid=".$trap_model_info["companyid"]." and trapno='".$trap_model_info["trapno"]."' order by datecheck desc limit 0,3";
								//echo $sql_str_a."*";
								$SELECTARRAY = $SELECTPerExleveDAO->query($sql_str_a);
								
								if($SELECTARRAY!=NULL && count($SELECTARRAY)>=3){
									$PER0=((int)($SELECTARRAY[0]["perexleve"]));
									$PER1=((int)($SELECTARRAY[1]["perexleve"]));
									$PER2=((int)($SELECTARRAY[2]["perexleve"]));
									//$this->WriteLog("log_leak_per.txt","p_leak_".$PER0."-".$PER1."-".$PER2);
									if($PER0>0 && $PER1>0 && $PER2>0){
										//连续四条数据泄露 进行泄露纠偏
										
										$ExMin_ = ((int)$c_array["ExLevel"])-1;
										$ExMax_ = ((int)$c_array["ExLevel"])+1;
										if($ExMin_<1){
											$ExMin_=1;
										}

										if($PER0>=$ExMin_ && $ExMax_>=$PER0 && $PER1>=$ExMin_ && $ExMax_>=$PER1 && $PER2>=$ExMin_ && $ExMax_>=$PER2){
											//若此次泄露在前三次的区间内则不进行操作
										}else{
											//否则进行计算取值
											$Three_AVG = round(($PER0+$PER1+$PER2)/3);
											$C_AVG_CR = ((int)$c_array["ExLevel"])-$Three_AVG;
											$c_array["ExLevel"]=$Three_AVG+round($C_AVG_CR/3);
										}
										/*----------------*/
									}else{
										//若没有连续四次的泄露数据 则否认泄露
										$c_array["ExLevel"]="0";
									}
								}else{
									//$c_array["ExLevel"]="0";
									//echo "Helo111111";
									$this->WriteLog("log_leak.txt","n_leak_".$trap_model_info["companyid"]."-".$trap_model_info["trapno"]."-".$sql_str_a);
								}
							}
							if((int)$c_array["ExLevel"]>5){
								#$c_array["ExLevel"]=((int)$c_array["ExLevel"])+1;
							} 
							//偶尔单体泄露数据 隔离   结束////////////////////////////////////
						}catch(Exception $e) {
							$c_array["ExLevel"]="0";
							$this->WriteLog("log_error.txt","perexlevel");
						}
						/////////////////反赋值纠偏数据, 计算泄漏量///////////
						$LeakInfo["leaklevel"] = $c_array["ExLevel"];
						$LeakInfo_AVG[0]["leaklevel"] = $c_array["ExLevel"];
						$LeakInfo_PER[0]["leaklevel"] = $c_array["ExLevel"];
						////////////////////
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
						if($trap_model_info["outtype"].""=="0"){
							$c_array["LossAmount"]="0".(((double)$Loss_array_per[0])*0.5);
							$c_array["LossAmountYear"]="0".(((double)$Loss_array_per[1])*0.5);
						}
						//$c_array["LossMoneyYear"] = ((double)$COM_RES["moneyton"]*(double)$c_array["LossAmountYear"])/1000;
						
						
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
						$AN_RES_READ = ($AN_RESLUT->where(" CompanyID=$comid and TrapNo='".$c_array["TrapNo"]."' ")->order("datecheck desc")->select());
						$AN_RES_READ= $AN_RES_READ[0];
						$_exlevel_DATA = $AN_RES_READ["lastlevel"];
						$_ALL_LEAK_DATA = 0;
						if($_exlevel_DATA!="0"){
							$_date_HOUR = (double)((strtotime($c_array["DateCheck"])-strtotime($AN_RES_READ["datecheck"]))/3600);
							$_ALL_LEAK_DATA = $_date_HOUR*(double)$AN_RES_READ["lossvalue"] + (double)$AN_RES_READ["allleak"];
						}else{
							$_ALL_LEAK_DATA = (double)$AN_RES_READ["allleak"];
						}
						$AN_RES_ARRAY["LastLevel"] = $c_array["ExLevel"];
						$AN_RES_ARRAY["UseMTFI"] = $c_array["UseMTFI"];
						$AN_RES_ARRAY["TrapType"] = $c_array["TrapType"];
						$AN_RES_ARRAY["AreaID"] = $c_array["AreaId"];
						$AN_RES_ARRAY["Area"] = $c_array["Area"];
						$AN_RES_ARRAY["LossValue"] = $c_array["LossAmount"];
						$AN_RES_ARRAY["AllLeak"] = $_ALL_LEAK_DATA;
						$AN_RES_ARRAY["DateCheck"] = $c_array["DateCheck"];
						$AN_RES_ARRAY["CompanyID"] = $comid;
						$AN_RES_ARRAY["TrapNo"] = $c_array["TrapNo"];
						$AN_RES_ARRAY["temperature"] = $c_array["NewTem"];
						$AN_RES = $AN_RESLUT->add($AN_RES_ARRAY);
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
	function GUID(){
		if (function_exists('com_create_guid') === true)
		{
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
	//写入日志文件
	public function WriteLog($fileName,$str){
		$file  = $fileName;
		$content = $str."\ntime：".date("Y-m-d H:i:s")."\n";
		file_put_contents($file, $content,FILE_APPEND);
	}
	public function WriteFile($fileName,$str){
		$myfile = fopen($fileName, "w") or die("Unable to open file!");
		fwrite($myfile, $str);
		fclose($myfile);
	}
	public function ReadLog($filename){
		$myfile = fopen($filename, "r");
		$str=fread($myfile,filesize($filename));
		fclose($myfile);
		return $str;
	}
	//TMS-CS获取区域
	public function getareas(){
		$DAO_Area=M("areainfo");
		$where_str="CompanyID=".cookie("oid");
		$List_area = $DAO_Area->where($where_str)->select();
		$areas="";
		foreach ($List_area as $area_) {
			$areas.=$area_["id"].",".$area_["areaname"]."/";
		}
		echo rtrim($areas,",");
	}
	//TMS-CS 统计分析
	Public function getTraoInfoByAnalysis(){
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
	//TMS-CS 获取下个月的第一天
	public function getNextMonthDays($date){
		$timestamp=strtotime($date);
		$arr=getdate($timestamp);
		if($arr['mon'] == 12){
			$year=$arr['year'] +1;
			$month=$arr['mon'] -11;
			$firstday=$year.'-0'.$month.'-01';
			$lastday=date('Y-m-01',strtotime("$firstday +1 month -1 day"));
		}else{
			$firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)+1).'-01'));
			$lastday=date('Y-m-01',strtotime("$firstday +1 month -1 day"));
		}
		return $lastday;
	}
	//TMS-CS 获取区域里面的节点
	public function getTraps(){
		$area_id=$_GET["areaid"];
		if($area_id!=""){
			$DAO_trap=M("trapmodel");
			$where_str="CompanyID=".cookie("oid")." and AreaID='".$area_id."'";
			$List_trap = $DAO_trap->where($where_str)->select();
			$traps="";
			foreach ($List_trap as $trap_){
				$traps.=$trap_["id"].",".$trap_["trapno"]."/";
			}
			echo rtrim($traps,",");
		}
	}
	//TMS-CS获取区域列表
	public function getAreaList(){
		$getOid=$_POST["data"];
		if($getOid==""){
			echo '{"res":"0"}';
			exit;
		}
		$getOid=json_decode($getOid);
		$oid=$getOid->oid;
		$PageSize=$getOid->pagesize;
		$PageR=$getOid->pager;
		$user_status=$getOid->status;
		$user_name=$getOid->loginname;
		if($oid==""||$PageSize==""||$PageR==""||$user_status==""||$user_name==""){
			echo '{"res":"0"}';
			exit;
		}
		$Datastart=(int)((int)$PageSize-1)*5;
		$AreaDAO = M("areainfo");
		$where_Str=" companyid=".$oid;
		if($user_status=="user"){
			$where_Str.=" and AreaUser='".$user_name."'";
		}
		$List_all=$AreaDAO->where($where_Str)->order("Id")->limit($Datastart,(int)$PageR)->select();
		$List_count=count($List_all);
		for($i=0;$i<$List_count;$i++){
			$temp=json_encode($List_all[$i]);
			$array=array($i->$temp);
		}
		$all_count=$AreaDAO->where(" companyid=".$oid)->count();
		$list_all=json_encode($List_all);
		$List_arr=array("res"=>"1","data"=>$list_all,"count"=>$all_count);
		echo json_encode($List_arr);
	}
	public function queryLearnState($trapid){
		$Model=M("learntrap");
		$result=$Model->where("TrapNo='".$trapid."'")->count();
		return $result;
	}
	//TMS-CS 获取用户状态,判断是否被禁用
	public function getUserState(){
		$DATA_Post=$_POST["data"];
		if($DATA_Post==""){
			return '{"res":"0"}';
		}
		$UserInfo=json_decode($DATA_Post);
		$UserName=$UserInfo->loginname;
		$CompanyID=$UserInfo->companyid;
		if($UserName==""){
			return '{"res":"0"}';
		}
		$SubUserModel=M("subuser");
		$U_=$SubUserModel->where("CompanyID=".$CompanyID." and LoginName='".$UserName."'")->find();
		if($U_!=null&&$U_["id"]>0){
			if($U_["status"]=="1"){
				echo '{"res":"1"}';
			}else{
				echo '{"res":"-1"}';
			}
		}else{
			echo '{"res":"0"}';
		}
	}
	public function Warningtest(){
		$new_tem=$_GET["temp"];
		$hz_number=$_GET["hz"];
		$trap_no=$_GET["id"];
		$comid="1";
		$PD_Data = new Alert_Judge();
		$Res_PDData = $PD_Data->JudgeData($new_tem,$hz_number,$trap_no,$comid,0);
		echo $Res_PDData;
	}

	//获取报表数据(根据口径统计)-list
	public function GetRepDataSize(){
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
	public function GetRepDataPT(){
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
	public function GetRepDataPTRP()
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

	public function GetMoneyUseMtfi()
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

	public function GetMoneyType()
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
		$query_str = "select UseMTFI,exlevel,id,(datecheck+'') as datecheck,lossamount,traptype,trapno from trapinfo $where_str order by traptype asc,datecheck asc";
		$list = $Dao->query($query_str);
		if($list){
			echo json_encode($list);
		} else {
			//error
		}


	}

	public function GetMoneyArea()
	{
		$Dao = M();
		$DATA_Post=$_POST["data"];
		$SearchInfo=json_decode($DATA_Post);
		$CID=$SearchInfo->cmid;
		$AREA=$SearchInfo->area;
		$ST=$SearchInfo->st;
		$ET=$SearchInfo->et;
		$traptype = $SearchInfo->traptype;
		$localno = $SearchInfo->localno;
		$tjtype = $SearchInfo->tjtype;//统计类型 默认为列表   c为柱图 l为线图 p为饼图

		$where_str="where CompanyID=".$CID;
		if($AREA!=""){
			$where_str.=" and area='".$AREA."'";
		}
		if($traptype!=""){
			$where_str.=" and TrapType='".$traptype."'";
		}
		if($localno!=""){
			$where_str.=" and TrapName like '%".$localno."%'";
		}
		$where_str.=" and datecheck between '$ST' and '$ET' ";


		$query_str = "select Area,UseMTFI,exlevel,id,(datecheck+'') as datecheck,lossamount,traptype,trapno from trapinfo $where_str order by Area asc,datecheck asc";

		if($tjtype=="c"){
			$query_str = "select TrapNo,moneyton,area,UseMTFI,exlevel,id,(datecheck+'') as datecheck,lossamount,traptype,trapno from trapinfo $where_str and exlevel>0 order by id asc,datecheck asc";
		}
		if($tjtype=="l"){
			$query_str = "select TrapNo,moneyton,area,UseMTFI,exlevel,id,(datecheck+'') as datecheck,lossamount,traptype,trapno from trapinfo $where_str and exlevel>0 order by id asc,datecheck asc";
		}

		$list = $Dao->query($query_str);
		if($list){
			echo json_encode($list);
		} else {
			//error
		}


	}

	//获取报表数据(根据阀门品牌-查看类型)
	public function GetRepDataUSEmtfiRP()
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
	public function GetRepDataTT()
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
	public function GetRepDataArea()
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
	public function GetRepDataTTRP()
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
	public function GetRepDataTTRPInfo()
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
	public function GetRepDataRMInfo()
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
	public function GetRepDataRBInfo()
	{
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
	public function GetRepDataLL_SX()
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
	public function GetRepDataLL()
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
	public function GetRepDataLL2()
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
	//TMS-Android 统计分析 区域
	public function GetTrapRepA()
	{
		$data_post=$_POST["data"];
		if($data_post==""){
			echo '0';
			exit;
		}
		$arr_=json_decode($data_post);
		$comid=$arr_->comid;
		$username=$arr_->username;
		$status=$arr_->status;
		$order=$arr_->order;
		if($comid=="" || $username=="" ||$status==""||$order==""){
			echo '0';
			exit;
		}
		$AreaData=array();
		$Index=0;
		$Com_info=M("companyinfo");
		$Company_Data=$Com_info->where("Id=".$comid)->select();
		if($status=="user"){
			$SubUser_Info=M("subuser");
			$SubUser_Data=$SubUser_Info->where("CompanyID=".$comid." and LoginName='".$username."'")->select();
			if(count($SubUser_Data)>0){
				$Data_id=explode("*",$SubUser_Data[0]["areaid"]);
				$Data_name=explode("*",$SubUser_Data[0]["areaname"]);
				foreach ($Data_id as $key) {
					if($key!=""){
						$Data_arr["id"]=$key;
						$Data_arr["areaname"]=$Data_name[$Index];
						$Data_arr["areauser"]=$SubUser_Data[0]["username"];
						$Data_arr["usertel"]=$SubUser_Data[0]["usertel"];
						$AreaData[$Index]=$Data_arr;
						$Index++;
					}
				}
			}else{
				echo '0';
				exit;
			}
		}else{
			$Area_Info=M("areainfo");
			$AreaData=$Area_Info->where("CompanyID=".$comid)->select();
		}
		$Area_Data=array();
		$Index=0;
		if(count($AreaData)>0){
			$TrapModel_Info=M("trapmodel");
			$Trap_Info=M("trapinfo");
			$Warning=M("warning");
			foreach ($AreaData as $var) {
				$Area_arr=array();
				$Trap_Data=$TrapModel_Info->where("CompanyID=".$comid." and AreaId=".$var["id"]."")->select();
				$Area_arr["areaname"]=$var["areaname"];
				$Area_arr["count"]=count($Trap_Data);
				$Area_arr["user"]=$var["areauser"];
				$Area_arr["tel"]=$var["usertel"];
				$ErrorCount=0;
				$YearLoss=0;
				$AddLoss=0;

				foreach ($Trap_Data as $key ) {
					$where_sql="CompanyID=".$comid." and AreaId=".$var["id"]." and TrapNo='".$key["trapno"]."'";
					$Trap_=$Trap_Info->where($where_sql)->order("DateCheck desc")->limit(0,1)->select();
					// if((int)($Trap_[0]["exlevel"])>0 ||(int)($Trap_[0]["temstate"])>0){
					// $ErrorCount+=1;
					// }
					if((int)($Trap_[0]["exlevel"])>0){
						$ErrorCount+=1;
					}
					$YearLoss+=(int)($Trap_[0]["lossmoneyyear"]);//年损失费
					//累计损失
					$Warn_=$Warning->where($where_sql)->order("CreateTime asc")->limit(0,1)->select();
					if($Warn_!=NULL){
						$endTime=($Warn_[0]["repairtime"]=="" ||$Warn_[0]["repairtime"]==NULL)?date('Y-m-d H:i:s'):$Warn_[0]["repairtime"];
						//$startTime=date('Y-m-d',strtotime($Warn_[0]["createtime"]));
						$startTime=$Warn_[0]["createtime"];
						$date_day=floor((strtotime($endTime)-strtotime($startTime))/86400);
						$lossone=(int)($date_day*((int)$Warn_[0]["lossamount"])*((int)$Company_Data[0]["moneyton"]));
						$AddLoss+=$lossone;
					}

				}
				$Area_arr["error"]=$ErrorCount;
				$Area_arr["rate"]=(sprintf("%.2f",$ErrorCount/$Area_arr["count"])*100);
				$Area_arr["year"]=$YearLoss;//.$Company_Data[0]["moneyunit"];//年损失费
				$Area_arr["add"]=$AddLoss;//.$Company_Data[0]["moneyunit"];//累计损失
				$Area_Data[$Index]=$Area_arr;
				$Index+=1;
			}

			$sort=explode(",",$order);

			$DATA[0]=$this->list_sort_by($Area_Data,$sort[0],$sort[1]);
			$DATA[1]=$Company_Data[0]["moneyunit"];
			echo json_encode($DATA);
		}else{
			echo '0';
		}
	}
	
	//TMS-Android 统计分析 故障
	public function GetTrapEXA()
	{
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
	
	//TMS-Android 产生随机六位数密码
	function genPassword(){
		$validchars="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$max_char=strlen($validchars)-1;
		$password = "";
		for($i=0;$i<6;$i++ )
		{
			$password.=$validchars[mt_rand(0,$max_char)];
		}
		return $password;
	}
	//TMS-Android 重置密码
	public function ResetPwdA(){
		$data=$_POST["data"];
		if($data==""){
			echo '{"res":"-1"}';
			exit;
		}
		$data_=json_decode($data);
		$username=$data_->username;
		$userTel=$data_->userTel;
		$userEmail=$data_->userEmail;
		if($username==""||$userTel==""||$userEmail==""){
			echo '{"res":"2"}';
			exit;
		}
		$Dao=M("companyinfo");
		$result=$Dao->where("LoginName='".$username."' and UserTel='".$userTel."' and CompanyEmail='".$userEmail."'")->find();
		if($result!=null){
			$pwd=$this->genPassword();
			$arr["LoginPWD"]=md5($pwd);
			$Dao_model=$Dao->where("Id=".$result["id"]." and LoginName='".$username."'")->data($arr)->save();
			if($Dao_model){
				echo '{"res":"1","pwd":"'.$pwd.'"}';
			}else{
				echo '{"res":"3"}';
			}
		}else{
			echo '{"res":"2"}';
		}
	}
	//TMS-android 获取节点数据
	public function GetTrapData(){
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

	///TMS-cs 节点数量读取(全部,正常,异常)
	public function GetTrapStateCount()
	{
		$JsonData=$_POST["data"];
		$Res_Data = json_decode($JsonData);
		$companyid=$Res_Data->companyid;

		$DAO_Trap = M("trapinfo");

		$List_ALL = $DAO_Trap->query("select exlevel,trapno,datecheck,TrapName,TemState from ((select * from trapinfo where companyid=".$companyid." order by datecheck desc) as te) group by trapno");


		$DAO_TrapModel = M("trapmodel");
		$allCount=$DAO_TrapModel->where("companyid=".$companyid."")->count();
		$n_count=0;
		$e_count=0;
		$e_tn="";
		$c_count=0;
		$c_tn="";
		$run_number=0;
		$s_count=0;
		$DATE_NOW = strtotime (date("y-m-d h:i:s"));

		foreach ($List_ALL as $value) {
			$DATA_TIME = (strtotime ($value["datecheck"]));

			$date=floor(($DATE_NOW-$DATA_TIME)/86400);
			$hour=floor(($DATE_NOW-$DATA_TIME)%86400/3600);
			$minute=floor(($DATE_NOW-$DATA_TIME)%86400/60);
			$second=floor(($DATE_NOW-$DATA_TIME)%86400%60);
			
			if($date<0){$date=0;}
			if($hour<0){$hour=0;}
			if($minute<0){$minute=0;}
			if($second<0){$second=0;}
			
			if((($date*24*60)+($hour*60)+$minute)<=60){//$minute<=60
				if($value["temstate"].""=="1"){
					$c_count++;
					$c_tn=$c_tn.$value["trapname"].",";
				}else{
					if((int)$value["exlevel"]>0){
						$e_count++;
						$e_tn=$e_tn.$value["trapname"].",";
					}else{
						$n_count++;
					}
				}

			}else{
				$s_count++;
			}
		}
		$s_count = $allCount - $n_count - $e_count - $c_count;
		echo $allCount."*".$n_count."*".$e_count."*".$c_count."*".$e_tn."*".$c_tn."*".$s_count;
	}
	//TMS-android 获取节点详细信息
	public function GetTrapInfoData(){
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
	//TMS 获取TT采集TC数据的时间间隔
	public function GetWorkTime(){
		
		$comid=$_GET["comid"];
		$data_str = $_GET["data"];
		$mcuid= $_GET["mcuid"]; 
		$gd = $_GET["gd"];
		$ifsecond=0;
		if($_GET["sec"].""=="1")
		{
			$ifsecond=1;
		}
		if($data_str==""){
			if($gd==""){
				if($comid==""){
					$Ctime=date('Y-m-d H:i:s');
					echo "1*".date('Y-m-d w H:i:s',strtotime((string)$Ctime." + 0 min")); //因网络请求的延迟与TT的计时在原有时间基础上加一分钟
					exit;
				}
				$ComModel=M("companyinfo");
				$ComModel_DAO=$ComModel->where("Id=".$comid)->find();
				if($ComModel_DAO){
					$Ctime=date('Y-m-d H:i:s');
					$COMMTIME["CommTime"]=$Ctime;
					$COMMTIME["Id"]=$comid;
					$R=$ComModel->save($COMMTIME);
					echo $ComModel_DAO["accesstimespan"]."*".date('Y-m-d w H:i:s',strtotime((string)$Ctime." + 0 min"));
				}else{
					$Ctime=date('Y-m-d H:i:s');
					echo "1*".date('Y-m-d w H:i:s',strtotime((string)$Ctime." + 0 min"));
				}
			}else{
				//echo $gd;
				$res_hx=$this->GetHX($comid,$mcuid,$ifsecond);
				echo $res_hx;
			}
		}else{
			//echo $data_str;
			$res = $this->GetData($data_str,$comid);
			echo $res;
		}
	}
	//TMS-CS 子账户权限修改
	public function SubuserPerControl(){
		$port_data=$_POST["data"];
		if($port_data==""){
			echo '{"res":"-1"}';
			exit;
		}
		$json_data=json_decode($port_data);
		$uid=$json_data->uid;
		$per=$json_data->per;
		$type_=$json_data->type;
		if($uid==""||$per==""||$type_==""){
			echo '{"res":"-1"}';
			exit;
		}
		$subModel=M("subuser");
		$data_["Id"]=$uid;
		$data_["PermissionSet"]=$per;
		$subDao=$subModel->where("Id=".$uid)->data($data_)->save();
		echo '{"res":"'.$subDao.'"}';
	}
	//TMS-CS 获取磁盘剩余量(单机缓存服务器版)
	public function GetDiskSize($companyid){
		// $companyid=1;
		$days=-1;
		$ComModel=M("companyinfo");
		$read_data=$ComModel->where("Id=".$companyid)->find();#获取数据库中存储的剩余量
		if($read_data!=null && $read_data["id"]>0){

			$sizedata=exec("sh /var/test.sh");//执行脚本获取现在的容量
			if($read_data["disksize"]!="0"){
				$read_str=(int)$read_data["disksize"];
				$size_span=$read_str-$sizedata;
				if($size_span>0){
					$days=floor($sizedata/$size_span);
				}else{
					$days=20;
				}
			}
			else{
				$days=20;
			}
			$save_data["DiskSize"]=$sizedata;
			$result=$ComModel->where("Id=".$companyid)->save($save_data);
		}
		//echo $days;
		return $days;
	}
	//数组排序
	private function list_sort_by($list, $field, $sortby){
		if (is_array($list))
		{
			$refer = $resultSet = array();
			foreach ($list as $i => $data)
			{
				$refer[$i] = &$data[$field];
			}
			switch ($sortby)
			{
				case 'asc': // 正向排序
					asort($refer);
					break;
				case 'desc': // 逆向排序
					arsort($refer);
					break;
				case 'nat': // 自然排序
					natcasesort($refer);
					break;
			}
			foreach ($refer as $key => $val)
			{
				$resultSet[] = &$list[$key];
			}
			return $resultSet;
		}
		return false;
	}

	//TM节点布局读取
	public function LayoutData()
	{
		$post_data=$_POST["data"];
		$json_data=json_decode($post_data);
		$companyid =  $json_data->cid;
		$Areaid =  $json_data->area;

		$DAO_Layout = M("layoutdata");
		$where_str="";
		if($Areaid!=""){
			$where_str.=" and AreaId=".$Areaid;
		}

		$res = $DAO_Layout->where("CompanyId=".$companyid." and flag=0 ".$where_str)->select();

		echo json_encode($res);
	}
	//TM节点布局添加 添加之前删除该企业的布局数据所有数据不做更新只做新增
	public function LayoutDataAdd()
	{
		$post_data=$_POST["data"];
		$json_data=json_decode($post_data);
		$DAO_Layout = M("layoutdata");
		$Companyid=0;
		$index=0;
		foreach ($json_data as $key) {

			$DataRES[$index]["CompanyId"]   =$json_data[$index]->cid;
			$Companyid = $json_data[$index]->cid;
			$DataRES[$index]["LayoutType"]  =$json_data[$index]->laytype;
			$DataRES[$index]["TrapNo"]      =$json_data[$index]->trapno;
			$DataRES[$index]["AreaId"]      =$json_data[$index]->areaid;
			$DataRES[$index]["Area"]        =$json_data[$index]->area;
			$DataRES[$index]["TrapPointX"]  =$json_data[$index]->tpx;
			$DataRES[$index]["TrapPointY"]  =$json_data[$index]->tpy;
			$DataRES[$index]["LayLineArray"]=$json_data[$index]->lla;
			$DataRES[$index]["ControlName"] =$json_data[$index]->cn;
			$DataRES[$index]["ControlText"] =$json_data[$index]->ct;
			$DataRES[$index]["ControlColor"]=$json_data[$index]->cc;
			$DataRES[$index]["Creator"]     =$json_data[$index]->creator;

			$index++;
		}
		$res_final="-1";
		$res_del = $DAO_Layout->where("CompanyId=".$Companyid)->data("flag=1")->save();

		$add_res = $DAO_Layout->addAll($DataRES);
		if($add_res){
			$res_final = $DAO_Layout->where("CompanyId=".$Companyid." and flag=1")->delete();
		}

		echo $res_final;
	}
	function CheckLength($s_,$l_){
		while (strlen($s_)<$l_) {
			$s_="0".$s_;
		}
		return $s_;
	}
	// TCPS 获取TC的频率配置信息
	public function GetTCSetInfo(){
		$comid=$_GET["comid"];
		$mcuid=$_GET["mcu"];
		if($comid=="" || $mcuid==""){
			echo "-1";
			exit;
		}
		$TrapModel=M("trapmodel");
		$trapInfo=$TrapModel->where("SetState=1 and SetState is not null and MCUId='".$mcuid."' and CompanyID=".$comid."")->limit(0,1)->select();
		$str_="-1";
		foreach ($trapInfo as $key) {
			$str_="TPCMSMP".$mcuid;
			$str_.=$key["trapno"].$this->CheckLength($key["criticalvalue"],5);
			$str_.=$key["collectionnum"].$this->CheckLength($key["revealper"],5);
			$str_.=$this->CheckLength($key["zoommultiple"],4).$key["minhz"]."FFFF";
			$str_.=$key["maxhz"]."FF".$key["comswitch"]."FFF|";
		}
		echo $str_;
	}
	//TMS-Sale 历史数据
	public function getTrapHistoryData(){
		$comid=$_GET["ci"];
		$lang=$_GET["lang"];
		$data_post=$_POST["data"];
		if($comid==""||$data_post==""||$lang==""){
			echo "0";
			exit;
		}
		$data_arr=json_decode($data_post);
		$trapno=$data_arr->trapno;
		if($trapno==""){
			echo "0";
			exit;
		}
		$info_data=array();
		$TrapModel=M("trapmodel");
		$trap_data=$TrapModel->where("CompanyID=".$comid." and trapNo='".$trapno."'")->select();
		if(count($trap_data)>0){
			//$model_data["trapname"]=$trap_data[0]["trapname"];//名称
			//$model_data["usemtfi"]=$trap_data[0]["usemtfi"];//品牌
			//$model_data["traptype"]=$trap_data[0]["traptype"];//阀门类型(形式)
			//$model_data["modelname"]=$trap_data[0]["modelname"];//型号

			//压差 （入口压力-出口压力）

			$pressure_cha=((int)$trap_data[0]["spressure"])-((int)$trap_data[0]["spressureout"]);
			$Trap_Info=M("trapinfo");
			// 显示最新的20条数据
			$Trap_Data=$Trap_Info->where("CompanyID=".$comid." and trapNo='".$trapno."'")->order("CreateTime desc")->limit(0,20)->select();

			$Index_Count=0;
			foreach ($Trap_Data as $key) {
				$info_key["createtime"]=date("Y-m-d H:i", strtotime($key["createtime"]));
				if((int)($key["temstate"])>0){
					if($lang=="en_US"){
						$info_key["trapstate"]=(int)($key["temstate"])>0?"Low Temp":"Normal";
					}else{
						$info_key["trapstate"]=(int)($key["temstate"])>0?"低温":"正常";
					}
					/*20170710 当温度异常时即低温，泄漏量和等级显示0*/
					$info_key["exlevel"]="0";
					$info_key["lossamount"]="0";
				}else{
					if($lang=="en_US"){
						$info_key["trapstate"]=(int)($key["exlevel"])>0?"Defect":"Normal";
					}else{
						$info_key["trapstate"]=(int)($key["exlevel"])>0?"异常":"正常";
					}

					$info_key["exlevel"]=$key["exlevel"];
					$info_key["lossamount"]=((int)$key["lossamount"])>0?number_format(floatval($key["lossamount"]),2):$key["lossamount"];
				}

				$info_key["newtem"]=$key["newtem"];
				//$info_key["exlevel"]=$key["exlevel"];
				//$info_key["lossamount"]=((int)$key["lossamount"])>0?number_format(floatval($key["lossamount"]),2):$key["lossamount"];
				//阀门基本属性部分
				$info_key["trapname"]=$key["trapname"];//名称
				$info_key["usemtfi"]=$key["usemtfi"];//品牌
				$info_key["traptype"]=$key["traptype"];//阀门类型(形式)
				$info_key["modelname"]=$key["trapmodel"];//型号
				$info_key["pressure"]=$pressure_cha;

				$info_data[$Index_Count]=$info_key;
				$Index_Count++;
			}

			echo json_encode($info_data);
			exit;
		}
		echo "0";
		exit;

	}
	//TMS-TM app 获取公式详解
	public function getFormulaData(){
		$id = $_GET["id"];
		if($id==""){
			echo "0";
			exit;
		}
		$Dao = M("formuladata");
		$data = $Dao->where("Type='".$id."'")->select();
		if($data!=null){
			echo json_encode($data);
		}else{
			echo "0";
		}
		
		
	}
	
	//获取泄漏统计信息
	public function GetAnlyData(){
		$comid = $_GET["ci"]."";
		$data_post=$_POST["data"];
		$data_arr=json_decode($data_post);
		
		$AreaID = $data_arr->aid;
		$tn=  $data_arr->tn;
		$st=$data_arr->st;
		$et= $data_arr->et;
		
		$WHERE_STR = " CompanyID=".$comid." ";
		if($AreaID!=""){
			$WHERE_STR.=" and AreaID=".$AreaID." ";
		}
		if($tn!=""){
			$WHERE_STR.=" and TrapNo='".$tn."' ";
		}
		if($st!="" && $et!=""){
			$WHERE_STR.=" and datecheck between '".$st."' and '".$et."' ";
		}
		//$this->WriteLog("log_an.txt",$WHERE_STR);
		$this->WriteLog("log_an.txt","+++++++++++++++++++++++++++");
		$AN_RES_DAO = M("analysis");
		$AN_RES_ARRAY_COUNT = $AN_RES_DAO->where($WHERE_STR)->count();
		$this->WriteLog("log_an.txt","+++++++++++++++++++++++++++");
		$FQ=1;
		
		while ($AN_RES_ARRAY_COUNT / $FQ > 1000){
			$FQ=$FQ*10;
		}
		
		$this->WriteLog("log_an.txt",$AN_RES_ARRAY_COUNT."---".$FQ);
		
		$SQL_SELECT = "select * from (select (@i:=@i+1) i,a.* from analysis a,(SELECT @i:=-1) as i where ".$WHERE_STR.") t where i%".$FQ."=0";
		$this->WriteLog("log_an.txt",$SQL_SELECT."%%%%%%%");
		$DAO_M=M();
		$AN_RES_ARRAY = $DAO_M->query($SQL_SELECT);
		$this->WriteLog("log_an.txt","$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$%%%%%%%");
		//$AN_RES_DAO = M("analysis");
		//$AN_RES_ARRAY = $AN_RES_DAO->where($WHERE_STR)->order("datecheck desc")->select();
		//$this->WriteLog("log_an.txt","^^^^^^^");
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
			$RESJson.='"lossvalue":"'.$this->DWHS("3",$anres["lossvalue"],1,$comid).'",';
			$RESJson.='"allleak":"'.$this->DWHS("3",$anres["allleak"],1,$comid).'",';
			$RESJson.='"datecheck":"'.$anres["datecheck"].'",';
			$RESJson.='"createtime":"'.$anres["createtime"].'",';
			$RESJson.='"temperature":"'.$anres["temperature"].'"'; 
			$RESJson.="},";
		}
		$RESJson = rtrim($RESJson,",");
		$RESJson.=']}';
		echo $RESJson;
	}
	
	//获取泄漏统计信息
	public function GetAnlyDataArea(){
		$comid = $_GET["ci"]."";
		$data_post=$_POST["data"];
		$data_arr=json_decode($data_post);
		
		$AreaID = $data_arr->aid;
		$tn=  $data_arr->tn;
		$st=$data_arr->st;
		$et= $data_arr->et;
		
		$Sql_="select min(allleak) minv,max(allleak) maxv,(max(allleak)-min(allleak)) leak,trapno,area,areaid,LossValue,datecheck,temperature,TrapType,UseMTFI,LastLevel from ( select * from analysis where companyid=".$comid." and datecheck >='".$st."' and datecheck <='".$et."' order by datecheck desc) tm  where datecheck >='".$st."' and datecheck <='".$et."' group by trapno,area,areaid";
		$Dao=M();
		$RE_DATA = $Dao->query($Sql_);
		$RESJson='{"res":[';
		foreach ($RE_DATA as $anres) {
			
			$RESJson.="{";
			$RESJson.='"minv":"'.$anres["minv"].'",';
			$RESJson.='"maxv":"'.$anres["maxv"].'",';
			$RESJson.='"trapno":"'.$anres["trapno"].'",';
			$RESJson.='"leak":"'.$anres["leak"].'",';
			$RESJson.='"area":"'.$anres["area"].'",';
			$RESJson.='"lossvalue":"'.$anres["lossvalue"].'",';
			$RESJson.='"datecheck":"'.$anres["datecheck"].'",';
			$RESJson.='"temp":"'.$anres["temperature"].'",';
			$RESJson.='"traptype":"'.$anres["traptype"].'",';
			$RESJson.='"mtfi":"'.$anres["usemtfi"].'",';
			$RESJson.='"lastlevel":"'.$anres["lastlevel"].'",';
			$RESJson.='"areaid":"'.$anres["areaid"].'"';
			$RESJson.="},";
		}
		$RESJson = rtrim($RESJson,",");
		$RESJson.=']}';
		echo $RESJson;
	}
	
	
	
	public function GetAnlyLine(){
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

}

class Alert_Judge{
	public function Alert_Judge(){}

	public function JudgeData($data_TEM,$data_HZ,$trapno,$CI,$EXLevel)
	{
		$N_1=false;//判断标准一，比较已设置的值
		$N_2=false;//判断标准二，比较历史数值
		$N_3=false;//判断标准三，比较已学习的最新值
		$N_Des="";//异常描述
		//三项均为TRUE则为产生泄露，若N3为null则默认为true
		////一///////////
		$TrapmodelDAO = M("trapmodel");
		$trap_Model_ = $TrapmodelDAO->where("CompanyID=".$CI." and trapNo='".$trapno."'")->find();
		//温度一
		if((int)$trap_Model_["stemptop"]+20<(int)$data_TEM){
			//温度过高
			$N_1=true;
			$N_Des.="H,";
		}
		if((int)$trap_Model_["stemplow"]-30>(int)$data_TEM){
			//温度过低
			$N_1=true;
			$N_Des.="L,";
		}
		//赫兹一
		//未写

		////一结束///////////
		////二///////////
		$TrapinfoDAO = M("trapinfo");
		$trapinfo_LIST = $TrapinfoDAO->where("companyid=".$CI." and TrapNo='".$trapno."' and DateCheck between '".date('Y-m-d H:i',strtotime('-15 day'))."' and '".date("Y-m-d H:i")."'")->select();
		$Max_tem=0;
		$Sum_=0;
		$List_count=count($trapinfo_LIST);
		for ($i=0; $i < $List_count; $i++) {
			//这里判断温度
			if((int)$trapinfo_LIST[$i]["NewTem"]>(int)$Max_tem){
				$Max_tem = (int)$trapinfo_LIST[$i]["NewTem"];
			}
			$Sum_+=(int)$trapinfo_LIST[$i]["NewTem"];
			//这里判断赫兹
			//未写
		}
		//温度二
		if((int)$data_TEM>(int)$Max_tem+10){
			$N_2=true;
			$N_Des.="H,";
		}
		if((int)$data_TEM<((int)$Sum_/(int)$List_count)-10){
			$N_2=true;
			$N_Des.="L,";
		}
		//赫兹二
		//未写

		////二结束///////////
		////三///////////
		//温度三
		$Learn_trap=M("learntrap");
		$LearnResult_L=$Learn_trap->where("CompanyID=".$CI." and TrapNO='".$trapno."' and AlertType=1")->order("CreateTime desc")->find();
		$LearnResult_H=$Learn_trap->where("CompanyID=".$CI." and TrapNO='".$trapno."' and AlertType=3")->order("CreateTime desc")->find();
		$FL=false;
		if($LearnResult_H!=null){
			$FL=false;
			if((int)$data_TEM>(int)$LearnResult_H["newvalue"]){
				$N_3=true;
				$N_Des.="H,";
			}
		}else{
			$FL=true;
		}
		if($LearnResult_L!=null){
			$FL=false;
			if((int)$data_TEM<(int)$LearnResult_L["newvalue"]){
				$N_3=true;
				$N_Des.="L,";
			}
		}else{
			$FL=true;
		}
		//赫兹三
		//未写
		////三结束///////////
		//echo $N_1."*".$N_2."*".$N_3."*".$FL;
		$Alert_INFO_DAO = M("revealinfo");
		if($N_1&&$N_2&&($N_3||$FL)){
			$N_Array = split(",",$N_Des);
			$FLAG_S = 1;
			$N_F = "";
			foreach ($N_Array as $N_Str) {
				if($N_F==""){
					$N_F=$N_Str;
				}	else {
					if($N_F!=$N_Str && $N_Str!=""){
						$FLAG_S=0;
						break;
					}
				}
			}
			if($FLAG_S==1){
				$Alert_info_find = $Alert_INFO_DAO->where("CompanyID=".$CI." and trapno='".$trapno."' and EndTime is null")->find();
				if($Alert_info_find==null){
					$InsertINFO["CompanyID"]=$CI;
					$InsertINFO["TrapNO"]   =$trapno;
					$InsertINFO["StartTem"] =$data_TEM;
					$InsertINFO["StartHZ"]  =$data_HZ;
					$InsertINFO["FristTime"]=date("Y-m-d H:i:s");
					$InsertINFO["EndTime"]  = null;
					$InsertINFO["AlertDes"] =$N_F;
					$Alert_INFO_DAO->add($InsertINFO);


					//数据插入报警管理
					$WN_DAO = M("warning");
					$W_I_Array["CompanyId"]=$CI;
					$W_I_Array["AreaId"]=$trap_Model_["areaid"];
					$W_I_Array["Area"]=$trap_Model_["area"];
					$W_I_Array["TrapNo"]=$trapno;
					$W_I_Array["Location"]=$trap_Model_["location"];
					$W_I_Array["RepairState"]="0";
					$W_I_Array["TrapState"]="1";
					$W_I_Array["ExLevel"]=$N_F;
					$W_I_Array["LevelDesc"]=$EXLevel;
					$W_I_Array["AlertHZ"]=$data_HZ;
					$W_I_Array["AlertTem"]=$data_TEM;
					$W_I_Array["CreateTime"]=date('Y-m-d H:i:s');

					$WN_DAO->add($W_I_Array);
				}
				//	return $N_F;
			}else{
				$Alert_info_find = $Alert_INFO_DAO->where("CompanyID=".$CI." and trapno='".$trapno."' and EndTime is null")->find();
				if($Alert_info_find!=null){
					$InsertINFO["Id"]   =$Alert_info_find["id"];
					$InsertINFO["CompanyID"]=$CI;
					$InsertINFO["TrapNO"]   =$trapno;
					$InsertINFO["EndTime"]  = date("Y-m-d H:i:s");
					$InsertINFO["EndTem"]   =$data_TEM;
					$InsertINFO["EndHZ"]    =$data_HZ;

					//泄露量暂不写入
					//$InsertINFO["OutLevel"] =
					//$InsertINFO["TotalOut"] =
					//$InsertINFO["SavingInY"]=

					$Alert_INFO_DAO->save($InsertINFO);
				}
				return "0";
			}
		}else{
			$Alert_info_find = $Alert_INFO_DAO->where("CompanyID=".$CI." and trapno='".$trapno."' and EndTime is null")->find();
			if($Alert_info_find!=null){
				$InsertINFO["Id"]   =$Alert_info_find["id"];
				$InsertINFO["CompanyID"]=$CI;
				$InsertINFO["TrapNO"]   =$trapno;
				$InsertINFO["EndTime"]  = date("Y-m-d H:i:s");
				$InsertINFO["EndTem"]   =$data_TEM;
				$InsertINFO["EndHZ"]    =$data_HZ;
				//$InsertINFO["OutLevel"] =
				//$InsertINFO["TotalOut"] =
				//$InsertINFO["SavingInY"]=
				$Alert_INFO_DAO->save($InsertINFO);
			}
			return "0";
		}
	}
}

class Crypt3Des {
	var $key;
	function Crypt3Des($key){
		$this->key = $key;
	}

	function encrypt($input){
		$size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
		$input = $this->pkcs5_pad($input, $size);
		$key = str_pad($this->key,24,'0');
		$td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
		$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		@mcrypt_generic_init($td, $key, $iv);
		$data = mcrypt_generic($td, $input);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		//    $data = base64_encode($this->PaddingPKCS7($data));
		$data = base64_encode($data);
		return $data;
	}

	function decrypt($encrypted){
		$encrypted = base64_decode($encrypted);
		$key = str_pad($this->key,24,'0');
		$td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
		$ks = mcrypt_enc_get_key_size($td);
		@mcrypt_generic_init($td, $key, $iv);
		$decrypted = mdecrypt_generic($td, $encrypted);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$y=$this->pkcs5_unpad($decrypted);
		return $y;
	}

	function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	function pkcs5_unpad($text){
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text)) {
			return false;
		}
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
			return false;
		}
		return substr($text, 0, -1 * $pad);
	}

	function PaddingPKCS7($data) {
		$block_size = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
		$padding_char = $block_size - (strlen($data) % $block_size);
		$data .= str_repeat(chr($padding_char),$padding_char);
		return $data;
	}

}
