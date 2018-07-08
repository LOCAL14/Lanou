var echarts = require("../echarts");

var zrUtil = require("zrender/lib/core/util");

require("../coord/cartesian/Grid");

require("./bar/PictorialBarSeries");

require("./bar/PictorialBarView");

var _barGrid = require("../layout/barGrid");

var layout = _barGrid.layout;

var visualSymbol = require("../visual/symbol");

require("../component/gridSimple");

// In case developer forget to include grid component
echarts.registerLayout(zrUtil.curry(layout, 'pictorialBar'));
echarts.registerVisual(visualSymbol('pictorialBar', 'roundRect'));