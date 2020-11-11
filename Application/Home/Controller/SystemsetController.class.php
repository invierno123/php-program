<?php
namespace Home\Controller;
use Think\Controller;
///首页
class SystemsetController extends Controller {
	public function index(){
		if($_GET["oid"]!="" && cookie("companyid").""==""){
				cookie("companyid",$_GET["oid"]);
		}

		$this->AreaInfoGet();


		$LAYOUTDAO=M("layout");
		$GetLayOutInfo = $LAYOUTDAO->where("companyid=".cookie("companyid"))->find();
		$this->layoutStyle=$GetLayOutInfo["layouttype"];
    $this->display();
  }

	public function updatetrap(){
		$ID_Array =split(",",$_GET["ida"]);
		$AreaID = $_GET["aid"];
		$Sort_Array=split(",",$_GET["sort"]);

		$areainfo=M("areainfo");
		$areainfo_result = $areainfo->where("companyid=".cookie("companyid")." and id=".str_replace("area_","",$AreaID))->find();

		$arrayLength=count($ID_Array);
		$trapmodel = M("trapmodel");
		for ($i=0; $i < $arrayLength; $i++) {
			if($ID_Array[$i]==""){continue;}
			$DATA_UPDATE["AreaId"] = str_replace("area_","",$AreaID);
			$DATA_UPDATE["Area"] = $areainfo_result["areaname"];
			$DATA_UPDATE["location"] = $areainfo_result["areaname"];
			$DATA_UPDATE["OrderNum"]=$Sort_Array[$i];
			$trapmodel->where("companyid=".cookie("companyid")." and id=".$ID_Array[$i]."")->data($DATA_UPDATE)->save();
		}


	}

	public function deltrap(){
		$trapDAO = M("trapmodel");
		$AID = $_GET["aid"];
		$TID = $_GET["tid"];
		$DATA_UPDATE["AreaId"] = "0";
		$DATA_UPDATE["Area"] = "";
		$DATA_UPDATE["location"] = "";
		$DATA_UPDATE["OrderNum"]=0;
		$trapDAO->where("companyid=".cookie("companyid")." and id=".$TID." and areaid=".str_replace("area_","",$AID))->data($DATA_UPDATE)->save();
	}
	public function TrapEdit(){

		$AreaID = $_GET["areacode"];
		$AreaID= str_replace("area_","",$AreaID);

		$TrapDAO = M("trapmodel");


		$AllListCount=$TrapDAO->where("companyid=".cookie("companyid")." and areaid=".$AreaID)->group("trapno")->order("ordernum")->count();
		$MAXORDERINFO=$TrapDAO->where("companyid=".cookie("companyid")." and areaid=".$AreaID)->group("trapno")->order("ordernum desc")->limit(1)->find();

		$CuPage=(int)("0".$_GET["page"]);
		if($CuPage==0){
			$CuPage=1;
		}
		$PageSize=10;

		$Trap_list = $TrapDAO->where("companyid=".cookie("companyid")." and areaid=".$AreaID)->group("trapno")->order("ordernum")->limit(($CuPage-1)*$PageSize,$PageSize)->select();

		$TP=1;
		if((int)$AllListCount%(int)$PageSize==0){
			$TP=(int)$AllListCount/(int)$PageSize;
		}else{
			$TP=(int)((int)$AllListCount/(int)$PageSize)+1;
		}

		$this->CP=$CuPage;
		$this->TP=$TP;
		$this->MaxOrder=$MAXORDERINFO["ordernum"];
		$this->tlist=$Trap_list;

		$AreaInfo_Name = L("L_AREA_QY");//.$Trap_list[0]["area"];
		$DAO_AREA = M("areainfo");
		$AreaInfo_Name_M = $DAO_AREA->where("Id=".$AreaID)->find();
		$AreaInfo_Name.=":".$AreaInfo_Name_M["areaname"];
		$this->AreaINFO=$AreaInfo_Name;
		$this->display();
	}

	public function GetTrap(){
		$TrapModel = M("trapmodel");
		$opID= $_GET["id"];
		$modelinfo = $TrapModel->where(" CompanyID=".cookie("companyid")." and id='".$opID."'")->find();
		echo $modelinfo["trapname"].",".$modelinfo["traptype"].",".$modelinfo["linktype"].",".$modelinfo["linesize"].",".$modelinfo["outtype"]."";
	}
	public function GetReTrap(){
		$TrapModel = M("trapmodel");
		$SelectedID= $_GET["selected"];
		$List_ = $TrapModel->where(" CompanyID=".cookie("companyid")." and AreaId=0 and id not in(".rtrim($SelectedID,",").")")->select();
		$res_html = "<option value='0'></option>";
		foreach ($List_ as $key) {
			$res_html .= "<option value='".$key["id"]."'>".$key["trapno"]."</option>";
		}
		if($res_html=="<option value='0'></option>"){
			$res_html="";
		}
		echo $res_html;
	}
	public function changepwd()
	{
		$this->now_name=cookie("companyloginame");
		$this->display();
	}
	public function delarea(){
		$areaid = $_GET["aid"];
		$AreaDAO=M("areainfo");
		$AreaDAO->where("id=".str_replace("area_","",$areaid))->delete();
		echo $del_res;
	}

