<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;


$this->title = Dhtml::getTitleMenu();
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
                      <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
                <?php
                    $btn_toolbar = [
                        'search' => [
                            'attributes' => [
                               'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                               'data-table-id' => 'laporan-penerimaan-obat-alkes',
                               'data-options' => 'click',
                            ]
                         ],
                         'reset' => [
                            'attributes' => [
                               'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                               'data-table-id' => 'laporan-penerimaan-obat-alkes',
                               'data-options' => 'click',
                            ]
                         ],
                         'export-excel-serconn' => [
                            'type' => 'button',
                            'icon' => 'fa fa-file-excel-o',
                            'title' => \Yii::t('fe', 'Unduh Excel'),
                            'attributes' => [
                                'id' => 'data-export-excel-bg',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '75%',
                                'data-url' => Url::home() . 'pengadaan/laporan-penerimaan-obat-alkes/show-popup-excel?',
                                'data-table-param-exclude' => ['columns'],
                            ]
                        ],
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-penerimaan-obat-alkes');?>
            </div>

            <div class="panel-body">
                <table id="laporan-penerimaan-obat-alkes" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=\Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Tanggal PR");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Approve PR");?></th>
                            <th><?=\Yii::t("fe", "Kode Supplier");?></th>
                            <th><?=\Yii::t("fe", "Nama Supplier");?></th>
                            <th><?=\Yii::t("fe", "Nama Manufaktur");?></th>
                            <th><?=\Yii::t("fe", "Payment Term");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Diterima Oleh");?></th>
                            <th><?=\Yii::t("fe", "Status Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Validasi PO");?></th>
                            <th><?=\Yii::t("fe", "Nomor PO");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty PO");?></th>
                            <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                            <th><?=\Yii::t("fe", "Qty Peneirmaan");?></th>
                            <th><?=\Yii::t("fe", "Qty Return");?></th>
                            <th><?=\Yii::t("fe", "Qty Terima");?></th>
                            <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                            <th><?=\Yii::t("fe", "PO Balance");?></th>
                            <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                            <th><?=\Yii::t("fe", "Nilai Konversi");?></th>
                            <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                            <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                            <th><?=\Yii::t("fe", "Harga Netto");?></th>
                            <th><?=\Yii::t("fe", "Harga");?></th>
                            <th><?=\Yii::t("fe", "Discount");?></th>
                            <th><?=\Yii::t("fe", "PPN");?></th>
                            <th><?=\Yii::t("fe", "Sub Total");?></th>
                            <th><?=\Yii::t("fe", "Total");?></th>
                            <th><?=\Yii::t("fe", "Catatan PO");?></th>
                            <th><?=\Yii::t("fe", "Nomor PR");?></th>
                            <th><?=\Yii::t("fe", "Nomor Batch");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                            <th><?=\Yii::t("fe", "No. Surat Jalan");?></th>
                            <th><?=\Yii::t("fe", "No. Faktur");?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    const filter = ' . json_encode($filter) . ';
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
