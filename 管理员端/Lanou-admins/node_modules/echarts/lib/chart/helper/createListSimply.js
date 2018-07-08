var createDimensions = require("../../data/helper/createDimensions");

var List = require("../../data/List");

var _util = require("zrender/lib/core/util");

var extend = _util.extend;
var isArray = _util.isArray;

/**
 * [Usage]:
 * (1)
 * createListSimply(seriesModel, ['value']);
 * (2)
 * createListSimply(seriesModel, {
 *     coordDimensions: ['value'],
 *     dimensionsCount: 5
 * });
 *
 * @param {module:echarts/model/Series} seriesModel
 * @param {Object|Array.<string|Object>} opt opt or coordDimensions
 *        The options in opt, see `echarts/data/helper/createDimensions`
 * @param {Array.<string>} [nameList]
 * @return {module:echarts/data/List}
 */
function _default(seriesModel, opt, nameList) {
  opt = isArray(opt) && {
    coordDimensions: opt
  } || extend({}, opt);
  var source = seriesModel.getSource();
  var dimensionsInfo = createDimensions(source, opt);
  var list = new List(dimensionsInfo, seriesModel);
  list.initData(source, nameList);
  return list;
}

module.exports = _default;