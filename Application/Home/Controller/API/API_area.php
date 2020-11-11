<?php


//TMS-CS 区域信息列表获取
/*2017-02-07 林静添加
  添加普通用户权限，只能查看自己负责的区域信息
*/
function getAreaListDrapDown()
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

//TMS-CS分区增加
function AreaADD()
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
function AreaEdit(){
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

//TMS-CS获取区域
function getareas(){
	$DAO_Area=M("areainfo");
	$where_str="CompanyID=".cookie("oid");
	$List_area = $DAO_Area->where($where_str)->select();
	$areas="";
	foreach ($List_area as $area_) {
		$areas.=$area_["id"].",".$area_["areaname"]."/";
	}
	echo rtrim($areas,",");
}

//TMS-CS获取区域列表
function getAreaList(){
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
