<?php

use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = $model->satuankonversi_id == null ? Yii::t('fe', 'Detail Obat') : Yii::t('fe', 'Ubah Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Satuan Konversi'), 'url' => ['index']];
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
                        'title' => Yii::t('fe', 'Tambah Satuan'),
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/satuan-konversi/create-satuan?id='.$id,
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'form', 
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                ]); 
                ?>
                <div class="header-data">
                    <div class="col-md-6">
                        <div class="col-md-3 text-bold"><?= Yii::t('fe', 'Nama Obat Alkes') ?></div>
                        <div class="col-md-6"><?= @$dataObat['obatalkes_nama'] ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-3 text-bold"><?= Yii::t('fe', 'Satuan Terkecil / Penyimpanan') ?></div>
                        <div class="col-md-6"><?= @$dataObat['satuan_kecil'] ?></div>
                    </div>
                </div>
                <div class="row col-md-12">
                    <table id="konversi" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'No') ?></th>
                                <th><?= Yii::t("fe", "Satuan Besar") ?></th>
                                <th><?= Yii::t("fe", "Nilai Konversi") ?></th>
                                <th><?= Yii::t("fe", "Status") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var id = "'.$id.'";
$(".switch").bootstrapSwitch();
$(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
    var dataStatus = "0";
    var dataId = $(this).attr("data-id");
    if (e.target.checked == true)
        dataStatus = "1";

    $(this).docoForm("delete",{
        url: baseUrl+"master/satuan-konversi/change-status?id="+dataId+"&status="+dataStatus,
        confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
        confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
        success : function (data) {
            tableKonversi .draw();
        }
    });
    tableKonversi .draw();
});

$(document).ready(function() {
    tableKonversi = $("#konversi").docoTabel({
        filter: false,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"master/satuan-konversi/get-konversi?obatalkes_id=" + id,
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Satuan Besar", 
                data: "satuan_besar",
                orderable: false
            },
            {
                title: "Nilai Konversi",
                data: "nilai_konversi",
                searchable: false,
                orderable: false,
                class: "text-right"
            },
            {title: "Status", data: "is_active", class: "text-center"},
        ],
    });
});

', View::POS_END, 'b-index');
?>