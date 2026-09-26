<?php

/**
 * @author Naufal Ziyad L
 * @copyright 18 January 2018
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
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
                            <div class="col-md-12 filter-form"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Pasien Penunjang') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table id="table-pasien-penunjang" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="0">No</th>
                                        <th><?=\Yii::t("fe", "Tgl Pendaftaran");?></th>
                                        <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                        <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                                        <th><?=\Yii::t("fe", "Nama Depan");?></th>
                                        <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                        <th><?=\Yii::t("fe", "Alamat");?></th>
                                        <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                                        <th><?=\Yii::t("fe", "Ruangan Penunjang");?></th>
                                        <th><?=\Yii::t("fe", "Ruangan Asal");?></th>
                                        <th><?=\Yii::t("fe", "No Masuk Penunjang");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Masuk Penunjang");?></th>
                                        <th><?=\Yii::t("fe", "Kelas");?></th>
                                        <th><?=\Yii::t("fe", "Kasus Penyakit");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    </tr>
                                    <!-- <tr>
                                        <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr> -->
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
  /*  $this->registerCss($this->render('../assets/css/pendaftaran.css'));*/

    $this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            additional: "data-rm",
            success : function (data) {
                table.draw()
            }
        });
        return false;
    });

    // Event Ready
    $(document).ready(function() {
        $(function(){
            // $(".pickadate").pickadate({
            //     format: "dd mmm yyyy",
            //     formatSubmit: "yyyy-mm-dd",
            // });
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });
        })

        // Generate Table
        table = $("#table-pasien-penunjang").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/pasien-penunjang/get-data-informasi",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                 {title: "'.(\Yii::t("fe", "Tgl Pendaftaran")).'", data: "tgl_pendaftaran"},
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "No Pendaftaran")).'", data: "pendaftaran_id"}, //sementara/
                {title: "'.(\Yii::t("fe", "Nama Depan")).'",  data: "nama_depan", searchable: false},
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "Alamat")).'", data: "alamat_pasien", searchable: false},
                {title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", data: "jeniskelamin",searchable: false},
                {title: "'.(\Yii::t("fe", "Ruangan Penunjang")).'", data: "ruangan_penunjang", searchable: false},
                {title: "'.(\Yii::t("fe", "Ruangan Asal")).'", data: "ruangan_asal", searchable: false},
                {title: "'.(\Yii::t("fe", "No Masuk Penunjang")).'", data: "no_masukpenunjang", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Masuk Penunjang")).'", data: "tglmasukpenunjang", searchable: false},
                {title: "'.(\Yii::t("fe", "Kelas")).'", data: "kelaspelayanan_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Kasus Penyakit")).'", data: "jeniskasuspenyakit_nama", searchable: false},


            ],
            scrollCollapse: true,
           /* fixedColumns: {
                leftColumns: 2,
            }*/
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [1, \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::textInput('tgl_pendaftaran', '', ['class' => 'form-control daterange','placeholder'=>\Yii::t('fe', 'Tanggal Pendaftaran')])
                    )).'\'],
            ]
        );
    });
', View::POS_END, 'b-index');

?>
