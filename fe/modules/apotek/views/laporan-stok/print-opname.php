<?php

/**
 * @author Randy Vianda Putra
 * @copyright 16 January 2018 aweutist
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
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'Laporan Stok Opname') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'Apotek Farmasi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'Periode') ?> 12-10-2017 - 15-01-2018<a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    </div>
                    <div class="panel-body">
                        <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?=\Yii::t("fe", "Tanggal Mutasi");?></th>
                                    <th><?=\Yii::t("fe", "No Stok Opname");?></th>
                                    <th><?=\Yii::t("fe", "Harga Netto Sistem");?></th>
                                    <th><?=\Yii::t("fe", "Harga Netto Fisik");?></th>
                                    <th><?=\Yii::t("fe", "Selisih");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>16 Januari 2018</td>
                                    <td>R201801160001</td>
                                    <td>Rp. 20.000.00</td>
                                    <td>Rp. 20.000.00</td>
                                    <td>Rp. 0</td>
                                </tr>
                                <!-- <tr>
                                    <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                </tr> -->
                            </tbody>
                        </table>
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
                </div>

            </div>
        </div>
    </div>
</div>
<script src=""></script>
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));
?>

<script>
</script>
