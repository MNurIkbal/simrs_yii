<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Zat Aktif Obat'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .header-data{
      margin: 10px;
    }
</style>
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
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back',
                    'add' => [
                        'title' => Yii::t('fe', 'Tambah'),
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/zat-aktif-obat/create?id='.$id,
                        ]
                    ],
                    'hapus-zat-aktif' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-trash',
                        'attributes' => [
                            'id' => 'btn-hapus',
                            'data-options' => 'click'
                        ],
                    ]
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="header-data row" style="font-size: 16px">
                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label" style="padding: 0 10px">
                                <?= Yii::t("fe", "Kode Obat:") ?>
                            </label>
                        </div>
                        <div class="row">
                            <div>
                                <p style="padding: 0 10px"><b><?= $response['obatalkes_kode'] ?></b></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label" style="padding: 0 10px">
                                <?= Yii::t("fe", "Nama Obat:") ?>
                            </label>
                        </div>
                        <div class="row">
                            <div>
                                <p style="padding: 0 10px"><b><?= $response['obatalkes_nama'] ?></b></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label" style="padding: 0 10px">
                                <?= Yii::t("fe", "Jenis Obat:") ?>
                            </label>
                        </div>
                        <div class="row">
                            <div>
                                <p style="padding: 0 10px"><b><?= $response['jenisobatalkes_nama'] ?></b></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row col-md-12">
                    <table id="zat-aktif-obat" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">&nbsp;</th>
                                <th><?= Yii::t('fe', 'No') ?></th>
                                <th><?= Yii::t("fe", "Zat Aktif Obat") ?></th>
                                <th><?= Yii::t("fe", "Zat Utama") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var id = "'.$id.'";
var table;

$(".switch").bootstrapSwitch();
$(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
    var dataStatus = "0";
    var dataId = $(this).attr("data-id");
    if (e.target.checked == true)
        dataStatus = "1";

    $(this).docoForm("click", {
        url: baseUrl+"master/zat-aktif-obat/assign-primary?id="+id+"&zataktif_id="+dataId+"&is_primary="+dataStatus,
        confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
        confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah zat utama obat?")).'",
        success : function (data) {
            table.draw();
        }
    });

    table .draw();
});

$(document).ready(function() {
    table = $("#zat-aktif-obat").docoTabel({
        filter: false,
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "multi",
            selector: "tr"
        },
        sorting: [[2, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl + "master/zat-aktif-obat/get-list-detail?id=" + id,
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
                title: "Zat Aktif", 
                data: "zataktif_nama",
                orderable: false
            },
            {
                title: "Zat Utama",
                data: "is_primary", 
                class: "text-center"
            }
        ],
    });
});

$("#btn-hapus").on("click", function() {
    var data = table.rows(".selected").data().toArray();

    if (data.length > 0) {
        var obatalkes_id = data[0].obatalkes_id;
        let selected_ids = [];
        $.each(data, function(k, v){
            selected_ids.push(v.primary);
        });

        if(!data[0].is_utama) {
            $(this).docoForm("click", {
                url: baseUrl+"master/zat-aktif-obat/remove?obatalkes_id=" + obatalkes_id,
                method: "POST",
                type: "json",
                data: {
                    zataktif_ids: selected_ids
                },
                success : function(response) {
                    table.draw();
                }
            });
        } else {
            docoNotification("warning", "Peringatan!", "Tidak bisa hapus zat aktif utama. Silakan ubah zat aktif utama terlebih dahulu.");
        }
    } else {
        docoNotification("warning", "Silahkan Cek Masukan!", "Tidak ada data yang dipilih.");
    }
});
', View::POS_END, 'b-index');
?>
