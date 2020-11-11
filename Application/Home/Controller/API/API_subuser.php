<?php



//TMS-CS  用户列表展示
function  subAccount(){
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
function DisableUser(){
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

//TMS-CS 账户管理-删除用户
function DelUser(){
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
function SubuserControl(){
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

//TMS-CS 获取用户状态,判断是否被禁用
function getUserState(){
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

//TMS-CS 子账户权限修改
function SubuserPerControl(){
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
