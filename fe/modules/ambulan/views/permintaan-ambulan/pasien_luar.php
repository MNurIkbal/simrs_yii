<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:33:35
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-30 11:41:31
 */
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
?>
<style>
    .datepicker>div{
        display:block;
    }
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
            'id' => 'pasien-luar-form',
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

        echo Html::hiddenInput('PesanAmbulanForm[ambulan_id]', null, [
            'class' => 'ambulan_id'
        ]);

        echo Html::hiddenInput('PesanAmbulanForm[jenis]', "luar", [
            'class' => 'jenis'
        ]);

        echo Html::hiddenInput('PesanAmbulanForm[no_pesanambulan]', null, [
            'class' => 'no_pesanambulan'
        ]);
    ?>
    <div class="row">
        <div class="row">
            <div class="col-md-6">
                <?=
                    $form->field($model, 'tgl_pesanambulan', [
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
                                'class' => 'order-date-form'
                            ]
                        ]);
                ?>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="text-left control-label col-sm-4 text-bold">Pilih Ambulan</label>
                    <div class="col-md-3">
                        <?php
                            echo Html::button('<b><i class="fa fa-search"></i></b>' . Yii::t('fe','Cari Ketersediaan Ambulan'),[
                                'class' => 'btn btn-success btn-labeled btn-xs',
                                'disabled' => true,
                                'id' => 'search-ambulance-btn',
                                'action' => Url::home().'ambulan/permintaan-ambulan/list-pemesanan',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '75%',
                            ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="text-left control-label col-sm-4 text-bold">Jenis Ambulan</label>
                    <div class="col-md-8">
                        <p style="margin-top:9px"><b class="is_emergency">-</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="text-left control-label col-sm-4 text-bold">No. Polisi</label>
                    <div class="col-md-8">
                        <div class="background-plat">
                            <span class="plat-nomor">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <legend><b><li>Informasi Pasien</li></b></legend>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'pemesan', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm',
                ])->label(Yii::t('fe', 'Nama Pasien / Pemesan')); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'jenis_kelamin', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-6'
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
                <?php $model->tgl_lahir = date('Y-m-d'); ?>
                <?=
                    $form->field($model, 'tgl_lahir', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4 text-bold',
                            'wrapper' => 'col-md-6'
                        ],
                    ])
                        ->textInput([
                            'class' => 'form-control input-sm'
                        ])
                        ->widget(DatePicker::classname(), [
                            'pluginOptions' => [
                                'endDate' => date('Y-m-d'),
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ],
                        ]);
                ?>
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
                        'wrapper' => 'col-md-6'
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
                <?= $form->field($model, 'is_sadar', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3 text-bold',
                        'wrapper' => 'col-md-5'
                    ]
                ])->radioList([1 => 'Sadar', 0 => 'Tidak Sadar'], ['inline' => true])
                ->label(Yii::t('fe', 'Kesadaran')); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'is_nafas', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3 text-bold',
                        'wrapper' => 'col-md-5'
                    ]
                ])->radioList([1 => 'Ada', 0 => 'Tidak Ada'], ['inline' => true])
                ->label(Yii::t('fe', 'Nafas')); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'is_nadi', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3 text-bold',
                        'wrapper' => 'col-md-5'
                    ]
                ])->radioList([1 => 'Ada', 0 => 'Tidak Ada'],
                            ['inline' => true])
                ->label(Yii::t('fe', 'Nadi')); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'keluhan', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3 text-bold',
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
                        'label' => 'text-left control-label col-sm-3 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm',
                ])->label(Yii::t('fe', 'Nama Penanggung Jawab Pasien')); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'kontak_pj', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm',
                ])->label(Yii::t('fe', 'Telp / Hp Penanggung Jawab')); ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="row">
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
                <div class="col-md-6">
                    <?= $form->field($modelTindakan, 'daftartindakan_id', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-3 text-bold',
                                        'wrapper' => 'col-md-6'
                                    ]
                                    ])->dropDownList([],[
                                        'class' => '',
                                        'id' => 'daftartindakan_id',
                                    ])->label(Yii::t('fe', 'Biaya Tambahan')); ?>
                </div>
                <div class="col-md-6">
                    <div class="col-sm-8">
                        <?= $form->field($modelTindakan, 'qty', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-5 text-bold',
                                'wrapper' => 'col-md-7',
                            ]
                        ])->textInput([
                            'class' => 'form-control input-sm doco-number text-right',
                        ])->label(Yii::t('fe', 'Qty')); ?>
                    </div>
                    <div class="col-sm-4">
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


<script type="text/javascript">
    var tableTindakan;
    var umur;
    var ambulanId;
    var tgl_lahir = $("#pesanambulanform-tgl_lahir").val();
    umur = getUmur(convertTanggalYmd(tgl_lahir), new Date());
    $('.umur').text(umur);

    $(document).on("change", "#pesanambulanform-tgl_lahir", function(){
        var tgl_lahir = $(this).val();
        if(tgl_lahir != ''){
            umur = getUmur(convertTanggalYmd(tgl_lahir), new Date());
        }
        $('.umur').text(umur);
    });

    $(document).ready(function($) {
        $(".input-group-addon.kv-date-remove").remove();
        $('#pesanambulanform-tgl_lahir').trigger("change");
        $("input[type=radio]").uniform({
            radioClass: 'choice'
        });
        $("#daftartindakan_id").select2({
            placeholder: "Pilih",
            minimumInputLength: 3,
            ajax : {
                url: "/ambulan/permintaan-ambulan/search-tindakan",
                dataType: 'json',
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
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            },
        }).on('select2:select', function(e){
            var data = e.params.data;
        });

        $(document).on("keyup", ".qty", function(e) {
            var _qtyObat = parseInt($(this).val());
            var _curr = parseInt($(this).attr('data-val'));
            if (_qtyObat > 0) {
                var dataPost = {
                    daftartindakan_id: $(this).attr("data-id"),
                    qty: $(this).val()
                };
                $.ajax({
                    method: 'POST',
                    data: dataPost,
                    url: '/ambulan/permintaan-ambulan/update-cache',
                    success: function(data) {
                        tableTindakan.draw();
                    }
                });
                return true;
            }
            docoNotification("error","Proses Gagal !", "Qty tidak boleh 0.");
            $(this).val(_curr);
        });

        tableTindakan = $("#tabel-temp").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[2, "asc"]],
            ajax: baseUrl+"ambulan/permintaan-ambulan/get-list-tindakan-luar",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Tindakan',
                    data: "daftartindakan_nama",
                    orderable: false
                },
                {
                    title: "Kelompok Biaya",
                    data: "is_default",
                    orderable: false
                },
                {
                    title: "Qty",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "Tarif Satuan (Rp.)",
                    data: "harga_tariftindakan",
                    class:"text-right",
                    orderable: false
                },
                {
                    title: "Jumlah Tarif (Rp.)",
                    data: "jumlah_tarif",
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
                    estimasi_biaya += parseInt(val.jumlah_tarif2);
                });
                $(".estimasi_biaya").text(docoHelper.convertToRupiah(estimasi_biaya));
            },
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var _harga = parseInt(aData.harga);
                if (_harga == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                    $(nRow).find('input').prop("disabled",true);
                }
            }
        });
    });
</script>