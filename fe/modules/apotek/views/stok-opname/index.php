<?php
/**
 * @author Randy Vianda Putra
 * @copyright 18 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use Doco\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
.pickadate{
    top:187px !important;
}
');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->title;?></h3>
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
            <div class="panel-body">
                <div class="row">
                    <!-- <div class="col-md-12 panel panel-flat">
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
                            echo Html::beginForm(null,'POST',[
                                    'class' => 'form-filter form-horizontal',
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
                                            <?php

                                            echo Html::activeDropDownList($model, 'instalasi',
                                                ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), [
                                                    'class' => 'select2 selectInstalasi',
                                                    'prompt' => Yii::t('fe', '-- Pilih instalasi --')
                                                ])
                                            ?>
                                        </div>

                                        <div class="col-md-4">
                                                <?= Html::activeDropDownList($model, 'ruangan_tujuan',
                                                    ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                                        'class' => 'select2 dokter_resep',
                                                        'prompt' => Yii::t('fe', '-- Pilih ruangan --')
                                                    ])
                                                ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <div class="input-group">

                                                <?=
                                                Html::dropDownList('noformulir', '', array(), [
                                                    'class' => 'form-control selectFormulir',
                                                ]);
                                                // Html::activeDropDownList($model, 'obatalkes_id',
                                                //     ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                                //         'class' => 'select2 dokter_resep',
                                                //         'prompt' => Yii::t('fe', '-- Pilih no formulir --')
                                                //     ])
                                                ?>
                                                <span class='input-group-addon'>
                                                    <span class='cursor-pointer' action='/apotek/informasi-formulir search?tipe=noformulir' data-toggle='modal' data-target='#modal_backdrop_search'><i class='fa fa-list'></i> <i class='fa fa-search'></i></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <?= Html::button('<i class="fa fa-search"></i> '. Yii::t('fe', "Cari"), ['class' => 'btn btn-primary cari']); ?>
                                    <?= Html::resetButton('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),['class' => 'btn btn-lime-green batal']); ?>
                                </div>
                            </div>
                            <?php
                                echo Html::endForm();
                            ?>
                        </div>
                    </div> -->

                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Stok Opname') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                         <?php $form = ActiveForm::begin([
                                'id'=>'stokopname-form',
                                'options' => [
                                    'class' => 'horizontal-form',
                                    'role'=>'form',
                                    'enableClientValidation'=>false,
                                    ]
                                ]);

                            echo $form->field($model_form, 'ruangan_id')->hiddenInput(['value'=>Yii::$app->docoVars->workspace("ruangan_id")])->label(false);
                            echo $form->field($model_form, 'formuliropname_id')->hiddenInput(['id'=>'formuliropname_id'])->label(false);
                            echo $form->field($model_form, 'pegawai_id')->hiddenInput(['value'=>Yii::$app->docoVars->user("id_pegawai")])->label(false);
                                ?>
                        <div class="panel-body">
                            <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                                        <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                        <th><?=\Yii::t("fe", "No batch")?></th>
                                        <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                                        <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                                        <th><?=\Yii::t("fe", "Kondisi");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center">Data tidak tersedia</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-lg-3 control-label" style="margin-top: 5px"><?= Yii::t('fe', 'Total harga netto sistem') ?></label>
                                <div class="col-lg-9">
                                    <?php
                                    echo $form->field($model_form, 'total_harganetto_sistem')->textInput(['readonly'=>'true','id'=>'total_harganetto_sistem','placeholder'=>Yii::t('fe', 'Total harga netto sistem')])->label(false);

                                    ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="col-lg-3 control-label" style="margin-top: 5px"><?= Yii::t('fe', 'Jenis stok opname') ?></label>
                                <div class="col-lg-9">
                                    <?php
                                    echo
                                    $form->field($model_form, 'jenis_stokopname')->dropdownList($jenis,
                                        ['prompt'=>Yii::t('fe', 'Jenis stok opname')]
                                    )->label(false);

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-lg-3 control-label" style="margin-top: 5px"><?= Yii::t('fe', 'Total harga netto fisik') ?></label>
                                <div class="col-lg-9">
                                     <?php
                                    echo $form->field($model_form, 'total_harganetto_fisik')->textInput(['readonly'=>'true','id'=>'total_harganetto_fisik','placeholder'=>Yii::t('fe', 'Total harga netto fisik')])->label(false);

                                    ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="col-lg-3 control-label" style="margin-top: 5px"><?= Yii::t('fe', 'Selisih harga netto') ?></label>
                                <div class="col-lg-9">
                                     <?php
                                    echo $form->field($model_form, 'selisih_harganetto')->textInput(['readonly'=>'true','id'=>'selisih_harganetto','placeholder'=>Yii::t('fe', 'Selisih harga netto')])->label(false);

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <br>
                    <div class="col-md-3 pull-left">
                        <?= Html::submitButton('<i class="fa fa-floppy-o"></i>'.Yii::t('fe', ' Simpan'),
                            [
                                'class' => 'btn bg-teal',
                                'id'=>'btn-simpan'
                            ]);
                        ?>
                        <?= Html::a('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),
                            ['#'],
                            [
                                'class' => 'btn btn-lime-green ulang',
                                'title' => Yii::t('fe', 'Ulang'),
                                'data-tooltip' => 'tooltip'
                                ]
                            );
                        ?>
                        <?= Html::a('<i class="fa fa-print"></i> '. Yii::t('fe', 'Print'),
                                ['/apotek/transaksi-resep/print-rs'],
                                [
                                    'class' => 'btn btn-dodger-blue btn-custom-table reseptur',
                                    'title' => Yii::t('fe', 'Print'),
                                    'data-tooltip' => 'tooltip'
                                ]
                            );
                        ?>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerCss($this->render('../assets/css/apotek.css'));

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

    $this->registerJs("


        $('#stokopname-form').docoForm('submit',{
            before: function(){
                console.log('tester');
                return false;
            },
            success : function(response) {
                console.log(response);
            }
        });

        $('.cari').click(function(){
            var id = $('.selectFormulir').val();
            loadData(id);
            $('#formuliropname_id').val(id);
        })
        $('.selectFormulir').select2({
                placeholder: '".\Yii::t("fe", "No Formulir")."',
                minimumInputLength: 4,
                ajax: {
                    url: '/apotek/stok-opname/get-data-formulir',
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
        function loadData(id){
            table = $('#example').docoTabel({
                bDestroy: true,
                filter: false,
                sorting: [[1,'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'apotek/stok-opname/get-data-detail?id='+id,
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Nama Obat Alkes')."',
                        data: 'obatalkes_namalain',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'No batch')."',
                        data: 'nobatch',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Stok Sistem')."',
                        data: 'stok_sistem',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Stok Fisik")."',
                        data: 'stok_fisik',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Kondisi")."',
                        data: 'kondisi',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Tanggal Kadaluarsa")."',
                        data: 'tgl_kadaluarsa',
                        searchable: false,
                        orderable: false,
                    },

                ],
                initComplete: function(settings, json) {
                    $('.daterange-single').daterangepicker({
                        singleDatePicker: true,
                        applyClass: 'bg-slate-600',
                        cancelClass: 'btn-default',
                        locale: {
                            format: 'DD-MMMM-YYYY'
                        }
                    });
                    $('.stokfisik').keyup(function(){
                        var stokfisik = $(this).val();
                        var target = $(this).data('target');
                        var harga = $(this).data('harga');
                        var txtharga = $('.'+harga).val();
                        let sum = 0;
                        $('.'+target).val(stokfisik*txtharga);
                        $('.total_row').each(function(){
                            if(!isNaN(this.value)){
                                sum += this.value ? parseInt(this.value) : 0;
                            }

                        })
                        $('#total_harganetto_fisik').val(sum);
                        let harga_sistem = $('#total_harganetto_sistem').val();
                        $('#selisih_harganetto').val(selisih(sum,harga_sistem));
                    });
                    function selisih(hargafisik, hargasistem){
                        return hargafisik-hargasistem;
                    }
                }
            });

            $('.dataTables_filter').hide();
            $.ajax({
                url:  baseUrl+'apotek/stok-opname/get-data-sum?id='+id,
                type: 'json',
                success: function(response){
                    $('#total_harganetto_sistem').val(response);
                }
            });
        }
        $(document).ready(function(){
            var pickdate = $('.pickadate').pickadate({
                formatSubmit: 'yyyy-mm-dd',
            });
        });



        ");

?>
