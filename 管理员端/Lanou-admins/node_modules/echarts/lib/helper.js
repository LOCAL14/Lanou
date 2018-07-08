var zrUtil = require("zrender/lib/core/util");

var createListFromArray = require("./chart/helper/createListFromArray");

var axisHelper = require("./coord/axisHelper");

var axisModelCommonMixin = require("./coord/axisModelCommonMixin");

var Model = require("./model/Model");

var _layout = require("./util/layout");

var getLayoutRect = _layout.getLayoutRect;
exports.getLayoutRect = _layout.getLayoutRect;

var _completeDimensions = require("./data/helper/completeDimensions");

exports.completeDimensions = _completeDimensions;

var _createDimensions = require("./data/helper/createDimensions");

exports.createDimensions = _createDimensions;

var _symbol = require("./util/symbol");

exports.createSymbol = _symbol.createSymbol;

// import createGraphFromNodeEdge from './chart/helper/createGraphFromNodeEdge';

/**
 * Create a muti dimension List structure from seriesModel.
 * @param  {module:echarts/model/Model} seriesModel
 * @return {module:echarts/data/List} list
 */
function createList(seriesModel) {
  return createListFromArray(seriesModel.getSource(), seriesModel);
} // export function createGraph(seriesModel) {
//     var nodes = seriesModel.get('data');
//     var links = seriesModel.get('links');
//     return createGraphFromNodeEdge(nodes, links, seriesModel);
// }


/**
 * Create scale
 * @param {Array.<number>} dataExtent
 * @param {Object|module:echarts/Model} option
 */
function createScale(dataExtent, option) {
  var axisModel = option;

  if (!Model.isInstance(option)) {
    axisModel = new Model(option);
    zrUtil.mixin(axisModel, axisModelCommonMixin);
  }

  var scale = axisHelper.createScaleByModel(axisModel);
  scale.setExtent(dataExtent[0], dataExtent[1]);
  axisHelper.niceScaleExtent(scale, axisModel);
  return scale;
}
/**
 * Mixin common methods to axis model,
 *
 * Inlcude methods
 * `getFormattedLabels() => Array.<string>`
 * `getCategories() => Array.<string>`
 * `getMin(origin: boolean) => number`
 * `getMax(origin: boolean) => number`
 * `getNeedCrossZero() => boolean`
 * `setRange(start: number, end: number)`
 * `resetRange()`
 */


function mixinAxisModelCommonMethods(Model) {
  zrUtil.mixin(Model, axisModelCommonMixin);
}

exports.createList = createList;
exports.createScale = createScale;
exports.mixinAxisModelCommonMethods = mixinAxisModelCommonMethods;