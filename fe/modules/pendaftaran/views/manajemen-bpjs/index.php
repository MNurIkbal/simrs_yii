<?php

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .btn-cari {
        width:80px;
    }
    .btn-simpan {
        width:80px;
    }
    .data-pasien {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .nama-pasien {
        height: 35px;
        background: #49ce8e6b;
    }
    #bpjsnew_detail_nama {
        color: #333333;
        margin-left: 10px;
        padding-top: 10px;
    }
    .info-bpjs {
        height: 32px;
        vertical-align: middle;
    }
    .info-bpjs > p {
        margin-left: 10px;
        font-size: 12px;
        padding-top: 5px;
    }
    .isi-data-pasien {
        height: 200px;
        background:#F2FDF7;
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
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'form',
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'formConfig' => [
                        'labelSpan' => 5,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ]
                ]) ?>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'jenis_pencarian')
                            ->radioList(
                                [
                                    '1'=> Yii::t('fe', 'Nomor SEP'),
                                    // '2'=> Yii::t('fe', 'Rujukan'),
                                    // '3'=> Yii::t('fe', 'Rujukan Manual/IGD'),
                                ],
                                ['id'=>'jenis_pencarian', 'name'=>'jenis_pencarian', 'inline'=>true]
                            ); 
                        ?>

                        <?= $form->field($model, 'no_sep', [
                                'inputOptions' => [
                                    'id' => 'no_sep',
                                    'class' => 'form-control input-sm'
                                ]
                            ])->textInput(['class' => 'no_sep']) ?>
                    </div>
                </div>
                
                <button type="button" class="btn btn-info btn-sm btn-cari"><?=Yii::t('fe','Cari')?></button>
                <br>
                <hr>

                <div class="row">
                    <div id="info-pasien" style="display: none;">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <div class="content-group">
                                    <div class="bg-indigo-300 border-radius-top nama-pasien">
                                        <div id="bpjsnew_detail_nama">Nama Pasien</div>
                                    </div>
                                    <div class="no-margin no-border-radius bg-teal-400 border-top border-top-teal-300 info-bpjs">
                                        <p>Info Bpjs</p>
                                    </div>
                                    <div class="tab-content panel-body" style="border: 1px solid #dddddd;background:#f2fdf7;">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="col-md-4">No Kartu</div>
                                                <div class="col-md-8" id="bpjsnew_detail_nokartu">:&nbsp;-</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="col-md-4">NIK</div>
                                                <div class="col-md-8" id="bpjsnew_detail_nik">:&nbsp;-</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="col-md-4">Tanggal Lahir</div>
                                                <div class="col-md-8" id="bpjsnew_detail_tgl_lahir">:&nbsp;-</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="col-md-4">Jenis Peserta</div>
                                                <div class="col-md-8" id="bpjsnew_detail_jenis_peserta">:&nbsp;-</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="col-md-4">Hak Kelas</div>
                                                <div class="col-md-8" id="bpjsnew_detail_hak_kelas">:&nbsp;-</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="col-md-4">TMT/TAT</div>
                                                <div class="col-md-8" id="bpjsnew_detail_tmt_tat">:&nbsp;-</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="col-md-4">Kode/Provinsi</div>
                                                <div class="col-md-8" id="bpjsnew_detail_ppk_rujukan">:&nbsp;-</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="col-md-4">Status Peserta</div>
                                                <div class="col-md-8" id="bpjsnew_detail_status_peserta">:&nbsp;-</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div align="center">
                                            <button type="button" class="btn btn-detail-bpjs btn-info btn-labeled btn-xs" action="<?= Url::home() ?>pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=" data-toggle="modal" data-target="#modal_backdrop" data-width="90%"><b><i class="fa fa-eye"></i></b>History BPJS</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?= Yii::$app->controller->renderPartial('form-sep',[
                                    'form' => $form,
                                    'model' => $model,
                                ]);?>
                                
                            
                        </div>
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-success btn-sm btn-rujukan_internal" action="<?= Url::home() ?>pendaftaran/manajemen-bpjs/modal-rujukan-internal" data-toggle="modal" data-target="#modal_backdrop" data-width="70%" disabled="true"><?=Yii::t('fe','Rujukan Internal')?></button>
                            <button type="button" class="btn btn-danger btn-sm btn-hapus"><?=Yii::t('fe','Hapus')?></button>
                            <button type="submit" class="btn btn-info btn-sm btn-simpan"><?=Yii::t('fe','Simpan')?></button>
                        </div>
                    </div>

                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs("
        var rujukan_internal = null;

        function cetakRujukanInternal(identifier) {
            var nosep = $(identifier).data('nosep');
            var kddokter = $(identifier).data('kddokter');
            var kdpolituj = $(identifier).data('kdpolituj');
        
            window.open('/pendaftaran/manajemen-bpjs/print-sep-internal?nosep='+nosep+'&kddokter='+kddokter+'&kdpolituj='+kdpolituj, '_blank');
        }

        $(document).on('click', '.btn-hapus-rujukan', function(e) {
            var nosep = $(this).data('nosep');
            var nosurat = $(this).data('nosurat');
            var kdpolituj = $(this).data('kdpolituj');
            var tglrujukinternal = $(this).data('tglrujukinternal');
            e.preventDefault();

            $(this).docoForm('delete',{
                url: baseUrl+'pendaftaran/manajemen-bpjs/hapus-sep-internal?nosep='+nosep+'&nosurat='+nosurat+'&kdpolituj='+kdpolituj+'&tglrujukinternal='+tglrujukinternal,
                success : function (data) {
                    $('#modal_backdrop').modal('hide');
                    $('.btn-cari').trigger('click');
                }
            });
            return false;
        });
    ", View::POS_END, 'index');
    $this->registerJs($this->render('js/manajemen-bpjs.js'));
    $this->registerJs($this->render('js/bpjs-helper.js'));
?>
