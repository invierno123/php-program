<?php
namespace Home\Controller;
use Think\Controller;
///首页
class RealTimeController extends Controller {
	public function index(){
		$this->display();
	}
	public function dzlist(){
		$PageIndex = $_GET["pageindex"];
		$limit_s = 0;
		$limit_e = 50;
		if($PageIndex!=""){
			$limit_s = (((int)$PageIndex)-1)*$limit_e;
		}
		if(cookie("companyid")==null){
			redirect("/home/index/index?open=".$_SERVER["REQUEST_URI"]);
			exit;
		}
		$TrapINFO = M("trapinfo");
		$TrapModelDAO = M("trapmodel");
		$Where_STR="1=1 and companyid=".cookie("companyid");
		if($_GET["open"].""!=""){
			$Where_STR.=" and TrapNo='".$_GET["open"]."'";
		}
		$ALL_Array = $TrapINFO->where($Where_STR)->order("datecheck desc")->limit($limit_s,$limit_e)->select();
		$result="";
		$num=0;
		foreach ($ALL_Array as $key) {
			$tm_info = $TrapModelDAO->where("trapNo='".$key['trapno']."'")->find();
			$num++;

			$CSARRAY=split(",",$key['setoption']);
			$BFB = $tm_info['revealper'];
			$DQFZ=$tm_info['criticalvalue'];
			if($key['setoption']!=""){
				$BFB = $CSARRAY[5];
				$DQFZ = $CSARRAY[1];
			}


			$STSTR="正常";
			if((int)$key['trapstate']>(int)$BFB){
				$STSTR="异常";
				//<td>".$STSTR."</td><td>".$key['lossamountyear']."</td>
			}

			$FZXL = ((int)((((double)$key['hznumberpj']-(double)$key['hznumberdz'])/((double)$key['hznumber']-(double)$key['hznumberdz']))*100));
			$FZXLFZ = ((int)((((double)$key['hznumberpj']-(double)$DQFZ)/((double)$key['hznumber']-(double)$DQFZ))*100));
			if($FZXLFZ>=100 || $FZXLFZ<0){$FZXLFZ=0;}
			$XL1=$this->OutLevel((((double)$key['trapstate']*$tm_info["maxlk"])/100),$tm_info["spressure"]);
			$XL2=$this->OutLevel((((double)$FZXL*$tm_info["maxlk"])/100),$tm_info["spressure"]);
			$XL3=$this->OutLevel((((double)$FZXLFZ*$tm_info["maxlk"])/100),$tm_info["spressure"]);


			$title_ = "放大数:$CSARRAY[0]\n阀  值:$CSARRAY[1]\n采集数:$CSARRAY[2]\n最小频:$CSARRAY[3]\n最大频:$CSARRAY[4]\n百分比:$CSARRAY[5]";

			$STA_PA = ((double)$key['trapstate']-(double)$BFB)/(100.00-(double)$BFB);
			$result.="<tr title='$title_'>
			<td rowspan='4' name='td_r4' title='序号'>".$num."</td>
			<td rowspan='4' name='td_r4' title='节点名称'>".$key['trapname']."</td>
			<td rowspan='4' name='td_r4' title='TC号'>".$key['trapno']."</td>

			<td style='background:#cdd15f;' title='温度'>".$key['newtem']." ℃</td>
			<td style='display:block; background:#cdd15f;' title='最大幅值'>".$key['hznumber']." mv</td>
			<td style='display:block; background:#cdd15f;' title='最小幅值'>".$key['hznumberdz']." mv</td>
			<td style='display:block; background:#cdd15f;' title='平均幅值'>".$key['hznumberpj']." mv</td>
			<td style='display:block; background:#cdd15f;' title='最大频率'>".$key['hznumbermax']." _版本</td>
			<td style='display:block; background:#cdd15f;' title='最小频率'>".$key['hznumbermin']." V</td>
			<td style='display:block; background:#cdd15f;' title='平均频率'>".$key['hznumberavg']." Hz</td>


			<td style='display:block; background:#cdd15f;' title='幅值泄漏等级'>".$key["exlevel"]." 级</td>

			<td style='display:block; background:#cdd15f;' title='电池电量'>".$key['battery']." %</td>
			<td style='background:#cdd15f;' title='获取时间'>".date("Y-m-d H:i:s", strtotime($key['datecheck']))."</td>

			</tr>
			<tr title='' style='cursor:pointer;' name='tr_info_1' onclick='tr_click_show(this)'>
			<td style='background:#48b8c2;'>疏水阀泄漏判断(泄漏率计算法)</td>
			<td style='background:#48b8c2;' title='泄漏率-泄露比例'>".$key['trapstate']."%</td>
			<td style='background:#48b8c2;' title='泄漏率-泄露等级'>".(((int)($STA_PA*10))<0?0:((int)($STA_PA*10)))."级</td>
			<td style='background:#48b8c2;' title='泄漏率-泄露量/年'>".($XL1)." kg</td>

			<td rowspan='3' style='background:#8cb76d;' title='放大倍数'><p>放大倍数</p>".($CSARRAY[0])." 倍</td>
			<td rowspan='3' style='background:#8cb76d;' title='阀值'><p>阀值</p>".($CSARRAY[1])." 毫伏</td>
			<td rowspan='3' style='background:#8cb76d;' title='采集次数'><p>采集次数</p>".($CSARRAY[2])." 次</td>
			<td rowspan='3' style='background:#8cb76d;' title='最小频率'><p>最小频率</p>".($CSARRAY[3])." Hz</td>
			<td rowspan='3' style='background:#8cb76d;' title='最大频率'><p>最大频率</p>".($CSARRAY[4])." Hz</td>
			<td rowspan='3' style='background:#8cb76d;' title='泄露百分比'><p>泄露百分比</p>".($CSARRAY[5])." %</td>

			</tr>
			<tr title=''  style='cursor:pointer;' name='tr_info_2' onclick='tr_click_show(this)'>
			<td style='background:#c15492;'>疏水阀泄漏判断(幅值计算法)</td>
			<td style='background:#c15492;' title='幅值-泄露比例'>".$FZXL."%</td>
			<td style='background:#c15492;' title='幅值-泄露等级'>".((int)($FZXL/10))."级</td>
			<td style='background:#c15492;' title='幅值-泄露量/年'>".($XL2)." kg</td>

			</tr>
			<tr title=''  style='cursor:pointer;' name='tr_info_3' onclick='tr_click_show(this)'>
			<td style='background:#c1a954;'>疏水阀泄漏判断(阀值计算法)</td>
			<td style='background:#c1a954;' title='阀值-泄露比例'>".$FZXLFZ."%</td>
			<td style='background:#c1a954;' title='阀值-泄露等级'>".((int)($FZXLFZ/10))."级</td>
			<td style='background:#c1a954;' title='阀值-泄露量/年'>".($XL3)." kg</td>
			</tr>
			<tr><td colspan='13' style='border-style:none;'><hr/></td></tr>
			";
		}

		$REAL_ENDER_DAO=M("trapmodel");;
		$ender_list=$REAL_ENDER_DAO->select();
		$select_html="";
		$DATA_SCRIPT="";
		$CompanyDAO = M("companyinfo");
		$companyinfo = $CompanyDAO->where("id=".cookie("companyid"))->find();
		foreach ($ender_list as $enderkey) {
			$check_="";
			if($_GET["open"].""!="" && $_GET["open"]==$enderkey['trapno']){
				$check_="selected";
				$DATA_SCRIPT = ' <script>function onloaddata() {
				document.getElementById("input_set_dz").value="'.$enderkey["dz"].'";
				document.getElementById("input_set_minpl").value="'.$enderkey["minhz"].'";
				document.getElementById("input_set_maxpl").value="'.$enderkey["maxhz"].'";
				document.getElementById("input_set_fd").value="'.$enderkey["zoommultiple"].'";
				document.getElementById("input_set_fz").value="'.$enderkey["criticalvalue"].'";
				document.getElementById("input_set_cjcount").value="'.$enderkey["collectionnum"].'";
				document.getElementById("input_set_uar").value="'.$enderkey["comswitch"].'";
				document.getElementById("input_set_bfz").value="'.$enderkey["revealper"].'";
				document.getElementById("input_set_mxlk").value="'.$enderkey["maxlk"].'";
				document.getElementById("input_set_gzwdfz").value="'.$enderkey["worktem"].'";
				document.getElementById("input_set_cjjg").value="'.$companyinfo["accesstimespan"].'";
				}</script>';

			}
			$select_html.="<option ".$check_." value='".$enderkey['trapno']."'>".$enderkey['trapname']."</option>";
		}

