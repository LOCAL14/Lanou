var echarts = require("../../echarts");

var _sourceHelper = require("../../data/helper/sourceHelper");

var detectSourceFormat = _sourceHelper.detectSourceFormat;

var _sourceType = require("../../data/helper/sourceType");

var SERIES_LAYOUT_BY_COLUMN = _sourceType.SERIES_LAYOUT_BY_COLUMN;
var DatasetModel = echarts.extendComponentModel({
  type: 'dataset',

  /**
   * @protected
   */
  defaultOption: {
    // 'row', 'column'
    seriesLayoutBy: SERIES_LAYOUT_BY_COLUMN,
    // null/'auto': auto detect header, see "module:echarts/data/helper/sourceHelper"
    sourceHeader: null,
    dimensions: null,
    source: null
  },
  optionUpdated: function () {
    detectSourceFormat(this);
  }
});
var _default = DatasetModel;
module.exports = _default;