<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\web\View;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['/']];
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
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/radiologi/lap-waktu-tunggu-pasien-rad/show-popup?tipe=1&',
                        ]
                     ],
                     'pdf-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Cetak PDF',
                        'icon' => 'fa fa-file-pdf-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'pdf-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/radiologi/lap-waktu-tunggu-pasien-rad/show-popup?tipe=2&',
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="table-wrapper table-scroll-x">
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'No') ?></th>
                                <th><?= Yii::t("fe", "Tanggal Rujukan") ?></th>
                                <th><?= Yii::t("fe", "No Pendaftaran") ?></th>
                                <th><?= Yii::t("fe", "Pasien") ?></th>
                                <th><?= Yii::t("fe", "Dokter Radiologi") ?></th>
                                <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                                <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                                <th><?= Yii::t("fe", "Jenis Rujukan") ?></th>
                                <th><?= Yii::t("fe", "Asal Rujukan / Nama RS") ?></th>
                                <th><?= Yii::t("fe", "Tanggal Pendaftaran") ?></th>
                                <th><?= Yii::t("fe", "Tanggal Persetujuan") ?></th>
                                <th><?= Yii::t("fe", "Tanggal Ambil Foto") ?></th>
                                <th><?= Yii::t("fe", "Tanggal Expertise") ?></th>
                                <th><?= Yii::t("fe", "Waktu Tunggu Pendaftaran Expertise") ?></th>
                                <th><?= Yii::t("fe", "Waktu Tunggu Pemeriksaan Radiologi") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="15"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
