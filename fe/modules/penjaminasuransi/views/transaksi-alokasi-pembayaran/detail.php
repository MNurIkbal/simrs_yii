<?php

/**
 * @author Yaya
 * @copyright 23 March 2018
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;
use kartik\datetime\DateTimePicker;

?>

<style type="text/css">
td {height: 50px}

.checker-inverse span {
    color: #fff;
    border: 2px solid #fff;
    margin: 3px 5px;
}
.kv-datetime-picker{
        display : none;
}
.kv-datetime-remove{
    border-right: 1px solid #ddd !important;
}
</style>

<div class="col-md-12 info-pengajuan">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pengajuan'); ?></b></h6>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-3">
                    <label class="text-right control-label col-sm-5"><b>Cara Bayar :</b></label>
                    <div class="col-sm-7">
                        <?= isset($header['carabayar_nama']) ? $header['carabayar_nama'] : null ?>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label class="text-right control-label col-sm-5"><b>Penjamin :</b></label>
                    <div class="col-sm-7">
                        <?= isset($header['penjamin_nama']) ? $header['penjamin_nama'] : null ?>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label class="text-right control-label col-sm-5"><b>Instalasi :</b></label>
                    <div class="col-sm-7">
                        <?= isset($header['instalasi_nama']) ? $header['instalasi_nama'] : null ?>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label class="text-right control-label col-sm-5"><b>Ruangan :</b></label>
                    <div class="col-sm-7">
                        <?= isset($header['ruangan_nama']) ? $header['ruangan_nama'] : null ?>
                    </div>
                </div>
            </div>
            <hr>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="control-label col-md-2">
                            <b><?=$model->attributeLabels()['tanggal_pembayaran']?></b>
                        </label>
                        <div class="col-sm-3">
                            <?=DateTimePicker::widget([
                                    'model' => $model,
                                    'attribute' => 'tanggal_pembayaran',
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND ,
                                    'readonly' => true,
                                    'name' => "TransaksiAlokasiForm[tanggal_pembayaran]",
                                    'id' => 'transaksialokasiform-tanggal_pembayaran',
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'disabled' => true,
                                        'format' => 'yyyy-MM-dd hh:mm:ss',
                                        'timePicker24Hour'=>true,
                                        'startDate' => date('Y-m-d H:i:s',strtotime('-2 minute')),
                                        'autoclose' => true,
                                        'minuteStep' => 1,
                                        'endDate' => date('Y-m-d H:i:s')
                                    ]
                                ]);
                            ?>
                            <div class="help-block"></div>
                        </div>
                        <label class="control-label col-md-2">
                            <b><?=$model->attributeLabels()['total_pengajuan']?></b>
                        </label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon" title="Select date &amp; time">
                                    <span>Rp.</span>
                                </span>
                                <?=
                                    Html::activeTextInput($model, 'total_pengajuan',[
                                        'class' => 'form-control doco-number text-right',
                                        'readonly' => true
                                    ])
                                ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label col-md-2 required">
                            <b><?=$model->attributeLabels()['no_pembayaran']?></b>
                        </label>
                        <div class="col-sm-3">
                            <?= Html::activeDropdownList($model, 'no_pembayaran', [], [
                                    'class' => 'form-control no_pembayaran',
                                    'prompt' => Yii::t('fe', 'Pilih')]
                                ) ?>
                            <div class="help-block"></div>
                        </div>
                        <label class="control-label col-md-2">
                            <b><?=$model->attributeLabels()['total_terbayar']?></b>
                        </label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon" title="Select date &amp; time">
                                    <span>Rp.</span>
                                </span>
                                <?=
                                    Html::activeTextInput($model, 'total_terbayar',[
                                        'class' => 'form-control doco-number text-right',
                                        'readonly' => true
                                    ])
                                ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label col-md-2 required">
                            <b><?=$model->attributeLabels()['jumlah_pembayaran']?></b>
                        </label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon" title="Select date &amp; time">
                                    <span>Rp.</span>
                                </span>
                                <?=
                                    Html::activeTextInput($model, 'jumlah_pembayaran',[
                                        'class' => 'form-control doco-number text-right',
                                        'readonly' => true
                                    ])
                                ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <label class="control-label col-md-2">
                            <b><?=$model->attributeLabels()['sisa_piutang']?></b>
                        </label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon" title="Select date &amp; time">
                                    <span>Rp.</span>
                                </span>
                                <?=
                                    Html::activeTextInput($model, 'sisa_piutang',[
                                        'class' => 'form-control doco-number text-right',
                                        'readonly' => true
                                    ])
                                ?>
                                <div class="help-block"></div>
                            </div>
                        </div>

                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label col-md-2">
                            <b><?=$model->attributeLabels()['catatan']?></b>
                        </label>
                        <div class="col-sm-3">
                            <?=
                                Html::activeTextarea($model, 'catatan',[
                                    'class' => 'form-control',
                                    'rows' => 6,
                                ])
                            ?>
                            <div class="help-block"></div>
                        </div>
                        <div class="col-md-6 text-center">
                                <b>
                                    <h3 style="margin-top : 49px !important;">Total Alokasi Pembayaran : Rp.
                                        <nominal id="total-bayar">0</nominal>
                                    </h3>
                                </b>
                        </div>
                    </div>
                </div>
                <h6>Alokasi Pembayaran</h6>
                <hr>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="select-checkbox">
                                <div class="checkbox">
                                  <label><input type="checkbox" id="check-all" value="1"></label>
                                </div>
                            </th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Data Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Invoice");?></th>
                            <th><?=\Yii::t("fe", "No SEP");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Keluar");?></th>
                            <th><?=\Yii::t("fe", "Instalasi / Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Pasien Bayar");?></th>
                            <th><?=\Yii::t("fe", "Piutang");?></th>
                            <th><?=\Yii::t("fe", "Piutang (Telah Bayar)");?></th>
                            <th width="15%"><?=\Yii::t("fe", "Jumlah Bayar");?></th>
                            <th><?=\Yii::t("fe", "Sisa Tagihan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var _tabel;

    function trigger_tr_click(){
        $.each($("#example tbody tr"), function(){
            $(this).trigger("click");
        });
    }

    var date = new Date();
    var dateStr =
    date.getFullYear() + "-" +
    ("00" + (date.getMonth() + 1)).slice(-2) + "-" +
    ("00" + date.getDate()).slice(-2) + " " +
    ("00" + date.getHours()).slice(-2) + ":" +
    ("00" + date.getMinutes()).slice(-2) + ":" +
    ("00" + date.getTime()).slice(-2);

    var startDate = new Date(date);
    startDate.setMinutes(startDate.getMinutes() - 1);
    var dateStart =
        date.getFullYear() + "-" +
        ("00" + (startDate.getMonth() + 1)).slice(-2) + "-" +
        ("00" + startDate.getDate()).slice(-2) + " " +
        ("00" + startDate.getHours()).slice(-2) + ":" +
        ("00" + startDate.getMinutes()).slice(-2) + ":" +
        ("00" + startDate.getTime()).slice(-2);

    function count_selected() {
        var all_el = _tabel.rows()[0].length;
        var checked_el = $("#example tr.selected").length;

        if (parseInt(all_el) != parseInt(checked_el)) {
            $("#check-all").prop("checked", false);
        }else{
            $("#check-all").prop("checked", true);
        }
    }

    function checkSelectedIsImport(dataRows){
        $.each(dataRows, function (key, val) {
            let rowIndex = key
            if(val.is_imported) {
                $(`#example tbody > tr:eq(${rowIndex}) .select-checkbox`).trigger('click');
            }
        })
    }

    function checkValJumlahBayarIsImport(dataRows){
        $.each(dataRows, function (key, val) {
            let rowIndex = key
            if(val.is_imported) {
                $(`#example tbody > tr:eq(${rowIndex}) .total-alokasi`).val(val.jumlah_bayar_session);
                $(`#example tbody > tr:eq(${rowIndex}) .total-alokasi`).trigger('keyup');
            }
        })
    }

    var _countTotalbayar = function () {
        var _target = $('#total-bayar');
        var _total = 0;
        $.each(_cacheAlokasi, function (key, val) {
           _total += val.jumlah_bayar;
           _totalPembayaran = _total;
        });
        _target.html(docoHelper.convertToRupiah(_total));
    }

    $(document).on("keyup click", "#example .total-alokasi", function(event){
        event.preventDefault();
        var _parent = $(this).closest('tr');
        var result = _tabel.row($(_parent)).data();
        var _nominal = docoHelper.convertToAngka($(this).val());
        var _idParent = result.pengajuanklaimdetail_id;
        if (_nominal > result.sisa_piutang) {
            _nominal = result.sisa_piutang;
            $(this).val(result.sisa_piutang).trigger('change');
        }
        if (typeof _cacheAlokasi[_idParent] == 'undefined') {
            _cacheAlokasi[_idParent] = {
                pendaftaran_id : result.pendaftaran_id,
                pasienadmisi_id : result.pasienadmisi_id,
                pasien_id : result.pasien_id,
                jumlah_piutang : result.piutang,
                jumlah_telahbayar : result.bayar,
                jumlah_bayar : 0,
                is_imported : false,
            }
        }
        _cacheAlokasi[_idParent].jumlah_bayar = _nominal;
        _countTotalbayar();
        count_selected();

    });

    $(document).on('change', '.no_pembayaran', function (event) {
        event.preventDefault();
        showLoader();
        var _value = $(this).val();
        var _pengajuan = $('#transaksialokasiform-total_pengajuan').val();
        var _telahBayar = $('#transaksialokasiform-total_terbayar').val();
        _pengajuan = parseInt(docoHelper.convertToAngka(_pengajuan));
        _telahBayar = parseInt(docoHelper.convertToAngka(_telahBayar));
        if (typeof _rawInfo[_value] != 'undefined') {
            $("#transaksialokasiform-tanggal_pembayaran").datetimepicker({
                    format : 'yyyy-mm-dd hh:ii:ss',
                    minuteStep : 1,
                    timePicker24Hour : true,
                    startDate : _rawDetail,
                    autoclose : false,
                    endDate : dateStr,
                });
            var datepicker= document.getElementsByClassName('datetimepicker');
            if(datepicker.length > 1) {
                document.getElementsByClassName('datetimepicker')[0].remove();
            }
            $(document).on('click', function(e) {
                if (e.target.id !== 'transaksialokasiform-tanggal_pembayaran') {
                    document.getElementsByClassName('datetimepicker')[0].style.display = 'none';
                } 
            })
            $("#transaksialokasiform-tanggal_pembayaran").on('click', function(){
                document.getElementsByClassName('datetimepicker')[0].style.display = 'block';
            });
            $('#transaksialokasiform-jumlah_pembayaran').val(_rawInfo[_value]).trigger('change');
            $('#transaksialokasiform-sisa_piutang').val((_pengajuan - _telahBayar) - parseInt(_rawInfo[_value])).trigger('change');
        }
        hideLoader();
    });

    $(document).ready(function() {

        var configInfinity = {
            url: `/api/penjamin/list-pembayaran`,
            additionalOption: {
                placeholder: `-- Pilih Pembayaran --`
            },
            callbackProccess : (data) => {
                var results = []; 
                $.each(data.results.result, function (index, row) {
                    _rawInfo[row.terimabayarklaim_id] = row.pembayaran;
                    _rawDetail = row.parent.tgl_terimabayarklaim;
                    results.push({
                        id: row.terimabayarklaim_id,
                        text: row.parent.no_terimabayarklaim
                    });
                });
                return {
                    results: results,
                    pagination: {
                        more: data.results.pagination.more
                    },
                    incomplete_results: false,
                };    
            },
            callbackData: (params) => {
                return {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                    additionalPayload : {
                        pengajuanklaim_id: `<?= $id ?>` ?? null
                    }
                }
            }
        }
        $(`.no_pembayaran`).select2InfinityScroll(configInfinity);

        $('.no_pembayaran_backup').select2({
            placeholder: '-',
            minimumInputLength: 3,
            ajax: {
                url: '/penjamin-asuransi/transaksi-alokasi-pembayaran/get-no-pembayaran?id=<?= $id ?>',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  _rawInfo = data.rawInfo;
                  _rawDetail = data;
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        _tabel = $("#example").docoTabel({
            columnDefs: [
                {
                    targets: 13,
                    width : "35%"

                },
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }
            ],
            select: {
                style:    'multiple',
                selector: 'tr td:not(:nth-child(13))'
            },
            sorting: [[3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            ajax: "/penjamin-asuransi/transaksi-alokasi-pembayaran/get-data?id=<?= $id ?>",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Data Pasien",
                    orderable: false,
                    render: function ( data, type, row, meta ) {
                        const noPendaftaran = row.no_pendaftaran
                        const namaPasien = row.nama_pasien
                        const dataPasien = `<b>${namaPasien}</b> </br> ${noPendaftaran}`;
                        return dataPasien;
                    }
                },
                {
                    title: "No Invoice",
                    data: "no_pembayaran"
                },
                {
                    title: "No SEP",
                    data: "nosep"
                },
                {
                    title: "Tanggal Masuk",
                    data: "tgl_pendaftaran"
                },
                {
                    title: "Tanggal Keluar",
                    data: "tglpasienpulang"
                },
                {
                    title: "Instalasi / Ruangan",
                    orderable: false,
                    render: function ( data, type, row, meta ) {
                        const installasi = row.instalasi_nama
                        const ruangan = row.ruangan_nama
                        const dataResult = `<b>${installasi}</b> </br> ${ruangan}`;
                        return dataResult;
                    }
                },
                {
                    title: "Tagihan (Rp.)",
                    data: "total_tagihan",
                    orderable: false,
                    className : "text-right"
                },
                {
                    title: "Jumlah Pasien Bayar (Rp.)",
                    data: "jumlah_telahbayar",
                    orderable: false,
                    className : "text-right"
                },
                {
                    title: "Piutang (Rp.)",
                    data: "jumlah_piutang",
                    orderable: false,
                    className : "text-right"
                },
                {
                    title: "Piutang (Telah Bayar) (Rp.)",
                    data: "jumlah_bayar",
                    orderable: false,
                    className : "text-right"
                },
                {
                    title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Bayar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Rp.)",
                    data: "label_bayar",
                    searchable: false,
                    orderable: false,
                    className : "text-right"
                },
                {
                    title: "Sisa Tagihan (Rp.)",
                    data: "jumlah_sisapiutang",
                    orderable: false,
                    className : "text-right"
                },
            ],
            drawCallback : function (setting) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var tr = $(this);
                $.each(dataRows, function (key, val) {
                    let rowIndex = key
                    var _primary = val.pengajuanklaimdetail_id;
                    _cacheAlokasi[_primary] = {
                        pendaftaran_id : val.pendaftaran_id,
                        pasienadmisi_id : val.pasienadmisi_id,
                        pasien_id : val.pasien_id,
                        jumlah_piutang : val.piutang,
                        jumlah_telahbayar : val.bayar,
                        jumlah_bayar : 0,
                        is_imported : val.is_imported
                    }
                });
                checkSelectedIsImport(dataRows);
                checkValJumlahBayarIsImport(dataRows);
                $('.doco-number').trigger('change');
                _countTotalbayar();
            }
        });

        $('.doco-number').trigger('change');
        $(".dataTables_filter").hide();
    });

    $(document).on("click", "#check-all", function(){
        if ($("#check-all").prop("checked") == true) {
            _tabel.rows().select();
        }else{
            _tabel.rows().deselect()
        }

        trigger_tr_click();
    });

    $("#check-all").uniform({
        radioClass: 'choice',
        checkboxClass: 'checker checker-inverse'
    });
</script>