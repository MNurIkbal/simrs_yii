<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .wraptext {
        white-space:normal;
        width:200px;
    }
</style>

<style>
    .card {
      box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
      transition: 0.3s;
      width: 100%;
      border: 1px solid #34bfa3;
    }

    .card:hover {
      box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .container {
      padding: 2px 16px;
    }

    .img-rekap {
        height: 35px;
        margin: 4px;
    }
</style>

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
                      <h3 class="panel-title"><b><?= Yii::t('fe', $title); ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search' => [
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                                'data-table-id' => 'example',
                                'data-options' => 'click',
                            ]
                         ], 
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/kasir/lap-rekap-jasa-dokter/create',
                                // 'style' => 'z-index:1065;'
                            ]
                        ],
                        'delete'=>[
                            'attributes'=>[
                                'data-additional'=>'data-rm',
                                'id' => 'btn-delete-jd',
                                'disabled' => 'true',
                            ]
                        ],
                        'reset' => [
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                                'data-table-id' => 'example',
                                'data-options' => 'click',
                            ]
                         ],
                         'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'kasir/lap-rekap-jasa-dokter/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                        'pdf' => [
                            'title' => 'Cetak Summary Jasa Dokter'
                        ],
                        'rincian-details' => [
                            'title' => Yii::t('fe', 'Cetak Rincian Jasa Dokter'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id'=>'cetak-rincian-jasdok',
                                'data-options' => 'pdf',
                                'target'=>'_blank'
                            ]
                        ],
                    ]);?>
                    <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>Flag Bayar Jasa Dokter",[
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-flag-bayar-jasdok',
                        'disabled' => 'true',
                    ]); ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                              <div class="container">
                                <img src="/media/img/icon-app/Jasa.png" class="img-rekap">
                                <label style="font-size:1vw;bottom: 10px;position: relative;"><b>Jasa</b></label>
                                <span style="font-size:1vw;position: relative;top: 10px;right: 30px;"><b id="jasa">Rp. 0 </b></span>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                              <div class="container">
                                <img src="/media/img/icon-app/Bruto.png" class="img-rekap">
                                <label style="font-size:1vw;bottom: 10px;position: relative;"><b>Bruto</b></label>
                                <span style="font-size:1vw;position: relative;top: 10px;right: 38px;"><b id="bruto">Rp. 0 </b></span>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                              <div class="container">
                                <img src="/media/img/icon-app/DPP.png" class="img-rekap">
                                <label style="font-size:1vw;bottom: 10px;position: relative;"><b>DPP</b></label>
                                <span style="font-size:1vw;position: relative;top: 10px;right: 30px;"><b id="dpp">Rp. 0 </b></span>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="table-wrapper table-scroll-x">
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"><input type="checkbox" class="pickMe select-checkbox" name="pickMe" value="all"></th>
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Tanggal Transaksi");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Bayar Jasa Dokter");?></th>
                                <th><?=\Yii::t("fe", "Nama Dokter");?></th>
                                <th><?=\Yii::t("fe", "Status Billing");?></th>
                                <th><?=\Yii::t("fe", "Status Bayar Jasa Dokter");?></th>
                                <th><?=\Yii::t("fe", "No Transaksi");?></th>
                                <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                <th><?=\Yii::t("fe", "Ruangan");?></th>
                                <th><?=\Yii::t("fe", "Tindakan");?></th>
                                <th><?=\Yii::t("fe", "Nama Jasa");?></th>
                                <th><?=\Yii::t("fe", "Tarif");?></th>
                                <th><?=\Yii::t("fe", "Jasa (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Bruto (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "DPP (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Keterangan");?></th>
                                <th><?=\Yii::t("fe", "Jenis Transaksi");?></th>
                                <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                                <th><?=\Yii::t("fe", "Penjamin");?></th>
                                <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                                <th><?=\Yii::t("fe", "Pelayanan");?></th>
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
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
$this->registerJs('
    var status_bayar = '.json_encode($status_bayar).';
    var status_jasa = '.json_encode($status_jasa).';
    var ruangan      = '.json_encode($ruangan).';
    var cara_bayar   = '.json_encode($cara_bayar).';
    var penjamin     = '.json_encode($penjamin).';
    var pegawai     = '.json_encode($pegawai).';
    var pelayanan = '.json_encode($pelayanan).';
    var kelas_pelayanan = '.json_encode($kelas_pelayanan).';
    ', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END); ?>
