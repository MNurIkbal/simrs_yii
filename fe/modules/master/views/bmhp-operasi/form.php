<?php

/**
 * @author Yaya
 * @copyright 3 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Bedah Sentral'), 'url' => ['']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $title), 'url' => ['index']];

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
                <?=
                    DocoHelpers::generateToolbar([
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'simpan-bmhp'
                            ],
                        ],
                        'back',
                        // 'reset',
                    ]);
                ?>
            </div>
            <div class="panel-body" style="min-height: 400px;">
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= $title ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form', 
                                    'action' => '/master/bmhp-operasi/set-list-item',
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
                                echo Html::hiddenInput('PaketBmhpForm[obatalkes_nama]', $model->obatalkes_nama, [
                                    'class' => 'obatalkes_nama'
                                ]);
                                echo Html::hiddenInput('PaketBmhpForm[satuankecil_id]', $model->satuankecil_id, [
                                    'class' => 'satuankecil_id'
                                ]);
                                echo Html::hiddenInput('PaketBmhpForm[operasi_nama]', $model->operasi_nama, [
                                    'class' => 'operasi_nama'
                                ])
                            ?>
                            <?= $form->field($model, 'daftartindakan_id',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'daftartindakan_id',
                                    ])->label(Yii::t('fe', 'Nama Operasi')); 
                            ?>
                            <?= $form->field($model, 'obatalkes_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'select2',
                                    'id' => 'obatalkes_id',
                                ])->label(Yii::t('fe', 'Obat Alkes')); ?>
                            
                            <?= $form->field($model, 'satuankecil_nama', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Satuan Kecil'),
                                            'class' => 'form-control input-sm',
                                            'autocomplete' => "off",
                                            'readonly' => true
                                        ])->label(Yii::t('fe', 'Satuan Kecil')); ?>

                            <?= $form->field($model, 'qty_pemakaian', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Qty'),
                                            'class' => 'form-control input-sm doco-number',
                                            'autocomplete' => "off",
                                        ])->label(Yii::t('fe', 'Qty')); ?>
                        <?php if(!$id) : ?>
                        <hr>
                        <div class="btn-group pull-right">
                            <?= Html::submitButton(
                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                    [
                                        'class' => 'btn btn-success btn-labeled btn-xs',
                                        'id' => 'simpan-pemakain-obat'
                            ]) ?>
                        </div>
                        <?php endif; ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <?php if(!$id) : ?>
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Tabel BMHP Operasi</b></h6>
                        </div>

                        <div class="panel-body">
                            <table id="bmhp" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama Operasi");?></th>
                                        <th><?=\Yii::t("fe", "Obat Alkes");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                                        <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="8">
                                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs('
    var table;
    var attributes = {};
    var id = "'.$id.'";
    var daftartindakan_id = "'.$model->daftartindakan_id.'";
    var daftartindakan_nama = "'.$model->operasi_nama.'";
    var obatalkes_id = "'.$model->obatalkes_id.'";
    var obatalkes_nama = "'.$model->obatalkes_nama.'";
    var satuankecil_nama = "'.$model->satuankecil_nama.'";

    $(document).ready(function() {
        var dataTindakan = {
            id: daftartindakan_id,
            text: daftartindakan_nama
        };

        var dataObat = {
            id: obatalkes_id,
            text: obatalkes_nama
        };

        var newOptionTindakan = new Option(dataTindakan.text, dataTindakan.id, false, false);
        var newOptionObat = new Option(dataObat.text, dataObat.id, false, false);
        $("#daftartindakan_id").append(newOptionTindakan).trigger("change");
        $("#obatalkes_id").append(newOptionObat).trigger("change");
        $("#satuankecil_nama").val(satuankecil_nama);

        $("#obatalkes_id").select2({
            placeholder: "'. \Yii::t("fe", "Pilih Obat Alkes") .'",
            minimumInputLength: 3, 
            ajax : {
                url: baseUrl+"master/bmhp-operasi/search-obat-alkes",
                dataType: \'json\',
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
                dropdownCssClass: \'bigdrop\',
                escapeMarkup: function (m) { return m; },
            },
        }).on("select2:select", function(e){
            var data = e.params.data;
            console.log(data);
            $("#paketbmhpform-satuankecil_nama").val(data.satuankecil_nama);
            $(".obatalkes_nama").val(data.text);
            $(".satuankecil_id").val(data.satuankecil_id);
        });
        
        $("#daftartindakan_id").select2({
            placeholder: "'. \Yii::t("fe", "Pilih Nama Operasi") .'",
            minimumInputLength: 3, 
            ajax : {
                url: baseUrl+"master/bmhp-operasi/search-tindakan-operasi",
                dataType: \'json\',
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
                dropdownCssClass: \'bigdrop\',
                escapeMarkup: function (m) { return m; },
            },
        }).on("select2:select", function(e){
            var data = e.params.data;
            console.log(data);
            $(".operasi_nama").val(data.operasi_namalainnya);
        });

        table = $("#bmhp").docoTabel({
            filter: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: baseUrl+"master/bmhp-operasi/get-list-item",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Operasi")).'", 
                    data: "operasi_nama",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Obat Alkes")).'", 
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Qty")).'",
                    data: "qty_pemakaian",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Kecil")).'",
                    data: "satuankecil_nama",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
    });
    
    $("#simpan-bmhp").on("click",function (event) {
        event.preventDefault();
        $(this).docoForm("click",{
            url : "/master/bmhp-operasi/save?id=" + id,
            method : "POST",
            type : "json",
            data: $("#ajax-form").serializeArray(),
            success : function (data) {
                $("#obatalkes_id").val(\'\').trigger(\'change\');
                $("#daftartindakan_id").val(\'\').trigger(\'change\');
                $("#paketbmhpform-satuankecil_id").val(\'\').trigger(\'change\');
                $("#paketbmhpform-qty_pemakaian").val("");
                table.draw();
                window.location.href = "/master/bmhp-operasi";
            }
        });
    });

    $("#ajax-form").submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        if (Object.keys(attributes).length) {
            $.each(attributes, function (key, val) {
                _value.push({
                    name : key,
                    value : val
                });
            });
        } 
        $(this).docoForm("submit",{
            data : _value,
            success : function (data) {
                $("#obatalkes_id").val(\'\').trigger(\'change\');
                $("#daftartindakan_id").val(\'\').trigger(\'change\');
                $("#paketbmhpform-satuankecil_nama").val(\'\');
                $("#paketbmhpform-qty_pemakaian").val("");
                table.draw();
            }
        });
    });

    $(document).on(\'click\',\'.delete\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            success : function (data) {
                table.draw();
            }
        });
    });

',View::POS_END,'js');