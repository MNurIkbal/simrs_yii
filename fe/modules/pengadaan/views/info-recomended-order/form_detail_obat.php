<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-10-02 15:40:21
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-10-10 16:06:06
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'kembali' => [
                        'title' => 'Kembali',
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'id' => 'btn-back',
                            'data-options' => 'click',
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <center><h3 class="form-section"><b><?= $title ?><br /> <?= $ruangan ?></b></h3>
                </center>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-7"><b>
                                    <?= Yii::t("fe", "Nomor Transaksi") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:&nbsp;<?= isset($header['no_rekomendasiobat']) ? $header['no_rekomendasiobat'] : '' ?> 
                                    </b></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-7"><b><?= Yii::t("fe", "Tanggal Transaksi") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:&nbsp;<?= isset($header['tgl_rekomendasiobat']) ? date('d M Y', strtotime($header['tgl_rekomendasiobat']))  : '' ?> </b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Reorder Point");?></th>
                            <th><?=\Yii::t("fe", "Stok");?></th>
                            <th><?=\Yii::t("fe", "Rekomendasi");?></th>
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
var id = "'.$id.'";
var table;
$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: false,
        sorting: [[2, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"pengadaan/info-recomended-order/get-data-detail-obat?id=" + id,
        columns: [
            {
                title: "No.",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Nama Obat")).'", data: "obatalkes_nama"},
            {title: "'.(\Yii::t("fe", "Reorder Point")).'", data: "nilai_ro"},
            {title: "'.(\Yii::t("fe", "Stok")).'", data: "qty_tersedia"},
            {title: "'.(\Yii::t("fe", "Rekomendasi")).'", data: "rekomendasi"},
        ],
    });
})

$("#btn-back").on("click", function(){
    window.location.href = baseUrl+"pengadaan/info-recomended-order/obat";
});
', View::POS_END, 'b-index');
?>