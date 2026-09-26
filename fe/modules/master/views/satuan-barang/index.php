<?php

/**
 * @Author: Budi
 * @Date:   2018-04-24 11:36:33
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = $title;
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    "search"=> [
                        'attributes'=>[
                            'id' => 'btn-search',
                        ]
                    ],
                    'reset'=> [
                        'attributes'=>[
                            'id' => 'btn-reset',
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/satuan-barang/create',
                        ]
                    ],
                    // 'detail' => [
                    //     'title' => Yii::t('fe', 'Lihat'),
                    //     'attributes' => [
                            
                    //         'data-options'=>'modal',
                    //         'data-target'=>'#modal_backdrop',
                    //         'data-url' => '/master/satuan-barang/detail?id=',
                    //     ]
                    // ],
                    'delete' => [
                        'attributes' => [
                            'id'=>'btn-hapus',
                            // 'disabled'=>'true',
                            'data-additional'=>'data-rm',
                        ]
                    ],
                    // 'pdf',
                    // 'excel',
                ], '#tabel-satuan') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tabel-satuan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Satuan") ?></th>
                            <th><?= Yii::t("fe", "Nama Lain") ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

    var tableSatuan;
    $(document).ready(function() {
        tableSatuan = $("#tabel-satuan").docoTabel({
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
            ajax: baseUrl+"master/satuan-barang/get-data",
            columns: [
                {
                    title: "", 
                    data: null, 
                    defaultContent: "", 
                    searchable: false, 
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan")).'",  
                    data: "satuanunit_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Lain")).'", 
                    data: "satuanunit_namalain",
                }
            ],
        });
        $(".dataTables_filter").hide();

         $(".filter-form").datatableBootstrapFilter(tableSatuan, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('satuanunit_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Satuan')]))).'\'
            ],
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('satuanunit_namalain', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Lain')]))).'\'
            ]
        ]);
        //$(".filter-form").datatableBootstrapFilter(tableSatuan);

        $(".selectNama").select2({
            placeholder: "",
            minimumInputLength: 3,                  
            ajax: {
                url: "/master/satuan-barang/get-satuan?tipe=1",
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

        $(".selectNamaLain").select2({
            placeholder: "",
            minimumInputLength: 3,                  
            ajax: {
                url: "/master/satuan-barang/get-satuan?tipe=2",
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

        $("#tabel-satuan tbody").on("click", "tr", function () {
            try {
                primaryKey = tableSatuan.row(".selected").data().primary ? tableSatuan.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }
            if (primaryKey) {
                $.ajax({
                    url: "/master/satuan-barang/check-transaction?id=" + primaryKey,
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

    function btnEditHapus(val){
        if(val != 200){
            $("#btn-hapus").prop("disabled",true);
        } else {
            $("#btn-hapus").prop("disabled",false);
        }
    }

', View::POS_END, 'b-index');
