<?php


/*是否符合规则的判断*/
function CheckRoleInfo_GET($opCompanyId)
{
	$res_="";
	try{
		$DAO_ROLE=M("roleadmin");
		$LIST_DAO = $DAO_ROLE->where("(CompanyID=0 or CompanyID=".$opCompanyId.")")->select();
		foreach ($LIST_DAO as $RoleItem) {
			if(((int)$RoleItem["opdesc"])>0){
				$res_ =" and NOW()>=(createtime + INTERVAL ".$RoleItem["opdesc"]." MINUTE)";
			}
		}
	} catch (Exception $e) {
	}
	return $res_;
}

/*是否符合规则的判断-数据修改前的插入*/
function CheckRoleInfo_ADD($opCompanyId,$DataSTR,$FZBL)
{
	$DAO_ROLE=M("roleadmin");
	$LIST_DAO = $DAO_ROLE->where("(CompanyID=0 or CompanyID=".$opCompanyId.")")->select();
	$res_="1=1 ";
	foreach ($LIST_DAO as $RoleItem) {
		if(((int)$RoleItem["opdesc"])==0){
			//始终正常
			if(((int)$FZBL["bl"])>30){
				$FZBL["maxfz"]=rand(4,6);
				$FZBL["minfz"]=rand(2,4);
				$FZBL["avgfz"]=rand(4,5);
				$FZBL["bl"] = 0;
				$FZBL["tem"] = 170;
				$DAO_CHANGE_TRAP = M("changetrapinfo");
				$DAO_CHANGE_TRAP->add();
			}
		}else if(((int)$RoleItem["opdesc"])==-1) {
			//始终异常
			if(((int)$FZBL["bl"])<=30){
				$FZBL["maxfz"]=rand(104,126);
				$FZBL["minfz"]=rand(14,16);
				$FZBL["avgfz"]=rand(86,95);
				$FZBL["bl"]= 99;
				$FZBL["tem"] = 170;
				$DAO_CHANGE_TRAP = M("changetrapinfo");

			}
		}
	}
	return $FZBL;
}