<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Klasifikasi Diagnosa');
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
                                'data-target' => '/master/klasifikasi-diagnosa/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-target' => '/master/klasifikasi-diagnosa/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'data-delete-diagnosa',
                                'data-target' => '/master/klasifikasi-diagnosa/delete?id=',
                                'action' => 'null_id',
                                'data-additional' => 'data-rm',
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-klasifikasidiagnosa');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-klasifikasidiagnosa"></div>
                </div>
                <table id="table-klasifikasidiagnosa" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                          <th width="1%"></th>
                          <th width="5%"><?= Yii::t('fe', 'No'); ?></th>
                          <th><?= Yii::t('fe', 'ID Klasifikasi'); ?></th>
                          <th><?= Yii::t('fe', 'Kode Klasifikasi'); ?></th>
                          <th><?= Yii::t('fe', 'Nama Klasifikasi'); ?></th>
                          <th><?= Yii::t('fe', 'Nama Lainnya'); ?></th>
                          <th><?= Yii::t('fe', 'Status Klasifikasi'); ?></th>
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
    var tableklasifikasidiagnosa = $('#table-klasifikasidiagnosa').docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,

        ajax: baseUrl+"master/klasifikasi-diagnosa/get-data",
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
            {title: "<?=Yii::t('fe', 'ID Klasifikasi')?>",  data: "klasifikasidiagnosa_id"},
            {title: "<?=Yii::t('fe', 'Kode Klasifikasi')?>",  data: "klasifikasidiagnosa_kode"},
            {title: "<?=Yii::t('fe', 'Nama Diagnosa')?>",  data: "klasifikasidiagnosa_nama"},
            {title: "<?=Yii::t('fe', 'Nama Lainnya')?>",  data: "klasifikasidiagnosa_namalain"},
            {title: "Status Klasifikasi",  data: "is_active"}
        ]
    });

    $(".dataTables_filter").hide();

    $(".filter-form-klasifikasidiagnosa").datatableBootstrapFilter(tableklasifikasidiagnosa,
        [
            [6, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
        ]
    );

    // Event Delete
    $(document).on("click", ".data-delete-diagnosa", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                tableklasifikasidiagnosa.draw()
            }
        });
        return false;
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableklasifikasidiagnosa.draw();
    });
</script>