	public function addarea(){
		$area_name=$_GET["an"];
		$area_loca=$_GET["al"];
		if(strtolower($area_loca)=="left"){
			$area_loca="L";
		}else if(strtolower($area_loca)=="center"){
			$area_loca="M";
		}else if(strtolower($area_loca)=="right"){
			$area_loca="R";
		}
		$C_=(((int)$_GET[$area_loca."MC"])+1);

		$AreaDAO=M("areainfo");
		$Ai_model["AreaName"]=$area_name;
		$Ai_model["AreaUser"]="暂无";
		$Ai_model["UserTEL"]="12346578";
		$Ai_model["AreaLocation"]=$area_loca."".$C_;
		$Ai_model["CompanyID"]=cookie("companyid");

		$res_find_data = $AreaDAO->where("AreaName='".$area_name."'")->find();
		if($res_find_data!=null && $res_find_data["id"]!="0")
		{
			echo "-9";
			return;
		}
		$add_res = $AreaDAO->add($Ai_model);
		echo $add_res;
	}

	public function updatearea(){
		$left = $_GET["left"];
		$middle = $_GET["middle"];
		$right = $_GET["right"];
		$layout = $_GET["layout"];
		if($layout=="默认"){
			$layout="1:1:1";
		}

		$LAYOUTDAO=M("layout");
		$GetLayOutInfo = $LAYOUTDAO->where("companyid=".cookie("companyid"))->find();
		if(count($GetLayOutInfo)>0){
			$UPDATE_LayOut["layouttype"]=$layout;
			$LAYOUTDAO->where("companyid=".cookie("companyid"))->save($UPDATE_LayOut);
		}else{
			$ADDDATA["layouttype"]=$layout;
			$ADDDATA["companyid"]=cookie("companyid");
			$ADDDATA["descript"]="";
			$LAYOUTDAO->add($ADDDATA);
		}


		$left_array=split(",",$left);
		$middle_array=split(",",$middle);
		$right_array=split(",",$right);

		$left_array_count = count($left_array);
		$middle_array_count = count($middle_array);
		$right_array_count = count($right_array);

		//$this->LMC=$left_array_count;
		//$this->MMC=$middle_array_count;
		//$this->RMC=$right_array_count;
		//$where_array = array();
		//$data_array = array();
		$AreaDAO=M("areainfo");
		$error_log=array();
		for ($i=0; $i < $left_array_count; $i++) {
			if($left_array[$i]!=""){
				$id=str_replace("area_","",$left_array[$i]);
				$where_array = array('Id' => $id);
				$data_array = array('AreaLocation' => "L".$i);
				$res_ = $AreaDAO->where($where_array)->data($data_array)->save();
				if(!$res_){
					$error_log[]="L-".$id;
				}
			}
		}
		for ($i=0; $i < $middle_array_count; $i++) {
			if($middle_array[$i]!=""){
				$id=str_replace("area_","",$middle_array[$i]);
				$where_array = array('Id' => $id);
				$data_array = array('AreaLocation' => "M".$i);
				$res_ = $AreaDAO->where($where_array)->data($data_array)->save();
				if(!$res_){
					$error_log[]="M-".$id;
				}
			}
		}
		for ($i=0; $i < $right_array_count; $i++) {
			if($right_array[$i]!=""){
				$id=str_replace("area_","",$right_array[$i]);
				$where_array = array('Id' => $id);
				$data_array = array('AreaLocation' => "R".$i);
				$res_ = $AreaDAO->where($where_array)->data($data_array)->save();
				if(!$res_){
					$error_log[]="R-".$id;
				}
			}
		}

		//$u_res = $this->saveAll($where_array,$data_array,"areainfo");//$AreaDAO->where($where_array)->save($data_array);
		echo count($error_log);
	}


