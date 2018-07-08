var _model = require("../../util/model");

var makeInner = _model.makeInner;

/**
 * @return {string} If large mode changed, return string 'reset';
 */
function _default() {
  var inner = makeInner();
  return function (seriesModel) {
    var fields = inner(seriesModel);
    var pipelineContext = seriesModel.pipelineContext;
    var originalLarge = fields.large;
    var originalIncremental = fields.incrementalRender;
    var large = fields.large = pipelineContext.large;
    var incremental = fields.incrementalRender = pipelineContext.incrementalRender;
    return (originalLarge ^ large || originalIncremental ^ incremental) && 'reset';
  };
}

module.exports = _default;