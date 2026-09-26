<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-12-26 13:45:00
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 17:25:33
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => []];
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
                        <h3 class="panel-title"><b>
                            <?=
                                Yii::$app->docoVars->workspace("modul_alias",$this->title);
                            ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'save'=>[
                            'attributes' => [
                                'id' => 'btn-save',
                                'onClick' => ''
                            ]
                        ],
                        'reset',
                    ]);?>
            </div>

            <div class="panel-body">
                <br>
                <div class="row">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => 'ajax-form',
                            'enableAjaxValidation'=>false,
                            'enableClientValidation'=>false,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => [
                                'labelSpan' => 3,
                                'deviceSize' => ActiveForm::SIZE_SMALL
                            ],
                        ]);
                    ?>
                    <div class="form-group">
                    <div class="">
                        <div class="col-md-4 required">
                            <?= $form->field($model, 'tanggal_pemusnahan', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-5 ',
                                            'wrapper' => 'col-md-7 '
                                        ],
                                        'options' => [
                                            'tag' => false,
                                        ],
                                        'addon' => ['append' => [
                                                'content' => '<i class="fa fa-calendar"></i>']]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('tanggal_pemusnahan'),
                                            'class' => 'form-control input-sm pickadate',
                                            'id' => 'tanggal-pemakaian',
                                            'autocomplete' => "off",
                                            'readonly' => true,
                                            'required' => true
                                        ]); ?>
                        </div>
                    </div>

                    <div class="">
                        <div class="col-md-4 required">
                            <?= $form->field($model, 'pegawai_mengetahui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 required',
                                        'wrapper' => 'col-md-8'
                                    ],
                                    'options' => [
                                        'tag' => false, // Don't wrap with "form-group" div
                                    ],
                                ])->dropDownList($pegMengetahui,[
                                    'class' => 'select2',
                                    'id' => 'list-pegawai_mengetahui',
                                    'prompt' => Yii::t('fe', 'Pilih Pegawai')
                                ]); ?>
                        </div>
                    </div>

                    <div class="">
                        <div class="col-md-4 required">
                            <?= $form->field($model, 'pegawai_meyetujui')->hiddenInput(['value' => $pegawai_pelaksana_id])->label(false); ?>
                            <?= \Yii::t('fe', 'Pegawai Pelaksana') ?>&emsp;
                            <?= $pegawai_pelaksana; ?>
                        </div>
                    </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="row">
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th></th>
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Kode Obat Alkes");?></th>
                                <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                <th><?=\Yii::t("fe", "Qty Persediaan");?></th>
                                <th><?=\Yii::t("fe", "Harga Obat Alkes (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Qty Pemusnahan");?></th>
                                <th><?=\Yii::t("fe", "Total harga netto (Rp.)");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table">
                        <tfoot>
                            <tr class="bg-inverse">
                                <th width="80%" class="text-right">Total Rp.</th>
                                <th width="20%"class="text-right" id="total_seluruh" style="padding-right: 38px;">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <br>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs("
    var _cachePemusnahan= {};
    var _counter = 0;
    var _totalNetto = 0;
    $(document).ready(function(){
        table = $('#example').docoTabel({
            filter: true,
            paging: false,
            info: false,
            scrollY: '600px',
            scrollCollapse: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style: 'multi',
                selector: '.select-checkbox',
            },
            sorting: [[3, 'asc'],[4, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            rowCallback: function(row, data){
                if(typeof _cachePemusnahan[data.primary] != 'undefined')
                {
                    $(row).addClass('selected');
                }
            },
            ajax: baseUrl+'gudang/transaksi-pemusnahan-obat/get-data',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Kode Obat Alkes'))."', data: 'obatalkes_kode'},
                {title: '".(\Yii::t('fe', 'Nama Obat Alkes'))."', data: 'obatalkes_nama'},
                {title: '".(\Yii::t('fe', 'Tanggal Kadaluarsa'))."', data: 'tglkadaluarsa'},
                {title: '".(\Yii::t('fe', 'Qty Persediaan'))."', data: 'stok_satuan'},
                {title: '".(\Yii::t('fe', 'Harga Obat Alkes (Rp.)'))."', data: 'harganetto', class: 'text-right'},
                {
                    title: '".(\Yii::t('fe', 'Qty Pemusnahan'))."',
                    data: 'qty_pemusnahan',
                    orderable: false,
                    class: 'text-center',
                    searchable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Total Harga Netto (Rp.)'))."',
                    data: 'subtotal',
                    class: 'text-right',
                    orderable: false,
                    searchable: false
                },
            ],
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                3,
                \"<div class='input-group'><input type='text' value='".date('d-M-Y')."' id='rangeDemoStart' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' readonly=true value='".date('d-M-Y')."' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
        ], {3:0},true);

        dateRangeHelper('.startDate','.endDate','.targetDate');

        $('.no_pemusnahan').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/apotek/informasi-pemusnahan-obat/get-no-pemusnahan',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
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

        $(document).on('click', '#example tr', function(event){
            event.preventDefault();
            var tbl = $(this).hasClass('selected');
            var input = $(this).find('input.qty_pemusnahan');
            var checked = $(this).find('td').hasClass('select-checkbox');
            var result = table.row(this).data();
            if (tbl && checked) {
                input.removeAttr('disabled');
                input.focus();
                // _totalNetto = _totalNetto + docoHelper.convertToAngka(result.harganetto);
                // _cachePemusnahan[result.primary] = result;
            } else {
                input.prop('disabled', true);
                input.val('');
                delete _cachePemusnahan[result.primary];
                sumPemusnahan();
                $(this).find('span.total_netto').html(0);
                // _totalNetto = _totalNetto - docoHelper.convertToAngka(result.harganetto);
                // $(this).removeClass('selected');
            }
        });

        $(document).on('keyup', '.qty_pemusnahan', function(e){
            var index = $(this).data('index');
            var data = table.row(index).data();
            var persediaan = 0;
            var harga = 0;
            var total_harga = 0;
            var input = $(this).val();
            var temp = {};
            input = docoHelper.convertToAngka(input);

            if(typeof data != undefined) {
                persediaan = data.stok_exp;
                persediaan = docoHelper.convertToAngka(persediaan);
                data.stok_exp = persediaan;

                harga = data.harganetto;
                harga = docoHelper.convertToAngka(harga);
                data.harganetto = harga;

                temp = data;
            }

            if(input < 0) {
                $(this).val(0);
                input = 0;
            }

            if(input > persediaan){
                $(this).val(persediaan);
                input = persediaan;
            }

            // change harga
            total_harga = harga * input;
            $('.total_netto-'+index).html(docoHelper.convertToRupiah(total_harga));

            temp.qty_pemusnahan = input;
            temp.total_harganetto = total_harga;

            if(typeof data != undefined) {
                temp['qty_pemusnahan'] = input;
                _cachePemusnahan[data.primary] = temp;
            }

            sumPemusnahan();
        });

        function sumPemusnahan() {
            var sum_total = 0;
            $.each(_cachePemusnahan, function(index, val){
                sum_total += val.total_harganetto;
            });
            _totalNetto = sum_total;
            $('#total_seluruh').html(docoHelper.convertToRupiah(sum_total));
        }

        $('.data-reset').on('click', function(){
            _cachePemusnahan = {};
            sumPemusnahan();
            _totalNetto = 0;
        })

        $(document).on('click','#btn-save', function(e){
            if( Object.keys(_cachePemusnahan).length ){
                e.preventDefault();
                var data = $('#ajax-form').serializeArray();
                data.push({
                    name: 'totalharga_netto',
                    value: _totalNetto
                });
                data.push({
                    name: 'detail',
                    value: JSON.stringify(_cachePemusnahan)
                })
                $(this).docoForm('click',{
                    url : '/gudang/transaksi-pemusnahan-obat/save',
                    method : 'POST',
                    type : 'json',
                    data : data,
                    success : function (data) {
                        $('.data-reset').click();
                        // setTimeout(function(){ $('#btn-save').prop('disabled', true); }, 100);
                        let _action = '/gudang/informasi-pemusnahan-obat/print-pemusnahan?id='+data.response.parent_id+'&nopemusnahan='+data.response.nopemusnahan;
                        (new PNotify({
                                title: 'Berhasil',
                                text: 'Pemusnahan Obat dengan Nomor ' + '<strong>' + data.response.nopemusnahan + '</strong>' + ' telah berhasil, apakah Anda ingin melakukan cetak?',
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
                                window.open(_action);
                            }).on('pnotify.cancel', function() {

                            });
                    }
                });
            }else{
                docoNotification('error', 'Terjadi Kesalahan','Belum ada obat yang dipilih!');
            }
        })
    });
    ", View::POS_END, 'js');

?>
