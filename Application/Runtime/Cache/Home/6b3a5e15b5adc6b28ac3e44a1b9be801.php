<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="/Public/css/superline.css" type="text/css" media="screen" />
    <script src='/Public/js/jquery.js' ></script>


    <script src='/Public/js/echarts/3.1.5/echarts.js' ></script>
    <script src='/Public/js/echarts/theme/shine.js' ></script>
    <script>
      var Start_L=90;
      var End_L=100;
      //require(
      //[
      //    '/Public/js/echarts/',
      //    '/Public/js/echarts/chart/line' //按需加载图表关于线性图、折线图的部分
      //],
      //DrawEChart //异步加载的回调函数绘制图表
      //);
      //创建ECharts图表方法
     function DrawEChart() {
         //--- 折柱 ---
         myChart = echarts.init(document.getElementById('div_show_line'),'shine');
         //定义图表options
         var options = {
             title: {
                 text: "",
                 subtext: '',
                 x: 'center'
             },
             tooltip: {
               trigger: 'axis',
               /*formatter: function (params) {
                   return params[0].name + '<br/>'
                       + params[0].seriesName + ' : ' + params[0].value + ' (m^3/s)<br/>';
               },*/
               axisPointer: {
                   animation: false
               }
             },
             toolbox: {
                show : true,
                feature : {
                    markPoint : {show: true},
                    dataZoom : {show: true},
                }
             },
             legend: {
                 data: []
             },
             calculable: true,
             dataZoom : {
               type: 'inside',
               show: true,
               realtime : true,
               start : Start_L,
               end : End_L,
             },

             xAxis: [
                 {
                     type: '',
                     data: [""],
                     splitArea: { show: true },
                 }
             ],
             yAxis: [
                 {
                     type: 'value',
                     axisLabel: { formatter:'{value} ℃'},
                     splitArea: { show: true },
                 }
             ],
            series: [ /*{
                      name:'温度',
                      type:'line',
                      markPoint : {
                          data : [
                              {type : 'max', name: '最大值'},
                              {type : 'min', name: '最小值'}
                          ]
                      },
                      markLine : {
                          data : [
                              {type : 'average', name: '平均值'}
                          ]
                      },
                    }*/
                  ]
         };
         //选择一个空图表
         myChart.setOption(options);
     }
     ///点击按钮获取图表数据采用ajax方式
     function GetAjaxChartData() {
         //$("td[name='td_sjl']").show();
         //获得图表的options对象
         var options = myChart.getOption();
         //图表显示提示信息
         myChart.showLoading({
             text: "<?php echo L('L_ALERT_LOADING');?>..."
         });
         //通过Ajax获取数据
         $.ajax({
             type: "post",
             async: false,
             url: "/home/supervisory/drawline_search?sjjg="+$("#select_SJL_").val()+"&area="+$("#select_area_").val()+"&trap="+$("#input_t_n").val()+"&state="+$("#select_state_").val(),
             dataType: "json",
             success: function (res) {
                 var result=res;
                 if (result) {
                     options.xAxis[0].data = result.category;
                     options.series = result.series;
                     options.legend.data = result.legend;
                     myChart.hideLoading();
                     myChart.setOption(options);
                     setTimeout(function(){GetAjaxChartData();},5000);
                 }
             },
             error: function (errorMsg) {
                 alert("<?php echo L('L_ALERT_DATAERROR');?>!");
             }
         });
     }
     $(function(){
        $("[name='td_sjl']").hide();
        var trapno=getQueryString("trapid");
        $("#input_t_n").val(trapno);
        areabind();
        //$("td[name='td_sjl']").hide();
      });
      function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return "";
      }
      function Dline_Method(){
        if($("#select_area_").val()=="" && $("#input_t_n").val()=="" && $("#select_state_").val()==""){
          dline("");
        }else{
          $("#div_show_line").html('');
          DrawEChart();
          GetAjaxChartData();
        }

      }
      var myChart;
      function btn_search_click(){
        if($("#select_SJL_").val()=="d"){
          Start_L=90;
        }else if($("#select_SJL_").val()=="m"){
          Start_L=85;
        }else if($("#select_SJL_").val()=="y"){
          Start_L=80;
        }else if($("#select_SJL_").val()=="a"){
          Start_L=75;
        }
        if($("#select_area_").val()=="" && $("#input_t_n").val()=="" && $("#select_state_").val()==""){
          $("[name='td_sjl']").hide();
          dline("");
        }else{
          $("[name='td_sjl']").show();
          //dline_de();
          $("#div_show_line").html('');
          DrawEChart();
          GetAjaxChartData();
        }
      }
      function areabind(){
        var area_id="";
        if(getQueryString("areacode")!=""){
          area_id = ""+getQueryString("areacode").split("area_")[1]+"";
        }

        $.get("/home/supervisory/getareas",function(res){
          var area_array_=res.split('/');
          for(var i=0;i<area_array_.length;i++){
            if(area_array_[i]==""){break;}
            var check_="";
            if(area_id==area_array_[i].split(",")[0]){
              check_=" selected='true' ";
            }

            $("#select_area_").append("<option value='"+area_array_[i].split(",")[0]+"' "+check_+">"+area_array_[i].split(",")[1]+"</option>");
          }

          DrawEChart();
          Dline_Method();
        });

      }
      //var myChart; 过时的
      function dline_de(){
        $("#div_show_line").html("");
        $.get("/home/supervisory/drawline_search?area="+$("#select_area_").val()+"&trap="+$("#input_t_n").val()+"&state="+$("#select_state_").val(),function(res){
          //myChart = echarts.init(document.getElementById('div_show_line'),'infographic');
          eval(res);
        });
      }
      function dline(keys){
        $("#div_show_line").html("");
        $.get("/home/supervisory/drawline"+keys,function(res){
          var all_div=5;
          for(var i=0;i<all_div;i++){
            $("#div_show_line").append("<div  style='width: 100%; height:250px;' id='div_show_line_"+i+"'></div>")
          }
          eval(res);
        });
      }
      var mouse_loca=false;
      /*$(document).on("mousewheel DOMMouseScroll", function (e) {
          return;
          if($("#select_area_").val()=="" && $("#input_t_n").val()=="" && $("#select_state_").val()==""){
            return;
          }
          if(!mouse_loca){
            return;
          }
          var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) ||  // chrome & ie
                      (e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1));              // firefox
          var options = myChart.getOption();
          var op_dz = options.dataZoom[0];
          var Speed=2;
          if (delta > 0) {
              // 向上滚
              if(op_dz.end>op_dz.start){
                if(op_dz.end!=100){
                  op_dz.end-=Speed;
                  if(op_dz.end<=op_dz.start){
                    op_dz.end=op_dz.start;
                  }
                }
              }
              if(op_dz.start<op_dz.end){
                op_dz.start+=Speed;
                if(op_dz.start>=op_dz.end){
                  op_dz.start=op_dz.end;
                }
              }
              Start_L=op_dz.start;
              End_L=op_dz.end;
              myChart.setOption(options);
              //alert("aaaa");
          } else if (delta < 0) {
              // 向下滚
              if(op_dz.end<100){
                op_dz.end+=Speed;
                if(op_dz.end>100){
                  op_dz.end=100;
                }
              }
              if(op_dz.start>0){
                op_dz.start-=Speed;
                if(op_dz.start<0){
                  op_dz.start=0;
                }
              }
              Start_L=op_dz.start;
              End_L=op_dz.end;
              myChart.setOption(options);
              //alert("bbbb");
          }
      });*/

    </script>
