<?php

use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Html;

Yii::error(['model ' => $model->attributes]);
?>
<?= Html::hiddenInput('tmp_ruangan_id', null, [
    'id' => 'tmp-ruangan-id'
]) ?>
<?php $form = ActiveForm::begin([
    'id' => 'order-penunjang-form',
    'action' => $url['form-action'],
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>
<?= Html::hiddenInput('penjamin_id', $penjaminId, [
    'id' => 'instruksipenunjang-penjamin_id'
]) ?>
<?= Html::hiddenInput('kelaspelayanan_id', $kelaspelayananId, [
    'id' => 'instruksipenunjang-kelaspelayanan_id'
]) ?>
<?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
<?= Html::activeHiddenInput($model, 'instalasi_id', [
    'id' => 'instruksipenunjang-instalasi_id'
]) ?>
<?= Html::activeHiddenInput($model, 'pasienadmisi_id') ?>
<?= Html::activeHiddenInput($model, 'pegawai_id') ?>
<?= Html::activeHiddenInput($model, 'cppt_id') ?>
<?= Html::activeHiddenInput($model, 'ruangan') ?>
<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Fisioterapi</h5>
</div>
<div class="modal-body form-modal-fisioterapi">
    <div class="row">
        <div class="col-md-6">
            <br>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Tanggal Permintaan</label>
                        <?= Html::activeTextInput($model, 'tgl_kirimpasien', [
                            'class' => 'form-control pickadate',
                            'readonly' => true,
                            'value' => date('d/m/Y')
                        ]) ?>
                    </div>
                </div>
                <div class="col-sm-6 select2-md">
                    <?= $form->field($model, 'ruangan_id')->dropDownList($wards, [
                        'prompt' => 'Pilih'
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'diagnosis')->textarea([
                'class' => 'form-control input-sm',
                'rows' => '2'
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" style="margin-bottom: 5px">
            <hr>
            <button type='button' id="btn-tambah-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-info btn-xs' data-width="80%" data-href="<?= $url['modal-pemeriksaan'] ?>"><b><i class='fa fa-plus'></i></b> Tambah</button>
            <button type='button' id="btn-reset-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-danger btn-xs'><b><i class='fa fa-trash'></i></b> Kosongkan</button>
        </div>
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover" id="tbl-order-penunjang">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Terapi</th>
                            <th>Catatan</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-right">
    <button type='button' style='margin-right: 25px' id="btn-save-penunjang" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
</div>

<?php ActiveForm::end() ?>

<?php
$this->registerJs("
    var _jadwalOperasi = {}
    var _dokterPerujukId = '" . $user['id_pegawai'] . "'
    var _dokterPerujukNama = '" . $user['nama_pegawai'] . "'
" . $this->render('_modal.js'), View::POS_END);
?>
