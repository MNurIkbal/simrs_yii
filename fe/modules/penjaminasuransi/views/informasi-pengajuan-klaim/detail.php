<?php
    /**
     * @author Chacha Nurholis
     * A product of PT Citra Raya Nusatama
     * Powered by Sirs
     */

    use yii\web\View;
    use yii\helpers\Html;
    use app\components\DHtml; 
    use yii\widgets\Breadcrumbs;
    use kartik\widgets\ActiveForm;
    use kartik\widgets\DatePicker;
    use app\components\DocoHelpers;
    use app\widgets\kasir\DHBtnCetakDetailInvoice;

    $this->title = DHtml::getTitleMenu();
    $this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
    $this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['/']];
    $this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .datepicker>div{
        display: block;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'back',
                        'delete' => [
                            'title'      => 'Batal',
                            'icon'       => 'fa fa-close',
                            'attributes' => [
                                'data-confirm-message' => 'Apakah anda ingin membatalkan pengajuan pasien ini ?',
                                'data-target'          => '/penjamin-asuransi/informasi-pengajuan-klaim/batal-pengajuan-pasien?id=',
                                'disabled'             => true,
                                'id'                   => 'cancel-button'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width'  => '90%',
                                'action'      => '/penjamin-asuransi/informasi-pengajuan-klaim/tambah-pasien?id='.$id,
                            ]
                        ],
                        'tagihan' => DHBtnCetakDetailInvoice::widget(),
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/penjamin-asuransi/informasi-pengajuan-klaim/export-pengajuan-pdf?id='.$id.'&'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/penjamin-asuransi/informasi-pengajuan-klaim/export-pengajuan-excel?id='.$id.'&'
                            ]
                        ]
                    ], '#example')
                ?>
                <?= 
                    Html::button("<b><i class='fa fa-floppy-o'></i></b>".Yii::t('fe', 'Simpan '), [
                        'class'       => 'btn btn-info btn-labeled btn-xs',
                        'id'          => 'simpan-pengajuan',
                        'data-parent' => $id
                    ]) 
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Informasi Pengajuan Klaim</b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Tanggal Keluar") ?></b>
                                            </label>
                                            <div class="col-sm-7">
                                                <p>
                                                    <b>:</b>
                                                    &nbsp;
                                                    <?php
                                                        $tanggalMulai = isset($header['tgl_pelayanandari']) ? date('d-M-Y',strtotime($header['tgl_pelayanandari'])) . ' s/d ' : '';
                                                        $tanggalSelesai = isset($header['tgl_pelayanansampai']) ? date('d-M-Y',strtotime($header['tgl_pelayanansampai'])) : '';
                                                    ?>
                                                    <?= "{$tanggalMulai}{$tanggalSelesai}" ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Tanggal Pengajuan") ?></b>
                                            </label>
                                            <div class="col-sm-5">
                                                <p>
                                                    <b>:</b>
                                                    &nbsp;
                                                    <?= isset($header['tgl_pengajuanklaim']) ? date('d-M-Y', strtotime($header['tgl_pengajuanklaim'])) : '-' ?> 
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                    <?= Yii::t("fe", "No Pengajuan") ?></b>
                                            </label>
                                            <div class="col-sm-5">
                                                <p>
                                                    <b>:</b>
                                                    &nbsp;
                                                    <?= isset($header['no_pengajuanklaim']) ? $header['no_pengajuanklaim'] : '-' ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Cara Bayar") ?></b>
                                            </label>
                                            <div class="col-sm-5">
                                                <p>
                                                    <b>:</b>
                                                    &nbsp;
                                                    <?= isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '-' ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Penjamin") ?></b>
                                            </label>
                                            <div class="col-sm-5">
                                                <p>
                                                    <b>:</b>
                                                    &nbsp;
                                                    <?= isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '-' ?> 
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Instalasi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p>
                                                    <b>:</b>
                                                    &nbsp;
                                                    <?= isset($header['instalasi_nama']) ? $header['instalasi_nama'] : '-' ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Ruangan") ?></b></label>
                                            <div class="col-sm-5">
                                                <p>
                                                    <b>:</b>
                                                    &nbsp;
                                                    <?= isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '-' ?> 
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel -->
                    <div class="panel panel-default">
                        <!-- Panel Heading -->
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Input Pengajuan Klaim</b></h6>
                        </div>
                        <!-- Panel Body -->
                        <div class="panel-body">
                            <!-- Form Begin -->
                            <?php 
                                $form = ActiveForm::begin([
                                    'id'                    => 'pengajuan-form', 
                                    'action'                => '/penjamin-asuransi/informasi-pengajuan-klaim/update?id=' . $id,
                                    'enableAjaxValidation'  => false, 
                                    'enableClientValidation'=> true,
                                    'type'                  => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig'            => [
                                        'labelSpan'  => 3, 
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [
                                        'skip-confirm' => "true"
                                    ]
                                ]); 
                            ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="text-left control-label col-sm-5"><b>
                                                    <?= Yii::t("fe", "Tanggal Jatuh Tempo") ?></b>
                                                </label>
                                                <div class="col-sm-7">
                                                    <?= 
                                                        $form->field($model, 'tgl_jatuhtempo', [
                                                            'template' => '{input}',
                                                            'options'  => [
                                                                'tag' => false
                                                            ]
                                                        ])->widget(DatePicker::classname(), [
                                                            'value'         => date('Y-m-d'),
                                                            'readonly'      => true,
                                                            'language'      => 'en',
                                                            'pluginOptions' => [
                                                                'startDate'  => '0d',
                                                                // 'endDate' => '0d',
                                                                'autoclose'  => true,
                                                                'format'     => 'dd-M-yyyy',
                                                            ]
                                                        ])->label(false) 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="text-left control-label col-sm-5"><b>
                                                    <?= Yii::t("fe", "Total Pengajuan") ?></b>
                                                </label>
                                                <div class="col-sm-7">
                                                    <?= 
                                                        $form->field($model, 'total_pengajuan', [
                                                            'addon' => ['prepend' => ['content' => 'Rp.']],
                                                            'template' => '{input}',
                                                            'options'  => ['tag' => false]
                                                        ])->textInput([
                                                            'placeholder'  => $model->getAttributeLabel('total_pengajuan'),
                                                            'class'        => 'form-control input-sm text-right',
                                                            'autocomplete' => "off",
                                                            'readonly'     => true
                                                        ])->label(false)
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <?= 
                                                    $form->field($model, 'catatan', [
                                                    ])->textArea([
                                                        'rows' => 3,
                                                    ])
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- Form End -->
                            <?php ActiveForm::end() ?>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Data Pasien") ?></th>
                            <th><?= Yii::t("fe", "No Pendaftaran") ?></th>
                            <th><?= Yii::t("fe", "No Rekam Medik") ?></th>
                            <th><?= Yii::t("fe", "No Invoice") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Masuk") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Keluar") ?></th>
                            <th><?= Yii::t("fe", "No SEP") ?></th>
                            <th><?= Yii::t("fe", "Nama Pasien") ?></th>
                            <th><?= Yii::t("fe", "Instalasi / Ruangan") ?></th>
                            <th><?= Yii::t("fe", "Instalasi") ?></th>
                            <th><?= Yii::t("fe", "Ruangan") ?></th>
                            <th><?= Yii::t("fe", "Tagihan") ?></th>
                            <th><?= Yii::t("fe", "Jumlah Dibayarkan Pasien") ?></th>
                            <th><?= Yii::t("fe", "Jumlah Diskon") ?></th>
                            <th><?= Yii::t("fe", "Jumlah Pengajuan") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="14"><?= Yii::t("fe", "Data tidak ditemukan") ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
    $total_pengajuan = !empty($model->total_pengajuan) ? $model->total_pengajuan : 0;
    $this->registerJs('
        var table;
        var _totalPengajuan = '. $total_pengajuan .';
        var _valuePengurang = 0;

        $(document).ready(function() {
            $(".doco-number").trigger("change");
            table = $("#example").docoTabel({
                filter: false,
                columnDefs: [ {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                }],
                select: {
                    style:    "os",
                    selector: "tr"
                },
                sorting: [[4, "desc"]], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                stateSave: true,
                scrollX: true,
                ajax: baseUrl+"penjamin-asuransi/informasi-pengajuan-klaim/get-data-pengajuan?id='. $id .'",
                columns: [
                    {
                        title: "", 
                        data: null, 
                        defaultContent: "", 
                        searchable: false, 
                        orderable: false,
                        width: "10%"
                    },
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Data Pasien")).'", 
                        data: "nama_pasien",
                        searchable: false,
                        orderable: false,
                        render: (data, type, row, meta) => {
                            let namaPasien = row.nama_pasien
                            let noRm = row.no_rekam_medik
                            let noPend = row.no_pendaftaran

                            return `<b>` + namaPasien + `</b>` + `<br>` + noRm + `<br>` + noPend
                        }
                    },
                    {
                        title: "'.(\Yii::t("fe", "No Pendaftaran")).'", 
                        data: "no_pendaftaran",
                        visible: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "No Rekam Medik")).'", 
                        data: "no_rekam_medik",
                        visible: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "No Invoice")).'", 
                        data: "no_pembayaran"
                    },
                    {
                        title: "'.(\Yii::t("fe", "Tanggal Masuk")).'", 
                        data: "tgl_pendaftaran"
                    },
                    {
                        title: "'.(\Yii::t("fe", "Tanggal Keluar")).'", 
                        data: "tglpasienpulang"
                    },
                    {
                        title: "'.(\Yii::t("fe", "No SEP")).'", 
                        data: "nosep",
                        render: (data, type, row, meta) => {
                            return row.nosep ? row.nosep : `-`
                        }
                    },
                    {
                        title: "'.(\Yii::t("fe", "Nama Pasien")).'", 
                        data: "nama_pasien",
                        visible: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Instalasi / Ruangan")).'", 
                        data: "instalasi_nama",
                        orderable: false,
                        render: (data, type, row, meta) => {
                            let instalasiNama = row.instalasi_nama
                            let ruanganNama = row.ruangan_nama
                            return `<b>` + instalasiNama + `</b>` + `<br>` + ruanganNama 
                        }
                    },
                    {
                        title: "'.(\Yii::t("fe", "Instalasi")).'", 
                        data: "instalasi_nama",
                        visible: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Ruangan")).'", 
                        data: "ruangan_nama",
                        visible: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Tagihan")).'", 
                        data: "total_tagihan_label",
                        orderable: false,
                    },
                    {
                        title: "'.(\Yii::t("fe", "Jumlah Dibayarkan Pasien")).'", 
                        orderable: false,
                        data: "jumlah_telahbayar_label",
                    },
                    {
                        title: "'.(\Yii::t("fe", "Jumlah Diskon")).'", 
                        orderable: false,
                        data: "total_discountpembayaran"
                    },
                    {
                        title: "'.(\Yii::t("fe", "Jumlah Pengajuan")).'", 
                        orderable: false,
                        data: "jumlah_sisapiutang_label"
                    },
                ],
                drawCallback : function (settings) {
                    _totalPengajuan -= parseInt(_valuePengurang);
                    $(".doco-number").val(_totalPengajuan).trigger("change");
                    _valuePengurang = 0;
                },
                scrollCollapse: true,
            });
        });

        $(document).on("click", "#example tr", function(event){
            event.preventDefault();
            var tbl = $(this).hasClass("selected");
            var result = table.row(this).data();
            _valuePengurang = 0;
            if (tbl) {
                _valuePengurang = parseInt(result.jumlah_piutang)
            }
        });

        $(document).on("click", "#simpan-pengajuan", function (event) {
            event.preventDefault();
            $().docoForm("click",{
                url : $("#pengajuan-form").attr("action"),
                data : $("#pengajuan-form").serializeArray(),
                success : function (data) {
                    window.location.href = `/penjamin-asuransi/informasi-pengajuan-klaim`;
                }
            });
        });

        $(document).on("click", "#example tr", function(event){
            event.preventDefault();
            var tableData = table.row(".selected").data();
            if (tableData) {
                $("#cetak-detail-invoice").attr(`disabled`, false);
                $("#cetak-detail-invoice").attr(`data-pembayaran-id`, tableData.pembayaran_id);
                $("#cetak-detail-invoice").attr(`data-pendaftaran-id`, tableData.pendaftaran_id);
            } else {
                $("#cetak-detail-invoice").attr(`disabled`, true);
                $("#cetak-detail-invoice").attr(`data-pembayaran-id`, ``);
                $("#cetak-detail-invoice").attr(`data-pendaftaran-id`, ``);
            }
        });

        $(document).on("click", "#example tbody tr", function (event) {
            event.preventDefault();
            try {
                noInvoice = table.row(".selected").data().no_pembayaran ? table.row(".selected").data().no_pembayaran : null;
            } catch (e) {
                noInvoice = false;
            }
    
            if (!noInvoice) {
                $("#cancel-button").prop("disabled", true);
            } else {
                $("#cancel-button").prop("disabled", false);
            }
        });

    ', View::POS_END, 'b-index');
?>
