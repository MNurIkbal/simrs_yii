<?php
// Author : Budi
// Date : 17 Januari 2018
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs($this->render('assets/js/pemakaian_barang.js'));
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
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                        <?= Yii::$app->controller->renderPartial('search/_search_laporan_pemakaian_barang', array(
                            'url_popup' => $url_popup,
                        )) ?>
                    </div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pemakaian");?></th>
                            <th><?=\Yii::t("fe", "Nama Penginput");?></th>
                            <th><?=\Yii::t("fe", "Nama Barang");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
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
        <div class="form-group">
            <?= Html::button(Yii::t('fe', ' Print'), [
                'class' => 'btn btn-dodger-blue fa fa-print',
            ]);?>
            <?= Html::button(Yii::t('fe', ' PDF'), 
                [
                    'class' => 'btn btn-crimson fa fa-file-pdf-o',
                ]);
            ?>
            <?= Html::button(Yii::t('fe', ' Excel'), 
                [
                    'class' => 'btn btn-green fa fa-file-excel-o',
                ]);
            ?>
            <?= Html::button(Yii::t('fe', ' Petunjuk'), 
                [
                    'class' => 'btn btn-cyan fa fa-question-circle',
                ]);
            ?>
        </div>
    </div>
</div>
