<?php
namespace Home\Controller;
use Think\Controller;
///手机端接口
class MobileController extends Controller {
	public function index(){
		$this->display();
	}
	//登录 res:0 参数为空或密码用户名不匹配  res:-1 账号被禁用
	public function LoginData($JsonData){
		//$JsonData = $_POST["data"];
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
				echo '{"res":"'.$res_['id'].'","data":"admin","per":"11111111","days":"'.$days.'","comname":"'.$ComapnyName.'","username":"'.$res_['username'].'","regTime":"'.$res_['createtime'].'","areaid":"","spunit":"'.$res_['spunit'].'","lossunit":"'.$res_['lossunit'].'","temunit":"'.$res_['temunit'].'","sizeunit":"'.$res_['sizeunit'].'","pwunit":"'.$res_['pwunit'].'","workinghours":"'.$res_['workinghours'].'","workingdays":"'.$res_['workingdays'].'","addlogo":"'.$res_['addlogo'].'","moneyton":"'.$res_['moneyton'].'","moneyunit":"'.$res_['moneyunit'].'","loginname":"'.$res_['loginname'].'"}';
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
						
						echo '{"res":"'.$res_['companyid'].'","data":"user","per":"'.$res_['permissionset'].'","username":"'.$res_['username'].'","days":"'.$days.'","comname":"'.$res_['companyname'].'","areaid":"'.$res_['areaid'].'","regTime":"'.$res_['areaid'].'","spunit":"'.$_sub_com_result_['spunit'].'","lossunit":"'.$_sub_com_result_['lossunit'].'","temunit":"'.$_sub_com_result_['temunit'].'","sizeunit":"'.$_sub_com_result_['sizeunit'].'","pwunit":"'.$_sub_com_result_['pwunit'].'","workinghours":"'.$_sub_com_result_['workinghours'].'","workingdays":"'.$_sub_com_result_['workingdays'].'","addlogo":"'.$_sub_com_result_['addlogo'].'","moneyton":"'.$res_['moneyton'].'","moneyunit":"'.$res_['moneyunit'].'","loginname":"'.$res_['loginname'].'"}';
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

	//获取区域信息 res:0 参数为空 若权限限制, 只能查看自己所有权限的区域
	private function GetAreas($data_){
		//$data_=$_POST["data"];
		if($data_==""){
			echo '{"res":"0"}';
			exit;
		}
		$Json_Data=json_decode($data_);
		$user_status=$Json_Data->status;
		//$user_name=$Json_Data->loginname;
		$comid = $Json_Data->cid;

		if($user_status==""){
			echo '{"res":"-1"}';
			exit;
		}
		// $user_status=$_GET["status"];
		// $user_name=$_GET["loginname"];
		$wherestr="CompanyID=".$comid;
		if($user_status=="user"){
			$wherestr.=" and AreaUser='".$user_name."'";
		}

		$areaJson='{"res":[';
		$areaid="";
		
		if($user_status=="user"){
			$areainfoDAO = M("areainfo");
			$AreaList = $areainfoDAO->where($wherestr)->select();
			foreach ($AreaList as $area) {
				//$areaJson.="{";
				//$areaJson.='"id":"'.$area["id"].'",';
				//$areaJson.='"name":"'.$area["areaname"].'"';
				//$areaJson.="},";
				$areaid.=$area["id"].",";
			}
			$areaid=rtrim($areaid,",");
			$areaid=" and areaid in (".$areaid.") ";
		}

		$SQL_QUERY="select * from ( select id,trapno,area,areaid,lastlevel,lossvalue,allleak,datecheck from analysis where companyid=".$comid." ".$areaid." order by datecheck desc limit 0,9000) t group by trapno";


		$COM_DAO = M("companyinfo");
		$COM_RES = $COM_DAO->where("id=".$comid)->find();
		$CB = $COM_RES["moneyton"];
		$DAO = M();
		$RES_ = $DAO->query($SQL_QUERY);
		$AreaArray=array();
		foreach($RES_ as $item){
			$AreaArray[$item["area"]][]=$item["trapno"]."*".$item["areaid"]."*".$item["lastlevel"]."*".$item["lossvalue"]."*".$item["allleak"];
		}
		foreach($AreaArray as $key=>$value){
			$areaJson.="{";
			$areaJson.='"area":"'.$key.'",';
			$areaid='';
			$ssl=0;
			$ljsh=0;
			$gzs=0;
			$zs=count($value);
			foreach($value as $v){
				$v_array = explode("*",$v);
				$ssl=$ssl+(float)$v_array[3];
				$ljsh=$ljsh+(float)$v_array[4];
				if((int)$v_array[2]>0){
					$gzs=$gzs+1;
				}
				$areaid=$v_array[1];
			}
			$areaJson.='"areaid":"'.$areaid.'",';
			$areaJson.='"lossvalue":"'.round($this->DWHS("4",$ssl,1,$comid),2).'",';
			//$areaJson.='"totalvalue":"'.round($this->DWHS("3",$ljsh,1,$comid),2).'",';
			$RES_LJSH = round(((float)$ljsh)*((float)$CB)/1000,2);
			if($RES_LJSH>=10000){
				$RES_LJSH=round($RES_LJSH/10000,2)."万";
			}
			$areaJson.='"totalvalue":"'.$RES_LJSH.'",';
			//$areaJson.='"totalvalue":"'.$ljsh.'",';
			$areaJson.='"leakcount":"'.$gzs.'",';
			$areaJson.='"allcount":"'.$zs.'",';
			$areaJson.='"leakpercent":"'.(round($gzs/$zs,3)*100).'%"';
			$areaJson.="},";
		}
		$areaJson = rtrim($areaJson,",");
		$areaJson.=']}';
		echo $areaJson;
	}


	//获取节点列表-参数(区域)
	public function GetSupListByArea($POST_DATA){
		//$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);
		//$DAO_Trap=M("trapinfo");

		$areaid=$Res_Data->aid;
		//$tstate_where=$Res_Data->tstate;
		$CompanyID_ = $Res_Data->cid;
		$area_where="";
		if($areaid!=""){
			$area_where=" and areaid=".$areaid." ";
		}
		/*
		$tstate_where_w="";
		if($tstate_where=="" ){
			$tstate_where_w="";
		}else if($tstate_where=="1"){
			$tstate_where_w="1";//异常
		}else if($tstate_where=="-1"){
			$tstate_where_w="-1";//低温
		}else {
			$tstate_where_w="0";//正常
		}*/
		
		//$SQL_QUERY="select * from ( select id,trapno,area,areaid,exlevel,temstate,battery,traptype,trapmodel,datecheck,trapname,usemtfi,linesize from trapinfo where companyid=".$CompanyID_." ".$area_where." order by datecheck desc limit 0,1000) t group by trapno";
		$SQL_QUERY="select * from trapmodel where companyid=".$CompanyID_." and areaid=".$areaid." order by statusTime desc";
		$DAO = M();
		$res=$DAO->query($SQL_QUERY);
		
		$show_list_str='{"res":[';
		foreach($res as $item){
			$show_list_str.="{";
			$show_list_str.='"trapno":"'.$item['trapno'].'",';
			$show_list_str.='"battery":"'.$item['batterystatus'].'",';
			$show_list_str.='"traptype":"'.$item['traptype'].'",';
			$show_list_str.='"trapmodel":"'.$item['trapmodel'].'",';

			$show_list_str.='"trapname":"'.$item['trapname'].'",';
			$show_list_str.='"usemtfi":"'.$item['usemtfi'].'",';
			$show_list_str.='"spressure":"10 bar",';
			$show_list_str.='"linesize":"DN '.$item['linesize'].'",';


			$state_="0";
			if(strtotime($item["statustime"]." +60 min")<=time()){
				$state_="-2";//离线
			}else if($item['status']=="-1"){
				$state_="-1";//低温
			}else if((int)$item['status']>0){
				$state_="1";//泄漏
			}
			$show_list_str.='"trapstate":"'.$state_.'"';
			$show_list_str.="},";
		}
		$show_list_str=rtrim($show_list_str,",");
		$show_list_str.=']}';
		echo $show_list_str;
	}

	//获取节点详细
	public function GetSupListByTrap($POST_DATA){

		//$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);

		$Page_Cur=$Res_Data->page;
		$CompanyID = $Res_Data->cid;
		$PageSize=$Res_Data->pagesize;
		$TrapID = $Res_Data->tid;
		$TS = $Res_Data->ts;
		$TS = date("Y-m-d",strtotime("-".$TS." day"));
		$TE = date("Y-m-d",strtotime("+1 day"));//$Res_Data->te;
		
		if($Page_Cur==''){
			$Page_Cur=1;
		}

		$PageS=((int)$Page_Cur-1)*(int)$PageSize;

		$DAO_Trap=M("trapinfo");//."' and TrapState='".$_GET["trapstate"].
		/*
		符合规则的判断
		*/

		$WHERE_STR= "";//$this->CheckRoleInfo_GET($CompanyID);

		$List_ALL = $DAO_Trap->where("CompanyID=".$CompanyID." and TrapNo='".$TrapID."' and  DateCheck>='".$TS."' and DateCheck<='".$TE."' ".$WHERE_STR)->order("DateCheck desc")->limit($PageS,$PageSize)->select();

		$AllDataCount = $DAO_Trap->where("CompanyID=".$CompanyID." and TrapNo='".$TrapID."' and  DateCheck>='".$TS."' and DateCheck<='".$TE."' ".$WHERE_STR)->order("DateCheck desc")->count();

		$AllPageCount=(int)$AllDataCount/(int)$PageSize;
		if((int)$AllDataCount%(int)$PageSize!=0){
			(int)$AllPageCount=(int)$AllPageCount+1;
		}
		if((int)$AllPageCount==0){
			$AllPageCount=1;
		}

		$show_list_str='{"res":[';//']}}';
		foreach ($List_ALL as $trap_each) {
			//$ST_S=$trap_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");
			$show_list_str.="{";
			$show_list_str.='"trapno":"'.$trap_each["trapno"].'",';
			$show_list_str.='"trapname":"'.$trap_each["trapname"].'",';
			$show_list_str.='"area":"'.$trap_each["area"].'",';
			$show_list_str.='"location":"'.$trap_each["location"].'",';
			$show_list_str.='"trapmodel":"'.$trap_each["trapmodel"].'",';
			//$show_list_str.='"trapstate":"'.$ST_S.'",';
			$show_list_str.='"datecheck":"'.$trap_each["datecheck"].'",';
			$show_list_str.='"traptype":"'.$trap_each["traptype"].'",';
			$show_list_str.='"usemtfi":"'.$trap_each["usemtfi"].'",';
			$show_list_str.='"spressure":"'.$this->DWHS("0",$trap_each["spressure"],1,$CompanyID).'",';
			$show_list_str.='"linesize":"'.$this->DWHS("1",$trap_each["linesize"],1,$CompanyID).'",';
			$show_list_str.='"linktype":"'.$trap_each["linktype"].'",';
			//$show_list_str.='"outtype":"'.$trap_each["outtype"].'",';
			$show_list_str.='"temp":"'.$this->DWHS("2",$trap_each["newtem"],1,$CompanyID).'",';
			$show_list_str.='"exlevel":"'.$trap_each["exlevel"].'",';
			$show_list_str.='"tempstate":"'.$trap_each["temstate"].'",';
			$show_list_str.='"createtime":"'.$trap_each["createtime"].'",';
			$show_list_str.='"lossamount":"'.round($this->DWHS("4",$trap_each["lossamount"],1,$CompanyID),2).'",';
			$show_list_str.='"lossmoney":"'.$trap_each["moneyunit"].round(((float)$trap_each["lossamount"]*(float)$trap_each["moneyton"])/1000,2).'",';
			
			$show_list_str.='"battery":"'.$trap_each["battery"].'",';
			
			$show_list_str = rtrim($show_list_str,",");
			$show_list_str.="},";
		}
		$show_list_str = rtrim($show_list_str,",");
		$show_list_str.='],"datacount":"'.$AllDataCount.'"}';
		echo $show_list_str;
	}


	//节点详细-折线图
	public function GetLineByTrap($POST_DATA){
		
		//$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);
		$CompanyID = $Res_Data->cid;
		$TrapID = $Res_Data->tid;
		//$TS = $Res_Data->ts;
		//$TE = $Res_Data->te;
		$TS = $Res_Data->ts;
		$TS = date("Y-m-d",strtotime("-".$TS." day"));
		$TE = date("Y-m-d",strtotime("+1 day"));//$Res_Data->te;
		

		$WHERE_STR = " companyid=".$CompanyID." ";

		if($TrapID!=""){
			$WHERE_STR .= " and trapno='".$TrapID."'";
		}

		if($TS!=""){
			$WHERE_STR .= " and datecheck between '".$TS."' and '".$TE."' ";
		}
		$WHERE_STR .=" and datecheck <> '0000-00-00 00:00:00'";
		$Dao_Trapinfo = M("analysis");
		$RES_ARRAY_COUNT = $Dao_Trapinfo->where($WHERE_STR)->count();
		$FQ=1;
		while ($RES_ARRAY_COUNT / $FQ > 90){
			$FQ=$FQ+1;
		}

		$SQL_SELECT = "select * from (select (@i:=@i+1) i,a.* from analysis a,(SELECT @i:=-1) as i where ".$WHERE_STR.") t where i%".$FQ."=0 order by datecheck asc";
		$DAO = M();
		$Res_List = $DAO->query($SQL_SELECT);
		$show_list_str='{"res":[';//']}}';
		foreach($Res_List as $item){
			$show_list_str.="{";
			$show_list_str.='"tem":"'.$item["temperature"].'",';//温度
			//$show_list_str.='"level":"'.$item["lastlevel"].'",';//泄漏等级
			$show_list_str.='"level":"'.round((((float)$item["lossvalue"])/1000),2).'",';//泄漏量
			$show_list_str.='"leak":"'.round(((float)$item["allleak"]/1000),2).'",';//累计泄漏量
			$show_list_str.='"date":"'.$item["datecheck"].'"';//累计泄漏量
			$show_list_str.="},";
		}
		$show_list_str = rtrim($show_list_str,",");
		$show_list_str.=']}';
		echo $show_list_str;
	}


	//统计分析-柱图-概况 与饼图情况
	public function GetBarStatus($POST_DATA){
		//$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);
		$CompanyID = $Res_Data->cid;
		$AreaID = $Res_Data->aid;
		$Dao_Trapinfo = M();

		$where_SQL = " companyid=".$CompanyID." ";
		if($AreaID!=""){
			$where_SQL .= " and areaid=".$AreaID." ";
		}
		$SQL = "select statusTime as datecheck,`status`,usemtfi,traptype from trapmodel where ".$where_SQL." ";
		
		$res = $Dao_Trapinfo->query($SQL);
		$ALL = count($res);
		$ZC=0;
		$XL=0;
		$DW=0;
		$LX=0;
		$PP = array();
		$XX = array();
		foreach($res as $item){
			$EX=$item["status"];
			$TEM="0";
			if($item["status"]=="-1"){
				$EX="0";
				$TEM="1";
			}
			if($item["datecheck"] == "0000-00-00 00:00:00" || strtotime($item["datecheck"]." +60 min")<=time()){
				$LX=$LX+1;
			}else if($EX=="0" && $TEM=="0"){
				$ZC=$ZC+1;
			}else if((int)$EX>0){
				$XL=$XL+1;

				if(array_key_exists($item["usemtfi"],$PP)){
					$PP[$item["usemtfi"]]=$PP[$item["usemtfi"]]+1;
				}else{
					$PP[$item["usemtfi"]]=1;
				}

				if(array_key_exists($item["traptype"],$XX)){
					$XX[$item["traptype"]]=$XX[$item["traptype"]]+1;
				}else{
					$XX[$item["traptype"]]=1;
				}

			}else if($TEM!="0"){
				$DW=$DW+1;
			}
		}
		$PP_JSON="";
		$XX_JSON="";
		foreach ( $PP as $key =>  $value ) { 
			$PP_JSON.='"'.$key .'":"'.$value.'",';
		}
		foreach ( $XX as $key =>  $value ) { 
			$XX_JSON.='"'.$key .'":"'.$value.'",';
		}
		$PP_JSON = rtrim($PP_JSON,",");
		$XX_JSON = rtrim($XX_JSON,",");
		//echo '{"res":[{"bar":[{"all":"'.$ALL.'","normal":"'.$ZC.'","leak":"'.$XL.'","clod":"'.$DW.'","offline":"'.$LX.'"}],"piepp":[{'.$PP_JSON.'}],"piexx":[{'.$XX_JSON.'}]}]}';
		echo '{"res":[{"bar":[{"all":"'.$ALL.'","normal":"'.$ZC.'","leak":"'.$XL.'","clod":"'.$DW.'","offline":"'.$LX.'"}],"piexx":[{'.$XX_JSON.'}],"piepp":[{'.$PP_JSON.'}]}]}';

	}

	
	//统计分析-主页折线-统计
	public function GetBarAnalysis($POST_DATA){
		$days = 30;//默认30天
		//$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);
		$CompanyID = $Res_Data->cid;
		$AreaID = $Res_Data->aid;
		$where_SQL = " companyid=".$CompanyID." ";
		if($AreaID!=""){
			$where_SQL .= " and areaid=".$AreaID." ";
		}
		$ENDDATA = date('Y-m-d',strtotime('+1 day'));
		$STARTDATA = date('Y-m-d',strtotime('-30 day'));
		$where_SQL .= " and datecheck between '".$STARTDATA."' and '".$ENDDATA."' ";
		$TrapModelDAO = M("trapmodel");
		$TCount = $TrapModelDAO->where("companyid=".$CompanyID)->count();
		// $Sql="SELECT * FROM (
			// SELECT 
			// trapno,
			// id,AreaID,LastLevel,LossValue,AllLeak, 
			// datecheck,DATE_FORMAT(datecheck,'%Y-%m-%d %H:00:00') as dc,
			// IF(@FLAG = CONCAT(trapno,DATE_FORMAT(datecheck,'%Y-%m-%d %H:00:00')),@RNK := @RNK + 1,@RNK := 0) AS RNK,
			// @FLAG := CONCAT(trapno,DATE_FORMAT(datecheck,'%Y-%m-%d %H:00:00')) AS FLAG
			// FROM analysis A,(SELECT @FLAG := '',@RNK := 0) B where ".$where_SQL."
			// ORDER BY datecheck ASC
		  // ) T
		  // WHERE RNK = 0
		  // group by trapno,dc
		  // ORDER BY dc ASC  ";
		$Sql="SELECT trapno,id,AreaID,LastLevel,LossValue,AllLeak,dc FROM (SELECT trapno,id,AreaID,LastLevel,LossValue,AllLeak,DATE_FORMAT(datecheck,'%Y-%m-%d %H:00:00') as dc FROM analysis where ".$where_SQL." order by id desc)a group by dc,trapno order by dc asc";
		
		//if($AreaID ==""){$AreaID ="0";}
		//$Sql="call MobileMainLine(".$CompanyID.",".$AreaID.",'".$STARTDATA."','".$ENDDATA."',100)";
		$DAO = M();
		$res = $DAO->query($Sql);
		$RES_JSON='{"res":[';//']}';
		$Res_Array=array();
		foreach($res as $item){
			$Res_Array[$item["dc"]][]=$item["trapno"]."*".$item["lastlevel"]."*".$item["lossvalue"]."*".$item["allleak"];
		}
		$ZXL=0;
		$FirstZXL=0;
		$index_falg=0;
		$LV_JSON="";
		$LT_JSON="";
		$LC_JSON="";
		//$ALLCount = count($Res_Array);
		$LAST_ARRAY = array();
		$LAST_ARRAY_tem = array();
		//$LAST_ARRAY["total"]=0;
		foreach ( $Res_Array as $key =>  $value ) {
			$XL=0;
			$GZ=0;
			$ZXL=0;
			foreach($value as $item){
				$N_Array = explode("*",$item);
				if((int)$N_Array[1]>0){
					$GZ=$GZ+1;
				}
				$XL=$XL+(float)$N_Array[2];
				$ZXL=$ZXL+(float)$N_Array[3];
				if($index_falg==0){
					$FirstZXL=$FirstZXL+(float)$N_Array[3];
				}
				if(array_key_exists($N_Array[0],$LAST_ARRAY)){
					$LAST_ARRAY=$this->array_remove($LAST_ARRAY,$N_Array[0]);
				}
				$LAST_ARRAY_tem[$N_Array[0]]=(float)$N_Array[3];
			}
			
			foreach($LAST_ARRAY as $lastkey->$lastitem){
				$ZXL=$ZXL+$lastitem;
				$LAST_ARRAY_tem[$lastkey]=$lastitem;
			}
			$LAST_ARRAY=$LAST_ARRAY_tem;
			$LAST_ARRAY_tem=array();
			$index_falg=1;
			//$LAST_ARRAY["total"]=$ZXL;
			//$LV_JSON.='{"key":"'.strtotime($key).'","value":"'.round($this->DWHS("4",$XL,1,$CompanyID),2).'"},';
			//$LT_JSON.='{"key":"'.strtotime($key).'","value":"'.round($this->DWHS("3",($ZXL-$FirstZXL),1,$CompanyID),2).'"},';
			$LV_JSON.='{"key":"'.strtotime($key).'","value":"'.round(($XL/1000),3).'"},';
			$LT_JSON.='{"key":"'.strtotime($key).'","value":"'.round((($ZXL-$FirstZXL)/1000),3).'"},';
			$LC_JSON.='{"key":"'.strtotime($key).'","value":"'.round($GZ/$TCount*100,2).'"},';//round(((float)$GZ/(float)$ALLCount*100),2)
		}
		$LV_JSON = rtrim($LV_JSON,",");
		$LT_JSON = rtrim($LT_JSON,",");
		$LC_JSON = rtrim($LC_JSON,",");

		$RES_JSON.='{"lossvalue":['.$LV_JSON.']},{"losstotal":['.$LT_JSON.']},{"leakcount":['.$LC_JSON .']}';
		$RES_JSON.=']}';
		echo $RES_JSON;
	}
	
	public function array_remove($data, $key){
		if(!array_key_exists($key, $data)){
			return $data;
		}
		$keys = array_keys($data);
		$index = array_search($key, $keys);
		if($index !== FALSE){
			array_splice($data, $index, 1);
		}
		return $data;
	}
	//统计分析-饼图(品牌)
	/*
	public function GetPieAnalysisPP(){
		$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);
		$CompanyID = $Res_Data->cid;
		$AreaID = $Res_Data->aid;
		$Dao_Trapinfo = M();
	}*/

	//统计分析-饼图(形式)
	/*
	public function GetPieAnalysisXX(){
		$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);
		$CompanyID = $Res_Data->cid;
		$AreaID = $Res_Data->aid;
		$Dao_Trapinfo = M();
	}*/

	public function GetBarPie($POST_DATA){
		//$POST_DATA = $_POST["data"];
		if($POST_DATA==""){
			exit;
		}
		$Res_Data = json_decode($POST_DATA);
		$CompanyID = $Res_Data->cid;
		$AreaID = $Res_Data->aid;
		$LAN = $Res_Data->lan;
		$Dao_Trapinfo = M();

		$where_SQL = " companyid=".$CompanyID." ";
		if($AreaID!=""){
			$where_SQL .= " and areaid=".$AreaID." ";
		}
		$SQL = "select statusTime as datecheck,`status`,usemtfi,traptype from trapmodel where ".$where_SQL." ";
		
		$res = $Dao_Trapinfo->query($SQL);
		$ALL = count($res);
		$ZC=0;
		$XL=0;
		$DW=0;
		$LX=0;

		$PP = array();
		$XX = array();
		$PP_count = array();
		$XX_count = array();
		
		foreach($res as $item){
			
			if(array_key_exists($item["usemtfi"],$PP_count)){
				$PP_count[$item["usemtfi"]]=$PP_count[$item["usemtfi"]]+1;
			}else{
				$PP_count[$item["usemtfi"]]=1;
			}

			if(array_key_exists($item["traptype"],$XX_count)){
				$XX_count[$item["traptype"]]=$XX_count[$item["traptype"]]+1;
			}else{
				$XX_count[$item["traptype"]]=1;
			}
			
			if(strtotime($item["datecheck"]." +60 min")<=time()){
				$LX=$LX+1;
			}else if($item["status"]=="0"){
				$ZC=$ZC+1;
			}else if((int)$item["status"]>0){
				$XL=$XL+1;

				if(array_key_exists($item["usemtfi"],$PP)){
					$PP[$item["usemtfi"]]=$PP[$item["usemtfi"]]+1;
				}else{
					$PP[$item["usemtfi"]]=1;
				}

				if(array_key_exists($item["traptype"],$XX)){
					$XX[$item["traptype"]]=$XX[$item["traptype"]]+1;
				}else{
					$XX[$item["traptype"]]=1;
				}

			}else if($item["status"]=="-1"){//((int)$item["temstate"]<50){
				$DW=$DW+1;
			}
		}
		$PP_JSON="";
		$XX_JSON="";
		$XX_C=array(
			'bucket'=>'倒吊桶式','float'=>'浮球式','disc'=>'热动力式','t-ctrl'=>'调温式','thermo'=>'热静力式','wax'=>'蜡式','orifice'=>'孔板式','bellows'=>'波纹管式','other'=>'其他'
		);
		$XX_E=array(
			'bucket'=>'Bucket','float'=>'Float','disc'=>'Disc','t-ctrl'=>'T-ctrl','thermo'=>'Thermo','wax'=>'Wax',
			'orifice'=>'Orifice','bellows'=>'Bellows','other'=>'Other'
		);
		$PP_C=array(
			'cis'=>'中智精工','tlv'=>'TLV','miya'=>'宫协','yar'=>'YAR','sar'=>'斯派莎克','arm'=>'阿姆斯壮','gest'=>'杰斯特拉','nich'=>'尼克森','velan'=>'威兰','watm'=>'华申 马克丹尼',	'ari'=>'艾瑞','ayvaz'=>'AYVAZ','termo'=>'TERMO','zamkon'=>'ZAMKON','mepco'=>'MEPCO','hoffman'=>'霍夫曼','adca'=>'ADCA','pennant'=>'PENNANT','douglas'=>'道格拉斯','920'=>'红峰','yingqiao'=>'英侨','dsc'=>'DSC','shuangliang'=>'扬州双良','beifa'=>'北京北阀','other'=>'其他'
		);
		$PP_E=array(
			'cis'=>'CIS','tlv'=>'TLV','miya'=>'MIYAWAKI','yar'=>'YAR','sar'=>'spirax sarco','arm'=>'armstrong','gest'=>'gestra','nich'=>'nicholson','velan'=>'velan','watm'=>'watson mcdaniel',	'ari'=>'ARI','ayvaz'=>'AYVAZ','termo'=>'TERMO','zamkon'=>'ZAMKON','mepco'=>'MEPCO','hoffman'=>'HOFFMAN','adca'=>'ADCA','pennant'=>'PENNANT','douglas'=>'DOUGLAS','920'=>'920','yingqiao'=>'yingqiao','dsc'=>'DSC','shuangliang'=>'shuangliang','beifa'=>'Beijing North valve','other'=>'Other'
		);
		
		foreach ($PP_count as $key =>  $value ) { 
			$XL_IT="0";
			if(array_key_exists($key,$PP)){
				$XL_IT=$PP[$key];
			}
			
			//$PP_JSON.='"key":"'.$key .'","value":"'.$value.'","dec":{"total":"'.$value.'","leak":"'.$XL_IT.'","persent":"'.(round((float)$XL_IT/(float)$value*100,2)).'%"},';
			$DESC=$PP_C[strtolower($key)].'\n总数:'.$value.'\n故障:'.$XL_IT.'\n故障率:'.(round((float)$XL_IT/(float)$value*100,2)).'%';
			if($LAN=="e"){
				$DESC=$PP_E[strtolower($key)].'\nTotal:'.$value.'\nLeak:'.$XL_IT.'\nRate:'.(round((float)$XL_IT/(float)$value*100,2)).'%';
			}else if($LAN=="j"){
				$DESC=$PP_E[strtolower($key)].'\nTotal:'.$value.'\nLeak:'.$XL_IT.'\nRate:'.(round((float)$XL_IT/(float)$value*100,2)).'%';
			}
			$PP_JSON.='{"key":"'.$key .'","value":"'.$value.'","dec":"'.$DESC.'"},';
		}
		foreach ($XX_count as $key =>  $value ) { 
			$XL_IT="0";
			if(array_key_exists($key,$XX)){
				$XL_IT=$XX[$key];
			}
			$DESC=$XX_C[strtolower($key)].'\n总数:'.$value.'\n故障:'.$XL_IT.'\n故障率:'.(round((float)$XL_IT/(float)$value*100,2)).'%';
			if($LAN=="e"){
				$DESC=$XX_E[strtolower($key)].'\nTotal:'.$value.'\nLeak:'.$XL_IT.'\nRate:'.(round((float)$XL_IT/(float)$value*100,2)).'%';
			}else if($LAN=="j"){
				$DESC=$XX_E[strtolower($key)].'\nTotal:'.$value.'\nLeak:'.$XL_IT.'\nRate:'.(round((float)$XL_IT/(float)$value*100,2)).'%';
			}
			//$XX_JSON.='"key":"'.$key .'","value":"'.$value.'","dec":{"total":"'.$value.'","leak":"'.$XL_IT.'","persent":"'.(round((float)$XL_IT/(float)$value*100,2)).'%"},';
			$XX_JSON.='{"key":"'.$key .'","value":"'.$value.'","dec":"'.$DESC.'"},';
		}
		
		$PP_JSON = rtrim($PP_JSON,",");
		$XX_JSON = rtrim($XX_JSON,",");
		echo '{"res":[{"bar":[{"all":"'.$ALL.'","normal":"'.$ZC.'","leak":"'.$XL.'","clod":"'.$DW.'","offline":"'.$LX.'"}],"piepp":['.$PP_JSON.'],"piexx":['.$XX_JSON.']}]}';
		
	}
	//接口入口
	public function GetData($method){
		switch($method){
			case "login"://登录 username 用户名 password 密码
				$this->LoginData($_POST["data"]);
			break;
			case "area"://区域列表
				$this->GetAreas($_POST["data"]);
			break;
			case "traplist"://节点列表
				$this->GetSupListByArea($_POST["data"]);
			break;
			case "trapinfo"://节点详细
				$this->GetSupListByTrap($_POST["data"]);
			break;
			case "trapinfoline"://节点详细折线图
				$this->GetLineByTrap($_POST["data"]);
			break;
			case "barandpie"://主页饼
				$this->GetBarStatus($_POST["data"]);
			break;
			case "line"://主页折线图
				$this->GetBarAnalysis($_POST["data"]);
			break;
			case "pie"://主页饼与柱
				$this->GetBarPie($_POST["data"]);
			break;
		}
	}

	//加密函数
	public function Md5En($Par,$MK){
		$MD51=md5($Par);
		$MD5_res = md5($MD51."tms".$MK);
		return $MD5_res;
	}

	//参数验证函数
	public function DataVer($ParGet,$Par,$MK){
		$RES = $this->Md5En($Par,$MK);
		if($RES == $ParGet){
			return true;
		}else{
			return false;
		}
	}



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

}
