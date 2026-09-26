<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 14:40:50
 * @Last Modified by:   Iqbal
 * @Last Modified time: 2018-08-13 16:36:11
 * @Description:
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$form = ActiveForm::begin([
    'id' => 'form-periksa',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="col-md-4">
            <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu periksa'); ?></label>
        </div>
        <div class="col-md-8">
            <b><span id="tgl_masukperiksa"></span></b>
            <?= Html::hiddenInput('PendaftaranForm[tgl_masukperiksa]', $modelPendaftaran->tgl_masukperiksa); ?>
        </div>
    </div>
    <?= $form
        ->field($modelPendaftaran, 'pegawai_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', '-- Pilih --'),
            'id' => 'pegawai'
            // 'disabled' => true,
        ]);
    ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton(\Yii::t('fe', 'Masuk'), ['class' => 'btn btn-success btn-sm', 'id' => 'btn-simpan']); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#btn-simpan').unbind()
        $('#btn-simpan').bind('click', () => {
            if ($('#pegawai').val() == '') {
                docoNotification("warning", "Peringatan", "Harap pilih dokter!");
            } else {
                let _formData = $('#form-periksa').serializeArray()
                $("#form-periksa").docoForm('submit', {
                    skipConfirm: true,
                    success: function(data) {
                        window.location = data.url;
                        tabel.draw();
                        $("#modal_backdrop").modal("toggle");
                        $("#batal-form").hide();
                    },
                    error : function(data){
                        var res = data.responseJSON.response.title;
                        if (typeof res !== 'undefined' && res == 'Gagal Assign Dokter' || typeof res !== 'undefined' && res == 'Pasien Sudah Dipulangkan') {
                            table.draw()
                            $('#modal_backdrop').modal('hide');
                        }
                    }
                })
            }
        })
    });
</script>

<?php
$this->registerJs($this->render('js/_informasi.js'));
?>
