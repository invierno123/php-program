<?php
namespace Home\Controller;
use Think\Controller;

class  EmcController  extends  Controller{
  public function  index(){
    $this -> display();
  }


 public  function  setdata(){
    $Dao=M("emcdata");
    //CS最新添加
  $parmsStr=$_GET["type"];
  $this->woca=$parmsStr;

  //  $this->parmsStr=$parmsStr;
    if($parmsStr!=""&&$parmsStr=="1"){
    cookie("type","ok");
      $condition_["CompanyID"]=$_GET["oid"];

      cookie("oid",$_GET["oid"]);
    $data_select=$Dao->where($condition_)->find();
    $this->data=$data_select;

    //CS添加结束
  }else{
      $condition["CompanyID"]=cookie("companyid");
      $data_select=$Dao->where($condition)->find();
      $this->data=$data_select;
  }
    $this->display();
   }
   public   function  excel(){

     $Dao=M("emcdata");
     $condition["CompanyID"]=cookie("companyid");
  // $condition["CompanyID"]=$_GET["cid"];
    if($_GET["cid"]!=""){
      $condition["CompanyID"]=$_GET["cid"];
    }
     $data_select=$Dao->where($condition)->find();
     header("Content-Typ:text/html;charset=utf-8");
  //   include("./PHPExcel.php");
       vendor("Excel.PHPExcel");
     $objPHPExcel = new \PHPExcel();
     $objPHPExcel->getActiveSheet()->mergeCells('C1:N1');      //合并
     $objPHPExcel->getActiveSheet()->mergeCells('A5:A6');      //合并
     $objPHPExcel->getActiveSheet()->mergeCells('Q1:AB1');      //合并
     $objPHPExcel->getActiveSheet()->mergeCells('AE1:AP1');      //合并

    // $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
     $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
     $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
     $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);

     $objPHPExcel->getActiveSheet()->setCellValue('C1', "2013");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A3', "Production（Lb）");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A4', "Steam(Ton)");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A8', "单重耗能(T/Lb)");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A9', "同期单重节约耗能(Ton/Lb)");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A10', "同期节约耗能（Ton）");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A12', "节省费用（RMB）");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A14', "steam Trap");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A15', "Pipe And Insultion");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('A16', "Other");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('Q1', "2014");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O3', "Production（Lb）");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O4', "Steam(Ton)");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O8', "单重耗能(T/Lb)");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O9', "同期单重节约耗能(Ton/Lb)");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O10', "同期节约耗能（Ton）");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O12', "节省费用（RMB）");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O14', "steam Trap");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O15', "Pipe And Insultion");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('O16', "Other");//设置列的值


     $objPHPExcel->getActiveSheet()->setCellValue('B2', "比例");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('P2', "比例");//设置列的值



     $objPHPExcel->getActiveSheet()->setCellValue('C2', "Jan");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('D2', "Feb");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('E2', "Mar");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('F2', "Apr");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('G2', "May");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('H2', "Jun");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('I2', "Jul");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('J2', "Aug");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('K2', "Sep");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('L2', "Oct");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('M2', "Nov");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('N2', "Dec");//设置列的值

     $objPHPExcel->getActiveSheet()->setCellValue('Q2', "Jan");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('R2', "Feb");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('S2', "Mar");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('T2', "Apr");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('U2', "May");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('V2', "Jun");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('W2', "Jul");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('X2', "Aug");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('Y2', "Sep");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('Z2', "Oct");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('AA2', "Nov");//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('AB2', "Dec");//设置列的值



     $jia=$Dao->where($condition)->getField('Jia');
     $yi=$Dao->where($condition)->getField('Yi');
     $qita=$Dao->where($condition)->getField('Qita');
     $price=$Dao->where($condition)->getField('PowerPrice');
     $bili=$Dao->where($condition)->getField('ExpendScale');
     $objPHPExcel->getActiveSheet()->setCellValue('B5', $bili);//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('B12', $price);//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('B14', $jia);//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('B15', $yi);//设置列的值
     $objPHPExcel->getActiveSheet()->setCellValue('B16', $qita);//设置列的值
     $monthProduction=$Dao->where($condition)->getField('FristProductionM');
      $yearProduction=$Dao->where($condition)->getField('FristProductionY');
     if($monthProduction!=""){
          $array= explode( '/',$monthProduction);
     }else  if($yearProduction!=""){
       $array= explode( '/',$yearProduction);
     }
