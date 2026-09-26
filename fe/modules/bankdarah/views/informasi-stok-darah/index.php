<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'detail',
                    'excel',
                    'reset'
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th>No</th>
                            <th><?= \Yii::t('fe', "Jenis Darah") ?></th>
                            <th><?= \Yii::t('fe', "Gologan Darah") ?></th>
                            <th><?= \Yii::t('fe', "Rhesus") ?></th>
                            <th><?= \Yii::t('fe', "Tanggal Kadaluarsa") ?></th>
                            <th><?= \Yii::t('fe', "Suhu Penyimpanan (C)") ?></th>
                            <th><?= \Yii::t('fe', "Stok") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Data tidak ditemukan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 </div>



<?php
$this->registerJs(
    "var _data_goldar = ".json_encode($lookup["golongandarah"]).";
    var _data_jd = ".json_encode($lookup["jenis_darah"]).";"
    .$this->render("js/infostokdarah.js"), View::POS_END, 'js');
?>