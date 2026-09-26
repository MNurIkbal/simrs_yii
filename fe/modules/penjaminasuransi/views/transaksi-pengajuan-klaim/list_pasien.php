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
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = DHtml::getTitleMenu();
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Transaksi', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;

$caraBayarId = ArrayHelper::getValue($cachePenjamin, 'carabayar_id');
$caraBayarNama = ArrayHelper::getValue($cachePenjamin, 'carabayar_nama', '-');
$penjaminId = ArrayHelper::getValue($cachePenjamin, 'penjamin_id');
$penjaminNama = ArrayHelper::getValue($cachePenjamin, 'penjamin_nama', '-');
$instalasi = ArrayHelper::getValue($response, 'instalasi');
$ruangan = ArrayHelper::getValue($response, 'ruangan');
?>
<style>
    .checker-inverse span {
        color: #fff;
        border: 2px solid #fff;
        margin: 3px 5px;
    }

    .dataTables_scroll {
        max-height: none;
        overflow: auto;
        position: relative;
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
                    'add' => [
                        'title' => \Yii::t('fe', 'Pengajuan'),
                        'icon' => 'fa fa-folder-open',
                        'attributes' => [
                            'id' => 'btn-pengajuan',
                            'data-target' => '/penjamin-asuransi/transaksi-pengajuan-klaim/add-detail-klaim',
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/transaksi-pengajuan-klaim/export-pdf?carabayar_id=' . $caraBayarId . '&penjamin_id=' . $penjaminId . '&'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/transaksi-pengajuan-klaim/export-excel?carabayar_id=' . $caraBayarId . '&penjamin_id=' . $penjaminId . '&'
                        ]
                    ],
                ], '#example') ?>
            </div>
            
            <div class="panel-body">
                <diiv class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <p><strong>Cara Bayar : <?= $caraBayarNama ?></strong></p>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Penjamin : <?= $penjaminNama ?></strong></p>
                        </div>
                        <div class="col-md-2">
                            <?=
                            Html::a('<b><i class="fa fa-pencil"></i></b>' . Yii::t('fe', 'Ubah'), [
                                '/penjamin-asuransi/transaksi-pengajuan-klaim',
                            ], [
                                'class' => 'btn btn-success btn-labeled btn-xs',
                            ])
                            ?>
                        </div>
                    </div>
                </diiv>
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="row">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => 'ajax-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => [
                                'labelSpan' => 3,
                                'deviceSize' => ActiveForm::SIZE_SMALL
                            ],
                            'options' => [
                                'skip-confirm' => 'true'
                            ]
                        ]);
                        echo Html::hiddenInput('total_tagihan', '', ['class' => 'total_tagihan']);
                        echo Html::hiddenInput('instalasi_value', '', ['class' => 'instalasi_value']);
                        echo Html::hiddenInput('ruangan_value', '', ['class' => 'ruangan_value']);
                        echo Html::hiddenInput('pendaftaran_id', '', ['class' => 'pendaftaran_id']);
                    ?>
                </div>
                
                <!-- Table -->
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1" class="select-checkbox">
                                <div class="checkbox">
                                    <label><input type="checkbox" id="check-all" value="1"></label>
                                </div>
                            </th>
                            <th>No</th>
                            <th>No. Pendaftaran</th>
                            <th>No Rekam Medik</th>
                            <th>No Invoice</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>No SEP</th>
                            <th>Nama Pasien</th>
                            <th>Instalasi</th>
                            <th>Ruangan</th>
                            <th>Tagihan</th>
                            <th>Jumlah Dibayarkan Pasien</th>
                            <th>Jumlah Discount</th>
                            <th>Jumlah Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13">Data tidak ditemukan</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>

<?php

$this->registerJsVar('carabayar_id', $caraBayarId);
$this->registerJsVar('penjamin_id', $penjaminId);
$this->registerJsVar('ruangan', json_encode($ruangan));
$listInstalasi = ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama');
$listRuangan = ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama');

