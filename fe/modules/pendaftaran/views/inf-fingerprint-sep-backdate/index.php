<?php
/**
 * @Author: Fajar
 * @Date:   2022-04-08
 */

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = 'Informasi Pengajuan Fingerprint/SEP Backdate';
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
                    'approve' => [
                        'title' => \Yii::t('fe', 'Approve'),
                        'icon' => 'fa fa-check',
                        'attributes' => [
                            'id' => 'approval',
                            'data-options' => false,
                            'data-target' => '/pendaftaran/inf-fingerprint-sep-backdate/approval',
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar'
                        ]
                    ],
                ], '#tb-fingerprint-sep-backdate') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-fingerprint-sep-backdate" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th>No</th>
                            <th><?= Yii::t("fe", "No Kartu") ?></th>
                            <th><?= Yii::t("fe", "Nama Peserta") ?></th>
                            <th><?= Yii::t("fe", "Tgl SEP") ?></th>
                            <th><?= Yii::t("fe", "RI/RJ") ?></th>
                            <th><?= Yii::t("fe", "Persetujuan") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var table;
', View::POS_END, 'index');
$this->registerJs($this->render('partial/js/index.js'));
?>