<?php

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
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
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'add',
                    'edit' => [
                        'title' => \Yii::t('fe', 'Edit'),
                        'attributes' => [
                            'id' => 'data-edit',
                            'data-options' => false,
                            'data-target' => '/pendaftaran/rujukan-khusus/update?id=',
                            'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar',
                            'disabled' => true
                        ]
                    ],
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-ban',
                        'attributes' => [
                            'id' => 'data-hapus',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'pendaftaran/rujukan-khusus/confirm-hapus',
                            'disabled' => true

                        ]
                    ],
                ], '#tb-rujukan-khusus') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-rujukan-khusus" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "No Rujukan") ?></th>
                            <th><?= Yii::t("fe", "No. Kartu") ?></th>
                            <th><?= Yii::t("fe", "Nama Pasien") ?></th>
                            <th><?= Yii::t("fe", "Diagnosa") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Rujukan Awal") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Rujukan Akhir") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$phpVars = [
    'bulan' => $dataBulan,
    'tahun' => $dataTahun
];
$this->registerJsVar('phpVars',$phpVars);

$this->registerJs('
    const tableId = "tb-rujukan-khusus";
    $(document).on("click", "#tb-rujukan-khusus tbody tr", function () {
        var idrujukan = null;
        var norujukan = null;
        var url = $("#data-hapus").attr("data-url");
        $("#data-hapus").attr("disabled", true);
        try {
            idrujukan = table.row(".selected").data().idrujukan ? table.row(".selected").data().idrujukan : null;
            norujukan = table.row(".selected").data().norujukan ? table.row(".selected").data().norujukan : null;
            $("#data-hapus").attr("data-url", url+"?idrujukan="+idrujukan+"&norujukan=" + norujukan+"&primaryid=");
        } catch (e) {
            idrujukan = null;
            norujukan = null;
        }
        if (idrujukan != null){
            $("#data-hapus").attr("disabled", false);
        }
    });
', View::POS_END, 'index');

$this->registerJs($this->render('partial/js/index.js'));
?>
