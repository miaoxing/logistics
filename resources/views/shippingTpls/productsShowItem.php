<dl class="product-item border-top">
  <dt>配送：</dt>
  <dd>
    <?php if ($city) : ?>
      到<?= rtrim($city, '市') ?>
    <?php endif ?>

    <span data-toggle="modal" data-target=".js-shipping-fee-modal">
      <?= $defaultServices['name'] ?> &yen;<?= $defaultServices['fee'] ?>
      <span class="caret"></span>
    </span>
  </dd>
</dl>

<div class="js-shipping-fee-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">配送方式</h4>
      </div>
      <div class="modal-body">
        <table class="table table table-condensed" style="  margin-bottom: 0;">
          <thead>
          <tr>
            <th>物流服务</th>
            <th>运费(元)</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($services as $service) : ?>
            <tr>
              <td><?= $service['name'] ?></td>
              <td><?= $service['fee'] ?></td>
            </tr>
          <?php endforeach ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-primary hairline" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>