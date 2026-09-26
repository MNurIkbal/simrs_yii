<?php

use yii\helpers\Html;
?>
<div class="modal-header">
  <h5 class="modal-title" id="exampleModalLabel"><?= $is_update ? 'Update' : 'Simpan' ?> Template</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-sm-12">
      <label class="required-reseptur">Nama Template</label>
      <div class="form-group">
        <?= Html::textInput('template_name', null, [
          'id' => 'template_name',
          'class' => 'form-control'
        ]) ?>
        <?= Html::hiddenInput('reseptemp_id', $reseptemp_id, [
          'id' => 'old_reseptemp_id'
        ]) ?>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <?php if (!$is_update) : ?>
    <button type="button" class="btn btn-info" onclick="simpanTemplate()">Simpan Template</button>
  <?php else : ?>
    <button type="button" class="btn btn-info" onclick="simpanTemplate()">Update Template</button>
  <?php endif; ?>
</div>

<?php $this->registerJs("
  var is_update = '".$is_update."';
  if (is_update == 'true') {
    $('#template_name').val(relatedResepTempName);
  }
") ?>
