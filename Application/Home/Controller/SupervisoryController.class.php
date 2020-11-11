<?php
namespace Home\Controller;
use Think\Controller;
///首页
class SupervisoryController extends Controller {
	public function index(){
		//判断请求类型
		if($_GET["t"]=="line"){
			$AC = $_GET["areacode"];
			if($AC!=""){
				$AC="?areacode=".$AC;
			}
			$TI=$_GET["trapid"];

			if($TI!=""){
				$TRAPMDAO=M("trapmodel");
				$TRAP_MODEL_ = $TRAPMDAO->where("id=".$TI)->find();
				$TI=$TRAP_MODEL_["trapno"];
				if($AC!=""){
					$AC.="&trapid=".$TI;
				}else{
					$AC="?trapid=".$TI;
				}
			}
			redirect("/home/supervisory/superline".$AC);
			exit;
		}else if($_GET["t"]=="list1"){
			
			redirect("/home/supervisory/bill");
		}
		$show_list_str=$this->ListShow();
		$this->tab_list=$show_list_str;
		$this->display();
	}
	//输出平面展示图
	public function showmax2d(){
    if(cookie("companyid").""==""){
      if($_GET["oid"]!=""){
        cookie("companyid",$_GET["oid"]);
      }
    }
		$nodes_str="";//最后输出的nodes字符串
		$edges_str="";//最后输出的edges字符串

		$AreaDAO=M("areainfo");
		$TrapDAO=M("trapmodel");
		$TrapInfoDAO=M("trapinfo");
		$X_=0;//当前节点的X位置 越小越靠左
		$Y_=0;//当前节点的Y位置 越小越靠上
		$X_MAX=0;//目前到达的最大X值
		$Y_MAX=0;//目前到达的最大Y值

		$X_START=0;//边框的X起始位置；
		$Y_START=0;//边框的Y起始位置；

		$AreaList=$AreaDAO->where("CompanyID=".cookie("companyid"))->order("AreaLocation")->select();
		$LOCATION_L_M_R="";//当前区域的位置

		$LAY_IF=false;//是否输出
		$AREA_COUNT_=0;//区域的数量
		$ALL_AREACOUNT= count($AreaList);//所有区域的数量
		$FOR_INDEX=0;
		$MIN_Y_V=0;
		foreach ($AreaList as $areamodel) {
			$FOR_INDEX++;
			$Trap_Area_List = $TrapDAO->where("CompanyID=".cookie("companyid")." and AreaId=".$areamodel["id"])->order("OrderNum")->select();
			$LJ_NODE_1="";
			$LJ_NODE_2="";
			$All_trap_count = count($Trap_Area_List);
			//计算节点分成两列的位置
			$BR_NUM = (int)((int)$All_trap_count/2);
			$TRAP_FLAG=0;
			$Y_A_P_FLAG=false;
			$A_1="1";
			$A_2="2";
			if($LOCATION_L_M_R==""){
				$LOCATION_L_M_R=substr($areamodel["arealocation"],0,1);
			}

			//绘制标题
			$nodes_str.='{id: "title-'.$FOR_INDEX.'",
										label: "'.L("L_AREA_QY").':'.$areamodel["areaname"].'",
										title: "'.$areamodel["id"].'",
										x:'.((int)$X_+380).',
										y:'.((int)$Y_-100).',
										group:10,
										color:"blue",
										fontColor:"#fff",
										shape:"box",},';

			foreach ($Trap_Area_List as $trap_area) {
				$LAY_IF=true;
				if($All_trap_count>2&&$TRAP_FLAG==$BR_NUM){
					$Y_A_P_FLAG=true;
					$X_+=450;
					$A_1="2";
					$A_2="1";
					$Y_-=100;
				}
				if($Y_<$MIN_Y_V){
					$MIN_Y_V=$Y_;
				}
				$TRAP_FLAG++;
				$GRAP_=1;
				$COLOR_="green";

				$Last_DATA = $TrapInfoDAO->where("companyid=".cookie("companyid")." and trapno='".$trap_area["trapno"]."' and AreaId=".$trap_area["areaid"])->order("id desc")->find();
				$TITLE_STR="".L("L_ALERT_DATAEMPTY");
				if($Last_DATA!=null && $Last_DATA["id"]>0){
					$TITLE_STR=L("L_ALERT_TJ_WENDU")."：".$Last_DATA["newtem"]."<br/>".L("L_ALERT_TJ_PINLV")."：".$Last_DATA["hznumber"]."<br/>".L("L_LIST_SHOW_LASTTIME").":".date("m-d H:i:s",strtotime($Last_DATA["datecheck"]));
				}

				$nodes_str.='{id: "'.$trap_area["id"].'-'.$A_1.'",
				        			label: "",
				        			x:'.$X_.',
				        			y:'.$Y_.',
				        			group:0,
				        			color:"gray",
				        			fontColor:"#fff",
				        			shape:"dot"},
											{id: "'.$trap_area["id"].'",
				        			label: "'.$trap_area["trapno"].'",
											title:"'.$TITLE_STR.'",
				        			x:'.((int)$X_+150).',
				        			y:'.$Y_.',
				        			group:'.$GRAP_.',
				        			color:"'.$COLOR_.'",
				        			fontColor:"#fff",
				        			shape:"box"},
											{id: "'.$trap_area["id"].'-'.$A_2.'",
				        			 label: "",
											 x:'.((int)$X_+300).',
											 y:'.$Y_.',
											 group:0,
											 color:"gray",
											 fontColor:"#fff",
											 shape:"dot"},';

				$F_T1="";
				$F_T2="";
				if($LJ_NODE_1!=""){
					$F_T1='{from:"'.$LJ_NODE_1.'",to:"'.$trap_area["id"].'-1"},';
					$LJ_NODE_1="";
				}else{
					$LJ_NODE_1=$trap_area["id"]."-1";
				}
				if($LJ_NODE_2==""){
					$LJ_NODE_2=$trap_area["id"]."-2";
				}else{
					$F_T2='{from:"'.$LJ_NODE_2.'",to:"'.$trap_area["id"].'-2"},';
					$LJ_NODE_2=$trap_area["id"]."-2";
				}
				$edges_str.='{
	        						from: "'.$trap_area["id"].'-1",
	        						to: "'.$trap_area["id"].'",
										},
										{
							        from: "'.$trap_area["id"].'",
							        to: "'.$trap_area["id"].'-2",
										},'.$F_T1.$F_T2;


