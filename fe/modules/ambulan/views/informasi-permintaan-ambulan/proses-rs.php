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
    .daterangepicker.dropdown-menu.ltr.show-calendar.opensright {
        top: 657.573px !important;
    }
    .form-group {
        margin-bottom: 0px !important;
        margin-top: -5px !important;
    }
    .panel-default > .panel-heading {
        color: #606060;
        background-color: #fcfcfc;
        border-color: #ddd;
        margin-bottom: 10px;
    }
    .col-md-7.petugas-p, .col-md-7.obatalkes_id-p {
        margin-bottom: 10px;
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
                                'id' => 'simpan',
                                'disabled' => $statusPesan
                            ]
                        ],
                        'back',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form',
                                'id'=>'reset-form',
                                'disabled' => $statusPesan
                            ]
                        ],
                    ],'#example');?>
                </div>
                <div class="panel-body">
                    <div class="col-md-12 info-pengajuan form-vertical">
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
                                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 4, 
                                            'deviceSize' => ActiveForm::SIZE_SMALL
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

                                    echo Html::hiddenInput('PesanAmbulanForm[kelaspelayanan_id]', $model->kelaspelayanan_id, [
                                        'id' => 'kelaspelayanan_id'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[penjamin_id]', $model->penjamin_id, [
                                        'id' => 'penjamin_id'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[carabayar_id]', $model->carabayar_id, [
                                        'id' => 'carabayar_id'
                                    ]);



                                    echo Html::hiddenInput('FormProses[pendaftaran_id]', $model->pendaftaran_id, [
                                        'class' => 'pendaftaran_id'
                                    ]);

                                    echo Html::hiddenInput('FormProses[pasien_id]', $model->pasien_id, [
                                        'class' => 'pasien_id'
                                    ]);
                                ?>
                                <div class="row w-page">
                                    <div class="col-md-3">
                                        <label for="noRekamMedik" class="control-label">No Rekam Medik</label>
                                        <p class="label-value-form"><?= $model->no_rekam_medik ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">No Pendaftaran</label>
                                        <p class="label-value-form"><?= isset($pasien['no_pendaftaran']) && !empty($pasien['no_pendaftaran']) ? $pasien['no_pendaftaran'] : '-' ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Tanggal Lahir</label>
                                        <p class="label-value-form"><?= DocoHelpers::convertDate($model->tgl_lahir) ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Instalasi Asal</label>
                                        <p class="label-value-form"><?= $model->instalasi_asal ?></p>
                                    </div>
                                </div>
                                <div class="row w-page">
                                    <div class="col-md-3">
                                        <label class="control-label">Kelas Pelayanan</label>
                                        <p class="label-value-form"><?= $model->kelaspelayanan_nama ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Nama Pemesan</label>
                                        <p class="label-value-form"><?= $model->nama_pemesan ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Tempat Lahir</label>
                                        <p class="label-value-form"><?= $model->instalasi_asal ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Ruangan Asal</label>
                                        <p class="label-value-form"><?= $model->ruangan_asal ?></p>
                                    </div>
                                </div>
                                <div class="row w-page">
                                    <div class="col-md-3">
                                        <label class="control-label">Cara Bayar / Penjamin</label>
                                        <p class="label-value-form"><?= $model->carabayar_nama ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Jenis Kelamin</label>
                                        <p class="label-value-form"><?= $header['jns_kelamin'] ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Tujuan</label>
                                        <p class="label-value-form"><?= $model->tujuan_pasien ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Diagnosa Pasien</label>
                                        <p class="label-value-form"><?= !empty($pasien['diagnosa']) ? $pasien['diagnosa'] : '-' ?></p>
                                    </div>
                                </div>
                                
                                <legend><b><li>Informasi Ambulan</li></b></legend>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tanggal Pemesanan</label>
                                            <p class="label-value-form"><?= DocoHelpers::convertDate($model->tgl_pesanambulan) ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">Jenis Ambulan</label>
                                        <p class="label-value-form"><?= !empty($model->jenis_ambulan) ? $model->jenis_ambulan : '-' ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">No Polisi</label>
                                        <p>
                                            <span class="plat-nomor"><?= !empty($model->no_polisi) ? $model->no_polisi : '-' ?></span>
                                        </p>
                                    </div>
                                </div>
                                
                                <legend><li>Pelayanan Ambulan</li></legend>
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <?= $form->field($modelProses, 'tgl_pemakaian', [
                                        'addon' => [
                                            'append' => ['content'=>'<i class="glyphicon glyphicon-calendar"></i>'],
                                        ],
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left col-sm-3 text-bold required',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control',
                                            'id' => 'tgl_pemakaian'
                                        ])->label(Yii::t('fe', 'Tanggal Pemakaian')); ?>
                                    </div>
                                    <div class="col-md-6 required">
                                        <?= $form->field($modelProses, 'pelayanan_ambulan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold required',
                                                'wrapper' => 'col-md-6'
                                            ]
                                        ])->dropDownList( $pelayananAmbulan ,[
                                            'class' => 'form-control select2',
                                            'id' => 'pelayanan_ambulan',
                                            'prompt' => '— Pilih —', 
                                        ])->label($modelProses->attributeLabels()['pelayanan_ambulan']); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <?= $form->field($modelProses, 'km_awal', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold required',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm doco-number text-right',
                                            // 'placeholder' => 'Km Awal', 
                                        ])->label(Yii::t('fe', 'Km Awal')); ?>
                                    </div>
                                    <div class="col-md-6 required">
                                        <?= $form->field($modelProses, 'estimasi_jarak', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold required',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm doco-number text-right',
                                            // 'placeholder' => 'Km Awal', 
                                        ]); ?>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                                <legend><li>Petugas Ambulan</li></legend>
                                <?php 
                                    $form = ActiveForm::begin([
                                        'id' => 'petugas-ambulan',
                                        'enableAjaxValidation' => false, 
                                        'enableClientValidation'=> false, 
                                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 4, 
                                            'deviceSize' => ActiveForm::SIZE_SMALL
                                        ],
                                        'options' => [
                                            'skip-confirm' => "true"
                                        ]
                                    ]);
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $form->field($modelPegawai, 'pegawai_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7 petugas-p'
                                            ]
                                        ])->dropDownList([],[
                                            'class' => '',
                                            'id' => 'petugas-ambulan-id',
                                        ])->label(Yii::t('fe', 'Pegawai')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-3">
                                            <?= Html::submitButton(
                                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                                [
                                                    'class' => 'btn btn-success btn-labeled btn-xs simpan-petugas',
                                                ]) 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="tabel-petugas" class="table table-striped table-condensed table-hover" style="width:100%">
                                            <thead>
                                                <tr class="bg-inverse">
                                                    <th width="1">No</th>
                                                    <th><?=\Yii::t("fe", "Nama");?></th>
                                                    <th><?=\Yii::t("fe", "NIK");?></th>
                                                    <th><?=\Yii::t("fe", "Jabatan");?></th>
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

                                <legend><li>Obat Alkes</li></legend>
                                <?php 
                                    $form = ActiveForm::begin([
                                        'id' => 'add-obat-alkes',
                                        'enableAjaxValidation' => false, 
                                        'enableClientValidation'=> false, 
                                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 4, 
                                            'deviceSize' => ActiveForm::SIZE_SMALL
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
                                                'label' => 'text-left control-label col-sm-3 text-bold',
                                                'wrapper' => 'col-md-7 obatalkes_id-p'
                                            ]
                                        ])->dropDownList([],[
                                            'class' => '',
                                            'id' => 'obatalkes_id_rs',
                                        ])->label(Yii::t('fe', 'Obat Alkes')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-3">
                                            <?= $form->field($modelObat, 'qty', [
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4 text-bold',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput([
                                                'class' => 'form-control input-sm doco-number qty_obat_rs text-right',
                                            ])->label(Yii::t('fe', 'Qty')); ?>
                                        </div>
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
                                <div class="row">
                                        <div class="col-md-12">
                                            <div class='my-legend'>
                                                <div class='legend-title'>Keterangan</div>
                                                <div class='legend-scale'>
                                                <ul class='legend-labels'>
                                                    <li><span style='background:rgba(255, 188, 188, 0.58);'></span>&nbsp;&nbsp;<b>Obat tidak tersedia</b></li>
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
    var _tablePegawai;
    var ambulanId;
    var _idParent = "'.$id.'";
    var tgl_pesanambulan = $("#tgl_pesanambulan").html();
    var min_tgl_pesanambulan = new Date(tgl_pesanambulan);
    
    $(document).ready(function() {
        $("#tgl_pemakaian").daterangepicker({
            minDate:min_tgl_pesanambulan,
            timePicker: true,
            timePicker24Hour : true,
            timePickerIncrement : 1,
            locale : {
                format : "DD-MM-YYYY HH:mm",
                "separator": "  s/d  ",
                "applyLabel": "Simpan",
                "cancelLabel": "Kembali",
                "fromLabel": "Dari",
                "toLabel": "Sampai",
                "customRangeLabel": "Custom",
                "daysOfWeek": '.$namaHari.',
                "monthNames": '.$namaBulan.',
            }
          }, function(start, end, label) {
            console.log("tanggal : " + start.format("YYYY-MM-DD H:i") + " to " + end.format("YYYY-MM-DD H:i"));
          });

        _table = $("#tabel-petugas").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[2, "asc"]],
            ajax: baseUrl+"ambulan/informasi-permintaan-ambulan/get-data-pegawai?id='. $id .'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Nama", 
                    data: "nama_pegawai",
                    orderable: false
                },
                {
                    title: "NIK", 
                    data: "nomorindukpegawai",
                    orderable: false
                },
                {
                    title: "Jabatan", 
                    data: "jabatan_nama",
                    orderable: false
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ]
        });
        $("input[type=radio]").uniform({
            radioClass: \'choice\'
        });
        $("#pesanambulanform-tgl_lahir").trigger("change");
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

        $("#petugas-ambulan-id").select2({
            placeholder: "Pilih",
            minimumInputLength: 3, 
            ajax : {
                url: "/ambulan/informasi-permintaan-ambulan/search-pegawai",
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

        $("#date_pemakaiandari").pickadate({
            format: "dd mmm yyyy",
            formatSubmit: "yyyy-mm-dd",
            onStart: function() {
                var date = new Date();
                this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });

        $("#date_pemakaiansampai").pickadate({
            format: "dd mmm yyyy",
            formatSubmit: "yyyy-mm-dd",
            onStart: function() {
                var date = new Date();
                this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });

        $("#datetime").AnyTime_picker({
            format: "%d-%m-%Y %H:%i",
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

    $(document).on("click", "#reset-form", function(event) {
        event.preventDefault();
        $("#formproses-km_awal").val("");
        $("#pelayanan_ambulan").val(null).trigger("change");
        var x = '.$setPegawai.';
        _table.draw();
        location.reload();
    });

    $(document).on(\'click\',\'.delete-cache-pegawai\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            skipConfirm : true,
            success : function (data) {
               _table.draw();
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

    $(document).on("click", ".simpan-petugas", function (event) {
        event.preventDefault();
        var dataPost = $(\'#petugas-ambulan\').serializeArray();
        dataPost.push({
            name : "id",
            value : "'.$id.'"
        });
        $(this).docoForm("click", {
            url: "/ambulan/informasi-permintaan-ambulan/add-pegawai",
            method: "POST",
            type: "json",
            data: dataPost,
            skipConfirm: true,
            success: function (data) {
                $("#petugas-ambulan-id").val(\'\').trigger(\'change\');
                _table.draw();
            }
        });
    });

    $("#simpan").on("click", function(event){
        event.preventDefault();
        console.log("data");
        $(this).docoForm("click", {
            url : "/ambulan/informasi-permintaan-ambulan/save-pasien",
            method : "POST",
            type : "json",
            data: $("#pasien-rs-form").serializeArray(),
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apakah anda yakin akan memproses permintaan dengan nomor '.$no_pesanambulan.', permintaan yang telah diproses tidak dapat dibatalkan?"),
            success : function (data) {
                $(document).ready(function() {
                    $("#simpan").prop("disabled",true);
                });
                setTimeout(function(){
                    $("#simpan").prop("disabled", true);
                    $("#reset-form").prop("disabled", true);
                }, 100);
                (new PNotify({
                    title: "Berhasil",
                    text: "Nomor Permintaan " + "<strong>" + data.response.no_pesanambulan + "</strong>" + " telah selesai di proses. Apakah Anda akan melakukan cetak Surat Tugas?",
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
                    window.open("/ambulan/informasi-permintaan-ambulan/export-surat-tugas-pdf?pemakaianambulan_id="+data.response.pemakaianambulan_id);
                }).on("pnotify.cancel", function() {
                    window.open("/ambulan/informasi-permintaan-ambulan/#");
                });
                _table.draw();
            },
            error: function() {
                console.log("test error");
            },
        });
    });


    $(document).on("change", "#pesanambulanform-tgl_lahir", function(){
        var tgl_lahir = $(this).val();
        if(tgl_lahir != \'\'){
            umur = getUmur(convertTanggalYmd(tgl_lahir), new Date());
        }
        $(\'.umur\').text(umur);
    });

', View::POS_END, 'b-index');
