<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Laporan 10 Besar Penyakit');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medik', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    $bulan = DocoHelpers::daftarBulan();
?>
<?php
    $listTahun = [];
    for ($x = date('Y'); $x >= (date('Y') - 9); $x--) {
        $listTahun[$x] = $x;
    }
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar(['search','reset','excel'], '#lap-10-penyakit');?>
                </div>
            </div>

            <div class="panel-body">
                <fieldset class="scheduler-border">
                    
                    <div class="row">
                        <div class="col-md-12 filter-form"></div>
                    </div>
                </fieldset>
                <br />

                <fieldset class="scheduler-border">
                    
                    <table id="lap-10-penyakit" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                        
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "No DTD");?></th>
                                <th><?=\Yii::t("fe", "Kode diagnosa");?></th>
                                <th><?=\Yii::t("fe", "Nama diagnosa");?></th>
                                <?php
                                foreach ($custKolom as $k => $value) :
                                ?>
                                    <th colspan="<?=$value['colspan']?>" class="text-center"><?=$k?></th>
                                <?php
                                endforeach;
                                ?>
                                <th><?=\Yii::t("fe", "Jumlah");?></th>
                            </tr>
                            <tr>
                                <th width="1"></th>
                                <th></th>
                                <th></th>
                                <th></th>

                                <?php
                                foreach ($laporan_penyakit['header'] as $value) :
                                ?>
                                    <th class="text-center"><?= strtoupper($value['klasifikasipasien_nama']) ?></th>
                                <?php
                                endforeach;
                                ?>
                                <th></th>
                            </tr>

                        </thead>
                        <tbody>
                           <tr>
                            <td colspan="11"></td>
                           </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
    $col = [
        [
            'title' => '',
            'name' => '',
            'data' => 'rowNum',
            'searchable' => false,
            'orderable' => false,
            'visible' => true
        ],
        [
            'title' => '',
            'data' => 'no_dtd',
            'searchable' => false,
            'orderable' => false,
            'name' => '',
            'visible' => true
        ],
        [
            'title' => '',
            'data' => 'kode_diagnosa',
            'searchable' => false,
            'orderable' => false,
            'name' => '',
            'visible' => true
        ],
        [
            'title' => '',
            'data' => 'nama_diagnosa',
            'searchable' => false,
            'orderable' => false,
            'name' => '',
            'visible' => true
        ]
    ];

    foreach ($laporan_penyakit['header'] as $k => $value) :
        $col[] = [
                    'title' => $value['klasifikasipasien_nama'],
                    'data' => 'data_'.$k,
                    'searchable' => false,
                    'orderable' => false,
                    'name' => '',
                    'visible' => true
                ];
    endforeach;
        $col[] = [
            'title' => 'Jumlah',
            'name' => 'jumlah',
            'data' => 'jumlah',
            'searchable' => false,
            'orderable' => false,
            'visible' => true
        ];
        $col[] = [
                'title' => 'Instalasi',
                'data' => 'instalasi',
                'searchable' => true,
                'orderable' => false,
                'name' => 'instalasi_id',
                'visible' => false
        ];
        $col[] = [
            'title' => 'Ruangan',
            'data' => 'ruangan_id',
            'searchable' => true,
            'orderable' => false,
            'name' => 'ruangan_id',
            'visible' => false
        ];
        $col[] = [
            'title' => 'Nama Diagnosa',
            'name'=>'kode_diagnosa',
            'data' => 'kode_diagnosa',
            'searchable' => true,
            'orderable' => false,
            'visible' => false
        ];
        $col[] = [
            'title' => 'Bulan',
            'name' => 'bulan',
            'data' => 'bulan',
            'searchable' => true,
            'orderable' => false,
            'visible' => false
        ];
        $col[] = [
            'title' => 'Tahun',
            'name' => 'tahun',
            'data' => 'bulan',
            'searchable' => true,
            'orderable' => false,
            'visible' => false
        ];