//设置初年的月生产量
     $objPHPExcel->getActiveSheet()->setCellValue('C3',$array[0]);
     $objPHPExcel->getActiveSheet()->setCellValue('D3',$array[1]);
     $objPHPExcel->getActiveSheet()->setCellValue('E3',$array[2]);
     $objPHPExcel->getActiveSheet()->setCellValue('F3',$array[3]);
     $objPHPExcel->getActiveSheet()->setCellValue('G3',$array[4]);
     $objPHPExcel->getActiveSheet()->setCellValue('H3',$array[5]);
     $objPHPExcel->getActiveSheet()->setCellValue('I3',$array[6]);
     $objPHPExcel->getActiveSheet()->setCellValue('J3',$array[7]);
     $objPHPExcel->getActiveSheet()->setCellValue('K3',$array[8]);
     $objPHPExcel->getActiveSheet()->setCellValue('L3',$array[9]);
     $objPHPExcel->getActiveSheet()->setCellValue('M3',$array[10]);
     $objPHPExcel->getActiveSheet()->setCellValue('N3',$array[11]);
     $monthExpend=$Dao->where($condition)->getField('FristExpendM');
     $yearExpend=$Dao->where($condition)->getField('FristExpendY');
     if($monthExpend!=""){
      $arr= explode( '/',$monthExpend);
    }else if( $yearExpend!=""){
      $arr= explode( '/',$yearExpend);
    }

     $objPHPExcel->getActiveSheet()->setCellValue('C4',$arr[0]);
     $objPHPExcel->getActiveSheet()->setCellValue('D4',$arr[1]);
     $objPHPExcel->getActiveSheet()->setCellValue('E4',$arr[2]);
     $objPHPExcel->getActiveSheet()->setCellValue('F4',$arr[3]);
     $objPHPExcel->getActiveSheet()->setCellValue('G4',$arr[4]);
     $objPHPExcel->getActiveSheet()->setCellValue('H4',$arr[5]);
     $objPHPExcel->getActiveSheet()->setCellValue('I4',$arr[6]);
     $objPHPExcel->getActiveSheet()->setCellValue('J4',$arr[7]);
     $objPHPExcel->getActiveSheet()->setCellValue('K4',$arr[8]);
     $objPHPExcel->getActiveSheet()->setCellValue('L4',$arr[9]);
     $objPHPExcel->getActiveSheet()->setCellValue('M4',$arr[10]);
     $objPHPExcel->getActiveSheet()->setCellValue('N4',$arr[11]);

     $objPHPExcel->getActiveSheet()->setCellValue('C5',$bili*$arr[0]);
     $objPHPExcel->getActiveSheet()->setCellValue('D5',$bili*$arr[1]);
     $objPHPExcel->getActiveSheet()->setCellValue('E5',$bili*$arr[2]);
     $objPHPExcel->getActiveSheet()->setCellValue('F5',$bili*$arr[3]);
     $objPHPExcel->getActiveSheet()->setCellValue('G5',$bili*$arr[4]);
     $objPHPExcel->getActiveSheet()->setCellValue('H5',$bili*$arr[5]);
     $objPHPExcel->getActiveSheet()->setCellValue('I5',$bili*$arr[6]);
     $objPHPExcel->getActiveSheet()->setCellValue('J5',$bili*$arr[7]);
     $objPHPExcel->getActiveSheet()->setCellValue('K5',$bili*$arr[8]);
     $objPHPExcel->getActiveSheet()->setCellValue('L5',$bili*$arr[9]);
     $objPHPExcel->getActiveSheet()->setCellValue('M5',$bili*$arr[10]);
     $objPHPExcel->getActiveSheet()->setCellValue('N5',$bili*$arr[11]);



     $objPHPExcel->getActiveSheet()->setCellValue('C8',$arr[0]/$array[0]);
     $objPHPExcel->getActiveSheet()->setCellValue('D8',$arr[1]/$array[1]);
     $objPHPExcel->getActiveSheet()->setCellValue('E8',$arr[2]/$array[2]);
     $objPHPExcel->getActiveSheet()->setCellValue('F8',$arr[3]/$array[3]);
     $objPHPExcel->getActiveSheet()->setCellValue('G8',$arr[4]/$array[4]);
     $objPHPExcel->getActiveSheet()->setCellValue('H8',$arr[5]/$array[5]);
     $objPHPExcel->getActiveSheet()->setCellValue('I8',$arr[6]/$array[6]);
      $objPHPExcel->getActiveSheet()->setCellValue('J8',$arr[7]/$array[7]);
      $objPHPExcel->getActiveSheet()->setCellValue('K8',$arr[8]/$array[8]);
      $objPHPExcel->getActiveSheet()->setCellValue('L8',$arr[9]/$array[9]);
      $objPHPExcel->getActiveSheet()->setCellValue('M8',$arr[10]/$array[10]);
     $objPHPExcel->getActiveSheet()->setCellValue('N8',$arr[11]/$array[11]);
      //$objPHPExcel->getActiveSheet(0)->setTitle('emcdata');
      //    $objPHPExcel->setActiveSheetIndex(0);


    $objPHPExcel->getActiveSheet()->getStyle( 'Q10:AB10')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'Q10:AB10')->getFill()->getStartColor()->setARGB('0017C405');
    $objPHPExcel->getActiveSheet()->getStyle( 'Q12:AB12')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'Q12:AB12')->getFill()->getStartColor()->setARGB('#ff6337');
    $objPHPExcel->getActiveSheet()->getStyle( 'Q14:AB14')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'Q14:AB14')->getFill()->getStartColor()->setARGB('#0cedffb');

    $objPHPExcel->getActiveSheet()->getStyle( 'AE10:AP10')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'AE10:AP10')->getFill()->getStartColor()->setARGB('0017C405');
    $objPHPExcel->getActiveSheet()->getStyle( 'AE12:AP12')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'AE12:AP12')->getFill()->getStartColor()->setARGB('#ff6337');
    $objPHPExcel->getActiveSheet()->getStyle( 'AE14:AP14')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'AE14:AP14')->getFill()->getStartColor()->setARGB('#0cedffb');

   $objPHPExcel->getActiveSheet()->setCellValue('P12',$_GET['powerPrice']);//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('P5',$_GET['thisyearexpendScale']);//设置列的值

   $objPHPExcel->getActiveSheet()->setCellValue('P14', $_GET['jia']);//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('P15', $_GET['yi']);//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('P16', $_GET['qita']);//设置列的值

   $thisyearProductionY=$_GET['productionY'];
   $thisyearProductionM=$_GET['productionM'];
   if($thisyearProductionY!=""){
       $arra= explode( '/',$thisyearProductionY);
   }else  if ($thisyearProductionM!="") {
      $arra= explode( '/',$thisyearProductionM);
   }


  $objPHPExcel->getActiveSheet()->setCellValue('Q3',$arra[0]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('R3',$arra[1]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('S3', $arra[2]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('T3', $arra[3]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('U3', $arra[4]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('V3', $arra[5]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('W3', $arra[6]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('X3', $arra[7]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('Y3', $arra[8]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('Z3', $arra[9]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('AA3', $arra[10]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('AB3', $arra[11]);//设置列的值

  $thisyearexpendY=$_GET['expendY'];
  $thisyearexpendM=$_GET['expendM'];
  if($thisyearexpendY!=""){
      $ar= explode( '/',$thisyearexpendY);
  }else  if ($thisyearexpendM!="") {
     $ar= explode( '/',$thisyearexpendM);
  }
  $objPHPExcel->getActiveSheet()->setCellValue('Q4',$ar[0]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('R4',$ar[1]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('S4',$ar[2]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('T4',$ar[3]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('U4', $ar[4]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('V4',$ar[5]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('W4', $ar[6]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('X4', $ar[7]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('Y4', $ar[8]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('Z4', $ar[9]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('AA4',$ar[10]);//设置列的值
  $objPHPExcel->getActiveSheet()->setCellValue('AB4', $ar[11]);//设置列的值


  $objPHPExcel->getActiveSheet()->setCellValue('Q5',$_GET['thisyearexpendScale']*$ar[0]);
  $objPHPExcel->getActiveSheet()->setCellValue('R5',$_GET['thisyearexpendScale']*$ar[1]);
  $objPHPExcel->getActiveSheet()->setCellValue('S5',$_GET['thisyearexpendScale']*$ar[2]);
  $objPHPExcel->getActiveSheet()->setCellValue('T5',$_GET['thisyearexpendScale']*$ar[3]);
  $objPHPExcel->getActiveSheet()->setCellValue('U5',$_GET['thisyearexpendScale']*$ar[4]);
  $objPHPExcel->getActiveSheet()->setCellValue('V5',$_GET['thisyearexpendScale']*$ar[5]);
  $objPHPExcel->getActiveSheet()->setCellValue('W5',$_GET['thisyearexpendScale']*$ar[6]);
  $objPHPExcel->getActiveSheet()->setCellValue('X5',$_GET['thisyearexpendScale']*$ar[7]);
  $objPHPExcel->getActiveSheet()->setCellValue('Y5',$_GET['thisyearexpendScale']*$ar[8]);
  $objPHPExcel->getActiveSheet()->setCellValue('Z5',$_GET['thisyearexpendScale']*$ar[9]);
  $objPHPExcel->getActiveSheet()->setCellValue('AA5',$_GET['thisyearexpendScale']*$ar[10]);
  $objPHPExcel->getActiveSheet()->setCellValue('AB5',$_GET['thisyearexpendScale']*$ar[11]);



  $objPHPExcel->getActiveSheet()->setCellValue('Q8',$ar[0]/$arra[0]);
  $objPHPExcel->getActiveSheet()->setCellValue('R8',$ar[1]/$arra[1]);
  $objPHPExcel->getActiveSheet()->setCellValue('S8',$ar[2]/$arra[2]);
  $objPHPExcel->getActiveSheet()->setCellValue('T8',$ar[3]/$arra[3]);
  $objPHPExcel->getActiveSheet()->setCellValue('U8',$ar[4]/$arra[4]);
  $objPHPExcel->getActiveSheet()->setCellValue('V8',$ar[5]/$arra[5]);
  $objPHPExcel->getActiveSheet()->setCellValue('W8',$ar[6]/$arra[6]);
  $objPHPExcel->getActiveSheet()->setCellValue('X8',$ar[7]/$arra[7]);
  $objPHPExcel->getActiveSheet()->setCellValue('Y8',$ar[8]/$arra[8]);
  $objPHPExcel->getActiveSheet()->setCellValue('Z8',$ar[9]/$arra[9]);
  $objPHPExcel->getActiveSheet()->setCellValue('AA8',$ar[10]/$arra[10]);
  $objPHPExcel->getActiveSheet()->setCellValue('AB8',$ar[11]/$arra[11]);

  $C8 = $objPHPExcel->getActiveSheet()->getCell('C8')->getValue();
  $D8 = $objPHPExcel->getActiveSheet()->getCell('D8')->getValue();
  $E8 = $objPHPExcel->getActiveSheet()->getCell('E8')->getValue();
  $F8 = $objPHPExcel->getActiveSheet()->getCell('F8')->getValue();
  $G8 = $objPHPExcel->getActiveSheet()->getCell('G8')->getValue();
  $H8 = $objPHPExcel->getActiveSheet()->getCell('H8')->getValue();
  $I8 = $objPHPExcel->getActiveSheet()->getCell('I8')->getValue();
  $J8 = $objPHPExcel->getActiveSheet()->getCell('J8')->getValue();
  $K8 = $objPHPExcel->getActiveSheet()->getCell('K8')->getValue();
  $L8 = $objPHPExcel->getActiveSheet()->getCell('L8')->getValue();
  $M8 = $objPHPExcel->getActiveSheet()->getCell('M8')->getValue();
  $N8 = $objPHPExcel->getActiveSheet()->getCell('N8')->getValue();

  $Q8 = $objPHPExcel->getActiveSheet()->getCell('Q8')->getValue();
  $R8 = $objPHPExcel->getActiveSheet()->getCell('R8')->getValue();
  $S8 = $objPHPExcel->getActiveSheet()->getCell('S8')->getValue();
  $T8 = $objPHPExcel->getActiveSheet()->getCell('T8')->getValue();
  $U8 = $objPHPExcel->getActiveSheet()->getCell('U8')->getValue();
  $V8 = $objPHPExcel->getActiveSheet()->getCell('V8')->getValue();
  $W8 = $objPHPExcel->getActiveSheet()->getCell('W8')->getValue();
  $X8 = $objPHPExcel->getActiveSheet()->getCell('X8')->getValue();
  $Y8 = $objPHPExcel->getActiveSheet()->getCell('Y8')->getValue();
  $Z8 = $objPHPExcel->getActiveSheet()->getCell('Z8')->getValue();
  $AA8 = $objPHPExcel->getActiveSheet()->getCell('AA8')->getValue();
  $AB8 = $objPHPExcel->getActiveSheet()->getCell('AB8')->getValue();

  $objPHPExcel->getActiveSheet()->setCellValue('Q9',$C8-$Q8);
  $objPHPExcel->getActiveSheet()->setCellValue('R9',$D8-$R8);
  $objPHPExcel->getActiveSheet()->setCellValue('S9',$E8-$S8);
  $objPHPExcel->getActiveSheet()->setCellValue('T9',$F8-$T8);
  $objPHPExcel->getActiveSheet()->setCellValue('U9',$G8-$U8);
  $objPHPExcel->getActiveSheet()->setCellValue('V9',$H8-$V8);
  $objPHPExcel->getActiveSheet()->setCellValue('W9',$I8-$W8);
  $objPHPExcel->getActiveSheet()->setCellValue('X9',$J8-$X8);
  $objPHPExcel->getActiveSheet()->setCellValue('Y9',$K8-$Y8);
  $objPHPExcel->getActiveSheet()->setCellValue('Z9',$L8-$Z8);
  $objPHPExcel->getActiveSheet()->setCellValue('AA9',$M8-$AA8);
  $objPHPExcel->getActiveSheet()->setCellValue('AB9',$N8-$AB8);

  $C4 = $objPHPExcel->getActiveSheet()->getCell('C4')->getValue();
  $D4 = $objPHPExcel->getActiveSheet()->getCell('D4')->getValue();
  $E4 = $objPHPExcel->getActiveSheet()->getCell('E4')->getValue();
  $F4 = $objPHPExcel->getActiveSheet()->getCell('F4')->getValue();
  $G4 = $objPHPExcel->getActiveSheet()->getCell('G4')->getValue();
  $H4 = $objPHPExcel->getActiveSheet()->getCell('H4')->getValue();
  $I4 = $objPHPExcel->getActiveSheet()->getCell('I4')->getValue();
  $J4 = $objPHPExcel->getActiveSheet()->getCell('J4')->getValue();
  $K4 = $objPHPExcel->getActiveSheet()->getCell('K4')->getValue();
  $L4 = $objPHPExcel->getActiveSheet()->getCell('L4')->getValue();
  $M4 = $objPHPExcel->getActiveSheet()->getCell('M4')->getValue();
  $N4 = $objPHPExcel->getActiveSheet()->getCell('N4')->getValue();

  $Q4 = $objPHPExcel->getActiveSheet()->getCell('Q4')->getValue();
  $R4 = $objPHPExcel->getActiveSheet()->getCell('R4')->getValue();
  $S4 = $objPHPExcel->getActiveSheet()->getCell('S4')->getValue();
  $T4 = $objPHPExcel->getActiveSheet()->getCell('T4')->getValue();
  $U4 = $objPHPExcel->getActiveSheet()->getCell('U4')->getValue();
  $V4 = $objPHPExcel->getActiveSheet()->getCell('V4')->getValue();
  $W4 = $objPHPExcel->getActiveSheet()->getCell('W4')->getValue();
  $X4 = $objPHPExcel->getActiveSheet()->getCell('X4')->getValue();
  $Y4 = $objPHPExcel->getActiveSheet()->getCell('Y4')->getValue();
  $Z4 = $objPHPExcel->getActiveSheet()->getCell('Z4')->getValue();
  $AA4 = $objPHPExcel->getActiveSheet()->getCell('AA4')->getValue();
  $AB4 = $objPHPExcel->getActiveSheet()->getCell('AB4')->getValue();

  $objPHPExcel->getActiveSheet()->setCellValue('Q10',$C4-$Q4);
  $objPHPExcel->getActiveSheet()->setCellValue('R10',$D4-$R4);
  $objPHPExcel->getActiveSheet()->setCellValue('S10',$E4-$S4);
  $objPHPExcel->getActiveSheet()->setCellValue('T10',$F4-$T4);
  $objPHPExcel->getActiveSheet()->setCellValue('U10',$G4-$U4);
  $objPHPExcel->getActiveSheet()->setCellValue('V10',$H4-$V4);
  $objPHPExcel->getActiveSheet()->setCellValue('W10',$I4-$W4);
  $objPHPExcel->getActiveSheet()->setCellValue('X10',$J4-$X4);
  $objPHPExcel->getActiveSheet()->setCellValue('Y10',$K4-$Y4);
  $objPHPExcel->getActiveSheet()->setCellValue('Z10',$L4-$Z4);
  $objPHPExcel->getActiveSheet()->setCellValue('AA10',$M4-$AA4);
  $objPHPExcel->getActiveSheet()->setCellValue('AB10',$N4-$AB4);

$B12=$objPHPExcel->getActiveSheet()->getCell("B12")->getValue();
$P12=$objPHPExcel->getActiveSheet()->getCell("P12")->getValue();

$Q10=$objPHPExcel->getActiveSheet()->getCell("Q10")->getValue();
$R10=$objPHPExcel->getActiveSheet()->getCell("R10")->getValue();
$S10=$objPHPExcel->getActiveSheet()->getCell("S10")->getValue();
$T10=$objPHPExcel->getActiveSheet()->getCell("T10")->getValue();
$U10=$objPHPExcel->getActiveSheet()->getCell("U10")->getValue();
$V10=$objPHPExcel->getActiveSheet()->getCell("V10")->getValue();
$W10=$objPHPExcel->getActiveSheet()->getCell("W10")->getValue();
$X10=$objPHPExcel->getActiveSheet()->getCell("X10")->getValue();
$Y10=$objPHPExcel->getActiveSheet()->getCell("Y10")->getValue();
$Z10=$objPHPExcel->getActiveSheet()->getCell("Z10")->getValue();
$AA10=$objPHPExcel->getActiveSheet()->getCell("AA10")->getValue();
$AB10=$objPHPExcel->getActiveSheet()->getCell("AB10")->getValue();

$objPHPExcel->getActiveSheet()->setCellValue('Q12',$B12*$C4-$P12*$Q4);
$objPHPExcel->getActiveSheet()->setCellValue('R12',$B12*$D4-$P12*$R4);
$objPHPExcel->getActiveSheet()->setCellValue('S12',$B12*$E4-$P12*$S4);
$objPHPExcel->getActiveSheet()->setCellValue('T12',$B12*$F4-$P12*$T4);
$objPHPExcel->getActiveSheet()->setCellValue('U12',$B12*$G4-$P12*$U4);
$objPHPExcel->getActiveSheet()->setCellValue('V12',$B12*$H4-$P12*$V4);
$objPHPExcel->getActiveSheet()->setCellValue('W12',$B12*$I4-$P12*$W4);
$objPHPExcel->getActiveSheet()->setCellValue('X12',$B12*$J4-$P12*$X4);
$objPHPExcel->getActiveSheet()->setCellValue('Y12',$B12*$K4-$P12*$Y4);
$objPHPExcel->getActiveSheet()->setCellValue('Z12',$B12*$L4-$P12*$Z4);
$objPHPExcel->getActiveSheet()->setCellValue('AA12',$B12*$M4-$P12*$AA4);
$objPHPExcel->getActiveSheet()->setCellValue('AB12',$B12*$N4-$P12*$AB4);

$P14=$objPHPExcel->getActiveSheet()->getCell("P14")->getValue();
$P15=$objPHPExcel->getActiveSheet()->getCell("P15")->getValue();
$P16=$objPHPExcel->getActiveSheet()->getCell("P16")->getValue();

$Q12=$objPHPExcel->getActiveSheet()->getCell("Q12")->getValue();
$R12=$objPHPExcel->getActiveSheet()->getCell("R12")->getValue();
$S12=$objPHPExcel->getActiveSheet()->getCell("S12")->getValue();
$T12=$objPHPExcel->getActiveSheet()->getCell("T12")->getValue();
$U12=$objPHPExcel->getActiveSheet()->getCell("U12")->getValue();
$V12=$objPHPExcel->getActiveSheet()->getCell("V12")->getValue();
$W12=$objPHPExcel->getActiveSheet()->getCell("W12")->getValue();
$X12=$objPHPExcel->getActiveSheet()->getCell("X12")->getValue();
$Y12=$objPHPExcel->getActiveSheet()->getCell("Y12")->getValue();
$Z12=$objPHPExcel->getActiveSheet()->getCell("Z12")->getValue();
$AA12=$objPHPExcel->getActiveSheet()->getCell("AA12")->getValue();
$AB12=$objPHPExcel->getActiveSheet()->getCell("AB12")->getValue();

$objPHPExcel->getActiveSheet()->setCellValue('Q14',$P14*$Q12);
$objPHPExcel->getActiveSheet()->setCellValue('R14',$P14*$R12);
$objPHPExcel->getActiveSheet()->setCellValue('S14',$P14*$S12);
$objPHPExcel->getActiveSheet()->setCellValue('T14',$P14*$T12);
$objPHPExcel->getActiveSheet()->setCellValue('U14',$P14*$U12);
$objPHPExcel->getActiveSheet()->setCellValue('V14',$P14*$V12);
$objPHPExcel->getActiveSheet()->setCellValue('W14',$P14*$W12);
$objPHPExcel->getActiveSheet()->setCellValue('X14',$P14*$X12);
$objPHPExcel->getActiveSheet()->setCellValue('Y14',$P14*$Y12);
$objPHPExcel->getActiveSheet()->setCellValue('Z14',$P14*$Z12);
$objPHPExcel->getActiveSheet()->setCellValue('AA14',$P14*$AA12);
$objPHPExcel->getActiveSheet()->setCellValue('AB14',$P14*$AB12);



$objPHPExcel->getActiveSheet()->setCellValue('Q15',$P15*$Q12);
$objPHPExcel->getActiveSheet()->setCellValue('R15',$P15*$R12);
$objPHPExcel->getActiveSheet()->setCellValue('S15',$P15*$S12);
$objPHPExcel->getActiveSheet()->setCellValue('T15',$P15*$T12);
$objPHPExcel->getActiveSheet()->setCellValue('U15',$P15*$U12);
$objPHPExcel->getActiveSheet()->setCellValue('V15',$P15*$V12);
$objPHPExcel->getActiveSheet()->setCellValue('W15',$P15*$W12);
$objPHPExcel->getActiveSheet()->setCellValue('X15',$P15*$X12);
$objPHPExcel->getActiveSheet()->setCellValue('Y15',$P15*$Y12);
$objPHPExcel->getActiveSheet()->setCellValue('Z15',$P15*$Z12);
$objPHPExcel->getActiveSheet()->setCellValue('AA15',$P15*$AA12);
$objPHPExcel->getActiveSheet()->setCellValue('AB15',$P15*$AB12);


$objPHPExcel->getActiveSheet()->setCellValue('Q16',$P16*$Q12);
$objPHPExcel->getActiveSheet()->setCellValue('R16',$P16*$R12);
$objPHPExcel->getActiveSheet()->setCellValue('S16',$P16*$S12);
$objPHPExcel->getActiveSheet()->setCellValue('T16',$P16*$T12);
$objPHPExcel->getActiveSheet()->setCellValue('U16',$P16*$U12);
$objPHPExcel->getActiveSheet()->setCellValue('V16',$P16*$V12);
$objPHPExcel->getActiveSheet()->setCellValue('W16',$P16*$W12);
$objPHPExcel->getActiveSheet()->setCellValue('X16',$P16*$X12);
$objPHPExcel->getActiveSheet()->setCellValue('Y16',$P16*$Y12);
$objPHPExcel->getActiveSheet()->setCellValue('Z16',$P16*$Z12);
$objPHPExcel->getActiveSheet()->setCellValue('AA16',$P16*$AA12);
$objPHPExcel->getActiveSheet()->setCellValue('AB16',$P16*$AB12);



   $objPHPExcel->getActiveSheet()->setCellValue('AE1', "2015");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC3', "Production（Lb）");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC4', "Steam(Ton)");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC8', "单重耗能(T/Lb)");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC9', "同期单重节约耗能(Ton/Lb)");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC10', "同期节约耗能（Ton）");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC12', "节省费用（RMB）");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC14', "steam Trap");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC15', "Pipe And Insultion");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AC16', "Other");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AD2', "比例");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AE2', "Jan");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AF2', "Feb");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AG2', "Mar");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AH2', "Apr");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AI2', "May");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AJ2', "Jun");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AK2', "Jul");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AL2', "Aug");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AM2', "Sep");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AN2', "Oct");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AO2', "Nov");//设置列的值
   $objPHPExcel->getActiveSheet()->setCellValue('AP2', "Dec");//设置列的值

    $objPHPExcel->getActiveSheet()->setCellValue('AE3',$arra[0]-$array[0] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AF3',$arra[1]-$array[1] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AG3',$arra[2]-$array[2] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AH3',$arra[3]-$array[3] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AI3',$arra[4]-$array[4] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AJ3',$arra[5]-$array[5] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AK3',$arra[6]-$array[6] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AL3',$arra[7]-$array[7] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AM3',$arra[8]-$array[8] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AN3',$arra[9]-$array[9] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AO3',$arra[10]-$array[10] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AP3',$arra[11]-$array[11] );//设置列的值

    $objPHPExcel->getActiveSheet()->setCellValue('AE4',$ar[0]-$arr[0] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AF4',$ar[1]-$arr[1] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AG4',$ar[2]-$arr[2] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AH4',$ar[3]-$arr[3] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AI4',$ar[4]-$arr[4] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AJ4',$ar[5]-$arr[5] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AK4',$ar[6]-$arr[6] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AL4',$ar[7]-$arr[7] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AM4',$ar[8]-$arr[8] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AN4',$ar[9]-$arr[9] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AO4',$ar[10]-$arr[10] );//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AP4',$ar[11]-$arr[11] );//设置列的值

    $AE3=$objPHPExcel->getActiveSheet()->getCell("AE3")->getValue();
    $AF3=$objPHPExcel->getActiveSheet()->getCell("AF3")->getValue();
    $AG3=$objPHPExcel->getActiveSheet()->getCell("AG3")->getValue();
    $AH3=$objPHPExcel->getActiveSheet()->getCell("AH3")->getValue();
    $AI3=$objPHPExcel->getActiveSheet()->getCell("AI3")->getValue();
    $AJ3=$objPHPExcel->getActiveSheet()->getCell("AJ3")->getValue();
    $AK3=$objPHPExcel->getActiveSheet()->getCell("AK3")->getValue();
    $AL3=$objPHPExcel->getActiveSheet()->getCell("AL3")->getValue();
    $AM3=$objPHPExcel->getActiveSheet()->getCell("AM3")->getValue();
    $AN3=$objPHPExcel->getActiveSheet()->getCell("AN3")->getValue();
    $AO3=$objPHPExcel->getActiveSheet()->getCell("AO3")->getValue();
    $AP3=$objPHPExcel->getActiveSheet()->getCell("AP3")->getValue();

    $AE4=$objPHPExcel->getActiveSheet()->getCell("AE4")->getValue();
    $AF4=$objPHPExcel->getActiveSheet()->getCell("AF4")->getValue();
    $AG4=$objPHPExcel->getActiveSheet()->getCell("AG4")->getValue();
    $AH4=$objPHPExcel->getActiveSheet()->getCell("AH4")->getValue();
    $AI4=$objPHPExcel->getActiveSheet()->getCell("AI4")->getValue();
    $AJ4=$objPHPExcel->getActiveSheet()->getCell("AJ4")->getValue();
    $AK4=$objPHPExcel->getActiveSheet()->getCell("AK4")->getValue();
    $AL4=$objPHPExcel->getActiveSheet()->getCell("AL4")->getValue();
    $AM4=$objPHPExcel->getActiveSheet()->getCell("AM4")->getValue();
    $AN4=$objPHPExcel->getActiveSheet()->getCell("AN4")->getValue();
    $AO4=$objPHPExcel->getActiveSheet()->getCell("AO4")->getValue();
    $AP4=$objPHPExcel->getActiveSheet()->getCell("AP4")->getValue();

    $objPHPExcel->getActiveSheet()->setCellValue('AE8',$AE4/$AE3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AF8',$AF4/$AF3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AG8',$AG4/$AG3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AH8',$AH4/$AH3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AI8',$AI4/$AI3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AJ8',$AJ4/$AJ3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AK8',$AK4/$AK3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AL8',$AL4/$AL3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AM8',$AM4/$AM3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AN8',$AN4/$AN3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AO8',$AO4/$AO3);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AP8',$AP4/$AP3);//设置列的值

  /*  $AE8=$objPHPExcel->getActiveSheet()->getCell("AE8")->getValue();
    $AF8=$objPHPExcel->getActiveSheet()->getCell("AF8")->getValue();
    $AG8=$objPHPExcel->getActiveSheet()->getCell("AG8")->getValue();
    $AH8=$objPHPExcel->getActiveSheet()->getCell("AH8")->getValue();
    $AI8=$objPHPExcel->getActiveSheet()->getCell("AI8")->getValue();
    $AJ8=$objPHPExcel->getActiveSheet()->getCell("AJ8")->getValue();
    $AK8=$objPHPExcel->getActiveSheet()->getCell("AK8")->getValue();
    $AL8=$objPHPExcel->getActiveSheet()->getCell("AL8")->getValue();
    $AM8=$objPHPExcel->getActiveSheet()->getCell("AM8")->getValue();
    $AN8=$objPHPExcel->getActiveSheet()->getCell("AN8")->getValue();
    $A08=$objPHPExcel->getActiveSheet()->getCell("AO8")->getValue();
    $AP8=$objPHPExcel->getActiveSheet()->getCell("AP8")->getValue();*/

    $objPHPExcel->getActiveSheet()->setCellValue('AE9',$C8-$Q8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AF9',$D8-$R8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AG9',$E8-$S8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AH9',$F8-$T8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AI9',$G8-$U8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AJ9',$H8-$V8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AK9',$I8-$W8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AL9',$J8-$X8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AM9',$K8-$Y8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AN9',$L8-$Z8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AO9',$M8-$AA8);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AP9',$N8-$AB8);//设置列的值

/*  $AE9=$objPHPExcel->getActiveSheet()->getCell("AE9")->getValue();
    $AF9=$objPHPExcel->getActiveSheet()->getCell("AF9")->getValue();
    $AG9=$objPHPExcel->getActiveSheet()->getCell("AG9")->getValue();
    $AH9=$objPHPExcel->getActiveSheet()->getCell("AH9")->getValue();
    $AI9=$objPHPExcel->getActiveSheet()->getCell("AI9")->getValue();
    $AJ9=$objPHPExcel->getActiveSheet()->getCell("AJ9")->getValue();
    $AK9=$objPHPExcel->getActiveSheet()->getCell("AK9")->getValue();
    $AL9=$objPHPExcel->getActiveSheet()->getCell("AL9")->getValue();
    $AM9=$objPHPExcel->getActiveSheet()->getCell("AM9")->getValue();
    $AN9=$objPHPExcel->getActiveSheet()->getCell("AN9")->getValue();
    $A09=$objPHPExcel->getActiveSheet()->getCell("AO9")->getValue();
    $AP9=$objPHPExcel->getActiveSheet()->getCell("AP9")->getValue();
    */

    $objPHPExcel->getActiveSheet()->setCellValue('AE10',$C4-$Q4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AF10',$D4-$R4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AG10',$E4-$S4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AH10',$F4-$T4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AI10',$G4-$U4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AJ10',$H4-$V4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AK10',$I4-$W4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AL10',$J4-$X4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AM10',$K4-$Y4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AN10',$L4-$Z4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AO10',$M4-$AA4);//设置列的值
    $objPHPExcel->getActiveSheet()->setCellValue('AP10',$N4-$AB4);//设置列的值

    $objPHPExcel->getActiveSheet()->setCellValue('AE12',$B12*$C4-$P12*$Q4);
    $objPHPExcel->getActiveSheet()->setCellValue('AF12',$B12*$D4-$P12*$R4);
    $objPHPExcel->getActiveSheet()->setCellValue('AG12',$B12*$E4-$P12*$S4);
    $objPHPExcel->getActiveSheet()->setCellValue('AH12',$B12*$F4-$P12*$T4);
    $objPHPExcel->getActiveSheet()->setCellValue('AI12',$B12*$G4-$P12*$U4);
    $objPHPExcel->getActiveSheet()->setCellValue('AJ12',$B12*$H4-$P12*$V4);
    $objPHPExcel->getActiveSheet()->setCellValue('AK12',$B12*$I4-$P12*$W4);
    $objPHPExcel->getActiveSheet()->setCellValue('AL12',$B12*$J4-$P12*$X4);
    $objPHPExcel->getActiveSheet()->setCellValue('AM12',$B12*$K4-$P12*$Y4);
    $objPHPExcel->getActiveSheet()->setCellValue('AN12',$B12*$L4-$P12*$Z4);
    $objPHPExcel->getActiveSheet()->setCellValue('AO12',$B12*$M4-$P12*$AA4);
    $objPHPExcel->getActiveSheet()->setCellValue('AP12',$B12*$N4-$P12*$AB4);


    $objPHPExcel->getActiveSheet()->setCellValue('AE14',$P14*$Q12);
    $objPHPExcel->getActiveSheet()->setCellValue('AF14',$P14*$R12);
    $objPHPExcel->getActiveSheet()->setCellValue('AG14',$P14*$S12);
    $objPHPExcel->getActiveSheet()->setCellValue('AH14',$P14*$T12);
    $objPHPExcel->getActiveSheet()->setCellValue('AI14',$P14*$U12);
    $objPHPExcel->getActiveSheet()->setCellValue('AJ14',$P14*$V12);
    $objPHPExcel->getActiveSheet()->setCellValue('AK14',$P14*$W12);
    $objPHPExcel->getActiveSheet()->setCellValue('AL14',$P14*$X12);
    $objPHPExcel->getActiveSheet()->setCellValue('AM14',$P14*$Y12);
    $objPHPExcel->getActiveSheet()->setCellValue('AN14',$P14*$Z12);
    $objPHPExcel->getActiveSheet()->setCellValue('AO14',$P14*$AA12);
    $objPHPExcel->getActiveSheet()->setCellValue('AP14',$P14*$AB12);



    $objPHPExcel->getActiveSheet()->setCellValue('AE15',$P15*$Q12);
    $objPHPExcel->getActiveSheet()->setCellValue('AF15',$P15*$R12);
    $objPHPExcel->getActiveSheet()->setCellValue('AG15',$P15*$S12);
    $objPHPExcel->getActiveSheet()->setCellValue('AH15',$P15*$T12);
    $objPHPExcel->getActiveSheet()->setCellValue('AI15',$P15*$U12);
    $objPHPExcel->getActiveSheet()->setCellValue('AJ15',$P15*$V12);
    $objPHPExcel->getActiveSheet()->setCellValue('AK15',$P15*$W12);
    $objPHPExcel->getActiveSheet()->setCellValue('AL15',$P15*$X12);
    $objPHPExcel->getActiveSheet()->setCellValue('AM15',$P15*$Y12);
    $objPHPExcel->getActiveSheet()->setCellValue('AN15',$P15*$Z12);
    $objPHPExcel->getActiveSheet()->setCellValue('AO15',$P15*$AA12);
    $objPHPExcel->getActiveSheet()->setCellValue('AP15',$P15*$AB12);


    $objPHPExcel->getActiveSheet()->setCellValue('AE16',$P16*$Q12);
    $objPHPExcel->getActiveSheet()->setCellValue('AF16',$P16*$R12);
    $objPHPExcel->getActiveSheet()->setCellValue('AG16',$P16*$S12);
    $objPHPExcel->getActiveSheet()->setCellValue('AH16',$P16*$T12);
    $objPHPExcel->getActiveSheet()->setCellValue('AI16',$P16*$U12);
    $objPHPExcel->getActiveSheet()->setCellValue('AJ16',$P16*$V12);
    $objPHPExcel->getActiveSheet()->setCellValue('AK16',$P16*$W12);
    $objPHPExcel->getActiveSheet()->setCellValue('AL16',$P16*$X12);
    $objPHPExcel->getActiveSheet()->setCellValue('AM16',$P16*$Y12);
    $objPHPExcel->getActiveSheet()->setCellValue('AN16',$P16*$Z12);
    $objPHPExcel->getActiveSheet()->setCellValue('AO16',$P16*$AA12);
    $objPHPExcel->getActiveSheet()->setCellValue('AP16',$P16*$AB12);


      ob_end_clean();//清除缓冲区,避免乱码
      header('Content-Type:application/vnd.ms-excel;charset=utf-8');

    $time = time();
    $filename=date("y-m-d",$time)."Data analysis";

      header('Content-Disposition:attachment;'.'filename='.$filename.'.xls');
      header('Cache-Control: max-age=0');
      $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      $objWriter->save('php://output');
}
  public  function  update(){
      $Dao=M("emcdata");
           $data["PowerUnity"]=$_GET["powerUnity"];
          $data["PowerPrice"]=$_GET["powerPrice"];
        //  $data["thisyearExpend"]=$_POST["expendY"];
        $data["thisyearexpendScale"]=$_GET["thisyearexpendScale"];
         $condition["CompanyID"]=cookie("companyid");
       $result=$Dao->where($condition)->save($data);
    if($result){
        echo "ok";
    }
    else{
      echo  "err";
    }
  }

   public  function  add(){
    $Dao=M("emcdata");
    $data["CompanyID"]=cookie("companyid");
    $data["PowerUnity"]=$_POST["powerUnity"];
    $data["PowerName"]=$_POST["powerName"];
    $data["PowerNumber"]=$_POST["powerNumber"];
    $data["PowerPrice"]=$_POST["powerPrice"];
    $data["Currency"]=$_POST["currency"];
    $data["FristExpendY"]=$_POST["firstExpendY"];
    $data["FristExpendM"]=$_POST["firstExpendM"];
    $data["ExpendScale"]=$_POST["expendScale"];
    $data["FristProductionY"]=$_POST["firstProductionY"];
    $data["FristProductionM"]=$_POST["firstProductionM"];

    $data["Jia"]=$_POST["jia"];
    $data["Yi"]=$_POST["yi"];
    $data["Qita"]=$_POST["qita"];
    $result=$Dao->add($data);
    if($result){
        echo "ok";
    }
    else{
      echo  "err";
    }
   }
 }
?>
