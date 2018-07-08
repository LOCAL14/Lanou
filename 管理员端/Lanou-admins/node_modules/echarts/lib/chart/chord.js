var echarts = require("../echarts");

require("./chord/ChordSeries");

require("./chord/ChordView");

var chordCircularLayout = require("./chord/chordCircularLayout");

var dataColor = require("../visual/dataColor");

var dataFilter = require("../processor/dataFilter");

echarts.registerLayout(chordCircularLayout);
echarts.registerVisual(dataColor('chord'));
echarts.registerProcessor(dataFilter('pie'));