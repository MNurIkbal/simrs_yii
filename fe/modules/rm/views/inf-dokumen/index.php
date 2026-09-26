<?php
// Author : Budi
// Date : 16 Januari 2018

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi'];
$this->params['breadcrumbs'][] = $this->title;

// $this->registerJs($this->render('assets/js/dokumen.js'));
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
                        <h3 class="panel-title"><b><?= Yii::t('fe', 'Dokumen Rekam Medik');?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <!-- Panel toolbar -->
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'simpan_dokumen' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Simpan Dokumen'),
                        'icon' => 'fa fa-save',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'btn-simpan',
                            'data-target' => Url::home().('rm/transaksi-penyimpanan/index?id='),
                            'disabled'=>'disabled'
                        ] 
                    ],
                    'reset'
                ], '#example');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nomor Rak");?></th>
                            <th><?=\Yii::t("fe", "Nomor Rak");?></th>
                            <th><?=\Yii::t("fe", "Lokasi Sub Rak");?></th>
                            <th><?=\Yii::t("fe", "Lokasi Sub Rak");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Warna Dokumen");?></th>
                            <th><?=\Yii::t("fe", "Instalasi posisi dokumen")?></th>
                            <th><?=\Yii::t("fe", "Posisi dokumen rekam medik");?></th>
                            <th><?=\Yii::t("fe", "Posisi dokumen rekam medik");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
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
$this->registerCss('
.daterangepicker{
    // top:187px !important;
}
');
$this->registerJs('
    // localStorage.clear();
    localStorage.setItem("ruangan", \''.json_encode($data_ruangan).'\');
    var ruangan_id = '.$ruangan_id.';
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
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
            sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/inf-dokumen/get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal rekam medik")).'", data: "tglrekammedis"},
                {title: "'.(\Yii::t("fe", "Nomor rak")).'", data: "lokasirak_nama", name: "lokasirak_id", visible: false, searchable: true},
                {title: "'.(\Yii::t("fe", "Nomor sub rak")).'", data: "subrak_nama", name: "subrak_id", visible: false, searchable: true},
                {title: "'.(\Yii::t("fe", "Lokasi rak")).'", data: "lokasirak_nama", name: "lokasirak_id", visible: true, searchable: false},
                {title: "'.(\Yii::t("fe", "Lokasi sub rak")).'", data: "subrak_nama", name: "subrak_id", visible: true, searchable: false},
                {title: "'.(\Yii::t("fe", "Nomor rekam medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "Nama pasien")).'",  data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "Warna dokumen")).'", data: "warnadokrm_namawarna", name: "warnadokrm_id"},
                {title: "'.(\Yii::t("fe", "Instalasi posisi dokumen")).'", data: "instalasi_nama", name: "instalasi_id", visible: false, searchable: true},
                {title: "'.(\Yii::t("fe", "Ruangan posisi dokumen")).'", data: "ruangan_akhir", name: "ruanganakhir_id", visible: false, searchable: true},
                {title: "'.(\Yii::t("fe", "Posisi dokumen rekam medik")).'", data: "ruangan_akhir", name: "ruanganakhir_id", visible: true, searchable: false},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ],
                [
                    7,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pasien_id', NULL, [], ['class' => 'form-control select2 norm', 'prompt' => '', "col-index" => "4"]))).'\'
                ],
                [
                    8,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_pasien', NULL, [], ['class' => 'form-control select2 nama_pasien', 'prompt' => '', "col-index" => "5"]))).'\'
                ],
                [
                    3,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('lokasirak_id', NULL, $data_rak, ['class' => 'form-control select2', 'prompt' => '']))).'\'
                ],

                [
                    4,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('subrak_id', NULL, $data_subrak, ['class' => 'form-control select2', 'prompt' => '']))).'\'
                ],
                [
                    9,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('warnadokrm_id', NULL, $data_warna_dokumen, ['class' => 'form-control select2', 'prompt' => '']))).'\'
                ],
                [
                    10,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi_nama', '',
                            ArrayHelper::map($data_instalasi, 'instalasi_id', 'instalasi_nama'),
                            [
                                'id' => 'filter_instalasi',
                                'class' => 'form-control select2 dep-to-child',
                                'prompt' => \Yii::t('fe', ''),
                                'data-url' => '/rm/tra-pemesanan-dok-rekam-medik/get-ruangan',
                                'data-depend_id' => 'filter_ruangan',
                                'data-depend_prompt' => \Yii::t('fe', ''),
                                'data-storage' => 'ruangan',
                                'data-key' => 'ruangan_id',
                            ]
                        )
                    )).'\'
                ],
                [
                    11,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_nama', '',
                            ArrayHelper::map($data_ruangan, 'ruangan_id', 'ruangan_nama'),
                            [
                                'id' => 'filter_ruangan',
                                'class' => 'form-control select2 dep-to-parent',
                                'data-url' => '/rm/tra-pemesanan-dok-rekam-medik/get-instalasi',
                                'data-depend_id' => 'filter_instalasi',
                                'prompt' => \Yii::t('fe', '')
                            ]
                        )
                    )).'\'
                ],
            ], {
            }, true
        );
        dateRangeHelper(".startDate",".endDate",".targetDate");
        
        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")). '",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });

        $(".norm").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "/rm/inf-dokumen/get-no-rekam-medik",
                dataType: "json",
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        $(".nama_pasien").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "/rm/inf-dokumen/get-pasien",
                dataType: "json",
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        let tabel = $("#example").DataTable();
        let tableData = tabel.row(".selected").data();
        if (parseInt(ruangan_id) != 16) {
            $(".btn-simpan").hide();
            table.column(0).visible(false);
        }
        $(document).on("click", "tbody tr", function() {
            let tabel = $("#example").DataTable();
            let tableData = tabel.row(".selected").data();
            if (typeof tableData !== "undefined") {
                const is_indexing = tableData.is_indexing;
                if (is_indexing == null) {
                    if(tableData.ruanganakhir_id == 16){
                        $(".btn-simpan").removeAttr("disabled");
                    }else{
                        $(".btn-simpan").attr("disabled","disabled");
                    }
                } else {
                    $(".btn-simpan").attr("disabled","disabled");
                }
            }
        });
    });
', View::POS_END, 'b-index');
?>