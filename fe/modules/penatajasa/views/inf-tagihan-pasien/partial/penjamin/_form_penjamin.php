<?php 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\date\DatePicker;

use app\components\DocoHelpers;

?>
<div class="modal-header bg-inverse">
    <h4 class="modal-title"><?= $title ?></h4>
</div>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'penjamin-form', 
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]); 
    ?>
    <?=Html::activeHiddenInput($model, 'asuransipasien_id')?>
    <?=Html::activeHiddenInput($model, 'pasien_id')?>
    <?=Html::activeHiddenInput($model, 'carabayar_nama')?>
    <?=Html::activeHiddenInput($model, 'penjamin_nama')?>
    <?=Html::activeHiddenInput($model, 'nama_pasien')?>
    <?=$form->field($model, 'carabayar_id')->dropdownList($carabayar,
        [
            'id' => 'carabayar-dropdown',
            'class' => 'select2',
            'prompt' => 'Pilih Cara Bayar'
        ])?>
    <?=$form->field($model, 'penjamin_id')->widget(DepDrop::classname(), [
        'options' => [
            'class' => 'select2',
            'prompt' => 'Pilih Penjamin'
        ],
        'pluginOptions'=>[
            'depends'=>['carabayar-dropdown'],
            'placeholder'=>'Pilih Penjamin',
            'url'=>Url::to(['/penatajasa/end-point/get-penjamin','selected' => $penjamin_id])
        ],
        'pluginEvents' => [
            'depdrop:change' => "function(even){ 
                $('#penjaminform-penjamin_nama').val( $('#penjaminform-penjamin_id :selected').text() );
                $('#penjaminform-carabayar_nama').val( $('#carabayar-dropdown :selected').text() );
                $('#penjaminform-nama_pasien').val( nama_pasien );
                $('#btn-cari-asuransi').click();
            }",
        ]
    ])?>
    <?=$form->field($model, 'nokartuasuransi', [
        'addon' => [
            'append' => [
                'content' => '<button type="button" class="btn btn-info" id="btn-cari-asuransi" title="Check.." data-popup="tooltip"><b><i class="fa fa-search"></i></b></button>',
                'asButton' => true
            ] 
        ]
    ])?>
    <?=$form->field($model, 'nama_pemilik')->textInput(['readonly' => true])?>
    <?=$form->field($model, 'nominal_dijamin')->textInput(['class'=> 'doco-number'])?>
    <div id="asuransi-baru" class="hidden">
        <hr>
        <?=$form->field($model, 'namapemilikasuransi')?>
        <?=$form->field($model, 'nomorpokokperusahaan')?>
        <?=$form->field($model, 'kelastanggunganasuransi_id')->dropdownList($kelaspelayanan,[
            'class' => 'select2',
            'prompt' => 'Pilih Kelas Tanggungan'
        ])?>
        <?=$form->field($model, 'namaperusahaan')?>
        <?=$form->field($model, 'tgl_konfirmasi')->textInput()?>
        <?=$form->field($model, 'status_konfirmasi')->checkbox()?>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-info btn-labeled btn-xs" id="btn-simpan-penjamin"><b><i class="fa fa-save fa-xs"></i></b> Simpan</button>
    <button type="button" class="btn btn-info btn-labeled btn-xs" data-dismiss="modal"><b><i class="fa fa-close fa-xs"></i></b> Batal</button>
    <?php ActiveForm::end() ?>
