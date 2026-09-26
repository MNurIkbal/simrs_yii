
<?php
// Author : Budi
// Date : 17 Januari 2018
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// $this->registerCss('table {
//     display: block;
//     overflow: scroll;
// }');

$this->registerJs($this->render('assets/js/moralitas.js'));
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
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
                    <div class="col-md-12 filter-form">
                        <?= Yii::$app->controller->renderPartial('search/_search_moralitas', [
                            'url_popup' => $url_popup
                        ]) ?>
                    </div>
                </div>
                <!-- <div class="scrollit"> -->
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%" border="1">
                    <thead>
                        <tr class="bg-inverse">
                            <th rowspan="3" width="1"><?=\Yii::t("fe", "No Urut");?></th>
                            <th rowspan="3" ><?=\Yii::t("fe", "No DTD");?></th>
                            <th rowspan="3"><?=\Yii::t("fe", "No Daftar Terperinci");?></th>
                            <th rowspan="3"><?=\Yii::t("fe", "Golongan Sebab Penyakit");?></th>
                            <th colspan="18" class="text-center">
                                <?=\Yii::t("fe", "Jumlah Pasien Hidup dan Mati Menurut Golongan Umur &amp; Jenis Kelamin");?>
                            </th>
                           <th colspan="2" class="text-center" width="1">
                               <?=\Yii::t("fe", "Pasien Keluar <br/> (Hidup &amp; Mati) <br/> Menurut Jenis <br/> Kelamin");?>
                           </th>
                            <th rowspan="3" class="text-center">
                                <?=\Yii::t("fe", "Jumlah <br/> Pasien <br/> Keluar <br/> Meninggal <br/> (23 + 24)");?></th>
                        </tr>
                        <tr class="bg-inverse">
                            <th colspan="2" class="text-center">0-6 hr</th>
                            <th colspan="2" class="text-center">7-28 hr</th>
                            <th colspan="2" class="text-center">28hr-<1th</th>
                            <th colspan="2" class="text-center">1-4 th</th>
                            <th colspan="2" class="text-center">5-14 th</th>
                            <th colspan="2" class="text-center">15-24 th</th>
                            <th colspan="2" class="text-center">25-44 th</th>
                            <th colspan="2" class="text-center">45-64 th</th>
                            <th colspan="2" class="text-center">> 65</th>
                            <th rowspan="2" class="text-center">LK</th>
                            <th rowspan="2" class="text-center">PR</th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                            <th class="text-center">L</th>
                            <th class="text-center">P</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="background-color: #F2F2F2;">1</th>
                            <th class="text-center" style="background-color: #F2F2F2;">2</th>
                            <th class="text-center" style="background-color: #F2F2F2;">3</th>
                            <th class="text-center" style="background-color: #F2F2F2;">4</th>
                            <th class="text-center" style="background-color: #F2F2F2;">5</th>
                            <th class="text-center" style="background-color: #F2F2F2;">6</th>
                            <th class="text-center" style="background-color: #F2F2F2;">7</th>
                            <th class="text-center" style="background-color: #F2F2F2;">8</th>
                            <th class="text-center" style="background-color: #F2F2F2;">9</th>
                            <th class="text-center" style="background-color: #F2F2F2;">10</th>
                            <th class="text-center" style="background-color: #F2F2F2;">11</th>
                            <th class="text-center" style="background-color: #F2F2F2;">12</th>
                            <th class="text-center" style="background-color: #F2F2F2;">13</th>
                            <th class="text-center" style="background-color: #F2F2F2;">14</th>
                            <th class="text-center" style="background-color: #F2F2F2;">15</th>
                            <th class="text-center" style="background-color: #F2F2F2;">16</th>
                            <th class="text-center" style="background-color: #F2F2F2;">17</th>
                            <th class="text-center" style="background-color: #F2F2F2;">18</th>
                            <th class="text-center" style="background-color: #F2F2F2;">19</th>
                            <th class="text-center" style="background-color: #F2F2F2;">20</th>
                            <th class="text-center" style="background-color: #F2F2F2;">21</th>
                            <th class="text-center" style="background-color: #F2F2F2;">22</th>
                            <th class="text-center" style="background-color: #F2F2F2;">23</th>
                            <th class="text-center" style="background-color: #F2F2F2;">24</th>
                            <th class="text-center" style="background-color: #F2F2F2;">25</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="25"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- </div> -->
            </div>
        </div>
        <div class="form-group">
            <?= Html::button(Yii::t('fe', ' Print'), [
                'class' => 'btn btn-dodger-blue fa fa-print',
            ]);?>
            <?= Html::button(Yii::t('fe', ' PDF'), 
                [
                    'class' => 'btn btn-crimson fa fa-file-pdf-o',
                ]);
            ?>
            <?= Html::button(Yii::t('fe', ' Excel'), 
                [
                    'class' => 'btn btn-green fa fa-file-excel-o',
                ]);
            ?>
            <?= Html::button(Yii::t('fe', ' Petunjuk'), 
                [
                    'class' => 'btn btn-cyan fa fa-question-circle',
                ]);
            ?>
        </div>
    </div>
</div>
