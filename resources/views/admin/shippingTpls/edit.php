<?php $view->layout() ?>

<div class="page-header">
  <a class="btn btn-default pull-right" href="<?= $url('admin/shipping-tpls') ?>">返回列表</a>

  <h1>
    运费模板管理
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE detail BEGINS -->
    <form class="js-shipping-tpl-form form-horizontal" method="post" role="form"
      action="<?= $url('admin/shipping-tpls/' . $shippingTpl->getFormAction()) ?>">
      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          <span class="text-warning">*</span>
          模板名称
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="name" id="name" data-rule-required="true"
            placeholder="如:低于一公斤物品,首件10元,续件1元">
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="freeShipping">
          <span class="text-warning">*</span>
          是否包邮
        </label>

        <div class="col-lg-4">
          <label class="radio-inline">
            <input type="radio" name="freeShipping" value="1"> 包邮
          </label>
          <label class="radio-inline">
            <input type="radio" name="freeShipping" value="0"> 不包邮
          </label>
        </div>
      </div>

      <div class="form-group js-rules-form-group">
        <label class="col-lg-2 control-label">
          <span class="text-warning">*</span>
          运费规则
        </label>

        <div class="col-lg-6">
          <p class="form-control-static text-muted">请设置地区对应的运费，未设置的地区使用默认运费。</p>
          <div class="logistics-lists js-logistics-lists">

          </div>
          <a href="javascript:;" class="js-logistics-add logistics-add text-muted">
            <i class="fa fa-plus"></i>
            增加物流服务商
          </a>
        </div>
      </div>

      <input type="hidden" name="id" id="id">

      <div class="clearfix form-actions form-group">
        <div class="col-lg-offset-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>

          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-default" href="<?= $url('admin/shipping-tpls') ?>">
            <i class="fa fa-undo"></i>
            返回列表
          </a>
        </div>
      </div>
    </form>
  </div>
  <!-- PAGE detail ENDS -->
</div><!-- /.col -->
<!-- /.row -->

<!-- Modal -->
<div class="modal fade js-logistics-modal" tabindex="-1" role="dialog" aria-labelledby="logistics-modal-label"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="logistics-modal-label">选择物流服务</h4>
      </div>
      <div class="modal-body">
        <form class="form">
          <select class="form-control js-logistics-select">

          </select>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary js-logistics-confirm">确定</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="js-area-modal area-modal modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">选择区域</h4>
      </div>
      <div class="modal-body">
        <ul class="js-area-list list-unstyled">

        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="js-area-ok btn btn-primary">确定</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div>

<script id="logisticsTpl" type="text/html">
  <div class="logistics-item">
    <span class="logistics-name"><%= logistics[logisticsId] %></span>

    <input type="hidden" value="<%= logisticsId %>" name="logisticsIds[]" class="js-logistics-id">
    <input type="hidden" value="<%= useLogisticsId %>" name="useLogisticsIds[<%= logisticsId %>]"
      class="js-logistics-use" data-logistics-id="<%= logisticsId %>">

    <% if (logisticsId != 1 ) { %>
      <div class="btn-group js-logistics-use-dropdown">
        <a href="javascript:;" class="btn btn-default btn-link dropdown-toggle" data-toggle="dropdown">
          <span class="js-logistics-use-name">
            使用<%= useLogisticsId == 0 ? '自定义' : '"' + logistics[useLogisticsId] + '"' %>运费规则
          </span>
          <span class="fa fa-caret-down icon-on-right"></span>
        </a>
        <ul class="dropdown-menu dropdown-white">
        </ul>
      </div>

      <div class="action-buttons pull-right">
        <a href="javascript:;" class="js-logistics-remove bigger-120 text-danger">
          <i class="fa fa-trash-o"></i>
        </a>
      </div>
    <% } %>

    <div class="js-logistics-rules logistics-rules <% if (logistics['id'] != 0 ) { %>hide<% } %>">
      <table class="rules-table table table-bordered table-hover table-center">
        <thead>
        <tr>
          <th>地区</th>
          <th style="width: 100px">首件运费(元)</th>
          <th title="每增加1件运费(元)" style="width: 100px">续件运费(元)</th>
          <th style="width: 60px">操作</th>
        </tr>
        </thead>
        <tbody class="js-rule-list">

        </tbody>
        <tfoot>
        <tr>
          <td colspan="4">
            <a href="javascript:;" class="js-rule-add pull-left" data-logistics-id="<%= logistics['id'] %>">
              <i class="fa fa-plus"></i>
              为指定地区设置运费
            </a>
          </td>
        </tr>
        </tfoot>
      </table>
    </div>
  </div>