</head>
<body>
    <h1 style="font-size:14px;"><?php echo L('L_MENU_SUPER_PIC');?></h1>
  <table class="tab_search">
    <tr>
      <td><?php echo L("L_AREA_QY");?></td>
      <td>
        <select class="input_search" id="select_area_">
          <option value=""><?php echo L("L_ALERT_TJ_ALL");?></option>
        </select>
      </td>
      <td>&nbsp;</td>
      <td><?php echo L("L_AREA_JDBH");?></td>
      <td><input type="text" value="" id="input_t_n" class="input_search"/></td>
      <td name="td_sjl">&nbsp;</td>
      <td name="td_sjl"><?php echo L("L_AREA_SJL");?></td>
      <td name="td_sjl">
        <select class="input_search" id="select_SJL_">
          <option value="a"><?php echo L("L_ALERT_TJ_ALL");?></option>
          <option value="d"><?php echo L("L_AREA_DANGTIAN");?></option>
          <option value="m"><?php echo L("L_AREA_BENYUENEI");?></option>
          <option value="y"><?php echo L("L_AREA_BENNIANNEI");?></option>
          <!--<option value="a"><?php echo L("L_ALERT_TJ_ALL");?></option>-->
        </select>
      </td>
      <td>&nbsp;</td>
      <td><?php echo L("L_AREA_DQZT");?></td>
      <td>
        <select class="input_search" id="select_state_">
          <option value=""><?php echo L("L_ALERT_TJ_ALL");?></option>
          <option value="0"><?php echo L("L_ALERT_TJ_WORK");?></option>
          <option value="1"><?php echo L("L_ALERT_TJ_EXC");?></option>
        </select>
      </td>
      <td>&nbsp;</td>
      <td><input type="button" value=" <?php echo L('L_SEARCH_SEARCH');?> " onclick="btn_search_click()" class="btn_search" /></td>
    </tr>
  </table>
   <div id="div_show_line" onmouseover="javascript:mouse_loca=true;" onmouseout="javascript:mouse_loca=false;" style="width: 100%; height:500px"></div>
</body>
</html>