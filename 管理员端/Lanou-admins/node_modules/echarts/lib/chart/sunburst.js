var echarts = require("../echarts");

var zrUtil = require("zrender/lib/core/util");

require("./sunburst/SunburstSeries");

require("./sunburst/SunburstView");

require("./sunburst/sunburstAction");

var dataColor = require("../visual/dataColor");

var sunburstLayout = require("./sunburst/sunburstLayout");

var dataFilter = require("../processor/dataFilter");

echarts.registerVisual(zrUtil.curry(dataColor, 'sunburst'));
echarts.registerLayout(zrUtil.curry(sunburstLayout, 'sunburst'));
echarts.registerProcessor(zrUtil.curry(dataFilter, 'sunburst'));