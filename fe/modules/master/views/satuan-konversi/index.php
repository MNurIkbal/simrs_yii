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
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'edit' => [
                        'title' => Yii::t('fe', 'Detail'),
                        'icon' => 'fa fa-list-ul',
                        'attributes' => [
                            'data-target' => '/master/satuan-konversi/create?id=',
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Kode Obat Alkes') ?></th>
                            <th><?= Yii::t("fe", "Nama Obat Alkes") ?></th>
                            <th><?= Yii::t("fe", "Satuan Terkecil / Penyimpanan") ?></th>
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
var table;
var tableKonversi;
var attributes = {};
$(document).ready(function() {
    table = $("#example").docoTabel({
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
        ajax: baseUrl+"master/satuan-konversi/get-data",
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
                title: "'.(\Yii::t("fe", "Kode Obat Alkes")).'",  
                data: "obatalkes_kode",
                searchable: false
            },            
            {
                title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",  
                data: "obatalkes_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Satuan Terkecil / Penyimpanan")).'",  
                data: "satuan_kecil",
            },
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, 
        [
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('obatalkes_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Obat Alkes')]))).'\'
            ],
            [
                4, 
                \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('satuan_kecil', '', 
                        $satuan, 
                        [
                            'id' => '', 
                            'class' => 'form-control select2', 
                            'prompt' => Yii::t('fe', '— Pilih Satuan Terkecil / Penyimpanan —'),
                        ]
                    )
                )).'\'
            ]
        ]
    );

    $(".selectObat").select2({
        placeholder: "Pilih Obat Alkes",
        minimumInputLength: 3, 
        ajax : {
            url: "/master/satuan-konversi/search-obat-alkes",
            dataType: "json",
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
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });

    $("#form").submit(function(event){
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
                $("#satuankonversiform-satuanbesar_id").val("");
                $("#satuankonversiform-nilai_konversi").val("");
                table.draw();
            }
        });
    });
});
', View::POS_END, 'b-index');
