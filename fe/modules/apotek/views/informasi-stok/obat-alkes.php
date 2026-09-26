<?php

/**
 * @author Randy Vianda Putra
 * @copyright 19 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe', $title);
// $this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->title?></h3>
                <?= Breadcrumbs::widget([
                        'homeLink' => [ 
                            'label' => Yii::t('yii', 'Home'),
                            'url' => Yii::$app->homeUrl,
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">				
                <div class="row">					
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Pencarian') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php 
                            /*
                            $form = ActiveForm::begin([
                                'id' => 'mutasiobatalkes-form',
                                'options' => [
                                    'class' => 'form-horizontal', 			                    
                                    'role' => 'form',
                                ],
                            ]);
                            ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><input type="text" class="form-control" id="rangeDemoStart" placeholder="Start date"></p>
                                                </div>

                                                <div class="col-md-6">
                                                    <p><input type="text" class="form-control" id="rangeDemoFinish" placeholder="Finish date"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <?= Html::activeDropDownList($model, 'obatalkes_id',
                                                ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                                    'class' => 'select2 dokter_resep',
                                                    'prompt' => Yii::t('fe', '-- Pilih instalasi --')
                                                ]) 
                                            ?>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <?= Html::activeDropDownList($model, 'obatalkes_id',
                                                    ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                                        'class' => 'select2 dokter_resep',
                                                        'prompt' => Yii::t('fe', '-- Pilih nama obat alkes --')
                                                    ]) 
                                                ?>
                                                <?= Html::hiddenInput('TransaksiResep[obatalkes_id]', '', ['class' => 'reseptur_id']); ?>
                                                <span class="input-group-addon">
                                                    <?php
                                                        echo Html::a('<i class="fa fa-list-ul"></i>
                                                            <i class="fa fa-search"></i>',
                                                            Url::home().'apotek/obat-alkes-kasus/list-obat',[
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#modal_backdrop'
                                                        ]);
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <?= Html::activeDropDownList($model, 'penjamin',
                                                ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                                    'class' => 'select2 b',
                                                    'prompt' => Yii::t('fe', '-- Pilih ruangan --')
                                                ]) 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <?= Html::button('<i class="fa fa-search"></i> '. Yii::t('fe', "Cari"), ['class' => 'btn btn-primary cari']); ?>
                                    <?= Html::resetButton('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),['class' => 'btn btn-green batal']); ?>
                                </div>
                            </div>
                            <?php ActiveForm::end(); */ ?>
                            <div class="panel-body">
                               <div class="row">
                                    <div class="col-md-12 filter-form"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">										
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Stok dan Ketersediaan Obat') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Periode Stok");?></th>
                                        <th><?=\Yii::t("fe", "Instalasi");?></th>
                                        <th><?=\Yii::t("fe", "Ruangan");?></th>
                                        <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                        <th><?=\Yii::t("fe", "Qty Masuk");?></th>
                                        <th><?=\Yii::t("fe", "Qty Keluar");?></th>
                                        <th><?=\Yii::t("fe", "Qty Dipesan");?></th>
                                        <th><?=\Yii::t("fe", "Tersedia");?></th>
                                        <th><?=\Yii::t("fe", "Stok");?></th>
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
    </div>