</script>

<script id="logisticsUseTpl" type="text/html">
  <% for (var i in ids) { %>
    <li><a href="javascript:;" data-val="<%= ids[i] %>">使用"<%= logistics[ids[i]] %>"运费规则</a></li>
  <% } %>
  <li><a href="javascript:;" data-val="0">使用自定义运费规则</a></li>
</script>

<script id="shippingRuleTpl" type="text/html">
  <tr>
    <td class="text-left">
      <% if (isDefault == '1') { %>
        默认
        <input type="hidden" value="1" name="rules[<%= index %>][isDefault]" value="<%= isDefault %>">
      <% } else { %>
        <a href="javascript:;" class="js-areas-edit pull-right">编辑</a>
        <div class="js-rule-area-name rule-area-name"><%= areaNames.join('、') || '请选择地区' %></div>
      <% } %>
      <input class="js-rule-area-names" type="hidden" value="<%= areaNames.join(',') %>"
        name="rules[<%= index %>][areaNames]">
      <input class="js-rule-areas" type="hidden" value="<%= areas.join(',') %>" name="rules[<%= index %>][areas]">
      <input class="logistics-id" type="hidden" value="<%= logisticsId %>" name="rules[<%= index %>][logisticsId]">
    </td>
    <td>
      <input type="text" value="<%= startFee %>" name="rules[<%= index %>][startFee]" class="rule-fee js-fee"
        data-rule-number="true" data-rule-min="0" data-rule-required="true">
    </td>
    <td>
      <input type="text" value="<%= plusFee %>" name="rules[<%= index %>][plusFee]" class="rule-fee js-fee"
        data-rule-number="true" data-rule-min="0" data-rule-required="true">
    </td>
    <td>
      <% if (isDefault == '1') { %>
      -
      <% } else { %>
      <div class="action-buttons">
        <a class="text-danger js-rule-remove" href="javascript:;" title="删除">
          <i class="fa fa-trash-o bigger-130"></i>
        </a>
      </div>
      <% } %>
    </td>
  </tr>
</script>

<script id="areaTpl" type="text/html">
  <% for (var i in areas) { %>
  <li class="js-area-item area-item">
    <div class="area-name">
      <div class="checkbox-inline">
        <label>
          <input class="js-area-input" type="checkbox" value="<%= areas[i].name %>" data-name="<%= areas[i].name %>">
          <%= areas[i].name %>
        </label>
      </div>
    </div>
    <div class="js-province-list province-list">
      <% for (var provinceId in areas[i].provinces) { %>
      <div class="js-province-item province-item">
        <div class="province-name">
          <div class="checkbox-inline">
            <label>
              <input class="js-province-input" type="checkbox" value="<%= provinceId %>"
                data-name="<%= areas[i].provinces[provinceId] %>">
              <%= areas[i].provinces[provinceId] %><span class="js-city-num city-num text-danger smaller-75"></span>
            </label>
          </div>
          <a href="javascript:;" class="js-city-toggle">
            <span class="caret"></span>
          </a>
        </div>

        <div class="city-list">
          <% for (var cityId in cities[areas[i].provinces[provinceId]]) { %>
          <div class="checkbox-inline">
            <label>
              <input class="js-city-input" type="checkbox" value="<%= cityId %>"
                data-name="<%= cities[areas[i].provinces[provinceId]][cityId] %>">
              <%= cities[areas[i].provinces[provinceId]][cityId] %>
            </label>
          </div>
          <% } %>
          <div class="text-right">
            <a class="js-city-hide btn btn-sm btn-default" href="javascript:;">关闭</a>
          </div>
        </div>
      </div>
      <% } %>
    </div>
  </li>
  <% } %>
</script>

<?= $block->js() ?>
<script>
  require([
    'plugins/logistics/js/admin/shipping-tpls',
    'css!plugins/logistics/css/admin/shipping-tpls',
    'form',
    'template',
    'validator'
  ], function (shippingTpls, css, region) {
    $.getJSON($.url('admin/shipping-tpls/regions.json'), function (region) {
      shippingTpls.editAction({
        region: region,
        data: <?= $shippingTpl->toJson() ?>,
        logistics: <?= json_encode($logistics) ?>
      });
    });
  });
</script>
<?= $block->end() ?>
