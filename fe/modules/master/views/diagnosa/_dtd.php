<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'DTD');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'add' => [
                            'attributes' => [
                                'data-target' => '/master/dtd/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-target' => '/master/dtd/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'data-delete-diagnosa',
                                'data-target' => '/master/dtd/delete?id=',
                                'action' => 'null_id',
                                'data-additional' => 'data-rm',
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-dtd');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-dtd"></div>
                </div>
                <table id="table-dtd" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                          <th width="1%"></th>
                          <th width="5%"><?= Yii::t('fe', 'No'); ?></th>
                          <th><?= Yii::t('fe', 'ID DTD'); ?></th>
                          <th><?= Yii::t('fe', 'Tabular Chapter'); ?></th>
                          <th><?= Yii::t('fe', 'DTD Kode'); ?></th>
                          <th><?= Yii::t('fe', 'Kode Terperinci DTD'); ?></th>
                          <th><?= Yii::t('fe', 'DTD Nama'); ?></th>
                          <th><?= Yii::t('fe', 'DTD Nama Lainnya'); ?></th>
                          <th><?= Yii::t('fe', 'Status DTD'); ?></th>
                          <th><?= Yii::t('fe', 'Status Menular DTD'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>

<div id="modal_suku" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var tabledtd = $('#table-dtd').docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,

        ajax: baseUrl+"master/dtd/get-data",
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        }],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        sorting: [[2, 'asc']],
        columns: [
            {
                title: '',
                data: null,
                defaultContent: '',
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "<?=Yii::t('fe', 'Id DTD')?>",  data: "dtd_id"},
            {title: "<?=Yii::t('fe', 'Tabular Chapter')?>",  data: "tabularlist_id"},
            {title: "<?=Yii::t('fe', 'DTD Kode')?>",  data: "dtd_kode"},
            {title: "<?=Yii::t('fe', 'Kode Terperinci DTD')?>",  data: "dtd_noterperinci"},
            {title: "<?=Yii::t('fe', 'DTD Nama')?>",  data: "dtd_nama"},
            {title: "<?=Yii::t('fe', 'DTD Nama Lainnya')?>",  data: "dtd_namalainnya"},
            {title: "Status",  data: "is_active"},
            {title: "<?=Yii::t('fe', 'Status Menular DTD')?>",  data: "dtd_menular"}
        ]
    });

    $(".dataTables_filter").hide();

    $(".filter-form-dtd").datatableBootstrapFilter(tabledtd,
        [
            [8, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
        ]
    );

    // Event Delete
    $(document).on("click", ".data-delete-diagnosa", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                tabledtd.draw()
            }
        });
        return false;
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tabledtd.draw();
    });
</script>
