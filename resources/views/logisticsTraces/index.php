<?php $view->layout() ?>

<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('plugins/logistics/css/traces.css') ?>">
<style>
  .trace-icon.bg-primary {
    background-color: <?= $setting('theme.brandPrimary') ?: '#f28c48' ?>;
  }
</style>
<?= $block->end() ?>

<div class="trace-container bg-light">
  <span class="text-muted">运单号:</span>
  <?= $logistics['name'] ?> <?= $e($req['logisticsNo']) ?: '无' ?>

  <?php if (!$traces) : ?>
    <div class="trace-empty">暂无物流信息</div>
  <?php endif ?>

  <div class="trace-timeline">
    <ul class="list-unstyled">
      <?php foreach ($traces as $i => $trace) : ?>
        <li class="trace-item <?= $i == 0 ? 'text-primary' : '' ?>">
          <span class="trace-icon <?= $i == 0 ? 'bg-primary' : '' ?>"></span>
          <span><?= $e($trace['context']) ?></span>
          <span><?= $e($trace['time']) ?></span>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
</div>
