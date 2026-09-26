<?php

use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>

<?= Html::hiddenInput('tmp_ruangan_id', null, [
    'id' => 'tmp-ruangan-id'
]) ?>

<?php
$form = ActiveForm::begin([
    'id' => 'order-penunjang-form',
    'action' => $url['form-action'],
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]);
echo Html::hiddenInput('penjamin_id', $penjaminId, [
    'id' => 'instruksipenunjang-penjamin_id'
]);
echo Html::hiddenInput('kelaspelayanan_id', $kelaspelayananId, [
    'id' => 'instruksipenunjang-kelaspelayanan_id'
]);
echo Html::activeHiddenInput($model, 'pendaftaran_id');
echo Html::activeHiddenInput($model, 'instalasi_id', [
    'id' => 'instruksipenunjang-instalasi_id'
]);
echo Html::activeHiddenInput($model, 'pasienadmisi_id');
echo Html::activeHiddenInput($model, 'pegawai_id');
echo Html::activeHiddenInput($model, 'cppt_id');
echo Html::activeHiddenInput($model, 'ruangan')
?>
<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss-confirmation="modal">&times;</button>
    <h5 class="modal-title">Order Pemeriksaan <?= ucwords($type); ?> - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h5>
</div>
<div class="modal-body form-modal-<?= $type ?>">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">Tanggal Permintaan</label>
                <?= Html::activeTextInput($model, 'tgl_kirimpasien', [
                    'class' => 'form-control pickadate',
                    'readonly' => true,
                    'value' => date('d/m/Y')
                ]) ?>
            </div>
        </div>
        <div class="col-sm-5">
            <?php
            echo $form->field($model, 'diagnosis')->textarea([
                'class' => 'form-control input-sm',
                'rows' => '3',
                'disabled' => 'disabled'
            ])->label("Diagnosis");
            echo $form->field($model, 'ruangan_id')->hiddenInput([])->label(false);
            ?>
        </div>
        <div class="col-sm-4">
            <!-- Diagnosa penyerta -->
            <?= $form->field($model, 'a_diag_penyerta')->widget(Select2::classname(),[
                'showToggleAll' => false,
                'options' => [
                    'multiple' => true,
                    'placeholder' => '-- Pilih --'
                ],
                'pluginOptions' => [
                    'tags' => true,
                    'disabled' => 'disabled',
                    'tokenSeparators' => [',', '_'],
                    'maximumInputLength' => 50,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                    ],
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                        'dataType' => 'json',
                        'data' => new JsExpression('
                            function(params) {
                                return {
                                    q: params.term,
                                    page: params.page || 1,
                                    type: "diagnosa_penyerta",
                                    all_text: 0,
                                    id_with_text: 1,
                                    is_perawat: '.$is_perawat.'
                                };
                            }
                        ')
                    ],
                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" style="margin-bottom: 5px">
            <hr>
            <p class="text-bold">Tabel Pemeriksaan unit penunjang</p>
            <button type='button' id="btn-tambah-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-info btn-xs' data-width="80%" data-href="<?= $url['modal-pemeriksaan'] ?>"><b><i class='fa fa-plus'></i></b> Tambah</button>
            <button type='button' id="btn-reset-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-danger btn-xs'><b><i class='fa fa-trash'></i></b> Kosongkan</button>
        </div>
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover" id="tbl-order-penunjang">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Jenis Pemeriksaan</th>
                            <th>Nama Pemeriksaan</th>
                            <th width="5%">Qty. Pemeriksaan</th>
                            <th width="33%">Catatan</th>
                            <th width="10%">Frekuensi Terapi</th>
                            <th>Jadwal Terapi</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="title-empty text-center no-data-row">Belum Ada Data yang terpilih</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <br>
    <div class="row">
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'catatan_dokterpengirim')->textarea([
                'class' => 'form-control input-sm',
                'rows' => '3'
            ])->label("Catatan");
            ?>
        </div>
    </div>
</div>
<div class="modal-footer text-right">
    <button type='button' style='margin-right: 25px' id="btn-save-penunjang" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
</div>

<?php ActiveForm::end() ?>

<?php
$phpVars = [
    'maksFrekuensi' => $maksFrekuensi,
    'jadwalOperasi' => null,
    'dokterPerujukId' => ArrayHelper::getValue($user, 'id_pegawai'),
    'dokterPerujukNama' => ArrayHelper::getValue($user, 'nama_pegawai'),
    'pendId' => $id,
    'diagPenyerta' => $diagPenyerta
];
$this->registerJsVar('phpVars', $phpVars);
$this->registerJs($this->render('__modal.js'), View::POS_END);
?>
