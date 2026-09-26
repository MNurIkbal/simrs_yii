<?php
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
    'id' => 'diet-pasien-form',
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
<div class="modal-header">
    <button type="button" class="close close-modal-diet" data-dismiss-confirmation="modal">&times;</button>
    <h5 class="modal-title text-bold">Diet Pasien - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-8">
            <label class="control-label col-md-6"><?= Yii::t('fe', 'Jenis Diet') ?></label>
            <?= Html::activeTextarea($model, 'catatan_diet', ['class' => 'form-control']) ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="row">
            <div class="col-md-8">
            <button type="button" style="margin-right: 5px;" id="btn-save-diet-pasien" class="btn btn-labeled btn-info btn-xs"><b><i class="fa fa-save"></i></b> <?= Yii::t('fe', 'Simpan') ?></button>

            </div>
        </div>
</div>

<div class="panel-body">
        <h5 class="text-bold panel-title">Tabel Log Perubahan Diet</h5>
         <table class="table table-bordered datatable-basic dataTable" style="width:100%" id="tabel-log-diet">
               <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t('fe', 'Tanggal')?></th>
                        <th><?=Yii::t('fe', 'Jenis Diet')?></th>
                        <th><?=Yii::t('fe', 'Diinput Oleh')?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <br>
            <br>

</div>


<?php ActiveForm::end() ?>

<?php
    $this->registerJs('
        var _url = "' . $_url . '"
        var id = "' . $id . '"
    ', View::POS_END);
    $this->registerJs($this->render('js/__modal_diet_pasien.js'), View::POS_END);
?>
