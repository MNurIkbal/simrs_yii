<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
                <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-target' => '/penjamin-asuransi/informasi-alokasi-pembayaran/detail?id=',
                                'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar verif-button',
                                'disabled' => true
                            ]
                        ],
                        // 'delete' => [
                        //     'title' => 'Batal',
                        //     'icon' => 'fa fa-close',
                        //     'attributes' => [
                        //         'data-confirm-message' => 'Apakah anda ingin membatalkan pengajuan ini ?',
                        //         'class' => 'btn btn-info btn-labeled btn-xs data-delete btn-toolbar verif-button',
                        //         'disabled' => true
                        //     ]
                        // ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/penjamin-asuransi/informasi-alokasi-pembayaran/export-pdf?'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/penjamin-asuransi/informasi-alokasi-pembayaran/export-excel?'
                            ]
                        ],
                    ],'#example');?>
                </div>
                <div class="panel-body">
                    <div class="advanced-filter">
                </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th>No</th>
                                <th><?=\Yii::t("fe", "Tanggal Pengajuan");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pembayaran");?></th>
                                <th><?=\Yii::t("fe", "No Pembayaran");?></th>
                                <th><?=\Yii::t("fe", "No Pengajuan");?></th>
                                <th>Cara Bayar / Penjamin</th>
                                <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                                <th><?=\Yii::t("fe", "Penjamin");?></th>
                                <th><?=\Yii::t("fe", "Total Pengajuan (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Telah Bayar  (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Total Pembayaran  (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Sisa  (Rp.)");?></th>
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

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).on("click", "#example tbody tr", function (event) {
        event.preventDefault();
        try {
            status = table.row(".selected").data().is_edit 
                        ? table.row(".selected").data().is_edit : 0;
        } catch (e) {
            status = 0;
        }
        console.log(status);
        if (status != 0) {
            $(".verif-button").prop("disabled",false);
        } else {
            $(".verif-button").prop("disabled",true);
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
            sorting: [[4, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"penjamin-asuransi/informasi-alokasi-pembayaran/get-data",
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
                    title: "'.(\Yii::t("fe", "Tanggal Pengajuan")).'", 
                    data: "tgl_pengajuanklaim",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'", 
                    data: "tgl_pembayaranalokasi", 
                    name: "tgl_terimabayarklaim"
                },
                {
                    title: "'.(\Yii::t("fe", "No. Pembayaran")).'",  
                    data: "no_terimabayarklaim"
                },
                {
                    title: "'.(\Yii::t("fe", "No. Pengajuan")).'",  
                    data: "no_pengajuanklaim"
                },
                {
                    title: `Cara Bayar / Penjamin`, 
                    searchable: false,
                    orderable: false,
                    render: (data, type, row, meta) => {
                        let caraBayar = row.carabayar_nama
                        caraBayar = `<b>` + caraBayar + `</b>`;
                        const penjamin = row.penjamin_nama
                        const renderText = caraBayar + ` / <br/>` + penjamin;
                        return renderText;
                    }
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar")).'", 
                    data: "carabayar_nama",
                    visible: false
                },
                {
                    title: "'.(\Yii::t("fe", "Penjamin")).'", 
                    data: "penjamin_nama",
                    visible: false
                },
                {
                    title: "'.(\Yii::t("fe", "Total Pengajuan (Rp.)")).'", 
                    data: "total_pengajuan", 
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Telah Bayar (Rp.)")).'", 
                    data: "jumlah_pembayaran", 
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Total Pembayaran (Rp.)")).'", 
                    data: "total_pembayaran",
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Sisa (Rp.)")).'", 
                    data: "sisa",
                    searchable: false,
                    className : "text-right"
                },
            ],
            scrollCollapse: true,
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                3, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly/><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                7, 
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
                8, 
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
         ], {
            3:0,
            4:1,
            5:2,
            7:3,
            8:4
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });


', View::POS_END, 'b-index');
?>
