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
                            'title' => \Yii::t('fe', 'Proses'),
                            'icon' => 'fa fa-check-square-o',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => false,
                                'id' => 'btn-approve',
                                'data-target' => '/ambulan/informasi-permintaan-ambulan/proses?id=',
                                'disabled' => true,
                                'data-conditions' => 'jenis_pasien_id'
                            ]
                        ],
                        'edit' => [
                            'title' => \Yii::t('fe', 'Edit Transaksi'),
                            'attributes' => [
                                'data-options' => false,
                                'data-target' => '/ambulan/informasi-permintaan-ambulan/edit?id=',
                                'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar',
                                'data-conditions' => 'jenis_pasien_id'
                            ]
                        ],
                        'delete' => [
                            'title' => 'Batal',
                            'icon' => 'fa fa-close',
                            'attributes' => [
                                'data-confirm-message' => 'Apakah anda ingin membatalkan permintaan ambulan ini ?',
                                'class' => 'btn btn-info btn-labeled btn-xs data-delete btn-toolbar verif-button',
                                'data-additional' => 'rm'
                            ]
                        ],
                        'pdf' => [
                            'title' => 'Lihat',
                            'attributes' => [
                                'data-options' => false,
                                'data-pages' => '_blank',
                                'data-target' => '/ambulan/informasi-permintaan-ambulan/export-pdf?id=',
                                'data-conditions' => 'jenis_pasien_id'
                            ]
                        ]
                    ],'#datatable');?>
                </div>
                <div class="panel-body">
                    <div class="advanced-filter">
                    </div>
                    
                    <table id="datatable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1" style="padding:0px;"></th>
                                <th>No</th>
                                <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "No Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "Nama Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "Jenis Pasien");?></th>
                                <th><?=\Yii::t("fe", "Jenis Ambulan");?></th>
                                <th><?=\Yii::t("fe", "Tujuan");?></th>
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

    $(document).ready(function() {
        table = $("#datatable").docoTabel({
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
            aaSorting: [],
            order: [] ,
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"ambulan/informasi-permintaan-ambulan/get-data",
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
                    title: "'.(\Yii::t("fe", "No. Pemesanan")).'",  
                    data: "no_pesanambulan"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pemesanan")).'",  
                    data: "nama_pemesan"
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Pasien")).'", 
                    data: "jenis_pasien",
                    name: "jenis_pasien_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Ambulan")).'", 
                    data: "jenis_ambulan"
                },
                {
                    title: "'.(\Yii::t("fe", "Tujuan")).'", 
                    data: "tujuan_pasien"
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "status_pesanambulan", 
                    name : "status_pesan"
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly/><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_pesanambulan', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'No. Pemesanan')]))).'\'
            ],
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_pemesan', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Pemesanan')]))).'\'
            ],
            [
                5, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('carabayar_nama', '', 
                        [
                            1 => 'Pasien RS',
                            2 => 'Luar RS'
                        ], 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '— Pilih Jenis Pasien —'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                6, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('carabayar_nama', '', 
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
                8, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('carabayar_nama', '', $statusPesan, 
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
            8:5,
            7:6,
        },true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });
    $(document).on("click", "#datatable tbody tr", function (event) {
        event.preventDefault();
        try {
            status = table.row(".selected").data().status_pesan 
                        ? table.row(".selected").data().status_pesan : null;
        } catch (e) {
            status = false;
        }

        if (status == 606 ) {
            $("#btn-approve").prop("disabled",false);
            $(".data-delete").prop("disabled",false);
            $(".data-edit").prop("disabled",false);
        }else if(status == 608 ){
             $(".data-edit").prop("disabled",true);
             $(".data-delete").prop("disabled",true);
             $("#btn-approve").prop("disabled",true);
        }else {
            $(".data-delete").prop("disabled",true);
            $(".data-edit").prop("disabled",true);
            $("#btn-approve").prop("disabled",true);
        } 
    });

', View::POS_END, 'b-index');
?>
