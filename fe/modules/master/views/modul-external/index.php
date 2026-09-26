<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = "Modul External";
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
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/modul-external/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/modul-external/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        // 'pdf',
                        // 'excel',
                    ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>
                <div class="row">
                <table width="100%" id="example" class="table datatable-basic table-striped table-hover dataTable no-footer">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="80"><?= Yii::t('fe', 'Rownum') ?></th>
                                <th><?= Yii::t('fe', 'Nama') ?></th>
                                <th><?= Yii::t('fe', 'Alias') ?></th>
                                <th><?= Yii::t('fe', 'Url') ?></th>
                                <th><?= Yii::t('fe', 'Status') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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
                style:    'os',
                selector: 'tr'
            },
            sorting: [[1, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'master/modul-external/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Nama'))."',
                    data: 'modul_nama'
                },
                {
                    title: '".(\Yii::t('fe', 'Alias'))."',
                    data: 'modul_namalainnya'
                },
                {
                    title: '".(\Yii::t('fe', 'Url'))."',
                    data: 'url_modul',
                    searchable:false
                },
                {
                    title: '".(\Yii::t('fe', 'Status'))."',
                    data: 'status',
                    name : 'is_active'
                },
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,[
            [
                4,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('status', '',
                            $status,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- PILIH SEMUA --'),
                                'col-index'=>4,
                            ]
                        )
                    )))."\"
            ],
        ]);

        var primaryKey;

        $('#example tbody').on('click', 'tr', function(){
            try {
                primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                $('.data-edit').attr('action',$('.data-edit').data('url')+primaryKey);
                $('.data-delete').attr('action',$('.data-delete').data('target')+primaryKey);
            } else {
                $('.data-edit').removeAttr('action');
                $('.data-delete').removeAttr('action');
            }

        });

        $(document).on('click', '#batal', function(e) {
            e.preventDefault();
            $(this).docoForm('delete',{
                    success : function (data) {
                        table.draw();
                    }
                });
            return false;
        });

        $(document).on('click', '.data-detail', function(){
            window.location = $(this).attr('href');
        })
        $(document).on('click', '.data-add', function(){
            window.location = $(this).data('target');
        })
    });

    var _afterSave = function (bool) {
        table.draw();
    }
",View::POS_END,'Pendidikan');
?>
