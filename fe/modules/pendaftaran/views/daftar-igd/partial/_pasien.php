<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use yii\widgets\Breadcrumbs;
    use kartik\widgets\ActiveForm;
    use kartik\widgets\DepDrop;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    use app\components\DocoHelpers;
    use kartik\widgets\FileInput;
    use app\components\DocoConstants;
    use kartik\typeahead\Typeahead;
?>

<?php
    $form = ActiveForm::begin([
        'id' => 'tipe-pasien',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableClientValidation'=>false,
        'enableAjaxValidation'=>false,
        // 'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>

<!-- Form Pasien -->
<?php 
    $modelBpjs->asal_rujukan = 2; 
    $tipePasien->asalrujukan_id = 1; 
?>
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-tipepasien',[
    'form' => $form,
    'tipePasien' => $tipePasien,
    'carabayar' => $carabayar,
    'carabayarOptions' => $carabayarOptions,
    'tipePasien' => $tipePasien,
    'penjamin_id' => $penjamin_id,
    'asal_rujukan' => $asal_rujukan,
    'tipePasien' => $tipePasien,
    'modelBpjs' => $modelBpjs,
    'instalasi_id' => $instalasi_id,
    'default_asal_rujukan' => $default_asal_rujukan,
    'instalasi_workspace' => $instalasi_workspace,
    'data_lookup' => $data_lookup,
    'support_multipayer' => $support_multipayer,
    'multiPayer' => $multiPayer,
    'is_Igd' => true,
]); ?>
<!-- End of Form Pasien -->

<!-- Form Rujukan -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/bpjs-error',[
    'form' => $form,
    'modelPasien' => $modelPasien
]); ?>
<!-- End of Form Rujukan -->

<!-- Form Rujukan -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-rujukan',[
    'form' => $form,
    'modelRujukan' => $modelRujukan

]); ?>
<!-- End of Form Rujukan -->

<!-- Form Pasien -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-pasien',[
    'data_lookup' => $data_lookup,
    'form' => $form,
    'modelPasien' => $modelPasien,
    'data_master' => $data_master,
    'optionsProv' => $optionsProv,
    'instalasi_id' => $instalasi_id,
    'is_hide_alias' => $is_hide_alias
]); ?>
<!-- End of Form Pasien -->


<?php ActiveForm::end(); ?>