		echo $DATA_SCRIPT;
		$this->selecthtml=$select_html;
		$this->htmlstr=$result;
		$this->display();
	}

	public function SetDZDATA()
	{
		$REAL_DAO=M("trapmodel");
		$REAL_DAO_M = M();
		$UD["DZ"]=$_GET["dzv"];
		$UD["MinHZ"]=$_GET["minpl"];
		$UD["MaxHZ"]=$_GET["maxpl"];
		$UD["ZoomMultiple"]=$_GET["fd"];
		$UD["CriticalValue"]=$_GET["fz"];
		$UD["CollectionNum"]=$_GET["cjcount"];
		$UD["ComSwitch"]=$_GET["uar"];
		$UD["RevealPer"]=$_GET["bfz"];
		$UD["MaxLK"]=$_GET["xlkj"];
		//$UD["DZTB"]=1;
		//$RES = $REAL_DAO->where("trapNo='".$_GET["opt"]."'")->data($UD)->save();

		$RES = $REAL_DAO_M->execute("update trapmodel set dz='".$_GET["dzv"]."',SetState=1,minhz='".$_GET["minpl"]."',maxhz='".$_GET["maxpl"]."',zoommultiple='".$_GET["fd"]."',criticalvalue='".$_GET["fz"]."',collectionnum='".$_GET["cjcount"]."',comswitch='".$_GET["uar"]."',RevealPer='".$_GET["bfz"]."',MaxLK='".$_GET["xlkj"]."',WorkTEM='".$_GET["gzwd"]."' where  trapNo='".$_GET["opt"]."'");
		$RES_jg = $REAL_DAO_M->execute("update companyinfo set AccessTimeSpan='".$_GET["cjjg"]."' where id=".cookie("companyid"));
		if($RES || $RES_jg){

			echo "<script>alert('参数设置完成');location='/home/RealTime/dzlist';</script>";
		}else{
			//echo "操作失败";
			echo "<script>alert('数据未作出任何改变');location='/home/RealTime/dzlist';</script>";
		}
	}
	public function GetData(){
		$REAL_DAO=M("cachedata","","mysql://root:root@127.0.0.1:3306/cachedb");
		$result = $REAL_DAO->select();
		$Data_='[';
		foreach ($result as $key) {
			$DATA_R = ltrim(str_replace("\n","",str_replace("\r","",$key["data"]))," ");
			$Data_.='{"ID":"'.$key["id"].'","DATA":"'.$DATA_R.'","TIME":"'.$key["createtime"].'","TEM":"'.substr($DATA_R,22,5).'","HZ":"'.substr($DATA_R,28,7).'","ZF":"'.substr($DATA_R,36,4).'"},';
		}
		$Data_ = rtrim($Data_,",");
		$Data_.=']';
		echo $Data_;
	}
	
	public function IntegrationLeak(){
		$CID=$_GET["cid"]."";
		$TrapModelDAO = M("trapmodel");
		$CompanyDao = M("companyinfo");
		if($CID==""){
			//全部企业
			$AllCom = $CompanyDao->select();
			foreach($AllCom as $com){
				$AllTrap = $TrapModelDAO->where("companyid=".$com['id'])->select();
				foreach($AllTrap as $TrapM){
					$this->IntegrationTrap($TrapM['trapno'],$com['id']);
				}
			}
		}else{
			//单个企业
			$AllTrap = $TrapModelDAO->where("companyid=".$CID)->select();
			foreach($AllTrap as $TrapM){
				$this->IntegrationTrap($TrapM['trapno'],$CID);
			}
		}
	}
	public function IntegrationTrap($TrapNO,$CID){
		$AnalysisDao=M("analysis");
		$AnalyList=$AnalysisDao->where("trapno='".$TrapNO."' and companyid=".$CID)->order("datecheck asc")->select();
		$ACount=count($AnalyList)-1;
		if($ACount>0){
			for ($x=0; $x<$ACount; $x++) {
				$A_ = $AnalyList[$x];
				$B_ = $AnalyList[$x+1];
				$_date_HOUR = (double)((strtotime($B_["datecheck"])-strtotime($A_["datecheck"]))/3600);
				if($_date_HOUR>=12){
					//大于12小时无数据默认为停止工作 不做计算
					$_ALL_LEAK_DATA = (double)$A_["allleak"];
					$AnalyList[$x+1]["allleak"]=$_ALL_LEAK_DATA;
					$UDATA["AllLeak"]=$_ALL_LEAK_DATA;
					$res = $AnalysisDao->where("id=".$B_["id"])->save($UDATA);
				}else{
					$_ALL_LEAK_DATA = $_date_HOUR*(double)$A_["lossvalue"] + (double)$A_["allleak"];
					$AnalyList[$x+1]["allleak"]=$_ALL_LEAK_DATA;
					$UDATA["AllLeak"]=$_ALL_LEAK_DATA;
					$res = $AnalysisDao->where("id=".$B_["id"])->save($UDATA);
					//echo("id:".$B_["id"]."*** VALUE:".$_ALL_LEAK_DATA."*****res:".$res."<br/>");
				}
			}
		}
	}


	//泄露量函数
	  public function OutLevel($D,$TrapP){
					$companyid=cookie("companyid");
	        //蒸汽压力 单位bar
	        $ZQYL=(double)$TrapP*10000;//((double)$WD_YL_RES[0]["pressure"])*10;;
	        //冷凝压力 单位bar
	        $ZQLNYL=0.01;
	        //每小时泄露量
	        $HoursLossF=0;
	        $HoursLossR=0;
	        if((double)$ZQLNYL>0){
	          //Fail open
	          //if()
	          $HoursLossF = (((double)$ZQYL*100+101.3)*(double)$D*(double)$D*0.004123*0.55*0.6*1.5845386)*0.5;
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
					//echo "***".$HoursLossF."*****".$YearLoss."-";
	        return $HoursLossF+"*"+$YearLoss;
	  }

}
