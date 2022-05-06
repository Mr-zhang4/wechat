<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes"/>
    <title>打靶供束时间统计</title>
    <link rel="stylesheet" type="text/css" href="styles/iview.css">
    <script src="vue.min.js"></script>
    <script type="text/javascript" src="iview.min.js"></script>
    <script src="axios.min.js"></script>
    <script src="echarts.min.js"></script>
</head>
<body style="width:100%;">
<style>
.ivu-tabs-tab{/*这个class使用chrome浏览器f12调试工具找到，是标签页选项的class*/
   font-family:verdana;
   font-size:14px;
   font-weight:bold;
}

#title{
  font-size:20px;
  font-family:verdana;
  text-align:center;
  background-color:#F8F8FF;
  font-weight:bold;
}
</style>
<div align="center">
	<img width="100%" height="55" src="logo.png"/>
</div>
<p id="title">打靶供束时间统计</p>
<br>
<div id="app" style="position:relative;top:-8px;">
	<tabs type="card">
		<tab-pane label="按天统计"><!-- 标签页1的内容（按天统计） -->
					<div align="center" style="position:relative;top:-5px;">
		  			<span><font size="3" face="verdana">开始时间:</font></span>
		  			<i-input type="date" v-model="startDate" @on-change="changeDateHandle" style="width:150px;"></i-input><br>
						<span><font size="3" face="verdana">结束时间:</font></span>
						<i-input type="date" v-model="endDate" @on-change="changeDateHandle" style="width:150px;"></i-input><br>
			    </div>
		  <br>
			 <div style="font-family:verdana;font-size:14px;font-weight:bold;text-align:center;position:relative;top:-13px;z-index:-1;">
		    	<span>选中日期累计供束时间：</span>
		    	<span style="color:blue;">
		    	{{timeSum}}
					</span>
					<span> 小时</span>
   		</div>
			<div id="sumtable" style="width:360px;height:360px;margin:0 auto;position:relative;top:-53px;"></div><!-- 曲线 -->
			<p style="font-family:verdana;font-size:14px;text-align:center;position:relative;top:-90px;">注：每天8:00统计过去24小时供束时间</p>
		</tab-pane>
	  <tab-pane label="按秒统计"><!-- 标签页2的内容（精确到秒统计） -->
	  	<row type="flex" align="middle"><!-- 选择开始和结束时间 -->
		  		<i-col span="5" offset="4"><p><font size="3" face="verdana">开始时间:</font></p></i-col>
			    <i-col span="10"><date-picker type="datetime" @on-change="tab2ChangeDateStart"></date-picker></i-col>
			</row>
			<br>
			<row type="flex" align="middle">
					<i-col span="5" offset="4"><p><font size="3" face="verdana">结束时间:</font></p></i-col>
			    <i-col span="10"><date-picker type="datetime" @on-change="tab2ChangeDateEnd"></date-picker></i-col>
		  </row>
		  <br>
		  <p style="font-family:verdana;font-size:20px;text-align:center;">累计出束时间：{{tab2TimeSum}}小时</p><!-- 输出累计出束时间 -->
	  </tab-pane>
  </tabs>
</div>

<script>
/////////////以下为vue.js相关程序///////////////////////////////////////////////

