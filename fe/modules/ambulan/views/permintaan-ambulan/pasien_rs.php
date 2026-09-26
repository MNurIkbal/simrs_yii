<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:33:35
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-31 14:03:28
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
?>
<style>
    .datepicker>div{
        display:block;
    }
/*    position: relative; top: -0.5em; font-size: 80%;*/
    .plat-nomor-rs{
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #fff;
    }
    .background-plat{
        text-align: center;
        display: inline-block;
        position: relative;
        width: 165px;
        padding: 5px;
        border-color: #fff;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #54be8b;
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
    .tab-content > .has-padding {
        padding: 0px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
     <?php
        $form = ActiveForm::begin([
            'id' => 'pasien-rs-form',
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
            'options' => [
                'skip-confirm' => "true"
            ]
        ]);

        echo Html::hiddenInput('PesanAmbulanForm[jenis]', "rs", [
            'class' => 'jenis'
        ]);

        echo Html::hiddenInput('PesanAmbulanForm[pendaftaran_id]', null, [
            'class' => 'pendaftaran_id'
        ]);

        echo Html::hiddenInput('PesanAmbulanForm[no_pesanambulan]', null, [
            'class' => 'no_pesanambulan_rs'
        ]);
    ?>
    <legend><b><li>Informasi Pasien</li></b></legend>
    <div class="row">
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'pasien_id', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList([],[
                    'class' => '',
                    'id' => 'pasien_id',
                ])->label(Yii::t('fe', 'No Rekam Medik')); ?>
            </div>
            <div class="col-md-4">
                <?php $model->tgl_lahir = '<b>-</b>' ?>
                <?= $form->field($model, 'tgl_lahir', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->staticInput([
                    'class' => 'tgl_lahir',
                ])->label(Yii::t('fe', 'Tanggal Lahir')); ?>
            </div>
            <div class="col-md-4">
                <?php $model->kelaspelayanan_nama = '<b>-</b>' ?>
                    <?= $form->field($model, 'kelaspelayanan_nama', [
                    'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5 text-bold',
                            'wrapper' => 'col-md-7'
                        ]
                    ])->staticInput([
                        'class' => 'kelaspelayanan_nama',
                    ])->label(Yii::t('fe', 'Kelas Pelayanan')); ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php $model->pemesan = '<b>-</b>' ?>
                <?= $form->field($model, 'pemesan', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->staticInput([
                    'class' => 'pemesan',
                ])->label(Yii::t('fe', 'Nama Pasien')); ?>
            </div>
            <div class="col-md-4">
                <?php $model->tempat_lahir = '<b>-</b>' ?>
                    <?= $form->field($model, 'tempat_lahir', [
                    'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5 text-bold',
                            'wrapper' => 'col-md-6'
                        ]
                    ])->staticInput([
                        'class' => 'tempat_lahir',
                    ])->label(Yii::t('fe', 'Tempat Lahir')); ?>
            </div>
            <div class="col-md-4">
                <?php $model->carabayar_nama = '<b>-</b>' ?>
                <?= $form->field($model, 'carabayar_nama', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->staticInput([
                    'class' => 'carabayar_nama',
                ])->label(Yii::t('fe', 'Cara bayar / Penjamin')); ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php $model->jenis_kelamin = '<b>-</b>' ?>
                <?= $form->field($model, 'jenis_kelamin', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->staticInput(['class' => 'jenis_kelamin'])->label(Yii::t('fe', 'Jenis Kelamin')); ?>
            </div>
            <div class="col-md-4">
                <?php $model->instalasi_asal = '<b>-</b>' ?>
                <?= $form->field($model, 'instalasi_asal', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->staticInput([
                    'class' => 'instalasi_asal',
                ])->label(Yii::t('fe', 'Instalasi Asal')); ?>
            </div>
            <div class="col-md-4">
                <?php $model->diagnosa_pasien = '<b>-</b>' ?>
                <?= $form->field($model, 'diagnosa_pasien', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->staticInput([
                    'class' => 'diagnosa_pasien',
                ])->label(Yii::t('fe', 'Diagnosa Pasien')); ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'tujuan_pasien', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea([
                    'class' => 'form-control input-sm',
                    'rows' => 3
                ])->label(Yii::t('fe', 'Tujuan')); ?>
            </div>
            <div class="col-md-4">
                <?php $model->ruangan_asal = '<b>-</b>' ?>
                <?= $form->field($model, 'ruangan_asal', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->staticInput([
                    'class' => 'ruangan_asal',
                ])->label(Yii::t('fe', 'Ruangan Asal')); ?>
            </div>
            <div class="col-md-4">

            </div>
        </div>

        </div>
        <legend><b><li>Informasi Ambulan</li></b></legend>
        <div class="row">
            <div class="row">
                <div class="col-md-6">
                    <?=
                        $form->field($model, 'tgl_pesanambulan_rs', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4 text-bold',
                                'wrapper' => 'col-md-6'
                            ],
                        ])
                            ->textInput([
                                'class' => 'form-control input-sm'
                            ])
                            ->widget(DateTimePicker::classname(), [
                                'pluginOptions' => [
                                    'startDate' => date('Y-m-d H:i:s'),
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd HH:ii'
                                ],
                                'options' => [
                                    'class' => 'order-date-form-rs'
                                ]
                            ]);
                    ?>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-left control-label col-sm-4 text-bold">Tanggal Pemesanan</label>
                        <div class="col-md-3">
                            <?php
                                echo Html::button('<b><i class="fa fa-search"></i></b>' . Yii::t('fe','Cari Ketersediaan Ambulan'),[
                                    'class' => 'btn btn-success btn-labeled btn-xs btn-cek-rs',
                                    'disabled' => true,
                                    'id' => 'search-ambulance-btn-rs',
                                    'action' => Url::home().'ambulan/permintaan-ambulan/list-pemesanan?tipe=rs',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal_backdrop',
                                    'data-width' => '75%',
                                ]);
                                echo Html::hiddenInput('PesanAmbulanForm[ambulan_id]', null, [
                                    'class' => 'ambulan_id_rs'
                                ]);
                                echo Html::hiddenInput('PesanAmbulanForm[tgl_pesanambulan]', null, [
                                    'class' => 'tgl_pesanambulan_rs'
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="text-left control-label col-sm-4 text-bold">Jenis Ambulan</label>
                    <div class="col-md-8">
                        <p style="margin-top:9px"><b class="is_emergency_rs">-</b></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-left control-label col-sm-4 text-bold">No. Polisi</label>
                    <div class="col-md-8">
                        <div class="background-plat">
                            <span class="plat-nomor-rs">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <legend><b><li>Kondisi Pasien</li></b></legend>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'kesadaran', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm',
                ])->label(Yii::t('fe', 'Kesadaran Pasien')); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'tanda_vital', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm',
                ])->label(Yii::t('fe', 'Tanda Vital Pasien')); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'respirasi', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number',
                    'maxlength' => 4
                ])->label(Yii::t('fe', 'Respirasi Pasien')); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'td_diastolic', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number',
                    'maxlength' => 4
                ])->label(Yii::t('fe', 'Tekanan Darah Pasien')); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'detaknadi', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number',
                    'maxlength' => 4
                ])->label(Yii::t('fe', 'Nadi')); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'saturasi', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number',
                    'maxlength' => 4
                ])->label(Yii::t('fe', 'Saturasi O2')); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
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
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <legend><li>Tindakan Pelayanan</li></legend>
            <?php
                $form = ActiveForm::begin([
                    'id' => 'add-tindakan-rs',
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
                    <?= $form->field($modelTindakan, 'daftartindakan_id', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4 text-bold',
                            'wrapper' => 'col-md-8'
                        ]
                    ])->dropDownList([],[
                        'class' => '',
                        'id' => 'daftartindakan_id_rs',
                    ])->label(Yii::t('fe', 'Biaya Tambahan')); ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($modelTindakan, 'qty', [
                    'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4 text-bold',
                            'wrapper' => 'col-md-8'
                        ]
                    ])->textInput([
                        'class' => 'form-control input-sm doco-number qty_tindakan',
                    ])->label(Yii::t('fe', 'Qty')); ?>
                </div>
                <div class="col-md-4">
                    <div class="col-md-3">
                            <?= Html::submitButton(
                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'),
                                [
                                    'class' => 'btn btn-success btn-labeled btn-xs btn-tambah-tindakan-rs',
                                ])
                            ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="tabel-temp-tindakan-rs" class="table table-striped table-condensed table-hover" style="width:100%">
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
                                <td class="text-right"><b><span class="estimasi_biaya_rs"></span></b></td>
                                <td class="text-right"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
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
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_rs" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php $this->registerJs($this->render("../assets/js/pasien_rs.js"), View::POS_END) ?>