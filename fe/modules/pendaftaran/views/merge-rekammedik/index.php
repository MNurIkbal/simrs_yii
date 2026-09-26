<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/pendaftaran/daftar']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
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
                    'search' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Konfirmasi'),
                        'icon' => 'fa fa-search',
                        'method' => 'not exist',
                        'attributes' => [
                            'id' => 'btn-cari',
                            'data-options' => 'click',
                            'class' => 'bg-teal btn btn-info btn-labeled btn-xs',
                        ]
                    ],
                    'reset' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Muat Ulang'),
                        'icon' => 'fa fa-refresh',
                        'method' => 'not exist',
                        'attributes' => [
                            'id' => 'btn-reset',
                            'data-options' => 'click',
                            'class' => 'bg-teal btn btn-info btn-labeled btn-xs',
                        ]
                    ],
                    'add' => [
                        'title' => \Yii::t('fe', 'Gabungkan'),
                        'icon' => 'fa fa-floppy-o',
                        'attributes' => [
                            'id'=>'btn-simpan',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => Url::home().'pendaftaran/merge-rekammedik/confirm-merge?no_rekammedik_asal=',
                        ]
                    ],
                ]) ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group select2-md">
                            <?= Html::label(Yii::t('fe', 'No Rekam Medik Asal'), 'no_rekammedik_asal', [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeDropdownList($model, 'no_rekammedik_asal', [], [
                                'class' => 'form-control',
                                'id' => 'no_rekammedik_asal',
                                'prompt' => Yii::t('fe', 'Pilih')]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group select2-md">
                            <?= Html::label(Yii::t('fe', 'No Rekam Medik Tujuan'), 'no_rekammedik_tujuan', [
                                'class' => 'control-label'
                            ]) ?>
                             <?= Html::activeDropdownList($model, 'no_rekammedik_tujuan', [], [
                                'class' => 'form-control',
                                'id' => 'no_rekammedik_tujuan',
                                'prompt' => Yii::t('fe', 'Pilih')]
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="row data_pasien_asal" hidden>
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-sm-12">
                        <?= Yii::$app->controller->renderPartial('identitas_pasien_awal') ?>
                    </div>
                </div>
                <div class="row data_pasien_tujuan" hidden>
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-sm-12">
                        <?= Yii::$app->controller->renderPartial('identitas_pasien_tujuan') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs(""
    .$this->render('js/index.js')
    , View::POS_END, "js-index");

 ?>