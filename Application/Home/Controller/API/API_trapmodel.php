<?php



// 单位换算
///$TYPE_  0  压力   1 口径  2 温度  3 资源  4 泄漏  $LX 0 转为原始单位(非读取)
function DWHS_trapmodel($TYPE_, $U, $LX,$companyid)
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

//设置节点
function SetEnder()
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


///删除节点_已过期
function DeleteTrapinfo(){
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

///删除阀门节点
function DeleteModel()
{
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$companyid=$Res_Data->cid;
	$trapno = $Res_Data->trapno;
	$dataid = $Res_Data->id;
	
	$sql1="insert into trapmodel_delete(Id,CompanyID,ModelName,AreaId,Area,location,trapNo,houzhui,trapName,TrapType,UseMTFI,SPressure,SPressureOut,STempTop,STempLow,LineSize,LinkType,OutType,OrderNum,DZ,InstallState,SetState,MCUID,MaxLK,CriticalValue,CollectionNum,RevealPer,ZoomMultiple,MinHZ,MaxHZ,ComSwitch,WorkTEM,LeakBase,MaxPercent,AvgPercent,ShellNo) select * from trapmodel where id=$dataid and CompanyID=$companyid and trapNo='$trapno';";
	
	$Dao = M();
	$res = $Dao->execute($sql1);
	if($res>0)
	{
		$res = $Dao->execute("delete from trapmodel where id=$dataid and CompanyID=$companyid and trapNo='$trapno'");
		echo "{\"res\":\"$res\"}";
	}else{
		echo "{\"res\":\"0\"}";
	}
}



///节点信息列表获取
function GetTrapInfo()
{
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$companyid = $Res_Data->cid;
	$areaname = $Res_Data->areaid;
	$wh = $Res_Data->wh;
	$page = $Res_Data->page;
	$pagesize = $Res_Data->pagesize;
	$LIKE = $Res_Data->like;
	



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

	$ListData = $trapmodelDao->where($where_str)->limit(((int)$page-1)*(int)$pagesize,(int)$pagesize)->order("ordernum")->select();
	$ListCount = $trapmodelDao->where($where_str)->count();
	$show_list_str = '{"res":[';
	foreach ($ListData as $data_trapmodel) {
		
		$data_trapmodel["spressure"] = $this->DWHS_trapmodel("0",$data_trapmodel["spressure"],1,$companyid);
		$data_trapmodel["spressureout"] = $this->DWHS_trapmodel("0",$data_trapmodel["spressureout"],1,$companyid);
		
		$data_trapmodel["linesize"] = $this->DWHS_trapmodel("1",$data_trapmodel["linesize"],1,$companyid);
		
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

//更新外壳编号
function trapmodel_shell_()
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
function trapmodelAddUpdate(){
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
	$Data_ADD["SPressure"] = $this->DWHS_trapmodel("0",$Res_Data->sp,0,$Data_ADD["CompanyID"]);
	$Data_ADD["SPressureOut"] = $this->DWHS_trapmodel("0",$Res_Data->spout,0,$Data_ADD["CompanyID"]);
	$Data_ADD["STempTop"] = $this->DWHS_trapmodel("2",$Res_Data->temup,0,$Data_ADD["CompanyID"]);
	$Data_ADD["STempLow"] = $this->DWHS_trapmodel("2",$Res_Data->temlow,0,$Data_ADD["CompanyID"]);
	$Data_ADD["LineSize"] = $this->DWHS_trapmodel("1",$Res_Data->linksize,0,$Data_ADD["CompanyID"]);
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
function getTrapmodelByTrapNo(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$TrapNo = $Res_Data->tn;
	$Companyid = $_GET["ci"];
	$trapmodelDAO = M("trapmodel");
	$select_res_by_tn = $trapmodelDAO->where("trapNo='".$TrapNo."' and CompanyID=".$Companyid)->select();
	echo '{"res":"'.count($select_res_by_tn).'"}';
}

///TMS-cs 节点信息读取
function getTrapmodelByID()
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
	$res_json_str.='"spressure":"'.$this->DWHS_trapmodel(0,$select_res_by_tn["spressure"],1,$COMID).'",';
	$res_json_str.='"spressureout":"'.$this->DWHS_trapmodel(0,$select_res_by_tn["spressureout"],1,$COMID).'",';
	$res_json_str.='"stemptop":"'.$this->DWHS_trapmodel(2,$select_res_by_tn["stemptop"],1,$COMID).'",';
	$res_json_str.='"stemplow":"'.$this->DWHS_trapmodel(2,$select_res_by_tn["stemplow"],1,$COMID).'",';
	$res_json_str.='"linesize":"'.$this->DWHS_trapmodel(1,$select_res_by_tn["linesize"],1,$COMID).'",';
	$res_json_str.='"linktype":"'.$select_res_by_tn["linktype"].'",';
	$res_json_str.='"outtype":"'.$select_res_by_tn["outtype"].'",';
	$res_json_str.='"ordernum":"'.$select_res_by_tn["ordernum"].'",';
	$res_json_str.='"modelname":"'.$select_res_by_tn["modelname"].'",';
	$res_json_str.='"maxlk":"'.$select_res_by_tn["maxlk"].'"';
	$res_json_str.= '}';
	$res_json_str.= ']}';

	echo $res_json_str;
}

//TMS-CS 获取区域里面的节点
function getTraps(){
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

// TCPS 获取TC的频率配置信息
function GetTCSetInfo(){
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
function getTrapHistoryData(){
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