<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = [ 'label' => Yii::t('fe', 'Master Diagnosa'), 'url' => ['/master/diagnosa#view-dtd'] ];
$this->params['breadcrumbs'][] = $this->title;
$model->is_active=1;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => '/',
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'save' => ['attributes' => ['data-target' => 'dtd-form']],
                    'reset',
                    'back' => ['attributes' => ['href' => '/master/diagnosa#view-dtd']],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                    <?php
                        $form = ActiveForm::begin([
                            'id'=>'dtd-form',
                            'options'=>[
                                'class'=>'form-horizontal',
                            ],
                            'enableClientValidation'=>false
                        ]);
                    ?>
                        <div class="form-group required">
                            <label for="is_active" class="col-lg-6 control-label">
                                <?= Yii::t('fe', 'Tabular Chapter'); ?>
                            </label>
                            <div class="col-lg-6">
                                <?= $form->field($model, 'is_active')
                                    ->dropDownList($status,['class' => 'select2'])->label(false); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6"><?=Yii::t('fe','Kode DTD')?></label>
                            <div class="col-sm-6">
                                <?php echo $form->field($model, 'dtd_kode')->textInput(['class' => 'form-control'])->label(false); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6"><?=Yii::t('fe','Kode Terperinci DTD')?></label>
                            <div class="col-sm-6">
                                <?php echo $form->field($model, 'dtd_kode')->textInput(['class' => 'form-control'])->label(false); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6"><?=Yii::t('fe','Nama DTD')?></label>
                            <div class="col-sm-6">
                                <?php echo $form->field($model, 'dtd_kode')->textInput(['class' => 'form-control'])->label(false); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6"><?=Yii::t('fe','Nama Lainnya DTD')?></label>
                            <div class="col-sm-6">
                                <?php echo $form->field($model, 'dtd_kode')->textInput(['class' => 'form-control'])->label(false); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6">&nbsp;</label>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'is_active')->checkbox(['label' => 'DTD Menular']) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6">&nbsp;</label>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'is_active')->checkbox(['label' => 'DTD Aktif']) ?>
                            </div>
                        </div>
                    <?php ActiveForm::end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
    $('#dtd-form').docoForm('submit',{
        success : function(data) {
        if (data.status == 201)
            this.formInput[0].reset();
        }
    });
JS;

$this->registerJs($script,View::POS_END,'jkun');
?>