	public function AreaInfoGet(){
		$areaDAO = M("areainfo");
		$trapDAO = M("trapmodel");

		$Area_list = $areaDAO->where("companyid=".cookie("companyid"))->order("arealocation asc")->select();
		$Area_List_count = count($Area_list);
		$layoutDAO = M("layout");
		$layoutInfo = $layoutDAO->where("companyid=".cookie("companyid"))->find();
		$L_Number=0;
		$M_Number=0;
		$R_Number=0;
		$V_V=0;
		$MaxID=0;
		for ($i=0; $i <$Area_List_count ; $i++) {
			if($MaxID<(int)$Area_list[$i]["id"]){
				$MaxID = (int)$Area_list[$i]["id"];
			}

			$trap_number_area_list = $trapDAO->where("CompanyID='".cookie("companyid")."' and AreaId='".$Area_list[$i]["id"]."' ")->group("TrapNo")->select();
			$trap_number_area=count($trap_number_area_list);

			if($Area_list[$i]["arealocation"]=="L".$L_Number){
				$L_str.="'area_".$Area_list[$i]["id"]."':'".L("L_AREA_QY").":".$Area_list[$i]["areaname"]."<font style=color:gray;font-size:12px;float:right;padding-right:25px;>· ".L("L_AREA_SL").":".$trap_number_area."</font>',";
				$L_Number++;
				$V_V=1;
			}else if($Area_list[$i]["arealocation"]=="M".$M_Number){
				$M_str.="'area_".$Area_list[$i]["id"]."':'".L("L_AREA_QY").":".$Area_list[$i]["areaname"]."<font style=color:gray;font-size:12px;float:right;padding-right:25px;>· ".L("L_AREA_SL").":".$trap_number_area."</font>',";
				$M_Number++;
				$V_V=1;
			}else if($Area_list[$i]["arealocation"]=="R".$R_Number){
				$R_str.="'area_".$Area_list[$i]["id"]."':'".L("L_AREA_QY").":".$Area_list[$i]["areaname"]."<font style=color:gray;font-size:12px;float:right;padding-right:25px;>· ".L("L_AREA_SL").":".$trap_number_area."</font>',";
				$R_Number++;
				$V_V=1;
			}
			if($V_V==0){
				if(strpos($Area_list[$i]["arealocation"],"L")>-1){
					$L_str.="'area_0':'".L("L_AREA_KCQY")."',";
					$L_Number++;
					$i--;
				}else if(strpos($Area_list[$i]["arealocation"],"M")>-1){
					$M_str.="'area_0':'".L("L_AREA_KCQY")."',";
					$M_Number++;
					$i--;
				}else if(strpos($Area_list[$i]["arealocation"],"R")>-1){
					$R_str.="'area_0':'".L("L_AREA_KCQY")."',";
					$R_Number++;
					$i--;
				}
			}
			if($Area_list[$i]["arealocation"]=="") {
				$L_str.="'area_".$Area_list[$i]["id"]."':'".L("L_AREA_QY").":".$Area_list[$i]["areaname"]."<font style=color:gray;font-size:12px;float:right;padding-right:25px;>·".$trap_number_area." ".L("L_AREA_JD")."</font>',";
			}
			$V_V=0;
		}

		$this->LMC=$L_Number-1;
		$this->MMC=$M_Number-1;
		$this->RMC=$R_Number-1;

		$this->MAXID = $MaxID+1;
		$this->Al_L=rtrim($L_str,",");
		$this->Al_M=rtrim($M_str,",");
		$this->Al_R=rtrim($R_str,",");
	}
	public function updateuserinfo()
	{
		$Model_CompanyInfo_Dao= M("companyinfo");
		$up_key=$_POST["key"];
		$up_val=$_POST["val"];
		if($up_key==""||$up_val==""){
			echo "0";
			exit;
		}
		$up_key=explode(',', $up_key);
		$up_val=explode(',', $up_val);
		$arrdata;
		$co_key=count($up_key);
		for($i=0;$i<$co_key;$i++)
		{
			$arrdata[$up_key[$i]]=$up_val[$i];
		}
		$Model_CompanyInfo=$Model_CompanyInfo_Dao->where("id=".cookie("companyid")." and LoginName='".cookie("companyloginame")."'")->save($arrdata);
		if($Model_CompanyInfo!=false && $Model_CompanyInfo>0){
		//	cookie("companyloginame",$arrdata["LoginName"]);
			cookie("companyname",$arrdata["CompanyName"]);
			echo "1";
		}else{
			echo "-2";
		}

	}
	public function updatepwd()
	{
		$Model_CompanyInfo_Dao_up = M("companyinfo");
		$USER_NAME=$_POST["name"];
		$USER_PWD=$_POST["newpwd"];
		$USER_OLDPWD=$_POST["oldpwd"];
		if($USER_NAME=="" || $USER_PWD==""||$USER_OLDPWD==""){
			echo "0";
			exit;
		}
		 $Model_CompanyInfo_up=$Model_CompanyInfo_Dao_up->where("id=".cookie("companyid")." and LoginName='".$USER_NAME."' and LoginPWD='".strtolower(md5($USER_OLDPWD))."'")->find();

		if($Model_CompanyInfo_up!=null && $Model_CompanyInfo_up["id"]>0){
			$data['LoginPWD'] = strtolower(md5($USER_PWD));
			 $Model_CompanyInfo_up=$Model_CompanyInfo_Dao_up->where("id=".cookie("companyid")." and LoginName='".$USER_NAME."'")->save($data);
			 if($Model_CompanyInfo_up!=false && $Model_CompanyInfo_up>0){
				 echo "1";
			 }else{
				 echo "0";
			 }
		}else{
			echo "-2";
		}
	}
	public function addsubuser(){
		$this->display();
	}
	public function disableuser(){
		$uid=$_GET["oid"];
		if($uid==""){
			echo "-1";
			exit;
		}
		$type=$_GET["type"];
		$Dao_Model=M("subuser");
		$data_["Status"]=$type;
		$Dao_result=$Dao_Model->where("Id=".$uid)->save($data_);
		echo $Dao_result;
	}
	public function deluser(){
		$uid=$_GET["oid"];
		if($uid==""){
			echo "-1";
			exit;
		}
		$Dao_Model=M("subuser");
		$Dao_result=$Dao_Model->where("Id=".$uid)->delete();
		echo $Dao_result;
	}
	public function subuser_control(){
		$post_key=$_POST["key"];
		$post_val=$_POST["val"];
		$post_type=$_POST["type"];
		if($post_key==""||$post_key==""||$post_type==""){
      echo "-2";
			exit;
		}
		$str_="";
		$post_oid="";
		if($post_type=="update"){
       $post_oid=$_POST["oid"];
			 $str_.=" and Id !=".$post_oid;
		}
		$add_key=explode(',', $post_key);
		$add_val=explode(',', $post_val);
		$user_info=array();
		$user_info["CompanyID"]=cookie("companyid");
		$user_info["CompanyName"]=cookie("companyname");
		$user_info["AdminName"]=cookie("companyloginame");
    $len_=count($add_key);
		for($i=0;$i<$len_;$i++){
      if($add_key[$i]=="PassWord"){
			  $user_info[$add_key[$i]]=strtolower(md5($add_val[$i]));
			}else{
			  $user_info[$add_key[$i]]=$add_val[$i];
			}
		}
		$where_str="Id=".cookie("companyid")." and LoginName='".$user_info["LoginName"]."'";
		$dao_model=M("companyinfo");
		$model_result=$dao_model->where($where_str)->find();
		if($model_result!=null && $model_result["id"]>0){
      echo "-1";
			exit;
		}
		$DAO_Model=M("subuser");
		$where_str="CompanyID=".cookie("companyid")." and AdminName='".cookie("companyloginame")."' and LoginName='".$user_info["LoginName"]."'".$str_;
		$result_model=$DAO_Model->where($where_str)->find();
		if($result_model!=null && $result_model["id"]>0){
			 echo "-1";
			 exit;
		}
		if($post_type=="update"){
       $result=$DAO_Model->where("Id='".$post_oid."'")->save($user_info);
		}else{
			$user_info["Status"]="1";
			$result=$DAO_Model->add($user_info);
		}
		echo $result;
	}
	public function updateinfo(){
		$Model_Com=M("companyinfo");
    $this->info_list=$Model_Com->where("Id=".cookie("companyid")." and LoginName='".cookie("companyloginame")."'")->find();
		$this->display();
	}
	public function usermanager(){
		$Dao=M("subuser");
		$where_str="AdminName='".cookie("companyloginame")."' and CompanyID=".cookie("companyid");
		$this->count=$count=$Dao->where($where_str)->count();
		$PageSize=10;
		$this->page_all=$page_all = ceil($count/$PageSize);
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$this->page=$page;
		$this->page_up=$page_up = $page-1>0 ? $page-1 : 1;
		$this->page_down=$page_down = $page+1>$page_all ? $page_all : $page+1;
		$Data_List=$Dao->where($where_str)->order('Id ASC')->limit(($page-1)*$PageSize,$PageSize)->select();
        $count_=count($Data_List);
		for($i=0;$i<$count_;$i++){
		$Data_List[$i]["control"]=$Data_List[$i]["status"]=="1"?L("L_AU_Status_Disable"):L("L_AU_Status_Enabled");
		$Data_List[$i]["state"]=$Data_List[$i]["status"]=="1"?L("L_ALERT_TJ_WORK"):L("L_AU_Status_Disable");
		$Data_List[$i]["lastlogintime"]=$Data_List[$i]["lastlogintime"]==""?L("L_AU_No_Record"):$Data_List[$i]["lastlogintime"];
		}
		$this->list=$Data_List;
		$this->display();
	}
	public function updatesubuser(){
		$oid=$_GET["oid"];
		$Dao=M("subuser");
		$where_str="AdminName='".cookie("companyloginame")."' and CompanyID=".cookie("companyid")." and Id=".$oid;
		$Data_List=$Dao->where($where_str)->find();
		$this->list=$Data_List;
		$this->display();
	}
}
