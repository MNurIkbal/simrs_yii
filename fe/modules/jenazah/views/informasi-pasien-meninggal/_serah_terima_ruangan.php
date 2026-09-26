<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-09 11:00:02
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-09 14:32:36
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
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-serah-terima',
                        'disabled' => ($data['status_periksa'] == 581) ? false : true,
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
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nama Perawat</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->pegawai_ruangan ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Jabatan</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->jabatan_nama ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Form Serah Terima Ruangan') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <?php $form = ActiveForm::begin([
                                'id' => 'form', 
                                'action' => "/jenazah/informasi-pasien-meninggal/save-serah-terima?id={$id}",
                                'enableAjaxValidation'=>false, 
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL] 
                            ]); 
                            echo $form->field($model, 'pasien_id')->hiddenInput(['value' => $data['pasien_id']])->label(false);
                            echo $form->field($model, 'pendaftaran_id')->hiddenInput(['value' => $id])->label(false);
                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'pegawai_id',[
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3 text-bold',
                                            'wrapper' => 'col-md-6'
                                        ],
                                    ])->dropDownList(ArrayHelper::map($response['pegawai'], 'pegawai_id', 'nama_pegawai'),[
                                        'class' => 'form-control select2',
                                        'id' => 'pegawai_id',
                                        'prompt' => Yii::t('fe', 'Pilih Petugas Jenazah'),
                                    ])->label(Yii::t('fe', 'Petugas Jenazah')); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($model, 'jabatan_pegjenazah', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3 text-bold',
                                        ]
                                    ])->staticInput(['class' => 'jabatan_pegjenazah'])->label(Yii::t('fe', 'Jabatan')); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $model->tglserah_terima = date('d-M-Y'); ?>
                                    <?= $form->field($model, 'tglserah_terima', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ],
                                    ])->widget(DatePicker::classname(), [
                                        'name' => 'tglserah_terima',
                                        'value' => date('Y-m-d'),
                                        'readonly' => true,
                                        'language' => 'en',
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy',
                                            'endDate' => "0d",
                                            'startDate' => date('d-m-Y', strtotime($tanggal_pendaftaran)),
                                        ]
                                    ])->label(Yii::t('fe', 'Tanggal Serah Terima')); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($model, 'is_alatlepas', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ],
                                    ])->checkbox(['label' => Yii::t('fe', 'Ya'), 'class' => 'styled'], false)->label(Yii::t('fe', 'Peralatan Sudah di Lepas')); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->field($model, 'catatan', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3 text-bold',
                                            'wrapper' => 'col-md-12'
                                        ]
                                    ])->textArea([
                                        'placeholder' => Yii::t('fe', 'Catatan'),
                                        'class' => 'form-control input-sm',
                                        'rows' => 5,
                                    ]); ?>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var pendaftaran_id = "'.$id.'";
    $(document).ready(function(){
        $(".jabatan_pegjenazah").text("");
        $("#pegawai_id").on("change", function(){
            var _pegawai_id = $(this).val();
            if(_pegawai_id) {
                $.ajax ({
                    type: "GET",
                    url: "/jenazah/informasi-pasien-meninggal/search-pegawai",
                    data: { pegawai_id: _pegawai_id },
                    success : function(response) {
                        var result = response.result;
                        $(".jabatan_pegjenazah").text(result.jabatan);
                    }
                });
            }
        });

        $(".styled, .multiselect-container input").uniform({
            radioClass: \'choice\'
        });
    });
    
    
    
    $("#simpan-serah-terima").on("click", function (event) {
        event.preventDefault();
        var _data = $("#form").serializeArray();
        $("#form").docoForm("submit",{
            data : _data,
            success : function (data) {
                setTimeout(function(){
                    $("#simpan-serah-terima").prop("disabled", true);
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
                    window.open("/jenazah/informasi-pasien-meninggal/cetak-serah-terima?pendaftaran_id="+pendaftaran_id);
                }).on("pnotify.cancel", function() {

                });
            }
        });
        $("#form").trigger("submit");
    })
', View::POS_END, 'b-index');
?>
