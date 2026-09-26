<?php

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
                    <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'button-simpan',
                            'disabled' => $isDisable
                        ]);
                    ?>
                    <?=DocoHelpers::generateToolbar([
                        'reset' => [
                            'attributes' => [
                                'disabled' => $isDisable
                            ]
                        ],
                        'back'
                    ],'#table-informasi');?>
            </div>

            <div class="panel-body">
                <br>
                <div class="row">
                    <?php 
                        $form = ActiveForm::begin([
                            'id' => 'ajax-form', 
                            'action' => '/apotek/pemakaian-obat-alkes/set-list-item',
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
                    <div class="form-group">
                    <div class="">
                        <div class="col-md-4">
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
                                                'readonly' => true
                                        ]); ?>
                        </div>
                    </div>
                    <div class="">
                        <div class="col-md-4">
                            <?= $form->field($model, 'pegawai_meyetujui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4 required',
                                        'wrapper' => 'col-md-8'
                                    ],
                                    'options' => [
                                        'tag' => false, // Don't wrap with "form-group" div
                                    ],
                                ])->dropDownList($pegMenyetujui,[
                                    'class' => 'select2',
                                    'id' => 'list-pegawai_meyetujui',
                                    'prompt' => Yii::t('fe', 'Pilih Pegawai')
                                ])->label(\Yii::t('fe', 'Pegawai Menyetujui')); ?>
                        </div>
                    </div>
                    <div class="">
                        <div class="col-md-4">
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
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <br>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th>
                                <?= \Yii::t("fe", "Nama Obat Alkes"); ?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Tanggal Expired");?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Qty");?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Satuan Kecil");?>
                            </th>
                            <th class="text-right">
                                <?=\Yii::t("fe", "Jumlah Harga Netto (Rp.)");?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            $totalHarga = 0;
                            if (!empty($getListBarang)) :
                                foreach ($getListBarang as $value) :
                                    $totalHarga += $value['jumlah_harganetto'];
                        ?>
                                    <tr>
                                        <td>
                                            <?= $no ?>
                                        </td>
                                        <td>
                                            <?= $value['obatalkes_nama'] ?>
                                        </td>
                                        <td>
                                            <?= date('d-M-Y',strtotime($value['tglkadaluarsa'])) ?>
                                        </td>
                                        <td>
                                            <?= DocoHelpers::formatNumber($value['stok']) ?>
                                        </td>
                                        <td>
                                            <?= $value['satuan_kecil'] ?>
                                        </td>
                                        <td class="text-right">
                                            <?= DocoHelpers::formatNumber($value['jumlah_harganetto']) ?>
                                        </td>
                                    </tr>
                            <?php
                                    $no++;
                                endforeach;
                            else :
                        ?>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                        <?php
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 
$curentUrl = Yii::$app->request->url;
$this->registerJs('
    $("#button-simpan").on("click",function (event) {
        event.preventDefault();
        var _data = $("#ajax-form").serializeArray();
        _data.push({
            name : "total_netto",
            value : '.$totalHarga.'
        });
        $(this).docoForm("click",{
            confirmMessage: "Pemesanan yang berkaitan dengan obat alkes yang tertera pada list pemusnahan obat alkes akan secara otomatis dibatalkan, anda yakin untuk melanjutkan?",
            url : "/apotek/pemusnahan-obat/save",
            method : "POST",
            type : "json",
            data : _data,
            success : function (data) {
                setTimeout(function(){ $("#button-simpan").prop("disabled", true); }, 100);
                let _action = "/apotek/pemusnahan-obat/export-print?id="+data.response.parent_id;
                (new PNotify({
                        title: "Berhasil",
                        text: "Pemusnahan Obat dengan Nomor " + "<strong>" + data.response.nopemusnahan + "</strong>" + " telah berhasil, apakah Anda ingin melakukan cetak?",
                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                        type: "success",
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        hide: false,
                        confirm: {
                            confirm: true,
                            buttons: [
                                {
                                    text: "Ya",
                                    addClass: "btn btn-xs btn-success",
                                },
                                {
                                    text: "Tidak",
                                    addClass: "btn btn-xs btn-danger",
                                }
                            ]
                        },
                        history: {
                            history: false
                        }
                    })).get().on("pnotify.confirm", function() {
                        // Print
                        window.open(_action);
                    }).on("pnotify.cancel", function() {
        
                    });
            }
        });
    });

    $(document).on("click","#btn-print", function (event) {
        event.preventDefault();
        var _url = $(this).attr("data-target");
        window.open(_url);
    });

    var _checkDisabled = function (id) {
        setTimeout(function(){ $("#button-simpan").prop("disabled",true); }, 1000);
        $(".data-reset").prop("disabled",true);
        $("#btn-print").prop("disabled",false);
        $("#btn-print").attr("data-target","/apotek/pemusnahan-obat/export-print?id="+id);
        $("#ajax-form").find("input,select").prop("disabled",true);
    }
    var _idParent = "'.$id.'";
    $(function(){
        var d = new Date();
        $(".pickadate").pickadate({
            format: "dd-mmm-yyyy",
            formatSubmit: "yyyy-mm-dd",
            max: [d.getFullYear(),d.getMonth(),d.getDate()],
            clear: false,
            onStart: function() {
                var date = new Date();
                // this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });

        if (_idParent) {
            _checkDisabled(_idParent);
        }

        $(".get-pegawai").select2({
            placeholder: "'. \Yii::t("fe", "Pilih") .'",
            minimumInputLength: 3, 
            ajax : {
                url: baseUrl+"apotek/pemusnahan-obat/get-pegawai",
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
            cache: true
        });
    });
',View::POS_END,'b-index');