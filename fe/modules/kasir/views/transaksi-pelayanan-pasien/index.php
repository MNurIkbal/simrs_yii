<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\DateTimePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['/penjaminasuransi']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::t('fe', $title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'style' => 'display:none;',
                            'class' => 'btn btn-info btn-labeled btn-xs data-cari'
                        ]
                    ],
                    'save' => [
                        'attributes' => [
                            'onClick' => null,
                            'id' => 'simpan-penerimaan',
                            'disabled' => true
                        ]
                    ],
                    'print-rincian' => [
                        'type' => 'button',
                        'title' => 'Print Rincian',
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'class' => 'data-lihat print-tagihan',
                            'id' => 'print-tagihan',
                            'method' => 'json',
                            'data-options' => 'link',
                            'disabled' => true
                        ]
                    ],
                    'custom-print-kwitansi' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Print Kwitansi'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'class' => 'print-kwitansi print-tagihan',
                            'id' => 'print-kwitansi',
                            'method' => 'json',
                            'data-options' => 'link',
                            'disabled' => true
                        ],
                    ],
                    'custom-print' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Print BKM'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'class' => 'print-bkm print-tagihan',
                            'method' => 'json',
                            'data-options' => 'link',
                            'disabled' => true
                        ],
                    ],
                    // 'reset' => [
                    //     'attributes' => [
                    //         // 'data-parent' => '.filter-form'
                    //     ]
                    // ],
                ]);?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-penerimaan-pembayaran',
                        'formConfig' => [
                            'labelSpan' => 4,
                            'deviceSize' => ActiveForm::SIZE_MEDIUM
                        ]
                    ]);
                    ?>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Tanggal Pendaftaran :</label>
                        <div class="input-group">
                            <input type="text" id="rangeDemoStart"
                                class="form-control startDate"
                                value="<?= date('d-M-Y') ?>"
                                readonly="">
                            <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                            <input type="text" id="rangeDemoFinish"
                                   readonly=""
                                   class="form-control endDate"
                                   value="<?= date('d-M-Y') ?>">
                            <input type="text" style="display:none" class="targetDate">
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="required">No Pendaftaran </label>&nbsp;:
                        <?= Html::activeDropdownList($model, 'no_pendaftaran', [], [
                                'class' => 'form-control',
                                'id' => 'no_pendaftaran',
                                'prompt' => Yii::t('fe', 'Pilih')]
                            ) ?>
                    </div>
                </div>
                <hr>
                <div id="content-transaksi"></div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $(document).on('click','.data-cari', function (event) {
        event.preventDefault();
        $('div').removeClass('has-error');
        $('span.help-block.error').remove();
        $('div.help-block.error').remove();
        if (!$('#no_pendaftaran').val()) {
            // docoNotification('error','Pencarian Gagal !','No Pendaftaran diisi');
            // return false;
        }
        var _data = {
            id_pendaftaran : $('#no_pendaftaran').val(),
        }
        $('#content-transaksi').docoLoad({
            url : '/kasir/transaksi-pelayanan-pasien/get-detail-transaksi',
            data : _data,
            success : function (data) {

            }
        });
    });

    $(document).on('click', '.data-reset', function (event) {
        event.preventDefault();
        $('#no_pendaftaran').val(0).trigger('change');
        var _data = {
            id_pendaftaran : $('#no_pendaftaran').val(),
        }
        $('#content-transaksi').docoLoad({
            url : '/kasir/transaksi-pelayanan-pasien/get-detail-transaksi',
            data : _data,
            success : function (data) {
                $('.data-save').prop('disabled',true);
                $('.print-tagihan').prop('disabled',true);
                $('#form-penerimaan-pembayaran').find('input').prop('disabled',false);
            }
        });
    });

    $('#simpan-penerimaan').on('click', function (e) {
        e.preventDefault();
        if (!$('#no_pendaftaran').val()) {
            docoNotification('error','Pencarian Gagal !','No Pendaftaran diisi');
            return false;
        }
        var _pembulatan = $('#pembulatan');
        _pembulatan.val(docoHelper.convertToRupiah(_pembulatan.val()));
        var _form = $('#form-penerimaan-pembayaran').serializeArray();
        var _id = $('#no_pendaftaran').val();
        _form.push({
            name : 'add_tindakan',
            value : JSON.stringify(_listTindakan)
        });
        _form.push({name:'ruangan_pelakhir_id',value:_info.ruangan_id});
        _form.push({name:'instalasi_id',value:_info.instalasi_id});
        _form.push({name:'pendaftaran_id',value:_info.pendaftaran_id});
        _form.push({name:'pasienadmisi_id',value:_info.pasienadmisi_id});
        _form.push({name:'pasien_id',value:_info.pasien_id});
        _form.push({name:'nama_pasien',value:_info.nama_pasien});
        _form.push({name:'status_pasien',value:_info.status_pasien});
        _form.push({name:'uang_muka',value: (isNaN(_jumUM) ? 0 : _jumUM)});
        $().docoForm('click',{
            url : '/kasir/transaksi-pelayanan-pasien/simpan?id=' + _id,
            data : _form,
            success : function (data) {
                $('.row-default').remove();
                $('.data-save').prop('disabled',true);
                $('.print-tagihan').prop('disabled',false);
                $('#form-penerimaan-pembayaran').find('input').prop('disabled',true);
                $.each($('.deleteRow'),function() {
                    var _td = $(this).closest('td');
                    _td.html('<i class=\"fa fa-lock\"></i>')
                })
            }
        });
    });
    $(document).on('click', '.print-bkm', function() {
        var _id = $('#no_pendaftaran').val();
        window.open('/kasir/pembayaran-tagihan/export-bkm?id='+_id);
    });

    $(document).on('click', '.print-kwitansi', function() {
        var _id = $('#no_pendaftaran').val();
        window.open('/kasir/pembayaran-tagihan/export-kwitansi?id='+_id);
    });


    $(document).on('click','#print-tagihan', function (event) {
        event.preventDefault();
        var _id = $('#no_pendaftaran').val();
        window.open('/kasir/pembayaran-tagihan/export-rincian?id='+_id);
    });



    $(document).on('keydown', null, 'alt+s', function(){
        $('textarea').blur();
        $('input').each(function(){
            $(this).blur();
        });
        $('#simpan-penerimaan').click();
    });

    $(document).on('keydown', null, 'alt+t', function(){
        $('.addrow').click();
    });

    $(document).ready(function() {
        dateRangeHelper('.startDate','.endDate','.targetDate');
        $('#no_pendaftaran').select2({
            placeholder: '-- Pilih --',
            minimumInputLength: 3,
            ajax: {
                url: '/kasir/transaksi-pelayanan-pasien/get-no-pendaftaran',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    term.start = $('.startDate').val();
                    term.end = $('.endDate').val();
                    return {
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        }).on('change', function (e) {
            e.preventDefault();
            $('.data-cari').trigger('click');
            $('.data-save').prop('disabled',false);
            $('.print-tagihan').prop('disabled',true);
        });
    });
", View::POS_END, 'js');

?>