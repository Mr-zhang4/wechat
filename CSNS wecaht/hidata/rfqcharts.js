document.write("<script language='javascript' src='echarts.min.js'></script>");
function addcharts(data,date)//data为y轴,date为x轴
{
    // 基于准备好的dom，初始化echarts实例
  var myChart = echarts.init(document.getElementById('main'));

  // 指定图表的配置项和数据
  var option = {
    title: {
        //text: 'RFQ入腔功率24小时历史曲线',
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
        name: '功率(kW)',
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
    },
    series: [{//数值曲线配置
        name: 'Power',
        type: 'line',
        //showSymbol: false,
        //hoverAnimation: false,
        data: data,
        lineStyle: {//曲线格式
        	normal: {
            color: 'blue',
            width: 2,
        	}
      	}
    }],
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
}