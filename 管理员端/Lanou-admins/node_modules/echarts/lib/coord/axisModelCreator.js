var zrUtil = require("zrender/lib/core/util");

var axisDefault = require("./axisDefault");

var ComponentModel = require("../model/Component");

var _layout = require("../util/layout");

var getLayoutParams = _layout.getLayoutParams;
var mergeLayoutParam = _layout.mergeLayoutParam;

var OrdinalMeta = require("../data/OrdinalMeta");

// FIXME axisType is fixed ?
var AXIS_TYPES = ['value', 'category', 'time', 'log'];
/**
 * Generate sub axis model class
 * @param {string} axisName 'x' 'y' 'radius' 'angle' 'parallel'
 * @param {module:echarts/model/Component} BaseAxisModelClass
 * @param {Function} axisTypeDefaulter
 * @param {Object} [extraDefaultOption]
 */

function _default(axisName, BaseAxisModelClass, axisTypeDefaulter, extraDefaultOption) {
  zrUtil.each(AXIS_TYPES, function (axisType) {
    BaseAxisModelClass.extend({
      /**
       * @readOnly
       */
      type: axisName + 'Axis.' + axisType,
      mergeDefaultAndTheme: function (option, ecModel) {
        var layoutMode = this.layoutMode;
        var inputPositionParams = layoutMode ? getLayoutParams(option) : {};
        var themeModel = ecModel.getTheme();
        zrUtil.merge(option, themeModel.get(axisType + 'Axis'));
        zrUtil.merge(option, this.getDefaultOption());
        option.type = axisTypeDefaulter(axisName, option);

        if (layoutMode) {
          mergeLayoutParam(option, inputPositionParams, layoutMode);
        }
      },

      /**
       * @override
       */
      optionUpdated: function () {
        var thisOption = this.option;

        if (thisOption.type === 'category') {
          this.__ordinalMeta = OrdinalMeta.createByAxisModel(this);
        }
      },

      /**
       * Should not be called before all of 'getInitailData' finished.
       * Because categories are collected during initializing data.
       */
      getCategories: function () {
        // FIXME
        // warning if called before all of 'getInitailData' finished.
        if (this.option.type === 'category') {
          return this.__ordinalMeta.categories;
        }
      },
      getOrdinalMeta: function () {
        return this.__ordinalMeta;
      },
      defaultOption: zrUtil.mergeAll([{}, axisDefault[axisType + 'Axis'], extraDefaultOption], true)
    });
  });
  ComponentModel.registerSubTypeDefaulter(axisName + 'Axis', zrUtil.curry(axisTypeDefaulter, axisName));
}

module.exports = _default;