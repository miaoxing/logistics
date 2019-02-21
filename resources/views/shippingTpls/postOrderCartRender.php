<script type="text/html" id="logisticsServicesTpl">
  <% $.each(services, function (i, service) { %>
    <option value="<%= service.id %>" data-fee="<%= service.fee %>"><%= service.name %> &yen;<%= service.fee %></option>
  <% }) %>
</script>

<li class="js-group-user-logistics-id list-item order-form-group has-feedback list-over-fix display-none">
  <label for="user-logistics-id">配送方式</label>

  <div class="order-form-col text-right">
    <select id="user-logistics-id" name="userLogisticsId" class="order-form-select js-user-logistics-id">
    </select>
    <div class="order-form-select-fake js-user-logistics-name">
    </div>
  </div>
  <i class="bm-angle-right list-feedback"></i>
</li>

<?= $block->js() ?>
<script>
  require(['plugins/order/js/orders', 'jquery-form', 'plugins/app/libs/artTemplate/template.min'], function (orders) {
    template.helper('$', $);

    var $form = $('.js-order-form');
    var $userLogisticsId = $('.js-user-logistics-id');
    var $userLogisticsName = $('.js-user-logistics-name');
    var $groupUserLogisticsId  =$('.js-group-user-logistics-id');

    // 根据地址渲染配送方式
    renderLogistics(<?= $addressId ?>);

    // 选择了地址,更新配送方式
    $(document).on('address:select', function (e, picker) {
      renderLogistics(picker.data.id);
    });

    // 选择配送方式
    $userLogisticsId.change(selectLogistics);

    function selectLogistics() {
      var $selected = $userLogisticsId.find('option:selected');
      $userLogisticsName.html($selected.html());

      var fee = $selected.data('fee');
      orders.setAmountRule('address', {name: '运费', shippingFee: fee});
      orders.applyAmountRule();
    }

    function renderLogistics(addressId) {
      if (!addressId) {
        $groupUserLogisticsId.hide();
        return;
      }

      $form.ajaxSubmit({
        url: $.url('shipping-tpls/get-services'),
        dataType: 'json',
        data: {
          addressId: addressId
        },
        loading: true,
        success: function (ret) {
          if (ret.code === 1) {
            $userLogisticsId.html(template.render('logisticsServicesTpl', ret));
            selectLogistics();
            $groupUserLogisticsId.show();
          } else {
            $.msg(ret);
          }
        }
      });
    }
  });
</script>
<?= $block->end() ?>
