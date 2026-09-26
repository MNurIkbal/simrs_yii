<?php

/**
 * @Author: sunarko
 * @Date:   2018-05-15 10:26:11
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-31 13:33:33
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Pasien Sudah Bayar'), 'url' => ['index']];
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
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'print-sudah-bayar'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes'=>[
                          'id'=>'btn-print',
                          'class'=>'btn-print-sudah-bayar',
                          'data-options'=>'click',
                          'data-target'=> '/kasir/inf-pasien-sudah-bayar/export-pdf?id='.$id.'&pdf_id='.$pendaftaran_id."&tipe_pasien=".$tipe_pasien,
                        ]
                    ],
                    // 'print-kwitansi'=>[
                    //     'type'=>'button',
                    //     'title' => \Yii::t('fe', 'Kwitansi'),
                    //     'icon' => 'fa fa-print',
                    //     'method' => 'not-exist',
                    //     'attributes' => [
                    //         'id'=>'btn-print-kwitansi',
                    //         'class'=>'btn-print-sudah-bayar',
                    //         'data-options'=>'click',
                    //         'data-target'=> '/kasir/inf-pasien-sudah-bayar/print-kwitansi?id='.$id.'&pdf_id='.$pendaftaran_id."&tipe_pasien=".$tipe_pasien,
                    //     ]
                    // ],
                    // 'print-bkm'=>[
                    //     'type'=>'button',
                    //     'title' => \Yii::t('fe', 'BKM'),
                    //     'icon' => 'fa fa-print',
                    //     'method' => 'not-exist',
                    //     'attributes' => [
                    //         'id'=>'btn-print-bkm',
                    //         'class'=>'btn-print-sudah-bayar',
                    //         'data-options'=>'click',
                    //         'data-target'=>'/kasir/inf-pasien-sudah-bayar/print-bkm?id='.$id.'&pdf_id='.$pendaftaran_id."&tipe_pasien=".$tipe_pasien,
                    //     ]
                    // ],
                    // 'retur'=>[
                    //     'type'=>'link',
                    //     'title' => \Yii::t('fe', 'Retur'),
                    //     'icon' => 'fa fa-shopping-cart',
                    //     'method' => 'not-exist',
                    //     'attributes' => [
                    //         'class'=>'hidden',
                    //         'id'=>'btn-print-retur',
                    //         'data-options'=>'link',
                    //         'data-target'=>'/kasir/tra-retur-tagihan?id='.$id."&tipe_pasien=".$tipe_pasien,
                    //     ]
                    // ],
                ]);?>
            </div>

            <div class="panel-body">
                <div class="row row-eq-height " style="margin-top:10px;">
                <!-- Informasi pasien -->
                    <div class="col-md-8" id="informasi">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>

                                    <p class="p-data" id="data-pasien">
                                        <?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?> -
                                        <b class="font" ><?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?></b>
                                        (<?= isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-' ?>)
                                    </p>

                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>

                                </div>
                            </a>

                            <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                                <div class="col-xs-2">
                                    <div class="border-img">
                                        <?php
                                        $filename = isset($header['photopasien']) ? !empty($header['photopasien']) ? '/media/img/pasien/'.$header['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                        ?>
                                        <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                                    </div>
                                </div>

                                <div class="col-xs-9">
                                    <div class="row">
                                        <br>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?> -
                                                <?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?> -
                                                <?= isset($header['jeniskelamin']) ? $header['jeniskelamin'] : '-' ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-' ?> -
                                                (<?= isset($header['umur']) ? $header['umur'] : '-' ?>)
                                            </p>
                                        </div>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                            <p>
                                                <?= isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-' ?> -
                                                (<?= isset($header['tgl_pendaftaran']) ? date('d-M-Y', strtotime($header['tgl_pendaftaran'])) : '-' ?>)
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                            <p>
                                                <?= isset($header['kelaspelayanan_nama']) ? $header['kelaspelayanan_nama'] : '-' ?> -
                                                <?= isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '-' ?> -
                                                <?= isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '-' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>
                                </div>
                            </a>
                            <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                                <div class="row row-eq-height">
                                    <br>
                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", " Penyakit") ?></b>
                                        <p>
                                            <?= isset($header['jeniskasuspenyakit_nama']) ? $header['jeniskasuspenyakit_nama'] : '-' ?>
                                        </p>

                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan") ?></b>
                                        <p>
                                            <?php $ruanganBayar = isset($header['ruangan_nama']) ? $header['ruangan_nama'] : ''?>
                                            <?= $ruanganBayar ?>
                                        </p>
                                    </div>

                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Dokter") ?></b>
                                        <p>
                                            <?= !empty($header['pegawai_rd_rj']) ? $header['pegawai_rd_rj'] : null ?>
                                        </p>

                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Status Bayar") ?></b>
                                        <p>
                                            <?= isset($header['status_bayar']) ? $header['status_bayar'] : '-' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Riwayat Pembayaran -->
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?= Yii::t('fe', 'Riwayat Pembayaran') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="test-footer"></div>
                            <div class="panel-body">
                                <table class="table datatable-basic table-striped table-hover" style="width: 100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?= Yii::t('fe', 'Total Tagihan') ?></th>
                                            <th><?= !empty($header['total_tagihan'])
                                                    ? DocoHelpers::rupiahDisplay($header['total_tagihan']) : '0' ?></th>
                                        </tr>
                                        <tr class="bg-inverse">
                                            <th><?= Yii::t('fe', 'Total Uang Muka') ?></th>
                                            <th><?= !empty($header['total_uang_muka'])
                                                    ? DocoHelpers::rupiahDisplay($header['total_uang_muka']) : '0' ?></th>
                                        </tr>
                                        <tr class="bg-inverse">
                                            <th><?= Yii::t('fe', 'Subsidi Asuransi') ?></th>
                                            <th><?= !empty($header['subsidi_asuransi'])
                                                    ? DocoHelpers::rupiahDisplay($header['subsidi_asuransi']) : '0' ?></th>
                                        </tr>
                                        <tr class="bg-inverse">
                                            <th><?= Yii::t('fe', 'Total Sudah Dibayarkan') ?></th>
                                            <th><?= !empty($header['total_sudah_dibayarkan'])
                                                    ? DocoHelpers::rupiahDisplay($header['total_sudah_dibayarkan']) : '0' ?></th>
                                        </tr>
                                        <tr class="bg-inverse">
                                            <th><?= Yii::t('fe', 'Total Sisa Tagihan') ?></th>
                                            <th><?= !empty($header['total_sisa_tagihan'])
                                                    ? DocoHelpers::rupiahDisplay($header['total_sisa_tagihan']) : '0' ?></th>
                                        </tr>
                                        <tr class="bg-inverse">
                                            <th><?= Yii::t('fe', 'Biaya Administrasi') ?></th>
                                            <th><?= !empty($header['biaya_administrasi'])
                                                    ? DocoHelpers::rupiahDisplay($header['biaya_administrasi']) : '0' ?></th>
                                        </tr>
                                        <tr class="bg-inverse">
                                            <th><?= Yii::t('fe', 'Pembulatan') ?></th>
                                            <th><?= !empty($header['pembulatan'])
                                                    ? DocoHelpers::rupiahDisplay($header['pembulatan']) : '0' ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Tindakan -->
                <input type="hidden" id="id_tindakan" value="<?= !empty($dataTindakan) ? $dataTindakan['info']['pendaftaran_id'] : '' ?>">
                <?php if ($dataTindakan):
                    foreach ($dataTindakan as $key => $value) {
                        if($key != 'info'){
                            ?>
                            <div class="row" style="margin-top:10px;">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h6 class="panel-title"><?= "Tindakan ". str_replace('-', ' ', $key) ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                                            <div class="heading-elements">
                                                <ul class="icons-list">
                                                    <li><a data-action="collapse"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="test-footer"></div>
                                        <div class="panel-body">
                                            <table id="table-tindakan-<?=$key?>" class="table datatable-basic table-striped table-hover dataTable" style="width: 100%">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                    <th width="1">No</th>
                                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                                        <th><?= Yii::t('fe', 'Tarif Cyto') ?></th>
                                                        <th><?= Yii::t('fe', 'Tarif Diskon') ?></th>
                                                        <th><?= Yii::t('fe', 'Tarif Dijamin') ?></th>
                                                        <th><?= Yii::t('fe', 'Tarif Dibayarkan') ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5" style="text-align: right;font-weight:bold;"><?= Yii::t('fe', 'Total') ?></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                ?>
                <?php endif; ?>

                <!-- Info Obat -->
                <input type="hidden" id="id_obat" value="<?= !empty($dataObat) ? $dataObat[0]['pendaftaran_id'] : '' ?>">
                <?php if ($dataObat): ?>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h6 class="panel-title"><?= Yii::t('fe', 'Obat') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                              <div class="heading-elements">
                                <ul class="icons-list">
                                  <li><a data-action="collapse"></a></li>
                                </ul>
                              </div>
                            </div>
                            <div class="test-footer"></div>
                            <div class="panel-body">
                              <table class="table datatable-basic table-striped table-hover dataTable" id="exampleobat" style="width: 100%">
                                <thead>
                                  <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Tanggal Order Obat') ?></th>
                                    <th><?= Yii::t('fe', 'Nama Obat') ?></th>
                                    <th><?= Yii::t('fe', 'Qty') ?></th>
                                    <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                    <th><?= Yii::t('fe', 'Tarif Diskon') ?></th>
                                    <th><?= Yii::t('fe', 'Tarif Dijamin') ?></th>
                                    <th><?= Yii::t('fe', 'Tarif Dibayarkan') ?></th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right;font-weight:bold;"><?= Yii::t('fe', 'Total') ?></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                              </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info Pemeriksaan Laboratorium -->
                  <input type="hidden" id="id_lab" value="<?= !empty($dataLab) ? $dataLab[0]['pendaftaran_id'] : '' ?>">
                  <?php if ($dataLab): ?>
                    <div class="row">
                        <div class="col-md-12" style="margin-top:10px;">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Laboratorium') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="test-footer"></div>
                                <div class="panel-body">
                                    <table class="table datatable-basic table-striped table-hover dataTable" id="examplelab" style="width: 100%">
                                        <thead>
                                            <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?= Yii::t('fe', 'Tanggal Pemeriksaan') ?></th>
                                            <th><?= Yii::t('fe', 'Nama Pemeriksaan') ?></th>
                                            <th><?= Yii::t('fe', 'Qty') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Cyto') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Diskon') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Dijamin') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Dibayarkan') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" style="text-align: right;font-weight:bold;"><?= Yii::t('fe', 'Total') ?></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  <?php endif; ?>

                <!-- Info Pemeriksaan Radiologi -->
                  <input type="hidden" id="id_radiologi" value="<?= !empty($dataRadiologi) ? $dataRadiologi[0]['pendaftaran_id'] : '' ?>">
                  <?php if ($dataRadiologi): ?>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Radiologi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="test-footer"></div>
                                <div class="panel-body">
                                    <table class="table datatable-basic table-striped table-hover dataTable" id="exampleradiologi" style="width: 100%">
                                        <thead>
                                            <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?= Yii::t('fe', 'Tanggal Pemeriksaan') ?></th>
                                            <th><?= Yii::t('fe', 'Nama Pemeriksaan') ?></th>
                                            <th><?= Yii::t('fe', 'Qty') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Cyto') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Diskon') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Dijamin') ?></th>
                                            <th><?= Yii::t('fe', 'Tarif Dibayarkan') ?></th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" style="text-align: right;font-weight:bold;"><?= Yii::t('fe', 'Total') ?></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs("
    var arrRuangan = ".json_encode($arrRuangan).";
    $(document).on('click', '#btn-print', function(){
        window.open($(this).attr('data-target'), '_blank')
    })
    $(document).on('click', '#btn-print-kwitansi', function(){
        window.open($(this).attr('data-target'), '_blank')
    })
    $(document).on('click', '#btn-print-bkm', function(){
        window.open($(this).attr('data-target'), '_blank')
    })

    function initTable(tableId, _param){
        $(tableId).docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, 'asc']],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 'Semua']],
            ajax: '/kasir/inf-pasien-sudah-bayar/get-tindakan?id=$pendaftaran_id&tipe_pasien=$tipe_pasien&ppId=$id&key='+_param,
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Tindakan'))."', data: 'tgl_pelayanan', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Nama Tindakan'))."', data: 'tindakan_obat_nama', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'qty',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Satuan'))."',  data: 'tarif_satuan',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Cyto'))."',  data: 'tarif_cyto',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Diskon'))."',  data: 'tarif_diskon',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dijamin'))."',  data: 'tarif_dijamin',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dibayarkan'))."',  data: 'tarif_dibayarkan',searchable: false,orderable: false},
            ],
            footerCallback: function(rowNum, data, start, end, display) {
                var api = this.api(), data;
                var intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };
                function addDots(nStr) {
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';
                    var rgx = /(\d+)(\d{3})/;
                    while (rgx.test(x1)) {
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }
                    return x1 + x2;
                };
                pageTotalCito = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDiskon = api.column(6, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDijamin = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDibayar = api.column(8, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                $(api.column(5).footer()).html(`<b>Rp. `+addDots(pageTotalCito)+'</b>');
                $(api.column(6).footer()).html(`<b>Rp. `+addDots(pageTotalDiskon)+'</b>');
                $(api.column(7).footer()).html(`<b>Rp. `+addDots(pageTotalDijamin)+'</b>');
                $(api.column(8).footer()).html(`<b>Rp. `+addDots(pageTotalDibayar)+'</b>');
            }
        });
        $('.dataTables_filter').hide();
    }
var loadTindakan = function(){
    $.each(arrRuangan, function(k, v){
        initTable('#table-tindakan-'+v, v)
    })
}
    $(document).ready(function(){
    var obat_id = $('#id_obat').val();
    var tindakan_id = $('#id_tindakan').val();
    var lab_id = $('#id_lab').val();
    var radiologi_id = $('#id_radiologi').val();
    if (tindakan_id) {
        loadTindakan();
    }
    if (obat_id) {
        table_obat = $('#exampleobat').docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, 'asc']],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 'Semua']],
            ajax: '/kasir/inf-pasien-sudah-bayar/get-data-tindakan?id=$pendaftaran_id&tipe_pasien=$tipe_pasien&type=obat&ppId=$id',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Order Obat'))."', data: 'tgl_pelayanan', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Nama Obat'))."', data: 'tindakan_obat_nama', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'qty',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Satuan'))."',  data: 'tarif_satuan',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Diskon'))."',  data: 'tarif_diskon',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dijamin'))."',  data: 'tarif_dijamin',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dibayarkan'))."',  data: 'tarif_dibayarkan',searchable: false,orderable: false},
            ],
            footerCallback: function(rowNum, data, start, end, display) {
                var api = this.api(), data;
                var intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };
                function addDots(nStr) {
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';
                    var rgx = /(\d+)(\d{3})/;
                    while (rgx.test(x1)) {
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }
                    return x1 + x2;
                };
                pageTotalDiskon = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDijamin = api.column(6, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDibayar = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                $(api.column(5).footer()).html(`<b>Rp. `+addDots(pageTotalDiskon)+'</b>');
                $(api.column(6).footer()).html(`<b>Rp. `+addDots(pageTotalDijamin)+'</b>');
                $(api.column(7).footer()).html(`<b>Rp. `+addDots(pageTotalDibayar)+'</b>');
            }
        });
    }
    if (lab_id) {
        table_lab = $('#examplelab').docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, 'asc']],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 'Semua']],
            ajax: '/kasir/inf-pasien-sudah-bayar/get-data-tindakan?id=$pendaftaran_id&type=lab&ppId=$id',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Tindakan'))."', data: 'tgl_pelayanan', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Nama Pemeriksaan'))."', data: 'tindakan_obat_nama', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'qty',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Satuan'))."',  data: 'tarif_satuan',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Cyto'))."',  data: 'tarif_cyto',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Diskon'))."',  data: 'tarif_diskon',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dijamin'))."',  data: 'tarif_dijamin',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dibayarkan'))."',  data: 'tarif_dibayarkan',searchable: false,orderable: false},
            ],
            footerCallback: function(rowNum, data, start, end, display) {
                var api = this.api(), data;
                var intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };
                function addDots(nStr) {
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';
                    var rgx = /(\d+)(\d{3})/;
                    while (rgx.test(x1)) {
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }
                    return x1 + x2;
                };
                pageTotalCito = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDiskon = api.column(6, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDijamin = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDibayar = api.column(8, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                $(api.column(5).footer()).html(`<b>Rp. `+addDots(pageTotalCito)+'</b>');
                $(api.column(6).footer()).html(`<b>Rp. `+addDots(pageTotalDiskon)+'</b>');
                $(api.column(7).footer()).html(`<b>Rp. `+addDots(pageTotalDijamin)+'</b>');
                $(api.column(8).footer()).html(`<b>Rp. `+addDots(pageTotalDibayar)+'</b>');
            }
        });
        $('.dataTables_filter').hide();
    }
    if (radiologi_id) {
        table_radio = $('#exampleradiologi').docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, 'asc']],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 'Semua']],
            ajax: '/kasir/inf-pasien-sudah-bayar/get-data-tindakan?id=$pendaftaran_id&type=rad&ppId=$id',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Tindakan'))."', data: 'tgl_pelayanan', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Nama Pemeriksaan'))."', data: 'tindakan_obat_nama', searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'qty',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Satuan'))."',  data: 'tarif_satuan',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Cyto'))."',  data: 'tarif_cyto',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Diskon'))."',  data: 'tarif_diskon',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dijamin'))."',  data: 'tarif_dijamin',searchable: false,orderable: false},
                {title: '".(\Yii::t('fe', 'Tarif Dibayarkan'))."',  data: 'tarif_dibayarkan',searchable: false,orderable: false},
            ],
            footerCallback: function(rowNum, data, start, end, display) {
                var api = this.api(), data;
                var intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };
                function addDots(nStr) {
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';
                    var rgx = /(\d+)(\d{3})/;
                    while (rgx.test(x1)) {
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }
                    return x1 + x2;
                };
                pageTotalCito = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDiskon = api.column(6, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDijamin = api.column(7, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                pageTotalDibayar = api.column(8, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                $(api.column(5).footer()).html(`<b>Rp. `+addDots(pageTotalCito)+'</b>');
                $(api.column(6).footer()).html(`<b>Rp. `+addDots(pageTotalDiskon)+'</b>');
                $(api.column(7).footer()).html(`<b>Rp. `+addDots(pageTotalDijamin)+'</b>');
                $(api.column(8).footer()).html(`<b>Rp. `+addDots(pageTotalDibayar)+'</b>');
            }
        });
        $('.dataTables_filter').hide();
    }
    });
    $(document).ready(function(){
        $('#info-heading').click(function(){
            $('#data-pasien').toggle();
        });
    });
", View::POS_END, 'js');

?>
