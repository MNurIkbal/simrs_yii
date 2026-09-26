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
use app\widgets\penjamin\DHSelectNoPengajuan;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Penjamin Asuransi', 'url' => ['/penjaminasuransi']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .my-legend .legend-title {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 11px;
    }
    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 10px;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        border: 1px solid #616161;
        padding:4px 10px;
        color: #191919;
    }
    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }
    .my-legend a {
        color: #777;
    }

    .belum-koreksi {
        background-color: #ffcccc !important;
        color: #484646;
    }
    .sudah-koreksi {
        background-color: #ffcc99 !important;
        color: #484646;
    }
    .proses-klaim {
        background-color: #c2e6f8 !important;
        color: #484646;
    }
    .final-klaim {
        background-color: #b5e4b5 !important;
        color: #484646;
    }
    .sudah-kirim-online {
        background-color: #ccff66 !important;
        color: #484646;
    }
    .belum-verifikasi {
        background-color: #ffcccc !important;
        color: #484646;
    }
    .sudah-verifikasi {
        background-color: #b5e4b5 !important;
        color: #484646;
    }
</style>

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
                    'custom-search' => [
                        'title' => 'Cari',
                        'icon' => 'fa fa-search',
                        'attributes' => [
                            'data-parent' => '',
                            'data-options' => 'click',
                            'class' => 'data-filter-custom'
                        ],
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'save' => [
                        'attributes' => [
                            'onClick' => null,
                            'id' => 'simpan-penerimaan',
                            'disabled' => true
                        ]
                    ],
                    'export' => [
                        'title' => 'Unduh Template Data',
                        'attributes'=>[
                            'id' => 'export-template-data',
                            'data-options' => 'click',
                            'disabled' => true
                        ]
                    ],
                    'custom-import' => [
                        'title' => 'Unggah Template',
                        'icon'=>'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'import-template-data',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/penjamin-asuransi/transaksi-alokasi-pembayaran/upload-template',
                            'disabled' => true
                        ]
                    ],
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
                        <label>Tanggal Pengajuan :</label>
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
                        <label class="required">No. Pengajuan </label>&nbsp;:
                        <?php echo DHSelectNoPengajuan::widget([
                            'id' => 'no_pengajuan',
                            'name' => 'TransaksiAlokasiForm[no_pengajuan]'
                        ]); ?>
                    </div>
                </div>
                <hr>
                <div id="content-transaksi">
                    <h3 class="text-center">Mohon Isi Data Filter Diatas !</h3>
                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var _tabel;
    var _rawInfo = [];
    var _cacheAlokasi = {};
    var _totalPembayaran = 0;
    var _rawDetail = [];

    function callClearDetailsSession(){
        $().docoForm('click',{
            url : '/penjamin-asuransi/transaksi-alokasi-pembayaran/clear-details-session',
            method: `get`,
            skipConfirm: true,
            skipSuccessNotif: true,
            skipErrorNotif: true,
            success : function (data) {}
        });
    }

    var rangeDemoConv = new AnyTime.Converter({
        format: `%e-%b-%Y`,
        moment: moment(),
    });

    $(document).on('click','#simpan-penerimaan', function (event) {
        event.preventDefault();
        var _data = $('#form-penerimaan-pembayaran').serializeArray();
        _data.push({
            name : 'data_alokasi',
            value : JSON.stringify(_cacheAlokasi)
        });
        _data.push({
            name : 'total_alokasi',
            value : JSON.stringify(_totalPembayaran)
        });
        $().docoForm('click',{
            url : '/penjamin-asuransi/transaksi-alokasi-pembayaran/save',
            data : _data,
            success : function (data) {
                $('.data-reset').trigger('click');
            }
        });
    });

    $(document).on('click','.data-filter-custom', function (event) {
        event.preventDefault();
        _cacheAlokasi = {};
        _totalPembayaran = 0;
        $('div').removeClass('has-error');
        $('span.help-block.error').remove();
        $('div.help-block.error').remove();
        if (!$('#no_pengajuan').val()) {
            docoNotification('error','Pencarian Gagal !','No Pengajuan diisi');
            return false;
        }
        var _data = {
            no_pengajuan : $('#no_pengajuan').val(),
        }
        $(`#export-template-data`).attr(`disabled`, false);
        $(`#import-template-data`).attr(`disabled`, false);
        $(`#simpan-penerimaan`).attr(`disabled`, false);
        $('#content-transaksi').docoLoad({
            url : '/penjamin-asuransi/transaksi-alokasi-pembayaran/content-detail',
            data : _data,
            success : function (data) {
            }
        });
    });

    $(document).on('click', '.data-reset', function (event) {
        event.preventDefault();
        callClearDetailsSession();
        $(`#export-template-data`).attr(`disabled`, true);
        $(`#import-template-data`).attr(`disabled`, true);
        $(`#simpan-penerimaan`).attr(`disabled`, true);
        resetDatePicker();
        _cacheAlokasi = {};
        _totalPembayaran = 0;
        $('#no_pengajuan').val(0).trigger('change');
        var _data = {
            no_pengajuan : $('#no_pengajuan').val(),
        }
        $('#content-transaksi').docoLoad({
            url : '/penjamin-asuransi/transaksi-alokasi-pembayaran/content-detail',
            data : _data,
            success : function (data) {
            }
        });
    });

    $(document).on('click', '#example tbody tr', function (event) {
        var _data = _tabel.row(this).data();
        var _idDetail = _data.pengajuanklaimdetail_id;
        var _sisaPiutang = _data.jumlah_sisapiutang;
        var _nominal = 0;
        if ($(this).hasClass('selected')) {
            $(this).find('input').prop('disabled', false);
            $(this).find('input').val(_sisaPiutang).trigger('keyup');
            return true;
        }
        $(this).find('input').prop('disabled', true);
        $(this).find('input').val(0).trigger('keyup');
    });

    function resetDatePicker() {
        const nowValue = rangeDemoConv.format(new Date());
        $(`.startDate`).val(nowValue);
        $(`.endDate`).val(nowValue);
        setDateToDataNoPengajuan();
    }

    function setDateToDataNoPengajuan(){
        const startVal = $(`.startDate`).val();
        const endVal = $(`.endDate`).val();
        $(`#no_pengajuan`).attr(`data-start`, startVal);
        $(`#no_pengajuan`).attr(`data-end`, endVal);
    }

    $(`.startDate`).on(`change`, function(){
        setDateToDataNoPengajuan();
    })

    $(`.endDate`).on(`change`, function(){
        setDateToDataNoPengajuan();
    })

    $(document).ready(function() {
        dateRangeHelper('.startDate','.endDate','.targetDate');
        setDateToDataNoPengajuan();
        $('#no_pengajuan_backup').select2({
            placeholder: '-- Pilih --',
            minimumInputLength: 3,
            ajax: {
                url: '/penjamin-asuransi/transaksi-alokasi-pembayaran/get-no-pengajuan',
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
        });
    });

    $(document).on(`click`, `#export-template-data`, function(e){
        e.preventDefault();
        // const params = $.param(table.ajax.params())
        const pengajuanKlaimId = $('#no_pengajuan').val();
        if (!pengajuanKlaimId) {
            docoNotification('error','Export Gagal !','No Pengajuan diisi');
            return false;
        }
        let params = `?pengajuanklaim_id=` + pengajuanKlaimId;
        window.open(baseUrl + `penjamin-asuransi/transaksi-alokasi-pembayaran/download-template` + params);
        return false;
    });
", View::POS_END, 'js');

?>