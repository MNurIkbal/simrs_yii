<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

use kartik\datetime\DateTimePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"), 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .datepicker>div{
        display:block;
    }
    .plat-nomor{
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #fff;
    }
    .background-plat{
        /*border: 1px solid transparent;*/
        text-align: center;
        display: inline-block;
        position: relative;
        width: 165px;
        padding: 5px;
        border-color: #fff;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #54be8b; /*#001;*/
        /*border: 1px solid #291ce8;*/
    }

    /*.my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }
    .my-legend .legend-scale ul li {
        display: contents;
        float: left;
        width: 50px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 50px;
        border: solid 0.2px;
    }
    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }
    .my-legend a {
        color: #777;
    }

    .square-batal {
        height: 30px;
        width: 70px;
        background-color: rgba(255, 188, 188, 0.58);
        color:#ffffff;
        padding: 5px 0 5px 10px;
    }
    .daterangepicker.dropdown-menu.ltr.show-calendar.opensright {
        top: 657.573px !important;
    }*/
/*    .form-group {
        margin-bottom: 0px !important;
        margin-top: 0px !important;
    }*/
    /*.panel-default > .panel-heading {
        color: #606060;
        background-color: #fcfcfc;
        border-color: #ddd;
        margin-bottom: 10px;*/

    /*.col-md-7.petugas-p, .col-md-7.obatalkes_id-p {
        margin-bottom: 10px;
    }*/
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                    <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
                <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'save' => [
                            'attributes' => [
                                'onClick' => false,
                                'id' => 'simpan',
                                // 'disabled' => $statusPesan
                            ]
                        ],
                        'back',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form',
                                'id'=>'reset-form',
                                // 'disabled' => $statusPesan
                            ]
                        ],
                    ],'#example');?>
                </div>
                <div class="panel-body">
                    <div class="col-md-12 info-pengajuan form-vertical">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b><?= Yii::t('fe', 'Tanggal Ambulan Kembali'); ?></b></h6>
                            </div>
                            <div class="panel-body">
                                
    <?php 
    $form = ActiveForm::begin([
            'id' => 'pemakaian-form', 
            'action' => '/ambulan/informasi-pemakaian-ambulan/simpan-pemakaian-ambulan?id='.$id, 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group">
        <label class="text-left control-label col-sm-3 text-bold">No. Polisi</label>
        <div class="col-md-8 background-plat">
            <span class="plat-nomor"><?= $model->no_polisi ?></span>
        </div>
    </div>
    <br>
    <div class="form-group">
        <label class="text-left control-label col-sm-3 text-bold">Tanggal Pemakaian</label>
        <div class="col-lg-3">
            <?php
                echo DateTimePicker::widget([
                    'name' => 'FormPengembalian[tgl_pemakaiandari]',
                    'id' => 'formpengembalian-tgl_pemakaiandari',
                    'class' => 'tgl_pemakaiandari',
                    'language' => 'en',
                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                    // 'value' => date('d-M-Y h:i', strtotime($model->tgl_pemakaiandari)),
                    'value' => $model->tgl_pemakaiandari,
                    'disabled' => true,
                    'pluginOptions' => [
                        'format' => 'dd-M-yyyy hh:ii',
                        'showMeridian' => true,
                        'autoclose' => true,
                        'todayBtn' => true,
                        'endDate' => date('Y-m-d H:i:s'),
                    ]
                ]);
            ?>
        </div>
    </div>
    <div class="form-group required">
        <label class="text-left control-label col-sm-3 text-bold ">Tanggal Kembali</label>
        <div class="col-lg-3">
            <?php
                echo DateTimePicker::widget([
                    'name' => 'FormPengembalian[tgl_kembali]',
                    'id' => 'formpengembalian-tgl_kembali',
                    'class' => 'tgl_kembali',
                    'language' => 'en',
                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                    'value' => null,
                    'readonly' => true,
                    'pluginOptions' => [
                        'format' => 'dd-M-yyyy hh:ii',
                        'showMeridian' => true,
                        'autoclose' => true,
                        'todayBtn' => true,
                        // 'endDate' => date('Y-m-d H:i:s', strtotime($model->tgl_pemakaiandari)),
                        'startDate' => date('Y-m-d H:i:s', strtotime($model->tgl_pemakaiandari))
                    ]
                ]);
            ?>

        </div>
    </div>
    <div class="form-group">
        <label class="text-left control-label col-sm-3 text-bold">Km Awal</label>
        <div class="col-lg-3">
            <?= Html::activeInput('text', $model, 'km_awal', ['class' => 'form-control text-right doco-number',
                    'readonly' => true,]) ?>
                    <div class="help-block"></div>
            <!-- <?= $form->field($model, 'km_awal')->textInput([
                    'class' => 'form-control text-right doco-number',
                    'readonly' => true,
                ])->label(false); ?> -->

        </div>
    </div>

    <div class="form-group required">
        <label class="text-left control-label col-sm-3 text-bold">Km Akhir</label>
        <div class="col-lg-3">
            <?= Html::activeInput('text', $model, 'km_akhir', ['class' => 'form-control text-right doco-number']) ?>
          
        </div>
    </div>
    
    <div class="form-group">
        <label class="text-left control-label col-sm-3 text-bold">Biaya Pemakaian</label>
        <div class="col-lg-3">
            <?= $form->field($model, 'biaya_pemakaian', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-5'
                    ],
                'addon' => [
                    'prepend' => [
                        'content' => 'Rp.'
                    ]]
                ])->textInput([
                    'class' => 'form-control text-right doco-number',
                    'readonly' => true,
                ])->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <?php
        // if (empty($model->pendaftaran_id)) :
        if (false) :
    ?>
        <div class="form-group required">
            <label class="text-left control-label col-sm-3 text-bold">Biaya Tambahan</label>
            <div class="col-lg-3">
                <?= $form->field($model, 'biaya_tambahan', [
                    'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-3',
                            'wrapper' => 'col-md-5'
                        ],
                    'addon' => [
                        'prepend' => [
                            'content' => 'Rp.'
                        ]]
                    ])->textInput([
                        'class' => 'form-control text-right doco-number',
                        'id' => 'biaya_tambahan'
                    ])->label(false); ?>
            </div>
        </div>
    <?php
        endif;
    ?>

    <!-- Tindakan -->
    <?php 
    $form = ActiveForm::begin([
        'id' => 'add-tindakan',
        'enableAjaxValidation' => false, 
        'enableClientValidation'=> false, 
                                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'labelSpan' => 4, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'skip-confirm' => "true"
        ]
    ]);
    ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($modelTindakan, 'daftartindakan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3 text-bold',
                    'wrapper' => 'col-md-7 daftartindakan_id-p'
                ]
            ])->dropDownList([],[
                'class' => '',
                'id' => 'daftartindakan_id_rs',
            ])->label(Yii::t('fe', 'Daftar Tindakan')); ?>
        </div>
        <div class="col-md-6">
            <div class="col-md-3">
                <?= $form->field($modelTindakan, 'tarif', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number tarif_tindakan_rs text-right',
                ])->label(Yii::t('fe', 'Tarif')); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelTindakan, 'qty', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number qty_tindakan_rs text-right',
                ])->label(Yii::t('fe', 'Qty')); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelTindakan, 'cyto', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-8'
                    ]
                ])->checkboxList([
                    '1' => 'Cyto',
                ])->label(Yii::t('fe', 'Cyto')); ?>
            </div>
            <div class="col-md-3">
                <?= Html::submitButton(
                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                    [
                        'class' => 'btn btn-success btn-labeled btn-xs btn-tambah-tindakan-rs',
                    ]) 
                    ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="tabel-temp-tindakan-rs" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                        <th><?=\Yii::t("fe", "Tarif");?></th>
                        <th><?=\Yii::t("fe", "Qty");?></th>
                        <th><?=\Yii::t("fe", "Cyto");?></th>
                        <th><?=\Yii::t("fe", "Sub Total");?></th>
                        <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="7">
                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><b>Total Biaya Tambahan</b></td>
                        <td class="text-right"><b><span class="estimasi_biaya_tindakan"></span></b></td>
                        <td class="text-right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <!-- Tindakan -->
    <br>
    <?php 
    $form = ActiveForm::begin([
        'id' => 'add-obat-alkes',
        'enableAjaxValidation' => false, 
        'enableClientValidation'=> false, 
                                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'labelSpan' => 4, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'skip-confirm' => "true"
        ]
    ]);
    ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($modelObat, 'obatalkes_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3 text-bold',
                    'wrapper' => 'col-md-7 obatalkes_id-p'
                ]
            ])->dropDownList([],[
                'class' => '',
                'id' => 'obatalkes_id_rs',
            ])->label(Yii::t('fe', 'Obat Alkes')); ?>
        </div>
        <div class="col-md-6">
            <div class="col-md-3">
                <?= $form->field($modelObat, 'tarif', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number tarif_obat_rs text-right',
                ])->label(Yii::t('fe', 'Tarif')); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelObat, 'qty', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm doco-number qty_obat_rs text-right',
                ])->label(Yii::t('fe', 'Qty')); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($modelObat, 'cyto', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4 text-bold',
                        'wrapper' => 'col-md-8'
                    ]
                ])->checkboxList([
                    '1' => 'Cyto',
                ])->label(Yii::t('fe', 'Cyto')); ?>
            </div>
            <div class="col-md-3">
                <?= Html::submitButton(
                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                    [
                        'class' => 'btn btn-success btn-labeled btn-xs btn-tambah-obat-rs',
                    ]) 
                    ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="tabel-temp-obat-rs" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                        <th><?=\Yii::t("fe", "Tarif");?></th>
                        <th><?=\Yii::t("fe", "Qty");?></th>
                        <th><?=\Yii::t("fe", "Cyto");?></th>
                        <th><?=\Yii::t("fe", "Sub Total");?></th>
                        <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="7">
                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><b>Total Biaya Tambahan</b></td>
                        <td class="text-right"><b><span class="estimasi_biaya_obat"></span></b></td>
                        <td class="text-right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <div class="form-group">
        <label class="text-left control-label col-sm-3 text-bold">Total Biaya</label>
        <div class="col-lg-3">
            <?php $model->total_biaya = (int) $model->total_biaya; ?>
            <?= $form->field($model, 'total_biaya', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-5'
                    ],
                'addon' => [
                    'prepend' => [
                        'content' => 'Rp.'
                    ]]
                ])->textInput([
                    'class' => 'form-control text-right doco-number',
                    'readonly' => true,
                    'id' => 'total_biaya'
                ])->label(false); ?>
        </div>
    </div>
    

                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
     var _table;
    var _tableObat;
    var _tableTindakan;
    var _tablePegawai;
    var ambulanId;
    var _idParent = "'.$id.'";
    var _jarakDekat = "'.$jarakDekat.'";
    var _jarakSedang = "'.$jarakSedang.'";
    var _jarakJauh = "'.$jarakJauh.'";
    var tgl_pesanambulan = $("#tgl_pesanambulan").html();
    var min_tgl_pesanambulan = new Date(tgl_pesanambulan);
    
    $(".tarif_tindakan_rs").prop("disabled", true);
    $(".tarif_obat_rs").prop("disabled", true);
    $("#formpengembalian-biaya_pemakaian").val("0")

    $(document).ready(function() {
        
        $("#formpengembalian-km_awal").trigger("keyup")
        $("#formpengembalian-km_akhir").val(0)

        $("#formpengembalian-km_akhir").keyup(function(e){
            var akhir  = docoHelper.convertToAngka($("#formpengembalian-km_akhir").val())
            var awal   = docoHelper.convertToAngka($("#formpengembalian-km_awal").val())
            var tempuh = akhir - awal
            var biaya  = 0
            if(tempuh > "0"){
                if(tempuh <= 3){
                    biaya = tempuh * _jarakDekat
                }
                else if(tempuh <= 6){
                    biaya = 3 * _jarakDekat
                    biaya += (tempuh-3) * _jarakSedang
                }
                else if(tempuh > 6){
                    biaya = 3 * _jarakDekat
                    biaya += 3 * _jarakSedang
                    biaya += (tempuh-6) * _jarakJauh
                }
            }
            $("#formpengembalian-biaya_pemakaian").val(docoHelper.convertToRupiah(biaya))
            hitung()
        });

        function hitung(){
            var tagihanTindakan = docoHelper.convertToAngka($(".estimasi_biaya_tindakan").text())
            var tagihanObat = docoHelper.convertToAngka($(".estimasi_biaya_obat").text())
            var tagihanJarak = docoHelper.convertToAngka($("#formpengembalian-biaya_pemakaian").val())
            var tot = parseInt(isNaN(tagihanTindakan) ? 0 : (tagihanTindakan)) + parseInt(isNaN(tagihanObat) ? 0 : (tagihanObat)) + parseInt(isNaN(tagihanJarak) ? 0 : (tagihanJarak))
            $("#total_biaya").val(docoHelper.convertToRupiah(tot))
        }

        $("#daftartindakan_id_rs").select2InfinityScroll({
            url: "/ambulan/informasi-pemakaian-ambulan/cari-tindakan?id='.$id.'",
            callbackProccess: (data) => {
                let resultProccess = {
                    pagination: data.pagination,
                    results: []
                }
                data.results.map((itemData) => {
                    resultProccess.results.push({
                        id: itemData.daftartindakan_id,
                        text: itemData.daftartindakan_nama,
                        ...itemData,
                        disable: itemData.status == 1
                    })
                })
                return resultProccess
            }
        })

        $("#daftartindakan_id_rs").on("select2:select", () => {
            $(".tarif_tindakan_rs").val($("#daftartindakan_id_rs").select2("data")[0]["harga_tariftindakan"])
        })

        _tableTindakan = $("#tabel-temp-tindakan-rs").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[1, "asc"]],
            ajax: baseUrl+"ambulan/informasi-pemakaian-ambulan/get-list-tindakan?id='. $id .'" ,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Nama Tindakan", 
                    data: "daftartindakan_nama",
                    orderable: false
                },
                {
                    title: "Tarif",
                    data: "harga",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Qty",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Cyto",
                    data: "cyto",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Sub Total",
                    data: "sub_total",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var estimasi_biaya_tindakan = 0;
                $.each(dataRows, function (key, val) {
                    estimasi_biaya_tindakan += parseInt(val.sub_total);
                });
                $(".estimasi_biaya_tindakan").text(docoHelper.convertToRupiah(estimasi_biaya_tindakan));
                hitung()
            },
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var stok = parseInt(aData.stok);
                if (stok == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                    $(nRow).find("input").prop("disabled",true);
                } 
            }
        });

        $(document).on("click", ".btn-tambah-tindakan-rs", function (event) {
            event.preventDefault();
            var dataPost = $(\'#add-tindakan\').serializeArray();
            dataPost.push({
                name : "id",
                value : "'.$id.'"
            });
            $(this).docoForm("click", {
                url: "/ambulan/informasi-pemakaian-ambulan/add-tindakan",
                method: "POST",
                type: "json",
                data: dataPost,
                skipConfirm: true,
                success: function (data) {
                    $("#daftartindakan_id_rs").val(\'\').trigger(\'change\');
                    $(".tarif_tindakan_rs").val(\'\')
                    $(".qty_tindakan_rs").val(\'\')
                    $(".cyto_tindakan_rs").val(\'\')
                    _tableTindakan.draw();
                }
            });
        });

        $(document).on(\'click\',\'.delete-cache-tindakan-rs\', function(event) {
            event.preventDefault();
            $(this).docoForm(\'delete\',{
                skipConfirm : true,
                success : function (data) {
                    _tableTindakan.draw();
                }
            });
        });

        $("#obatalkes_id_rs").select2InfinityScroll({
            url: "/ambulan/informasi-pemakaian-ambulan/cari-obat?id='.$id.'",
            callbackProccess: (data) => {
                let resultProccess = {
                    pagination: data.pagination,
                    results: []
                }
                data.results.map((itemData) => {
                    resultProccess.results.push({
                        id: itemData.obatalkes_id,
                        text: itemData.obatalkes_nama,
                        ...itemData,
                        disable: itemData.status == 1
                    })
                })
                return resultProccess
            }
        })

        $("#obatalkes_id_rs").on("select2:select", () => {
            $(".tarif_obat_rs").val($("#obatalkes_id_rs").select2("data")[0]["hargaygdipakai"])
        })

        _tableObat = $("#tabel-temp-obat-rs").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[1, "asc"]],
            ajax: baseUrl+"ambulan/informasi-pemakaian-ambulan/get-list-obat?id='. $id .'" ,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Nama Obat Alkes", 
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "Tarif",
                    data: "harga",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Qty",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Cyto",
                    data: "cyto",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Sub Total",
                    data: "sub_total",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
             drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var estimasi_biaya_obat = 0;
                $.each(dataRows, function (key, val) {
                    estimasi_biaya_obat += parseInt(val.sub_total);
                });
                $(".estimasi_biaya_obat").text(docoHelper.convertToRupiah(estimasi_biaya_obat));
                hitung()
            },
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var stok = parseInt(aData.stok);
                if (stok == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                    $(nRow).find("input").prop("disabled",true);
                } 
            }
        });

        $(document).on("click", ".btn-tambah-obat-rs", function (event) {
            event.preventDefault();
            var dataPost = $(\'#add-obat-alkes\').serializeArray();
            dataPost.push({
                name : "id",
                value : "'.$id.'"
            });
            $(this).docoForm("click", {
                url: "/ambulan/informasi-pemakaian-ambulan/add-obat",
                method: "POST",
                type: "json",
                data: dataPost,
                skipConfirm: true,
                success: function (data) {
                    $("#obatalkes_id_rs").val(\'\').trigger(\'change\');
                    $(".tarif_obat_rs").val(\'\')
                    $(".qty_obat_rs").val(\'\')
                    $(".cyto_obat_rs").val(\'\')
                    _tableObat.draw();
                }
            });
        });

        $(document).on(\'click\',\'.delete-cache-obat-rs\', function(event) {
            event.preventDefault();
            $(this).docoForm(\'delete\',{
                skipConfirm : true,
                success : function (data) {
                    _tableObat.draw();
                }
            });
        });

        $("#simpan").on("click", function(event){
        event.preventDefault();
        form = $("#pemakaian-form").serializeArray().concat([{ name: "FormPengembalian[total_biaya]", value: $("#total_biaya").val() }]),

            $(this).docoForm("click", {
                url : "/ambulan/informasi-pemakaian-ambulan/simpan-pemakaian-ambulan?id='. $id .'",
                method : "POST",
                type : "json",
                data: form,
                success : function (data) {
                    setTimeout(function(){
                        $("#simpan").prop(\'disabled\', true);
                    }, 100);

                    docoNotification(\'success\', \'Simpan Data Berhasil!\', \'Berhasil Melakukan Pengembalian Ambulan!\');

                    
                        window.location.replace("/ambulan/informasi-pemakaian-ambulan/");
                    
                }
            });
        });

    });
', View::POS_END, 'b-index');