/////////获取当天日期和6天前日期/////////////////////
function getFormatDate(AddDayCount) {
		var oneDay = -24 * 3600 * 1000;
		var xDay= oneDay * AddDayCount;
    var base = new Date();
    var date = new Date(base -= xDay);
    var seperator1 = "-";
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = year + seperator1 + month + seperator1 + strDate;
    return currentdate;
}
var getStartDate=getFormatDate(-6);//初始日期是从过去6天到今天，共7天时间
var getEndDate=getFormatDate(0);
///////////////////////////////////////////////////////////////
    	  
    var Main = {
        data () {
            return {
            		//以下为tab1用到的数据
                timeSum:'(未选择日期)',
                timeSumList:'',
                dateList:'',
                startDate: getStartDate,
                endDate: getEndDate,
                //以下为tab2用到的数据
                tab2TimeSum:'(未选择日期)',
                tab2StartDate:'',
                tab2EndDate:'',
                //测试数据
                testMessage: 'test'
            }
       },
     	  mounted() {/////////////////////////打开网页初始化执行的函数（tab1按日期统计默认查看7天数据）/////////////////////////
	  			var self = this;
			  	axios.get('./getTimeSum.php', {
				    params: {
				      sdate: self.startDate,
				      edate: self.endDate
				    }
				  })
				  .then(function (response) {
				    console.log(response);
				    self.timeSum=response.data;
				    myChart.setOption({
		        title: {
		            text: '7天累计出束:'+self.timeSum+'小时'
		        }
		    		});
				  })
				  .catch(function (error) {
				    console.log(error);
				  });
				  ////////////获取出束时间列表，并更新曲线y轴//////////
			  	axios.get('./getTimeList.php', {
				    params: {
				      sdate: self.startDate,
				      edate: self.endDate
				    }
				  })
				  .then(function (response) {//回调函数，更新曲线y轴
				    console.log(response);
				    self.timeSumList=response.data;
				    myChart.setOption({
		        series: [{
		            data: self.timeSumList
		        }]
		    		});
				  })
				  .catch(function (error) {
				    console.log(error);
				  });
				  ////////////获取日期列表，并更新曲线x轴//////////
			  	axios.get('./getDaterangeList.php', {
				    params: {
				      sdate: self.startDate,
				      edate: self.endDate
				    }
				  })
				  .then(function (response) {//回调函数，更新曲线y轴
				    console.log(response);
				    self.dateList=response.data;
				    myChart.setOption({
		        xAxis: {
		            data: self.dateList
		        }
		    		});
				  })
				  .catch(function (error) {
				    console.log(error);
				  });
  		},
	      methods: {//////////////////////////////////////////绑定的数据改变处理的函数////////////////////////////
					  	changeDateHandle:function() {//tab1(按天统计)修改日期的处理函数
					  	var self = this;
					  	//this.message=daterange;
					  	////////////获取累计出束时间//////////
					  	axios.get('./getTimeSum.php', {
						    params: {
						      sdate: self.startDate,
						      edate: self.endDate
						    }
						  })
						  .then(function (response) {
						    console.log(response);
						    self.timeSum=response.data;
						    myChart.setOption({
				        title: {
				            text: '选中日期累计出束:'+self.timeSum+'小时'
				        }
				    		});
						  })
						  .catch(function (error) {
						    console.log(error);
						  });
						  ////////////获取出束时间列表，并更新曲线y轴//////////
					  	axios.get('./getTimeList.php', {
						    params: {
						      sdate: self.startDate,
						      edate: self.endDate
						    }
						  })
						  .then(function (response) {//回调函数，更新曲线y轴
						    console.log(response);
						    self.timeSumList=response.data;
						    myChart.setOption({
				        series: [{
				            data: self.timeSumList
				        }]
				    		});
						  })
						  .catch(function (error) {
						    console.log(error);
						  });
						  ////////////获取日期列表，并更新曲线x轴//////////
					  	axios.get('./getDaterangeList.php', {
						    params: {
						      sdate: self.startDate,
						      edate: self.endDate
						    }
						  })
						  .then(function (response) {//回调函数，更新曲线y轴
						    console.log(response);
						    self.dateList=response.data;
						    myChart.setOption({
				        xAxis: {
				            data: self.dateList
				        }
				    		});
						  })
						  .catch(function (error) {
						    console.log(error);
						  });
						},
						tab2ChangeDateStart:function(date) {//标签2（按秒统计）修改起始日期的处理函数
					  	var self = this;
					  	self.tab2StartDate=date;
					  	//this.message=daterange;
					  	////////////获取累计出束时间//////////
					  	axios.get('./tab2_getdata_fromserver.php', {
						    params: {
						      sdate: self.tab2StartDate,
						      edate: self.tab2EndDate
						    }
						  })
						  .then(function (response) {
						    console.log(response);
						    self.tab2TimeSum=response.data.sum;
						  })
						  .catch(function (error) {
						    console.log(error);
						  });
						},
						tab2ChangeDateEnd:function(date) {//标签2（按秒统计）修改结束日期的处理函数
					  	var self = this;
					  	self.tab2EndDate=date;
					  	//this.message=daterange;
					  	////////////获取累计出束时间//////////
					  	axios.get('./tab2_getdata_fromserver.php', {
						    params: {
						      sdate: self.tab2StartDate,
						      edate: self.tab2EndDate
						    }
						  })
						  .then(function (response) {
						    console.log(response);
						    self.tab2TimeSum=response.data.sum;
						  })
						  .catch(function (error) {
						    console.log(error);
						  });
						}
	  		}
    }

