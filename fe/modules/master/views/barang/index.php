<?php

/**
 * @Author: Budi
 * @Date:   2018-04-24 11:36:33
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                        </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add',
                    'edit',
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => '/master/barang/export-pdf?'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/master/barang/export-excel?'
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "kode Barang") ?></th>
                            <th><?= Yii::t("fe", "Nama Barang") ?></th>
                            <th><?= Yii::t("fe", "Kelompok") ?></th>
                            <th><?= Yii::t("fe", "Sub Kelompok") ?></th>
                            <th><?= Yii::t("fe", "Golongan") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
var attributes = {};
$(document).ready(function() {
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";
        
        $(this).docoForm("delete",{
            url: baseUrl+"master/barang/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                table.draw();
            }
        });
        table.draw();
    });

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
        ajax: baseUrl+"master/barang/get-data",
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
                title: "'.(\Yii::t("fe", "Kode Barang")).'",  
                data: "barang_kode",
            },
            {
                title: "'.(\Yii::t("fe", "Nama Barang")).'",  
                data: "barang_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Kelompok")).'",  
                data: "kelompokbarang_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Sub Kelompok")).'",  
                data: "subkelompok_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Golongan")).'",  
                data: "golonganbarang_nama",
            },
            {title: "Status", data: "is_active", class: "text-center"},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            7, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', [
                1 => Yii::t('fe', 'Aktif'), 0 => Yii::t('fe', 'Tidak Aktif')
            ], ['class' => 'form-control select2', 
            'prompt' => \Yii::t('fe', 'Pilih Status')]))).'\'
        ],
        [
            4, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelompokbarang_nama', '', ArrayHelper::map($response['kelompok'], 'kelompokbarang_id', 'kelompokbarang_nama'), ['class' => 'form-control select2', 'id' => 'kelompokbarang_nama',
            'prompt' => \Yii::t('fe', 'Pilih Kelompok Barang')]))).'\'
        ],
        [
            5, 
            \''.(preg_replace("/[\n\t\r]/i", '', DepDrop::widget([
                    'name' => 'subkelompok_nama',
                    'options' => [
                        'id' => 'subkelompok_nama',
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                       'depends'  => ['kelompokbarang_nama'],
                       'prompt' => 'Pilih Sub Kelompok Barang',
                       'placeholder' => 'Pilih Sub Kelompok Barang',
                       'url' => Url::to(['/master/barang/get-sub-kelompok'])
                    ]
                ])))
            .'\'
        ],
        [
            6, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('golonganbarang_nama', '', ArrayHelper::map($response['golongan'], 'lookup_id', 'lookup_name'), ['class' => 'form-control select2', 
            'prompt' => \Yii::t('fe', 'Pilih Golongan Barang')]))).'\'
        ]
    ], {
    2:0,
    3:1,
    4:2,
    5:3,
    6:4,
    7:5
    });
    
});

$(".data-reset").on("click", function(){
    $("#subkelompok_nama").prop("disabled", true);
});
', View::POS_END, 'b-index');
