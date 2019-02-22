define(['plugins/app/libs/artTemplate/template.min'], function (template) {
  var DELAY_SLOW = 5000;

  var ShippingTpls = function () {
    this.$el = $('body');

    // 支持的物流
    this.logistics = {};

    // 片区,省份信息
    this.region = {};

    // 点击编辑的标签
    this.editingRule = null;

    this.logisticsTpl = template.compile($.trim($('#logisticsTpl').html()));
    this.logisticsUseTpl = template.compile($.trim($('#logisticsUseTpl').html()));
    this.shippingRuleTpl = template.compile($.trim($('#shippingRuleTpl').html()));
  };

  ShippingTpls.CUSTOM_RULE = 0;

  ShippingTpls.DEFAULT_RULE = 1;

  ShippingTpls.RULE_DEFAULTS = {
    areas: [],
    areaNames: []
  };

  // 编辑页面
  ShippingTpls.prototype.editAction = function (options) {
    $.extend(this, options);

    this.renderForm();
    this.renderLogisticsList();
    this.renderArea();
  };

  ShippingTpls.prototype.renderForm = function () {
    var that = this;

    this.$el.find('.js-shipping-tpl-form')
      .loadJSON(this.data)
      .ajaxForm({
        dataType: 'json',
        beforeSubmit: function (arr, $form) {
          return $form.valid();
        },
        success: function (result) {
          $.msg(result, function () {
            if (result.code > 0) {
              window.location = $.url('admin/shipping-tpls');
            }
          });
        }
      })
      .validate();

    // 切换包邮
    this.$el.find('input[name="freeShipping"]').click(function () {
      if ($(this).val() === '1') {
        that.$el.find('.js-rules-form-group').hide();
      } else {
        that.$el.find('.js-rules-form-group').show();
      }
    }).filter(':checked').click();

    // 更改价格
    this.$el.on('change', '.js-fee', function () {
      var $this = $(this);
      var val = parseFloat($this.val());
      if (isNaN(val)) {
        val = 0;
      }
      $this.val(val.toFixed(2));
    });
  };

  // 渲染物流服务商列表
  ShippingTpls.prototype.renderLogisticsList = function () {
    var that = this;

    // 将物流公司的编号作为索引
    var logisticsRules = {};
    this.data.rules.forEach(function (rule) {
      if (typeof logisticsRules[rule.logisticsId] === 'undefined') {
        logisticsRules[rule.logisticsId] = [];
      }
      logisticsRules[rule.logisticsId].push(rule);
    });

    // 启用默认规则
    if (this.data.rules.length === 0) {
      logisticsRules[1] = [];
    }

    // 逐个物流公司渲染
    $.each(logisticsRules, function (logisticsId, rules) {
      that.renderLogistics(rules, logisticsId);
    });

    // 点击添加物流服务商
    this.$el.find('.js-logistics-add').click(function () {
      var logistics = $.extend({}, that.logistics);
      that.$el.find('.js-logistics-use').each(function () {
        delete logistics[$(this).data('logistics-id')];
      });

      var options = '';
      $.each(logistics, function (value, name) {
        options += '<option value="' + value + '">' + name + '</option>';
      });
      that.$el.find('.js-logistics-select').html(options);
      that.$el.find('.js-logistics-modal').modal('show');
    });

    // 点击确定,添加物流服务商
    this.$el.find('.js-logistics-confirm').click(function () {
      that.renderLogistics([], that.$el.find('.js-logistics-select').val());
      that.$el.find('.js-logistics-modal').modal('hide');
    });
  };

  // 渲染物流服务商
  ShippingTpls.prototype.renderLogistics = function (rules, logisticsId) {
    var that = this;

    var $logisticsLists = this.$el.find('.js-logistics-lists');

    var useLogisticsId = typeof this.data['useLogisticsIds'][logisticsId] === 'undefined' ?
      ShippingTpls.DEFAULT_RULE : this.data['useLogisticsIds'][logisticsId];

    var $logisticsItem = $(this.logisticsTpl({
      rules: rules,
      logistics: this.logistics,
      logisticsId: logisticsId,
      useLogisticsId: useLogisticsId
    }));


    // 渲染运费规则
    var $ruleLists = $logisticsItem.find('.js-rule-list');

    if (rules.length === 0) {
      rules.push({
        isDefault: '1',
        logisticsId: logisticsId
      });
    }

    rules.forEach(function (rule) {
      that.renderRule(rule, $ruleLists);
    });

    $logisticsLists.append($logisticsItem);

    // 事件
    var $use = $logisticsItem.find('.js-logistics-use');
    var $useDropdown = $logisticsItem.find('.js-logistics-use-dropdown');
    var $rules = $logisticsItem.find('.js-logistics-rules');

    // 点击删除物流
    $logisticsItem.on('click', '.js-logistics-remove', function () {
      // 检查该物流是否被其他物流使用
      var err = false;
      var id = $logisticsItem.find('.js-logistics-id').val();
      that.$el.find('.js-logistics-use').each(function () {
        if ($(this).val() === id) {
          err = true;
          var dataId = $(this).data('logistics-id');
          $.err('该物流已被"' + that.logistics[dataId] + '"使用,不能删除', DELAY_SLOW);
        }
      });

      if (err) {
        return;
      }

      $.confirm('确认删除该物流?', function () {
        $logisticsItem.remove();
      });
    });

    // 点击添加规则
    $logisticsItem.on('click', '.js-rule-add', function () {
      that.renderRule({logisticsId: logisticsId}, $ruleLists);
    });

    // 弹出"使用xx运费规则"之前,构造运费规则列表
    $useDropdown.on('show.bs.dropdown', function () {
      var ids = [];
      var intLogisticsId = parseInt(logisticsId, 10);
      that.$el.find('.js-logistics-use').each(function () {
        if (parseInt($(this).val(), 10) === ShippingTpls.CUSTOM_RULE) {
          var dataId = $(this).data('logistics-id');
          // 不显示自己
          if (dataId !== intLogisticsId) {
            ids.push(dataId);
          }
        }
      });

      var lists = that.logisticsUseTpl({
        ids: ids,
        logistics: that.logistics
      });
      $(this).find('.dropdown-menu').html(lists);
    });

    // 选择使用"XX"运费规则时,更新文案和表单的值
    $useDropdown.on('click', '.dropdown-menu a', function () {
      var val = parseInt($(this).data('val'), 10);
      var useVal = parseInt($use.val(), 10);

      // 如果使用自定义运费规则,检查是否被其他物流使用
      if (val !== ShippingTpls.CUSTOM_RULE && useVal === ShippingTpls.CUSTOM_RULE) {
        var useId = that.getUseId($logisticsItem.find('.js-logistics-id').val());
        if (useId) {
          $.err('该物流已被"' + that.logistics[useId] + '"使用,不能更改');
          return;
        }
      }

      $useDropdown.find('.js-logistics-use-name').html($(this).html());
      $use.val(val).change();
    });

    // 如果选择的是"使用自定义运费规则",显示规则列表
    $use.change(function () {
      if ($(this).val() === '0') {
        $rules.removeClass('hide');
      } else {
        $rules.addClass('hide');
      }
    });

    // 赋值,触发事件
    $use.change();
  };

  // 检查指定的物流是否被其他物流使用,如果是返回第一个使用的物流编号
  ShippingTpls.prototype.getUseId = function (logisticsId) {
    var id = null;
    this.$el.find('.js-logistics-use').each(function () {
      if ($(this).val() === logisticsId) {
        id = $(this).data('logistics-id');
        return false;
      }
      return true;
    });
    return id;
  };

  // 渲染一条运费规则
  ShippingTpls.prototype.renderRule = function (rule, $ruleLists) {
    rule = $.extend({}, ShippingTpls.RULE_DEFAULTS, rule);
    rule.index = $.guid++;

    var html = this.shippingRuleTpl(rule);
    var $ruleItem = $(html);
    $ruleLists.append($ruleItem);

    // 点击删除
    $ruleItem.on('click', '.js-rule-remove', function () {
      $.confirm('确定删除运费规则?', function () {
        $ruleItem.remove();
      });
    });
  };

  ShippingTpls.prototype.renderArea = function () {
    var that = this;

    this.$el.find('.js-area-list').html(template.render('areaTpl', this.region));

    // 点击片区
    this.$el.on('click', '.js-area-input', function () {
      var $this = $(this);
      var $areaItem = $this.closest('.js-area-item');

      $areaItem.find('input:checkbox').prop('checked', $this.prop('checked'));

      $areaItem.find('.js-province-item').each(function () {
        that.updateCityNum($(this));
      });
    });

    // 点击省份
    this.$el.on('click', '.js-province-input', function () {
      var $this = $(this);
      var $provinceItem = $this.closest('.js-province-item');

      $provinceItem.find('input:checkbox').prop('checked', $this.prop('checked'));

      that.updateCityNum($provinceItem);
      that.updateAreaCheckStatus(this);
    });

    // 显示城市浮层
    this.$el.on('click', '.js-city-toggle', function () {
      $(this).closest('.js-province-item').toggleClass('city-open');
    });

    // 关闭城市浮层
    that.$el.on('click', '.js-city-hide', function () {
      $(this).closest('.js-province-item').removeClass('city-open');
    });

    // 点击城市
    this.$el.on('click', '.js-city-input', function () {
      that.updateCityNum($(this).closest('.js-province-item'));
      that.updateAreaCheckStatus(this);
    });

    // 打开"选择地区"
    var $modal = that.$el.find('.js-area-modal');
    this.$el.on('click', '.js-areas-edit', function () {
      that.editingRule = $(this).parent();

      var areas = that.editingRule.find('.js-rule-areas').val().split(',');

      // 重置表单数据
      $modal.find('input:checkbox').prop('checked', false);
      $modal.find('.js-city-num').html('');

      areas.forEach(function (name) {
        $modal.find('input[value="' + name + '"]:first').trigger('click');
      });

      $modal.modal('show');
    });

    // "选择地区"点击确定
    this.$el.find('.js-area-ok').click(function () {
      var data = that.getAreaData();
      var name = data.areaNames.join('、');

      that.editingRule.find('.js-rule-areas').val(data.areas.join(','));
      that.editingRule.find('.js-rule-area-names').val(data.areaNames.join(','));
      that.editingRule.find('.js-rule-area-name').html(name);

      $modal.modal('hide');
    });
  };

  // 获取选中的区域信息
  ShippingTpls.prototype.getAreaData = function () {
    var data = {
      areas: [],
      areaNames: []
    };

    // 逐个片区检查
    this.$el.find('.js-area-item').each(function () {
      var area = $(this).find('.js-area-input');

      // 选中了某个片区
      if (area.prop('checked') === true) {
        data.areaNames.push(area.data('name'));
        $(this).find('.js-city-input').each(function () {
          data.areas.push(this.value);
        });
      } else {
        // 逐个省份检查
        $(this).find('.js-province-item').each(function () {
          var province = $(this).find('.js-province-input');

          if (province.prop('checked') === true) {
            data.areaNames.push(province.data('name'));
            $(this).find('.js-city-input').each(function () {
              data.areas.push(this.value);
            });
          } else {
            // 逐个城市检查
            $(this).find('.js-city-input:checked').each(function () {
              data.areaNames.push($(this).data('name'));
              data.areas.push(this.value);
            });
          }
        });
      }

    });
    return data;
  };

  // 根据已选中的城市,更新省份上的城市数量
  ShippingTpls.prototype.updateCityNum = function ($provinceItem) {
    var checkboxes = $provinceItem.find('.js-city-input');
    var checkedCheckboxes = checkboxes.filter(':checked');

    var cityNum = $provinceItem.find('.js-city-num');
    if (checkedCheckboxes.length) {
      cityNum.html('(' + checkedCheckboxes.length + ')');
    } else {
      cityNum.html('');
    }

    var provinceCheckbox = $provinceItem.find('.js-province-input');
    provinceCheckbox.prop('checked', checkedCheckboxes.length === checkboxes.length);
  };

  // 更新片区的选中状态
  ShippingTpls.prototype.updateAreaCheckStatus = function (checkbox) {
    var areaItem = $(checkbox).closest('.js-area-item');
    var checkboxes = areaItem.find('.js-province-input');
    areaItem.find('.js-area-input').prop('checked', checkboxes.length === checkboxes.filter(':checked').length);
  };

  return new ShippingTpls();
});
