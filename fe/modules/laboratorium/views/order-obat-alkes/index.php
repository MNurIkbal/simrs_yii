<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$id = DocoHelpers::encrypt($id);
$this->title = Yii::t('fe', 'Pemakaian Obat / Alkes Non - Reseptur');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Input Hasil'),
        'url' => ['/laboratorium/hasil-lab?id='.$id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pemakaian Obat / Alkes Non - Reseptur'), 'url' => ['index']];


?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                  <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), [
                    'class' => 'btn btn-labeled btn-xs btn-info',
                    'id' => 'btn-save'
                    ]) ?>
                <?= Html::a('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), '/laboratorium/hasil-lab?id='.$id, [
                    'class' => 'btn btn-labeled btn-xs btn-info',
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Pemakaian Obat / Alkes Non - Reseptur</b></h6>
                        </div>

                        <div class="panel-body">
                            <?php
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form',
                                    'action' => '/laboratorium/order-obat-alkes/add-obat?id='.$id,
                                    'enableAjaxValidation'=>false,
                                    'enableClientValidation'=>false,
                                    'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => [
                                        'labelSpan' => 3,
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [
                                        'skip-confirm' => "true"
                                    ]
                                ]);
                            ?>
                            <?= $form->field($model, 'tindakan_id',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-2',
                                            'wrapper' => 'col-md-4'
                                        ]
                                    ])->dropDownList($list_pemeriksaan,[
                                            'class' => 'select2',
                                            'id' => 'tindakan_id',
                                            'prompt' => Yii::t('fe','--Pilih--')
                                            ]);
                            ?>
                            <?= $form->field($model, 'obatalkes_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-2',
                                        'wrapper' => 'col-md-4'
                                    ],
                                ])->dropDownList([],[
                                    'class' => '',
                                    'id' => 'list-obatalkes_id',
                                    'data-kelas' => isset($info_pasien['kelaspelayanan_id']) ? $info_pasien['kelaspelayanan_id'] : null,
                                    'data-penjamin' => isset($info_pasien['penjamin_id']) ? $info_pasien['penjamin_id'] : null,
                                    'prompt' => Yii::t('fe','--Pilih--')
                                ]); ?>
                            <div class="form-group field-pemakaian-barang-qty required">
                                <label class="control-label text-left control-label col-sm-2" for="pemakaian-barang-qty">Jumlah</label>
                                <div class="input-group col-md-3">
                                    <div class="col-md-6">
                                        <input type="text"
                                        id="pemakaian-barang-qty"
                                        class="form-control input-sm doco-number text-right"
                                        name="OrderObatAlkesForm[qty]"
                                        placeholder="Jumlah"
                                        autocomplete="off"
                                        aria-required="true">
                                        <div id="error_OrderObatAlkesFormqty"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkbox">
                                            <!-- <input type="hidden" name="OrderObatAlkesForm[is_tagihkan]" value="0"> --><label>
                                            <input type="checkbox"
                                            id="is_tagihkan"
                                            name="OrderObatAlkesForm[is_tagihkan]"
                                            class="styled"
                                            value="1" aria-invalid="false">
                                                <b><?= Yii::t('fe','Tagihkan ke Pasien') ?></b>
                                            </label></div>
                                    </div>
                                </div>
                            </div>

                            <?= $form->field($model, 'jumlah_tarif', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-2'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('jumlah_tarif'),
                                            'class' => 'form-control input-sm text-right',
                                            'autocomplete' => "off",
                                            'id' => 'pemakaian-barang-jumlah_tarif',
                                            'readonly' => true
                                        ]); ?>
                            <?= $form->field($model, 'petugas_satu',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-2',
                                            'wrapper' => 'col-md-3'
                                        ]
                                    ])->dropDownList($list_pegawai,[
                                            'class' => 'select2',
                                            'id' => 'petugas_satu',
                                            'prompt' => Yii::t('fe','--Pilih--')
                                            ]);
                            ?>
                            <?= $form->field($model, 'petugas_dua',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-2',
                                            'wrapper' => 'col-md-3'
                                        ]
                                    ])->dropDownList($list_pegawai,[
                                            'class' => 'select2',
                                            'id' => 'petugas_dua',
                                            'prompt' => Yii::t('fe','--Pilih--')
                                            ]);
                            ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Pemakaian Obat/Alkes Non - Reseptur</b></h6>
                        </div>

                        <div class="panel-body">
                            <table id="pemakaian-obat-alkes"
                            class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Tanggal Tindakan");?></th>
                                        <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                                        <th><?=\Yii::t("fe", "Obat/Alkes");?></th>
                                        <th><?=\Yii::t("fe", "Petugas 1");?></th>
                                        <th><?=\Yii::t("fe", "Petugas 2");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Ditagihkan");?></th>
                                        <th><?=\Yii::t("fe", "Hapus");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="9">
                                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    var table;
    var _tmp = {};
    var _obatAlkes = {};
    var _statusSelesai = "'.DocoConstants::ST_SELESAI_PNNJG.'";


    var _checkStatus = function () {
        var _currentStatus = "'. $status .'";
        if (_statusSelesai === _currentStatus) {
            $("#ajax-form").find("input, select").prop("disabled",true);
            $("#btn-save, #btn-ulang").prop("disabled",true);
        }
    }

    $("#btn-save").on("click", function (event) {
        event.preventDefault();
        var _form = $("#ajax-form");
        var _data = _form.serializeArray();
        _data.push({
            name : "stok",
            value : _obatAlkes.stok
        });
        $().docoForm("click",{
            data : _data,
            url : _form.attr("action"),
            success : function (data) {
                table.draw();
                _muatUlang();
                location.reload();
            }
        });
    });

    $(document).on("click",".delete" , function (event) {
        event.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                table.draw();
            }
        });
    });

    $("#btn-ulang").on("click", function (event) {
        event.preventDefault();
        _muatUlang();
    });

    var _muatUlang = function () {
        docoResetForm($("#ajax-form"));
        $("#list-obatalkes_id").val("").trigger("change");
    }

    $("#pemakaian-barang-qty").on("keyup change", function (event) {
        event.preventDefault();
        var _val = docoHelper.convertToAngka($(this).val());
        var _idObatAlkes = $("#list-obatalkes_id").val();
        if (typeof _tmp[_idObatAlkes] != "undefined") {
            _obatAlkes = _tmp[_idObatAlkes];
            if (_obatAlkes.stok < _val) {
                $(this).val(_obatAlkes.stok);
                return true;
            }

            var result = docoHelper.convertToRupiah(_obatAlkes.harga_netto * _val);
            $("#pemakaian-barang-jumlah_tarif").val(result);
        }

    })

    var _parseObat = function (data) {
        $("#pemakaian-barang-qty").val("");
        $("#pemakaian-barang-jumlah_tarif").val("");
        $.each(data, function (key, val) {
            _tmp[val.id] = val;
        })
    }

    $(document).ready(function(){
        $(".styled, .multiselect-container input").uniform({
                    radioClass: \'choice\'
        });
        _checkStatus();
        var _objObat = $("#list-obatalkes_id");
        var _kelasId = _objObat.data("kelas");
        var _penjamin = _objObat.data("penjamin");
        $("#list-obatalkes_id").select2({
            placeholder: "--Pilih--",
            minimumInputLength: 3,
            ajax: {
                url: `/laboratorium/end-point/get-obat-alkes?kelas_id=${_kelasId}&penjamin_id=${_penjamin}`,
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                },
                success : function (data) {
                    _parseObat(data.result)
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        table = $("#pemakaian-obat-alkes").docoTabel({
            filter: true,
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/order-obat-alkes/get-data?id='.$id.'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    sortable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Tindakan")).'",
                    data: "tglpelayanan",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Tindakan")).'",
                    data: "daftartindakan_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Obat/Alkes")).'",
                    data: "obatalkes_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Petugas 1")).'",
                    data: "nama_pegawai"
                },
                {
                    title: "'.(\Yii::t("fe", "Petugas 2")).'",
                    data: "nama_pegawai_dua"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty")).'",
                    data: "qty_oa"
                },
                {
                    title: "'.(\Yii::t("fe", "Ditagihkan")).'",
                    data: "ditagihkan",
                    searchable: false,
                    "class":"text-center",
                    sortable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Hapus")).'",
                    data: "aksi",
                    searchable: false,
                    "class":"text-center",
                    sortable: false
                },
            ]
        });
        $(".dataTables_filter").hide();
    });
',View::POS_END,'pemakaian-obat-alkes');