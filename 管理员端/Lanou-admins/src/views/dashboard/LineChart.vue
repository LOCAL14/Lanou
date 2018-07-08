<template>
  <div :class="className" :style="{height:height,width:width}"></div>
</template>

<script>
import echarts from 'echarts'
require('echarts/theme/macarons') // echarts theme
import { debounce } from '@/utils'

export default {
  props: {
    className: {
      type: String,
      default: 'chart'
    },
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '350px'
    },
    autoResize: {
      type: Boolean,
      default: true
    },
    chartData: {
      type: Object
    }
  },
  data() {
    return {
      chart: null
    }
  },
    computed:{
      date:function(){
          var now = new Date();
          var date = now.getDate();//得到日期
          return date
      }

    },
  mounted() {
    this.initChart()
    if (this.autoResize) {
      this.__resizeHanlder = debounce(() => {
        if (this.chart) {
          this.chart.resize()
        }
      }, 100)
      window.addEventListener('resize', this.__resizeHanlder)
    }

    // 监听侧边栏的变化
    const sidebarElm = document.getElementsByClassName('sidebar-container')[0]
    sidebarElm.addEventListener('transitionend', this.__resizeHanlder)
  },
  beforeDestroy() {
    if (!this.chart) {
      return
    }
    if (this.autoResize) {
      window.removeEventListener('resize', this.__resizeHanlder)
    }

    const sidebarElm = document.getElementsByClassName('sidebar-container')[0]
    sidebarElm.removeEventListener('transitionend', this.__resizeHanlder)

    this.chart.dispose()
    this.chart = null
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.setOptions(val)
      }
    }
  },
  methods: {
    setOptions({ aData, bData } = {}) {
      this.chart.setOption({
        xAxis: {
          data: [this.date-6+'日', this.date-5+'日', this.date-4+'日', this.date-3+'日',
              this.date-2+'日', this.date-1+'日', this.date+'日'], //前七天的日期
          boundaryGap: false,
          axisTick: {
            show: false
          }
        },
        grid: {
          left: 10,
          right: 10,
          bottom: 20,
          top: 30,
          containLabel: true
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross'
          },
          padding: [5, 10]
        },
        yAxis: {
          axisTick: {
            show: false
          }
        },
        legend: {
          data: ['低评价订单', '新增订单']
        },
        series: [{
          name: '低评价订单', itemStyle: {
            normal: {
              color: '#FF9800',
              lineStyle: {
                color: '#FF9800',
                width: 2
              }
            }
          },
          smooth: true,
          type: 'line',
          data: aData,
          animationDuration: 2800,
          animationEasing: 'cubicInOut'
        },
        {
          name: '新增订单',
          smooth: true,
          type: 'line',
          itemStyle: {
            normal: {
              color: '#2DCB70',
              lineStyle: {
                color: '#2DCB70',
                width: 2
              },
              areaStyle: {
                color: '#f3fff8'
              }
            }
          },
          data: bData,
          animationDuration: 2800,
          animationEasing: 'quadraticOut'
        }]
      })
    },
    initChart() {
      this.chart = echarts.init(this.$el, 'macarons')
      this.setOptions(this.chartData)
    }
  }
}
</script>
