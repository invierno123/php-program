<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>统计分析</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="/Public/css/supervisory.css" type="text/css" media="screen" />
  <script type="text/javascript" src="../../../../Public/js/jquery-1.7.1.min.js" ></script>
  <script src='/Public/js/echarts/echarts.common.min.js' ></script>
  <script src='/Public/js/echarts/theme/infographic.js' ></script>
      <script language="javascript" type="text/javascript" src="/Public/js/My97DatePicker/WdatePicker.js"></script>
</head>
<script>
$(function(){
//	$("#chart_line").hide();
	//$("#chart_bar").hide();
  $.get("/home/supervisory/getareas",function(res){
    var area_array_=res.split('/');
    var area_array_l=area_array_.length;
    for(var i=0;i<area_array_l;i++){
      if(area_array_[i]==""){break;}
      $("#select_area").append("<option value='"+area_array_[i].split(",")[0]+"'>"+area_array_[i].split(",")[1]+"</option>");
    }
  });
  $("#select_area").change(function(){
    if($(this).val()!=""){
      $.get("/home/AreaInfo/getTraps?areaid="+$(this).val(),function(res){
      //  alert(res);
        var area_array_=res.split('/');
		$("#select_trapno").html("");
		$("#select_trapno").append("<option value=''><?php echo L('L_ALERT_TJ_ALL');?></option>");
    var area_l=area_array_.length;
        for(var i=0;i<area_l;i++){
          if(area_array_[i]==""){break;}
          //if($("#select_trapno option[value='"+area_array_[i].split(",")[0]+"']").size()<=0){
            $("#select_trapno").append("<option value='"+area_array_[i].split(",")[0]+"'>"+area_array_[i].split(",")[1]+"</option>");
          //}
        }
      });
    }else{
	 $("#select_trapno").html("");
	$("#select_trapno").append("<option value=''><?php echo L('L_ALERT_TJ_ALL');?></option>");
	 }
  });
  btn_search_click();
});
</script>
<body>
  <h1 style="font-size:14px;"><?php echo L('L_SA_Titel');?></h1>
  <table class="tab_search">
    <tr>
      <td><?php echo L("L_AREA_QY");?></td>
      <td><select class="input_search" id="select_area"><option value=""><?php echo L("L_ALERT_TJ_ALL");?></option></select></td>
      <td>&nbsp;</td>
      <td><?php echo L('L_AREA_JDBH');?></td>
      <td><select class="input_search" id="select_trapno"><option value=""><?php echo L("L_ALERT_TJ_ALL");?></option></select></td>
      <td>&nbsp;</td>
      <td><?php echo L("L_AREA_SJL");?></td>
      <!--<td><input type="text" value="" id="input_t_s" class="input_search" onclick="WdatePicker()"/></td>
      <td>-</td>
      <td><input type="text" value="" id="input_t_e" class="input_search" onclick="WdatePicker()"/></td>-->
      <td><select class="input_search" id="select_time">
        <option value="0"><?php echo L("L_AREA_BENYUENEI");?></option>
        <option value="1"><?php echo L("L_SA_Quarter");?></option>
        <option value="2"><?php echo L("L_AREA_BENNIANNEI");?></option>
      </select></td>
      <td>&nbsp;</td>
      <td><?php echo L("L_SA_DT");?></td>
      <td><select class="input_search" id="select_chartstype">
        <option value="pie"><?php echo L("L_SA_ChartPie");?></option>
        <option value="bar"><?php echo L("L_SA_ChartBar");?></option>
        <option value="line"><?php echo L("L_SA_ChartLine");?></option>
      </select>
    </td>
      <td>&nbsp;</td>
      <td><input type="button" value=" <?php echo L('L_SEARCH_SEARCH');?> " onclick="btn_search_click()" class="btn_search" /></td>
    </tr>
  </table>
