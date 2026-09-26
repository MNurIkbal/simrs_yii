<?php
// Author : Ardi Pratama
    use kartik\widgets\ActiveForm;
    use kartik\typeahead\Typeahead;
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
            'id' => 'jenis-tarif-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Kode Jenis Tarif')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'jenistarif_kode')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Nama Jenis Tarif')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'jenistarif_nama')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Nama Lainnya')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'jenistarif_namalainnya')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <!-- <label class="control-label text-left control-label col-sm-4 required">Antrian yang ditambahkan <span class="text-danger">*</span></label> -->
            <div class="col-md-12" id="penjamin-col">
                <?= $form->field($model, 'penjamin_idx', ['horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-10',
                ]])
                    ->dropDownList($additional_data['list_penjamin'], [
                        'class' => 'form-control input-sm list-penjamin',
                        'multiple' => 'multiple',
                        'id' => 'dualistbox-penjamin',
                    ]);
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="is_active" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Status'); ?>
        </label>
        <div class="col-lg-6">
            <?=$form->field($model, 'is_active')->checkbox()?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Catatan')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'catatan')
                ->textArea(['class' => 'form-control'])->label(false); ?>
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

    var dualistbox_penjamin = $('#dualistbox-penjamin').bootstrapDualListbox();
    $('#jenis-tarif-form').docoForm('submit',{
        success : function(data) {
            // table_render.draw();
            // table_komponen.draw();
        }
    });
</script>