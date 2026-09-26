<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-09 11:00:02
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-10 16:18:23
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-serah-terima-keluarga'
                    ]);
                ?>
                <?=
                    DocoHelpers::generateToolbar([
                        'back',
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Informasi Pasien') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nama Pasien</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->nama_pasien ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tempat Lahir / Umur</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->umur ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Alamat</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->alamat_pasien ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Jenis Kelamin</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->jenis_kelamin ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Instalasi Asal</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->instalasi_asal ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Ruangan Asal</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->ruangan_nama ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Meninggal Pada Hari</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->tgl_meninggal ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Form Penyerahan Jenazah Kepada Keluarga') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <?php $form = ActiveForm::begin([
                                'id' => 'form', 
                                'action' => "/jenazah/informasi-pasien-meninggal/save-serah-terima-keluarga?id={$id}",
                                'enableAjaxValidation'=>false, 
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL] 
                            ]); 
                            echo $form->field($model, 'pasien_id')->hiddenInput(['value' => $data['pasien_id']])->label(false);
                            echo $form->field($model, 'pendaftaran_id')->hiddenInput(['value' => $id])->label(false);
                            echo $form->field($model, 'tgl_meninggal')->hiddenInput(['value' => $data['tgl_meninggal']])->label(false);
                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'kondisi', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ])->textArea([
                                        'placeholder' => Yii::t('fe', 'Kondisi / Ciri Khusus'),
                                        'class' => 'form-control input-sm',
                                        'rows' => 5,
                                    ])->label(Yii::t('fe', 'Kondisi / Ciri Khusus')); ?>
                                </div>
                            </div><hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=$form->field($model, 'tempat_lahir', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ]);?>
                                </div>
                                <div class="col-md-6">
                                    <?=$form->field($model, 'hubungan_keluarga', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ])->dropDownList(ArrayHelper::map($response['hubungan_keluarga'], 'lookup_id', 'lookup_name'), ['class'=>'select2','prompt'=> Yii::t('fe', 'Pilih')])->label(Yii::t('fe', 'Status Keluarga Jenazah'))?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'tgl_lahir', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-6'
                                            ],
                                    ])->widget(DatePicker::classname(), [
                                        'name' => 'tgl_lahir',
                                        'options' => ['value' => date('d-M-Y')],
                                        'readonly' => true,
                                        'language' => 'en',
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy',
                                            'endDate' => '0d',

                                        ]
                                    ])->label(Yii::t('fe', 'Tanggal Lahir')); ?>
                                </div>
                                <div class="col-md-6">
                                    <?=$form->field($model, 'nama_pengambil', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ])->label(Yii::t('fe', 'Nama Pengambil'));?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=$form->field($model, 'pekerjaan', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ]);?>
                                </div>
                                <div class="col-md-6">
                                    <?=$form->field($model, 'jenis_identitas', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ])->dropDownList(ArrayHelper::map($response['jenis_identitas'], 'lookup_id', 'lookup_name'), ['class'=>'select2','prompt'=> Yii::t('fe', 'Pilih')])?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'alamat', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ])->textArea([
                                        'placeholder' => Yii::t('fe', 'Alamat'),
                                        'class' => 'form-control input-sm',
                                        'rows' => 5,
                                    ]); ?>
                                </div>
                                <div class="col-md-6">
                                    <?=$form->field($model, 'no_identitas', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-6'
                                        ]
                                    ]);?>
                                </div>
                            </div>
                            <div class="row">
                                
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php 
$this->registerJs('
    $("#simpan-serah-terima-keluarga").on("click", function (event) {
        event.preventDefault();
        var _data = $("#form").serializeArray();
        $("#form").docoForm("submit",{
            data : _data,
            success : function (data) {
                var pendaftaran_id = data.response.pendaftaran_id;
                setTimeout(function(){
                    $("#simpan-serah-terima-keluarga").prop("disabled", true);
                }, 100);
                (new PNotify({
                    title: "&nbsp;Proses Berhasil !",
                    text: "Data Berhasil disimpan, apakah Anda ingin melakukan cetak?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: "Ya",
                                addClass: "btn btn-xs btn-success",
                            },
                            {
                                text: "Tidak",
                                addClass: "btn btn-xs btn-danger",
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on("pnotify.confirm", function() {
                    window.open("/jenazah/informasi-pasien-meninggal/cetak-serah-terima-keluarga2?pendaftaran_id="+pendaftaran_id);
                }).on("pnotify.cancel", function() {

                });
            }
        });
        $("#form").trigger("submit");
    })
', View::POS_END, 'b-index');
?>