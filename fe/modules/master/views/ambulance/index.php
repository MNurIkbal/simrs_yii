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
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',
                            'data-target' => '/master/ambulance/detail?id='
                        ]
                    ],
                    'add',
                    'edit'=> [
                        'attributes'=>[
                            'id' => 'edit-ambulan'
                        ]
                    ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
                            'data-target' => '/master/ambulance/delete-ambulan?id=',
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/master/ambulance/export-excel?'
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Nomor Polisi") ?></th>
                            <th><?= Yii::t("fe", "Jenis Ambulan") ?></th>
                            <th><?= Yii::t("fe", "Merek") ?></th>
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
$(".switch").bootstrapSwitch();

// $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
//     var dataStatus = "0";
//     var dataId = $(this).attr("data-id");
//     if (e.target.checked == true)
//         dataStatus = "1";
//     var ResData = {};
//
//     $(this).docoForm("click",{
//         url: baseUrl+"master/ambulance/change-status?id="+dataId+"&status="+dataStatus,
//         confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
//         confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
//         data: ResData,
//         method: "GET",
//     });
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
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"master/ambulance/get-data",
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
                title: "'.(\Yii::t("fe", "Nomor Polisi")).'",
                data: "no_polisi",
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Ambulan")).'",
                data: "is_emergency",
            },
            {
                title: "'.(\Yii::t("fe", "Merek")).'",
                data: "barang_merk",
                searchable: false,
            },
            {title: "Status", data: "is_active", class: "text-center"},
        ],
        initComplete: () => {
          $(".change-status").docoToggleSwitch({
            url: baseUrl+"master/ambulance/change-status",
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            method: "GET",
          });
        }
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
		[
            2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_polisi', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nomor Polisi')]))).'\'
        ],
        [
            3,
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_emergency', '', $jenisAmbulan, ['class' => 'form-control select2',
            'prompt' => \Yii::t('fe', '— Pilih Jenis Ambulan —')]))).'\'
        ],
        [
            5,
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $is_active, ['class' => 'form-control select2',
            'prompt' => \Yii::t('fe', '— Pilih Status — ')]))).'\'
        ],
    ]);

    $("#edit-ambulan").on("click", function(){
        try {
            status_ambulan = table.row(".selected").data().status_ambulan ? table.row(".selected").data().status_ambulan : null;
        } catch (e) {
            status_ambulan = false;
        }


        if (status_ambulan) {
           if(status_ambulan != 591){
                docoNotification("warning", i18next.t("Perhatian, Gagal di Ubah !"), i18next.t("Ambulan ini sedang dipakai"));
                return false;
           }
        }

    });

});


', View::POS_END, 'b-index');