$this->registerJs('
    var table;

    localStorage.clear();
    localStorage.setItem("ruangan", ruangan);

    var _cacheTagihan = {};
    var _cacheTanggalMasukStart = {};
    var _cacheTanggalMasukEnd = {};
    var _cacheInstalasiKlaim = {};
    var _cacheRuanganKlaim = {};

    if (localStorage.getItem("tagihan")) {
        var _cacheTagihan = JSON.parse(localStorage.getItem("tagihan"));
    }

    if (localStorage.getItem("pk_tanggal_masuk_start")) {
        var _cacheTanggalMasukStart = JSON.parse(localStorage.getItem("pk_tanggal_masuk_start"));
    }

    if (localStorage.getItem("pk_tanggal_masuk_end")) {
        var _cacheTanggalMasukEnd = JSON.parse(localStorage.getItem("pk_tanggal_masuk_end"));
    }

    if (localStorage.getItem("instalasi_klaim")) {
        var _cacheInstalasiKlaim = JSON.parse(localStorage.getItem("instalasi_klaim"));
    }

    if (localStorage.getItem("ruangan_klaim")) {
        var _cacheRuanganKlaim = JSON.parse(localStorage.getItem("ruangan_klaim"));
    }

    $(document).on("click", ".data-reset", function() {
        table.draw();
    });

    function renderInfoTextSelected()
    {
        const totalCountSelected = Object.keys(_cacheTagihan).length;
        const textInfo = totalCountSelected + ` rows selected`;
        $(`.dataTables_info .select-info .select-item`).first().html(textInfo);
        return true;
    }

    $(document).ready(function() {
        if (!carabayar_id) {
            window.location.href = "/penjamin-asuransi/transaksi-pengajuan-klaim";
        }

        var urlGetData = baseUrl + "penjamin-asuransi/transaksi-pengajuan-klaim/get-data-list-pasien?carabayar_id=" + carabayar_id + "&penjamin_id=" + penjamin_id;
        table = $("#example").docoTabel({
            filter: true,
            paging: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style: "multi",
                selector: "tr"
            },
            sorting: [],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl + "penjamin-asuransi/transaksi-pengajuan-klaim/get-data-list-pasien?carabayar_id=" + carabayar_id + "&penjamin_id=" + penjamin_id,
            columns: [
                {
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
                    title: "Data Pasien",
                    data: `nama_pasien`,
                    render: (data, type, row, meta) => {
                        let namaPasien = row.nama_pasien;
                        namaPasien = `<b>` + namaPasien + `</b>`;
                        let noPendaftaran = row.no_pendaftaran;
                        let noRekamMedik = row.no_rekam_medik;
                        let resultDataColumn = namaPasien + `<br/>` + noRekamMedik + `<br/>` + noPendaftaran
                        return resultDataColumn;
                    }
                },
                {
                    title: "No Rekam Medik",
                    data: "no_rekam_medik",
                    searchable: false,
                    orderable: false,
                    visible: false
                },
                {
                    title: "No Invoice",
                    data: "no_pembayaran"
                },
                {
                    title: "Tanggal Masuk",
                    data: "tgl_pendaftaran",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Tanggal Keluar",
                    data: "tglpasienpulang"
                },
                {
                    title: "No SEP",
                    data: "nosep",
                    render: (data, type, row, meta) => {
                        return row.nosep ? row.nosep : `-`
                    }
                },
                {
                    title: "Nama Pasien",
                    data: "nama_pasien",
                    visible: false
                },
                {
                    title: "Instalasi / Ruangan",
                    data: "instalasi_nama",
                    searchable: false,
                    render: (data, type, row, meta) => {
                        const namaInstalasi = row.instalasi_nama
                        const namaRuangan = row.ruangan_nama
                        let resultDataColumn = `<b>` + namaInstalasi + `</b>` + ` <br/> ` + namaRuangan
                        return resultDataColumn;
                    }
                },
                {
                    title: "Ruangan",
                    data: "ruangan_nama",
                    visible: false
                },
                {
                    title: "Tagihan",
                    data: "total_tagihan_label",
                    searchable: false
                },
                {
                    title: "Jumlah Dibayarkan Pasien",
                    data: "total_sdh_bayar_label",
                    searchable: false
                },
                {
                    title: "Jumlah Discount",
                    data: "total_discount_label",
                    searchable: false
                },
                {
                    title: "Jumlah Pengajuan",
                    data: "total_pengajuan_label",
                    searchable: false
                },
                {
                    title: "Instalasi",
                    data: "instalasi_nama",
                    searchable: true,
                    visible: false
                },
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page: "current"} ).data();
                var tr = $(this);
                $.each(dataRows, function (key, val) {
                    var _primary = val.primary;
                    if (typeof _cacheTagihan[_primary] != "undefined") {
                        table.row(":eq("+key+")").select();
                    }
                })
                setTimeout(()=>{
                    renderInfoTextSelected();
                }, 10)
            },
            scrollCollapse: true
        });

        $(".dataTables_filter").hide();
        
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                6,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=5 readonly="true"></div>\'
            ],
            [
                15,
                \'<div class=\"form-group\">' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_nama', '', $listInstalasi,
                        [
                            'id' => 'filter_instalasi',
                            'class' => 'form-control select2 dep-to-child',
                            'prompt' => '-- Pilih Instalasi --',
                            'data-url' =>  '/penjamin-asuransi/informasi-pasien-non-bpjs/get-ruangan',
                            'data-depend_id' => 'filter_ruangan',
                            'data-depend_prompt' => '-- Pilih Ruangan --',
                            'data-storage' => 'ruangan',
                            'data-key' => 'ruangan_id',
                        ]
                    )
                )) . '</div>\'
            ],
            [
                10,
                \'<div class="form-group">' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', $listRuangan,
                        [
                            'id' => 'filter_ruangan',
                            'class' => 'form-control select2 dep-to-parent',
                            'prompt' => '-- Pilih Ruangan --',
                            'data-url' =>  '/penjamin-asuransi/informasi-pasien-non-bpjs/get-instalasi',
                            'data-depend_id' => 'filter_instalasi',
                        ]
                    )
                )) . '</div>\'
            ],
        ], {
            6:0,
            15:1,
            10:2
        }, true);

        var _totalTagihan = function () {
            var _total = 0;

            $.each(_cacheTagihan, function (key, val) {
                _total += parseInt(val.total_pengajuan);
            });

            $(".total_tagihan").val(_total).trigger("change");
        }

        $(document).on("click", "#example tbody tr", function(event) {
            event.preventDefault();
            
            var tbl    = $(this).hasClass("selected");
            var result = table.row(this).data();
            
            if (tbl) {
                _cacheTagihan[result.primary] = result;
            } else {
                if(result) delete _cacheTagihan[result.primary];
            }

            localStorage.setItem("tagihan", JSON.stringify(_cacheTagihan));
            
            _totalTagihan();
            renderInfoTextSelected();
        });

        $("#filter_instalasi").on("change", function() {
            var instalasi_value = $("#filter_instalasi :selected").text();
            $(".instalasi_value").val(instalasi_value);
        });

        $("#filter_ruangan").on("change", function() {
            var ruangan_value = $("#filter_ruangan :selected").text();
            $(".ruangan_value").val(ruangan_value);
        });

        $("#btn-pengajuan").on("click", function(e) {
            var _tagihan = localStorage.getItem("tagihan") ? JSON.parse(localStorage.getItem("tagihan")) : 0;
            var tanggal_masuk_start = $("#rangeDemoStart").val();
            var tanggal_masuk_end = $("#rangeDemoFinish").val();
            var instalasi_value = $(".instalasi_value").val();
            var ruangan_value = $(".ruangan_value").val();

            if (Object.keys(_tagihan).length <= 0) {
                docoNotification("error", "Proses Gagal!", "Pengajuan tidak boleh kosong");
                return false;
            }

            _cacheTanggalMasukStart = tanggal_masuk_start;
            _cacheTanggalMasukEnd = tanggal_masuk_end;
            
            _cacheInstalasiKlaim = {
                key: $("#filter_instalasi").val(),
                value: $("#filter_instalasi").val() != "" ? $("#filter_instalasi").select2("data")[0].text : ""
            };

            _cacheRuanganKlaim = {
                key: $("#filter_ruangan").val(),
                value: $("#filter_ruangan").val() != "" ? $("#filter_ruangan").select2("data")[0].text : ""
            };;

            localStorage.setItem("pk_tanggal_masuk_start", JSON.stringify(_cacheTanggalMasukStart));
            localStorage.setItem("pk_tanggal_masuk_end", JSON.stringify(_cacheTanggalMasukEnd));
            localStorage.setItem("instalasi_klaim", JSON.stringify(_cacheInstalasiKlaim));
            localStorage.setItem("ruangan_klaim", JSON.stringify(_cacheRuanganKlaim));
        });

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDatePendaftaran",".endDatePendaftaran",".targetDatePendaftaran");

        $(document).on("click", "#check-all", function(){
            $(`.checker`).removeClass(`focus`);
            const isExist = table.data().count() > 0;
            // Ajax ke api (Tanpa paging)
            var dataParams = table.ajax.params().columns;
            let filteredAdvanceds = []
            filteredAdvanceds = filterByKey({
                lists:dataParams,
                key:`searchable`,
                value: true
            })
            let customParam = ``
            filteredAdvanceds.map((item)=>{
                const key = item.data
                const val = item.search.value
                if(val) customParam += `&` + key + `=` + val
            })
            if (($("#check-all").prop("checked") == true) && isExist) {
                table.rows().select();
                $().docoForm(`click`, {
                    url: urlGetData + `&is_paging=0`+customParam ,
                    type: `json`,
                    skipConfirm: true,
                    skipSuccessNotif: true,
                    success: function (response) {
                        response.data.map((item)=>{
                            _cacheTagihan[item.primary] = item;
                        })
                        localStorage.setItem("tagihan", JSON.stringify(_cacheTagihan));
                        _totalTagihan();
                        renderInfoTextSelected();
                    }
                })
            }else{
                localStorage.setItem("tagihan", "{}");
                _cacheTagihan = {};
                table.rows().deselect();
            }
            trigger_tr_click();
        });

        $("#check-all").uniform({
            radioClass: "choice",
            checkboxClass: "checker checker-inverse"
        });

        function trigger_tr_click(){
            $.each($("#example tbody tr"), function(){
                $(this).trigger("click");
            });
        }
    });

    $(`.data-reset`).on(`click`, () =>{ 
        $(`#check-all`).attr(`checked`, false);
        const parentCheckAll = $(`#check-all`).parents();
        parentCheckAll.removeClass(`checked`);
        localStorage.setItem("tagihan", "{}");
        _cacheTagihan = {};
    })

    $(`.data-filter`).on(`click`, () =>{ 
        $(`#check-all`).attr(`checked`, false);
        const parentCheckAll = $(`#check-all`).parents();
        parentCheckAll.removeClass(`checked`);
        localStorage.setItem("tagihan", "{}");
        _cacheTagihan = {};
    })
', View::POS_END, 'b-index');
?>