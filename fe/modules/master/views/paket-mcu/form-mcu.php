<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Paket MCU'), 'url' => ['index']];
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
            </div> 
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-kembali']) ?>
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-save-paket-mcu']) ?>
            </div>
            <div class="panel-body">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => 'paket-mcu-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            // 'type' => ActiveForm::TYPE_INLINE,
                            // 'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => [
                                'labelSpan' => 3,
                                'deviceSize' => ActiveForm::SIZE_SMALL
                            ],
                            'options' => [
                                'role' => 'form',
                                'enctype'=>'multipart/form-data'
                            ]
                        ]);
                    ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'tipepaket_kode')
                                    ->textInput([
                                        'class' => 'form-control input-sm kode_unique',
                                        'id' => 'tipepaket_kode'
                                    ]); 
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'tipepaket_nama')
                                    ->textInput([
                                        'class' => 'form-control input-sm tipepaket_nama',
                                        'id' => 'tipepaket_nama'
                                    ]); 
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'tipepaket_namalainnya')
                                    ->textInput([
                                        'class' => 'form-control input-sm tipepaket_namalainnya',
                                        'id' => 'tipepaket_namalainnya'
                                    ]); 
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-9">
                            <?= $form->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])->checkbox(['class'=>'pull-left', 'id' => 'is_active','label'=> Yii::t('fe', "Aktif")])->label(Yii::t('fe', "Status")); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'keterangan_tipepaket')
                                    ->textArea([
                                        'class' => 'form-control input-sm keterangan_tipepaket',
                                        'id' => 'keterangan_tipepaket'
                                    ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br><hr>
                    </div>
                    <div class="col-md-8">
                        <table id="table-paket-mcu" class="table table-striped table-condensed table-hover dataTable no-footer" style="width: 100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width=3%>No</th>
                                    <th width=40%><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
                                    <th width=70%><?=\Yii::t("fe", "Paket/Tindakan");?></th>
                                    <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                </tr>
                                <tr>
                                    <td>#</td>
                                    <td>
                                        <?= $form->field($model, 'ruangan_id',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                        ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'ruangan_id',
                                            'prompt' => '— Pilih —'
                                        ])->label(false); ?>
                                    </td>
                                    <div id="_paket" style="display: block;">
                                    <td>
                                    <?= $form->field($model, 'tipepaket_id',[
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                            'addon' => [
                                                'prepend' => [
                                                    'content' => Html::checkbox('paket', false, [
                                                        'id' => 'paket', 
                                                        'label' => Yii::t('fe', 'Paket')
                                                    ])
                                                ],
                                            ]
                                        ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'tipepaket_id',
                                            'tabindex' => '1'
                                        ])->label(false); ?>
                                    </td>
                                    </div>
                                    <?= Html::activeHiddenInput($model, 'paketdetail_id', ['id' => 'paketdetail_id']); ?>
                                    <?= Html::activeHiddenInput($model, 'daftartindakan_id', ['id' => 'daftartindakan_id']); ?>
                                    <td>
                                        <div class="btn-group pull-right">
                                            <?= Html::Button(
                                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                                    [
                                                        'class' => 'btn btn-success btn-labeled btn-xs btn-block',
                                                        'id' => 'simpan-table-paket-mcu'
                                            ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="isi-table">
                                    <td class="text-center" colspan="6">
                                        <?=\Yii::t("fe", "No data available in table.");?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs($this->render('js/form-mcu.js'), View::POS_END, 'b-index');
?>