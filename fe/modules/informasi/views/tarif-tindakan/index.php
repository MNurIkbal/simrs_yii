<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?= Yii::t('fe', 'Rownum') ?></th>
                            <th><?= Yii::t('fe', 'Instalasi / Ruangan') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan') ?></th>
                            <th><?= Yii::t('fe', 'Jenis Tarif') ?></th>
                            <th><?= Yii::t('fe', 'Kategori Tindakan') ?></th>
                            <th><?= Yii::t('fe', 'Kelompok Tindakan') ?></th>
                            <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                            <th><?= Yii::t('fe', 'Kelas Pelayanan') ?></th>
                            <th><?= Yii::t('fe', 'Tarif') ?></th>
                            <th><?= Yii::t('fe', 'Cito') ?></th>
                            <th><?= Yii::t('fe', 'Diskon') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    // Global Var
    var table;
    var data;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"informasi/tarif-tindakan/get-data",
            columns: [
                {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Instalasi/Ruangan")).'", data: "instalasi_nama"},
            {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", visible: false},
            {title: "'.(\Yii::t("fe", "Jenis tarif")).'", data: "jenistarif_nama"},
            {title: "'.(\Yii::t("fe", "Kategori tindakan")).'", data: "kategoritindakan_nama"},
            {title: "'.(\Yii::t("fe", "Kelompok tindakan")).'", data: "kelompoktindakan_nama"},
            {title: "'.(\Yii::t("fe", "Nama tindakan")).'", data: "daftartindakan_nama"},
            {title: "'.(\Yii::t("fe", "Kelas pelayanan")).'", data: "kelaspelayanan_nama"},
            {title: "'.(\Yii::t("fe", "Tarif")).'", data: "harga_tariftindakan", searchable: false},
            {title: "'.(\Yii::t("fe", "Cyto (%)")).'", data: "persencyto_tindakan", searchable: false},
            {title: "'.(\Yii::t("fe", "Diskon (%)")).'", data: "persendiskon_tindakan", searchable: false},
            ],
        });

        table.on( \'xhr\', function () {
            data = table.ajax.params();
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi', '', 
                        ArrayHelper::map($api['response']['instalasi'], 'instalasi_nama', 'instalasi_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Instalasi')]))).'\'
                ],
                [
                    2, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', 
                        ArrayHelper::map($api['response']['ruangan'], 'ruangan_nama', 'ruangan_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Ruangan')]))).'\'
                ],
                [
                    3, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenistarif_nama', '', 
                        ArrayHelper::map($api['response']['jenis_tarif'], 'jenistarif_nama', 'jenistarif_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Jenis tarif')]))).'\'
                ],
                [
                    4, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kategoritindakan_nama', '', 
                        ArrayHelper::map($api['response']['kategori_tindakan'], 'kategoritindakan_nama', 'kategoritindakan_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Kategori tindakan')]))).'\'
                ],
                [
                    5, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelompoktindakan_nama', '', 
                        ArrayHelper::map($api['response']['kelompok_tindakan'], 'kelompoktindakan_nama', 'kelompoktindakan_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Kelompok tindakan')]))).'\'
                ],
                [
                    7, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelaspelayanan_nama', '', 
                        ArrayHelper::map($api['response']['kelas_pelayanan'], 'kelaspelayanan_nama', 'kelaspelayanan_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Kelas pelayanan')]))).'\'
                ],
            ], {
                1:0,
                2:1,
                3:2,
                4:3,
                5:4,
                7:5,
            }, true
        );

        $(".daterange-basic").daterangepicker({
            // autoUpdateInput: false,
            startDate: "'.(date("01-m-Y")).'",
            endDate: "'.(date("d-m-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
    });

    
',View::POS_END, 'InformasiTarif');

?>