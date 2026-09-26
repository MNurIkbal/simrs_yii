<?php
/**
 * @author Randy Vianda Putra
 * @copyright 19 January 2018 aweutist
 */
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;

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
                                            <div class="input-group">
                                                <?= Html::activeDropDownList($model, 'obatalkes_id',
                                                    ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                                        'class' => 'select2 dokter_resep',
                                                        'prompt' => Yii::t('fe', '-- Pilih nama barang --')
                                                    ]) 
                                                ?>
                                                <?= Html::hiddenInput('TransaksiResep[obatalkes_id]', '', ['class' => 'reseptur_id']); ?>
                                                <span class="input-group-addon">
                                                    <?php
                                                        echo Html::a('<i class="fa fa-list-ul"></i>
                                                            <i class="fa fa-search"></i>',
                                                            Url::home().'apotek/transaksi-resep/list-resep',[
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
                                <div class="col-md-3">
                                    <?= Html::button('<i class="fa fa-search"></i> '. Yii::t('fe', "Cari"), ['class' => 'btn btn-primary cari']); ?>
                                    <?= Html::resetButton('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),['class' => 'btn btn-green batal']); ?>
                                </div>
                            </div>
                            <?php
                                echo Html::endForm();
                            ?>
                        </div>
                    </div>

                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel pemakaian barang') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
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
                                        <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal pemakaian");?></th>
                                        <th><?=\Yii::t("fe", "Nama barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>18-01-2018</td>
                                        <td>Spidol</td>
                                        <td>5</td>
                                        <td>Buah</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    });');

?>