$this->registerJs('
    // Global Var
    var koloms = '.json_encode($col). ';
    var table;
    var tableColumnIndex = [];

    

    $(document).ready(function(){

        table = $("#lap-10-penyakit").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"rm/lap-sepuluh-besar-penyakit-brimob/get-data",
            columns:koloms,
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 4,
            // }
        });

        var kode_diagnosa = table.column("kode_diagnosa:name").index();
        var instalasi_id = table.column("instalasi_id:name").index();
        var ruangan_id = table.column("ruangan_id:name").index();
        var bulan = table.column("bulan:name").index();
        var tahun = table.column("tahun:name").index();

        var $kode_diagnosa = table.column("kode_diagnosa:name").index();
        var $instalasi_id = table.column("instalasi_id:name").index();
        var $ruangan_id = table.column("ruangan_id:name").index();
        var $bulan = table.column("bulan:name").index();
        var $tahun = table.column("tahun:name").index();
        
        var data_urutan = {};
        data_urutan[$bulan] = 0;
        data_urutan[$tahun] = 1;
        data_urutan[$kode_diagnosa] = 2;
        data_urutan[$instalasi_id] = 3;
        data_urutan[$ruangan_id] = 4;
 
        $(".dataTables_filter").hide();
        
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                kode_diagnosa, \'<div class="input-group">'.(preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Select2::widget([
                        'name' => 'kode_diagnosa',
                        'options' => ['placeholder' => \Yii::t('fe', 'Nama Diagnosa'), 'autocomplete' => 'off'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::home().(Yii::$app->controller->module->id).
                                '/lap-sepuluh-besar-penyakit-brimob/get-diagnosa',
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                    ])
                )).
                '</div>\'
            ],
            [
                instalasi_id, \''.(preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Html::dropDownList(
                        'instalasi_id',
                        '',
                        $instalasiList,
                        [
                            'class' => 'form-control select2',
                            'id' => 'instalasi_id',
                            'prompt' => \Yii::t('fe', '--Pilih Instalasi--')
                        ]
                    )
                )).
                '\'
            ],
            [
                ruangan_id, \''.(preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    DepDrop::widget(
                        [
                            'name' => 'ruangan_id',
                            'options' => [
                                'id' => 'ruangan_id',
                                'class' => 'form-control select2',
                            ],
                            'pluginOptions' => [
                                'depends' => ['instalasi_id'],
                                'placeholder' => \Yii::t('fe', '--Pilih Ruangan--'),
                                'url' => Url::to(['/rm/lap-jenis-penyakit-brimob/list-ruangan'])
                            ]
                        ]
                    )
                )).
                '\'
            ],
            [
                bulan, \''.(preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Html::dropDownList(
                        'bulan',
                        '',
                        $bulan,
                        [
                            'class' => 'form-control select2',
                            'id' => 'bulan',
                            'prompt' => \Yii::t('fe', '--Pilih Bulan--'),
                            'value' => date('m'),
                            'options' => [date('m') => ['Selected' => 'selected']] 
                        ]
                    )
                )).
                '\'
            ],
            [
                tahun, \''.(preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Html::dropDownList(
                        'tahun',
                        '',
                        $listTahun,
                        [
                            'class' => 'form-control select2',
                            'id' => 'tahun',
                            'prompt' => \Yii::t('fe', '--Pilih Tahun--'),
                            'value' => date('Y'),
                            'options' => [date('Y') => ['Selected' => 'selected']] 
                        ]
                    )
                )).
    '\'
            ],
        ], data_urutan);

        
        // Event click
        $(document).on("click", ".data-reset", function() {
            var d = new Date();
            var bulan = d.getMonth();
                bulan = bulan+1;
            var tahun = d.getFullYear();
            
            
            // Reload table
            table.draw();

            $("#bulan").val(bulan).trigger("change");
            $("#tahun").val(tahun).trigger("change");
        });

    });
    
', View::POS_END, 'b-index');
?>
