<?php

//TMS-CS  报警列表误报添加
function AddErrorReport(){
	$JsonData=$_POST["data"];
	$Res_Data = json_decode($JsonData);
	$Dao_Learningtrap= M("learntrap");
	$companyid=$Res_Data->cid;
	$wariningid=$Res_Data->wid;
	$trapno=$Res_Data->trapno;
	$alerttem=$Res_Data->alerttem;
	$alerthz=$Res_Data->alerthz;
	$standardtem=$Res_Data->standardtem;
	$alerttype=$Res_Data->alerttype;
	$fluidtype=$Res_Data->fluidtype;
	$pressure=$Res_Data->pressure;
	$mintem=$Res_Data->mintem;
	$maxtem=$Res_Data->maxtem;
	$data['CompanyID']=$companyid;
	$data['TrapNO']=$trapno;
	$data['FristValue']=$standardtem;
	$data['AlertValue']=$alerttem;
	$data['AlertHZ']=$alerthz;
	$data['AlertType']= $alerttype;
	$data['TType']=  $fluidtype;
	$data['TPValue']=$pressure;
	$data['MinTem']= $mintem;
	$data['MaxTem']=$maxtem;
	$Trap_MODEL_DAO = M("trapmodel");

	$TRAP_RES = $Trap_MODEL_DAO->where("trapNo='".$data['TrapNO']."' and companyid=".$companyid)->find();

	$data['SPValue']=$TRAP_RES['SPressure'];

	$ALL_DATA_LT=$Dao_Learningtrap->where("CompanyID=".$companyid)->select();
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

	$result =$Dao_Learningtrap->add($data);
	if($result>0){
		$Waring_=M("warning");
		$wariningData["LearnState"]="1";
		$model=$Waring_->where("Id=".$wariningid)->save($wariningData);
		if($model!=false && $model>0)
		{
			echo '{"res":"1"}';
		}
		else{
			echo '{"res":"0"}';
		}
	}else {
		echo '{"res":"0"}';
	}
}

function queryLearnState($trapid){
	$Model=M("learntrap");
	$result=$Model->where("TrapNo='".$trapid."'")->count();
	return $result;
}