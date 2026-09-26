<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-05 10:56:37
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-13 14:17:57
 * @Last Modified by:   Muhamad Lukman Hakim
 * @Last Modified time: 2020-09-15 10:38:00
 */


use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;


$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => []];
$this->params['breadcrumbs'][] = $this->title;
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php
                $checkRoute = $this->context->checkAksesMenu();
                echo DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form','id' => 'btn-reset-penjualan',]],
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Export CSV'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'apotek/laporan-penjualan-resep/show-popup-excel?',
                            'data-width' => '75%',
                            'data-visible' => $checkRoute
                        ]
                    ],
                    // 'excel' => [
                    //     'title' => Yii::t('fe', 'Export CSV'),
                    //     'attributes'=>[
                    //         'data-target'=>Url::home() . 'apotek/laporan-penjualan-resep/export-excel?'
                    //     ]
                    // ],
                   
                ]);
            
                ?>
                
            </div>
            <div class="panel-body">
                <div class="advanced-filter">

                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Penjualan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Jenis Penjualan");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Resep");?></th>
                            <th><?=\Yii::t("fe", "Nama Dokter");?></th>
                            <th><?=\Yii::t("fe", "Nomor Rekam Medis");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                            <th><?=\Yii::t("fe", "Nomor HP");?></th>
                            <th><?=\Yii::t("fe", "Alamat Pembeli");?></th>
                            <th><?=\Yii::t("fe", "Rke");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Obat");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Total tagihan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Formularium");?></th>
                            <th><?=\Yii::t("fe", "Psikotropika");?></th>
                            <th><?=\Yii::t("fe", "Narkotika");?></th>
                            <th><?=\Yii::t("fe", "Supplier");?></th>
                            <th><?=\Yii::t("fe", "Principle");?></th>
                            <th><?=\Yii::t("fe", "User");?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
    $this->registerJs('
    var table;
    var PENJUALAN_RESEP_BEBAS = '.DocoConstants::JUAL_BEBAS.';
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function(){
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"apotek/laporan-penjualan-resep/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Tanggal Penjualan')).'",
                    data: "tgltransaksi"
                },
                {
                    title: "'.(\Yii::t('fe', 'Nomor Pendaftaran')).'",
                    data: "no_pendaftaran",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Jenis Penjualan')).'",
                    data: "jenis_resep",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Ruangan')).'",
                    data: "ruangan_nama",
                    searchable: true,
                    orderable: false
                },                
                {
                    title: "'.(\Yii::t('fe', 'Nomor Resep')).'",
                    data: "noresep"
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Dokter')).'",
                    data: "nama_dokter",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nomor Rekam Medis')).'",
                    data: "no_rekammedik",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Pasien')).'",
                    data: "nama_pasien",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Tanggal Lahir')).'",
                    data: "tanggal_lahir"
                },
                {
                    title: "'.(\Yii::t('fe', 'Nomor Hp')).'",
                    data: "no_telepon_pembeli",
                    searchable: false,
                    render: function (data, type, row, meta) {

                        let noTelepon = typeof row.no_telepon_pembeli != "undefined" && row.no_telepon_pembeli != null ? row.no_telepon_pembeli : " - ";
                        let noMobile = typeof row.no_mobile_pembeli != "undefined" && row.no_mobile_pembeli != null ? row.no_mobile_pembeli : " - ";

                        if (row.jenispenjualan == PENJUALAN_RESEP_BEBAS) { // if resep bebas
                            return "Penjualan Resep Bebas";
                        }  else {
                            return noTelepon+"/"+noMobile;
                        }
                    }
                },
                {
                    title: "'.(\Yii::t('fe', 'Alamat Pembeli')).'",
                    data: "alamat_pembeli",
                    searchable: false,
                    render: function (data, type, row, meta) {
                        if (row.jenispenjualan == PENJUALAN_RESEP_BEBAS) { // if resep bebas
                            return "Penjualan Resep Bebas";
                        } else { // else resep rs, karyawan, bmhp
                            return typeof row.alamat_pembeli != "undefined" && row.alamat_pembeli != null ? "<span style=\"white-space:normal\">" + row.alamat_pembeli + "</span>" : " ";
                        }
                    }

                },
                {
                    title: "'.(\Yii::t('fe', 'Rke')).'",
                    data: "rke",
                    searchable: false,
                    orderable: false
                },                
                {
                    title: "'.(\Yii::t('fe', 'Kode Obat')).'",
                    data: "kode_obat",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Obat')).'",
                    data: "nama_obat",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Jenis Obat')).'",
                    data: "jenisobatalkes_nama",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Jumlah Obat')).'",
                    data: "jumlah_obat",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Satuan')).'",
                    data: "satuan",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Total tagihan (Rp)')).'",
                    data: "totaltagihan",
                    className : "text-right",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Status')).'",
                    data: "status_reseptur_nama",
                    searchable: false,
                    orderable: false
                },                
                {
                    title: "'.(\Yii::t('fe', 'Cara Bayar')).'",
                    data: "carabayar_nama",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Penjamin')).'",
                    data: "penjamin_nama",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Formularium')).'",
                    data: "formularium",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Psikotropika')).'",
                    data: "is_psycothropica",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Narkotika')).'",
                    data: "is_narcotic",
                    searchable: true,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Supplier')).'",
                    data: "supplier",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Principle')).'",
                    data: "principle",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'User')).'",
                    data: "user",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Jenis Penjualan')).'",
                    data: "jenispenjualan",
                    searchable: false,
                    visible: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Status')).'",
                    data: "status_reseptur",
                    visible: false
                }
            ],
        });
        $(".dataTables_filter").hide();

        // sesuaikan nomor index filter dengan urutan deklarasi kolom pada tabel
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'. date('d-M-Y') .'" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y') .'" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
            ],
            [
                4,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('ruangan_nama', '',
                        ArrayHelper::map($ruangan, 'ruangan_nama', 'ruangan_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--'),
                            'id'=>'filter_ruangan'
                        ]
                    )
                )).'\'
            ],
            [
                6,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('nama_dokter', '',
                        ArrayHelper::map($dokter, 'nama_pegawai', 'nama_pegawai'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--'),
                            'id'=>'filter_dokter'
                        ]
                    )
                )).'\'
            ],
            [
                20,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('carabayar_nama', '',
                        [],
                        [
                            'class' => 'form-control select2',
                            'prompt' => '-',
                            'id'=>'filter_carabayar'
                        ]
                    )
                )).'\'
            ],
            [
                3,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('jenis_resep', '',
                        $jenis_penjualan,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--'),
                            'id'=>'filter_resep'
                        ]
                    )
                )).'\'
            ],
            [
                21,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('penjamin_nama', '',
                        [],
                        [
                            'class' => 'form-control select2',
                            'prompt' => '-',
                            'id' => 'filter_penjamin',
                        ]
                    )
                )).'\'
            ],
            [
                29,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_reseptur', '',
                        $statusPenjualan,
                        [
                            'class' => 'form-control select2',
                            'prompt' => '-',
                            'id' => 'filter_status_reseptur',
                        ]
                    )
                )).'\'
            ],
            [
                23,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('is_psycothropica', '',
                        [
                            "true" => "Ya",
                            "false" => "Tidak"
                        ],
                        [
                            'id' => 'is_psycothropica',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],                 
            [
                25,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('is_narcotic', '',
                        [
                            "true" => "Ya",
                            "false" => "Tidak"
                        ],
                        [
                            'id' => 'is_narcotic',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
            [9,filterTanggalLahir]      
        ], {
            1:0,
            14:1,
            20:2,
            21:3,
            23:4,
        }, true);

        dateRangeHelper(".startDate", ".endDate", ".targetDate");

        var pickadate_tanggal_lahir = $("#pickadate_tanggal_lahir").pickadate({
            editable: true,
            format: "dd-mm-yyyy",
            formatSubmit: "dd-mm-yyyy",
            selectMonths: true,
            selectYears: true,
            min: [1900, 01, 01],
            max: true,
            onClose: function() {
                $(".datepicker").focus();
            }
        });

        var tanggal_baru = pickadate_tanggal_lahir.pickadate("picker");

        $("#btn-reset-penjualan").on("click", function(){
            $("#penjamin_nama").val(null).trigger("change");
            // $("#penjamin_nama").prop("disabled", true);
        })
   

        $(document).on("click", "#pickadate_tanggal_lahir", function (event) {
            if (tanggal_baru.get("open")) {
                tanggal_baru.close();
            } else {
                tanggal_baru.open();
            }
            event.stopPropagation();
        });

        $("#filter_carabayar").select2InfinityScroll({
            url: "/master/cara-bayar/get-data-select2",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })

        $("#filter_carabayar").on("change.select2", function() {
            $("#filter_penjamin").val(null).trigger("change"); // reset filter penjamin
        });

        $("#filter_penjamin").select2InfinityScroll({
            url: "/master/penjamin/get-data-select2",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        carabayar_id: $("#filter_carabayar").val()
                    }
                }
            }
        })

    });
    var filterTanggalLahir = \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
        Html::textInput('tanggal_lahir', '', [
            'class' => 'form-control',
            'id' => 'pickadate_tanggal_lahir',
            'data-mask' => '99-99-9999'
        ])
    )).'</div>\'


    ', VIEW::POS_END, "js-kunings");
?>
