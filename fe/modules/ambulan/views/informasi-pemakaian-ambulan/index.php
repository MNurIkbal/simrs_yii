<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"), 
    'url' => ['index']
];
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
                    <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
                        'proses' => [
                            'title' => \Yii::t('fe', 'Pengembalian Ambulan'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                // 'class' => 'spa',
                                // 'data-options' => false,
                                // 'id' => 'btn-approve',
                                'data-target' => '/ambulan/informasi-pemakaian-ambulan/pengembalian?id=',
                                'disabled' => true,
                                'id' => 'pengembalian-ambulan-form'
                                // 'data-conditions' => 'pengembalian-ambulan-form'
                            ]
                        ],
                        // 'edit' => [
                        //     'type' => 'button',
                        //     'title' => Yii::t('fe', 'Pengembalian Ambulan'),
                        //     'icon' => 'fa fa-arrow-left',
                        //     'attributes' => [
                        //         'data-options' => 'modal',
                        //         'disabled' => true,
                        //         'data-target' => '#modal_backdrop',
                        //         'data-url' => '/ambulan/informasi-pemakaian-ambulan/form-pengembalian?id=',
                        //         'id' => 'pengembalian-ambulan'
                        //     ],
                        // ],
                        'pdf' => [
                            'title' => \Yii::t('fe', 'Lihat'),
                            'attributes' => [
                                'id' => 'btn-lihat-surat-tugas',
                                'data-options' => false,
                                'data-pages' => '_blank',
                                'data-target' => '/ambulan/informasi-pemakaian-ambulan/export-surat-tugas-pdf',
                            ]
                        ],
                        'delete'=>[
                            'attributes'=>[
                                'data-confirm-message' => 'Apakah anda ingin membatalkan permintaan ambulan ini ?',
                                'class' => 'btn btn-info btn-labeled btn-xs data-delete btn-toolbar verif-button',
                                'data-additional'=>'data-rm',
                                'disabled'=> true,
                            ],
                        ],
                        /*
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/ambulan/informasi-pemakaian-ambulan/export-excel?'
                            ]
                        ]
                        */
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
                                <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "Nomor Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "Perkiraan Tanggal Kembali");?></th>
                                <th><?=\Yii::t("fe", "Nama Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "Jenis Ambulan");?></th>
                                <th><?=\Yii::t("fe", "Status Ambulan");?></th>
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

    // $(document).on("click", "#example tbody tr", function (event) {
    //     event.preventDefault();
    //     try {
    //         status = table.row(".selected").data().status_pesan 
    //                     ? table.row(".selected").data().status_pesan : null;
    //     } catch (e) {
    //         status = false;
    //     }

    //     if (status != 606) {
    //         $(".data-delete").prop("disabled",true);
    //         // $(".data-edit").prop("disabled",true);
    //         return true;
    //     } 
    //     $(".data-delete").prop("disabled",false);
    //     $(".data-edit").prop("disabled",false);
    // });

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
            // sorting: [[4, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"ambulan/informasi-pemakaian-ambulan/get-data",
            columns: [
                {
                    title: "", 
                    data: null, 
                    defaultContent: "", 
                    searchable: false, 
                    orderable: false,
                    width: "5%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'", 
                    data: "tgl_pesanambulan",
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Pemesanan")).'",  
                    data: "no_pesanambulan"
                },
                {
                    title: "'.(\Yii::t("fe", "Perkiraan Tanggal Kembali")).'",  
                    data: "tgl_pemakaiansampai",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pemesanan")).'", 
                    data: "nama_pemesan",
                    name: "nama_pemesan"
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Ambulan")).'", 
                    data: "jenis_ambulan"
                },
                {
                    title: "'.(\Yii::t("fe", "Status Ambulan")).'", 
                    data: "status_ambulan_nama", 
                    name : "status_ambulan"
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly/><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_pesanambulan', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nomor Pemesanan')]))).'\'
            ],
            [5, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_pemesan', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Pemesanan')]))).'\'
            ],
            [
                6, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('jenis_ambulan', '', 
                        [
                            'EMERGENCY' => 'EMERGENCY',
                            'NON EMERGENCY' => 'NON EMERGENCY',
                        ], 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '— Pilih Jenis Ambulan —'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                7, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('status_ambulan_nama', '', $statusPesan, 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '— Pilih Status —'),
                        ]
                    )
                )).'</div>\'
            ],
         ],{
            2:0,
            3:1,
            4:2,
            5:3,
            6:4,
            7:5,
        },true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $("#example tbody").on("click", "tr", function(){
            try {
                ambulan_id = table.row(".selected").data().ambulan_id ? table.row(".selected").data().ambulan_id : null;
                status_ambulan = table.row(".selected").data().ambulan_id ? table.row(".selected").data().status_ambulan : null;
            } catch (e) {
                ambulan_id = status_ambulan = false;
            }

            console.log(status_ambulan);

            if (status_ambulan == 591) {
                $("#pengembalian-ambulan, .verif-button").prop("disabled",true);
                $("#pengembalian-ambulan-form, .verif-button").prop("disabled",true);
            } else {
                $("#pengembalian-ambulan, .verif-button").prop("disabled",false);
                $("#pengembalian-ambulan-form, .verif-button").prop("disabled",false);
            }


            if (ambulan_id) {
                var pendaftaran_id = table.row(".selected").data().pendaftaran_id ? table.row(".selected").data().pendaftaran_id : null;
                $("#btn-lihat-surat-tugas").attr("data-target",$("#btn-lihat-surat-tugas").data("target")+ "?pendaftaran_id=" + pendaftaran_id + "&id=");
            } else {
                $("#btn-lihat-surat-tugas").removeAttr("action");
                $("#pengembalian-ambulan, .verif-button").prop("disabled",true);
                $("#pengembalian-ambulan-form, .verif-button").prop("disabled",true);
            }
        });


    });


', View::POS_END, 'b-index');
?>
