<?php

/**
 * @Author: Arief Saputra
 * @Date:   2018-02-22 11:40:04
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-05-24 14:27:08
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;
use kartik\widgets\Typeahead;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;
use softark\duallistbox\DualListbox;
use yii\helpers\ArrayHelper;

// $this->title = $title;
// $this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Kelas'), 'url' => ['/master/kelas#view-kelaspelayanan']];
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'kelasruangan-form', 
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    
    echo Html::hiddenInput('latest[kelaspelayanan_id]', (isset($model->kelaspelayanan_id) ? $model->kelaspelayanan_id : '') ,[]);
    echo Html::hiddenInput('latest[ruangan_id]',(isset($model->ruangan_id) ? $model->ruangan_id : ''),[]);
    echo Html::hiddenInput('user_id',Yii::$app->user->identity->loginpemakai_id,[]);
    ?>
    <div class="form-group required">
        <label for="kelaspelayanan_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Kelas pelayanan'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'kelaspelayanan_id')
                ->dropDownList(
                    ArrayHelper::map($listPelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama'),
                    [
                        'class' => 'select2 autoKelasPelayanan',
                        'prompt' => Yii::t('fe', '-- Pilih --')
                    ]
                )->label(false); 
            ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="ruangan_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Ruangan'); ?>
        </label>
        <div class="col-lg-9">
            <?php
            echo $form->field($model, 'list_ruangan_id')
                ->dropDownList(
                    $listRuangan,
                    [
                        'class' => 'select2 autoListRuangan', 
                        'multiple'=>'multiple',
                        'id' => 'select2_list_ruangan_id'
                    ]
                )->label(false); 
            echo '<div id="error_KelasRuanganFormruangan_id"></div>';
            // $options = [
            //     'multiple' => true,
            //     'size' => 20,
            // ];
            // echo $form->field($model, 'list_jeniskasuspenyakit_id')->widget(\softark\duallistbox\DualListbox::className(),[
            //     'items' => $items,
            //     'options' => $options,
            //     'clientOptions' => [
            //         'moveOnSelect' => false,
            //         'selectedListLabel' => 'Selected Items',
            //         'nonSelectedListLabel' => 'Available Items',
            //     ],
            // ]);
            ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('Simpan', ['class' => 'btn btn-success btn-md']) ?>
            <?= Html::button('Kembali',[
                                'class' => 'btn btn-default btn-md',
                                'data-dismiss' => 'modal'
                                ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
$('#kelasruangan-form').docoForm('submit',{
    success : function(data) {
        this.formInput[0].reset();
        $('#modal_backdrop').modal('hide');
        tableRuangan.draw();
    }
}); 
</script>

