<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .datepicker>div{
        display:block;
    }
    .plat-nomor{
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #fff;
    }
    .background-plat{
        /*border: 1px solid transparent;*/
        text-align: center;
        display: inline-block;
        position: relative;
        width: 165px;
        padding: 5px;
        border-color: #fff;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #54be8b; /*#001;*/
        /*border: 1px solid #291ce8;*/
    }

    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }
    .my-legend .legend-scale ul li {
        display: contents;
        float: left;
        width: 50px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 50px;
        border: solid 0.2px;
    }
    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }
    .my-legend a {
        color: #777;
    }

    .square-batal {
        height: 30px;
        width: 70px;
        background-color: rgba(255, 188, 188, 0.58);
        color:#ffffff;
        padding: 5px 0 5px 10px;
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
                    <?=DocoHelpers::generateToolbar([
                        'save' => [
                            'attributes' => [
                                'onClick' => false,
                                'id' => 'simpan'
                            ]
                        ],
                        'back'
                    ],'#example');?>
                </div>
                <div class="panel-body">
                    <div class="col-md-12 info-pengajuan">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pemesan'); ?></b></h6>
                            </div>
                            <div class="panel-body">
                                <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'pasien-rs-form',
                                        'enableAjaxValidation'=>false,
                                        'enableClientValidation'=>false,
                                        'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 4,
                                            'deviceSize' => ActiveForm::SIZE_MEDIUM
                                        ],
                                        'options' => [
                                            'skip-confirm' => "true"
                                        ]
                                    ]);

                                    echo Html::hiddenInput('id_parent', $id, [
                                        'class' => 'id_parent_ambulan'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[ambulan_id]', $model->ambulan_id, [
                                        'class' => 'ambulan_id_rs'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[jenis]', "rs", [
                                        'class' => 'jenis'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[pendaftaran_id]', $model->pendaftaran_id, [
                                        'class' => 'pendaftaran_id'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[tgl_pesanambulan]', $model->tgl_pesanambulan, [
                                        'class' => 'tgl_pesanambulan'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[no_pesanambulan]', $model->no_pesanambulan, [
                                        'class' => 'no_pesanambulan_rs'
                                    ]);

                                    $model->pasien_id = !empty($pasien['pasien_id']) ? $pasien['pasien_id'] : null;

                                    echo Html::hiddenInput('PesanAmbulanForm[pasien_id]', $model->pasien_id, [
                                        'id' => 'pasien_id'
                                    ]);
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'no_rekam_medik', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-3'
                                            ]
                                        ])->staticInput(['class' => 'no_rekam_medik'])->label(Yii::t('fe', 'No Rekam Medik')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                            $model->jenis_kelamin = !empty($header['jns_kelamin'])
                                                ? $header['jns_kelamin'] : null;
                                        ?>
                                        <?= $form->field($model, 'jenis_kelamin', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-3'
                                            ]
                                        ])->staticInput(['class' => 'jenis_kelamin'])->label(Yii::t('fe', 'Jenis Kelamin')); ?>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'nama_pemesan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'nama_pemesan',
                                        ])->label(Yii::t('fe', 'Nama Pasien')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'instalasi_asal', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'instalasi_asal',
                                        ])->label(Yii::t('fe', 'Instalasi Asal')); ?>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                            $model->tgl_lahir = date('d-M-Y',strtotime($model->tgl_lahir));
                                        ?>
                                        <?= $form->field($model, 'tgl_lahir', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'tgl_lahir',
                                        ])->label(Yii::t('fe', 'Tanggal Lahir')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'ruangan_asal', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'ruangan_asal',
                                        ])->label(Yii::t('fe', 'Ruangan Asal')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'tempat_lahir', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'tempat_lahir',
                                        ])->label(Yii::t('fe', 'Tempat Lahir')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                            $model->diagnosa_pasien = !empty($pasien['diagnosa'])
                                                ? $pasien['diagnosa'] : null;
                                        ?>
                                        <?= $form->field($model, 'diagnosa_pasien', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'diagnosa_pasien',
                                        ])->label(Yii::t('fe', 'Diagnosa Pasien')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $model->kelaspelayanan_nama = !empty($pasien['kelaspelayanan_nama'])
                                            ? $pasien['kelaspelayanan_nama'] : null ?>
                                        <?= $form->field($model, 'kelaspelayanan_nama', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'kelaspelayanan_nama',
                                        ])->label(Yii::t('fe', 'Kelas Pelayanan')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $model->carabayar_nama = !empty($pasien['carabayar_nama'])
                                            ? $pasien['carabayar_nama'] . ' / ' . $pasien['penjamin_nama'] : null ?>
                                        <?= $form->field($model, 'carabayar_nama', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->staticInput([
                                            'class' => 'carabayar_nama',
                                        ])->label(Yii::t('fe', 'Cara bayar / Penjamin')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'tujuan_pasien', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-9'
                                            ]
                                        ])->textArea([
                                            'class' => 'form-control input-sm',
                                            'rows' => 3
                                        ])->label(Yii::t('fe', 'Tujuan')); ?>
                                    </div>
                                </div>
                                <legend><b><li>Informasi Ambulan</li></b></legend>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-left control-label col-sm-4 text-bold">Tanggal Pemesanan</label>
                                            <div class="col-md-8">
                                                <?php
                                                    echo Html::button('<b><i class="fa fa-search"></i></b>' . Yii::t('fe','Cari Ketersediaan Ambulan'),[
                                                        'class' => 'btn btn-success btn-labeled btn-xs',
                                                        'action' => Url::home().'ambulan/informasi-permintaan-ambulan/list-pemesanan?tipe=rs',
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#modal_backdrop',
                                                        'data-width' => '75%',
                                                    ]);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-left control-label col-sm-4 text-bold">Jenis Ambulan</label>
                                        <div class="col-md-8">
                                            <p style="margin-top:9px"><b class="is_emergency">
                                                <?= !empty($model->jenis_ambulan) ? $model->jenis_ambulan : '-' ?>
                                            </b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-left control-label col-sm-4 text-bold">No. Polisi</label>
                                        <div class="col-md-8 background-plat">
                                            <span class="plat-nomor"><?= !empty($model->no_polisi) ? $model->no_polisi : '-' ?></span>
                                        </div>
                                    </div>
                                </div>

                                <legend><b><li>Kondisi Pasien</li></b></legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kesadaran', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm',
                                        ])->label(Yii::t('fe', 'Kesadaran Pasien')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'tanda_vital', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm',
                                        ])->label(Yii::t('fe', 'Tanda Vital Pasien')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'td_diastolic', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm doco-number',
                                        ])->label(Yii::t('fe', 'Tekanan Darah Pasien')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'detaknadi', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm doco-number'
                                        ])->label(Yii::t('fe', 'Nadi')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'respirasi', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm doco-number',
                                        ])->label(Yii::t('fe', 'Respirasi Pasien')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'saturasi', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-6'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm doco-number',
                                        ])->label(Yii::t('fe', 'Saturasi O2')); ?>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                                <legend><li>Obat Alkes</li></legend>
                                <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'add-obat-alkes',
                                        'enableAjaxValidation' => false,
                                        'enableClientValidation'=> false,
                                        'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 4,
                                            'deviceSize' => ActiveForm::SIZE_MEDIUM
                                        ],
                                        'options' => [
                                            'skip-confirm' => "true"
                                        ]
                                    ]);
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $form->field($modelObat, 'obatalkes_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-5 text-bold',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList([],[
                                            'class' => '',
                                            'id' => 'obatalkes_id_rs',
                                        ])->label(Yii::t('fe', 'Obat Alkes')); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?= $form->field($modelObat, 'qty', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm doco-number qty_obat_rs text-right',
                                        ])->label(Yii::t('fe', 'Qty')); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-3">
                                            <?= Html::submitButton(
                                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'),
                                                [
                                                    'class' => 'btn btn-success btn-labeled btn-xs btn-tambah-obat-rs',
                                                ])
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="tabel-temp-obat-rs" class="table table-striped table-condensed table-hover" style="width:100%">
                                            <thead>
                                                <tr class="bg-inverse">
                                                    <th width="1">No</th>
                                                    <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                                    <th><?=\Yii::t("fe", "Qty");?></th>
                                                    <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" colspan="8">
                                                        <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                                <legend><li>Tindakan Pelayanan</li></legend>
                                    <?php
                                        $form = ActiveForm::begin([
                                            'id' => 'add-tindakan',
                                            'enableAjaxValidation' => false,
                                            'enableClientValidation'=> false,
                                            'type' => ActiveForm::TYPE_HORIZONTAL,
                                            'formConfig' => [
                                                'labelSpan' => 4,
                                                'deviceSize' => ActiveForm::SIZE_MEDIUM
                                            ],
                                            'options' => [
                                                'skip-confirm' => "true"
                                            ]
                                        ]);
                                    ?>
                                    <div class="row">
                                            <div class="col-md-4">
                                                <?= $form->field($addTindakan, 'daftartindakan_id', [
                                                        'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-4 text-bold',
                                                            'wrapper' => 'col-md-8'
                                                        ]
                                                        ])->dropDownList([],[
                                                            'class' => 'select2',
                                                            'id' => 'daftartindakan_id',
                                                        ]); ?>
                                            </div>
                                            <div class="col-md-2">
                                                <?= $form->field($addTindakan, 'qty', [
                                                'horizontalCssClasses' => [
                                                        'label' => 'text-left control-label col-sm-3 text-bold',
                                                        'wrapper' => 'col-md-5'
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => Yii::t('fe', 'Qty'),
                                                    'class' => 'form-control input-sm doco-number text-right qty-tindakan',
                                                    'maxlength' => 3
                                                ])->label(Yii::t('fe', 'Qty')); ?>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="col-md-3">
                                                    <?= Html::button(
                                                        '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'),
                                                        [
                                                            'class' => 'btn btn-success btn-labeled btn-xs btn-tambah',
                                                        ])
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php ActiveForm::end(); ?>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table id="tabel-temp" class="table table-striped table-condensed table-hover" style="width:100%">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th width="1">No</th>
                                                        <th><?=\Yii::t("fe", "Tindakan");?></th>
                                                        <th><?=\Yii::t("fe", "Kelompok Biaya");?></th>
                                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                                        <th><?=\Yii::t("fe", "Tarif Satuan");?></th>
                                                        <th><?=\Yii::t("fe", "Jumlah Tarif");?></th>
                                                        <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center" colspan="8">
                                                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5" class="text-right"><b>Estimasi Biasa</b></td>
                                                        <td class="text-right"><b><span class="estimasi_biaya"></span></b></td>
                                                        <td class="text-right"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                            <div class="col-md-12">
                                                <div class='my-legend'>
                                                    <div class='legend-title'>Keterangan</div>
                                                    <div class='legend-scale'>
                                                    <ul class='legend-labels'>
                                                        <li><span style='background:rgba(255, 188, 188, 0.58);'></span>&nbsp;&nbsp;<b>Tindakan / Obat tidak tersedia</b></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var _table;
    var _tableObat;
    var ambulanId;
    var _idParent = "'.$id.'";
    $(document).ready(function() {
        _table = $("#tabel-temp").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[2, "asc"]],
            ajax: baseUrl+"ambulan/informasi-permintaan-ambulan/get-data-tindakan?id='. $id .'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Tindakan",
                    data: "daftartindakan_nama",
                    orderable: false
                },
                {
                    title: "Kelompok Biaya",
                    data: "kelompok_biaya",
                    orderable: false
                },
                {
                    title: "Qty",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    width: "10%",
                    class: "text-right"
                },
                {
                    title: "Tarif Satuan (Rp.)",
                    data: "tarif_satuan_label",
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "Jumlah Tarif (Rp.)",
                    data: "jumlah_tarif_label",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var estimasi_biaya = 0;
                $.each(dataRows, function (key, val) {
                    estimasi_biaya += parseInt(val.jumlah_tarif);
                });
                $(".estimasi_biaya").text(docoHelper.convertToRupiah(estimasi_biaya));
            },
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var _harga = parseInt(aData.harga_tariftindakan);
                if (_harga == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                    $(nRow).find(\'input\').prop("disabled",true);
                }
            }
        });
        $("input[type=radio]").uniform({
            radioClass: \'choice\'
        });
        $("#pesanambulanform-tgl_lahir").trigger("change");
        $("#daftartindakan_id").select2({
            placeholder: "Pilih",
            minimumInputLength: 3,
            ajax : {
                url: "/ambulan/permintaan-ambulan/search-tindakan",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var _ambulan_id = $(".ambulan_id_rs").val();
                  params.ambulan_id = _ambulan_id;
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        }).on("select2:select", function(e){
            var data = e.params.data;
        });
        $("#obatalkes_id_rs").select2({
            placeholder: "Pilih",
            minimumInputLength: 3,
            ajax : {
                url: "/ambulan/permintaan-ambulan/search-obat",
                dataType: \'json\',
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: \'bigdrop\',
                escapeMarkup: function (m) { return m; },
            },
        }).on(\'select2:select\', function(e){
            var data = e.params.data;
        });

        _tableObat = $("#tabel-temp-obat-rs").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[1, "asc"]],
            ajax: baseUrl+"ambulan/informasi-permintaan-ambulan/get-list-obat?id='. $id .'" ,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Nama Obat Alkes",
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "Qty",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var stok = parseInt(aData.stok);
                if (stok == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                    $(nRow).find("input").prop("disabled",true);
                }
            }
        });

    });

    $(document).on(\'click\',\'.delete-cache-obat-rs\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            skipConfirm : true,
            success : function (data) {
                _tableObat.draw();
            }
        });
    });

    $(document).on("click", ".btn-tambah-obat-rs", function (event) {
        event.preventDefault();
        var dataPost = $(\'#add-obat-alkes\').serializeArray();
        dataPost.push({
            name : "id",
            value : "'.$id.'"
        });
        $(this).docoForm("click", {
            url: "/ambulan/informasi-permintaan-ambulan/add-obat",
            method: "POST",
            type: "json",
            data: dataPost,
            skipConfirm: true,
            success: function (data) {
                $("#obatalkes_id_rs").val(\'\').trigger(\'change\');
                $(".qty_obat_rs").val(\'\')
                _tableObat.draw();
            }
        });
    });

    $("#simpan").on("click", function(event){
        event.preventDefault();

        $(this).docoForm("click", {
            url : "/ambulan/informasi-permintaan-ambulan/save-pasien",
            method : "POST",
            type : "json",
            data: $("#pasien-rs-form").serializeArray(),
            success : function (data) {
                console.log(data);
                $("#simpan").prop("disabled","disabled");
                (new PNotify({
                    title: "Berhasil",
                    text: "Ambulan dengan Nomor " + "<strong>" + data.response.no_pesanambulan + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak Surat Tugas?",
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
                    // Print
                    window.open("/ambulan/informasi-permintaan-ambulan/cetak-surat-tugas?id="+data.response.id);
                }).on("pnotify.cancel", function() {

                });
                _table.draw();
            }
        });
    });

    $(".btn-tambah").on("click", function (event) {
        event.preventDefault();
        var _data = $("#add-tindakan").serializeArray();
        _data.push({
            name : "id",
            value : "'.$id.'"
        });
        $().docoForm("click",{
            data : _data,
            url : "/ambulan/informasi-permintaan-ambulan/add-tindakan",
            skipConfirm : true,
            success : function (data) {
                $("#daftartindakan_id").val("").trigger("change");
                $(".qty-tindakan").val("").trigger("change");
                _table.draw();
            }
        });
    });

    $(document).on("keyup", ".qty", function(e) {
        var _qtyObat = parseInt($(this).val());
        var _curr = parseInt($(this).attr(\'data-val\'));
        if (_qtyObat > 0) {
            var dataPost = {
                daftartindakan_id: $(this).attr("data-id"),
                qty: $(this).val(),
                id : "'. $id .'"
            };
            $.ajax({
                method: \'POST\',
                data: dataPost,
                url: \'/ambulan/informasi-permintaan-ambulan/update-cache\',
                success: function(data) {
                    _table.draw();
                }
            });
            return true;
        }
        docoNotification("error","Proses Gagal !", "Qty tidak boleh 0.");
        $(this).val(_curr);
    });

    $(document).on("change", "#pesanambulanform-tgl_lahir", function(){
        var tgl_lahir = $(this).val();
        if(tgl_lahir != \'\'){
            umur = getUmur(convertTanggalYmd(tgl_lahir), new Date());
        }
        $(\'.umur\').text(umur);
    });

    $(document).on("click",".delete-cache-tindakan", function(event) {
        event.preventDefault();
        $(this).docoForm("delete",{
            skipConfirm : true,
            success : function (data) {
                _table.draw();
            }
        });
    });

', View::POS_END, 'b-index');
