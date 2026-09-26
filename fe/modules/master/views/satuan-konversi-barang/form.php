<?php

use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = $model->satuankonversi_id == null ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Satuan Konversi Barang'), 'url' => ['index']];
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
                            'action' => '/master/satuan-konversi-barang/create-satuan?id='.$id,
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
                <div class="form-group">
                    <label class="control-label text-left control-label col-sm-3">Nama Obat Alkes</label>
                    <div class="col-md-5">
                        <p style="margin-top: 6px;"><?= $dataBarang['barang_nama'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label text-left control-label col-sm-3">
                        <?= Yii::t('fe', 'Satuan Kecil') ?></label>
                    <div class="col-md-5">
                        <p style="margin-top: 6px;"><?= $dataBarang['satuan_kecil'] ?></p>
                    </div>
                </div>
                <div class="row">
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
        url: baseUrl+"master/satuan-konversi-barang/change-status?id="+dataId+"&status="+dataStatus,
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
        ajax: baseUrl+"master/satuan-konversi-barang/get-konversi?barang_id=" + id,
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