<?php
    use yii\helpers\Html;
    use kartik\form\ActiveForm;
    use kartik\file\FileInput;
    use app\components\DocoHelpers;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<?php 
$hasDashboard = $model->url_page === '__dashboard__';
$form = ActiveForm::begin([
        'id' => 'modul-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'options' => ['enctype'=>'multipart/form-data'],
        'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
?>
<div class="modal-body">
    <?= $form->field($model, 'modul_nama',['labelSpan' => 3]) ?>
    <?= $form->field($model, 'modul_namalainnya',['labelSpan' => 3]) ?>
    <?= $form->field($model, 'url_modul',['labelSpan' => 3]) ?>
    <?= $form->field($model, 'icon_modul',['labelSpan' => 3] ) ?>
    <?= $form->field($model, 'is_active',['labelSpan' => 3])->dropDownList($status, [
        'id'=>'is_active',
        'class' => 'select2'
    ]) ?>
    <?php if (\Yii::$app->superset->enabled) : ?>
        <div class="form-group highlight-addon field-modulform-dashboard-setup">
            <label class="control-label col-sm-3">Default Page</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                        <input id="modulform-dashboard-setup" type="checkbox" <?= $hasDashboard ? 'checked' : ''; ?> > Dashboard
                    </label>
                </div>
            </div>
        </div>
        <?= $form->field($model, 'dashboard_id',['labelSpan' => 3, 'options' => ['class' => ['form-group required', 'modulform-toggle-dashboard-visible', $hasDashboard ? '' : 'hide']]]) ?>
    <?php endif; ?>
    <?= $form->field($model, 'url_page',['labelSpan' => 3, 'options' => ['class' => ['form-group', 'modulform-toggle-dashboard-visible', $hasDashboard && \Yii::$app->superset->enabled ? 'hide' : '']]]) ?>
</div>
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

<script type="text/javascript">
    $('#modul-form').docoForm('submit',{
        success : function(data) {
            $('#modal_backdrop').modal('toggle');
            _afterSave()
        }
    });
    $('#modulform-dashboard-setup').on('change', function() {
        $('.modulform-toggle-dashboard-visible').removeClass('hide');
        if ($(this).is(':checked')) {
            $('#modulform-url_page').val('__dashboard__');
            $('#modulform-url_page').closest('.form-group').addClass('hide');
        } else {
            $('#modulform-url_page').val('<?= $model->url_page == '__dashboard__' ? '' : $model->url_page; ?>');
            $('#modulform-dashboard_id').closest('.form-group').addClass('hide');
        }
    });
</script>