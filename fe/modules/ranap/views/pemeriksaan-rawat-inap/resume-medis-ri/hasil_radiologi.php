<?php

use dosamigos\ckeditor\CKEditor;
?>
<div class="row">
    <!-- <div class="col-md-12">
        <h6 class="text-label-size">Radiologi :</h6>
    </div> -->
    <div class="col-sm-12">
        <button type="button" data-type="rad" id="search-rad-btn" class="btn btn-info btn-labeled btn-xs search-penunjang"><b><i class="fa fa-search"></i></b> Cari Hasil Radiologi</button>
    </div>
    <div class="col-md-12" id="rad-form-wrapper" style="margin-top: 8px;">
        <?=
            $form->field($model, 'order_radiologi', [
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
            ])->label(Yii::t('fe', 'Radiologi'),['class' => 'text-bold'])
        ?>
        <div id="temporary_order_rad" style="display: none;"></div>
    </div>
</div>
