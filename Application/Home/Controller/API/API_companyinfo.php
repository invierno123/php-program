<?php

///TMS-CS登录
function LoginData(){
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
			echo '{"res":"'.$res_['id'].'","data":"admin","per":"11111111","days":"'.$days.'","comname":"'.$ComapnyName.'","username":"'.$res_['username'].'","regTime":"'.$res_['createtime'].'","areaid":"","SPUnit":"'.$res_['spunit'].'","lossUnit":"'.$res_['lossunit'].'","temUnit":"'.$res_['temunit'].'","sizeUnit":"'.$res_['sizeunit'].'","pwUnit":"'.$res_['pwunit'].'","workinghours":"'.$res_['workinghours'].'","workingdays":"'.$res_['workingdays'].'","addlogo":"'.$res_['addlogo'].'"}';
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
					
					echo '{"res":"'.$res_['companyid'].'","data":"user","per":"'.$res_['permissionset'].'","username":"'.$res_['username'].'","days":"'.$days.'","comname":"'.$res_['companyname'].'","areaid":"'.$res_['areaid'].'","regTime":"'.$res_['areaid'].'","SPUnit":"'.$_sub_com_result_['spunit'].'","lossUnit":"'.$_sub_com_result_['lossunit'].'","temUnit":"'.$_sub_com_result_['temunit'].'","sizeUnit":"'.$_sub_com_result_['sizeunit'].'","pwUnit":"'.$_sub_com_result_['pwunit'].'","workinghours":"'.$_sub_com_result_['workinghours'].'","workingdays":"'.$_sub_com_result_['workingdays'].'","addlogo":"'.$_sub_com_result_['addlogo'].'"}';
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

//TMS-CS 获取公司信息
function GetCompanyInfo(){
	$post_=$_POST["data"];
	if($post_==""){
		echo '{"res":"0"}';
		exit;
	}
	$post_=json_decode($post_);
	$companyID=$post_->oid;
	$loginName=$post_->name;
	if($companyID==""||$loginName==""){
		echo '{"res":"0"}';
		exit;
	}

	$Dao_=M("companyinfo");
	$result=$Dao_->where("Id=".$companyID." and LoginName='".$loginName."'")->find();
	if($result!=null && $result["id"]>0){
		$json_list=json_encode($result);
		$arr=array("res"=>"1","data"=>$json_list);
		echo json_encode($arr);
	}else{
		echo '{"res":"0"}';
	}
}

//TMS-CS 修改公司信息
function UpdateCompanyInfo()
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
function UpdatePwd()
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

function ErrorData(){
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


//TMS-Android 统计分析 区域
function GetTrapRepA()
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

//TMS-Android 重置密码
function ResetPwdA(){
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

///TMS-cs 节点数量读取(全部,正常,异常)
function GetTrapStateCount()
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
