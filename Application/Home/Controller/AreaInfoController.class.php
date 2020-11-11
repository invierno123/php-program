<?php
namespace Home\Controller;
use Think\Controller;
///首页
class AreaInfoController extends Controller {
	public function index(){
		$Dao_=M("companyinfo");
		$result=$Dao_->where("Id=".cookie("companyid")." and LoginName='".cookie("companyloginame")."'")->find();
		$this->list=$result;
    $this->display();
  }
  public function TrapAnalysis(){
		$this->display();
	}
	public function TrapAnalysis_cs(){
		/*CS使用*/
			$parmsStr=$_GET["type"];
			if($parmsStr!=""&&$parmsStr=="1324"){
				cookie("type","ok");
				cookie("oid",$_GET["oid"]);
				cookie("companyid",$_GET["oid"]);
			}
			$this->display();
	}
  public function getarealist(){
    $PageNum = $_GET["page"];
    $PageSize=3;

    if($PageNum.""==""){
      $PageNum=1;
    }
    $WhereSQL="";
    $AreaDAO = M("areainfo");
    $List_count =$AreaDAO->where(" companyid=".cookie("companyid")."".$WhereSQL)->order("Id")->count();

    $AllPage = (int)$List_count/(int)$PageSize;

    if((int)$List_count%(int)$PageSize!=0){
      $AllPage = (int)((int)$List_count/(int)$PageSize)+1;
    }
    if($AllPage==0){
      $AllPage=1;
    }

    $Rest_List = $AreaDAO->where(" companyid=".cookie("companyid")."".$WhereSQL)->order("Id")->limit((int)((int)$PageNum-1)*$PageSize,$PageSize)->select();
    $result_html="";
    foreach ($Rest_List as $key) {
      $result_html.="<tr><td>".$key["id"]."</td><td>".$key["areaname"]."</td><td>".$key["areauser"]."</td><td>".$key["usertel"]."</td><td>".$key["arealocation"]."</td><td>".$key["areainfo"]."</td><td><a onclick=\"edit_area('".$key["id"]."')\">".L("L_AREA_EDIT")."</a></td></tr>";
    }

    echo $result_html."○".$AllPage.",".$PageNum;
  }
  public function  update(){
			$DAO = M("areainfo");
			$post_key=$_POST["key"];
			$post_val=$_POST["val"];
			$id['Id']=$_POST['id'];
			$post_key=explode(',', $post_key);
			$post_val=explode(',', $post_val);
			$arrdata;
			for($i=0;$i<count($post_key);$i++){
				$arrdata[$post_key[$i]]=$post_val[$i];
			}
	   $data['AreaName']=$arrdata['areaname'];
		 $data['AreaUser']=$arrdata['areauser'];
		 $data['UserTEL']=$arrdata['usertel'];
		 $data['AreaLocation']=$arrdata['arealocation'];
		 $data['areainfo']=$arrdata['areainfo'];
		 $result=$DAO->where($id)->save($data);
		 if($result){
			 echo 'ok';
		 }

	}
	public  function  edit(){
			$DAO = M("trapmodel");
		$id['Id']=$_POST['id'];
		$post_key=$_POST["key"];
		$post_val=$_POST["val"];
		$post_key=explode(',', $post_key);
		$post_val=explode(',', $post_val);
		$arrdata;
		for($i=0;$i<count($post_key);$i++){
			$arrdata[$post_key[$i]]=$post_val[$i];
		}

				$data['Area']=$arrdata['Area'];

		$data['location']=$arrdata['location'];
		$data['trapNo']=$arrdata['trapNo'];
		$data['trapName']=$arrdata['trapName'];
		$data['LineSize']=$arrdata['LineSize'];
		$data['SPressure']=$arrdata['SPressure'];
		$data['OrderNum']=$arrdata['OrderNum'];
		$data['AreaId']=$arrdata['AreaId'];
		$data['UserMTFI']=$arrdata['UserMTFI'];
		$data['TrapType']=$arrdata['TrapType'];
		$data['LinkType']=$arrdata['LinkType'];
		$data['OutType']=$arrdata['OutType'];
		 $result=$DAO->where($id)->save($data);
	}
	public function settrap(){
		$post_key=$_POST["key"];
		$post_val=$_POST["val"];
		$post_type=$_POST["type"];
		$post_id="";
		$sql_where_str_id="";
		if($post_val==""||$post_key==""||$post_type==""){
			echo "0";
			exit;
		}
		if($post_type=="update"){
			$post_id=$_POST["oid"];
			if($post_id==""){
				echo "0";
				exit;
			}
			$sql_where_str_id.=" and Id!=".$post_id;
		}
		$post_key=explode(',', $post_key);
		$post_val=explode(',', $post_val);
		$arrdata;
		for($i=0;$i<count($post_key);$i++)
		{
			$arrdata[$post_key[$i]]=$post_val[$i];
		}
		$AreaDAO = M("trapmodel");
		$AreaDAO_MODEL=$AreaDAO->where("companyid=".cookie("companyid")." and trapno='".$arrdata["trapNo"]."'".$sql_where_str_id)->find();
		if($AreaDAO_MODEL!=null&&$AreaDAO_MODEL["id"]>0){
			echo "-2";
			//echo $AreaDAO_MODEL."*";
			exit;
		}
		$AreaDAO_MODEL=$AreaDAO->where("companyid=".cookie("companyid")." and OrderNum=".$arrdata["OrderNum"].$sql_where_str_id)->find();
		if($AreaDAO_MODEL!=null&&$AreaDAO_MODEL["id"]>0){
			echo "-3";
			exit;
		}
	  $arrdata["CompanyID"]=cookie("companyid");
    if($post_type=="add"){
			$AreaDAO_MODEL=$AreaDAO->add($arrdata);
			if($AreaDAO_MODEL!=false&&$AreaDAO_MODEL>0){
				echo "1";
			}else{
				echo "0";
			}
		}else if($post_type=="update"){
			$AreaDAO_MODEL=$AreaDAO->where("Id=".$post_id)->save($arrdata);
			if($AreaDAO_MODEL!=false&&$AreaDAO_MODEL>0){
				echo "1";
			}else if($AreaDAO_MODEL==0){
				echo "2";
			}
		}else{
			echo "0";
		}
	}
  public function gettraplist(){
    $area=$_GET["area"];
    $trap=$_GET["trap"];
    $trapty=$_GET["trapty"];
    $keys=$_GET["keys"];
    $PageNum = $_GET["page"];
    $PageSize=5;

    if($PageNum.""==""){
      $PageNum=1;
    }
    $WhereSQL="";
    if($area!=""){
      $WhereSQL.=" and areaid=".$area;
    }
    if($trap!=""){
      $WhereSQL.=" and trapno like '%".$trap."%'";
    }
    if($trapty!=""){
      $WhereSQL.=" and traptype='".$trapty."'";
    }
    if($keys!=""){
      $WhereSQL.=" and (usemtfi like '%".$keys."%' or LineSize like '%".$keys."%' or OutType like '%".$keys."%')";
    }

    $AreaDAO = M("trapmodel");
    $List_count =$AreaDAO->where(" companyid=".cookie("companyid")."".$WhereSQL)->order("Id")->count();

    $AllPage = (int)$List_count/(int)$PageSize;

    if((int)$List_count%(int)$PageSize!=0){
      $AllPage = (int)((int)$List_count/(int)$PageSize)+1;
    }
    if($AllPage==0){
      $AllPage=1;
    }

    $Rest_List = $AreaDAO->where(" companyid=".cookie("companyid")."".$WhereSQL)->order("areaId,ordernum ")->limit((int)((int)$PageNum-1)*$PageSize,$PageSize)->select();
    $result_html="";
    foreach ($Rest_List as $key) {
      $SSQY=$key["area"];
      if($key["area"]=="" && ($key["areaid"]==0 || $key["areaid"]=="")){
        $SSQY="".L("L_AREA_EMPTYSPACE");
      }else if($key["area"]=="" && (int)$key["areaid"]>0){
        $AreaINFO = M("areainfo");
        $AreaINFO_model=$AreaINFO->where(" companyid=".cookie("companyid")." and id=".$key["areaid"])->find();
        $SSQY=$AreaINFO_model["areaname"];
      }
			$KB_ = L("L_STATE_BISHI");
			if($key["outtype"]=="1"){
				$KB_=L("L_STATE_KAISHI");
			}
      $result_html.="<tr><td>".$key["trapno"]."</td>
      <td>".$SSQY."</td>
      <td>".$key["trapname"]."</td>
      <td>".$key["traptype"]."</td>
      <td>".$key["linktype"]."</td>
      <td>".$key["linesize"]."</td>
      <td>".$KB_."</td>
      <td>".$key["ordernum"]."</td>
      <td><a onclick=\"edit_trap('".$key["id"]."')\">".L("L_AREA_EDIT")."</a></td></td></tr>";
    }

    echo $result_html."○".$AllPage.",".$PageNum;
  }
 public function getTrapInfoById(){
	 $trap_id=$_POST["data"];
	 if($trap_id==""){
		 echo "0";
		 exit;
	 }
	 $AreaDAO = M("trapmodel");
	 $AreaDAO_MODEL=$AreaDAO->where("companyid=".cookie("companyid")." and id=".$trap_id)->find();
	 if($AreaDAO_MODEL!=null&&$AreaDAO_MODEL["id"]>0){
		 echo json_encode($AreaDAO_MODEL);
	 }else{
		 echo "0";
	 }
 }
 public function getAreaInfoById(){
 	$area_id=$_POST["data"];
 	if($area_id==""){
 		echo "0";
 		exit;
 	}
 	$AreaDAO = M("areainfo");
 	$AreaDAO_MODEL=$AreaDAO->where("companyid=".cookie("companyid")." and id=".$area_id)->find();
 	if($AreaDAO_MODEL!=null&&$AreaDAO_MODEL["id"]>0){
 		echo json_encode($AreaDAO_MODEL);
 	}else{
 		echo "0";
 	}
 }
 Public function getTraoInfoByAnalysis(){
	 $area_where=$_POST["area"];
	 $ty_where=$_POST["ty"];
	 $tn_where=$_POST["trapno"];
	 $cty_where=$_POST["cty"];
	 $sql_head="SELECT MONTH(DateCheck) AS 'time' FROM trapinfo";
	 $sql_end="GROUP BY MONTH(DateCheck) ASC";
	 $sql_zheng="select COUNT(*)as count from trapinfo ";
	 $sql_yi="select COUNT(*)as count from trapinfo ";
	 $sql_zheng_e="and TrapState='0' and MONTH(DateCheck)='";
	 $sql_yi_e="and TrapState='1' and MONTH(DateCheck)='";
   $where_str=" CompanyID=".cookie("companyid")." ";
	 //$where_str=" CompanyID='".$this->comid."' ";
	 $aa=date('Y-');
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
		 $sql_zheng_e="and TrapState='0' and DAY(DateCheck)='";
		 $sql_yi_e="and TrapState='1' and DAY(DateCheck)='";
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
			$jidu_s=date('Y-01-01');
			$jidu_e=(string)(date('Y')+1)."-01-01";
		}
			$where_str.="and DateCheck BETWEEN '".$jidu_s."' and '".$jidu_e."'";
	}
	 $DAO_Trap=M("trapinfo");
	if($cty_where=="pie"){
		$select_normal=$DAO_Trap->where($where_str."and TrapState='0'")->count();
		$select_notnormal=$DAO_Trap->where($where_str."and TrapState='1'")->count();
		echo $select_normal.",".$select_notnormal;
	}else{
		$result_zheng=array();
		$result_yi=array();
		$result_time=array();
		$model = M();
		$sql=$sql_head." where ".$where_str." ".$sql_end;
		$model_time=$model->query($sql);
		for($i=0;$i<count($model_time);$i++){
			$riqi=$model_time[$i]["time"];
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


public function getTraps(){
		$area_id=$_GET["areaid"];
		if($area_id!=""){
			$DAO_trap=M("trapmodel");
			$where_str="CompanyID=".cookie("companyid")." and AreaID='".$area_id."'";
			$List_trap = $DAO_trap->where($where_str)->select();
			$traps="";
			foreach ($List_trap as $trap_){
				$traps.=$trap_["id"].",".$trap_["trapno"]."/";
			}
			echo rtrim($traps,",");
		}
 }
}
