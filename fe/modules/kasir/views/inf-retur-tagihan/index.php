<?php
/**
* Author : Ramdhan Nurrachman
*
* Last Modified : 15 Feb 2019
*   by Anggoro (tri.anggoro@docotel.com)
*   branch : feature/kasir-retur-tagihan-pasien
**/

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'kwitansi' => [
                            'title' => \Yii::t('fe', 'Cetak Kwitansi'),
                            'icon' => 'fa fa-file',
                            'attributes' => [
                                'data-target' => $module."print-kwitansi?id=",
                                'data-pages' => '_blank',
                            ]
                        ],
                        'bkk' => [
                            'title' => \Yii::t('fe', 'Cetak BKK'),
                            'icon' => 'fa fa-file-o',
                            'attributes' => [
                                'data-target' => $module."print-bkk?id=",
                                'data-pages' => '_blank',
                            ]
                        ],
                        'delete' => [
                            'title' => \Yii::t('fe', 'Hapus'),
                            'icon' => 'fa fa-trash-o',
                            'attributes' => [
                                'data-options' => 'delete',
                                'data-additional' => 'data-rm',
                                'data-target' => $module."hapus?id=",
                                'id' => 'btn-hapus',
                            ]
                        ],
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th><?=\Yii::t("fe", "Tanggal Retur");?></th>
                            <th><?=\Yii::t("fe", "No Retur");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No. Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Retur");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/infreturtagihan.js'), VIEW::POS_END, 'js');
?>