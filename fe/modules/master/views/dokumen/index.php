<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'action' => '/master/dokumen/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'id' => 'btn-edit',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-url' => '/master/dokumen/update?id=',
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'data-additional' => 'data-rm'
                        ]
                    ],
                ], '#example'); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?= \Yii::t("fe", "Nama Dokumen"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis Dokumen"); ?></th>
                            <th><?= \Yii::t("fe", "Status"); ?></th>
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
            stateSave: false,
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dokumen/get-data",
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
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, rowElement, rowData, rowAdditionalData) => {
                        var tableInfo = table.page.info()
                        return tableInfo.start + rowAdditionalData.row + 1
                    }
                    
                },
                {title: "' . (\Yii::t("fe", "Nama Dokumen")) . '",  data: "nama_dokumen"},
                {title: "' . (\Yii::t("fe", "Jenis Dokumen")) . '",  data: "jenis_dokumen_nama", name: "jenis_dokumen_id"},
                {title: "' . (\Yii::t('fe', 'Status')) . '", data: "is_active"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [3, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_dokumen_id', '', $jenisDokumen, ['class' => 'form-control select2', 'prompt' => 'Pilih']))) . '\'],
            [4, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2']))) . '\']
        ]);
        
        $("#example tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit").attr("action",$("#btn-edit").data("url")+primaryKey);
            } else {
                $("#btn-edit").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }
        });
    });
', View::POS_END, 'b-index');
?>