<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
?>
<div class="row" id="periksafisik-row">
    <!-- <div class="col-md-12">
        <h6 class="text-label-size">Laboratorium :</h6>
    </div> -->
    <div class="col-sm-12">
        <button type="button" data-type="lab" id="search-lab-btn" class="btn btn-info btn-labeled btn-xs search-penunjang"><b><i class="fa fa-search"></i></b> Cari Hasil Laboratorium</button>
        <button type="button" data-type="lab-external" id="search-lab-external-btn" class="btn btn-info btn-labeled btn-xs search-penunjang"><b><i class="fa fa-search"></i></b> Cari Hasil Laboratorium (Integrasi)</button>
    </div>
    <div class="col-md-12" id="lab-form-wrapper" style="margin-top: 8px;">
        <?=
            $form->field($model, 'order_laboratorium', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-12'
                ],
            ])->widget(CKEditor::className(), [
                'options' => ['rows' => 20],
                'preset' => 'basic',
                'clientOptions' => [
                    'extraPlugins' => '',
                    'allowedContent' => true,
                ]
            ])->label(Yii::t('fe', 'Laboratorium'),['class' => 'text-bold'])
        ?>
        <div id="temporary_order_lab" style="display: none;"></div>
    </div>
</div>
