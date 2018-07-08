var echarts = require("../echarts");

require("./effectScatter/EffectScatterSeries");

require("./effectScatter/EffectScatterView");

var visualSymbol = require("../visual/symbol");

var layoutPoints = require("../layout/points");

echarts.registerVisual(visualSymbol('effectScatter', 'circle'));
echarts.registerLayout(layoutPoints('effectScatter'));