<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-14 10:56:55
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-06-02 10:25:27
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi mutasi obat alkes masuk'), 'url' => ['obat-alkes']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .input-group{
        margin-bottom: 0 !important;
    }

    .form-info-p{
        padding: 7px 5px;
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar">
                <?=DocoHelpers::generateToolbar([
                    'save-penerimaan' => [
                        'type' => 'button',
                        'method' => 'ss',
                        'icon' => 'fa fa-floppy-o',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'attributes' => [
                            'id' => 'btn-simpan-penerimaan',
                            'form_id' => 'penerimaan-form',
                            'class' => 'btn btn-success btn-labeled btn-xs',
                            'disabled' => (in_array($arr_no_mutasi['status_mutasi'], [null, DocoConstants::STATUS_TERIMA]))
                        ]
                    ],
                    'print-rincian' => [
                        'type' => 'button',
                        'title' => 'Print',
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'class' => 'data-lihat print-tagihan',
                            'id' => 'print-tagihan',
                            'method' => 'json',
                            'data-options' => 'link',
                            'disabled' =>  (@$arr_no_mutasi['status_mutasi'] == DocoConstants::STATUS_TERIMA ? false : true)
                        ]
                    ],
                    'kembali'=>[
                        'type'=>'link',
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'method' => 'not-exist',
                        'attributes' => [
                            'class'=>'btn btn-success btn-labeled btn-xs btn btn-info data-kembali',
                            'href' => Url::home().Yii::$app->controller->module->id.'/informasi-obat-alkes-keluar'
                        ]
                    ],
                ],'#table-obat');?>
                <div class="pull-right">
                    <?=DocoHelpers::generateToolbar([
                    'reset' => [
                        'attributes' => [
                            'id' => 'reset-form'
                        ]
                    ]
                ]);?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12" id="informasi" style="margin-top:10px;">
                        <?php
                        $form = ActiveForm::begin([
                            'class'=>'penerimaan-form',
                            'id'=>'penerimaan-form',
                        ]);
                        ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-3" style="margin-top: 5px"><?= Yii::t('fe', "Tanggal Terima") ?></label>
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control pickadate" name="PenerimaanObatForm[tglterima]">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-3" style="margin-top: 5px"><?= Yii::t('fe', "Tanggal Kirim") ?></label>
                                    <div class="col-md-9">
                                        <p class="form-info-p"><?= is_null($arr_no_mutasi) ? "-" : date('d F Y', strtotime($arr_no_mutasi['tglmutasioa'])) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-4" style="margin-top: 5px"><?= Yii::t('fe', "Instalasi Pengirim") ?></label>
                                    <div class="col-md-8">
                                        <p class="form-info-p"><?= !empty($arr_pemesanan['instalasi_tujuan']) ? $arr_pemesanan['instalasi_tujuan'] : $arr_no_mutasi['instalasi_nama'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-3" style="margin-top: 5px"><?=Yii::t('fe','No Pengiriman')?></label>
                                    <div class="col-md-9">
                                        <p class="form-info-p"><?= is_null($arr_no_mutasi) ? "-" : $arr_no_mutasi['nomutasioa'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-3" style="margin-top: 5px"><?=Yii::t('fe','No Pemesanan')?></label>
                                    <div class="col-md-9">
                                        <p class="form-info-p"><?= !empty($arr_pemesanan['nopemesanan']) ? $arr_pemesanan['nopemesanan'] : "-" ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-4" style="margin-top: 5px"><?= Yii::t('fe', "Ruangan Pengirim") ?></label>
                                    <div class="col-md-8">
                                        <p class="form-info-p"><?= !empty($arr_pemesanan['ruangan_tujuan']) ? $arr_pemesanan['ruangan_tujuan'] : $arr_no_mutasi['ruangan_nama'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                        <label class="control-label col-md-3 required" style="margin-top: 5px"><?=Yii::t('fe','Pegawai Mengetahui')?></label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <?=
                                                Select2::widget([
                                                    'name'=>'PenerimaanObatForm[pegawai_mengetahui]',
                                                    'data' => $optMengetahui,
                                                    'value' => $arr_no_mutasi['id_pegawai_mengetahui'],
                                                    'options' => [
                                                        'placeholder' => Yii::t('fe','-- Pilih --'),
                                                        'class' => 'selectMengetahui select2'
                                                    ],
                                                    'pluginOptions'=>[
                                                        'allowClear'=>true,
                                                        'minimumInputLength'=>3,
                                                        'language'=>[
                                                            'errorLoading'=>new JsExpression("function() {return 'Loading...'}"),
                                                        ],
                                                        'ajax'=>[
                                                            'url'=>Url::to(['get-pegawai']),
                                                            'dataType'=>'json',
                                                            'data'=>new JsExpression('function(params){return {q: params.term}; }'),
                                                            'processResults'=>new JsExpression('function(data) {return {results: data.result}; }')
                                                        ],
                                                        'escapeMarkup'=> new JsExpression('function(markup) {return markup;}'),
                                                    ]
                                                ]);

                                                ?>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-sm-3 required" style="margin-top: 5px"><?=Yii::t('fe','Pegawai Menyetujui')?></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <?=
                                            Select2::widget([
                                                'name'=>'PenerimaanObatForm[pegawai_menyetujui]',
                                                'data' => $optPenerima,
                                                'value' => $arr_no_mutasi['id_pegawai_penerima'],
                                                'options' => [
                                                    'placeholder'=>Yii::t('fe','-- Pilih --'),
                                                    'class' => 'selectMenyetujui select2'
                                                ],
                                                'pluginOptions'=>[
                                                    'allowClear'=>true,
                                                    'minimumInputLength'=>3,
                                                    'language'=>[
                                                        'errorLoading'=>new JsExpression("function() {return 'Loading...'}"),
                                                    ],
                                                    'ajax'=>[
                                                        'url'=>Url::to(['get-pegawai']),
                                                        'dataType'=>'json',
                                                        'data'=>new JsExpression('function(params){return {q: params.term}; }'),
                                                        'processResults'=>new JsExpression('function(data) {return {results: data.result}; }')
                                                    ],
                                                    'escapeMarkup'=> new JsExpression('function(markup) {return markup;}'),
                                                ]
                                            ]);
                                            ?>
                                        </div>

                                    </div>
                               </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                    <th><?= Yii::t('fe', 'Qty Kirim') ?></th>
                                    <th><?= Yii::t('fe', 'Qty Terima') ?></th>
                                    <th><?= Yii::t('fe', 'Satuan Konversi') ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="clear"><br></div>
                            <?php
                            echo Html::hiddenInput('PenerimaanObatForm[mutasiobatruangan_id]', $mutasiobatruangan_id);
                            echo Html::hiddenInput('PenerimaanObatForm[pegawai_id]',
                                Yii::$app->docoVars->user("id_pegawai"));
                            echo Html::hiddenInput('PenerimaanObatForm[totalharganetto]','',['class'=>'totalharganetto']);
                            echo Html::hiddenInput('PenerimaanObatForm[totalhargajual]','',['class'=>'totalhargajual']);
                            echo Html::hiddenInput('PenerimaanObatForm[ruangan_penerima]',$ruangan_tujuan,['class'=>'ruangan_penerima']);
                            echo Html::hiddenInput('PenerimaanObatForm[ruangan_asal]',$ruangan_asal,['class'=>'ruangan_asal']);
                            ActiveForm::end();
                            ?>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<?php
    $_terimaMutasi = isset($arr_no_mutasi['terimamutasiobat_id'])
                ? DocoHelpers::encrypt($arr_no_mutasi['terimamutasiobat_id']) : null;
    $_tgl_kirim = explode("-", date('Y-m-d', strtotime($arr_no_mutasi['tglmutasioa'])));
    $_tgl_kirim[1] = $_tgl_kirim[1] - 1; // dateapick months start from 0; 0 = jan, 11 = dec
    $_tgl_kirim = implode(",", $_tgl_kirim);
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs(
        "
        var _idTerima = '{$_terimaMutasi}';
        var id = '".$pesanobatalkes_id."';
        var pesanan = '".$pesanobatalkes_id."';

        if(pesanan != '') {
            paramUrl = 'id=' + id + '&pesanan=' + pesanan;
        } else {
            paramUrl = 'id=' + id;
        }
        console.log(paramUrl);
        $(document).ready(function(){
            table = $('#tabel-obat').docoTabel({
                bDestroy: true,
                filter: false,
                sorting: [[1,'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'apotek/informasi-mutasi/detail-penerimaan-pesanan?' + paramUrl,
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Nama Obat Alkes')."',
                        data: 'obatalkes_namalain',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Qty Pesan')."',
                        data: 'jumlah_input',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Qty Terima')."',
                        data: 'jumlah_input_mutasi',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Satuan')."',
                        data: 'satuanbesar_nama',
                        searchable: false,
                        orderable: false,
                    },

                ],
                initComplete: function(settings, json) {

                }
            });

            $('.dataTables_filter').hide();

            $('.pickadate').pickadate({
                format: 'dd mmmm yyyy',
                min: [$_tgl_kirim],
                max: true,
                onStart: function () {
                    var date = new Date();
                    this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
                }
            });
        });

        $(document).off('click', '.btn-toolbar');
        $('#penerimaan-form').docoForm('submit',{
            success : function(data) {
                _idTerima = data.response.id;
                $('#btn-simpan-penerimaan').prop('disabled',true);
                $('#print-tagihan').prop('disabled',false);
                window.location.replace('/apotek/informasi-obat-alkes-keluar');
            }
        });
        var str_validasi = 'Error';
        var is_any_cek = false;
        $(document).on('click','#btn-simpan-penerimaan', function(e){
            e.preventDefault();
            $('#penerimaan-form').submit();
        });

        $('.selectNomutasioa').select2({
            placeholder: '".\Yii::t("fe", "No Mutasi")."',
                minimumInputLength: 4,
                ajax: {
                    url: '/apotek/informasi-mutasi/get-data-nomutasi',
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

        function loadData(id){
            table = $('#tabel-obat').docoTabel({
                bDestroy: true,
                filter: false,
                sorting: [[1,'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'apotek/informasi-mutasi/get-data-detail?id='+id,
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Nama Obat Alkes')."',
                        data: 'obatalkes_namalain',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Qty Mutasi')."',
                        data: 'jumlah_mutasi',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Qty Penerimaan')."',
                        data: 'jumlah_mutasi',
                        searchable: false,
                        orderable: false,
                    },

                ],
                initComplete: function(settings, json) {

                }
            });

            $('.dataTables_filter').hide();

        }
        var totalharganetto = 0;
        var totalhargajual = 0;

        $(document).on('click','#reset-form', function (event) {
            event.preventDefault();
            $('.select2').val('').trigger('change');
        });

        $(document).on('click','#print-tagihan', function (event) {
            event.preventDefault();
            window.open('/apotek/informasi-mutasi/print-penerimaan?id='+_idTerima);
        });

        ", View::POS_END, 'js-kuning'
    );
?>
