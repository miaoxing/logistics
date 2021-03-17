<?php $view->layout() ?>

<?php echo $block->css() ?>
<link rel="stylesheet" href="<?php echo $asset('plugins/logistics/css/traces.css') ?>">
<?php echo $block->end() ?>

<div class="trace-container bg-white">
  <span class="text-muted">运单号:</span>
  <?php echo $logistics['name'] ?> <?php echo $e($req['logisticsNo']) ?: '无' ?>

  <?php if (!$traces) { ?>
    <div class="trace-empty">暂无物流信息</div>
  <?php } ?>

  <div class="trace-timeline">
    <ul class="list-unstyled">
      <?php foreach ($traces as $i => $trace) { ?>
        <li class="trace-item <?php echo 0 == $i ? 'text-primary' : '' ?>">
          <span class="trace-icon <?php echo 0 == $i ? 'bg-primary' : '' ?>"></span>
          <span><?php echo $e($trace['context']) ?></span>
          <span><?php echo $e($trace['time']) ?></span>
        </li>
      <?php } ?>
    </ul>
  </div>
</div>
