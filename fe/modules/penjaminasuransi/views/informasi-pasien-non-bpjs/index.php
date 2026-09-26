<?php
    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     * A Product of PT Citra Raya Nusatama
     * Powered by Sirs
     */

    use yii\web\View;
    use yii\helpers\Html;
    use app\components\DHtml;
    use yii\widgets\Breadcrumbs;
    use yii\helpers\ArrayHelper;
    use app\components\DocoHelpers;
    use app\widgets\kasir\DHBtnCetakDetailInvoice;

    $this->title = DHtml::getTitleMenu();
    $this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <!-- Panel Heading -->
            <div class="panel-heading">
                <!-- Breadcrumb -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <!-- Toolbar -->
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'koreksi' => [
                        'title' => \Yii::t('fe', 'Surat Ket. Dokter'),
                        'icon'  => 'fa fa-folder-open',
                        'attributes' => [
                            'id'              => 'btn-skd',
                            'data-options'    => 'click',
                            'data-target'     => '/penjamin-asuransi/informasi-pasien-non-bpjs/skd?id=',
                            'data-conditions' => 'admisi'
                        ]
                    ],
                    'tagihan' => DHBtnCetakDetailInvoice::widget(),
                    'pdf' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-pasien-non-bpjs/export-pdf?'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-pasien-non-bpjs/export-excel?'
                        ]
                    ],
                ], '#example') ?>
            </div>
            <!-- Panel Body -->
            <div class="panel-body">
                <!-- Advanced Filter -->
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <!-- Table -->
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th><?= \Yii::t("fe", "No") ?></th>
                            <th><?= \Yii::t("fe", "Data Pasien") ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Masuk") ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Keluar") ?></th>
                            <th><?= \Yii::t("fe", "No Pendaftaran") ?></th>
                            <th><?= \Yii::t("fe", "No Rekam Medik") ?></th>
                            <th><?= \Yii::t("fe", "No Invoice") ?></th>
                            <th><?= \Yii::t("fe", "Nama Pasien") ?></th>
                            <th><?= \Yii::t("fe", "Cara Bayar / Penjamin") ?></th>
                            <th><?= \Yii::t("fe", "Cara Bayar") ?></th>
                            <th><?= \Yii::t("fe", "Penjamin") ?></th>
                            <th><?= \Yii::t("fe", "Instalasi / Ruangan") ?></th>
                            <th><?= \Yii::t("fe", "Instalasi") ?></th>
                            <th><?= \Yii::t("fe", "Ruangan") ?></th>
                            <th><?= \Yii::t("fe", "Tagihan") ?></th>
                            <th><?= \Yii::t("fe", "Jumlah Dibayarkan Pasien") ?></th>
                            <th><?= \Yii::t("fe", "Jumlah Diskon") ?></th>
                            <th><?= \Yii::t("fe", "Jumlah Piutang") ?></th>
                            <th><?= \Yii::t("fe", "Sisa Tagihan") ?></th>
                            <th><?= \Yii::t("fe", "Jumlah Dibayarkan") ?></th>
                            <th><?= \Yii::t("fe", "Status Pengajuan") ?></th>
                            <th><?= \Yii::t("fe", "Status SKD") ?></th>
                            <th><?= \Yii::t("fe", "Status koreksi") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="23"><?= \Yii::t("fe", "Data tidak ditemukan") ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->registerJs('
        var table;
        localStorage.clear();
        localStorage.setItem("penjamin", \''.json_encode(ArrayHelper::map($response['penjamin'], 'penjamin_id', 'penjamin_nama')).'\');
        localStorage.setItem("ruangan", \''.json_encode(ArrayHelper::map($response['ruangan'], 'ruangan_id', 'ruangan_nama')).'\');

        $(document).on("click", ".data-reload", function() {
            table.draw();
        });

        $(document).ready(function() {
            table = $("#example").docoTabel({
                filter: true,
                columnDefs: [ {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                }],
                select: {
                    style:    "os",
                    selector: "tr"
                },
                displayLength: 10,
                processing: true,
                serverSide: true,
                stateSave: true,
                scrollX: true,
                ajax: baseUrl + "penjamin-asuransi/informasi-pasien-non-bpjs/get-data",
                columns: [
                    {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "No")).'", data: "rowNum", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Data Pasien")).'", data: "nama_pasien", searchable: false, orderable: false, render: function (data, type, row, meta) {
                        let namaPasien = row.nama_pasien
                        let noRm = row.no_rekam_medik
                        let noPendaftaran = row.no_pendaftaran
                        return `<b>` + namaPasien + `</b>` + `<br>` + noRm + `<br>` + noPendaftaran
                    }},
                    {title: "'.(\Yii::t("fe", "Tanggal Masuk")).'", data: "tgl_pendaftaran", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Tanggal Keluar")).'", data: "tglpasienpulang", searchable: true, orderable: true},
                    {title: "'.(\Yii::t("fe", "No Pendaftaran")).'",  data: "no_pendaftaran", visible: false},
                    {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", visible: false},
                    {title: "'.(\Yii::t("fe", "No Invoice")).'",  data: "no_invoice", searchable: false, orderable: false, render: (data, type, row, meta) => {
                        return row.no_invoice ? row.no_invoice : `-`
                    }},
                    {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien", searchable: true, orderable: true, visible: false},
                    {title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'", data: "carabayar_nama", searchable: false, orderable: false, render: (data, type, row, meta) => {
                        let caraBayarNama = row.carabayar_nama
                        let penjaminNama = row.penjamin_nama
                        return `<b>` + caraBayarNama + `</b>` + `<br>` + penjaminNama
                    }},
                    {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama", searchable: true, orderable: true, visible: false},
                    {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", searchable: true, orderable: true, visible: false},
                    {title: "'.(\Yii::t("fe", "Instalasi / Ruangan")).'", data: "instalasi_nama", searchable: false, orderable: false, render: (data, type, row, meta) => {
                        let instalasiNama = row.instalasi_nama
                        let ruanganNama = row.ruangan_nama
                        return `<b>` + instalasiNama + `</b>` + `<br>` + ruanganNama
                    }},
                    {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_nama", searchable: true, orderable: true, visible: false},
                    {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", searchable: true, orderable: true, visible: false},
                    {title: "'.(\Yii::t("fe", "Tagihan")).'", data: "total_tagihan", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Jumlah Dibayarkan Pasien")).'", data: "total_sdh_bayar", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Jumlah Piutang")).'", data: "total_asuransi", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Jumlah Pembayaran")).'", data: "jumlah_pembayaran", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Jumlah Diskon")).'", data: "total_discountpembayaran", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Sisa Tagihan")).'", data: "total_sisa_tagihan", searchable: false, orderable: false},
                    {title: "'.(\Yii::t("fe", "Status Pengajuan")).'", data: "statuspengajuan_nama", searchable: true, orderable: false, render: (data, type, row, meta) => {
                        return row.statuspengajuan_nama ? row.statuspengajuan_nama : `-` 
                    }},
                    {title: "'.(\Yii::t("fe", "Status SKD")).'", data: "status_skd", searchable: true, orderable: true},
                    {title: "'.(\Yii::t("fe", "Status koreksi")).'", data: "status_verif", name: "status_verifikasi", searchable: true, orderable: true},
                ],
                scrollCollapse: true
            });

            $(".dataTables_filter").hide();

            $(".filter-form").datatableBootstrapFilter(table, [
                [
                    4, \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" readonly="" /><input type="text" style="display:none" class="targetDate" col-index=5 readonly="true"></div>\'
                ],
                [
                    10, \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('carabayar_nama', '', 
                            ArrayHelper::map($response['cara_bayar'], 'carabayar_id', 'carabayar_nama'), 
                            [
                                'id'                 => 'filter_carabayar', 
                                'class'              => 'form-control select2 dep-to-child', 
                                'prompt'             => \Yii::t('fe', '-- Pilih Cara Bayar --'),
                                'data-url'           => '/penjamin-asuransi/informasi-pasien-non-bpjs/get-penjamin',
                                'data-depend_id'     => 'filter_penjamin',
                                'data-depend_prompt' => \Yii::t('fe', '-- Pilih Penjamin --'),
                                'data-storage'       => 'penjamin',
                                'data-key'           => 'penjamin_id',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    11, \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('penjamin_nama', '',
                            ArrayHelper::map($response['penjamin'], 'penjamin_id', 'penjamin_nama'),
                            [
                                'id'             => 'filter_penjamin',
                                'class'          => 'form-control select2',
                                'prompt'         => \Yii::t('fe', '-- Pilih Penjamin --'),
                                'data-url'       => '/penjamin-asuransi/informasi-pasien-non-bpjs/get-carabayar',
                                'data-depend_id' => 'filter_carabayar',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    13, \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('instalasi_nama', '', 
                            ArrayHelper::map($response['instalasi'], 'instalasi_id', 'instalasi_nama'), 
                            [
                                'id'                 => 'filter_instalasi', 
                                'class'              => 'form-control select2 dep-to-child', 
                                'prompt'             => \Yii::t('fe', '-- Pilih Instalasi --'),
                                'data-url'           => '/penjamin-asuransi/informasi-pasien-non-bpjs/get-ruangan',
                                'data-depend_id'     => 'filter_ruangan',
                                'data-depend_prompt' => \Yii::t('fe', '-- Pilih Ruangan --'),
                                'data-storage'       => 'ruangan',
                                'data-key'           => 'ruangan_id',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    14, \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_nama', '',
                            ArrayHelper::map($response['ruangan'], 'ruangan_id', 'ruangan_nama'),
                            [
                                'id'             => 'filter_ruangan',
                                'class'          => 'form-control select2',
                                'prompt'         => \Yii::t('fe', '-- Pilih Ruangan --'),
                                'data-url'       => '/penjamin-asuransi/informasi-pasien-non-bpjs/get-instalasi',
                                'data-depend_id' => 'filter_instalasi',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    20,
                    \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('s_pengajuanklaim', '', 
                        ArrayHelper::map($response['status_klaim'], 'lookup_id', 'lookup_name'),
                        [
                            'class'  => 'form-control select2',
                            'id'     => 's_pengajuanklaim',
                            'prompt' => Yii::t('fe', '-- Pilih Status Pengajuan --')
                        ]
                    ))).'\' 
                ],
                [
                    21, \'' . (preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('status_skd', '', 
                            [
                                0 => 'Belum Dibuat',
                                1 => 'Sudah Dibuat'
                            ],
                            [
                                'class'  => 'form-control select2',
                                'id'     => 'status_skd',
                                'prompt' => Yii::t('fe', '-- Pilih Status SKD --')
                            ]
                        )
                    )).'\' 
                ],
                [
                    22, \'' . (preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('status_verifikasi', '', 
                            ArrayHelper::map($response['status_verifikasi'], 'lookup_id', 'lookup_name'),
                            [
                                'class'  => 'form-control select2',
                                'prompt' => Yii::t('fe', '-- Pilih Status Koreksi --')
                            ]
                        )
                    )).'\'
                ],
            ], {
                4:0,
                10:1,
                11:2,
                13:3,
                14:4,
                20:5,
                21:6,
                22:7
            }, true);

            dateRangeHelper(".startDate",".endDate",".targetDate");
            dateRangeHelper(".startDatePendaftaran",".endDatePendaftaran",".targetDatePendaftaran");
            
            $("#filter_nama_pasien").attr("placeholder", "Cari Berdasarkan Nama Pasien")
            $("#filter_no_pendaftaran").attr("placeholder", "Cari Berdasarkan No Pendaftaran")
            $("#filter_no_rekam_medik").attr("placeholder", "Cari Berdasarkan No Rekam Medik")

            $(document).on("click", "#btn-skd", function() {
                var tableData = table.row(".selected").data();

                if (typeof tableData != "undefined") {
                    if (tableData.status_bayar == "349") {
                        docoNotification("error", "Transaksi Dibatalkan", "Tidak bisa membuat surat keterangan dokter karna status pembayaran belum lunas");
                    } else {
                        var conditions = $(this).attr("data-conditions") ? $(this).attr("data-conditions").split(",") : "";
                        var primary = tableData.primary;
                        var ext = "";

                        if (conditions.length > 0) {
                            $.each(conditions, function(index, value){
                                ext += "&"+value+"="+tableData[value];
                            });
                        }

                        var target = $(this).attr("data-target");
                        window.open(target+primary+ext, "_self");
                    }
                } else {
                    docoNotification("warning", "Peringatan", "Belum ada data yang dipilih!");
                }
            })
            $("#get-penjamin").select2Penjamin();
            $("#get-cara-bayar").select2CaraBayar();
        });

        function disabledBtnDetailInvoice() {
            $("#cetak-detail-invoice").attr(`disabled`, true);
            $("#cetak-detail-invoice").attr(`data-pembayaran-id`, ``);
            $("#cetak-detail-invoice").attr(`data-pendaftaran-id`, ``);
        }

        function enabledBtnDetailInvoice({
            pembayaran_id, pendaftaran_id
        }) {
            $("#cetak-detail-invoice").attr(`disabled`, false);
            $("#cetak-detail-invoice").attr(`data-pembayaran-id`, pembayaran_id);
            $("#cetak-detail-invoice").attr(`data-pendaftaran-id`, pendaftaran_id);
        }

        $(document).on("click", "#example tr", function(event){
            event.preventDefault();
            var tableData = table.row(".selected").data();
            if (tableData) {
                const isExistData = tableData.pembayaran_id && tableData.pendaftaran_id
                if (isExistData) {
                    enabledBtnDetailInvoice({
                        pembayaran_id: tableData.pembayaran_id,
                        pendaftaran_id: tableData.pendaftaran_id
                    })
                } else {
                    disabledBtnDetailInvoice()
                }
            } else {
                disabledBtnDetailInvoice()
            }
        });

    ', View::POS_END, 'b-index');
?>