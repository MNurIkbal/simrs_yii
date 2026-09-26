<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?=DocoHelpers::generateToolbar([
                        'save' => [
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-submit-migrasi'
                            ]
                        ],
                        'download' => [
                            'type' => 'button',
                            'attributes' => [
                                'id' => 'btn-download-template',
                                'data-target' => Url::home().'gudang/migrasi-adjustment/download-template',
                                'data-options' => 'click'
                            ]
                        ]
                    ]);?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <?php
                            $form = ActiveForm::begin([
                                "id" => "import-adjustment",
                                "enableAjaxValidation" => false,
                                "enableClientValidation" => false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                "options" => [
                                    'role' => 'form',
                                    'enctype' => 'multipart/form-data'
                                ],
                            ]);
                        ?>

                        <div class="form-horizontal">
                            <?= $form->field($form_model, 'tgl_adjusmen')->textInput([
                                'readonly' => true,
                                'value' => date('d-M-Y')
                            ])
                            ->label(Yii::t('fe', 'Tanggal Adjustment')) ?>

                            <?= $form->field($form_model, 'ruangan_adjusmen_id')
                            ->dropDownList($ruangan, [
                                'class' => "select2"
                            ])
                            ->label(Yii::t('fe', 'Ruangan')) ?>

                            <?= $form->field($form_model, 'jenis_adjusmen')
                            ->dropDownList(['Masuk', 'Keluar'], [
                                'class' => 'select2'
                            ])
                            ->label(Yii::t('fe', 'Jenis Adjustment')) ?>

                            <?= $form->field($form_model, 'attachment')->fileInput()
                            ->label(Yii::t('fe', 'File Excel')) ?>

                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <div class="row" id="invalid_wrapper" style="display: none">
                    <div class="col-md-12">
                        <h1 class="text-center">Data Invalid</h1>
                        <table class="table table-bordered table-hover table-responsive" id="invalid_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Obat</th>
                                    <th>Nama Obat</th>
                                    <th>Tanggal Kadaluarsa</th>
                                    <th>Satuan</th>
                                    <th>Qty</th>
                                    <th>Harga Netto</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('migrasi-adjustment.js'),View::POS_END, 'js')
?>