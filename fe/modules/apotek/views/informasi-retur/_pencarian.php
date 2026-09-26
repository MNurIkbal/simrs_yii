<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\typeahead\Typeahead;
use kartik\widgets\ActiveForm;

?>
<?php
    $form = ActiveForm::begin([
        'id' => 'ajax-form', 
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
?>
<div class="col-md-12 search-nama-pasien">
    <div class="row">
        <label for="search_nama_pasien" class="col-sm-4 col-form-label" id="pasien">Nama Pasien / No. Pendaftaran / No. RM :</label>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <?= $form->field($model, 'no_pendaftaran', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-12',
                    'wrapper' => 'col-md-8'
                ],
                ])->widget(Typeahead::classname(),[
                    'pluginOptions' => [
                        'highlight' => true,
                        'limit' => 10,
                        'minLength' => 3
                    ],
                    'dataset' => [
                        [
                            'limit' => 10,
                            'display' => 'text',
                            'remote' => [
                                'url' => Url::to(['/apotek/informasi-retur/get-pencarian-pasien']) .
                                '?term=%QUERY',
                                'wildcard' => '%QUERY',
                            ]
                        ]
                    ],
                    'pluginEvents' => [
                        "typeahead:select" => "function(obj, item) {
                            $('#search_nama_pasien').val(item.id).trigger('change')
                        }"
                    ]
                ])->textInput([
                    'placeholder' => 'Nama Pasien / No. Pendaftaran / No. RM',
                    'class' => 'form-control input-sm typeahead',
                    'autocomplete' => "on"
                ])->label(false); 
            ?>
            <input type="hidden" name="TransaksiNoWorkListForm[search_nama_pasien]" id="search_nama_pasien" value="">
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>