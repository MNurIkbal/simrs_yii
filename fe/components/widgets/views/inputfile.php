<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\FileInput;

$dataPasien = (!empty($data['is_pasien']) && ($data['is_pasien'] == 1)) ? 1 : 0;

?>
<style>
    .isi-table {
        padding: 10px;
        height: 60px;
    }

    .modal {
        overflow-y: auto;
    }
</style>

<div class="panel" id="<?= $parent_id ?>">
    <div class="panel-white">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'upload-dokumen-pasien-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        //'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                            'enctype' => 'multipart/form-data'
                        ]
                    ]);
                    ?>
                    <?= $form->field($model, 'no_rekam_medik')->hiddenInput()->label(false); ?>
                    <?= $form->field($model, 'no_pendaftaran')->hiddenInput()->label(false); ?>
                    <?= Html::hiddenInput('DokumenPasienForm[doc_date]', '',['id' => 'doc_date']); ?>
                    <?= Html::hiddenInput('DokumenPasienForm[ruangan_id]', '',['id' => 'ruangan_id']); ?>
                    <?= Html::hiddenInput('DokumenPasienForm[dokter_id]', '',['id' => 'dokter_id']); ?>
                    <?= Html::hiddenInput('widget_pendaftaran_id', isset($pendaftaran_id) ? $pendaftaran_id : '',['id' => 'widget_input-file_pendaftaran_id']); ?>
                    <?php if(!$isHide) : ?>
                    <div class="col-md-4 required">
                        <?php if(!empty($is_pasienid) && $is_pasienid!=null && $is_pasienid!='null') { ?>
                            <?= $form->field($model, 'nama_dokumen_freetext', [
                                'horizontalCssClasses' => [
                                    'label' => 'control-label',
                                    'wrapper' => 'col-md-8',
                                ]
                            ])->label(Yii::t('fe', 'Nama Dokumen')) ?>
                        <?php } else { ?>
                            <?= $form->field($model, 'dokumen_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'control-label',
                                    'wrapper' => 'col-md-8',
                                ]
                            ])->dropDownList($data, ['class' => 'dokumen-select2 required', 'prompt' => '— Pilih —', 'id' => 'dokumen_id'])->label(Yii::t('fe', 'Nama Dokumen')) ?>
                        <?php } ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'is_eklaim')->checkbox(['class' => 'is_eklaim', 'disabled' => $isDisabled])->label(Yii::t('fe', 'Dokumen Eklaim')); ?>
                    </div>
                    <div class="col-md-2 required">
                        <?= $form->field($model, 'attachment', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left',
                                'wrapper' => 'col-md-2',
                                'id' => 'file-logo-header',
                                'class' => 'custom-file-upload'
                            ]
                        ])->fileInput([
                            'class' => 'custom-file-upload',
                            'id' => 'file',
                        ])->label(Yii::t('fe', 'File') . ' <i>(Max 20MB)</i>');
                        ?>

                    </div>
                    <div class="col-md-2">
                        <div style="padding-top:5px">
                            <?= Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-sm btn-info btn-upload', 'id' => 'btn-upload', 'onclick' => '']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                </div>
                <?php if ($dataPasien == 0) : ?>
                <table class="table table-bordered table-hover tabel-upload" id="table-dokumen" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Nama Dokumen</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">Belum Ada Data Tersedia</td>
                        </tr>
                    </tbody>
                </table>
                <?php else: ?>
                <table class="table table-bordered table-hover tabel-upload" id="table-dokumen" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Tanggal Kunjungan / No Pendaftaran</th>
                            <th>Ruangan / Kamar</th>
                            <th>Dokter Pemeriksa</th>
                            <th>Nama Dokumen</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">Belum Ada Data Tersedia</td>
                        </tr>
                    </tbody>
                </table>
                <?php endif; ?>
                <!-- <div class="row">
                    <div class="col-md-6">
                        <?php
                        // echo $form->field($model, 'attachment', [
                        //     'horizontalCssClasses' => [
                        //     'label' => 'text-left control-label col-sm-4',
                        //     'wrapper' => 'col-md-8',
                        //     'id' => 'file-logo-header',
                        //     'class' => 'custom-file-upload'
                        // ]])->widget(FileInput::classname(),[
                        // 	'pluginOptions'=>[]
                        // ]);
                        ?>
                    </div>
                </div> -->
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <!-- <div class="row">
	        <div class="col-md-12">
	        	<table class="table-striped table-condensed table-hover no-footer">
	        		<thead>
	        			<tr class="bg-inverse">
	        				<th>a</th>
	        			</tr>
	        		</thead>
	        		<tbody>
	        			<tr>
	        				<td>1</td>
	        			</tr>
	        		</tbody>
	        	</table>
	        </div>
	    </div> -->
    </div>
</div>

<?php
$this->registerJs('
    var _dokumen = `' . json_encode($dokumen) . '`
    var _dokumen_eklaim = `' . json_encode($dokumen_eklaim) . '`
    var _dokumenArr = []
    var _modul = `' . Yii::$app->controller->module->id . '`
    var _controller = `' . Yii::$app->controller->id . '`
    var parent_id = `' . $parent_id . '`
    var unique_id = `' . $pendaftaran_id . '`
    
    var dataPasien = `' . $dataPasien . '`
    var is_pasienid = parseInt(`'.$is_pasienid.'`)
    var isHide = `' . $isHide . '`

' . $this->render("upload.js"), View::POS_END);
