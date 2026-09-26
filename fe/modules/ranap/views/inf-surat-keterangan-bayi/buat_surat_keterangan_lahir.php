<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/dashboard']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$instalasi_id = DocoHelpers::encrypt(Yii::$app->docoVars->workspace('instalasi_id'));
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
             <!-- breadcrumbs replace with this -->
             <div class="row">
              <div class="column-1">
                <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
            </div>
            <div class="column-2">
                <h3 class="panel-title"><b><?= $title; ?></b></h3>
                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
            </div>
        </div>
        <!-- end -->
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <?=
        DocoHelpers::generateToolbar([
            'save' => [
                'attributes' => [
                    'id' => 'buat-surat_keterangan-lahir',
                    'onClick' => false
                ]
            ],
            'pdf' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'attributes'=>[
                            'data-id' => $pendaftaran_id,
                            'id' => 'print-skl',
                            'data-target'=>Url::home().'ranap/inf-surat-keterangan-bayi/export-pdf?pendaftaran_id='
                      ],
                    ],
            'back' => [
                'attributes' => [
                    'href' => 'index'
                ]
            ]
        ]) ?>

    </div>
    <div class="panel-body">
        <!-- identitas pasien start -->
        <?=Yii::$app->controller->renderPartial('_pasien_identitas', [
            'data_pasien' => $data_pasien
        ]);?>
        <!-- identitas pasien end -->

        <hr>

        <?php $form = ActiveForm::begin([
            'id' => 'buat-surat_keterangan-lahir-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
        ]) ?>

        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Ayah'); ?></b></h6>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'ayah_nama',[
                        ])->textInput([
                            'placeholder' => $model->getAttributeLabel('ayah_nama'),
                            'class' => 'form-control input-sm'
                        ]); ?>

                        <?= $form->field($model, 'ayah_ktp',[
                        ])->textInput([
                            'placeholder' => $model->getAttributeLabel('ayah_ktp'),
                            'class' => 'form-control input-sm'
                        ]); ?>

                        <?= $form->field($model, 'ayah_alamat',[
                        ])->textInput([
                            'placeholder' => $model->getAttributeLabel('ayah_alamat'),
                            'class' => 'form-control input-sm'
                        ]); ?>

                        <?= $form->field($model, 'ayah_pekerjaan_id')->dropDownList(
                            $data_pekerjaan, [
                                'id'=>'ayah_pekerjaan_id',
                                'class' => 'form-control select2',
                                'prompt' => Yii::t('fe', '-- Pilih --'),
                                'style' => ['padding'=>'0 0 0 0']
                            ]
                        ) ?>

                        <?= $form->field($model, 'ayah_golongandarah_id')->dropDownList(
                            $data_golongan_darah, [
                                'id'=>'lookup_id',
                                'class' => 'form-control select2',
                                'prompt' => Yii::t('fe', '-- Pilih --'),
                                'style' => ['padding'=>'0 0 0 0']
                            ]
                        ) ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Bayi'); ?></b></h6>
                    </div>
                    <div class="panel-body">
                        <?php 
                        //$form->field($model, 'hari_lahir')->dropDownList(
                        //    $data_hari, [
                        //        'id'     =>'lookup_id',
                        //        'class'  => 'form-control select2',
                        //        'prompt' => Yii::t('fe', '-- Pilih --'),
                        //        'style'  => ['padding'=>'0 0 0 0']
                        //    ]
                        //) 
                        ?>

                        <?= $form->field($model, 'tgl_lahir', [
                        ])->textInput([
                            'class' => ' form-control input-sm pickadate',
                        ]); 
                        ?>

                        <?= $form->field($model, 'jam_lahir',[
                            'addon' => [
                                'append' => [
                                    'content' => 'WIB'
                                ]
                            ]
                        ])->textInput([
                            'class' => ' form-control input-sm timepicker',
                        ]); ?>

                        <?= $form->field($model, 'bb_lahir',[
                            'addon' => [
                                'append' => [
                                    'content' => 'Gram'
                                ]
                            ]
                        ])->textInput([
                            'placeholder' => $model->getAttributeLabel('bb_lahir'),
                            'class'       => 'form-control input-sm'
                        ]); ?>

                        <?= $form->field($model, 'panjang_lahir',[
                            'addon' => [
                                'append' => [
                                    'content' => 'Cm'
                                ]
                            ]
                        ])->textInput([
                            'placeholder' => $model->getAttributeLabel('panjang_lahir'),
                            'class'       => 'form-control input-sm'
                        ]); ?>

                        <?= $form->field($model, 'kelahiran',[
                        ])->textInput([
                            'placeholder' => $model->getAttributeLabel('kelahiran'),
                            'class'       => 'form-control input-sm'
                        ]); ?>

                        <?= $form->field($model, 'anakke',[
                        ])->textInput([
                            'placeholder' => $model->getAttributeLabel('anakke'),
                            'class'       => 'form-control input-sm',
                            'type'        => 'number'
                        ]); ?>

                        <?= $form->field($model, 'golongan_darah')->dropDownList(
                            $data_golongan_darah, [
                                'id'     =>'lookup_id',
                                'class'  => 'form-control select2',
                                'prompt' => Yii::t('fe', '-- Pilih --'),
                                'style'  => ['padding'=>'0 0 0 0']
                            ]
                        ) ?>

                        <?= $form->field($model, 'pendaftaran_id',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['pendaftaran_id']
                        ]); ?>

                        <?= $form->field($model, 'pasienadmisi_id',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['pasienadmisi_id']
                        ]); ?>

                        <?= $form->field($model, 'pasien_id',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['pasien_id']
                        ]); ?>

                        <?= $form->field($model, 'dokterdpjp_id',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['dokter_id']
                        ]); ?>

                        <?= $form->field($model, 'ibu_nama',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['nama_ibu']
                        ]); ?>

                        <?= $form->field($model, 'ibu_ktp',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['no_identitas']
                        ]); ?>

                        <?= $form->field($model, 'ibu_alamat',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['alamat']
                        ]); ?>

                        <?= $form->field($model, 'ibu_pekerjaan',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['pekerjaan']
                        ]); ?>

                        <?= $form->field($model, 'ibu_golongandarah',[
                        ])->label(false)->hiddenInput([
                                //'value'       => $data_pasien['golongan_darah']
                        ]); ?>

                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end() ?>

    </div>
</div>
</div>
</div>

<?php
$this->registerJs($this->render('js/buat_surat_keterangan_lahir.js'), View::POS_END);
?>
