<?php

use app\components\DHtml;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title =  (DHtml::getTitleMenu() != NULL) ? DHtml::getTitleMenu() : $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php
                $defaultBtn= [
                    'search',
                    'reset',
                    'add'=> [
                        'attributes' => [
                            'action' => '/kasir/kontrak-manajemen/create',
                        ]
                    ],
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',                            
                            'data-target' => '/kasir/kontrak-manajemen/detail?id=',
                            'id' => 'view-kontrak-manajemen'
                        ] 
                    ],
                ];
                ?>

            <?=DocoHelpers::generateToolbar($defaultBtn,'#table-informasi');?>

               
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <br />
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Nomor Kontrak");?></th>
                            <th><?=\Yii::t("fe", "Nama Kontrak");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Mulai");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Berakhir");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
//
$this->registerJs('
// Global Var
var table;


$(document).ready(function() {
    
    table = $("#table-informasi").docoTabel({
        filter: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "os",
            selector: "tr"
        },
        sorting: [[2, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"kasir/kontrak-manajemen/get-data",
        columns: [
            {
                data : null,
                render : function ( data, type, full, meta ) {
                    return null;
                },
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Nama Penjamin")).'",
                data: "penjamin_nama",
                name:"penjamin_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Cara Bayar")).'",
                data: "carabayar_nama",
                name:"carabayar_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Nomor Kontrak")).'",
                data: "no_kontrak",
                name:"no_kontrak"
            },
            {
                title: "'.(\Yii::t("fe", "Nama Kontrak")).'",
                data: "nama_kontrak",
                name:"nama_kontrak"
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Mulai")).'",
                data: "tgl_mulai",
                name:"tgl_mulai",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Berakhir")).'",
                data: "tgl_selesai",
                name:"tgl_selesai",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Status")).'",
                data: "is_active",
                name:"is_active",
            },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,
    [
        [
            3,
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('carabayar_nama', '',
                    ArrayHelper::map($resMaster['carabayar'], 'carabayar_nama', 'carabayar_nama'),
                    [
                        'id' => 'filter_carabayar',
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
                        //'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-penjamin',
                        // 'data-depend_id' => 'filter_penjamin',
                        // 'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                        // 'data-storage' => 'penjamin',
                        // 'data-key' => 'penjamin_nama',
                    ]
                )
            )).'</div>\'
        ],
        [
            2,
            \''.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('penjamin_nama', '',
                     ArrayHelper::map($resMaster['penjamin'], 'penjamin_nama', 'penjamin_nama'),
                    [
                        'id' => 'filter_penjamin',
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),                        
                        // 'data-depend_id' => 'filter_carabayar',
                    ]
                )
            )).'\'
        ],
        [
            8,
            \''.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('is_active','',[
                    TRUE => 'Aktif',
                    FALSE => 'Tidak Aktif',
                ],
                [
                    'class' => 'form-control input-xs',
                    'prompt' => \Yii::t('fe', '--Pilih Status--'),
                ]

                )
            )).'\'
        ],     
    ], {
        3:0,
        2:1,
        4:2,
        5:3,
    }, true);
});

$("#view-kontrak-manajemen").on("click", function(){
    try {
        status_kontrak = table.row(".selected").data().status_kontrak ? table.row(".selected").data().status_kontrak : null;
    } catch (e) {
        status_kontrak = false;
    }


    if (status_kontrak) {
       if(status_kontrak != 591){
            docoNotification("warning", i18next.t("Perhatian, Gagal di Ubah !"), i18next.t("Belum ada kontrak yang terpilih"));
            return false;
       }
    }
    
});

',View::POS_END,'b-index');
