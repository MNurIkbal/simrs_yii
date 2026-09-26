<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
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
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
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
                <?=
                    DocoHelpers::generateToolbar([
                        'search',
                        'reset' => ['attributes' => ['data-parent' => '.filter-form']]
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <table id="konfig-obat-ruangan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th style="display: none;"><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Min Stok");?></th>
                            <th><?=\Yii::t("fe", "Max Stok");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Rak");?></th>
                            <th><?=\Yii::t("fe", "Laci");?></th>
                            <th><?=\Yii::t("fe", "Aksi");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
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
    var ruangan_id = "'.$ruangan_id.'";

    $(document).on("click", ".data-reset", function() {
        $("#instalasi_ruangan").val(ruangan_id).trigger("change");
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#konfig-obat-ruangan").docoTabel({
            filter: true,
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"gudang/konfig-obat-ruangan/get-by-ruangan",
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Instalasi - Ruangan")).'",
                    data: "ruangan_id",
                    visible: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",
                    data: "obatalkes_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Stok Minimal")).'",
                    data: "min_stok",
                    class: "text-right",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Stok Maksimal")).'",
                    data: "max_stok",
                    class: "text-right",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan")).'",
                    data: "satuan_kecil",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Rak")).'",
                    data: "nama_rak",
                },
                {
                    title: "'.(\Yii::t("fe", "Locator")).'",
                    data: "nama_laci",
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('instalasi_ruangan', '',
                        $instalasi_ruangan, [
                            'id' => 'instalasi_ruangan',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua --')
                        ]
                    )
                )).'</div>\'
            ],
        ]);

        $("#instalasi_ruangan").val(ruangan_id).trigger("change");
    });

', View::POS_END, 'b-index');
?>

