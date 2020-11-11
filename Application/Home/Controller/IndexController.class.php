<?php
namespace Home\Controller;
use Think\Controller;
///首页
class IndexController extends Controller {
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
	public function index(){
		
		$IP = $_SERVER['REMOTE_ADDR'];
		$fw_ = $this->getCity($IP);
		if(strpos($fw_,"潍坊")>0){
			echo("***");
		}else{
			echo("---");
		}
		
		if(cookie("companyid")!=null && cookie("companyid")!=""){
			if($_GET["open"]!=""){
				redirect($_GET["open"]);
			}else {
				redirect("/home/index/main"); 
			}

		}
		
		if($_GET["trapno"]!="" && $_GET["ci"]!="" && $_GET["op"]=="an"){
			if($_GET["trapno"]=="all"){
				$TRAPDAO = M("trapmodel");
				$ALLTRAP = $TRAPDAO->where("companyid=".$_GET["ci"])->select();
				
				foreach($ALLTRAP as $trapitem){
					$res = $this->anlistzl($_GET["ci"],$trapitem["trapno"]);
				}
				
			}else{
				$res = $this->anlistzl($_GET["ci"],$_GET["trapno"]);
			}
		}
		$this->display();
	}
	public function set(){
		$this->display();
	}
	public function setfun(){
		$cjsj=$_GET["cj"];
		$wldz=$_GET["dz"];
		$tzjs=$_GET["js"];
		$comid=cookie("companyid");
		
		
		$res = "00";
		if($cjsj!=""){
			$CompanyDAO=M("companyinfo");
			$DATA["AccessTimeSpan"]=$cjsj;
			$C_RE = $CompanyDAO->where("id=".$comid)->data($DATA)->save();
			$res=$C_RE."";
		}
		if($tzjs!=""){
			if($wldz!=""){
				$TrapDAO = M("trapmodel");
				$DATA_TRAP["LeakBase"]=$tzjs;
				$res.=$TrapDAO->where("CompanyID=$comid and trapNo='".$wldz."'")->data($DATA_TRAP)->save();
				
			}else{
				echo "99";
			}
		}
		
		echo $res;
		
	}
	public function getleakbase(){
		$comid=cookie("companyid");
		$wldz=$_GET["dz"];
		$TrapDAO = M("trapmodel");
		$RES = $TrapDAO->where("CompanyID=$comid and trapNo='".$wldz."'")->select();
		$RES_LB="";
		if(count($RES)>0){
			$RES_LB = $RES[0]["leakbase"];
		}else{
		}
		$RES_LB.=",";
		$CompanyDAO=M("companyinfo");
		$C_RE = $CompanyDAO->where("id=".$comid)->select();
		if(count($C_RE)>0){
			$RES_LB.=$C_RE[0]["accesstimespan"];
		}
		echo $RES_LB;
	}
	public function anlistzl($ci,$tn){
		$ANDAO = M("analysis");
		$ALLLIST = $ANDAO->where("companyid=$ci and trapno='$tn'")->order("DateCheck")->select();
		
		$FALG=-1;
		$PERLEAK=0;
		foreach($ALLLIST as $item){
			$ANDAO2 = M("analysis");
			$FALG=$FALG+1;
			$ALLLEAK=0;
			$UAR["AllLeak"]=0;
			if($FALG==0){
				$ALLLEAK=0;
				$UAR["AllLeak"]=$ALLLEAK;
				$ures = $ANDAO2->where("id=".$item["id"])->data($UAR)->save();
			}else{
				$PerItem=$ALLLIST[$FALG-1];
				$time_span = (((double)((strtotime($item["datecheck"])-strtotime($PerItem["datecheck"]))/3600)));
				$LLOSS=((((double)$PerItem["lossvalue"])));
				$NLEAK = $time_span * $LLOSS;
				$ALLLEAK=$NLEAK+$PERLEAK;
				$PERLEAK= $ALLLEAK;
				$UAR["AllLeak"]=$ALLLEAK;

				$ures = $ANDAO2->where("id=".$item["id"])->data($UAR)->save();
			}
			
			
			
		}
	}
	public function menu(){
		if(cookie("userstatus")=="admin"){
			$this->menu_add="<li><a onclick=\"gourl('/home/Systemset/usermanager?page=1')\">".L("L_AU_title")."</a></li>";
		}else{
			$this->menu_add="";
		}
		$this->display();
	}
	public function main(){
		$this->display();
	}
	public function top(){
		$this->CMPNA =cookie("companyname");
		$this->display();
	}
	public  function  Loginout(){
		cookie("companyloginame",null);
		cookie("companyid",null);
		cookie("companyname",null);
		cookie("userstatus",null);
	}
	
