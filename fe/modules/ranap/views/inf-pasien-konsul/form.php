<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use softark\duallistbox\DualListbox;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
            'id' => 'ajax-form',
            'options' => [
                    'class' => 'form-horizontal',
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]);
    ?>
    <div class="form-group">
        <label for="no_pendaftaran" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'No. Pendaftaran'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'no_pendaftaran')
                ->textInput(['class' => 'form-control', 'readonly' => 'readonly'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="no_rekam_medik" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'No. Rekam Medik'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'no_rekam_medik')
                ->textInput(['class' => 'form-control', 'readonly' => 'readonly'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="nama_pasien" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Nama Pasien'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'nama_pasien')
                ->textInput(['class' => 'form-control', 'readonly' => 'readonly'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="tgl_admisi" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Tanggal Pembatalan'); ?>
        </label>
        <div class="col-lg-6">
          <?= $form->field($model, 'tgl_admisi')
              ->textInput([
                  'class' => 'form-control ambildate tgl_masuk',
                  'value' => $model->tgl_admisi
              ])->label(false);?>
        </div>
    </div>
    <div class="form-group required">
        <label for="alasan_batal" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Alasan Pembatalan'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($modelBatal, 'alasan_batal')
            ->textarea(['rows' => '4'],['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="keterangan_batal" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Keterangan'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($modelBatal, 'keterangan_batal')
            ->textarea(['rows' => '4'],['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::hiddenInput('pasienadmisi_id', $model->pasienadmisi_id);?>
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success btn-md']) ?>
        <?= Html::button('Batal',['class' => 'btn btn-default btn-md','data-dismiss' => 'modal']); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs("
      const tgl_masuk = $('.tgl_masuk').val();
      let yesterday = new Date(tgl_masuk);
      yesterday.setDate(yesterday.getDate() - 1);

      $('.ambildate').pickadate({
          formatSubmit: 'yyyy-mm-dd',
          format: 'dd mmmm yyyy',
          disable: [{
              from: [0, 0, 0],
              to: yesterday
          }],
          onStart: function () {
              var date = new Date();
              this.set('select', tgl_masuk)
          }
      });

      $('#ajax-form').docoForm('submit',{
          success : function(data) {
              // tabel.draw();
              $('#modal_backdrop').modal('toggle');
              table.draw();
          }
      });
");
?>
