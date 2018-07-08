var completeDimensions = require("./completeDimensions");

/**
 * Substitute `completeDimensions`.
 * `completeDimensions` is to be deprecated.
 */

/**
 * @param {module:echarts/data/Source|module:echarts/data/List} source or data.
 * @param {Object|Array} [opt]
 * @param {Array.<string|Object>} [opt.coordDimensions=[]]
 * @param {number} [opt.dimensionsCount]
 * @param {string} [opt.extraPrefix]
 * @param {boolean} [opt.extraFromZero]
 * @param {Array.<string|Object>} [opt.dimensionsDefine=source.dimensionsDefine] Overwrite source define.
 * @param {Object|HashMap} [opt.encodeDefine=source.encodeDefine] Overwrite source define.
 * @return {Array.<Object>} dimensionsInfo
 */
function _default(source, opt) {
  opt = opt || {};
  return completeDimensions(opt.coordDimensions || [], source, {
    dimsDef: opt.dimensionsDefine || source.dimensionsDefine,
    encodeDef: opt.encodeDefine || source.encodeDefine,
    dimCount: opt.dimensionsCount,
    extraPrefix: opt.extraPrefix,
    extraFromZero: opt.extraFromZero
  });
}

module.exports = _default;