				//$X_+=100;
				if($Y_A_P_FLAG){
					$Y_-=100;
				}else{
					$Y_+=100;
				}
				if($X_>$X_MAX){
					$X_MAX=$X_;
				}
				if($Y_>$Y_MAX){
					$Y_MAX=$Y_;
				}
			}


/*
				//绘制边框
				$PADDING_PX=20;
				$nodes_str.='{id: "border-'.$FOR_INDEX.'-1",
											label: "",
											x:'.((int)$X_START-(int)$PADDING_PX).',
											y:'.((int)$MIN_Y_V-(int)$PADDING_PX).',
											group:10,
											color:"#ccc",
											shape:"dot"},';
				$nodes_str.='{id: "border-'.$FOR_INDEX.'-2",
											label: "",
											x:'.((int)$X_START-(int)$PADDING_PX).',
											y:'.((int)$Y_MAX+(int)$PADDING_PX).',
											group:10,
											color:"#ccc",
											shape:"dot"},';
				$nodes_str.='{id: "border-'.$FOR_INDEX.'-4",
											 label: "",
											 x:'.((int)$X_MAX+(int)$PADDING_PX).',
											 y:'.((int)$MIN_Y_V-(int)$PADDING_PX).',
											 group:10,
											 color:"#ccc",
											 shape:"dot"},';
				 $nodes_str.='{id: "border-'.$FOR_INDEX.'-3",
 											 label: "",
 											 x:'.((int)$X_MAX+(int)$PADDING_PX).',
 											 y:'.((int)$Y_MAX+(int)$PADDING_PX).',
 											 group:10,
 											 color:"#ccc",
 											 shape:"dot"},';

				 $edges_str.='{
	 	        						from: "border-'.$FOR_INDEX.'-1",
	 	        						to: "border-'.$FOR_INDEX.'-2",
												style:"dash-line",
	 										},
	 										{
												from: "border-'.$FOR_INDEX.'-2",
	 	        						to: "border-'.$FOR_INDEX.'-3",
												style:"dash-line",
	 										},
											{
												from: "border-'.$FOR_INDEX.'-3",
	 	        						to: "border-'.$FOR_INDEX.'-4",
												style:"dash-line",
	 										},
											{
												from: "border-'.$FOR_INDEX.'-4",
	 	        						to: "border-'.$FOR_INDEX.'-1",
												style:"dash-line",
	 										},';

			$X_START=$X_MAX;
			$Y_START=$Y_MAX;*/
			if(((int)$AREA_COUNT_+1)<$ALL_AREACOUNT){
				if(substr($AreaList[$AREA_COUNT_+1]["arealocation"],0,1)!=substr($areamodel["arealocation"],0,1)){
					if($LAY_IF){
						$X_+=950;
						$Y_=0;
					}
					$LOCATION_L_M_R=substr($areamodel["arealocation"],0,1);
				}else if(substr($AreaList[$AREA_COUNT_+1]["arealocation"],0,1)==substr($areamodel["arealocation"],0,1)){
					if($LAY_IF){
						$Y_=$Y_MAX+100;
					}
					if($Y_A_P_FLAG){
						$X_-=450;
					}
				}
			}
			$AREA_COUNT_++;
		}
		$this->NODES=rtrim($nodes_str,",");
		$this->EDGES=rtrim($edges_str,",");
		$this->display();
	}
	public function superline(){

		if(cookie("companyid").""==""){
			if($_GET["oid"]!=""){
				cookie("companyid",$_GET["oid"]);
			}
		}
		$this->display();
	}
	//绘制单个折线图
	public function drawline_search(){
		$sjjg = $_GET["sjjg"];
		$areaid=$_GET["area"];
		$trap=$_GET["trap"];
		$state=$_GET["state"];
		$where_str="CompanyID=".cookie("companyid");
		$title_text="";
		if($areaid!=""){
			$where_str.=" and AreaId=".$areaid;
		}
		if($trap!=""){
			$where_str.=" and TrapNo like '%".$trap."%'";
		}
		if($state!="" && $state!="0"){
			$where_str.=" and TrapState = '".$state."'";
		}
		$TIME_STR=" and DateCheck >='".date("Y-m-d")." 00:00:00' and DateCheck <= '".date("Y-m-d")." 23:60:99' ";
		if($sjjg=="d"){
			$TIME_STR=" and DateCheck >='".date("Y-m-d")." 00:00:00' and DateCheck <= '".date("Y-m-d")." 23:60:99' ";
		}else if($sjjg=="m") {
			$TIME_STR=" and DateCheck >= '".date("Y-m")."-01 00:00:00' and DateCheck <= '".date("Y-m")."-30 23:60:99' ";
		}
		else if($sjjg=="y") {
			$TIME_STR=" and DateCheck >='".date("Y",strtotime("-1 year"))."-01-01 00:00:00' and DateCheck <='".date("Y")."-12-30 23:60:99' ";
		}else {
			$TIME_STR = "";
		}
		//echo $where_str."*".$TIME_STR;
		$DAO_Trap=M("trapinfo");

		$DAO_OP = M();
		$WHERE_CHECK_=$this->CheckRoleInfo_GET(cookie("companyid"));
		$LIST_All_TRAP = $DAO_OP->query("select id,companyid,areaid,area,trapname,trapno,newtem,DATE_FORMAT(datecheck,'%Y-%m-%d %H:%i') dc from trapinfo where ".$where_str.$TIME_STR.$WHERE_CHECK_." GROUP by dc order by dc");
		$sqlstr="select trapno,areaid from trapinfo where ".$where_str.$TIME_STR.$WHERE_CHECK_." group by trapno";
		$LIST_AREA_ = $DAO_OP->query($sqlstr);

		//$List_Line_search = $DAO_Trap->where($where_str.$TIME_STR)->order("DateCheck desc")->group("TrapNo")->select();
		//$DateCheck_STR_LIST = $DAO_Trap->where($where_str.$TIME_STR)->order("DateCheck")->group("DateCheck")->select();

		$res_echo.="myChart = echarts.init(document.getElementById('div_show_line'),'infographic');";
		$res_echo.="var option = {title: {text: ''},tooltip: {},legend:{data:['".L("L_ALERT_TJ_WENDU")."']},";
		$trap_time="";
		$trap_data_="";
		$flag_foreach="0";
		$MAX_NODE_TRP_TIME="";

		$TIME_ALL_ = "";
		$DATE_ALL_Array_OLD=array();
		foreach ($LIST_AREA_ as $TrapInfo) {
			$T_INFO="";
			$trap_time="";
			$HavaTEM = "0";
			foreach ($LIST_All_TRAP as $Trap_Info) {
				$trap_time.='"'.$Trap_Info["dc"].'",';
				//$DATE_ALL_Array_OLD[$TrapInfo['areaid']."-".$TrapInfo["dc"]]=$TrapInfo["newtem"];
				if($TrapInfo["trapno"]==$Trap_Info["trapno"]){
					$T_INFO.='"'.$Trap_Info["newtem"].'",';
					$HavaTEM = $Trap_Info["newtem"];
				}else{
					$HavaTEM = ''.(((int)$HavaTEM)-0.5);
					if((double)$HavaTEM<0){
						$HavaTEM = "0";
					}
					$T_INFO.='"'.$HavaTEM.'",';

				}

			}
			$trap_data_.='{"name": "'.$TrapInfo["trapno"].'","type": "line","data":['.rtrim($T_INFO,",").']},';
		}
		echo '{"category":['.rtrim($trap_time,",").'],"series":['.rtrim($trap_data_,",").']}';
	}
	/*是否符合规则的判断*/
	public function CheckRoleInfo_GET($opCompanyId)
	{
		$DAO_ROLE=M("roleadmin");
		$LIST_DAO = $DAO_ROLE->where("(CompanyID=0 or CompanyID=".$opCompanyId.")")->select();
		$res_="";
		foreach ($LIST_DAO as $RoleItem) {
			if(((int)$RoleItem["opdesc"])>0){
				$res_ =" and NOW()>=(createtime + INTERVAL ".$RoleItem["opdesc"]." MINUTE)";
			}
		}
		return $res_;
	}
	//绘制多个区域的折线图
	public function drawline(){
		$DAO_Trap=M("trapinfo");
		$draw_num=$_GET["dn"];
		$res_echo="";
		if($draw_num==""){
			$draw_num="5";
		}
		$List_Line_area = $DAO_Trap->where("CompanyID=".cookie("companyid"))->order("DateCheck desc")->group("areaid")->limit((int)$draw_num)->select();
		$A_A_count = count($List_Line_area);
		for ($i=0; $i <$A_A_count ; $i++) {
			$List_Line_trap = $DAO_Trap->where("CompanyID=".cookie("companyid")." and TrapNo='".$List_Line_area[$i]["trapno"]."'")->order("DateCheck desc")->limit(10)->select();
			$res_echo.="myChart_".$i." = echarts.init(document.getElementById('div_show_line_".$i."'),'infographic');";
			$data_title=L("L_ALERT_TJ_WENDU");
			if($i>0){$data_title="";}
			$res_echo.="var option_".$i." = {title: {text: '".L("L_AREA_QY")."".$List_Line_area[$i]["area"]."'},tooltip: {},legend: {data:['".$data_title."']},";
			$trap_a_count=count($List_Line_trap);
			$trap_info="";
			$trap_time="";
			for ($j=$trap_a_count-1; $j >=0 ; $j--) {
				if(($i+1)%2==0){
					$trap_time.= "'".date("H:i",strtotime($List_Line_trap[$j]["datecheck"]))."',";
				}else{
					$trap_time.= "'',";
				}
				$NT_SHOW = $List_Line_trap[$j]["newtem"];

				$trap_info.=$NT_SHOW.",";
			}
			$res_echo.="xAxis: {data: [".rtrim($trap_time,",")."]},yAxis: {type:'value',axisLabel: { formatter:'{value} ℃'},splitNumber:2},series: [{name: '".$List_Line_area[$i]["trapno"]."',type: 'line',data: [".rtrim($trap_info,",")."]}]};myChart_".$i.".setOption(option_".$i.");";
		}
		echo $res_echo;
	}
	//详细信息展示
	Public function checkinfo()
	{
		$Page_Cur=$_GET["p"];
		$PageSize=20;
		if($Page_Cur==''){
			$Page_Cur=1;
		}

		$PageS=($Page_Cur-1)*$PageSize;
		$CID = cookie("companyid");
		if($CID==""){
			$CID = $_GET["cid"];
			cookie("companyid",$CID);
		}
		if ($CID=="") {
			exit;
		}
		$DAO_Trap=M("trapinfo");//."' and TrapState='".$_GET["trapstate"].
		$List_ALL = $DAO_Trap->where("CompanyID=".$CID." and TrapNo='".$_GET["tid"]."'")->order("DateCheck desc")->limit($PageS,$PageSize)->select();
		$AllDataCount = $DAO_Trap->where("CompanyID=".$CID." and TrapNo='".$_GET["tid"]."'")->order("DateCheck desc")->count();

		$AllPageCount=(int)$AllDataCount/$PageSize;
		if((int)$AllDataCount%$PageSize!=0){
			(int)$AllPageCount=(int)$AllPageCount+1;
		}
		if((int)$AllPageCount==0){
			$AllPageCount=1;
		}

		$show_list_str="";
		foreach ($List_ALL as $trap_each) {
			$show_list_str.="<tr>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapno"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapname"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["area"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["location"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");//1为异常  0为正常
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=date("m-d H:i",strtotime($trap_each["datecheck"]));
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["traptype"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["usemtfi"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["spressure"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["linesize"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["linktype"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["outtype"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["newtem"];
			$show_list_str.="</td>";
			$show_list_str.="</tr>";
		}

		$this->pc=$Page_Cur;
		$this->apc=$AllPageCount;
		$this->pagedata=L("L_ALERT_PAGE_DI")."".$Page_Cur."/".$AllPageCount."".L("L_ALERT_PAGE_YE")."   ".L("L_ALERT_PAGE_GONG")."".$AllDataCount."".L("L_ALERT_PAGE_SHUJU");//".L("L_ALERT_PAGE_TIAO")."
		$this->tab_list=$show_list_str;
		$this->display();
	}
	///报警信息展示
	Public function warninginfo()
	{
		$Page_Cur=$_GET["p"];
		$PageSize=20;
		if($Page_Cur==''){
			$Page_Cur=1;
		}
		$PageS=($Page_Cur-1)*$PageSize;

		$DAO_Trap=M("warning");
		$List_ALL = $DAO_Trap->where("CompanyID=".cookie("companyid")." and TrapNo='".$_GET["tid"]."'")->order("RepairTime desc")->limit($PageS,$PageSize)->select();
		$AllDataCount = $DAO_Trap->where("CompanyID=".cookie("companyid")." and TrapNo='".$_GET["tid"]."'")->order("RepairTime desc")->count();

		$AllPageCount=(int)$AllDataCount/$PageSize;
		if((int)$AllDataCount%$PageSize!=0){
			(int)$AllPageCount=(int)$AllPageCount+1;
		}
		if((int)$AllPageCount==0){
			$AllPageCount=1;
		}

		$show_list_str="";
		foreach ($List_ALL as $trap_each) {
			$show_list_str.="<tr>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["area"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapno"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["location"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["repairstate"]=="1"?L("L_ALERT_HANDLED"):L("L_ALERT_HANDLING");//1为已处理  0为未处理
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["repairtype"]=="1"?L("L_ALERT_REPAIR"):L("L_ALERT_REPLACE");//1为维修0更换
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=date("Y-m-d",strtotime($trap_each["repairtime"]));
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["repairnum"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["repairprice"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");//1为异常0正常
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["exlevel"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["leveldesc"];
			$show_list_str.="</td>";/*
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["alerthz"];
			$show_list_str.="</td>";*/
			$str_re=$trap_each["repairdescription"];
			$show_list_str.="<td><a style='cursor:pointer;' onclick= \"show_repairinfo('".$str_re."');\">";
			$show_list_str.=strlen($str_re)>5?mb_substr($str_re,0,5,"utf-8").'...':$str_re;
			$show_list_str.="</a></td>";
			$show_list_str.="</tr>";
		}

		$this->pc=$Page_Cur;
		$this->apc=$AllPageCount;
		$this->pagedata=L("L_ALERT_PAGE_DI")."".$Page_Cur."/".$AllPageCount."".L("L_ALERT_PAGE_YE")."   ".L("L_ALERT_PAGE_GONG")."".$AllDataCount.L("L_ALERT_PAGE_SHUJU");
		$this->tab_list=$show_list_str;
		$this->display();
	}

	public function search(){
		echo $this->ListShow();
	}
	// ----------------------------------
	
	public function bill(){
		$show_list_str=$this->ListShow1();
		$this->tab_list=$show_list_str;
	    $this->display();
	}
	public function ListShow1(){
		$DAO_Trap=M("trapmodel");
		$show_list_str="";
		$area_where=$_GET["area"];
		$ts_where=$_GET["ts"];
		$te_where=$_GET["te"];
		$tn_where=$_GET["tn"];
		$tstate_where=$_GET["tstate"];

		$where_str="CompanyID=".cookie("companyid")." ";
		if($area_where!=""){
			$where_str.=" and AreaID = '".$area_where."' ";
		}
		if($ts_where!=""){
			$where_str.=" and DateCheck>='".$ts_where."' ";
		}
		if($te_where!=""){
			$where_str.=" and DateCheck<='".$te_where."' ";
		}
		if($tn_where!=""){
			$where_str.=" and TrapNo like '%".$tn_where."%' ";
		}
		if($tstate_where!=""){ //&& $tstate_where!="0"
			$where_str.=" and TrapState='".$tstate_where."' ";
		}
		
		$List_ALL = $DAO_Trap->where($where_str)->order("areaid,trapno")->group("TrapNo")->select();
		foreach ($List_ALL as $trap_each) {
			$show_list_str.="<tr>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["area"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["modelname"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["spressure"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			// $show_list_str.=$trap_each["linktype"];
			$show_list_str.=$trap_each["linktype"]=="0"?L("L_AREA_SFO"):L("L_AREA_FLANGE");//1为法兰  0为螺丝
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["linesize"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["location"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["traptype"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["usemtfi"];
			
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			// $show_list_str.=$trap_each["outtype"];
			$show_list_str.=$trap_each["outtype"]=="0"?L("L_AREA_down"):L("L_AREA_open");//1为开式  0为闭式
			$show_list_str.="</td>";
			$show_list_str.="<td>";//,'".$trap_each["trapstate"]."'
			$show_list_str.="<a onclick=\"edit('".$trap_each["id"]."','".$trap_each["trapno"]."');\" style='cursor:pointer;'>".L("L_AREA_BJJD")."</a>";
			$show_list_str.="</td>";
			$show_list_str.="</tr>";
			
		}
		return $show_list_str;
		
	}


	// -----------------------------------
	//获取区域
	public function getareas(){
		$DAO_Area=M("areainfo");
		$where_str="CompanyID=".cookie("companyid");
		$List_area = $DAO_Area->where($where_str)->select();
		$areas="";
		foreach ($List_area as $area_) {
			$areas.=$area_["id"].",".$area_["areaname"]."/";
		}
		echo rtrim($areas,",");
	}
//编辑---------------------------------------------------------------------------------------
	public  function  edit(){
		$Dao=M("trapmodel");
		$result=$Dao->where('id='.$_GET['id'])->find();
	//   $data['']==0?"正常":"异常";
		$this->trapdata=$result;
		$this->display();
		}

		public  function  update(){
			$Dao=M("trapmodel");
			$id['Id']=$_POST['id'];
			$data['ModelName']=$_POST['modelname'];
			$data['Area']=$_POST['area'];
			$data['SPressure']=$_POST['spressure'];
			$data['LinkType']=$_POST['linktype'];
			$data['LineSize']=$_POST['linesize'];
			$data['location']=$_POST['location'];
			$data['TrapType']=$_POST['traptype'];
			$data['UseMTFI']=$_POST['usemtfi'];
			$data['OutType']=$_POST['outtype'];

			$result=$Dao->where($id)->save($data);
			if($result){
			  echo "ok";
			}
			else{
			  echo  "err";
			}
		  }

//-----------------------------------------------------------------------------

	//列表展示
	public function ListShow(){
		$DAO_Trap=M("trapinfo");
		$show_list_str="";
		$area_where=$_GET["area"];
		$ts_where=$_GET["ts"];
		$te_where=$_GET["te"];
		$tn_where=$_GET["tn"];
		$tstate_where=$_GET["tstate"];

		$where_str="CompanyID=".cookie("companyid")." ";
		if($area_where!=""){
			$where_str.=" and AreaID = '".$area_where."' ";
		}
		if($ts_where!=""){
			$where_str.=" and DateCheck>='".$ts_where."' ";
		}
		if($te_where!=""){
			$where_str.=" and DateCheck<='".$te_where."' ";
		}
		if($tn_where!=""){
			$where_str.=" and TrapNo like '%".$tn_where."%' ";
		}
		if($tstate_where!=""){ //&& $tstate_where!="0"
			$where_str.=" and TrapState='".$tstate_where."' ";
		}
		
		
		$List_ALL = $DAO_Trap->where($where_str)->order("DateCheck desc,areaid asc,trapno asc")->group("TrapNo")->select();
		foreach ($List_ALL as $trap_each) {
			$show_list_str.="<tr>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapno"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapname"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["area"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["trapstate"]=="1"?L("L_ALERT_TJ_EXC"):L("L_ALERT_TJ_WORK");//1为异常  0为正常
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=date("Y-m-d H:i",strtotime($trap_each["datecheck"]));
			$show_list_str.="</td>";
			$show_list_str.="<td>";
			$show_list_str.=$trap_each["description"];
			$show_list_str.="</td>";
			$show_list_str.="<td>";//,'".$trap_each["trapstate"]."'
			$show_list_str.="<a onclick=\"showinfo('".$trap_each["id"]."','".$trap_each["trapno"]."');\" style='cursor:pointer;'>".L("L_ALERT_TJ_XIANGXI")."</a>";
			$show_list_str.="</td>";
			$show_list_str.="</tr>";
			
		}
		return $show_list_str;
		
	}
}
