<?php

/**
 * @author Yaya
 * @copyright 23 March 2018 
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;
use kartik\widgets\TimePicker;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => []];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $title)];

?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan'
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'ajax-form', 
                        'action' => '/bankdarah/pemesanan-darah-ruangan/set-list-item',
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

                    echo Html::hiddenInput('PesanDarahForm[total_harga]', null, [
                        'class' => 'total_harga'
                    ]);

                    echo Html::hiddenInput('PesanDarahForm[pendaftaran_id]', null, [
                        'class' => 'pendaftaran_id'
                    ]);

                    echo Html::hiddenInput('PesanDarahForm[pasienadmisi_id]', null, [
                        'class' => 'pasienadmisi_id'
                    ]);

                    echo Html::hiddenInput('PesanDarahForm[golongandarah_id]', null, [
                        'class' => 'golongandarah_id'
                    ]);

                    echo Html::hiddenInput('PesanDarahForm[total_kantongdarah]', null, [
                        'class' => 'total_kantongdarah'
                    ]);
                ?>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'ruanganpemesan_id', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 text-bold',
                                        'wrapper' => 'col-md-5'
                                    ]
                                ])->staticInput(['class' => 'ruanganpemesan_id'])
                                ->label(Yii::t('fe', 'Nama Ruangan')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'dokter_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4 text-bold',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->dropDownList(ArrayHelper::map($dataRequest['dokter'], 'pegawai_id', 'nama_pegawai'),[
                                    'class' => 'form-control select2',
                                    'id' => 'dokter_id',
                                    'prompt' => Yii::t('fe', 'Dokter')
                                ]); ?>
                            </div>
                        </div><br><hr>
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'pasien_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4 text-bold',
                                    'wrapper' => 'col-md-6'
                                ]
                                ])->dropDownList([],[
                                    'class' => '',
                                    'id' => 'pasien_id',
                                    'prompt' => Yii::t('fe', 'Nama Pasien')
                                ]); ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'diagnosa_sementara', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 text-bold',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->staticInput(['class' => 'diagnosa_sementara'])
                                ->label(Yii::t('fe', 'Diagnosa')); ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'umur', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 text-bold',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->staticInput(['class' => 'umur'])
                                ->label(Yii::t('fe', 'Umur')); ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'kadar_hb', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 text-bold',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->staticInput(['class' => 'kadar_hb'])
                                ->label(Yii::t('fe', 'Kadar HB')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'jenis_kelamin', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 text-bold',
                                        'wrapper' => 'col-md-5'
                                    ]
                                ])->staticInput(['class' => 'jenis_kelamin'])
                                ->label(Yii::t('fe', 'Jenis Kelamin')); ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'golongandarah_nama', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 text-bold',
                                        'wrapper' => 'col-md-5'
                                    ]
                                ])->staticInput(['class' => 'golongandarah_nama'])
                                ->label(Yii::t('fe', 'Golongan Darah')); ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'indikasi_transfusi', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 text-bold',
                                        'wrapper' => 'col-md-6'
                                    ]
                                ])->textInput(['class' => 'indikasi_transfusi'])
                                ->label(Yii::t('fe', 'Indikasi')); ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'metode_pengambilan', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4 text-bold',
                                    'wrapper' => 'col-md-6'
                                ]
                                ])->dropDownList(ArrayHelper::map($dataRequest['metode_pengambilan'], 'lookup_id', 'lookup_name'),[
                                    'class' => 'form-control select2',
                                    'id' => 'metode_pengambilan',
                                    'prompt' => Yii::t('fe', 'Pilih Metode')
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br><hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b>Data Pemesanan Darah Ruangan</b></h6>
                            </div>
                            <div class="panel-body">
                                <?= $form->field($modelDetail, 'jenisdarah_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->dropDownList(ArrayHelper::map($dataRequest['jenis_darah'], 'jenisdarah_id', 'jenisdarah_nama'),[
                                    'class' => 'form-control select2',
                                    'id' => 'jenisdarah_id',
                                    'prompt' => Yii::t('fe', 'Jenis Darah')
                                ])->label(Yii::t('fe', 'Jenis Darah')); ?>

                                <div class="form-group div-tgl_mintakirim">
                                    <label class="text-left control-label col-sm-4">
                                    <?= Yii::t('fe', 'Tanggal di Kirim') ?> 
                                    <span class="text-danger">*</span></label>
                                    <?php $modelDetail->tgl_mintakirim = date('d-M-Y'); ?>
                                    <?= $form->field($modelDetail, 'tgl_mintakirim', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]])->widget(DatePicker::classname(), [
                                        'name' => 'date_12',
                                        'value' => date('Y-m-d'),
                                        'readonly' => true,
                                        'type' => DatePicker::TYPE_INPUT,
                                        'language' => 'en',
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy',
                                            'startDate' => "0d",
                                        ]
                                    ])->label(false); ?>
                                </div>
                                <?= $form->field($modelDetail, 'wkt_mintakirim', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-4'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control input-sm wkt_mintakirim',
                                        'data-mask' => '99:99'
                                    ])->label(Yii::t('fe', 'Waktu di Kirim')); 
                                ?>

                                <?= $form->field($modelDetail, 'jumlah', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-4'
                                    ]
                                ])->textInput([
                                    'placeholder' => Yii::t('fe', 'Jumlah'),
                                    'class' => 'form-control input-sm typeahead doco-number text-right',
                                    'maxlength' => 4,
                                    'id' => "jumlah",
                                ]); ?>

                                <div class="btn-group pull-right">
                                    <?= Html::submitButton(
                                        '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                            [
                                                'class' => 'btn btn-success btn-labeled btn-xs btn-tambah',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b>Tabel Pemesanan Darah Ruangan</b></h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <table id="pemesanan-darah-ruangan" 
                                    class="table table-striped table-condensed table-hover" style="width:100%">
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th width="1">No</th>
                                                <th><?=\Yii::t("fe", "Jenis Darah");?></th>
                                                <th><?=\Yii::t("fe", "Tanggal di Kirim");?></th>
                                                <th><?=\Yii::t("fe", "Jumlah");?></th>
                                                <th><?=\Yii::t("fe", "Harga");?></th>
                                                <th><?=\Yii::t("fe", "Sub Total");?></th>
                                                <th><?=\Yii::t("fe", "Aksi");?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="9">
                                                    <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <footer>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align: right;width: 20%"><strong> Total Kantong: </strong></td>
                                                <td style="text-align: right;"><strong><span class="subTotalKantong"></span></strong></td>
                                                <td style="text-align: right;width: 20%"><strong> Total Harga: </strong></td>
                                                <td style="text-align: right;"><strong><span class="subTotal"></span></strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </footer>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'riwayat_transfusi', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-10'
                            ]
                        ])->textarea([
                            'placeholder' => Yii::t('fe', 'Riwayat Transfusi'),
                            'rows' => 5,
                            'id' => "riwayat_transfusi",
                        ]); ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'riwayat_kehamilan', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-10'
                            ]
                        ])->textarea([
                            'placeholder' => Yii::t('fe', 'Riwayat Bayi Pada Kehamilan'),
                            'rows' => 5,
                            'id' => "riwayat_kehamilan",
                        ]); ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'keterangan', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-10'
                            ]
                        ])->textarea([
                            'placeholder' => Yii::t('fe', 'Keterangan Lain'),
                            'rows' => 5,
                            'id' => "keterangan",
                        ]); ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var table;
    var attributes = {};
    $("#pasien_id").prop("disabled", true);
    $("#ajax-form").submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        if (Object.keys(attributes).length) {
            $.each(attributes, function (key, val) {
                _value.push({
                    name : key,
                    value : val
                });
            });
        } 
        $(this).docoForm("submit",{
            data : _value,
            success : function (data) {
                $("#jenisdarah_id").val("").trigger("change");
                $("#jumlah").val(1).trigger("change");
                $("#pesandarahpmidetailform-wkt_mintakirim").val("").trigger("change");
                $(".wkt_mintakirim").val("").trigger("change");
                table.draw();
            }
        });
    });
    
    $(document).on(\'click\',\'.delete\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            skipConfirm: true,
            success : function (data) {
                table.draw();
            }
        });
    });
    
    $(document).ready(function() {
        $("#dokter_id").bind("change", function(){
            var dokter_id = $(this).val();
            $("#pasien_id").prop("disabled", false);
            $("#pasien_id").val("");
            $(".diagnosa_sementara").text("");
            $(".umur").text("");
            $(".kadar_hb").text("-");
            $(".jenis_kelamin").text("");
            $(".golongandarah_nama").text("");
            $("#pasien_id").select2({
                placeholder: "Pilih Nama Pasien",
                minimumInputLength: 3, 
                ajax : {
                    url: "/bankdarah/pemesanan-darah-ruangan/search-pasien?dokter_id=" + dokter_id,
                    dataType: "json",
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
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function (m) { return m; },
                },
            }).on("select2:select", function(e){
                var data = e.params.data;
                var nama_pasien = data.nama_pasien;
                var no_rekam_medik = data.no_rekam_medik;
                var jenis_kelamin = data.jenis_kelamin;
                var umur = data.umur;
                var diagnosa = data.diagnosa;
                var umur = data.umur;
                var kadar_hb = data.kadar_hb;
                var pasienadmisi_id = data.pasienadmisi_id;
                var pendaftaran_id = data.pendaftaran_id;
                var golongandarah_id = data.golongandarah_id;
                var golongandarah_nama = data.golongandarah_nama;
                
                $(".no_rekam_medik").text(no_rekam_medik);
                $(".jenis_kelamin").text(jenis_kelamin);
                $(".umur").text(umur);
                $(".diagnosa_sementara").text(diagnosa);
                $(".kadar_hb").text(kadar_hb);
                $(".pendaftaran_id").val(pendaftaran_id);
                $(".pasienadmisi_id").val(pasienadmisi_id);
                $(".golongandarah_id").val(golongandarah_id);
                $(".golongandarah_nama").text(golongandarah_nama);
            });
        });

        $(".pickadate").pickadate({
            format: "dd-mm-yyyy",
            formatSubmit: "yyyy-mm-dd",
            onStart: function() {
                var date = new Date();
                this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });
        
        $("#pesandarahdetailform-wkt_mintakirim").on("change", function(){
            var wkt_mintakirim = $(this).val();
            var result = wkt_mintakirim.split(":");
            var jam = result[0];
            var menit = result[1];

            if(jam > 23 || menit > 59) {
                docoNotification("error", "Error", "Format Waktu Tidak Sesuai.");
                $("#pesandarahdetailform-wkt_mintakirim").val("");
                $(".btn-tambah").prop("disabled", true);
            }
            else {
                $(".btn-tambah").prop("disabled", false);
            }
        });

        table = $("#pemesanan-darah-ruangan").docoTabel({
            filter: false,
            displayLength: 20,
            lengthChange : false,
            processing: true,
            serverSide: true,
            paging: false,
            info: false,
            scrollY: "195px",
            ajax: baseUrl+"bankdarah/pemesanan-darah-ruangan/get-list-item",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width: "1",
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Darah")).'", 
                    data: "jenisdarah_nama",
                    searchable: false,
                    orderable: false,
                },
               {
                    title: "'.(\Yii::t("fe", "Tanggal di Kirim")).'", 
                    data: "tgl_mintakirim",
                    searchable: false,
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Jumlah")).'",
                    data: "jumlah",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Harga (Rp.)")).'",
                    data: "harga",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Sub Total (Rp.)")).'",
                    data: "subTotal",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var total_harga = 0;
                var total_kantongdarah = 0;
                $.each(dataRows, function (key, val) {
                    total_harga += parseInt(val.subTotal2);
                    total_kantongdarah += parseInt(val.jumlah);
                });
                
                $(".subTotal").text(docoHelper.convertToRupiah(total_harga));
                $(".total_harga").val(docoHelper.convertToAngka(total_harga));
                $(".total_kantongdarah").val(docoHelper.convertToAngka(total_kantongdarah));
                $(".subTotalKantong").text(docoHelper.convertToAngka(total_kantongdarah));
            }
        });
    });
    
    $("#simpan").on("click",function (event) {
        event.preventDefault();
        $(this).docoForm("click",{
            url : "/bankdarah/pemesanan-darah-ruangan/save",
            method : "POST",
            type : "json",
            data : {
                pasien_id : $("#pasien_id").val(),
                pendaftaran_id : $(".pendaftaran_id").val(),
                pasienadmisi_id : $(".pasienadmisi_id").val(),
                dokter_id : $("#dokter_id").val(),
                total_harga : $(".total_harga").val(),
                total_kantongdarah : $(".total_kantongdarah").val(),
                indikasi_transfusi : $("#pesandarahform-indikasi_transfusi").val(),
                metode_pengambilan : $("#metode_pengambilan").val(),
                riwayat_transfusi : $("#riwayat_transfusi").val(),
                riwayat_kehamilan : $("#riwayat_kehamilan").val(),
                keterangan : $("#keterangan").val(),
                golongandarah_id : $(".golongandarah_id").val(),
            },
            success : function (data) {
                console.log(data);
                var no_pesandarah = data.response.no_pesandarah;
                var urlCetak = "/bankdarah/pemesanan-darah-ruangan/cetak?no_pesandarah="+no_pesandarah;
                $("#pasien_id").val("").trigger("change");
                $("#dokter_id").val("").trigger("change");
                $("#metode_pengambilan").val("").trigger("change");
                $("#pesandarahform-indikasi_transfusi").val("").trigger("change");
                $("#riwayat_transfusi").val("").trigger("change");
                $("#riwayat_kehamilan").val("").trigger("change");
                $("#keterangan").val("").trigger("change");
                $(".wkt_mintakirim").val("").trigger("change");
                
                $(".no_rekam_medik").text("");
                $(".diagnosa_sementara").text("");
                $(".umur").text("");
                $(".jenis_kelamin").text("");
                $(".kadar_hb").text("");
                $(".golongandarah_nama").text("");

                table.draw();
                (new PNotify({
                    title: "Berhasil",
                    text: "Data Berhasil di Simpan dengan Nomor Pemesanan Darah Ruangan " + "<strong>" + no_pesandarah + "</strong>" + " , Apakah Anda Ingin Mencetak Bukti Pemesanan Darah Ruangan?",
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
                    window.open(urlCetak);
                }).on("pnotify.cancel", function() {

                });
            }
        });
    });
    
',View::POS_END,'pemesanan-darah-ruangan');