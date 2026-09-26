<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\LoketForm;
use Doco\master\controllers\loketController;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master/loket']];
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
                        'reset',
                        'add',
                        'edit',
                        'delete' => [
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Hapus'),
                            'icon' => 'fa fa-close',
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs data-delete btn-toolbar',
                                'id' => 'delete-loket',
                                'additional'=>'data-rm',
                                'data-options'=>'click'
                            ]
                        ],
                        // 'pdf',
                        'excel',
                    ],'#data-loket');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>

                <table width="100%" class="table datatable-basic table-striped table-hover dataTable no-footer" id="data-loket" data-source="<?=Url::home();?>" data-filter=".form-filter" data-test="true">
                    <thead class="bg-inverse">
                        <tr>
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t('fe', 'Nama Jenis Antrian'); ?></th>
                            <th><?=\Yii::t('fe', 'Kode Antrian'); ?></th>
                            <th><?=\Yii::t('fe', 'No Loket'); ?></th>
                            <th><?=\Yii::t('fe', 'Nama Loket'); ?></th>
                            <th><?=Yii::t('fe', 'Status'); ?></th>
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
    var tableloket;

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableloket = $('#data-loket').docoTabel({
            filter: true,
            ordering : false,
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
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            oLanguage: {
                sLengthMenu: '".(\Yii::t('fe', 'dt_length_menu'))."',
                sZeroRecords: '".(\Yii::t('fe', 'dt_zero_records'))."',
                sEmptyTable: '".(\Yii::t('fe', 'dt_empty_table'))."',
                sInfoFiltered: '".(\Yii::t('fe', 'dt_info_filtered'))."',
                sInfoEmpty: '".(\Yii::t('fe', 'dt_info_empty'))."',
                sInfo: '".(\Yii::t('fe', 'dt_info'))."',
                oPaginate: {
                    sFirst: '".(\Yii::t('fe', 'dt_first_page'))."',
                    sPrevious: '".(\Yii::t('fe', 'dt_previous_page'))."',
                    sNext: '".(\Yii::t('fe', 'dt_next_page'))."',
                    sLast: '".(\Yii::t('fe', 'dt_last_page'))."'
                }
            },
            ajax: {
                url:baseUrl+'master/loket/get-data',
                type:'POST'
            },
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
                {
                    title: '".(\Yii::t('fe', 'Nama Jenis Antrian'))."',
                    data: 'jenis_name'
                },
                {
                    title: '".(\Yii::t('fe', 'Kode Antrian'))."',
                    data: 'jenis_value',
                    searchable: false
                },
                {
                    title: '".(\Yii::t('fe', 'No Loket'))."',
                    data: 'loket_nourut'
                },
                {
                    title: '".(\Yii::t('fe', 'Nama Loket'))."',
                    data: 'loket_namalain',
                },
                {
                    title: '".(\Yii::t('fe', 'Status'))."',
                    data: 'status',
                },
            ],
            order: [[ 2, 'desc' ]],
            'responsive': true,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(2, { page: 'current' }).data().each(function (group, i) {

                    if (last !== group) {

                        $(rows).eq(i).before(
                            '<tr class=\'group\'><td colspan=\'8\' style=\'BACKGROUND-COLOR:rgb(181, 216, 197);font-weight:700;color:#006232;\'>' + group  + '</td></tr>'
                        );

                        last = group;
                    }
                });
            }
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(tableloket, [
            [
                2,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('jenisantrian_id', '', ArrayHelper::map($jenis_antrian, 'lookup_id', 'lookup_name'),
                            [
                                'class' => 'form-control select2 ddl_jenisantrian',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>1,
                            ]
                        )
                    )))."\"
            ],
            [
                6,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('status', '', $dataDropdown,
                            [
                                'class' => 'form-control select2 status',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ]
        ]);


        $(document).on('click', '#delete-loket', function(e) {
            e.preventDefault();
            let primaryKey = tableloket.row('.selected').data()?.primary
            if (primaryKey == undefined || primaryKey == '') {
                docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih')
                return false
            }
            
            $(this).docoForm('delete',{
                skipErrorNotif: true,
                skipNotifyMessage: true,
                skipSuccessNotif: true,
                url: window.location.origin + `/master/loket/delete?id=`+primaryKey,
                success : function (data) {
                    tableloket.draw()
                    return false;
                },
                error : function (error) {
                    docoNotification('warning', 'Terjadi Kesalahan', error.responseJSON?.meta?.message)
                    return false;
                }
            });
        });
    });
",View::POS_END,'jkun');
?>
