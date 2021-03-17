<dl>
  <dt>配送：</dt>
  <dd>
    <?php if ($city) { ?>
      到<?php echo rtrim($city, '市') ?>
    <?php } ?>

    <span data-toggle="modal" data-target=".js-shipping-fee-modal">
      <?php echo $defaultServices['name'] ?> &yen;<?php echo $defaultServices['fee'] ?>
      <span class="caret"></span>
    </span>
  </dd>
</dl>

<div class="js-shipping-fee-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="shipping-fee-modal-label"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shipping-fee-modal-label">配送方式</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-sm mb-0">
          <thead>
          <tr>
            <th>物流服务</th>
            <th>运费(元)</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($services as $service) { ?>
            <tr>
              <td><?php echo $service['name'] ?></td>
              <td><?php echo $service['fee'] ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
