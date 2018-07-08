var echarts = require("../echarts");

require("./funnel/FunnelSeries");

require("./funnel/FunnelView");

var dataColor = require("../visual/dataColor");

var funnelLayout = require("./funnel/funnelLayout");

var dataFilter = require("../processor/dataFilter");

echarts.registerVisual(dataColor('funnel'));
echarts.registerLayout(funnelLayout);
echarts.registerProcessor(dataFilter('funnel'));