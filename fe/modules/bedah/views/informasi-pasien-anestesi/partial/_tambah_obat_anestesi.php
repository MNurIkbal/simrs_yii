<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use yii\widgets\MaskedInput;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body" id="parent-drop">
    <?php
    $form = ActiveForm::begin([
        'id' => 'tambah-obat-anestesi-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'formConfig' => [
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>

    <?= $form->field($model, 'obatalkes_id')->dropDownList([],
        [
            'class' => 'select2',
            'id' => 'obatalkes_id',
            'prompt' => Yii::t('fe', '-- Pilih --'),
        ]);
    ?>
    <?= $form->field($model, 'dose')->textInput() ?>
    <?= $form->field($model, 'time_delivery')->textInput(['class' => 'timepicker'])
    ?>

    <div class="modal-footer">
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save btn-save-tambah-obat']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#obatalkes_id').docoPaginationSelec2(
            config = {
                placeholder : 'Pilih ... ',
                _api : '/apotek/transaksi-resep/list-obat-alkes-depo',
                dropdownParent: $('#parent-drop'),
                ajax: {
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            ruangan_id: 29, //Sementara hardcode
                        }
                    },
                    results: function (data, params) {
                        var more = (params.page * 30) < data.total_count;
                        return { results: data.items, more: more };
                    },
                    processResults: function(res, params) {
                        params.page = params.page || 1;
                        var arr = [];
                        $.each(res.data_stok, function(index, value) {
                            if (index < 10) {
                                var _disabled = value.qty_tersedia <= 0 ? true : false;
                                arr.push({
                                    id: value.obatalkes_id,
                                    text: value.obatalkes_nama,
                                    disabled: _disabled
                                })

                                let data = [];
                                let response = res.data_stok;
                                for (var i in response) {
                                    data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
                                }
                            }
                        });
                        return {
                            results: arr,
                            pagination: {
                                more: res.data_stok.length > 10
                            }
                        };
                    }
                },
            }
        );

        $(".timepicker:not([readonly])").timepicker({
            showMeridian: false,
            minuteStep: 1
        });
    });

    $(".btn-save-tambah-obat").on("click", function () {
        var obatalkes_id = $('#obatalkes_id').val();
        var obatalkes_nama = $('#obatalkes_id option:selected').text(); 
        var dose = $('#anestesidetailform-dose').val();
        var time_delivery = $('#anestesidetailform-time_delivery').val();
        var anestesi_id = $('#anestesiform-anestesi_id').val();
        var index = premedicationList.length;

        if (obatalkes_id != null && obatalkes_id != ''){
            premedicationList[index] = {
                anestesi_id: anestesi_id,
                obatalkes_id: obatalkes_id,
                obatalkes_nama: obatalkes_nama,
                dose: dose,
                time_delivery: time_delivery,
            }
            isUpdatePremed = 1;

            loadPremedication(premedicationList);
        }
        
        $("#modal_backdrop").modal("toggle");
    });
</script>
