<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>通过率</title>
    <link rel="stylesheet" type="text/css" href="styles/iview.css">
    <script src="vue.min.js"></script>
    <script type="text/javascript" src="iview.min.js"></script>
    <script src="axios.min.js"></script>
    <script src="echarts.min.js"></script>
</head>
<body style="width:1002px;position:relative;left:-20px;">
<style>
#title{
  font-size:50px;
  font-family:verdana;
  text-align:center;
  background-color:#F8F8FF;
  font-weight:bold;
  position:relative;
  top:-50px;
}
.ivu-select-single .ivu-select-selection .ivu-select-placeholder, .ivu-select-single .ivu-select-selection .ivu-select-selected-value{
    display: block;
    height: 30px;
    line-height: 30px;
    font-size: 25px;
    font-weight: bold;
}
.ivu-select-item {
    margin: 0;
    line-height: normal;
    padding: 7px 16px;
    clear: both;
    color: #495060;
    font-size: 25px!important;
    font-weight: bold;
    white-space: nowrap;
    list-style: none;
    cursor: pointer;
    transition: background .2s ease-in-out;
}

/*.ivu-select-large.ivu-select-single .ivu-select-selection .ivu-select-placeholder, .ivu-select-large.ivu-select-single .ivu-select-selection .ivu-select-selected-value {
    height: 34px;
    line-height: 34px;
}*/
</style>
<div id="app">
		<div align="center">
			<img width="100%" height="150" src="logo.png"/>
		</div>
    <p id="title">通过率24小时历史曲线</p>
    <span style="position:relative;left:720px;">
    	<span style="font-family:verdana;font-size:25px;font-weight: bold;position:relative;top:5px;left:-10px;">位置选择:</span>
			<i-select v-model="sPosition" @on-change="changeDateHandle" style="width:150px">
	        <i-option v-for="item in PositionList" :value="item.value" :key="item.value">{{ item.label }}</i-option>
	    </i-select>
		</span>
    <div id="main" style="width:960px;height:800px;position:relative;top:10px;left:20px;"></div>
    <p style="font-family:verdana;font-size:35px;text-align:center;position:relative;top:20px;">注：数据采样率为1次/分钟</p>
    <p style="font-family:verdana;font-size:35px;text-align:center;position:relative;top:20px;"><span style="color:blue;">蓝色：DTL；</span><span style="color:red;">红色：RCS</p>
</div>
<script>
///////////////vue.js相关程序///////////////
    var Main = {
        data () {
            return {
                sPosition: '0',
                PositionList: [
                  {
                    value: '0',
                    label: '全部'
                  },
                  {
                    value: '1',
                    label: 'DTL'
                  },
                  {
                    value: '2',
                    label: 'RCS'
                  }
                ]
            }
       },
     	  mounted() {//打开网页初始化执行的函数
  		},
	      methods: {
	      	changeDateHandle:function() {//切换测量位置
	      		var self = this;
					  myChart.setOption({legend: {selected:{'DTL':false,'RCS': false}}});
					  switch(self.sPosition)
					  {
					  	case '0':
					  		myChart.setOption({legend: {selected:{'DTL':true,'RCS': true}}});
					  		break;
					  	case '1':
					  		myChart.setOption({legend: {selected:{'DTL': true}}});
					  		break;
					  	case '2':
					  		myChart.setOption({legend: {selected:{'RCS': true}}});
					  		break;
					  }
					}
	  		}
    };

