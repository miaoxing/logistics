<?php $view->layout() ?>

<!-- /.page-header -->
<div class="page-header">
  <div class="pull-right">
    <a class="btn btn-success" href="<?= $url('admin/shipping-tpls/new') ?>">添加运费模板</a>
  </div>
  <h1>
    运费模板管理
  </h1>
</div>

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">
      <table id="record-table" class="lottery-table table table-bordered table-hover table-center">
        <thead>
        <tr>
          <th>模板名称</th>
          <th>是否包邮</th>
          <th class="t-10">最后更改时间</th>
          <th class="t-6">操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <!-- /.table-responsive -->
    <!-- PAGE CONTENT ENDS -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->

<script id="table-actions" type="text/html">
  <div class="action-buttons">
    <a href="<%= $.url('admin/shipping-tpls/%s/edit', id) %>" title="编辑">
      <i class="fa fa-edit bigger-130"></i>
    </a>
    <a class="text-danger delete-record" href="javascript:;"
      data-href="<%= $.url('admin/shipping-tpls/destroy', {id: id}) %>" title="删除">
      <i class="fa fa-trash-o bigger-130"></i>
    </a>
  </div>
</script>

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/data-table', 'form', 'jquery-unparam'], function () {
    var recordTable = $('#record-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/shipping-tpls.json')
      },
      columns: [
        {
          data: 'name'
        },
        {
          data: 'freeShipping',
          render: function (data) {
            return data == '1' ? '包邮' : '不包邮';
          }
        },
        {
          data: 'updateTime'
        },
        {
          data: 'id',
          render: function (data, type, full) {
            return template.render('table-actions', full);
          }
        }
      ]
    });

    recordTable.deletable();
  });
</script>
<?= $block->end() ?>
