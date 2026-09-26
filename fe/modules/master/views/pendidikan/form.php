<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'pendidikan-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label">Pendidikan</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'pendidikan_nama')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label">Indexing</label>
        <div class="col-lg-6 ">

            <?= $form->field($model, 'indexing_id',[
                    'inputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'Indexing'
                    ],
                    'template' => '
                    <div class="form-group has-feedback">
                        {input}
                        <div class="form-control-feedback">
                            <i class="icon-search4 text-size-base text-muted"></i>
                        </div>
                        {error}
                    </div>'
                ])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label">Nama Lainnya</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'pendidikan_namalainnya')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label">Urutan Pendidikan</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'pendidikan_urutan')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label">Status</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'is_active')
                ->dropDownList($status,['class' => 'select2'])->label(false); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
                <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
                <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
                                    'class' => 'btn bg-slate',
                                    'data-dismiss' => 'modal'
                                    ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#pendidikan-form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();
            _afterSave()
        }
    });
</script>