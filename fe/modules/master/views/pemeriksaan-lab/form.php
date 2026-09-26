<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 17:48:26
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-27 18:01:13
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
    <h5 class="modal-title"><?= $model->pemeriksaanlab_kode == null ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?php
        $is_disabled = $model->pemeriksaanlab_kode == null ? false : true;
    ?>
    <?= $form->field($model, 'pemeriksaanlab_kode', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm kode_unique', 'readonly' => $is_disabled]) ?>
    <?= $form->field($model, 'daftartindakan_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList($daftarTindakan, ['class' => 'form-control input-sm select2', 'id' => 'daftartindakan_id', 'prompt' => Yii::t('fe', '--Pilih--')])->label(Yii::t('fe', 'Nama Pemeriksaan')) ?>
    <?= $form->field($model, 'jenispemeriksaanlab_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList($jenisPemeriksaanLab, ['class' => 'form-control input-sm select2', 'id' => 'jenispemeriksaanlab_id', 'prompt' => Yii::t('fe', '--Pilih--')]) ?>
    <div class="form-group">
        <?= Html::activeLabel($model, 'kelompokpemeriksaanlab_id', ['class' => 'text-right control-label col-sm-4']) ?>
        <div class="col-sm-8">
            <?= Html::textInput('PemeriksaanLabForm[nama_kelompok]', $model->nama_kelompok, ['class' => 'form-control input-sm', 'id' => 'nama_kelompok', 'readonly' => 'readonly']) ?>
            <?= $form->field($model, 'kelompokpemeriksaanlab_id')->hiddenInput(['id' => 'kelompokpemeriksaanlab_id'])->label(false); ?>
            <?= $form->field($model, 'pemeriksaanlab_nama')->hiddenInput(['id' => 'pemeriksaanlab_nama'])->label(false); ?>
        </div>
    </div>
    <?= $form->field($model, 'is_exception', ['labelOptions' => ['class' => 'text-right']])->radioList(array(1=>'Ya',0=>'Tidak'), ['class' => 'tes']) ?>
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
    var dataRecord = JSON.parse('<?= $data ?>')
    if (typeof dataRecord.is_exception != 'undefined') {
        $(`input[name="PemeriksaanLabForm[is_exception]"][value="${dataRecord.is_exception ? '1' : '0'}"]`).prop('checked', true)
    } else {
        $(`input[name="PemeriksaanLabForm[is_exception]"][value="0"]`).prop('checked', true)
    }
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
    $(document).on("change", "#jenispemeriksaanlab_id", function() {
        // Ajax
        $.ajax({
            url: "pemeriksaan-lab/get-kelompok-by-jenis-id",
            type: "GET",
            data: {id: $(this).val()},
            dataType: 'json',
            success: function(result) {
                // Assign to id and nama
                $("#kelompokpemeriksaanlab_id").val(result.kelompokpemeriksaanlab_id);
                $("#nama_kelompok").val(result.nama_kelompok);
            }
        });
    });

    $('#daftartindakan_id').on('change', function() {
        const selected = $(this).find(':selected');
        const data = selected.data() || {};
        const pemeriksaanlab_nama = data.data?.text || '';
        $('#pemeriksaanlab_nama').val(pemeriksaanlab_nama);
    });
</script>