<?php

/**
 * @Author: Budi
 * @Date:   2018-04-24 11:36:33
 */

use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'form',
    'enableAjaxValidation' =>false, 
    'enableClientValidation' =>false, 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 3, 
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
]);
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
                    'generate' => [
                        'type' => 'button',
                        'title' => 'Generate',
                        'icon' => 'fa fa-cog',
                        'attributes' => [
                            'id' => 'generate',
                            'data-options' => 'click',
                        ]
                    ],
                    'add' => [
                        'title' => 'Buat RO',
                        'attributes' => [
                            'id' => 'buat-ro',
                            'data-options' => 'click',
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <h5 class="panel-title"><?= Yii::t('fe', 'Tanggal Transaksi') ?> : <?= date('d M Y'); ?> </h5>
                <br>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Nama Obat Alkes") ?></th>
                            <th><?= Yii::t("fe", "Reorder Point") ?></th>
                            <th><?= Yii::t("fe", "Stok") ?></th>
                            <th><?= Yii::t("fe", "Rekomendasi") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
var table;

$(document).ready(function() {
    $(document).on("click", "#generate", function(){
        table.destroy();
        var status_generate = true;
        $("#buat-ro").prop("disabled", false);
        table = $("#example").docoTabel({
            filter: true,
            searching: false,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"gudang/recomended-order/get-data-obat?status_generate=" + status_generate,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",  
                    data: "obatalkes_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Reorder Point")).'",  
                    data: "nilai_ro",
                    className:"text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Stok")).'",  
                    data: "sisa_stok",
                    className:"text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Rekomendasi")).'",  
                    data: "rekomendasi",
                    className:"text-right"
                },
            ],
        });
    });

    table = $("#example").docoTabel({
        filter: true,
        searching: false,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"gudang/recomended-order/get-data-obat",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",  
                data: "obatalkes_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Reorder Point")).'",  
                data: "nilai_ro",
            },
            {
                title: "'.(\Yii::t("fe", "Stok")).'",  
                data: "sisa_stok",
            },
            {
                title: "'.(\Yii::t("fe", "Rekomendasi")).'",  
                data: "rekomendasi",
            },
        ],
    });
});

$("#buat-ro").prop("disabled", true);
$("#buat-ro").on("click", function(){
    $(this).docoForm("click", {
        url : baseUrl+"gudang/recomended-order/save",
        method : "POST",
        type : "json",
        skipSuccessNotif: true,
        data: $("#form").serializeArray(),
            success : function (data) {
                var no_rekomendasiobat = data.response.no_rekomendasiobat;
                table.destroy();
                table = $("#example").docoTabel({
                filter: true,
                searching: false,
                sorting: [[1, "asc"]], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                ajax: baseUrl+"gudang/recomended-order/get-data-obat",
                columns: [
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",  
                        data: "obatalkes_nama",
                    },
                    {
                        title: "'.(\Yii::t("fe", "Reorder Point")).'",  
                        data: "nilai_ro",
                    },
                    {
                        title: "'.(\Yii::t("fe", "Stok")).'",  
                        data: "sisa_stok",
                    },
                    {
                        title: "'.(\Yii::t("fe", "Rekomendasi")).'",  
                        data: "rekomendasi",
                    },
                ],
            });
            setTimeout(function(){
                $("#buat-ro").prop("disabled", true);
                (new PNotify({
                    title: "Berhasil",
                    text: "Recomended Order Obat dengan Nomor " + "<strong>" + no_rekomendasiobat + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: "Ya",
                                addClass: "btn btn-xs btn-success",
                            },
                            {
                                text: "Tidak",
                                addClass: "btn btn-xs btn-danger",
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on("pnotify.confirm", function() {
                    // Print
                    window.open("/gudang/recomended-order/cetak-obat?no_rekomendasiobat="+no_rekomendasiobat);
                }).on("pnotify.cancel", function() {

                });
            }, 100);
        }
    });
});

', View::POS_END, 'b-index');
