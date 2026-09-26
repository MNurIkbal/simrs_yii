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
?>

<style>
    .dt-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        text-align: left !important;
    }
    .dd-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        padding-top: 2px;
    }
    .input-group-btn.dropdown-list {
        min-width:75px !important;
        text-align:left !important;
    }
    .input-group {
        width: 100%;
    }
</style>

<?php
$jenis = ['1'=>'APS', '0'=>'Rujukan'];
$form = ActiveForm::begin([
    'id' => 'form-daftar-rajal',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
    'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>

<div class="row select-no-rm">
    <div class="col-md-6">
        <?php
        $isRanap = 1;
        echo $form->field($modelPasien, 'no_rekam_medik', [
            'addon' => [
                'prepend' => [
                    'content'=> Html::checkbox('chk-statuspasien', true) . ' ' . Yii::t('fe', 'Pasien lama'),
                    'options'=>[
                        'class' =>  'hidden',
                    ]
                ]
            ]
        ])->widget(Select2::classname(), [
            'options' => [
                'id' => 'no_rekam_medik',
                'placeholder' => Yii::t('fe','No rm').' / '.Yii::t('fe', 'Nama pasien'). ' / ' .Yii::t('fe', 'Tanggal lahir')
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/pendaftaran/end-point/norm-ibu']),
                    'dataType' => 'json',
                    'delay' => 500,
                    'data' => new JsExpression('
                        function(params) {
                            return {
                                q:params.term,
                                isRanap:' . $isRanap . ',
                                isAps: $("input[name=\"PasienForm[is_aps]\"]:checked").val()
                            };
                        }
                    ')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression(
                    'function(no_rekam_medik) {
                        return no_rekam_medik.text;
                    }'
                ),
                'templateSelection' => new JsExpression(
                    'function (no_rekam_medik) {
                        $(".pendaftaran-id").val(no_rekam_medik.pendaftaran_id);
                        return no_rekam_medik.text;
                    }'
                ),
            ],
            'pluginEvents' => [
                'change' => 'function() {
                    var data_id = $(this).val();
                    if (data_id) {
                        getInfoPasienBayi(data_id);
                    }
                }'
            ],
        ]);
        ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<button class="btn btn-primary grid-button btn-sm btn-cari-norm hidden" data-toggle="modal" data-target="#modal_backdrop" data-width="75%"></button>

<button class="btn btn-primary grid-button btn-sm btn-form-bpjs hidden" data-toggle="modal" data-target="#modal_backdrop" data-width="90%" action="/pendaftaran/daftar/get-form-bpjs?pendaftaran_id=364&no_rekam_medik=00005">Tampilkan</button>

<button class="btn btn-primary grid-button btn-sm btn-form-asuransi hidden" data-toggle="modal" data-target="#modal_backdrop" data-width="75%" action="/pendaftaran/daftar/get-form-asuransi?pendaftaran_id=364&asalrujukan_id=3">Tampilkan</button>

<?php
$this->registerJs('
    '.$this->render('../js/bayi.js'));
?>
