<?php
/*use Yii; */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\helpers\Url;
use kartik\widgets\DepDrop;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'menu-kasus-penyakit', 
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Jenis Kasus Penyakit'); ?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'jeniskasuspenyakit_id')
                ->dropDownList($kasusPenyakit,[
                    'class' => 'form-control select2',
                    'id' => 'jeniskasuspenyakit_id',
                    'prompt' => \Yii::t('fe', '--Pilih--')
                    ])->label(false); ?>
        </div>
    </div>
    <?php
        if (!empty($id_obat) && !empty($id_penyakit)) :
    ?>
        <div class="form-group required">
            <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Obat Alkes'); ?></label>
            <div class="col-lg-6">
                <?= $form->field($model, 'obatalkes_id')
                    ->dropDownList($obatAlkes,[
                        'class' => 'form-control select2',
                        'id' => 'obatalkes_id',
                        ])->label(false); ?>
            </div>
        </div>
    <?php
        else :
    ?>
        <div class="form-group required">
            <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Obat Alkes'); ?></label>
            <div class="col-lg-6">
                <?= $form->field($model, 'obatalkes_id')
                    ->dropDownList($obatAlkes,[
                        'class' => 'select-multiple-tags',
                        'id' => 'obatalkes_id',
                        'multiple' => true
                        ])->label(false); ?>
            </div>
        </div>
    <?php
        endif;

    ?>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;Simpan', 
                    [
                        'class' => 'btn bg-teal btn-md'
                    ]) 
            ?>
            <?= Html::button('<i class="fa fa-arrow-left"></i>&nbsp;Kembali',[
                                'class' => 'btn bg-slate btn-md',
                                'data-dismiss' => 'modal'
                                ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
        // $("#menu-kasus-penyakit").submit(function(event){
        //     alert("xxx");
        //     event.preventDefault();
            $("#menu-kasus-penyakit").docoForm('submit',{
                success : function(data) {
                    $('#modal_backdrop').modal('toggle');
                    table.draw();
                }
            });
        // });
</script>