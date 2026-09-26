<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;

$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$title); ?></b></h3>
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
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'add',
                        'edit' => [
                            'attributes' => [
                                'data-target' => '/master/pengambilan-antrian/update?id=',
                                'data-conditions' => 'jenisantrian_id'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                            ]
                        ],
                        // 'pdf',
                        'excel',
                    ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%">&nbsp;</th>
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Jenis antrian'); ?></th>
                            <th><?=Yii::t('fe', 'Kode Antrian'); ?></th>
                            <th><?=Yii::t('fe', 'Ruangan'); ?></th>
                            <th><?=Yii::t('fe', 'Dokter'); ?></th>
                            <th><?=Yii::t('fe', 'Cara Bayar')?></th>
                            <th><?=Yii::t('fe', 'Klasifikasi Pasien')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
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
$url = Url::to(['pengambilan-antrian/detail']);
$this->registerJs("
    $(document).ready(function() {
        table = $('#example').docoTabel({
            filter: true,
            columnDefs: [{
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
            ajax: baseUrl+'master/pengambilan-antrian/get-data',
            columns: [
                {
                    data : null,
                    render : function ( data, type, full, meta ) {
                        return null;
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Jenis antrian'))."', data: 'jenis_antrian'},
                {title: '".(\Yii::t('fe', 'Kode Antrian'))."', data: 'kode_antrian'},
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama'},
                {title: '".(\Yii::t('fe', 'Dokter'))."', data: 'nama_dokter'},
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'group_carabayar', name : 'group_id'},
                {title: '".(\Yii::t('fe', 'Klasifikasi Pasien'))."', data: 'klasifikasipasien_nama',searchable: false},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'status', name: 'is_default'},
            ],
            order: [[ 2, 'desc' ]],
            responsive: true,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.rows({page:'current'}).data().each(function (data, i){
                    var group = data.jenis_antrian;
                    var groupLink = '<a href=\'".$url."?id='+data.jenisantrian_id+'\'>' + $('<div>').text(group).html() + '</a>';

                    if (last !== group) {

                        $(rows).eq(i).before(
                            '<tr class=\'group\'><td colspan=\'9\' style=\'BACKGROUND-COLOR:rgb(181, 216, 197);font-weight:700;color:#006232;\'>' + groupLink  + '</td></tr>'
                        );

                        last = group;
                    }
                });
            }
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                 \"".(preg_replace('/[\n\t\r]/i', 'w', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('jenisantrian_id', '', $jenis_antrian,
                            [
                                'class' => 'form-control select2 ddl_jenisantrian',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                            ]
                        )
                    )))."\"
            ],
            [
                5,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('fungsiantrian_id', '', $cara_bayar,
                            [
                                'class' => 'form-control select2 ddl_fungsiantrian',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                            ]
                        )
                    )))."\"
            ],
            [
                7,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('jenisantrian_id', '', $ddlstatus,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                            ]
                        )
                    )))."\"
            ]
        ],{
            2:0,
            3:1
        },true);
    });
",View::POS_END,'jkun');
?>
