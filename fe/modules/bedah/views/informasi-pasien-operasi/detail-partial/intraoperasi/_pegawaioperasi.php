<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-10 11:12:41
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-13 10:18:31
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'intra-pegawai-operasi-form', 
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <?= $form->field($model, 'pegawai_id')->dropDownList([], ['class' => 'select2 select-pegawai', 'prompt' => '']); ?>
    <?= $form->field($model, 'posisi_tim')->dropDownList([], ['class' => 'select-posisi', 'prompt' => '']); ?>
    <?= Html::activeHiddenInput($model, 'prosentase', ['id' => 'prosentase-pegawai']) ?>
    <?= Html::activeHiddenInput($model, 'pegawai_nama', ['class' => 'pegawai-nama']) ?>
    <?= Html::activeHiddenInput($model, 'posisi_tim_nama', ['class' => 'posisi-nama']) ?>
    <?= Html::activeHiddenInput($model, 'slug_posisi', ['id' => 'slug-posisi']) ?>
    <?= Html::activeHiddenInput($model, 'inpostoperasi_id', ['class' => 'pegawai-inpost-id']) ?>
    <?= Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class' => 'pegawai-pasienmasukpenunjang']) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'> " . Yii::t('fe', 'Simpan') . "</i>", ['class' => 'btn bg-teal']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'> " . Yii::t('fe', 'Kembali') . "</i>", [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#intra-pegawai-operasi-form").docoForm("submit", {
        success: function(data) {
            _tableoperasi.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function() {
        var timOperasi = JSON.parse('<?= $timOperasi ?>')
        timOperasi.map((eachTim) => {
            $(".select-posisi").append(`
                <option value="${eachTim.id}" data-slug="${eachTim.slug}" data-prosentase="${eachTim.prosentase}">${eachTim.text}</option>
            `)
        })
        $(".select-posisi").select2()
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-pegawai').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/bedah/informasi-pasien-operasi/get-pegawai',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page) {
                    return {
                        q: term,
                        page: page
                    }
                },
                processResults: function(data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function(m) {
                return m;
            },
        });
        $('.select-pegawai').on('change', function() {
            $('.pegawai-nama').val($('.select-pegawai').select2('data')[0].text)
        })
        $('.select-posisi').on('change', function() {
            const {
                data,
                prosentase,
                slug
            } = $('.select-posisi').find(':selected').data()
            $('.posisi-nama').val(data.text)
            $("#prosentase-pegawai").val(prosentase)
            $("#slug-posisi").val(slug)
        })
    })
</script>
