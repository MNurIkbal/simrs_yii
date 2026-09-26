<?php

/**
 * @author Randy Vianda Putra
 * @copyright 6 September 2018 aweutist
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
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Shift'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
    $form = ActiveForm::begin([
        'id' => 'ajax-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
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
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-save']) ?>
                <?= Html::button('<b><i class="fa fa-repeat"></i></b>' . \Yii::t('fe', 'Muat Ulang'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-ulang']) ?>
                <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-kembali']) ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'shift-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
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
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'shift_kode')
                                    ->textInput([
                                        'class' => 'form-control input-sm'
                                    ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'shift_nama')
                                    ->textInput([
                                        'class' => 'form-control input-sm shift_nama'
                                    ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'shift_namalainnya')
                                    ->textInput([
                                        'class' => 'form-control input-sm shift_namalainnya'
                                    ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'shift_jamawal')
                                    ->textInput([
                                        'class' => 'form-control input-sm jam_awal'
                                    ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-lg-9">
                                <?=$form->field($model, 'shift_jamakhir')
                                    ->textInput([
                                        'class' => 'form-control input-sm jam_akhir'
                                    ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                    <?php if (!empty($id)) { ?>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'is_active')->radioList(array('1'=>'Ya', '0'=>'Tidak')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs($this->render('form.js'));
?>