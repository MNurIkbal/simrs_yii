<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-17 13:22:35
 */

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
?>

<?php $form = ActiveForm::begin([
    'id' => 'form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 4,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
]) ?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $model->fasilitasrs_id == null ? Yii::t('fe', 'Tambah Fasilitas') : Yii::t('fe', 'Ubah Fasilitas') ?></h5>
</div>

<div class="modal-body">
    <?=Html::activeHiddenInput($model, 'fasilitasrs_id', ['readonly' => 'readonly'])?>

    <?= $form->field($model, 'nama_fasilitas')->widget(Select2::classname(), [
        'data' => $listJenisFasilitas,
        'options' => [
            'placeholder' => Yii::t('fe', '-- Pilih --'),
            'class' => 'select-multiple-tags'
        ],
        'pluginOptions' => [
            'tokenSeparators' => [',', ' '],
            'maximumInputLength' => 50
        ],
    ])->label(Yii::t('fe', 'Jenis Fasilitas')) ?>

    <?= $form->field($modelDetail, 'nama_fasilitas')->widget(Select2::classname(), [
        'data' => $listNamaFasilitas,
        'options' => [
            'class' => 'select-multiple-tags',
            'placeholder' => Yii::t('fe', '-- Pilih --'),
            'multiple' => true
        ],
        'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', ' '],
            'maximumInputLength' => 50
        ],
    ]) ?>
</div>

<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
    <?= Html::submitButton($model->fasilitasrs_id == null ? "<i class='fa fa-floppy-o'></i> ".Yii::t('fe', 'Simpan') : "<i class='fa fa-pencil'></i> ".Yii::t('fe', 'Ubah'), ['class' => 'btn bg-teal btn-sm']) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(".kv-plugin-loading").remove();
    });

    $(document).on("change", "#fasilitasrsform-nama_fasilitas", function() {
        var jenis_fasilitas = $(this).val();
        var list_nama_fasilitas = [];
        var value_nama_fasilitas = [];
        var splitted_jenis_fasilitas = jenis_fasilitas.split('_@_');
        var jenis_fasilitas_id = splitted_jenis_fasilitas[0];

        if (splitted_jenis_fasilitas.length > 1) {
            $.ajax({
                type: "GET",
                datatype: "json",
                url: "/master/fasilitas-rs/get-nama-fasilitas?jenis_fasilitas="+jenis_fasilitas_id,
                success: function(data) {
                    var data = JSON.parse(data);

                    if (data.length > 0) {
                        data.forEach(function(value) {
                            list_nama_fasilitas.push({id: value.ruangan_nama, text: value.ruangan_nama});
                            value_nama_fasilitas.push(value.ruangan_nama);
                        });

                        $("#fasilitasrsdetailform-nama_fasilitas").html("").select2();
                        $("#fasilitasrsdetailform-nama_fasilitas").select2({data: list_nama_fasilitas});
                        $("#fasilitasrsdetailform-nama_fasilitas").val(value_nama_fasilitas).trigger("change");
                        $(".select-multiple-tags").select2({tags: true});
                    } else {
                        $("#fasilitasrsdetailform-nama_fasilitas").html("").select2();
                        $("#fasilitasrsdetailform-nama_fasilitas").val("").trigger("change");
                        $(".select-multiple-tags").select2({tags: true});
                    }
                }
            });
        } else {
            $("#fasilitasrsdetailform-nama_fasilitas").html("").select2();
            $(".select-multiple-tags").select2({tags: true});
            $("#fasilitasrsdetailform-nama_fasilitas").val(null).trigger("change");
        }
    });

    $("#form").docoForm("submit", {
        success : function(data) {
            if (data.metadata.status == 201) {
                $("#form")[0].reset();
                $("#modal_backdrop").modal("toggle");
                $("#btn-edit").prop("disabled", true);
                $("#btn-delete").prop("disabled", true);

                table.draw();
            } else if (data.metadata.status == 200) {
                $("#modal_backdrop").modal("toggle");
                $("#btn-edit").prop("disabled", true);
                $("#btn-delete").prop("disabled", true);

                table.draw();
            }
        }
    });
</script>