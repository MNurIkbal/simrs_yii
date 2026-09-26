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
                            <?php ActiveForm::end(); ?>
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
                                    <tr>
                                        <td>1</td>
                                        <td>22-08-2017</td>
                                        <td>Apotek Farmasi</td>
                                        <td>Apotek Farmasi</td>
                                        <td>Paracetamol</td>
                                        <td>100</td>
                                        <td>50</td>
                                        <td>25</td>
                                        <td>25</td>
                                        <td>50</td>
                                    </tr>
                                    <!-- <tr>
                                        <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?= Html::a('<i class="fa fa-print"></i> '. Yii::t('fe', 'Print'), 
                            ['/apotek/laporan-stok/print-opname'], 
                            [
                                'class' => 'btn btn-dodger-blue btn-custom-table reseptur',
                                'title' => Yii::t('fe', 'Print'),
                                'data-tooltip' => 'tooltip'
                            ]
                        );
                    ?>
                    <?= Html::a('<i class="fa fa-file-pdf-o"></i> '. Yii::t('fe', 'PDF'), 
                            ['/apotek/transaksi-resep/print-rs'], 
                            [
                                'class' => 'btn btn-crimson btn-custom-table reseptur',
                                'title' => Yii::t('fe', 'PDF'),
                                'data-tooltip' => 'tooltip'
                            ]
                        );
                    ?>
                    <?= Html::a('<i class="fa fa-file-excel-o"></i> '. Yii::t('fe', 'Excel'), 
                            ['/apotek/transaksi-resep/print-rs'], 
                            [
                                'class' => 'btn btn-green btn-custom-table reseptur',
                                'title' => Yii::t('fe', 'Excel'),
                                'data-tooltip' => 'tooltip'
                            ]
                        );
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src=""></script>
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
    });');

?>

<script>
</script>
