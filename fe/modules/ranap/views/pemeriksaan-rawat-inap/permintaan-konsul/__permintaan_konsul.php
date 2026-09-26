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


<div class='panel panel-flat' onload="startTime()">
    <div class="panel-heading">
        <h6 class="panel-title"><?= Yii::t('fe', 'Permintaan Konsul'); ?> - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h6>
    </div>    
    <div class="panel-toolbar clearfix">
        <?php 
        $attributes_custom_save = ['id'=>'btn-save-permintaan-konsul'];
        $attributes_custom_reset = ['id'=>'btn-reset-permintaan-konsul'];
        if($status_disabled === true){
            $attributes_custom_save['disabled'] = true;
            $attributes_custom_reset['disabled'] = true;
        }
        ?>
        <?= DocoHelpers::generateToolbar([
            'custom-save' => [
                'type' => 'submit',
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-floppy-o',
                'attributes' => $attributes_custom_save
            ],
           'ubah' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Ubah'),
                    'icon' => 'fa fa-pencil',
                    'method' => 'not exist',
                    'attributes' => [
                        'class' => 'data-ubah',
                        'id' => 'btn-ubah-permintaan-konsul',
                    ]
                ],
            'kembali'=>[
                'title' => \Yii::t('fe', 'Kembali'),
                'icon' => 'fa fa-arrow-left',
                'attributes' => [
                    'id' => 'btn-kembali-permintaan-konsul',
                    'class'=>'btn btn-success btn-labeled btn-xs btn btn-info data-kembali',
                ] 
            ],
            'custom-reset' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Muat Ulang'),
                'icon' => 'fa fa-refresh',
                'attributes' => $attributes_custom_reset
            ],
        ]) ?>
    </div>
    <div class="clearfix"><br></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">
                <?php $form = ActiveForm::begin([
                    'id' => 'permintaan-konsul-form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                ]) ?>
                <?= Html::hiddenInput('PermintaanKonsulForm[pendaftaran_id]', $model->pendaftaran_id);?>
                <?= Html::hiddenInput('PermintaanKonsulForm[pasienadmisi_id]', $model->pasienadmisi_id);?>
                <?= Html::hiddenInput('PermintaanKonsulForm[permintaankonsul_id]', '');?>
                <div class="form-group field-waktu_permintaan-permintaan-konsul required">
                    <div class="col-md-4">
                        <label class="control-label" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu permintaan'); ?></label>
                    </div>
                    <div class="col-md-8">
                        <b><span id="waktu-permintaan"></span></b>
                        <?= Html::hiddenInput('PermintaanKonsulForm[waktu_permintaan]', $model->waktu_permintaan);?>
                    </div>
                </div>

                <?= $form->field($model, 'dokter_id')->dropDownList(
                    $listfilter['listDokter'], [
                        'id'=>'dokter_id-permintaan-konsul',
                        'class' => 'form-control select2',
                        'prompt' => Yii::t('fe', '-- Pilih --'),
                        'style' => ['padding'=>'0 0 0 0']
                    ]
                ) ?>
                <?= $form->field($model, 'dokter_nama', ['options'=>['class'=>'hidden']])->staticInput(['id'=>'dokter_nama']) ?>

                <?= $form->field($model, 'jenis_konsul')->dropDownList(
                    $listfilter['listJenisKonsul'], [
                        'id'=>'jenis_konsul-permintaan-konsul',
                        'class' => 'form-control select2',
                        'prompt' => Yii::t('fe', '-- Pilih --'),
                        'style' => ['padding'=>'0 0 0 0']
                    ]
                ) ?>
                <?= $form->field($model, 'jenis_konsul_nama', ['options'=>['class'=>'hidden']])->staticInput(['id'=>'jenis_konsul_nama']) ?>

                <?= $form->field($model, 'ket_konsul')->textArea(); ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
    <div class="clearfix"><br></div>
    <div class="panel-body">
        <div class="row">
            <?php 
                    $form = ActiveForm::begin([
                        'id' => 'list-form',
                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
                        // 'enableAjaxValidation' => false,
                        // 'enableClientValidation'=>false,
                        // 'action' => '/ranap/pemeriksaan-rawat-inap/save-rekon-cache',
                        // 'formConfig' => ['labelSpan' => 4,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]); 
                ?>
                <label>
                    <h6 class="panel-title"><?= Yii::t('fe', 'Daftar permintaan konsultasi'); ?></h6> 
                </label>
                <div class="clearfix"><br></div>
                <table class="table datatable-basic table-striped table-hover dataTable" id="tb_permintaan_konsultasi" style="width:100%">
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
        var list_dokter_available = ' . json_encode($listfilter['listDokter']) . ';
    ', View::POS_END);
    $this->registerJs($this->render('_permintaankonsul.js',['pasienId'=>$pasienId]), View::POS_END);
?>