var echarts = require("../echarts");

require("./line/LineSeries");

require("./line/LineView");

var visualSymbol = require("../visual/symbol");

var layoutPoints = require("../layout/points");

var dataSample = require("../processor/dataSample");

require("../component/gridSimple");

// In case developer forget to include grid component
echarts.registerVisual(visualSymbol('line', 'circle', 'line'));
echarts.registerLayout(layoutPoints('line')); // Down sample after filter

echarts.registerProcessor(echarts.PRIORITY.PROCESSOR.STATISTIC, dataSample('line'));