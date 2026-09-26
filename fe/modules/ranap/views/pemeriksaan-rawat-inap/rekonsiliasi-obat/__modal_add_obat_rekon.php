<?php
/**
* @author Rizal F. <rizal@docotel.com>
* @since 2018-07-18 
*/
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'add-obat-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation' => false,
    'enableClientValidation'=>false,
    'action' => '/ranap/pemeriksaan-rawat-inap/save-obat-rekon',
    // 'formConfig' => ['labelSpan' => 4,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Yii::t('fe', 'Tambah obat'); ?></h4>
</div>
<div class="modal-body">
    <?= $form->field($model, 'obatalkes_nama')->textInput(['class' => 'form-control']); ?>

    <?= 
    $form->field($model, 'obatalkes_kode', [
        'addon' => [
            'append' => [
                'content' => Html::button('<i class="fa fa-refresh"></i>', ['class'=>'btn btn-info btn-kode', 'title'=>'Buat ulang kode']),
                'asButton' => true
            ],
        ]
    ])->textInput(['class' => 'form-control kode-oa']); 
    ?>

</div>
<hr>
<div class="modal-footer">

    <?= Html::submitButton('Simpan', ['class' => 'btn btn-success btn-md']) ?>
    <?= Html::button('Batal',['class' => 'btn btn-default btn-md','data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>



<?php
    $this->registerJs($this->render('__modal_add_obat_rekon.js'), View::POS_END);
?>
