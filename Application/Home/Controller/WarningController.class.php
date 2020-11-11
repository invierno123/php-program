<?php
namespace Home\Controller;
use Think\Controller;

class  WarningController  extends  Controller{
  public function  index(){
    $this -> display();

  }
  public  function  errorreport(){
    $Dao=M("warning");
    $result=$Dao->where('id='.$_GET['id'])->find();
    $this->list=$result;
    $this->display();
  }
  public  function  adderror(){
    $Dao=M("learntrap");
    $data['CompanyID']=cookie('companyid');
    $data['TrapNO']=$_POST['trapno'];
    $data['FristValue']=$_POST['standardtem'];
    $data['AlertValue']=$_POST['alerttem'];
    $data['AlertHZ']=$_POST['alerthz'];
    $data['AlertType']=$_POST['alerttype'];
    $data['TType']=$_POST['fluidtype'];
    $data['TPValue']=$_POST['tpvalue'];
    $data['MinTem']=$_POST['mintem'];
    $data['MaxTem']=$_POST['maxtem'];
    $Trap_MODEL_DAO = M("trapmodel");

    $TRAP_RES = $Trap_MODEL_DAO->where("trapNo='".$data['TrapNO']."' and companyid=".cookie('companyid'))->find();

    $data['SPValue']=$TRAP_RES['spressure'];

    $ALL_DATA_LT=$Dao->where("CompanyID=".cookie('companyid'))->select();
    $AVG_HZ=0.00;
    $AVG_TEM=0.00;
    $MAX_TEM=0.00;
    $MAX_HZ=0.00;
    $ALL_Count=0;
    foreach ($ALL_DATA_LT as $key_LT) {
      $ALL_Count++;
      $AVG_HZ += (double)$key_LT["NewHZ"];
      $AVG_TEM += (double)$key_LT["NewValue"];
      if((double)$key_LT["NewValue"]>$MAX_TEM){
        $MAX_TEM=(double)$key_LT["NewValue"];
      }
      if((double)$key_LT["NewHZ"]>$MAX_HZ){
        $MAX_HZ = (double)$key_LT["NewHZ"];
      }
    }
    $AVG_TEM = $AVG_TEM/(double)count($ALL_DATA_LT);
    $AVG_HZ = $AVG_HZ/(double)count($ALL_DATA_LT);

    if($AVG_TEM>$data['AlertValue']){
      $data['NewValue'] = $AVG_TEM;
    }else {
      if($MAX_TEM>$data['AlertValue']){
        $data['NewValue'] = (double)(((double)$AVG_TEM+(double)$MAX_TEM)/2);
      }else {
        $data['NewValue'] = $data['AlertValue'];
      }
    }
    if($AVG_HZ>$data['AlertHZ']){
      $data['NewHZ'] = $AVG_HZ;
    }else {
      if($MAX_HZ>$data['AlertHZ']){
        $data['NewHZ'] = (double)(((double)$AVG_HZ+(double)$MAX_HZ)/2);
      }else {
        $data['NewHZ'] = $data['AlertHZ'];
      }
    }
    $data['CreateTime'] = date("Y-m-d H:i:s");
    $data['AlertCount'] = $ALL_Count+1;

    $result =$Dao->add($data);
    if($result){
      echo "ok";
    }else {
      echo "error";
    }
  }

