<?php

/**
 * @Author: afil
 * @Date:   2018-01-17 14:27:07
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-25 17:53:24
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Rencana kontrol');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
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
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-rencanakontrol" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Tanggal rencana kontrol')?></th>
                            <th><?=Yii::t('fe', 'Tanggal pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No RM')?></th>
                            <th><?=Yii::t('fe', 'Nama pasien')?></th>
                            <th><?=Yii::t('fe', 'Alamat')?></th>
                            <th><?=Yii::t('fe', 'No telepon')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
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
$this->registerJs($this->render('js/_rencanakontrol.js'));
$this->registerJs('
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#table-rencanakontrol").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"rajal/rencana-kontrol/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "tgl_jadwal")).'", data: "tgl_jadwal"},
                {title: "'.(\Yii::t("fe", "tgl_buatjanji")).'", data: "tgl_buatjanji"},
                {title: "'.(\Yii::t("fe", "no_pendaftaran")).'",  data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "no_rekam_medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "nama_pasien")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "alamat_pasien")).'", data: "alamat_pasien"},
                {title: "'.(\Yii::t("fe", "no_telepon_pasien")).'", data: "no_telepon_pasien"},
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [1, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tgl_jadwal', '', ['class' => 'form-control daterange', 'placeholder' => \Yii::t('fe', 'Tanggal rencana kontrol')]))).'\'],
            [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tgl_jadwal', '', ['class' => 'form-control daterange', 'placeholder' => \Yii::t('fe', 'Tanggal rencana kontrol')]))).'\'],
            [10, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\']
        ]);
    });
', View::POS_END, 'b-index');
?>
