<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $model->pemeriksaanrad_kode == null ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?php
        $is_disabled = $model->pemeriksaanrad_kode == null ? false : true;
    ?>
    <?= $form->field($model, 'pemeriksaanrad_kode', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm kode_unique', 'readonly' => $is_disabled]) ?>
    <?= $form->field($model, 'daftartindakan_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList($daftarTindakan, ['class' => 'form-control input-sm select2 tindakan', 'prompt' => Yii::t('fe', '--Pilih--')])->label(Yii::t('fe', 'Nama Pemeriksaan')) ?>
    <?= $form->field($model, 'jenispemeriksaanrad_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList($jenisPemeriksaanRad, ['class' => 'form-control input-sm select2', 'id' => 'jenispemeriksaanrad_id', 'prompt' => Yii::t('fe', '--Pilih--')]) ?>
    <div class="form-group">
        <?= Html::activeLabel($model, 'kelompokpemeriksaanrad_id', ['class' => 'text-right control-label col-sm-4']) ?>
        <div class="col-sm-8">
            <?= Html::hiddenInput('PemeriksaanRadForm[pemeriksaanrad_nama]', $model->pemeriksaanrad_nama, ['class' => 'form-control input-sm', 'id' => 'pemeriksaanrad_nama', 'readonly' => 'readonly']) ?>
            <?= Html::textInput('PemeriksaanRadForm[kelompok]', $model->pemeriksaanrad_nama, ['class' => 'form-control input-sm', 'id' => 'kelompok_nama', 'readonly' => 'readonly']) ?>
            <?= $form->field($model, 'kelompokpemeriksaanrad_id')->hiddenInput(['id' => 'kelompokpemeriksaanrad_id'])->label(false); ?>
        </div>
    </div>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<!-- Javascript -->
<script type="text/javascript">
    // After submit
    $("#form").docoForm("submit", {
        success : function(data) {
            // Check data status
            if (data.metadata.status == 201) {
                // Reset form
                $("#form")[0].reset();

                // Draw table
                table.draw();
                $("#btn-edit").prop("disabled", true);
                $("#btn-delete").prop("disabled", true);
                $('#modal_backdrop').modal('toggle');
            }
            else if (data.metadata.status == 200) {
                // Draw table
                table.draw();
                $("#btn-edit").prop("disabled", true);
                $("#btn-delete").prop("disabled", true);
                $('#modal_backdrop').modal('toggle');
            }
        }
    });

    // After jenis changed
    $(document).on("change", "#jenispemeriksaanrad_id", function() {
        // Ajax
        $.ajax({
            url: "pemeriksaan-rad/get-kelompok-by-jenis-id",
            type: "GET",
            data: {id: $(this).val()},
            dataType: 'json',
            success: function(result) {
                // Assign to id and nama
                $("#kelompokpemeriksaanrad_id").val(result.kelompokpemeriksaanrad_id);
                $("#kelompok_nama").val(result.nama_kelompok);
            }
        });
    });

    $(document).on('change', '.tindakan', function () {
        let nama_tindakan = $(this).find(':selected').text();
        $("#pemeriksaanrad_nama").val(nama_tindakan);
    })
</script>