  public  function  add(){
   $this->display();
  }
  public  function  addMessage(){
    $Dao=M("warning");
    $data["CompanyId"]=cookie("companyid");
    $data['AreaId']=$_POST['areaid'];
        $data['Area']=$_POST['area'];
    $data['TrapNo']=$_POST['trapno'];
    $data['Location']=$_POST['location'];
    $data['RepairState']=$_POST['repairstate'];
    $data['RepairType']=$_POST['repairtype'];
    $data['RepairTime']=$_POST['repairtime'];
    $data['RepairNum']=$_POST['repairnum'];
    $data['RepairPrice']=$_POST['repairprice'];
    $data['TrapState']=$_POST['trapstate'];
    $data['ExLevel']=$_POST['exlevel'];
    $data['LevelDesc']=$_POST['leveldesc'];
    $data['AlertTem']=$_POST['alerttem'];
    $data['AlertHZ']=$_POST['alerthz'];
    $data['StandardTem']=$_POST['standardtem'];

    $data['RepairDescription']=$_POST['repairdescription'];
    $result =$Dao->add($data);
    if($result){
      echo  "ok";
    }else{
      echo  "error";
    }
  }
  public function getwarninglist(){
    $AreaDAO = M("warning");
    $area_where=$_GET["area"];
    $ts_where=$_GET["ts"];
    $te_where=$_GET["te"];
    $tn_where=$_GET["trapno"];
    $repairstate_where=$_GET["repairstate"];

  //  $rstate_where=$_GET["rstate"];
  /*  $PageNum = $_GET["page"];
    $PageSize=3;
    if($PageNum.""==""){
      $PageNum=1;
    }*/
  $where_str="CompanyId=".cookie("companyid")." ";
    if($area_where!=""){
      $where_str.=" and AreaId = '".$area_where."' ";
    }
    if($ts_where!=""){
      $where_str.=" and RepairTime>='".$ts_where."' ";
    }
    if($te_where!=""){
      $where_str.=" and RepairTime<='".$te_where."' ";
    }
    if($tn_where!=""){
    $where_str.=" and TrapNo like '%".$tn_where."%' ";
    }
    if($repairstate_where!=""){
    $where_str.=" and RepairState = '".$repairstate_where."' ";
    }


    $count =$AreaDAO->where($where_str)->count();
        $PageSize=10;
        $this->page_all=$page_all = ceil($count/$PageSize);
        $page = $_GET['page'] ? $_GET['page'] : 1;
        $this->page=$page;
      //  $this->page_up=$page_up = $page-1>0 ? $page-1 : 1;
      //  $this->page_down=$page_down = $page+1>$page_all ? $page_all : $page+1;
    $List_ALL  = $AreaDAO->where($where_str)->order("RepairTime desc,areaid,trapno")->limit(($page-1)*$PageSize,$PageSize)->select();
    $show_list_str="";
    foreach ($List_ALL as $trap_each) {
      $show_list_str.="<tr name='tr_repair'>
      <td>".$trap_each["id"]."</td>
      <td>".$trap_each["area"]."</td>
      <td>".$trap_each["trapno"]."</td>
      <td>".$trap_each["location"]."</td>
      <td class='repair'>".$trap_each["repairstate"]."</td>
      <td>".$trap_each["repairtype"]."</td>
      <td>".date("Y-m-d",strtotime($trap_each["repairtime"]))."</td>
      <td>".$trap_each["repairnum"]."</td>
      <td>".$trap_each["repairprice"]."</td>
      <td>".$trap_each["trapstate"]."</td>
      <td>".$trap_each["exlevel"]."</td>
      <td>".$trap_each["leveldesc"]."</td>
      <td>".$trap_each["alerttem"]."</td>
      <td>".$trap_each["alerthz"]."</td>
      <td>".$trap_each["standardtem"]."</td>
      <td title='".$trap_each["repairdescription"]."' >".mb_substr($trap_each["repairdescription"],0,8,'utf-8')."..."."</td>
      <td class='repair' ><a  onclick=\"innit('".$trap_each["id"]."','".$this->page."');\" style='text-decoration: none;cursor:pointer;'>".L('L_ALERT_HANDLE')."</a></td>
      <td class='repair' ><a  onclick=\"errorreport('".$trap_each["id"]."','".$this->page."');\" style='text-decoration: none;cursor:pointer;'>".L('L_ALERT_MISINFORMATION')."</a></td>
      </tr>";

    }
    echo $show_list_str."○".$page_all.",".$page.",".$count;
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
    public  function  innit(){
    $Dao=M("warning");
    $result=$Dao->where('id='.$_GET['id'])->find();
//   $data['']==0?"正常":"异常";
    $this->warnningdata=$result;
    $this->display();
    }
  public  function  update(){
    $Dao=M("warning");
    $id['Id']=$_POST['id'];
    $data['RepairState']=$_POST['repairstate'];
    $data['RepairType']=$_POST['repairtype'];
    $data['StandardTem']=$_POST['standardtem'];
    $data['RepairTime']=$_POST['repairtime'];
    $data['RepairNum']=$_POST['repairnum'];
    $data['RepairPrice']=$_POST['repairprice'];
    $data['RepairDescription']=$_POST['repairdescription'];
    $result=$Dao->where($id)->save($data);
    if($result){
      echo "ok";
    }
    else{
      echo  "err";
    }
  }
}
?>
