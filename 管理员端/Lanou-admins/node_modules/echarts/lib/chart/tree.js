var echarts = require("../echarts");

require("./tree/TreeSeries");

require("./tree/TreeView");

require("./tree/treeAction");

var visualSymbol = require("../visual/symbol");

var orthogonalLayout = require("./tree/orthogonalLayout");

var radialLayout = require("./tree/radialLayout");

echarts.registerVisual(visualSymbol('tree', 'circle'));
echarts.registerLayout(orthogonalLayout);
echarts.registerLayout(radialLayout);