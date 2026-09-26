<?php

    use yii\web\View;
    use yii\helpers\ArrayHelper;
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use kartik\widgets\DatePicker;
    use kartik\widgets\DepDrop;


    $model->instalasi_id = $instalasi_terakhir;
    $model->ruangan_id = $ruangan_terakhir;
?>
<style>
    .datepicker>div{
        display:block;
    }
    .btn-custom {
        padding : 5.5px 12px!important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
            'id' => 'add-pelayanan-form',
            'action' => '/kasir/transaksi-pelayanan-pasien/add-pelayanan?id='.$id,
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
    ?>
    <?php
        $model->tanggal_tindakan = date('d-M-Y');
    ?>
        <?= $form->field($model, 'tanggal_tindakan', [
        'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-5'
            ]
        ])->widget(DatePicker::classname(), [
            'value' => date('Y-m-d'),
            'readonly' => true,
            'language' => 'en',
            'pluginOptions' => [
                'startDate' => date('d-m-Y',strtotime($tglPendaftaran)),
                'endDate' => '0d',
                'autoclose' => true,
                'format' => 'dd-M-yyyy',
            ]
        ]); ?>

        <?= $form->field($model, 'instalasi_id',[
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->dropDownList($instalasi,[
                        'class' => 'select2',
                        'id' => 'instalasi_id',
                        'prompt' => '-- Pilih --'
                ]);
        ?>

        <?= $form->field($model, 'ruangan_id',[
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->widget(DepDrop::classname(), [
                    'options' => [
                        'id'=>'ruangan_id',
                        'class' => 'form-control select2'
                    ],
                    'pluginOptions'=>[
                        'depends' => ['instalasi_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih Ruangan --'),
                        'initialize' => true,
                        'url'=> Url::to(['/kasir/transaksi-pelayanan-pasien/get-ruangan?selected='.$ruangan_terakhir]),
                        'prompt' => Yii::t('fe', '-- Pilih Ruangan --'),
                    ],
                ]);
        ?>

        <?= $form->field($model, 'kelas_pelayanan',[
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->dropDownList($kelasPelayanan,[
                        'class' => 'select2',
                        'id' => 'kelas_pelayanan',
                        'prompt' => '-- Pilih --'
                ]);
        ?>

        <?= $form->field($model, 'dokter_pj',[
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->dropDownList([],[
                        'class' => 'select2',
                        'id' => 'dokter_pj',
                        'prompt' => '-- Pilih --'
                ]);
        ?>


        <?= $form->field($model, 'jenis_pelayanan',[
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->dropDownList([
                    'tindakan' => 'Tindakan',
                    'obat' => 'Obat Alkes',
                    'paket' => 'Paket'
                ],[
                    'class' => 'select2',
                    'id' => 'jenis_pelayanan',
                    'prompt' => '-- Pilih --'
                ]);
        ?>



        <?= $form->field($model, 'tindakan_pelayanan_id',[
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->dropDownList([],[
                        'class' => 'select2',
                        'id' => 'tindakan_pelayanan_id',
                        'prompt' => '-- Pilih --'
                ]);
        ?>
        <div class="form-group field-is_cyto">
            <label class="control-label text-left control-label col-sm-4" for="is_cyto">Cyto</label>
                <div class="col-md-5">
                        <input type="checkbox" id="is_cyto" name="PelayananKasirForm[is_cyto]" class="styled"
                        value="1" aria-invalid="false">
                </div>
        </div>
        <?= $form->field($model, 'qty', [
        'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-4'
            ]
        ])->textInput([
            'placeholder' => Yii::t('fe', 'Qty'),
            'class' => 'form-control input-sm text-right doco-number',
        ]); ?>
    <hr>
    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'simpan-tindakan'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'data-dismiss' => 'modal'
            ]); ?>
        <?= Html::button("<b><i class='fa fa-refresh'></i></b>&nbsp;Bersihkan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'bersihkan-tindakan'
            ]) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    var _id = '<?= $id ?>';
    var _defaultDate = '<?= $model->tanggal_tindakan ?>';
    $(document).ready(function () {
        $('div.field-tindakan_pelayanan_id, div.field-pelayanankasirform-qty, div.field-is_cyto').hide();
        $(".styled, .multiselect-container input").uniform({
            radioClass: 'choice'
        });
        /** Untuk Tindakan  filter berdasarkan jenis pelayanan, ruangan dan kelas pelayanan**/
        $('#tindakan_pelayanan_id').select2({
            placeholder: "-- Pilih --",
            minimumInputLength: 3,
            ajax : {
                url: baseUrl+"kasir/transaksi-pelayanan-pasien/get-tindakan",
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  params.jenis_pelayanan = $('#jenis_pelayanan').val();
                  params.ruangan_id = $('#ruangan_id').val();
                  params.kelas_pelayanan = $('#kelas_pelayanan').val();
                  params.id = _id;
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) {
                    return m;
                },
            },
            cache: true
        });

        /** Untuk Tindakan  filter berdasarkan jenis pelayanan, ruangan dan kelas pelayanan **/
        $('#dokter_pj').select2({
            placeholder: "-- Pilih --",
            minimumInputLength: 3,
            ajax : {
                url: baseUrl+"kasir/transaksi-pelayanan-pasien/get-dokter",
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) {
                    return m;
                },
            },
            cache: true
        });
    });

    $('#jenis_pelayanan, #ruangan_id, #kelas_pelayanan').on('change', function (event) {
        event.preventDefault();
        var _target = $('div.field-tindakan_pelayanan_id, div.field-pelayanankasirform-qty, div.field-is_cyto');
        $('#tindakan_pelayanan_id').val('').trigger('change');
        $('#pelayanankasirform-qty').val('').trigger('change');
        _target.find('div').removeClass('has-error');
        _target.removeClass('has-error');
        _target.find('span.help-block.error').remove();
        _target.find('div.help-block.error').remove();
        if ($('#jenis_pelayanan').val() && $('#ruangan_id').val() && $('#kelas_pelayanan').val()) {
            _target.show();
        } else {
            _target.hide();
        }

    })

    $('#simpan-tindakan').on('click', function (event) {
        event.preventDefault();
        var _form = $('#add-pelayanan-form');
        var _jenisPelayanan = $('#jenis_pelayanan').val();
        var _palayanan = $('#tindakan_pelayanan_id').val();

        $().docoForm('click',{
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function (data) {
                var _rest = data.response.data;
                var _dataInsert = data.response.data_insert;
                var _isObat = _rest.is_obat;
                var _paket = _rest.is_paket;
                var _idTindakan = _rest.tindakan_obat_id;
                var _randStr = _idTindakan + Math.random().toString(36).substring(7);
                if (_isObat) {
                    _listTindakan.obat[_randStr] = _dataInsert;
                } else {
                    if (_paket) {
                        _listTindakan.paket[_randStr] = _dataInsert;
                    } else {
                        _listTindakan.tindakan[_randStr] = _dataInsert;
                    }
                }
                var _html ='<button type="button" data-type="'+ _jenisPelayanan +'" data-id="'+ _randStr +'" class="deleteRow btn btn-danger btn-custom">';
                        _html += '<span class="fa fa-trash"></span>';
                    _html += '</button>';

                _jsonData.push({
                    no : 0,
                    penjamin_pelayanan_id : _rest.penjamin_pelayanan_id,
                    carabayar_pelayanan_id : _rest.carabayar_pelayanan_id,
                    tindakan_obat_id : _idTindakan,
                    tgl_pelayanan : _rest.tgl_pelayanan,
                    instalasi_pelayanan : $('#instalasi_id').find(":selected").text(),
                    ruangan_pelayanan : $('#ruangan_id').find(":selected").text(),
                    tindakan_obat_nama : $('#tindakan_pelayanan_id').find(":selected").text(),
                    carabayar_pelayanan : _rest.carabayar_pelayanan,
                    penjamin_pelayanan : _rest.penjamin_pelayanan,
                    action : _html,
                    pelayanan_id : null,
                    tarif_satuan_label : _rest.tarif_satuan_label,
                    qty_label : _rest.qty_label,
                    tarif_cyto_label : _rest.tarif_cyto_label,
                    sub_total_label : _rest.sub_total_label,
                    tarif_satuan : _rest.tarif_satuan,
                    qty : _rest.qty,
                    tarif_cyto : _rest.tarif_cyto,
                    sub_total : _rest.sub_total,
                    is_flag : true
                });
                _resetTabel();
                _tagihanPasien();
                // docoResetForm(_form);

                $("#tindakan_pelayanan_id").val("").trigger('change');
                $("#pelayanankasirform-qty").val(0);

                // $('#is_cyto').prop("checked",false);
                $('#is_cyto').parent().removeClass('checked');
                $('#pelayanankasirform-tanggal_tindakan').val(_defaultDate);
                $("#tindakan_pelayanan_id").focus();
            }
        });
    })

    $("#bersihkan-tindakan").click(function(e){
        docoResetForm($('#add-pelayanan-form'));
        $('#is_cyto').prop("checked",false);
        $('#pelayanankasirform-tanggal_tindakan').val(_defaultDate);
    });

    $(document).on('shown.bs.modal', null, function(){
        $('#kelas_pelayanan').focus();
    });

    $('select').on(
        'select2:close',
        function () {
            $(this).focus();
        }
    );
</script>