<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
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
                ]); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>
                <table width="100%" id="example" class="table datatable-basic table-striped table-hover dataTable no-footer">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="10%"><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Nama Konfig') ?></th>
                            <th width="20%"><?= Yii::t('fe', 'Aktif') ?></th>
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

$this->registerJs("

        // Global Var
        var table;

        // Event Reload
        $(document).on('click', '.data-reload', function() {
            table.draw();
        });

        $(document).ready(function(){
            table = $('#example').docoTabel({
                filter: true,
                //add for handle checkbox
                columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                }],
                select: {
                    style: 'os',
                    selector: 'tr'
                },
                sorting: [[2, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'master/konfig-worklist/get-konfig-table',
                columns: [
                    {
                        data: 'null',
                        searchable: false,
                        orderable: false,
                        defaultContent:'',
                        width: '7%',
                    },
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t('fe', 'Nama Konfig')). "', data: 'kode_nama'},
                    {title: 'Status', data: 'aktif', class: 'text-center'},
                ],
                initComplete: () => {
                    $('.change-status').docoToggleSwitch({
                        url: baseUrl+'master/konfig-worklist/change-status',
                        confirmTitle : '".(\Yii::t("fe", "Konfirmasi"))."',
                        confirmMessage : '".(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?"))."',
                        method: 'GET',
                    });
                }
            });
            $('.dataTables_filter').hide();
        });
            
", View::POS_END, 'b-index');

?>