</div>
<script src=""></script>
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));
    /*
    $this->registerJs('
    // Options
    var oneDay = 24*60*60*1000;
    var rangeDemoFormat = "%e-%b-%Y";
    var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});

    $("#rangeDemoToday").click( function (e)  {
        $("#rangeDemoStart").val(rangeDemoConv.format(new Date())).change();
    });

    // Clear dates
    $("#rangeDemoClear").click( function (e) {
        $("#rangeDemoStart").val("").change();
    });
    // Start date
    
    $("#rangeDemoStart").AnyTime_picker({
        format: rangeDemoFormat
    });

    // On value change
    $("#rangeDemoStart").change(function(e) {
        try {
            var fromDay = rangeDemoConv.parse($("#rangeDemoStart").val()).getTime();

            var dayLater = new Date(fromDay+oneDay);
                dayLater.setHours(0,0,0,0);

            var ninetyDaysLater = new Date(fromDay+(90*oneDay));
                ninetyDaysLater.setHours(23,59,59,999);

            // End date
            $("#rangeDemoFinish")
            .AnyTime_noPicker()
            .removeAttr("disabled")
            .val(rangeDemoConv.format(dayLater))
            .AnyTime_picker({
                earliest: dayLater,
                format: rangeDemoFormat,
                latest: ninetyDaysLater
            });
        }

        catch(e) {

            // Disable End date field
            $("#rangeDemoFinish").val("").attr("disabled","disabled");
        }
    });

    ');
    */

    $this->registerJs("
        // Global Var
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#example').docoTabel({
            filter: true,
            sorting: [[1, 'asc']], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'apotek/informasi-stok/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Periode Stok'))."', data: 'periodestok_nama'},
                {title: '".(\Yii::t('fe', 'Nama obat alkes'))."', data: 'obatalkes_namalain'},
                {title: '".(\Yii::t('fe', 'Instalasi akhir'))."',  data: 'instalasi_nama'},
                {title: '".(\Yii::t('fe', 'Ruangan akhir'))."', data: 'ruangan_nama'},
                {title: '".(\Yii::t('fe', 'Qty masuk'))."', data: 'qty_masuk', searchable: false, 'class':'text-right'},
                {title: '".(\Yii::t('fe', 'Qty keluar'))."', data: 'qty_keluar', searchable: false, 'class':'text-right'},
                {title: '".(\Yii::t('fe', 'Qty dipesan'))."', data: 'qty_dipesan', searchable: false, 'class':'text-right'},
                {title: '".(\Yii::t('fe', 'Qty tersedia'))."', data: 'qty_tersedia', searchable: false, 'class':'text-right'},
                {title: '".(\Yii::t('fe', 'Stok'))."', data: 'qty_stok', searchable: false, 'class':'text-right'},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \"<div class='input-group'><span class='input-group-addon'><i class='icon-calendar22'></i></span><input type='text' class='form-control daterange-basic' value='' placeholder='".(\Yii::t('fe', 'Periode Stok'))."' col-index='1'></div>\"
                ], 
                [
                    3, 
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('instalasi_nama', '', 
                            ArrayHelper::map($instalasi, 'instalasi_nama', 'instalasi_nama'), 
                            [
                                'class' => 'form-control no-select2', 
                                'prompt' => \Yii::t('fe', 'Instalasi akhir')
                            ]
                        )
                    )))."\"
                ], 
                [
                    4, 
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('ruangan_nama', '', 
                                ArrayHelper::map($ruangan, 'ruangan_nama', 'ruangan_nama'), 
                                [
                                    'class' => 'form-control no-select2', 
                                    'prompt' => \Yii::t('fe', 'Ruangan akhir')
                                ]
                            )
                        )
                    )
                )."\"
                ], 
                [
                    2, 
                    \"<div class='input-group'>".(preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('obatalkes_namalain', '', array(), 
                            [
                                'class' => 'form-control select2 obatalkes_namalain', 
                                'prompt' => \Yii::t('fe', 'Nama obat alkes'), 
                                'col-index' => '2'
                            ]
                        )
                    )))."<span class='input-group-addon'><span class='cursor-pointer' action='".Url::home()."kasir/modal-search/modal-stok-obat-alkes' data-toggle='modal' data-target='#modal_backdrop_search'><i class='fa fa-list'></i> <i class='fa fa-search'></i></span></span></div>\"
                ]
            ], {
                1:0,
                2:4,
                3:1,
                4:3
            }, true
        );

        $('.daterange-basic').daterangepicker({
            startDate: '".(date('01-m-Y'))."', autoUpdateInput: false,
            endDate: '".(date('d-m-Y'))."',
            applyClass: 'bg-slate-600',
            cancelClass: 'btn-default',
            locale: {
                format: 'DD-MMMM-YYYY'
            }
        });

        $('.obatalkes_namalain').select2({
                placeholder: '".\Yii::t("fe", "Nama obat alkes")."',
                minimumInputLength: 2,  
                ajax: {
                    url: '/apotek/informasi-stok/get-obatalkes',
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(term, page){
                        return{
                            q: term,
                            page: page
                        }
                    },
                    processResults: function (data) {                
                      return {
                        results: data.result
                      };
                    }                   
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            });

    });
        ");

?>

<script>
</script>