</div>
<?php
$this->registerJs("
    $('#carabayar-dropdown').trigger('depdrop:change');
    $('#penjaminform-nokartuasuransi').on('keyup', function(e){
        var code = e.which; // recommended to use e.which, it's normalized across browsers
        if(code == 13){
            $('#btn-cari-asuransi').click();
        }
    });
    $('#penjaminform-nominal_dijamin').on('keyup', function(e){
        var _max = _getMax();
        if( docoHelper.convertToAngka($(this).val()) > _max ){
            $(this).val(docoHelper.convertToRupiah(_max));
        }
    });
    $('#penjaminform-penjamin_id').on('change', function(){
        var _text = $('#penjaminform-penjamin_id :selected').text();
        $('#penjaminform-penjamin_nama').val( $('#penjaminform-penjamin_id :selected').text() );
        $('#btn-cari-asuransi').click();
    })
    $('#penjaminform-tgl_konfirmasi').pickadate({
        format: 'd-mmmm-yyyy',
        formatSubmit: 'yyyy-mm-d',
        max: '".date('Y-m-d')."'
    });
    $('#btn-simpan-penjamin').on('click', function(e){
        e.preventDefault();
        _form = $('#penjamin-form');
        $(this).docoForm('click', {
            skipConfirm: true,
            skipSuccessNotif: true,
            data: _form.serializeArray(),
            url: _form.attr('action'),
            success: function(res){
                $('#penjaminform-nokartuasuransi').val(null);
                $('#penjaminform-nominal_dijamin').val(null);
                $('#carabayar-dropdown').val(null).trigger('change'); 
                $('#carabayar-dropdown').val(null).trigger('depdrop:change');

                $('#penjaminform-nama_pemilik').val(null);
                $('#penjaminform-asuransipasien_id').val(null);
                $('#penjaminform-namapemilikasuransi').val(null);
                $('#penjaminform-nomorpokokperusahaan').val(null);
                $('#penjaminform-namaperusahaan').val(null);
                $('#penjaminform-tgl_konfirmasi').val(null);
                $('#penjaminform-status_konfirmasi').prop('checked', false);
                data_audit(pendaftaranID);
                tblPenjamin.ajax.url('get-list-penjamin?pendaftaran_id='+ pendaftaran_id +'&get_session=true').load();
            }
        });
    });
    $('#btn-cari-asuransi').on('click', function(e){
        e.preventDefault();
        if( $('#penjaminform-nokartuasuransi').val() == '' || $('#penjaminform-penjamin_id').val() == ''){
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '/penatajasa/inf-tagihan-pasien/cari-no-asuransi',
            data: {
                no_asuransi: $('#penjaminform-nokartuasuransi').val(),pasien_id: $('#penjaminform-pasien_id').val(),penjamin_id: $('#penjaminform-penjamin_id').val()
            },
            beforeSend: function(){
                $('#btn-cari-asuransi').prop('disabled', true).html('<b><i class=\'fa fa-gear fa-spin\'></i></b>');
                $('#btn-simpan-penjamin').prop('disabled', true);
            },
            success: function(res){
                $('#btn-simpan-penjamin').prop('disabled', false);
                $('#btn-cari-asuransi').prop('disabled', false).html('<b><i class=\'fa fa-search\'></i></b>');

                $('#penjaminform-nama_pemilik').val(null);
                $('#penjaminform-asuransipasien_id').val(null);
                $('#penjaminform-namapemilikasuransi').val(null);
                $('#penjaminform-nomorpokokperusahaan').val(null);
                $('#penjaminform-namaperusahaan').val(null);
                $('#penjaminform-tgl_konfirmasi').val(null);
                $('#penjaminform-status_konfirmasi').prop('checked', false);

                if(res.asuransipasien_id == null){
                    $('#asuransi-baru').removeClass('hidden');
                    docoNotification('warning', 'Data Tidak Ditemukan','Silahkan isi asuransi secara manual');
                    return false;
                }
                $('#asuransi-baru').addClass('hidden');
                $('#penjaminform-nama_pemilik').val(res.namapemilikasuransi);
                $('#penjaminform-asuransipasien_id').val(res.asuransipasien_id);
            },
            error: function(err){
                $('#btn-simpan-penjamin').prop('disabled', false);
                $('#btn-cari-asuransi').prop('disabled', false).html('<b><i class=\'fa fa-search\'></i></b>');
                docoNotification('error', 'Terjadi Kesalahan', err.message);
                return false;
            }
        })
    });
    var _getMax = function(){
        var _totalTagihan = docoHelper.convertToAngka($('#tagihan_pasien').val());
            _totalDibayar = docoHelper.convertToAngka($('#total_tagihan').val());
            totalPenjamin = 0;
        tblPenjamin.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var val = this.data();
            if(typeof val.pendaftaranpenjamin_id === 'undefined'){
                totalPenjamin += parseInt(docoHelper.convertToAngka(val.nominal_dijamin));
            }else{
                if(val.is_deleted === false && val.penjamin_id != '1'){
                    totalPenjamin += parseInt(docoHelper.convertToAngka(val.nominal_dijamin));
                }
            }
        } );
        if(totalPenjamin == 0){
            return _totalDibayar;
        }
        return _totalTagihan;
    }
")

?>