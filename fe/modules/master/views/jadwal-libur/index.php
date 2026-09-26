<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
$form = ActiveForm::begin([
    'id' => 'jadwallibur-form-update', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
]); 
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important;">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$title); ?></b></h3>
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
                    // 'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-jenis', 'class' => 'btn btn-info btn-labeled btn-xs data-reset ']],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/jadwal-libur/create',
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <br>
                <div id="calendar">
                </div>

            </div>
        </div>
    </div>
</div>

<div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span
                        class="sr-only">close</span></button>
                <h5 class="modal-title"><?=$titleUbah;?></h5>
            </div>
            <div id="modalBody" class="modal-body">
                <?= $form->field($model, 'jadwallibur_id')->hiddenInput(['id'=>'jadwallibur_id'])->label(false); ?>
                <?= $form->field($model, 'tgl_libur', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9'
                    ],
                    'inputOptions'=>['id'=>'tgl_libur'],
                    'addon' => [
                        'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                    ]
                ]); 
                ?>
                <?= $form->field($model, 'ket_libur')->textarea(['rows' => '4'],['class' => 'form-control']); ?>

                <div class="form-group">
                    <label for="is_liburnasional" class="col-lg-1 control-label">
                        <?= Yii::t('fe', 'Libur Nasional'); ?>
                    </label>
                    <div class="col-lg-6">
                        <?=$form->field($model, 'is_liburnasional')->checkbox()?>
                    </div>
                </div>
            </div>
            <br>
            <div class="modal-footer">
                <?= Html::button('Hapus', ['class' => 'btn btn-danger btn-md btn-hapus']) ?>
                <?= Html::button('Simpan', ['class' => 'btn btn-success btn-md btn-update']) ?>
                <?= Html::button('Kembali',[
                'class' => 'btn btn-default btn-md',
                'data-dismiss' => 'modal'
            ]); ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<div id="calendarModalDelete" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title"><?=$titleDelete;?></h5>
            </div>
            <div class="modal-body">
                <?php

                $formDelete = ActiveForm::begin([
                    'id' => 'batal-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'enableAjaxValidation'=>false, 
                    'enableClientValidation'=>false,
                    'action' => '',
                    'formConfig' => [
                        'labelSpan' => 3, 
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                ]);
                ?>

                <?= $formDelete->field($modelDelete, 'username')->textInput(['readonly'=>true]) ?>
                <?= $formDelete->field($modelDelete, 'password')->passwordInput() ?>
                <?= $formDelete->field($modelDelete, 'jadwallibur_id')->hiddenInput(['id'=>'jadwallibur_id_delete'])->label(false); ?>
                <div class="modal-footer">
                    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save-delete']); ?>
                    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <?php
$this->registerJs("
    $('.pickatime').pickatime({
        format: 'HH:i'
    });
    ", View::POS_READY, 'time-handler');
$this->registerJs($this->render('index.js'));

// $this->registerJs("
//     ", VIEW::POS_END, 'js-kunings');
?>