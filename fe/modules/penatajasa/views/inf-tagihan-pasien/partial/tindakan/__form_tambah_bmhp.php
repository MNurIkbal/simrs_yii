<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

    use yii\web\View;
    use yii\helpers\ArrayHelper;
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use kartik\widgets\DatePicker;
    use kartik\widgets\DateTimePicker;
    use kartik\widgets\DepDrop;
?>
<style>
    .btn-custom {
        padding : 5.5px 12px!important;
    }
    .modal {
      overflow: auto;
    }

    .modal-body {
      overflow: visible;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php $form = ActiveForm::begin([
            'id' => 'bmhp-form',
            'action' => '/penatajasa/inf-tagihan-pasien/save-bmhp?no_pendaftaran='.$no_pendaftaran,
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_VERTICAL,
        ]);
    ?>
    <?= Html::hiddenInput('nilai_konversi', '', ['id' => 'nilai_konversi']); ?>
    <?= Html::hiddenInput('TindakanForm[qty_konversi]', '', ['id' => 'qty_konversi']); ?>
    <?= Html::hiddenInput('TindakanForm[harga_netto]', '', ['id' => 'harga_netto']); ?>
    <?= Html::hiddenInput('TindakanForm[ditagihkan]', 0, ['id' => 'ditagihkan_hidden']); ?>
    <?= Html::hiddenInput('TindakanForm[pendaftaran_id]', $pendaftaran_id); ?>
    <?= Html::hiddenInput('TindakanForm[no_pendaftaran]', $no_pendaftaran); ?>
    <?= Html::hiddenInput('TindakanForm[tanggal_tindakan]', $dataTindakan['tgl_tindakan']); ?>
    <?= Html::hiddenInput('TindakanForm[instalasi_id]', $dataTindakan['instalasi_id']); ?>
    <?= Html::hiddenInput('TindakanForm[kelas_pelayanan]', $dataTindakan['kelaspelayanan_id']); ?>
    <?= Html::hiddenInput('TindakanForm[dokter_pj]', $dataTindakan['dokterpenanggungjawab_id']); ?>
    <?= Html::hiddenInput('TindakanForm[daftartindakan_id]', $dataTindakan['daftartindakan_id'],['id' => 'daftartindakan_id']); ?>
    <?= Html::hiddenInput('TindakanForm[ruangan_id]', $dataTindakan['ruangan_id'],['id' => 'ruangan_id']); ?>
    <?= Html::hiddenInput('TindakanForm[tindakan_pelayanan_id]', $tindakanpelayanan_id); ?>
    <?= Html::hiddenInput('TindakanForm[pasien_id]', $dataPendaftaran['pasien_id']); ?>
    <?= Html::hiddenInput('TindakanForm[pasienadmisi_id]', $dataPendaftaran['pasienadmisi_id']); ?>
    <?= Html::hiddenInput('TindakanForm[carabayar_id]', $dataPendaftaran['carabayar_id']); ?>
    <?= Html::hiddenInput('TindakanForm[penjamin_id]', $dataPendaftaran['penjamin_id']); ?>
    <?= Html::hiddenInput('TindakanForm[status_periksa]', $status_periksa,['id' => 'status_periksa']); ?>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-5">Tanggal Tindakan</label>
                <div class="col-md-6">
                    <span style="font-weight: bold;"><?= !empty($dataTindakan['tgl_tindakan']) ? DocoHelpers::convDateTime($dataTindakan['tgl_tindakan'],false,true) : '-' ?></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-5">Instalasi</label>
                <div class="col-md-6">
                    <span style="font-weight: bold;"><?= $dataTindakan['instalasi_nama'] ?></span>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-5">Ruangan</label>
                <div class="col-md-6">
                    <span style="font-weight: bold;"><?= $dataTindakan['ruangan_nama'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-5">Kelas Pelayanan</label>
                <div class="col-md-6">
                    <span style="font-weight: bold;"><?= $dataTindakan['kelaspelayanan_nama'] ?></span>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-5">Dokter Penanggung Jawab</label>
                <div class="col-md-6">
                    <span style="font-weight: bold;"><?= $dataTindakan['nama_dokter'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-5">Jenis Pelayanan</label>
                <div class="col-md-6">
                    <span style="font-weight: bold;">BMHP</span>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label text-left control-label col-sm-5">Tindakan</label>
                <div class="col-md-6">
                    <span style="font-weight: bold;"><?= $dataTindakan['daftartindakan_nama'] ?></span>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'obatalkes_id')->dropDownList([],[
                'class' => 'select2',
                'id' => 'obatalkes_id',
                'prompt' => '— Pilih —'
                ])->label(Yii::t('fe', 'BMHP'));
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'harga_satuan', [
                'addon' => ['prepend' => ['content'=>'Rp.']],
            ])->textInput([
                    'placeholder' => Yii::t('fe', 'Tarif Satuan'),
                    'id' => 'harga_satuan',
                    'class' => 'form-control input-sm text-right',
                    'readonly' => true,
            ])->label(Yii::t('fe', 'Tarif Satuan')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'qty')->textInput([
                'placeholder' => Yii::t('fe', 'Qty'),
                'id' => 'tindakan_qty',
                'class' => 'form-control input-xs text-right doco-number',
            ])->label(Yii::t('fe', 'Qty')); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'satuan_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'satuan_id', 'class' => 'select2'],
                'data' => [],
                'pluginOptions'=>[
                    'depends'=>['obatalkes_id'],
                    'initialize' => true,
                    'loadingText' => Yii::t('fe', 'Memuat...'),
                    'placeholder'=>'--Pilih Satuan--',
                    'url' => Url::to(['/penatajasa/end-point/get-satuan']),
                ],
            ])->label(Yii::t('fe', 'Satuan')); ?>
        </div>
        <div class="col-md-6">
            <?=$form->field($model, 'total',[
                'addon' => [
                    'prepend' => [
                        'content' => Html::checkbox('ditagihkan', false, [
                            'id' => 'ditagihkan', 
                            'label' => Yii::t('fe', 'Tagihkan')
                        ])
                    ],
                ]
            ])
            ->textInput([
                'class' => 'form-control input-sm text-right', 
                'readonly' => 'readonly', 
                'id' => 'total',
                'placeholder' => Yii::t('fe', 'Sub Total'),
            ])->label(Yii::t('fe', 'Sub Total')); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'simpan-bmhp'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'data-dismiss' => 'modal',
                    'id' => 'close-bmhp'
            ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#obatalkes_id').docoPaginationSelec2({
            _api: baseUrl+"penatajasa/inf-tagihan-pasien/get-bmhp",
            placeholder: "— Pilih —",
            minimumInputLength: 0,
            ajax : {
                dataType: 'json',
                quietMillis: 250,
                data: function(params) { 
                    params.ruangan_id = $('#ruangan_id').val();
                    params.id_pendaftaran = id_pendaftaran;
                    params.instalasi_id = instalasi_id;
                    params.penjamin_id = penjamin_id;
                    params.kelaspelayanan_id = kelaspelayanan_id;
                    var query = {
                        search: params,
                    }
                    return params;
                },
                processResults: function (data) {
                    return {
                        results: data.result
                    }
                },
            },
            cache: false
    });

    $('#obatalkes_id').on('select2:select', function(e){
        var data = e.params.data;
        $("#harga_satuan").val(docoHelper.convertToRupiah(data.hargaygdipakai)).trigger('change');
        $("#harga_netto").val(data.harganetto_ygdipakai).trigger('change');
    });

    $("#tindakan_qty").on('keyup', function(){
        var _val = $(this).val();
        var obatalkes_id = $('#obatalkes_id').val();
        var satuan_id = $('#satuan_id').val();
        var nilai_konversi = $('#nilai_konversi').val();
        var harga_satuan = docoHelper.convertToAngka($('#harga_satuan').val());
        var total_harga = 0;
        var total_konversi = nilai_konversi * _val;
        var is_ditagihkan = $("#ditagihkan").is(':checked');
        if(is_ditagihkan) {
            total_harga = (_val * nilai_konversi) * harga_satuan;
        }
        $("#total").val(docoHelper.convertToRupiah(total_harga)).trigger('change');
        $("#qty_konversi").val(total_konversi);
    });

    $("#satuan_id").on('depdrop:afterChange', function(){
        var obatalkes_id = $('#obatalkes_id').val();
        var satuanbesar_id = $(this).val();
        _getNilaiKonversi(satuanbesar_id, obatalkes_id); 
    });

    var _getNilaiKonversi = function(satuanbesar_id, obatalkes_id) {
        $.ajax({
            url: baseUrl + "penatajasa/end-point/get-nilai-konversi?satuanbesar_id=" + satuanbesar_id + '&obatalkes_id=' + obatalkes_id,
            type: 'GET',
            success: function(data) {
                $('#nilai_konversi').val(data.nilai_konversi);
            }
        });
    }

    $('#simpan-bmhp').on('click', function(event){
        event.preventDefault();
        var _form = $('#bmhp-form');
        $(this).docoForm('click', {
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function (res) {
                $("#modal_backdrop").modal('toggle');
                table.draw();
                resetForm();
            }
        });
    });

    $("#ditagihkan").on('change', function(){
        if($(this).is(':checked')) {
            var qty = $("#tindakan_qty").val();
            var harga_satuan = docoHelper.convertToAngka($('#harga_satuan').val());
            var harga = docoHelper.convertToRupiah(qty * harga_satuan);
            $("#total").val(harga).trigger('change');
            $("#ditagihkan_hidden").val(1);
        }
        else {
            $("#total").val(0).trigger('change');
            $("#ditagihkan_hidden").val(0);
        }
    });

    var resetForm = function() {
        $("#obatalkes_id").val(null).trigger("change");
        $("#satuan_id").val(null).trigger("change");
        $("#tindakan_qty").val(null).trigger("change");
        $("#harga_satuan").val(null).trigger("change");
        $("#total").val(null).trigger("change");
        $("#ditagihkan").prop("checked", false).trigger("change");
    }
    })
</script>