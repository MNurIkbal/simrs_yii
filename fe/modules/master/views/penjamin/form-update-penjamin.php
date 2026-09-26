<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'ajax-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => ['enctype' => 'multipart/form-data'],
    'formConfig' => ['showErrors' => true, 'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <?= $form
        ->field($model, 'carabayar_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList([], [
            'class' => 'form-control input-sm select2 selectCaraBayar',
            'prompt' => 'Pilih Cara Bayar'
        ]);
    ?>
    <?= $form->field($model, 'penjamin_kode', ['labelOptions' => ['class' => 'text-left required']])->textInput(['placeholder' => $model->getAttributeLabel('penjamin_kode'), 'class' => 'form-control input-sm']); ?>
    <?= $form->field($model, 'penjamin_nama', ['labelOptions' => ['class' => 'text-left required']])->textInput(['placeholder' => $model->getAttributeLabel('penjamin_nama'), 'class' => 'form-control input-sm']); ?>
    <?= $form->field($model, 'penjamin_namalainnya', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('penjamin_namalainnya'), 'class' => 'form-control input-sm']); ?>
    <?=
    $form->field($model, 'groupmargin_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($groupMargin, [
            'class' => 'form-control input-sm select2',
            'prompt' => '--Pilih Group Margin--'
        ]);
    ?>
    <?= $form->field($model, 'is_active')
        ->radioList(
            [
                1 => Yii::t('fe', 'Aktif'),
                0 => Yii::t('fe', 'Tidak Aktif'),
            ],
            ['id' => 'is_active', 'inline' => true, 'value' => $model->is_active]
        );
    ?>
    <?=
    $form->field($model, 'konfigasuransi_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($konfigAsuransi, [
            'class' => 'form-control input-sm select2 selectProvider',
            'prompt' => '--Pilih --',
            'disabled' => $disabled,
        ])->label(Yii::t('fe', 'Integrasi Asuransi'));
    ?>
    <div class="modal-footer">
        <button type="submit" id="btn-simpan" class="btn btn-info btn-labeled btn-xs data-save" data-target="ajax-form" onclick=""><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
        <button type="button" class="btn btn-info btn-labeled btn-xs data-back" data-dismiss="modal"><b><i class="fa fa-arrow-left"></i></b>Kembali</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        const groupJaminan = <?= $groupJaminan ?>;
        const defaultOptionsCaraBayar = <?= $defaultOptionsCaraBayar ?>;
        const defaultGroupCaraBayarId = <?= $groupCaraBayarId ?>;
        const konfigAsuransiId = "<?= $konfigAsuransiId ?>";

        $(".selectCaraBayar").select2InfinityScroll({
            url: "/master/penjamin/filters",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    },
                };
            },
        });

        setTimeout(() => {
            if (konfigAsuransiId) {
                $(".selectProvider").val(konfigAsuransiId).trigger('change');
            }
        }, 200)

        $(document).on('change', '.selectCaraBayar', function() {
            const caraBayar = $(this).select2('data');
            $(".selectProvider").val(null).trigger('change');
            if (caraBayar[0].groupcarabayar_id) {
                const groupCaraBayarId = caraBayar[0].groupcarabayar_id
                if (groupCaraBayarId == groupJaminan) {
                    $(".selectProvider").prop('disabled', false);
                } else {
                    $(".selectProvider").prop('disabled', true);
                }
            } else {
                if (defaultGroupCaraBayarId == groupJaminan) {
                    $(".selectProvider").prop('disabled', false);
                } else {
                    $(".selectProvider").prop('disabled', true);
                }
            }
        })

        if (defaultOptionsCaraBayar) {
            var newOption = new Option(
                defaultOptionsCaraBayar.text,
                defaultOptionsCaraBayar.id,
                true,
                true
            );
            $(".selectCaraBayar").append(newOption).trigger('change');
        }
    })

    $("#ajax-form").docoForm("submit", {
        success: function(data) {
            var form = $("#ajax-form");
            form[0].reset();
            $("#table-carabayar").DataTable().clear().draw();
            $("#modal_backdrop").modal('toggle');
        },
        error: function(data) {
            // $(this).find('.error').hide();
            // $(document).ready(function () {
            //     $("div.help-block").remove();
            //     console.log($(this).val() );
            // });
        }
    });

    $(document).on('keydown', null, 'alt+s', function(event) {
        $("#btn-simpan").click();
    });

    $(document).on('keydown', null, 'alt+S', function(event) {
        $("#btn-simpan").click();
    });
</script>