var Component = Vue.extend(Main);
new Component().$mount('#app');
</script>
    <script type="text/javascript">
	    var date = [];//x轴用当前时间生成，y轴数据由php发出http请求获得(函数的参数)
			var base = new Date();//获取现在的时间
			var oneDay = 24 * 3600 * 1000;
			var halfDay = 12 * 3600 * 1000;
			//var oneHours = 3600 * 1000;
			var oneMin = 60*1000;
			var halfMin = 30*1000;
			var now = new Date(base -= oneDay);//从过去24小时开始算起，每分钟一个点，填充时间数组
			for (var i = 0; i < 1440; i++) {
			  now = new Date(+now + oneMin);
			  
			  date.push([now.getHours(), now.getMinutes()].join(':')+" \n"+[(now.getMonth()+1), now.getDate()].join('/'));
			}
	    var data=<?php
			require_once dirname(__FILE__) . "/helper.php";
			$res=http_get("http://10.1.236.136:8080/accst/dtltrans");
			$hidatas=json_decode($res['content'],true);
			echo '[';
			for($i=0;$i<1439;$i++)//因为最后一个数字不需要逗号，所以不能用foreach
			{
				echo $hidatas[$i];
				echo ',';
			}
			echo $hidatas[1439];
			echo ']';
			?>;
			var data2=<?php
			require_once dirname(__FILE__) . "/helper.php";
			$res=http_get("http://10.1.236.136:8080/accst/rcstrans");
			$hidatas=json_decode($res['content'],true);
			echo '[';
			for($i=0;$i<1439;$i++)
			{
				echo $hidatas[$i];
				echo ',';
			}
			echo $hidatas[1439];
			echo ']';
			?>;
				    // 基于准备好的dom，初始化echarts实例
	  var myChart = echarts.init(document.getElementById('main'));
	
	  // 指定图表的配置项和数据
	  var option = {
	    title: {
	        //text: '历史曲线',
	        //textAlign: 'center'
	    },
	    tooltip: {//提示框
	        show: true,
	        trigger: 'axis',
	        //trigger: 'line',
	       // position:function (point, params, dom, rect, size) {return [point[0], '10%'];},//提示框固定在顶部
	       	position:function (point, params, dom, rect, size)//提示框位置
	       	{
						if(point[0]>500)
						{
						return [point[0]-280, '10%'];
						}else
						{
							return [point[0], '10%'];
						}
	       	},
	        axisPointer: {//指针
	          type: 'cross'
	      	},
	        textStyle:
	        {
	        	fontSize:27,
	        	fontWeight:'bold'
	        }
	    },
	/*			    toolbox: {//工具箱
	        show: true,
	        feature: {
	        dataZoom: {
	            yAxisIndex: 'none'
	        },
	        restore: {},
	        saveAsImage: {},
	        dataView: {}
	    		}
	    },*/
	    xAxis: {
	        type: 'category',
	        name: '时间',
	        nameTextStyle://坐标轴名字格式
	        {
	        	fontSize: 27,
	        	fontWeight:'bold'
	        },
	        axisLabel:{ //坐标轴数值格式
	        	fontSize:27,
	        	fontWeight:'bold'
	      	},
	        boundaryGap: false,
	        //boundaryGap: [0, '100%'],
	        splitLine: {//竖的分割线
	            show: true
	        },
	        axisLine:{//坐标轴的格式
		        lineStyle:{
		            width:3
		        }
	  		  },
	  		  axisPointer://跟随鼠标的指针（竖直那根）
	  		  {
	  		  	snap: true,//自动跟随数据
	  		  	lineStyle:{
	  		  		width:3,
	  		  		color: '#000',
	  		  		type: 'dashed'
	  		  	},
	  		  	handle:{//拖动手柄
	  		  		show: true,
	  		  		size: 45,
	  		  		margin: 100//偏移轴的距离，100已经看不见了，但触屏操作的效果还在
	  		  	},
	  		  	label:{//指针下方的标签
	  		  		show: true,
	  		  		fontSize: 27
	  		  	}
	  		  },
	        data: date
	    },
	    yAxis: {
	        type: 'value',
	        name: '通过率(%)',
	        nameTextStyle:
	        {
	        	fontSize: 27,
	        	fontWeight:'bold'
	        },
	        //boundaryGap: [0, '100%'],
	        boundaryGap: false,//上下不留白
	        splitLine: {
	            show: true
	        },
	        axisLine:{
		        lineStyle:{
		            //color:'yellow',
		            width:3
		        }
	  			},
	        axisLabel: {
	        	formatter: '{value}',
	        	fontSize:27,
	        	fontWeight:'bold'
	  			},
	  		  axisPointer:
	  		  {
	  		  	show: true,
	  		  	snap: false,
	  		  	lineStyle:{
	  		  		width:3,
	  		  		color: '#000',
	  		  		type: 'dashed'
	  		  	},
	  		  	label:{
	  		  		show: true,
	  		  		fontSize: 27
	  		  	}
	  		  },
	  		  max: 105,
	  		  min: 90
	    },
	    legend: {
        data: ['DTL', 'RCS']
    	},
	    series: [
	    {
      		name: 'DTL',
      		type: 'line',
          data: data,
          color: 'blue',
          width: 2
      },
      {
      		name: 'RCS',
      		type: 'line',
          data: data2,
          color: 'red',
          width: 2
      }
		  ],
				     dataZoom: [
	//	        {
	//	            type: 'slider',
	//	            xAxisIndex: 0,
	//	            filterMode: 'empty'
	//	        },
	//	        {
	//	            type: 'slider',
	//	            yAxisIndex: 0,
	//	            filterMode: 'empty'
	//	        },
		        {
		            type: 'inside',
		            xAxisIndex: 0,
		            filterMode: 'empty'
		        }
	//	        {
	//	            type: 'inside',
	//	            yAxisIndex: 0,
	//	            filterMode: 'empty'
	//	        }
		    		]
	};
	
	  // 使用刚指定的配置项和数据显示图表。
	  myChart.setOption(option);
    </script>
</body>
</html>