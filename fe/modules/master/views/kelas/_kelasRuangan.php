<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Kelas ruangan');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
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
                        'search' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-kelasruangan'
                            ]
                        ],
                        'reset' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-kelasruangan'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/kelas/create-ruangan',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/kelas/update-ruangan?id=',
                                'data-url' => '/master/kelas/update-ruangan?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-target' => '/master/kelas/delete-ruangan?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/kelas/export-pdf-ruangan?',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/kelas/export-excel-ruangan?',
                            ]
                        ],
                    ],'#tableKelasRuangan');?>    
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-kelasruangan"></div>
                </div>
                <table id="tableKelasRuangan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Kelas pelayanan");?></th>
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

<script type="text/javascript">
var tableRuangan;
$(document).ready(function() {
    // Generate Table
    tableRuangan = $('#tableKelasRuangan').docoTabel({
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        sorting: [[1, 'asc']], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+'master/kelas/get-data-kelas-ruangan',
        columns: [
            {
                title: '',
                data: null,
                defaultContent: '',
                searchable: false,
                orderable: false
            },
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {title: "<?= (\Yii::t("fe", "Ruangan")); ?>", data: 'ruangan_nama'},
            {title: "<?= (\Yii::t("fe", "Kelas Pelayanan")); ?>", data: 'kelaspelayanan_nama'},
        ],
    });
    $('.dataTables_filter').hide();
    $('.filter-form-kelasruangan').datatableBootstrapFilter(
        tableRuangan, 
        [
            [
                3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $listKelasPelayanan, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>'
            ],
            [
                2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $listRuangan, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>'
            ],
        ]
    );
});
</script>