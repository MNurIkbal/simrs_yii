<?php
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Url;
use app\modules\components\helpers\DynamicFormHelpers;
use app\modules\ranap\components\widget\DynamicFormWidget;
use yii\web\JsExpression;
use app\components\DocoConstants;
?>

<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Permintaan Konsultasi - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h5>
</div>
<div class="modal-body" id="section-konsulpoli">
    <div class="row">
        <div class="col-lg-3">
            <?php $form = ActiveForm::begin([
                'id' => 'permintaan-konsul-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                // 'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
            ]) ?>
            <?= Html::hiddenInput('PermintaanKonsulForm[pendaftaran_id]', $model->pendaftaran_id);?>
            <?= Html::hiddenInput('PermintaanKonsulForm[pasienadmisi_id]', $model->pasienadmisi_id);?>
            <?= Html::hiddenInput('PermintaanKonsulForm[permintaankonsul_id]', '');?>
            <?= Html::activeHiddenInput($model, 'cppt_id'); ?>

            <div class="form-group field-waktu_permintaan-permintaan-konsul required">
                    <label class="control-label" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu permintaan'); ?></label>
                    <br>
                    <b><span id="waktu-permintaan-modal"></span></b>
                    <?= Html::hiddenInput('PermintaanKonsulForm[waktu_permintaan]', $model->waktu_permintaan);?>
            </div>
            <?= $form->field($model, 'dokter_id')
            ->dropDownList([], [ 
                'id'     => 'dokter_id-permintaan-konsul',
                'prompt' => '--Pilih--', 
                'class'  => 'select2 select-dokter'
            ]); ?>
            <?= $form->field($model, 'dokter_nama', ['options'=>['class'=>'hidden']])->staticInput(['id'=>'dokter_nama']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'jenis_konsul')
            ->dropDownList([], [ 
                'id'     => 'jenis_konsul-permintaan-konsul',
                'prompt' => '--Pilih--', 
                'class'  => 'select2 select-konsul'
            ]); ?>
            <?= $form->field($model, 'jenis_konsul_nama', ['options'=>['class'=>'hidden']])->staticInput(['id'=>'jenis_konsul_nama']) ?>

            <?= $form->field($model, 'ket_konsul')->textArea(); ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-md-offset-3 text-right">
            <button type='button' style='margin-right: 5px' id="btn-save-permintaan-konsul" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
            <button type='button' style='margin-right: 5px' id="btn-ubah-permintaan-konsul" class='btn btn-labeled btn-info btn-xs data-ubah'><b><i class='fa fa-save'></i></b> Ubah</button>
            <button type='button' style='margin-right: 5px' id="btn-kembali-permintaan-konsul" class='btn btn-labeled btn-info btn-xs data-kembali'><b><i class='fa fa-save'></i></b> Kembali</button>
        </div>
    </div>
    <div class="row">
        <?php 
                $form = ActiveForm::begin([
                    'id' => 'list-form',
                ]); 
            ?>
            <label>
                <h6 class="panel-title"><?= Yii::t('fe', 'Daftar permintaan konsultasi'); ?></h6> 
            </label>
            <div class="clearfix"><br></div>
            <table class="table datatable-basic table-striped table-hover dataTable" id="tb_permintaan_konsultasi_modal" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?= Yii::t('fe', 'Waktu permintaan') ?></th>
                        <th><?= Yii::t('fe', 'Dokter DPJP') ?></th>
                        <th><?= Yii::t('fe', 'Dokter yang dikonsul') ?></th>
                        <th><?= Yii::t('fe', 'Jenis konsul') ?></th>
                        <th><?= Yii::t('fe', 'Permintaan konsultasi') ?></th>
                        <th><?= Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
    $this->registerJs('
        var is_disabled = "'.$disabled.'";
        var status_disabled = "'.$status_disabled.'";
        var is_valid = false;
        if(is_disabled || status_disabled) {
            is_valid = false;
        } else {
            is_valid = true;
        }

        $(document).ready(function(){
            $("#permintaan-konsul-form :input").prop("disabled", is_valid);
            $("#btn-save-permintaan-konsul").attr("disabled", is_valid);
        });

        var pasienId = "'. $pasienId .'";
        var pendaftaranId = "'. $pendaftaran_id .'";
        var list_dokter_available = ' . json_encode($listfilter['listDokter']) . ';
    ', View::POS_END);
    $this->registerJs($this->render('_modal_permintaankonsul.js',['pasienId'=>$pasienId]), View::POS_END);
?>
