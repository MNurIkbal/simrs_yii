<?php
// Author : Maulana Muhammad Rizky
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\web\View;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .dataTable thead .sorting {
        padding-left: 2.2rem !important;
        padding-right: 0.25rem !important;
    }
</style>

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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                <div class="btn-group pull-left">
                    <?= DocoHelpers::generateToolbar([
                        'search' => [
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                                'data-table-id' => 'example',
                                'data-options' => 'click',
                            ]
                        ],
                        'reset' => [
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                                'data-table-id' => 'example',
                                'data-options' => 'click',
                            ]
                        ],
                        'excel',
                    ]); ?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Pembayaran"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "No Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Kasir"); ?></th>
                            <th><?= \Yii::t("fe", "Limit Diskon"); ?></th>
                            <th><?= \Yii::t("fe", "Diskon"); ?></th>
                            <th><?= \Yii::t("fe", "Nominal Diskon"); ?></th>
                            <th class="text-center"><?= \Yii::t("fe", "Status"); ?></th>
                            <th><?= \Yii::t("fe", "Actor"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Approve"); ?></th>
                            <th><?= \Yii::t("fe", "Action"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php
$this->registerJs("
        let statusApprove = " . json_encode($statusApprove) . ";
    " . $this->render('js/index.js'), View::POS_END);
?>