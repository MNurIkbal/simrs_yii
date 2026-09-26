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
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-ban',
                        'attributes' => [
                            'id' => 'data-hapus',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/pendaftaran/rencana-kontrol-inap/hapus-data-vclaim?noSuratKontrol=',
                            'data-params' => 'noSuratKontrol',
                            'disabled' => true
                        ]
                    ],
                    'print-rujukan' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print Rencana Kontrol/Inap'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-rencana',
                            'class' => 'btn-print-rencana',
                            // 'data-options' => 'click',
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-rencana?is_vclaim=1&no_surat_kontrol=',
                            'disabled' => true,
                            'data-pages' => '_blank',
                        ]
                    ],
                    'edit' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'data-edit',
                                'data-target' => '/pendaftaran/rencana-kontrol-inap/update?id=',
                                'data-conditions' => 'vclaim,noKartu',
                                'disabled' => true,
                            ]
                        ],
                ], '#tb-rencana-kontrol-vclaim') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-rencana-kontrol-vclaim" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "Tanggal Rencana Kontrol") ?></th>
                            <th><?= Yii::t("fe", "RI/RJ") ?></th>
                            <th><?= Yii::t("fe", "No. SEP") ?></th>
                            <th><?= Yii::t("fe", "No. Kartu") ?></th>
                            <th><?= Yii::t("fe", "Nama") ?></th>
                            <th><?= Yii::t("fe", "No Surat Kontrol/SPRI") ?></th>
                            <th><?= Yii::t("fe", "Spesialis/Sub Spesialis") ?></th>
                            <th><?= Yii::t("fe", "DPJP Tujuan Kontrol/Inap") ?></th>
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
$this->registerJs($this->render('partial/js/informasi-vclaim.js'));
?>
