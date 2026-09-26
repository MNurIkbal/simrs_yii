<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-09 11:00:02
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-03 17:12:58
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
    }
    p.barang_merk {
        font-size: 15px;
        padding-left: 5px;
        font-weight: 500;
    }
    .panel-heading{
        margin-bottom: 10px;
    }
    legend {
        font-weight: bold !important;
        padding: 5px;
    }
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
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-simpan-form',
                    ]);
                ?>
                <?=
                    DocoHelpers::generateToolbar([
                        'back',
                        'reset',
                    ]);
                ?>
            </div>

            <div class="panel-body">
                <div>

                </div>

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Form').' '.$this->title ?></b></h6>
                        </div>
                        <div class="panel-body">
							<?php $form = ActiveForm::begin([
                                'id' => 'form', 
                                'action' => "/kasir/kontrak-manajemen/create",
                                'enableAjaxValidation'=>false, 
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL] 
                            ]); 
                            ?>

                            <div class="row">
                                <div class="col-md-6">
                                <?= $form->field($model, 'penjamin_id')->dropDownList($penjamin,[
						                'class' => 'form-control select2',
                                        'prompt' => Yii::t('fe', '— Pilih Nama Penjamin —'),
						                'id' => 'penjamin_id',
						            ])->label(Yii::t('fe', 'Nama Penjamin')); ?>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=$form->field($model, 'no_kontrak')?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=$form->field($model, 'nama_kontrak')?>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                                                         <?= $form->field($model, 'tgl_mulai', [
                                                        'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-3',
                                                            'wrapper' => 'col-md-9'
                                                        ]
                                                    ])->widget(DatePicker::classname(), [
                                                        'name' => 'date_12',
                                                        'value' => date('Y-m-d'),
                                                        'readonly' => true,
                                                        'language' => 'en',
                                                        'pluginOptions' => [
                                                            'autoclose' => true,
                                                            'format' => 'dd-M-yyyy',                                                            
                                                        ]
                                                    ]); ?>
                                                    <?= $form->field($model, 'tgl_selesai', [
                                                        'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-3',
                                                            'wrapper' => 'col-md-9'
                                                        ]
                                                    ])->widget(DatePicker::classname(), [
                                                        'name' => 'date_12',
                                                        'value' => date('Y-m-d'),
                                                        'readonly' => true,
                                                        'language' => 'en',
                                                        'pluginOptions' => [
                                                            'autoclose' => true,
                                                            'format' => 'dd-M-yyyy',                                                            
                                                        ]
                                                    ]); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <?= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', ' Tambah Grade'), 
                                        [
                                            'class' => 'btn btn-info btn-labeled btn-xs',
                                            'id' => 'btn-tambah',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal_backdrop',
                                            'action' => '/kasir/kontrak-manajemen/create-grade?penjamin_ids=',
                                        ]);
                                    ?>
                                </div>
                            </div>
                            <br/>
                            <!-- Tarif Tindakan -->
                            <table id="GradePenjamin" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th><?=\Yii::t("fe", "Grade");?></th>
                                        <th><?=\Yii::t("fe", "LOB");?></th>
                                        <th><?=\Yii::t("fe", "Tipe Diskon");?></th>
                                        <th><?=\Yii::t("fe", "Aksi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>       
                            <!-- Obat Alkes -->                             
			                <?php ActiveForm::end(); ?>
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
    var tableGradePenjamin;
    var groupColumn = 1;

    $(document).ready(function(){
        $("#btn-tambah").hide();        
    });

    dateRangeHelper(".startDate",".endDate");
    tableGradePenjamin = $("#GradePenjamin").docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        lengthChange: false,
        paging: false,
        columnDefs: [
            { visible: false, targets: groupColumn }
        ],
        order: [[groupColumn, "asc"]],
        ajax: baseUrl+"kasir/kontrak-manajemen/get-list-grade",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Grade", 
                data: "grade",
            },
            {
                title: "LOB", 
                data: "lookup_name",
                orderable: false,
            },
            {
                title: "Tipe Diskon",
                data: "tipediskon_nama",
                searchable: false,
                orderable: false,
            },
            {
                title: "Aksi",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
        drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:"current"} ).nodes();
            var last=null;
 
            api.column(groupColumn, {page:"current"} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        `<tr class="group" style="background-color:#d9d9d9"><td colspan="5" style="font-weight:bold">GRADE : ${group}</td></tr>`
                    );
 
                    last = group;
                }
            } );
        }
    });
    $(document).on("click",".delete-cache", function(event) {
        event.preventDefault();
        var id = $(this).data("id");
        var action = $(this).data("action");
        var button = this;
        var valButton = $(button).html();
        var ResData = {};
        console.log(action);
        $(this).docoForm("click",{
            url: action,
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
            data: ResData,
            method: "GET",
            before: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function () {
                tableGradePenjamin.draw();
                $(button).parent().parent().remove();
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));

            }
        });
    });

    $("#penjamin_id").change(function(){
        var penjamin_ids = $(this).find("option:selected").val();
        if(penjamin_ids){
            $("#btn-tambah").show();
            $("#btn-tambah").attr("action", "/kasir/kontrak-manajemen/create-grade?penjamin_ids="+penjamin_ids );
        } else{
            $("#btn-tambah").hide();
        }
    });
    
    $(document).on("click", "#btn-simpan-form", function (event) {
        var _data = $("#form").serializeArray();
            $("#form").docoForm("submit",{
                data : _data,
                success : function (data) {
                    docoResetForm($("#form"));
                    window.location.href = "/kasir/kontrak-manajemen";
                }
            });
            $("#form").trigger("submit");
        // }
    });    
', View::POS_END, 'b-index');
?>