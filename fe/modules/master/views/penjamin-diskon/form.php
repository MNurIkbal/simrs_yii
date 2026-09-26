<?php
/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php
$form = ActiveForm::begin([
    'id' => 'penjamin-diskon-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'validateOnSubmit' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>

<div class="modal-body">
    <?= $form->field($model, 'carabayar_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($caraBayarList, [
            'class' => 'form-control select2 selectCarabayar',
            'id'=>'selectCarabayar',
            'prompt' => Yii::t('fe', '--Pilih Cara Bayar--'),
        ]);
    ?>

    <?= $form->field($model, 'penjamin_id', ['labelOptions' => ['class' => 'text-left']])
        ->widget(DepDrop::classname(), [
            'options' => [
                'id' => 'penjamin_id',
                'class' => 'form-control select2'
            ],
            'data'=> isset($model->penjamin_id) ? $penjaminList : [],
            'pluginOptions'=>[
                'depends'=>['selectCarabayar'],
                'initialize' => true,
                'loadingText' => Yii::t('fe', 'Memuat...'),
                'placeholder'=>'--Pilih Penjamin--',
                'url'=>Url::to(['/master/penjamin/list-penjamin'])
            ]
        ]); 
    ?>


    <?= $form->field($model, 'diskon_otomatis', ['labelOptions' => ['class' => 'text-left']])
        ->input('number', [
            'placeholder' => $model->getAttributeLabel('diskon_otomatis'),
            'class' => 'form-control input-sm',
            'style' => 'width:20%',
            'step' => '0.01',
        ]);
    ?>

    <?= $form->field($model, 'is_active')
        ->radioList(
            [
                1=> Yii::t('fe', 'Ya'),
                0=> Yii::t('fe', 'Tidak'),
            ], 
            ['id'=>'is_active', 'inline'=>true, 'value'=> $model->is_active ]
        ); 
    ?> 

    <?= isset($id_before_update) ? Html::hiddenInput('PenjaminDiskonForm[id_before_update]', $id_before_update) : ''; ?>

    <div class="modal-footer">
        <button type="submit" id="btn-simpan" class="btn btn-info btn-labeled btn-xs data-save" data-target="ajax-form" onclick=""><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
        <button type="button" class="btn btn-info btn-labeled btn-xs data-back" data-dismiss="modal"><b><i class="fa fa-arrow-left"></i></b>Kembali</button>
    </div>
</div>

<script type="text/javascript">
    $('#penjamin-diskon-form').docoForm('submit',{
        skipErrorNotif: true,   
        success : function(data) {
            var form = $("#penjamin-diskon-form");
            form[0].reset();
            table_penjamin_diskon.draw();
            $("#modal_backdrop").modal('toggle');
        },
    });

    $(document).on('keydown', null, 'alt+s', function (event) {
        $("#btn-simpan").click();
    });
</script>

<?php ActiveForm::end(); ?>
