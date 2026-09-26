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
<div class="col-md-12 search-no-resep">
    <div class="row">
        <label for="search_no_resep" class="col-sm-2 col-form-label" id="no_resep">No. Resep / No. Reseptur :</label>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'no_transaksi', [
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
                                'url' => Url::to(['/apotek/worklist-farmasi/search-no-resep']) .
                                '?term=%QUERY',
                                'wildcard' => '%QUERY',
                            ]
                        ]
                    ],
                    'pluginEvents' => [
                        "typeahead:select" => "function(obj, item) {
                            $('#search_no_resep').val(item.id).trigger('change')
                        }"
                    ]
                ])->textInput([
                    'placeholder' => 'No. Resep / No. Reseptur / No. UDD',
                    'class' => 'form-control input-sm typeahead',
                    'autocomplete' => "on"
                ])->label(false); 
            ?>
            <input type="hidden" name="TransaksiNoWorkListForm[search_no_resep]" id="search_no_resep" value="">
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>