var echarts = require("../echarts");

require("../component/singleAxis");

require("./themeRiver/ThemeRiverSeries");

require("./themeRiver/ThemeRiverView");

var themeRiverLayout = require("./themeRiver/themeRiverLayout");

var themeRiverVisual = require("./themeRiver/themeRiverVisual");

var dataFilter = require("../processor/dataFilter");

echarts.registerLayout(themeRiverLayout);
echarts.registerVisual(themeRiverVisual);
echarts.registerProcessor(dataFilter('themeRiver'));