	public function GetWorkTime(){
		$comid=$_GET["comid"];
		$data_str = $_GET["data"];
		$mcuid= $_GET["mcuid"]; 
		$gd = $_GET["gd"];
		if($data_str==""){
			if($gd==""){
				if($comid==""){
					echo "1*".date('Y-m-d H:i:s');
					exit;
				}
				$ComModel=M("companyinfo");
				$ComModel_DAO=$ComModel->where("Id=".$comid)->find();
				if($ComModel_DAO){
					echo $ComModel_DAO["accesstimespan"]."*".date('Y-m-d H:i:s');
				}else{
					echo "1*".date('Y-m-d H:i:s');
				}
			}else{
				//echo $gd;
				$res_hx=$this->GetHX($comid);
				echo $res_hx;
			}
		}else{
			//echo $data_str;
			$res = $this->GetData($data_str,$comid);
			echo $res;
		}
	}
	///获取是否唤醒TC
	public function GetHX($CID){
		$res="gdqd";//默认启动
		//查询数据库判断是否启动
		$Cominfo = M("companyinfo");
		$comData = $Cominfo->where("id=$CID")->find();
		$AccessTime=$comData["accesstime"];
		$AccessTimeSpan=$comData["accesstimespan"];
		
		$SPANTIME = date('Y-m-d H:i:s',strtotime("$AccessTime + $AccessTimeSpan min"));
		$curTime=date("Y-m-d H:i:s");
		
		if($SPANTIME<=$curTime){
			$res="gdqd";
			$TrapModel = M("trapmodel");
			$AllTC_count = $TrapModel->where("CompanyID=$CID")->count();
			$AllTC_count_str = (string)$AllTC_count;
			while(strlen($AllTC_count_str)<4){
				$AllTC_count_str="0".$AllTC_count_str;
			}
			$res.=$AllTC_count_str; //TC数量 四位
			$AccessTimeSpan_str = (string)$AccessTimeSpan;
			while(strlen($AccessTimeSpan_str)<4){
				$AccessTimeSpan_str="0".$AccessTimeSpan_str;
			}
			$res.=$AccessTimeSpan_str; //TC工作间隔 四位
			$res.=date("Y-m-d-w-H-i-s");; //当前时间
			
			$dataarray["AccessTime"]=$curTime;
			$res_update = $Cominfo->where("id=$CID")->data($dataarray)->save();
		}else{
			$res="gdde";
		}
		return $res;
	}
	//数据处理并存入数据库
	public function GetData($GDData,$comid){
		if(preg_match("/TPCMSCD.+ender/",$GDData)){
			//echo("数据验证完成");
			//数据分解插入数据库
			$ReGDData = str_replace("ender","",str_replace("TPCMSCDTPCMSSD","TPCMSSD",$GDData));
			$dataArray = split(",",$ReGDData);
			$All_data="";
			foreach($dataArray as $dataitem){
				$All_data .= $dataitem.",";
			}
			$All_data = rtrim($All_data,",")."|".date("Y-m-d H:i:s")."/";
			$All_data_array["data"]=$All_data;
			//$insertdata_res = $this->request_post("http://127.0.0.1/home/API/InsertData?comid="+$comid,$All_data_array);
			$insertdata_res = $this->send_post("http://127.0.0.1/home/API/InsertData?comid="+$comid,$All_data_array);
			$myfile = fopen("newfile.txt", "w+");
			fwrite($myfile, $insertdata_res."******".$All_data);
			fclose($myfile);
			return $insertdata_res;
		}else{
			//echo("数据验证失败");
			return "9";
		}
	}
	public function send_post($url, $post_data) {
		$postdata = http_build_query($post_data);
		$options = array(
		'http' => array(
		  'method' => 'POST',
		  'header' => 'Content-type:application/x-www-form-urlencoded',
		  'content' => $postdata,
		  'timeout' => 15 * 60 // 超时时间（单位:s）
		)
		);
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		return $result;
	}

	 /**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param string $param
	 */
	public function request_post($url = '', $param = '') {
		if (empty($url) || empty($param)) {
			return false;
		}
		
		$postUrl = $url;
		$curlPost = $param;
		$ch = curl_init();//初始化curl
		curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
		curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$data = curl_exec($ch);//运行curl
		curl_close($ch);
		
		return $data;
	}
	
	public function welcome(){
		$this->display();
	}

	public function logindata(){
		$Model_CompanyInfo_Dao = M("companyinfo");
		$LN=$_POST["name"];
		$PWD=$_POST["pwd"];
		if($LN=="" || $PWD==""){
			echo "-1";
			exit;
		}
		//."'and state=1"
		$Model_CompanyInfo=$Model_CompanyInfo_Dao->where("LoginName='".$LN."' and LoginPWD='".strtolower(md5($PWD))."'")->find();
		if($Model_CompanyInfo!=null && $Model_CompanyInfo["id"]>0){
			if($Model_CompanyInfo['state']==1){
				cookie("companyid",$Model_CompanyInfo["id"]);
				cookie("companyname",$Model_CompanyInfo["companyname"]);
				cookie("companyloginame",$Model_CompanyInfo["loginname"]);
				cookie("userstatus","admin");
				echo "1";
			}else if($Model_CompanyInfo['state']==0){
				echo "-2";
			}

		}else{
			$Model_subuser=M("subuser");
			$Model_userinfo=$Model_subuser->where("LoginName='".$LN."' and PassWord='".strtolower(md5($PWD))."'")->find();
			if($Model_userinfo!=null && $Model_userinfo["id"]>0){
				if($Model_userinfo["status"]==0){
					echo "-2";
					exit;
				}
				cookie("companyid",$Model_userinfo["companyid"]);
				cookie("companyname",$Model_userinfo["companyname"]);
				cookie("companyloginame",$Model_userinfo["loginname"]);
				cookie("userstatus","user");
				$logintime=date("Y-m-d H:i:s");
				$change_data["LastLoginTime"]=$logintime;
				$result_=$Model_subuser->where("Id=".$Model_userinfo["id"])->save($change_data);
				echo "1";
			}else{
				echo "0";
			}
			//	echo "0";
		}
	}
}
?>