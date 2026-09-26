<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use yii\widgets\Breadcrumbs;
    use kartik\widgets\ActiveForm;
    use kartik\widgets\DepDrop;
    use kartik\select2\Select2;
    use app\components\DocoHelpers;
    use kartik\widgets\FileInput;
    use app\components\DocoConstants;
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
<?= Yii::$app->controller->renderPartial('/pendaftaran-ranap/partial/component/form-tipepasien',[
    'form' => $form,
    'tipePasien' => $tipePasien,
    'carabayar' => $carabayar,
    'carabayarOptions' => $carabayarOptions,
    'tipePasien' => $tipePasien,
    'penjamin_id' => $penjamin_id,
    'asal_rujukan' => $asal_rujukan,
    'tipePasien' => $tipePasien,
    'modelBpjs' => $modelBpjs,
    'modelAsuransi' => $modelAsuransi,
    'modelPj' => $modelPj,
    'modelPenanggungBiaya' => $modelPenanggungBiaya,
    'kelaspelayanan' => $kelaspelayanan,
    'data_lookup' => $data_lookup,
    'modelPasien' => $modelPasien,
    'modelKunjungan' => $modelKunjungan,
    'data_master' => $data_master,
    'optionsProv' => $optionsProv,
    'penOl' => $penOl,
    'instalasi_id' => $instalasi_id,
    'default_asal_rujukan' => $default_asal_rujukan,
    'instalasi_workspace' => $instalasi_workspace,
    'ddlkabupaten' => $ddlkabupaten,
    'ddlkecamatan' => $ddlkecamatan,
    'modelAdmisi' => $modelAdmisi,
    'bagianOptions' => $bagianOptions
]); ?>
<!-- End of Form Pasien -->

<!-- Form Rujukan -->
<?= Yii::$app->controller->renderPartial('/pendaftaran-ranap/partial/component/bpjs-error',[
    'form' => $form,
    'modelPasien' => $modelPasien
]); ?>
<!-- End of Form Rujukan -->

<!-- Form Rujukan -->
<?= Yii::$app->controller->renderPartial('/pendaftaran-ranap/partial/component/form-rujukan',[
    'form' => $form,
    'modelRujukan' => $modelRujukan

]); ?>
<!-- End of Form Rujukan -->

<!-- Form Pasien -->
<?= Yii::$app->controller->renderPartial('/pendaftaran-ranap/partial/component/form-pasien',[
    'data_lookup' => $data_lookup,
    'form' => $form,
    'modelPasien' => $modelPasien,
    'data_master' => $data_master,
    'optionsProv' => $optionsProv,
    'modelKp' => $modelKp,
    'data_lookup' => $data_lookup,
    'ddlkabupaten' => $ddlkabupaten,
    'ddlkecamatan' => $ddlkecamatan,
    'instalasi_id' => $instalasi_id,

]); ?>
<!-- End of Form Pasien -->

<?php ActiveForm::end(); ?>