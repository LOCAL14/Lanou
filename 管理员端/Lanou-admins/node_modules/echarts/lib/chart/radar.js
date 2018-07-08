var echarts = require("../echarts");

require("../component/radar");

require("./radar/RadarSeries");

require("./radar/RadarView");

var dataColor = require("../visual/dataColor");

var visualSymbol = require("../visual/symbol");

var radarLayout = require("./radar/radarLayout");

var dataFilter = require("../processor/dataFilter");

var backwardCompat = require("./radar/backwardCompat");

// Must use radar component
echarts.registerVisual(dataColor('radar'));
echarts.registerVisual(visualSymbol('radar', 'circle'));
echarts.registerLayout(radarLayout);
echarts.registerProcessor(dataFilter('radar'));
echarts.registerPreprocessor(backwardCompat);