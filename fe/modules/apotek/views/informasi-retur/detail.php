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
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .select2-selection select2-selection--single {
        height: 340px !important;
    }

    .custom-table {
        height: 450px !important;
        overflow-y: scroll;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php 
                    $toolbar = [
                        'back',
                        'print-custom' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Cetak'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-cetak-retur',
                                'class' => 'cetak-retur',
                                'target' => '_blank',
                                'class' => 'spa',
                                'data-options' => 'link',
                                'data-target' => Url::home() . 'apotek/transaksi-retur/cetak-pdf?returresep_id=' . $retur,
                                'style' => $type == 'tambah' ? 'display: none' : '',
                                'disabled' => $statusRetur == DocoConstants::BATAL_RETUR ? true : ($statusRetur == DocoConstants::RETUR_BELUM_VERIFIKASI ? true : false),
                            ]
                        ],
                        'simpan' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'id' => 'save-retur',
                                'data-options' => 'click',
                            ]
                        ],
                        'edit' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-pencil',
                            'attributes' => [
                                'id' => 'edit-retur',
                                'data-options' => 'click',
                                'disabled' => ($statusRetur == DocoConstants::RETUR_BELUM_VERIFIKASI ? false : true)
                            ]
                        ],
                        'log-activity' => [
                            'type'  => 'button',
                            'title' => Yii::t('fe', 'Log Activity'),
                            'icon'  => 'fa fa-list',
                            'attributes' => [
                                'id' => 'log-activity',
                                'data-width'  => '80%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => Url::home() . 'apotek/informasi-retur/log-activity?id=' . $retur
                            ]
                        ],
                    ];

                    if($statusRetur == DocoConstants::RETUR_BELUM_VERIFIKASI) {
                        $toolbar['batal'] = [
                            'type'  => 'button',
                            'title' => Yii::t('fe', 'Batal Retur'),
                            'icon'  => 'fa fa-trash',
                            'attributes' => [
                                'class' => 'hidden',
                                'id' => 'batal-retur',
                                'data-options' => 'click'
                            ]
                        ];
                    }

                    $textBtnVerif = $type == 'lihat' ? \Yii::t('fe', 'Verifikasi') : \Yii::t('fe', 'Simpan & Verifikasi');
                    if($hasAccessVerif && $statusRetur == DocoConstants::RETUR_BELUM_VERIFIKASI) {
                        $toolbar['verifikasi'] = [
                            'type' => 'button',
                            'title' => $textBtnVerif,
                            'icon' => 'fa fa-check',
                            'attributes' => [
                                'id' => 'verifikasi',
                                'data-options' => 'click',
                            ]
                        ];
                    }
                ?>
                <?= DocoHelpers::generateToolbar($toolbar); ?>
            </div>
            <div class="panel-body">
                <!-- Informasi Resep -->
                <input type="hidden" value="<?= DocoHelpers::decrypt($retur) ?>" id="returresep_id" >
                <div class="panel-body">
                    <table width="100%" class="tabel">
                        <tbody>
                            <tr>
                                <td style="width: 20%"><?= Yii::t('fe', 'Nama Pasien') ?></td>
                                <td style="width: 20%"><?= Yii::t('fe', 'No. Rekam Medik') ?></td>
                                <td style="width: 20%"><?= Yii::t('fe', 'No. Pendaftaran') ?></td>
                                <td style="width: 20%"><?= Yii::t('fe', 'DPJP') ?></td>
                                <td style="width: 20%"><?= Yii::t('fe', 'Ruangan Akhir') ?></td>
                            </tr>
                            <tr>
                                <td class="bold"><?= ArrayHelper::getValue($dataPasien, 'nama_pasien'); ?></td>
                                <td class="bold"><?= ArrayHelper::getValue($dataPasien, 'no_rekam_medik'); ?></td>
                                <td class="bold"><?= ArrayHelper::getValue($dataPasien, 'no_pendaftaran'); ?></td>
                                <td class="bold"><?= ArrayHelper::getValue($dataPasien, 'dokter_dpjp'); ?></td>
                                <td class="bold"><?= ArrayHelper::getValue($dataPasien, 'ruangan_akhir'); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>

                    <div class="d-flex" style="display: flex; margin-bottom: 10px;">
                        <div style="width: 20%; margin-right: 10px;">
                            <label for="">Tanggal Retur</label>
                            <!-- <?= Html::textInput('tanggal_retur', date('Y-m-d h:i:s'), [
                                        'class' => 'form-control tanggal_retur',
                                    ]);
                                    ?> -->

                            <?=
                            DateTimePicker::widget([
                                'id' => 'tanggal_retur',
                                'name' => 'tanggal_retur',
                                'type' => DateTimePicker::TYPE_INPUT,
                                'value' => date('d-m-Y h:i'),
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-mm-yyyy hh:ii'
                                ],
                                'disabled' => true
                            ]);
                            ?>
                        </div>
                        <div class="select2-md" style="width: 20%; margin-right: 10px;">
                            <label for="">Diretur Ke Depo</label>
                            <?= Select2::widget([
                                'id' => 'ruangan_retur',
                                'name' => 'ruangan_retur',
                                'data' => $ruanganFarmasi,
                                'options' => [
                                    'placeholder' => '— Pilih Ruangan—',
                                    'class' => 'ruangan_retur',
                                    'style' => 'height'
                                ],
                                'pluginOptions' => [
                                    'tags' => true,
                                    'tokenSeparators' => [',', '_'],
                                    'maximumInputLength' => 50
                                ],
                            ]);
                            ?>
                        </div>
                        <div style="width: 20%;" class="hidden" id="alasan_edit">
                            <label for="">Alasan Edit <span style="color: red;">*</span></label>
                            <textarea placeholder="Masukkan alasan edit Retur" id="edit_alasan" name="edit_alasan" rows="3" maxlength="100" class="form-control edit_alasan" required></textarea>
                        </div>
                    </div>
                </div>
                <div>
                    <!-- legends -->
                    <div class="col-md-12">
                        <div class='legend-index'>
                            <div class='legend-header'><strong>Keterangan</strong></div>
                                <div class="legend-wrapper">
                                <div class="legend-information">
                                    <div class="legend-information__color" style="background-color: #ffcece"></div>
                                    <div class="legend-information__text">Qty retur melebihi total pemberian</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="panel panel-default custom-table">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b>Daftar Obat/Alkes Resep</b></h6>
                            </div>
                            <table class="table datatable-basic table-striped" id="bulk-retur-resep" style="width: 100%;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Nama Obat/Alkes') ?></th>
                                        <th style="<?= $isReturPendaftaran ? '' : 'display: none;' ?>"><?= Yii::t('fe', 'Total Pemberian') ?></th>
                                        <th><?= Yii::t('fe', 'Satuan Pemberian') ?></th>
                                        <th><?= Yii::t('fe', 'Harga Satuan (Rp)') ?></th>
                                        <th><?= Yii::t('fe', 'Qty Retur') ?></th>
                                        <th><?= Yii::t('fe', 'Total Retur (Rp)') ?></th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody id="list-obat">
                                    <tr>
                                        <td colspan="7" class="text-center"><?= Yii::t('fe', 'Data transaksi resep tidak ditemukan') ?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr style="background: #fff">
                                        <td colspan="<?= $isReturPendaftaran ? "6" : "5" ?>" style="text-align:right;"><b>Total Rp.</b></td>
                                        <td id="subtotalItemresep" class="text-right" data-total="0">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="panel panel-default custom-table">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b>Daftar Obat/Alkes BMHP</b></h6>
                            </div>
                            <table class="table datatable-basic table-striped" id="bulk-retur-bmhp" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Nama Obat/Alkes') ?></th>
                                        <th><?= Yii::t('fe', 'Total Pemberian') ?></th>
                                        <th><?= Yii::t('fe', 'Satuan Pemberian') ?></th>
                                        <th><?= Yii::t('fe', 'Harga Satuan (Rp)') ?></th>
                                        <th><?= Yii::t('fe', 'Qty Retur') ?></th>
                                        <th><?= Yii::t('fe', 'Total Retur (Rp)') ?></th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody id="list-bmhp">
                                    <tr>
                                        <td colspan="7" class="text-center"><?= Yii::t('fe', 'Data transaksi BMHP tidak ditemukan') ?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr style="background: #fff">
                                        <td colspan="6" style="text-align:right;"><b>Total Rp.</b></td>
                                        <td id="subtotalItembmhp" class="text-right" data-total="0">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
        var transaksiResep = ' . json_encode($dataResep) . ';
        var transaksiBmhp = ' . json_encode($dataBmhp) . ';
        var typeRetur = "' . $type . '";
        var pendaftaran_id = ' . DocoHelpers::decrypt($id) . ';
        var ruangan_id = "' . $ruangan_id . '";
        var tgl_retur = "' . $tglRetur . '";
        var returResepId = "' . $retur . '";
        var isReturPendaftaran = "'. $isReturPendaftaran .'";
        var verifType;
        if(typeRetur == "lihat") {
            verifType = "verifikasi";
        } else if(typeRetur == "edit") {
            verifType = "simpan-verifikasi";
        }
    ');
$this->registerCss($this->render('../assets/css/apotek.css'));
$this->registerJs($this->render('js/detail.js'));
?>