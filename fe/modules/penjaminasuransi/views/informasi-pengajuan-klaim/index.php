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
    use yii\helpers\ArrayHelper;
    use app\components\DocoHelpers;

    $this->title = DHtml::getTitleMenu();
    $this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
    $this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['/']];
    $this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .legend-wrapper{
        margin-bottom: 15px !important;
        margin-top:15px !important;
    }
    .card {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        width: 100%;
        border: 1px solid #34bfa3;
        position: relative;
    }

    .card:hover {
      box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .container {
      padding: 2px 16px;
    }

    .img-rekap {
        height: 35px;
        margin: 4px;
    }
    .card .title{
        position: absolute; 
        font-size:1vw; 
        top: 5px; 
        left: 60px;
    }
    .card .subtitle{
        position: absolute; 
        font-size:1vw; 
        top: 23px; 
        left: 65px;
        color: #525252
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
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-pengajuan-klaim/detail?id=',
                            'class'       => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar verif-button',
                            'disabled'    => true
                        ]
                    ],
                    'delete' => [
                        'title'      => 'Batal',
                        'icon'       => 'fa fa-close',
                        'attributes' => [
                            'data-confirm-message' => 'Apakah anda ingin membatalkan pengajuan ini ?',
                            'class'                => 'btn btn-info btn-labeled btn-xs data-delete btn-toolbar verif-button',
                            'disabled'             => true
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-pengajuan-klaim/export-pdf?'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-pengajuan-klaim/export-excel?'
                        ]
                    ],
                ], '#example');?>
            </div>
            <div class="panel-body">
                <div class="form-group legend-wrapper">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="card">
                                <div class="container">
                                    <img src="/media/img/icon-app/penjamin/legend_penjamin_total.png" class="img-rekap">
                                    <label class="title"><b>Total Pengajuan</b></label>
                                    <span id="legend_total_pengajuan" class="subtitle">Loading . . .</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pengajuan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Jatuh Tempo");?></th>
                            <th><?=\Yii::t("fe", "No Pengajuan");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Total Pengajuan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
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
<?php 
$this->registerJs('
    var table;
    localStorage.clear();
    localStorage.setItem("penjamin", \''.json_encode($penjamin).'\');

    $(document).on("click", ".data-reset", function() {
        table.draw();
    });

    $(document).on("click", "#example tbody tr", function (event) {
        event.preventDefault();
        try {
            status = table.row(".selected").data().status_pengajuanklaim 
                        ? table.row(".selected").data().status_pengajuanklaim : null;
        } catch (e) {
            status = false;
        }

        if (status && status != 552) {
            $(".verif-button").prop("disabled",true);
        } else {
            $(".verif-button").prop("disabled",false);
        }
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
            sorting: [2, "desc"], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"penjamin-asuransi/informasi-pengajuan-klaim/get-data",
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
                {title: "'.(\Yii::t("fe", "Tanggal Pengajuan")).'", data: "tgl_pengajuanklaim"},
                {title: "'.(\Yii::t("fe", "Tanggal Jatuh Tempo")).'", data: "tgl_jatuhtempo", searchable: false},
                {title: "'.(\Yii::t("fe", "No Pengajuan")).'",  data: "no_pengajuanklaim"},
                {title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'", data: "carabayar_nama", searchable: false, orderable: false, render: function (data, type, row, meta) {
                    let caraBayarNama = row.carabayar_nama
                    let penjaminNama = row.penjamin_nama
                    return `<b>` + caraBayarNama + `</b>` + ` <br/> ` + penjaminNama
                }},
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama", visible: false},
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", visible: false},
                {title: "'.(\Yii::t("fe", "Total Pengajuan")).'", data: "total_piutang", searchable: false},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "s_pengajuanklaim"},
            ],
            scrollCollapse: true,
            drawCallback: function (setting) {
                let response = setting.json;
                let summary = response.summary;
                let total_pengajuan = docoHelper.convertToRupiah(summary.total_pengajuan);
                $("#legend_total_pengajuan").html(`Rp. ${total_pengajuan}`);
            },
            preDrawCallback: function (setting) {
                $("#legend_total_pengajuan").html(`Loading . . .`)
            },
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                6, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('carabayar_nama', '', 
                        ArrayHelper::map($response['cara_bayar'], 'carabayar_id', 'carabayar_nama'), 
                        [
                            'id' => 'filter_carabayar', 
                            'class' => 'form-control select2 dep-to-child', 
                            'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--'),
                            'data-url' =>  '/penjamin-asuransi/informasi-pasien-non-bpjs/get-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_id',
                        ]
                    )
                )).'</div>\'
            ],
            [
                7, 
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('penjamin_nama', '',
                        ArrayHelper::map($response['penjamin'], 'penjamin_id', 'penjamin_nama'),
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-url' =>  '/penjamin-asuransi/informasi-pasien-non-bpjs/get-carabayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                )).'</div>\'
            ],
            [
                9,
                \'' . (preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('s_pengajuanklaim', '', 
                        ArrayHelper::map($response['status_klaim'], 'lookup_id', 'lookup_name'), [
                            'class' => 'form-control select2',
                            'id' => 's_pengajuanklaim',
                            'prompt' => Yii::t('fe', '--Pilih Status--') 
                        ]
                    )
                )).'\' 
            ],
         ], {
            2:0,
            6:1,
            7:2,
            9:3
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });


', View::POS_END, 'b-index');
?>