var Component = Vue.extend(Main)
new Component().$mount('#app')
</script>

<script type="text/javascript">
///////////////////////////以下为曲线程序////////////////////////////////////////////
//实测如果后执行vue程序会导致曲线显示不出来
var tablex = ['00-00','00-00','00-00','00-00','00-00'];//x轴
var tabley = [0,0,0,0,0];//y轴
var myChart = echarts.init(document.getElementById('sumtable'));

  // 指定图表的配置项和数据
  var option = {
    title: {
        text: '请选择日期',
        //textAlign: 'center',
        left:'center',
        show: false,
        textStyle:
        {
        	fontSize:14,
        	fontWeight:'bold'
        }
    },
    tooltip: {//提示框
        show: true,
        trigger: 'axis',
        //trigger: 'line',
       // position:function (point, params, dom, rect, size) {return [point[0], '10%'];},//提示框固定在顶部
       	position:function (point, params, dom, rect, size)//提示框位置
       	{
					if(point[0]>280)
					{
					return [point[0]-40, '15%'];
					}else
					{
						return [point[0], '15%'];
					}
       	},
        axisPointer: {//指针
          type: 'cross'
      	},
        textStyle:
        {
        	fontSize:10,
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
        name: '',
        nameTextStyle://坐标轴名字格式
        {
        	fontSize: 10,
        	fontWeight:'bold'
        },
        axisLabel:{ //坐标轴数值格式
        	fontSize:10,
        	fontWeight:'bold'
      	},
        boundaryGap: false,
        //boundaryGap: [0, '100%'],
        splitLine: {//竖的分割线
            show: true
        },
        axisLine:{//坐标轴的格式
	        lineStyle:{
	            width:1
	        }
  		  },
  		  axisPointer://跟随鼠标的指针（竖直那根）
  		  {
  		  	snap: true,//自动跟随数据
  		  	lineStyle:{
  		  		width:1,
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
  		  		fontSize: 10
  		  	}
  		  },
        data: tablex
    },
    yAxis: {
        type: 'value',
        name: '',
        nameTextStyle:
        {
        	fontSize: 10,
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
	            width:1
	        }
  			},
        axisLabel: {
        	formatter: '{value}',
        	fontSize:10,
        	fontWeight:'bold'
  			},
  		  axisPointer:
  		  {
  		  	show: true,
  		  	snap: false,
  		  	lineStyle:{
  		  		width:1,
  		  		color: '#000',
  		  		type: 'dashed'
  		  	},
  		  	label:{
  		  		show: true,
  		  		fontSize: 10
  		  	}
  		  },
  		  max: 24,
  		  min: 0
    },
    series: [{//数值曲线配置
        name: '',
        type: 'line',
        //showSymbol: false,
        //hoverAnimation: false,
        data: tabley,
        lineStyle: {//曲线格式
        	normal: {
            color: 'blue',
            width: 1,
        	}
      	}
    }]
//			     dataZoom: [
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
//	        {
//	            type: 'inside',
//	            xAxisIndex: 0,
//	            filterMode: 'empty'
//	        },
//	        {
//	            type: 'inside',
//	            yAxisIndex: 0,
//	            filterMode: 'empty'
//	        }
//	    		]
};

  // 使用刚指定的配置项和数据显示图表。
  myChart.setOption(option);
</script>
</body>
</html>
