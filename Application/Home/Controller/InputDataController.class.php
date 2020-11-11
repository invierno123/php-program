<?php
namespace Home\Controller;
use Think\Controller;

class InputDataController extends Controller {
	public function index(){
	/*	if(cookie("companyid").""==""){
      if($_GET["oid"]!=""){
        cookie("companyid",$_GET["oid"]);
      }
    }*/
      $this->display();
 }

 public  function  saveExcel(){
   //设置上传目录
   $path = $_SERVER['DOCUMENT_ROOT'] . "/uploads"."/";
   if (!empty($_FILES)) {
       //得到上传的临时文件流
       $tempFile = $_FILES['Filedata']['tmp_name'];
       //允许的文件后缀
       $fileTypes = array('xls','xlsx');
       //得到文件原名
       $fileName = iconv("UTF-8","GB2312",$_FILES["Filedata"]["name"]);

       $fileParts = pathinfo($_FILES['Filedata']['name']);
       //接受动态传值
       $files=$_POST['typeCode'];
       //最后保存服务器地址
       if(!is_dir($path))
          mkdir($path);
       if (move_uploaded_file($tempFile, $path.$fileName)){
      //     echo $path.$fileName;
       }else{
          // echo "0";
       }
   }
  			   $db = mysql_connect('localhost', 'root', '125164') or
					
					die("Could not connect to database.");//连接数据库
         	//		$db = M();
					mysql_query("SET NAMES 'UTF8'");//输出中文
			  	mysql_select_db('tmsdata'); //选择数据库
					error_reporting(E_ALL ^ E_NOTICE);
					mysql_query("SET AUTOCOMMIT=0");
				//	$db->autocommit(FALSE);
			//		数据库类型://用户名:密码@数据库地址:数据库端口/数据库名
				 vendor('Excel.PHPExcel');//引用文件
				 vendor('Excel.PHPExcel.IOFactory');//引用文件
				 vendor('Excel.PHPExcel.Reader.Excel2007');//引用文件
				 $objReader=\PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
				 $objPHPExcel=\PHPExcel_IOFactory::load($path.$fileName);//$file_url即Excel文件的路径
				 $sheet=$objPHPExcel->getSheet(0);//获取第一个工作表
				 $highestRow=$sheet->getHighestRow();//取得总行数
				 $highestColumn=$sheet->getHighestColumn(); //取得总列数
		 		 $column=\PHPExcel_Cell::columnIndexFromString($highestColumn);
		    $filename=iconv('GB2312','UTF-8',$path.$fileName);
				$md5file=md5_file($path.$fileName);
				$importtime=date("y-m-d",time());
				$importNumber= intval($highestRow)-1;
			  $query="select md5 from import  where md5 ='".$md5file."'";
				$flag_=0;
	      $result1= mysql_query($query);
			  if(mysql_num_rows($result1)){
				echo L("L_IMPORT_FILE_EXISTS");
		  	}else{
				  	mysql_query("BEGIN");
					//循环读取excel文件,读取一条,插入一条
					for($j=2;$j<=$highestRow;$j++){//从第一行开始读取数据
					 $str='';
							 for($k=0;$k<=$column;$k++){            //从A列读取数据
							 //这种方法简单，但有不妥，以'\\'合并为数组，再分割\\为字段值插入到数据库,实测在excel中，如果某单元格的值包含了\\导入的数据会为空
								$str.=$sheet->getCellByColumnAndRow($k,$j)->getValue()."\\";//读取单元格
							 }
						 //explode:函数把字符串分割为数组。
					 $strs=explode("\\",$str);
					 $Dao=M('areainfo');
					 $data_select=$Dao->where("AreaName='".$strs[1]."' and companyid=".cookie('companyid'))->find();
					 if($data_select){

					 }else{
							  $sqlarea = "INSERT INTO areainfo(AreaName,AreaUser,UserTEL,AreaLocation,CompanyID,areainfo) VALUES('".
								 $strs[1]."','".
								 'NONE'."','".
								 '12345678'."','".
								 ''."','".
								 cookie('companyid')."','".
								 ''."' )";
								 $result=mysql_query($sqlarea);
							 if($result){
									mysql_query("COMMIT");
								}else{
									mysql_query("ROLLBACK");
									echo L('L_SUBMIT_FAIL');
									exit;
								}
				   }

				 $areaid = $Dao->where("AreaName='".$strs[1]."' and companyid=".cookie('companyid'))->getField('Id');
				 if($strs[14]==""){
					 $strs[14]="Good";
				 }
				 $TrapState=$strs[14]=='Good'?'1':'0';
			 //  echo '<br>';
				 //以下代码是将excel表数据【N个字段】插入到mysql中，根据你的excel表字段的多少，改写以下代码吧!
				 $LevelDESC_ = 0;
				 if($strs[16]!=""){
					 $LevelDESC_=$strs[16];
				 }
				 $LOSSAM_ = 0;
				 if($strs[17]!=""){
					 $LOSSAM_=$strs[17];
				 }
				 $LOSSMY_ = 0;
				 if($strs[20]!=""){
					 $LOSSMY_=$strs[20];
				 }
				if($strs[13]==""||$strs[18]==""||$strs[7]==""||$strs[8]==""){
					continue;//采集时间、温度、应用压力、管道口径为空的情况下，不插入数据库，跳出本次循环
				}
				$t=$strs[18];
				$n = intval(($t - 25569) * 3600 * 24); //转换成1970年以来的秒数
				$strs[18]= gmdate('Y-m-d', $n);//格式化时间
	      $time=date("y-m-d h:m:s",time());
				$sql = "insert into trapinfo(CompanyID,Area,AreaId,TrapNo,Location,TrapName,TrapType,UseMTFI,SPressure,LineSize,LinkType,OutType,Description,NewTem,TrapState,Exlevel,LevelDesc,LossAmount,LossMoneyYear,CreateTime,DateCheck) values('".
				 cookie('companyid')."','".
				 $strs[1]."',".
				 $areaid.",'".
				 $strs[2]."','".
				 $strs[3]."','".
				 $strs[4]."','".
				 $strs[5]."','".
				 $strs[6]."','".
				 $strs[7]."','".
				 $strs[8]."','".
				 $strs[9]."','".
				 $strs[10]."','".
				 $strs[11]."','".
				 $strs[13]."','".
				 $TrapState."','".
				 $strs[15]."','".
				 $LevelDESC_."','".
				 $LOSSAM_."','".
				 $LOSSMY_."','".
			  	$time."','".
				  $strs[18]."' );";
				 $res = mysql_query($sql);
				 if($res){

				 }
				else{
            $flag_++;
				 }
			 }
		        mysql_query("END");
		       if($flag_==0){
						 $sqlimport = "insert into import(companyid,filename,importPerson,importTime,importNumber,md5)values('".
						 cookie('companyid')."','".
						 $filename."','".
						 cookie('companyloginame')."','".
						 $importtime."','".
						 $importNumber."','".
						 $md5file."'  );";
						 $re = mysql_query($sqlimport);
						mysql_query("COMMIT");
						 echo L('L_SUBMIT_SUCCESS');
					 }else{
						 mysql_query("ROLLBACK");
						 echo L('L_SUBMIT_FAIL');
					 }

			}
  }
}
?>
