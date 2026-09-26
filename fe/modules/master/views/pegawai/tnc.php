<?php

use yii\helpers\Html;
?>

<div class="modal-header">
  <h5 class="modal-title text-bold">SYARAT & KETENTUAN LAYANAN TILAKA</h5>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-12">
      <?= $this->render('tnc_body') ?>
    </div>
  </div>
</div>
<div class="modal-footer">
    <?=Html::button('Setuju',[
        'id' => 'tnc-agree',
        'class' => 'btn btn-success btn-md',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
