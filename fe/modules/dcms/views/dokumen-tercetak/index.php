<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', $this->context->_title);
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
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
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'tambah' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-tambah',
                                'id' => 'tambah',
                                'data-target' => '/dcms/dokumen-tercetak/create',
                                'data-options' => 'link'

                            ]
                        ],
                        'ubah' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-ubah',
                                'data-target' => '/dcms/dokumen-tercetak/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                    ],'#table-dokumen');?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-dokumen" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Kode Dokumen");?></th>
                            <th><?=\Yii::t("fe", "Nama Dokumen");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kertas");?></th>
                            <th><?=\Yii::t("fe", "Jenis Header");?></th>
                            <th><?=\Yii::t("fe", "Jenis Footer");?></th>
                            <th><?=\Yii::t("fe", "Service");?></th>
                            <th><?=\Yii::t("fe", "Menu");?></th>
                            <th><?=\Yii::t("fe", "Dokumen");?></th>
                            <?php if($is_new_report){ ?>
                            <th><?=\Yii::t("fe", "Viewer");?></th>
                            <th><?=\Yii::t("fe", "Designer");?></th>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
// Global Var
var table;
var dataCollect;
var is_new_report = '.$is_new_report.';
$(function(){
    var defaultColumns = [
        {
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        {title: "'.(\Yii::t("fe", "Kode Dokumen")).'", data: "kode_doc"},
        {title: "'.(\Yii::t("fe", "Nama Dokumen")).'", data: "nama_doc"},
        {
            title: "'.(\Yii::t("fe", "Jenis Kertas")).'",
            data: "kertas_nama",
            name:"kertas_k.kertas_nama",
            searchable: false,
            searchable: false
        },
        {
            title: "'.(\Yii::t("fe", "Jenis Header")). '",
            data: "header_nama",
            name:"docheader_k.nama_header",
            searchable: false
        },
        {
            title: "'.(\Yii::t("fe", "Jenis Footer")). '",
            data: "footer_nama",
            name:"docfooter_k.nama_footer",
            searchable: false
        },
        {
            title: "'.(\Yii::t("fe", "Service")).'",
            data: "service",
            orderable: false,
            searchable: false
        },
        {
            title: "'.(\Yii::t("fe", "Menu")).'",
            data: "menu",orderable: false,
            searchable: false
        },
        {
            title: "'.(\Yii::t("fe", "Dokumen")).'",
            data: "dokumen",orderable: false,
            searchable: false
        }

    ];
    var reportColumns = [
        {
            title: "'.(\Yii::t("fe", "Viewer")).'",
            data: "viewer",
            orderable: false,
            searchable: false
        },
        {
            title: "'.(\Yii::t("fe", "Designer")).'",
            data: "designer",
            orderable: false,
            searchable: false
        }
    ];
    if(is_new_report){
        var columns = defaultColumns.concat(reportColumns);
    }else{
        var columns = defaultColumns;
    }
    table = $("#table-dokumen").docoTabel({
        filter: true,
        columnDefs: [{
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
        scrollX: true,
        ajax: baseUrl+"dcms/dokumen-tercetak/get-data",
        columns: columns
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table);
});

',View::POS_END,'b-index');
