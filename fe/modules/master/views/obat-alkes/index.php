<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-05 09:15:36
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-19 16:07:58
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DHtml;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'detail'=>[
                            'title'=> Yii::t('fe', 'Lihat'),
                            'icon'=>'fa fa-eye',
                            'attributes' => [
                                'id'=>'btn-detail',
                                'class' => 'btn btn-info btn-labeled btn-xs data-detail btn-toolbar '.(DHtml::cekHakAkses('edit') ? '' : 'hidden'),
                                // 'disabled'=> DHtml::cekHakAkses('edit') ? 'true' : 'false',
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'id'=>'btn-add',
                                'class' => 'btn btn-info btn-labeled btn-xs data-detail btn-toolbar '.(DHtml::cekHakAkses('create') ? '' : 'hidden'),
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id'=>'btn-hapus',
                                'class' => 'btn btn-info btn-labeled btn-xs data-detail btn-toolbar '.(DHtml::cekHakAkses('delete') ? '' : 'hidden'),
                                'data-additional'=>'data-rm',
                            ]
                        ],
                        // 'pdf',
                        // 'excel',
                    ],'#table-obatalkes');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-obatalkes" class="table table-striped table-condensed table-hover">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Jenis obat alkes");?></th>
                            <th><?=\Yii::t("fe", "Ven");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs('
    var table;
    
    $(document).ready(function() {
        $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
            $(this).attr("data-state", state);
            var dataId = $(this).attr("data-id");
            var that = $(this);
            var header = "'.(\Yii::t("fe", "Konfirmasi")).'";
            var message = "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'";
            var label = {buttons: { Yes: "button-yes", No: "button-no"}, hidden: true};
            $.showQuestionDialog(header, message, label, function (reaction) {
                showReaction(reaction, that, function(str) {
                    hideIt();
                });
            });
        });
        
        function showReaction(str, that, callback) {
            var dataId = that.attr("data-id");
            var dataState = that.attr("data-state");
            var dataStatus = "1";
            if (dataState == "false") {
                dataStatus = "0";
            }
            //jika pilih No
            if(dataState == "false") {
                dataState = true;
            } else {
                dataState = false;
            }
        
            if (str == "Yes") {
                $.ajax({
                    url: "/master/obat-alkes/change-status?id="+dataId+"&status="+dataStatus,
                    type: "POST",
                    dataType: "json",
                    success : function(data) {
                        new PNotify({
                          title: data.response.title,
                          text: data.response.text,
                          addclass: "alert alert-success alert-arrow-right alert-styled-right",
                          type: "success",
                        });
                        hideIt();
                    },
                    error : function(data) {
                        new PNotify({
                          title: data.responseJSON.response.title,
                          text: data.responseJSON.response.text,
                          addclass: "alert alert-success alert-arrow-right alert-styled-right",
                          type: "error",
                        });
                        that.bootstrapSwitch("state", dataState);
                        hideIt();
                    }
                });
            } else {
                that.bootstrapSwitch("state", dataState);
            }
            callback(str);
        }
        
        function hideIt() {
            $("#confirm-dialog-overlay").remove();
            $("#confirm-dialog").remove();
            $("#confirm-dialog-overlay").remove();
            $("#confirm-dialog").remove();            
        }
            
        // Generate Table
        table = $("#table-obatalkes").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "'.Url::to(['get-data']).'",
            drawCallback: function(settings) {
                $(".switch").bootstrapSwitch();
            },            
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",
                    data: "obatalkes_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis obat alkes")).'",
                    data: "jenisobatalkes_nama",
                    name: "jenisobatalkes_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Ven")).'",
                    data: "ven_name",
                    name: "ven"
                },
                {
                    title: "Status",
                    data: "is_active",
                    orderable: false, class: "text-center"
                },
                {
                    title: "Kode Obat",
                    data: "obatalkes_kode",
                    searchable: true,
                    visible: false, orderable: false
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('obatalkes_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Obat Alkes')]))).'\'
                ],
                [
                    6, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('obatalkes_kode', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Kode Obat Alkes')]))).'\'
                ],
                [
                    3,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenisobatalkes_nama', '', $data_obat,
                            [
                                'class' => 'form-control select2',
                                'prompt' => Yii::t('fe', '— Pilih Jenis Obat Alkes —'),
                            ]
                        )
                    )).'\'
                ],
                [
                    4,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ven', '', $data_ven,
                            [
                                'class' => 'form-control select2',
                                'prompt' => Yii::t('fe', '— Pilih Ven —'),
                            ]
                        )
                    )).'\'
                ],
            ], {2:0, 6:1, 3:2, 4:3});
        $(".selectObat").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "/master/obat-alkes/get-obat",
                dataType: "json",
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
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
        $("#table-obatalkes tbody").on("click", "tr", function () {
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $.ajax({
                    url: "/master/obat-alkes/check-transaction?id=" + primaryKey,
                    type: "get",
                    beforeSend: function () {
                    },
                    success: function (res) {
                        btnEditHapus(res);
                    },
                    error: function (res) {
                        btnEditHapus(res);
                    },
                    complete: function() {
                    }
                });
            } else {
                return false;
            }
        });
    });
    var disableButton = function(){
        $(".data-delete").prop("disabled", true)
    }

    function btnEditHapus(val){
        if(val != 200){
            $("#btn-hapus").prop("disabled",true);
        } else {
            $("#btn-hapus").prop("disabled",false);
        }
    }

    ', View::POS_END, 'js');

?>
