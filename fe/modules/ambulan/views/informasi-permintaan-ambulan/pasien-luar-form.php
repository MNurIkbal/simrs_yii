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
                                        'id' => 'pasien-luar-form',
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
                                        'class' => 'ambulan_id'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[jenis]', "luar", [
                                        'class' => 'jenis'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[tgl_pesanambulan]', $model->tgl_pesanambulan, [
                                        'class' => 'tgl_pesanambulan'
                                    ]);

                                    echo Html::hiddenInput('PesanAmbulanForm[no_pesanambulan]', $model->no_pesanambulan, [
                                        'class' => 'no_pesanambulan'
                                    ]);
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-left control-label col-sm-4 text-bold">Tanggal Pemesanan</label>
                                            <div class="col-md-8">
                                                <?php
                                                    echo Html::button('<b><i class="fa fa-search"></i></b>' . Yii::t('fe','Cari Ketersediaan Ambulan'),[
                                                        'class' => 'btn btn-success btn-labeled btn-xs',
                                                        'action' => Url::home().'ambulan/informasi-permintaan-ambulan/list-pemesanan',
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
                                <legend><b><li>Informasi Pasien</li></b></legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $model->pemesan = !empty($response['header']['nama_pemesan']) ? $response['header']['nama_pemesan'] : null ?>
                                        <?= $form->field($model, 'pemesan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm',
                                        ])->label(Yii::t('fe', 'Nama Pasien / Pemesan')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'jenis_kelamin', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->radioList([
                                            15 => 'Laki-Laki',
                                            16 => 'Perempuan'
                                        ], ['inline' => true])->label(Yii::t('fe', 'Jenis Kelamin')); ?>
                                        <div id="error_PesanAmbulanFormjenis_kelamin"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $model->tgl_lahir = !empty($model->tgl_lahir) ? date('d-M-Y',strtotime($model->tgl_lahir)) : date('d-M-Y'); ?>
                                        <?= $form->field($model, 'tgl_lahir', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->widget(DatePicker::classname(), [
                                            'name' => 'date_12',
                                            'value' => date('Y-m-d'),
                                            'readonly' => true,
                                            'language' => 'en',
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                'endDate' => "0d",
                                            ]
                                        ])->label(Yii::t('fe', 'Tanggal Lahir')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-left control-label col-sm-4 text-bold">Umur</label>
                                            <div class="col-md-8">
                                                <span class="umur"> </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'tempat_lahir', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm',
                                        ])->label(Yii::t('fe', 'Tempat Lahir')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'asal_pasien', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textarea([
                                            'class' => 'form-control input-sm',
                                            'rows' => 3
                                        ])->label(Yii::t('fe', 'Asal / Tempat Pasien')); ?>
                                    </div>
                                </div>
                                <legend><li>Kondisi Pasien</li></legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $model->is_sadar = ($model->is_sadar === false ? 0 : $model->is_sadar) ?>
                                        <?= $form->field($model, 'is_sadar', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->radioList([
                                            1 => 'Sadar',
                                            0 => 'Tidak Sadar'
                                        ], [
                                            'inline' => true,
                                            'class' => 'styled'
                                        ])
                                        ->label(Yii::t('fe', 'Kesadaran')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $model->is_nafas = ($model->is_nafas === false ? 0 : $model->is_nafas) ?>
                                        <?= $form->field($model, 'is_nafas', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->radioList([1 => 'Ada', 0 => 'Tidak Ada'], ['inline' => true])
                                        ->label(Yii::t('fe', 'Nafas')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $model->is_nadi = ($model->is_nadi === false ? 0 : $model->is_nadi) ?>
                                        <?= $form->field($model, 'is_nadi', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->radioList([1 => 'Ada', 0 => 'Tidak Ada'], ['inline' => true])
                                        ->label(Yii::t('fe', 'Nadi')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'keluhan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textArea([
                                            'class' => 'form-control input-sm',
                                            'rows' => 3
                                        ])->label(Yii::t('fe', 'Keluhan Pasien')); ?>
                                    </div>
                                </div>
                                <legend><li>Penanggung Jawab</li></legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'nama_pj', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm',
                                        ])->label(Yii::t('fe', 'Nama Penanggung Jawab Pasien')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kontak_pj', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                'wrapper' => 'col-md-5'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control input-sm',
                                        ])->label(Yii::t('fe', 'Telp / Hp Penanggung Jawab')); ?>
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
                    var _ambulan_id = $(".ambulan_id").val();
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
    });

    $("#simpan").on("click", function(event){
        event.preventDefault();

        $(this).docoForm("click", {
            url : "/ambulan/informasi-permintaan-ambulan/save-pasien",
            method : "POST",
            type : "json",
            data: $("#pasien-luar-form").serializeArray(),
            success : function (data) {
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