<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
   <div id="charts_div" name="charts_div" style="width:100%;"></div>
   <script type="text/javascript">

   $("#charts_div").css("height",$(document).height()-16-51-30);
       // 基于准备好的dom，初始化echarts实例
  function dongtai_pie(per_dataz,per_databz){
    // 指定图表的配置项和数据
  var myChar_pie = echarts.init(document.getElementById('charts_div'));
    if(per_dataz==0&&per_databz==0){
       myChar_pie.showLoading({
         text:"<?php echo L('L_ALERT_DATAEMPTY');?>"
       });
      //alert("<?php echo L('L_ALERT_DATAEMPTY');?>");
    }else{
   option = {
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            /*orient: 'vertical',
            left: 'left',*/
            data: ["<?php echo L('L_ALERT_TJ_EXC');?>","<?php echo L('L_ALERT_TJ_WORK');?>"]
        },
        series : [
            {
                name: '',
                type: 'pie',
                radius : '55%',
                center: ['50%', '60%'],
                data:[
                    {value:per_databz, name:"<?php echo L('L_ALERT_TJ_EXC');?>",
                    itemStyle:{normal: {
                            color: '#F08080'
                             }
                          }
                  },
                   {value:per_dataz, name:"<?php echo L('L_ALERT_TJ_WORK');?>",
         itemStyle:{normal: {
                 color: '#8FBC8F'
                  }
               }
              }
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    myChar_pie.setOption(option);
    }

  }
  function dongtai_bar(time_array,zheng_data,yi_data){
      var myChar_pie = echarts.init(document.getElementById('charts_div'));

    if(time_array.length<=0){
      myChar_pie.showLoading({
        text:"<?php echo L('L_ALERT_DATAEMPTY');?>"
      });
      //  alert("<?php echo L('L_ALERT_DATAEMPTY');?>");
    }else{  // 指定图表的配置项和数据
    option = {
        tooltip : {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        legend: {
              data: ["<?php echo L('L_ALERT_TJ_EXC');?>","<?php echo L('L_ALERT_TJ_WORK');?>"]
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                data : time_array
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
          {
              name:"<?php echo L('L_ALERT_TJ_EXC');?>",
              type:'bar',
              data:yi_data
          },
            {
                name:"<?php echo L('L_ALERT_TJ_WORK');?>",
                type:'bar',
                data:zheng_data
            },
          ]
    };
    myChar_pie.setOption(option);
  }

  }
  function dongtai_line(time_array,zheng_data,yi_data){
      var myChar_pie = echarts.init(document.getElementById('charts_div'));
    if(time_array.length<=0){
      myChar_pie.showLoading({
        text:"<?php echo L('L_ALERT_DATAEMPTY');?>"
      });
        //alert("<?php echo L('L_ALERT_DATAEMPTY');?>");
    }else{
    option = {
      tooltip : {
          trigger: 'axis',
          axisPointer : {            // 坐标轴指示器，坐标轴触发有效
              type : 'line'        // 默认为直线，可选为：'line' | 'shadow'
          }
      },
    legend: {
          data: ["<?php echo L('L_ALERT_TJ_EXC');?>","<?php echo L('L_ALERT_TJ_WORK');?>"]
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data :time_array
        }
    ],
    yAxis : [
        {
            type : 'value',
            axisLabel : {
                formatter: '{value}'
            }
        }
    ],
    series : [
        {
            name:"<?php echo L('L_ALERT_TJ_EXC');?>",
            type:'line',
            data:yi_data,/*
            markPoint : {
                data : [
                    {type : 'max', name: "<?php echo L('L_SA_Max');?>"},
                    {type : 'min', name: "<?php echo L('L_SA_Min');?>"}
                ]
            },
            markLine : {
                data : [
                    {type : 'average', name: "<?php echo L('L_SA_Average');?>"}
                ]
            }*/
        },
        {
            name:"<?php echo L('L_ALERT_TJ_WORK');?>",
            type:'line',
            data:zheng_data,}
            /*markPoint : {
                data : [
                    {name : '最低', value : -2, xAxis: 1, yAxis: -1.5}
                ]
            },
            markLine : {
                data : [
                    {type : 'average', name : "<?php echo L('L_SA_Average');?>"}
                ]
            }
        }*/
    ]
 };
  myChar_pie.setOption(option);
}

}
function btn_search_click(){
  var area=$.trim($("#select_area").val());
  var time_type=$.trim($("#select_time").val());
  var trapno=$.trim($("#select_trapno").find("option:selected").text());
  if($("#select_trapno").val()==""){
    trapno="";
  }
  var chart_type=$("#select_chartstype").val();

    $.post("/home/AreaInfo/getTraoInfoByAnalysis",{"area":area,"ty":time_type,"trapno":trapno,"cty":chart_type},function(res){
      if(chart_type=="pie"){
        dongtai_pie(res.split(",")[0],res.split(",")[1]);
      }else{
        var parsedJson = jQuery.parseJSON(res);
        var html_data_t=parsedJson.time;
        var html_data_z =parsedJson.zheng;
        var html_data_y =parsedJson.yi;
        var data_time=new Array();
        var data_z=new Array();
        var data_y=new Array();
        var html_length=html_data_t.length;
       for(var i=0;i<html_length;i++){
          data_time[i]=html_data_t[i];
          data_z[i]=html_data_z[i];
          data_y[i]=html_data_y[i];
        }
        if(chart_type=="bar"){
         dongtai_bar(data_time,data_z,data_y);
       }else{
         dongtai_line(data_time,data_z,data_y);
       }
      }
    });
}
   </script>
 </body>
<html>