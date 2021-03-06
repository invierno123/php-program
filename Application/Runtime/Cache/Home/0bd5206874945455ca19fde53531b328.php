<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
  <title></title>
  <style type="text/css">
    body {
      font: 10pt sans;
    }
    #mygraph {
      width:100%;
      border: 1px solid lightgray;
    }
  </style>
  <script src='/Public/js/jquery.js' ></script>
  <link href="/Public/css/jquery-fallr-1.3.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="/Public/js/jquery-fallr-1.3.pack.js"></script>
  <script type="text/javascript" src="/Public/js/dist/vis.js"></script>
  <script>
  $(function(){
    $("#mygraph").css("height",$(window).height()-50);
  });
  </script>
  <script type="text/javascript">

    var nodes = null;
    var edges = null;
    var graph = null;

    function draw() {
      nodes = [];
      edges = [];
      var connectionCount = [];

      // randomly create some nodes and edges
      var nodeCount = 35;//document.getElementById('nodeCount').value;
      nodes.push(<?php echo ($NODES); ?>);
      edges.push(<?php echo ($EDGES); ?>);
      // create a graph
      var container = document.getElementById('mygraph');
      var data = {
        nodes: nodes,
        edges: edges
      };

    var options = {
    edges: {
      width:3
    },
    stabilize: false,
    smoothCurves:false
    //configurePhysics:true,
    /*hierarchicalLayout: {
        enabled:true,
        direction: "UD",
        nodeSpacing: 100,
        levelSeparation: 150,
    }*/
    };
    graph = new vis.Graph(container, data, options);

    // add event listeners
    graph.on('select', function(params) {
      //params.nodes;
    });
    graph.on('doubleClick', function(params) {
      //alert(params.nodes)

      if((params.nodes+"").indexOf("-")>-1||(params.nodes+"")==""){return;}
      $.fallr("show", {
    			content: "<iframe src='/home/Supervisory?t=line&trapid="+params.nodes+"' frameborder='0' width='100%' height='100%'></iframe>",
    			position: "center",
    			height:"90%",
    			width:"90%",
    			buttons:{
    				button1: {
    						text: "<?php echo L('L_ALERT_CANCEL');?>",
    						onclick: function () {
    							$.fallr("hide");
    						}
    				}
    			}
    	});
    });
    }
  </script>
</head>

<body onload="draw();">
    <h1 style="font-size:14px;"><?php echo L('L_MENU_SUPER_2D');?></h1>


<div id="mygraph"></div>
</body>
</html>