<?php
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'plafon-bpjs-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
$label = !empty($id) ? "Update" : "Simpan";
?>
<style type="text/css">
.modal-open .modal {
    overflow-y: hidden !important;
}
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>

<div class="modal-body">
    <?= $form->field($model, 'plafonbpjs_id')->hiddenInput()->label(false);?>
    <?=$form
        ->field($model, 'instalasi_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList([], [
            'class' => 'form-control input-sm select2 selectInstalasi',
            'prompt' => \Yii::t('fe', 'Pilih'),
            'disabled' => $disabled
        ]);
    ?>
    <?=$form
        ->field($model, 'kelaspelayanan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList([], [
            'class' => 'form-control input-sm select2 selectKelas',
            'prompt' => \Yii::t('fe', 'Pilih'),
            'disabled' => $disabled
        ]);
    ?>
    <?= $form->field($model, 'is_ruangan')->checkbox(['label' => 'Ruangan Khusus']); ?>
    <?= $form->field($model, 'list_ruangan_id')->widget(DepDrop::classname(), [
            'options' => [
                'id' => 'ruangan_id',
                'class' => 'form-control select2',
                'multiple' => true,
            ],
            'pluginOptions'=>[
                'depends' => ['plafonbpjsform-instalasi_id'],
                'placeholder' => Yii::t('fe', 'Pilih'),
                'url'=> Url::to(['/master/plafon-bpjs/get-ruangan?selected='.$model->instalasi_id.'&ruangan_id='.$model->list_ruangan_id]),
                'prompt' => Yii::t('fe', 'Pilih Ruangan'),
                'initialize' => true,
            ]
        ])->label(Yii::t('fe', 'Ruangan'));
    ?>
    <?= $form->field($model, 'plafon_ruangan')->textInput(['class' => 'form-control input-sm doco-number']);?>
    <?= $form->field($model, 'plafon')->textInput(['class' => 'form-control input-sm doco-number']);?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> '.$label), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
    var id = "'.$id.'";
    var instalasiId = "'.$model->instalasi_id.'";
    var instalasi = "'.$model->instalasi.'";
    var kelasPelayananId = "'.$model->kelaspelayanan_id.'";
    var kelasPelayanan = "'.$model->kelas.'";
    var listRuanganId = "'.$model->list_ruangan_id.'";
    var listRuangan = "'.$model->list_ruangan.'";
    var isRuangan = "'.$model->is_ruangan.'";
',View::POS_END,'b-index');
$this->registerJs($this->render('form.js'), View::POS_END);
?>
