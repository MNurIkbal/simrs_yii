<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', 'Retur Obat Alkes');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi Penerimaan Obat Alkes Supplier', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>

<style>
    .datepicker>div{
        display:block;
    }

    .dataTables_scrollFoot {
        overflow: visible!important;
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                        </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'),
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-retur'
                    ]);
                ?>
                <?= DocoHelpers::generateToolbar([
                    'back',
                ]) ?>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Penerimaan'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Tanggal Penerimaan") ?></b></label>
                                            <div class="col-sm-7">
                                                <p>&nbsp;<?= isset($data['tgl_penerimaan']) ? date('d-M-Y',strtotime($data['tgl_penerimaan'])) : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Nama Supplier") ?></b></label>
                                            <div class="col-sm-7">
                                                <p>&nbsp;<?= isset($data['supplier_nama']) ? $data['supplier_nama'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Penerimaan") ?></b></label>
                                            <div class="col-sm-7">
                                                <p>&nbsp;<?= isset($data['no_penerimaan']) ? $data['no_penerimaan']  : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <?php $form = ActiveForm::begin(
                                    [
                                        'action' => '/gudang/inf-penerimaan-obat-alkes/save?id='.$id,
                                        'method' => 'post',
                                        'id' => 'form',
                                        'enableAjaxValidation'=>false,
                                        'enableClientValidation'=>false,
                                        'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 3,
                                            'deviceSize' => ActiveForm::SIZE_SMALL
                                        ],
                                    ]
                                ) ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 required">
                                            <label class="text-left control-label col-sm-5">
                                                <b><?= $model->getAttributeLabel('tanggal_retur') ?></b>
                                            </label>
                                            <?= $form->field($model, 'tanggal_retur', [
                                                    'horizontalCssClasses' => [
                                                            'wrapper' => 'col-md-6'
                                                        ],
                                                ])->widget(DatePicker::classname(), [
                                                'name' => 'tanggal_retur',
                                                'pluginOptions' => [
                                                    'language' => 'en',
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                    'startDate' => "0d"
                                                ],
                                                'options' => [
                                                    'placeholder' => $model->getAttributeLabel('tanggal_retur'),
                                                    'readonly' => true,
                                                    'tabindex' => 0,
                                                    'disabled' => true
                                                ]
                                            ])->label(false); ?>

                                        </div>
                                        <div class="col-md-4 required">
                                            <label class="text-left control-label col-sm-5 ">
                                                <b><?= $model->getAttributeLabel('pegawai_retur') ?></b>
                                            </label>
                                            <?= $form->field($model, 'pegawai_retur',[
                                            'horizontalCssClasses' => [
                                                    'wrapper' => 'col-md-6'
                                                ],
                                            ])->dropDownList([],[
                                                'class' => '',
                                                'id' => 'pegawai_retur',
                                            ])->label(false); ?>
                                        </div>
                                        <div class="col-md-4 required">
                                            <label class="text-left control-label col-sm-5 ">
                                                <b><?= $model->getAttributeLabel('alasan_retur') ?></b>
                                            </label>
                                            <?= $form->field($model, 'alasan_retur', [
                                                    'horizontalCssClasses' => [
                                                            'wrapper' => 'col-md-6'
                                                        ],
                                                    ])->textarea([
                                                            'placeholder' => $model->getAttributeLabel('alasan_retur'),
                                                            'class' => 'form-control input-sm pickadate',
                                                            'id' => 'tanggal-pemakaian',
                                                            'autocomplete' => "off",
                                                            'rows' => 3,
                                                            'style' => 'resize: none;'
                                                    ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div id="error_ReturPenerimaanFormdata_retur"></div>
                            <?php ActiveForm::end(); ?>
                            <table id="example"
                                class="table datatable-basic table-striped table-hover dataTable no-footer">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1"></th>
                                        <th width="1" class="text-center">No</th>
                                        <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                        <th><?=\Yii::t("fe", "No. Batch");?></th>
                                        <th><?=\Yii::t("fe", "Harga Satuan (Rp.)");?></th>
                                        <th><?=\Yii::t("fe", "Qty Terima");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                                        <th><?=\Yii::t("fe", "Sub Total (Rp.)");?></th>
                                        <th><?=\Yii::t("fe", "Qty Sisa");?></th>
                                        <th width="50"><?=\Yii::t("fe", "Qty Retur");?></th>
                                        <th><?=\Yii::t("fe", "Total Retur (Rp.)");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="12">
                                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="11" class="text-right">Total Retur (Rp.)</th>
                                        <th class="text-right"><span class="total_retur">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var _tabel;
    var _tmpData = {};

    $(document).on('click','#simpan-retur', function (event) {
        event.preventDefault();
        var _data = $('#form').serializeArray();
        _data.push({
            name : 'data_retur',
            value : JSON.stringify(_tmpData)
        });
        $().docoForm('click', {
            data : _data,
            url : $('#form').attr('action'),
            success : function (data) {
                _tmpData = {};
                table.draw();
                var _idParent = data.response.id_parent;
                var _noRetur = data.response.no_retur;
                (new PNotify({
                    title: '&nbsp;Proses Berhasil !',
                    text: 'Data Retur berhasil disimpan dengan no <strong>' + _noRetur +'</strong>, apakah Anda ingin melakukan cetak?',
                    addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                    type: 'success',
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: 'Ya',
                                addClass: 'btn btn-xs btn-success',
                            },
                            {
                                text: 'Tidak',
                                addClass: 'btn btn-xs btn-danger',
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on('pnotify.confirm', function() {
                    // Print
                    window.open('/gudang/informasi-penerimaan-obat/retur-pdf?no_retur='+_noRetur);
                    setTimeout(function(){
                        window.location.href = '/gudang/inf-penerimaan-obat-alkes';
                    }, 2000);
                }).on('pnotify.cancel', function() {
                    window.location.href = '/gudang/inf-penerimaan-obat-alkes';
                });
            }
        });
    });

    $(document).on('click', '#example tr', function(event){
        event.preventDefault();
        var tbl = $(this).hasClass('selected');
        var result = table.row(this).data();
        var attr = $(this).find('input');
        if (tbl) {
            _tmpData[result.primary] = {
                qty_retur : 0,
                harga_netto_satuan : result.harga_netto_satuan,
                sisa : result.qty_sisa,
                obatalkes_id : result.obatalkes_id,
                satuanbesar_id : result.satuanbesar_id,
                tglkadaluarsa : result.tgl_kadaluarsa,
            };
            attr.prop('disabled',false);
        } else {
            if (typeof _tmpData[result.primary] != 'undefined') {
                delete _tmpData[result.primary];
            }
            attr.val('');
            attr.prop('disabled',true);
        }
    });

    $(document).on('keyup', '.qty-retur', function (event) {
        var _key = $(this).attr('data-key');
        var _value = 0;

        if (typeof _tmpData[_key] != 'undefined') {
            _value = _tmpData[_key].sisa;
            if ($(this).val() < _value) {
                _value = $(this).val();
            }
            _tmpData[_key].qty_retur = _value;
        }
        $(this).val(_value);

        var total_retur = 0;
        $.each(_tmpData, function(key, val){
            var qty_retur = val.qty_retur;
            var hn = docoHelper.convertToAngka(val.harga_netto_satuan);
            total_retur = total_retur + (hn * qty_retur);
        });

        var qty_retur = $('[data-key='+_key+']').val();
        var harga_netto_satuan = docoHelper.convertToAngka(_tmpData[_key].harga_netto_satuan);
        var subtotal_retur = qty_retur * harga_netto_satuan;
        subtotal_retur = docoHelper.convertToRupiah(subtotal_retur);
        total_retur = docoHelper.convertToRupiah(total_retur);
        $('.subtotal_retur_'+_key).text(subtotal_retur);
        $('.total_retur').html(total_retur);
    });

    $(document).ready(function(){
        table = $('#example').docoTabel({
            sorting: false,
            scrollX: true,
            paging : false,
            lengthChange : false,
            pageLength : 50,
            sorting: [[2, 'asc']],
            columnDefs: [{
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }],
            createdRow: function (row, data, index) {
                $('td', row).eq(11).attr('class', 'subtotal_retur_'+data.primary+' text-right');
            },
            select: {
                style: 'multi',
                selector: 'td:nth-child(n+0):nth-child(-n+7)'
            },
            processing: true,
            serverSide: true,
            ajax: baseUrl+'gudang/inf-penerimaan-obat-alkes/get-data-retur?id={$id}',
            columns: [
                {
                    data: null,
                    searchable: false,
                    sortable: false,
                    defaultContent: \"\",
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Nama Obat Alkes',
                    data: 'obatalkes_nama',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Tanggal Kadaluarsa',
                    data: 'tglkadaluarsa_label',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No. Batch',
                    data: 'no_batch',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Harga Satuan (Rp.)',
                    data: 'harga_netto_satuan',
                    class: 'text-right',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Qty',
                    data: 'qty_besar_in_label',
                    class: 'text-right',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Satuan Besar',
                    data: 'satuan_besar',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Sub Total (Rp.)',
                    data: 'subtotal',
                    class: 'text-right',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Qty Sisa',
                    data: 'sisa_penerimaan_label',
                    class: 'text-right',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Qty Retur',
                    data: 'input_retur',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Total Retur (Rp.)',
                    data: null,
                    class: 'text-right',
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row) {
                        return 0;
                    }
                },
            ],
            drawCallback : function (event,data) {
                var _dataJson = event.json.data;
            }
        });
        $('.dataTables_filter').hide();
        $('#pegawai_retur').select2({
            placeholder: 'Pilih Pegawai retur',
            minimumInputLength: 3,
            ajax : {
                url: '/gudang/inf-penerimaan-obat-alkes/get-pegawai',
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
                escapeMarkup: function (m) { return m; },
            },
        }).on('select2:select', function(e){
            var data = e.params.data;
        });
    });

    $(document).on('keydown', null, 'alt+s',function(e){
        $('textarea').blur();
        $('input').each(function(){
            $(this).blur();
        });
        $('#simpan-retur').click();
    });

", VIEW::POS_END, 'js-kunings');