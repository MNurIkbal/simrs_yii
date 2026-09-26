<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use kartik\widgets\DateTimePicker;

?>



<?php $form = ActiveForm::begin([
    'id' => 'nursing-note-form', 
    'enableClientValidation' => false,
    'action' => $formActionUrl,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-5">
            <?= $form->field($modal, 'tanggal', [
                    'labelOptions' => ['class' => 'text-right'],
                ])->textInput([
                    'class' => 'form-control input-sm pickadate',
                    'readonly' => true,
                    'id' => 'nursingnoteform-tanggal'
                ]); 
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($modal, 'jam', [
                'labelOptions' => ['class' => 'text-right'],
            ])->textInput(['class' => 'form-control input-sm jam_mulai']); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
           
            <?=$form->field($modal,'kegiatan_perawat[]',[
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-10',
                                'wrapper' => 'col-md-10'
                            ],
                        'addon' => [
                            'append' => [
                                'content' => Html::checkbox('active',false, ['label' => 'Obgyn', 'id'=>'checkbox']),  
                            ]]
                        ])->dropDownList(empty($modal->kegiatan_perawat) ? [] : $modal->kegiatan_perawat, [
                            'class' => 'form-control',
                            'data-plugin'=>'select2',
                    ])?>
                    
        </div>
    </div>

   
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($modal, 'catatan')->textarea(array('rows'=>4,'cols'=>5));
            ?>
        </div>
    </div>
   <br>
   <div class="row">
       <div class="text-right">
       <button type="button" style="margin-right: 5px" class="btn btn-info btn-labeled btn-right btn-xs data-back" id="btn-save-note"><b><i class="fa fa-save"></i></b>Simpan</button>

       </div>

    </div>
   
   
</div>

<?php ActiveForm::end(); ?>
<?php 
$this->registerJs("
    var tgl = '" . $tgl . "';
    var bln = '" . ($bln - 1) . "';
    var thn = '" . $thn . "';
    var url = '" . $url . "';
  
  ".$this->render('js/addnote.js'), View::POS_END, 'js');

?>