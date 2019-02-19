<?php $view->layout() ?>

<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('plugins/logistics/css/traces.css') ?>">
<?= $block->end() ?>

<div class="page-header">
  <h1>
    物流跟踪
  </h1>
</div>

<div class="row">
  <div class="col-10 offset-1 col-sm-8 offset-sm-2">
    <form class="js-logistics-form logistics-form clearfix my-3" method="get" role="form">

      <div class="col-sm-3">
        <select class="js-logistics-id form-control" name="logisticsId">
          <option value="0" selected>请选择物流公司</option>
          <?php foreach (wei()->logistics->getNames() as $id => $name) : ?>
            <?php if ($req['logisticsName'] && $req['logisticsName'] == $name) : ?>
              <option value="<?= $id ?>" selected><?= $name ?></option>
            <?php else : ?>
              <option value="<?= $id ?>"><?= $name ?></option>
            <?php endif ?>
          <?php endforeach ?>
        </select>
      </div>

      <div class="col-sm-6">
        <input type="tel" class="js-logistics-no form-control" name="logisticsNo" placeholder="请输入快递运单号码"
          value="<?= $req['logisticsNo'] ?>">
      </div>

      <div class="col-sm-1">
      <span class="input-group-append">
        <button class="btn btn-default btn-primary" type="submit">搜索</button>
      </span>
      </div>

    </form>

    <div class="js-logistics-traces"></div>

  </div>
</div>

<script id="logistics-detail-tpl" type="text/html">
  <div class="trace-container bg-light">
    <span class="text-muted">运单号:</span>
    <%= logistics.name %> <%= logistics.no %>
    <% if (traces.length == 0) { %>
    <div class="trace-empty">暂无物流信息</div>
    <% } else { %>
    <div class="trace-timeline">
      <ul class="list-unstyled">
        <% for(var i in traces) { %>
        <li class="trace-item <% if(i == 0) { %> text-primary <% } %>">
          <span class="trace-icon <% if(i == 0) { %> text-primary <% } %>"></span>
          <span><%= traces[i].context %></span>
          <span><%= traces[i].time %></span>
        </li>
        <% } %>
      </ul>
    </div>
    <% } %>
  </div>
</script>

<?= $block->js() ?>
<script>
  require(['template', 'bootbox', 'form'], function () {
    template.helper('$', $);
    $('.js-logistics-form').ajaxForm({
      url: $.url('admin/logistics-traces/' + $('.js-logistics-id').val() + "_" + $('.js-logistics-no').val()),
      dataType: 'json',
      beforeSend: function () {
        var id = $('.js-logistics-id').val();
        var no = $('.js-logistics-no').val();

        if (!no) {
          $.err('请输入快递运单号码');
          return false;
        }

        if (id == 0) {
          $.err('请选择快递公司');
          return false;
        }

        $(this).url = $.url('admin/logistics-traces/' + id + "_" + no);
      },
      success: function (ret) {
        $(".js-logistics-traces").empty();
        if (ret.code !== 1) {
          $.msg(ret);
          return;
        }

        if (ret.data.url) {
          window.open(ret.data.url);
          return;
        }

        var htmlTemplate = template.render('logistics-detail-tpl', ret.data);
        $(".js-logistics-traces").append(htmlTemplate);
      }
    });

    // 加载页面完成自动提交
    $(function () {
      if ($('.js-logistics-no').val() != '') {
        $('.js-logistics-form').submit();
      }
    });

  });
</script>
<?= $block->end() ?>
