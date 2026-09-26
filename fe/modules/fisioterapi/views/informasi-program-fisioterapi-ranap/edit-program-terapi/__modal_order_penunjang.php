<?php

/**
 *  CLONE FROM PENDAFTARAN
 * @Author: Rizal
 * @Date:   2018-07-26 14:53:35
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-24 11:22:57
 */

use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
?>
<style>
    .paket-fisio-header {
        background-color: chocolate !important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="list_checkbox">
            <div class="row">
                <div class="col-sm-6">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <input id="txt-search" name="searching" class="form-control search-radlab" data-ruangan_id="<?= @$params['ruangan_id']; ?>" data-penjamin_id="<?= @$params['penjamin_id'] ?>" data-kelaspelayanan_id="<?= @$params['kelaspelayanan_id'] ?>" data-instalasi_id="<?= @$params['instalasi_id'] ?>" placeholder="Pencarian" />
                            <span class="input-group-btn">
                                <button id="btn-search_radlab" class="btn removefocus" type="button">
                                    <i class="fa fa-search "></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="margin-top: 15px;">
                    <input type="checkbox" name="check_kode" class="check_kode"> Tampilkan Kode
                </div>
            </div>
            <br />
            <div id="loading-content"></div>
            <div class="row content-radlab">
                <div class="col-sm-12">
                    <?php if ($result) {
                        $i = 0;
                    ?>
                        <?php
                        foreach ($result as $header => $detail_header) {
                            $checkIsPaketFisio = ArrayHelper::getValue($detail_header, '0.is_paketfisio') ? true : false;
                            $isAktif = $checkIsPaketFisio == false ? true : ArrayHelper::getValue($detail_header, '0.is_aktif');
                            $isDeleted = $checkIsPaketFisio == false ? false : ArrayHelper::getValue($detail_header, '0.is_deleted');
                            if($isAktif == true && $isDeleted == false){
                            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $header)));
                            $paketFisioBgClass = "";
                            if ($checkIsPaketFisio) $paketFisioBgClass = "paket-fisio-header";
                        ?>
                            <div class="col-sm-4">
                                <div class="panel panel-default">
                                    <a id="heading-<?= $slug ?>" data-toggle="collapse" href="#tab-<?= $slug ?>" role="button" aria-expanded="true" aria-controls="tab-<?= $slug ?>" class="">
                                        <div class="panel-heading flex-container <?= $paketFisioBgClass ?>" style="background-color:#37474f;color:white;">
                                            <h6 class="panel-title text-bold" style="font-size:12px;"><?= strtoupper($header) ?></h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-up"></i></li>
                                            </ul>
                                        </div>
                                    </a>
                                    <div class="panel-body multi-collpase label-information collapse out" id="tab-<?= $slug ?>" aria-expanded="true">
                                        <div class="row" id="parentOption">
                                            <?php
                                            if ($checkIsPaketFisio) {
                                                $firstDetail = ArrayHelper::getValue($detail_header, '0.paketfisio_jumlah');
                                                echo "
                                                <p style='margin-left: 10px; margin-top: 5px; font-weight: 400'>
                                                    <span>Jumlah yang </span><b>Harus</b> dipilih : $firstDetail
                                                </p>";
                                            }
                                            ?>
                                            <?php if (!empty($detail_header)) { ?>
                                                <?php
                                                foreach ($detail_header as $key => $detail2) {
                                                    $selected = false;
                                                ?>
                                                    <p style="margin-left:10px;margin-top:10px;">
                                                        <?= Html::checkbox('checkPemeriksaan', $selected, [
                                                            'id' =>  ArrayHelper::getValue($detail2, 'is_paketfisio') == true ? $detail2['parentdaftartindakan_id'] . '-' . $detail2['daftartindakan_id'] : $detail2['daftartindakan_id'],
                                                            'class' => 'cb_penunjang',
                                                            'data-catatan' => '',
                                                            'data-jenis' => $detail2['jenis'],
                                                            'data-tariftindakan_id' => $detail2['tariftindakan_id'],
                                                            'data-ruangan_id' => $detail2['ruangan_id'],
                                                            'data-ruangan_nama' => $detail2['ruangan_nama'],
                                                            'data-instalasi_id' => $detail2['instalasi_id'],
                                                            'data-instalasi_nama' => $detail2['instalasi_nama'],
                                                            'data-ruanganpaket_id' => $detail2['ruanganpaket_id'],
                                                            'data-ruanganpaket_nama' => $detail2['ruanganpaket_nama'],
                                                            'data-perdatarif_id' => $detail2['perdatarif_id'],
                                                            'data-perdanama_sk' => $detail2['perdanama_sk'],
                                                            'data-kelaspelayanan_id' => $detail2['kelaspelayanan_id'],
                                                            'data-kelaspelayanan_nama' => $detail2['kelaspelayanan_nama'],
                                                            'data-penjamin_id' => $detail2['penjamin_id'],
                                                            'data-penjamin_nama' => $detail2['penjamin_nama'],
                                                            'data-kelompoktindakan_id' => $detail2['kelompoktindakan_id'],
                                                            'data-kelompoktindakan_nama' => $detail2['kelompoktindakan_nama'],
                                                            'data-kategoritindakan_id' => $detail2['kategoritindakan_id'],
                                                            'data-kategoritindakan_nama' => $detail2['kategoritindakan_nama'],
                                                            'data-daftartindakan_id' => $detail2['daftartindakan_id'],
                                                            'data-daftartindakan_nama' => $detail2['daftartindakan_nama'],
                                                            'data-tipepaket_id' => $detail2['tipepaket_id'],
                                                            'data-tipepaket_nama' => $detail2['tipepaket_nama'],
                                                            'data-komponentarif_id' => $detail2['komponentarif_id'],
                                                            'data-komponentarif_nama' => $detail2['komponentarif_nama'],
                                                            'data-harga_tariftindakan' => $detail2['harga_tariftindakan'],
                                                            'data-persencyto_tindakan' => $detail2['persencyto_tindakan'],
                                                            'data-persendiskon_tindakan' => $detail2['persendiskon_tindakan'],
                                                            'data-is_default' => $detail2['is_default'],
                                                            'data-is_akomodasi' => $detail2['is_akomodasi'],
                                                            'data-carabayar_id' => $detail2['carabayar_id'],
                                                            'data-is_konsultasi' => $detail2['is_konsultasi'],
                                                            'data-kamarruangan_nokamar' => $detail2['kamarruangan_nokamar'],
                                                            'data-kamarruangan_id' => $detail2['kamarruangan_id'],
                                                            'data-ambulan_id' => $detail2['ambulan_id'],
                                                            'data-no_polisi' => $detail2['no_polisi'],
                                                            'data-kelompokpemeriksaanlab_id' => $detail2['kelompokpemeriksaanlab_id'],
                                                            'data-nama_kelompok' => $detail2['nama_kelompok'],
                                                            'data-jenispemeriksaanlab_id' => $detail2['jenispemeriksaanlab_id'],
                                                            'data-jenispemeriksaanlab_nama' => $detail2['jenispemeriksaanlab_nama'],
                                                            'data-pemeriksaanlab_id' => $detail2['pemeriksaanlab_id'],
                                                            'data-pemeriksaanlab_nama' => $detail2['pemeriksaanlab_nama'],
                                                            'data-persen_penyulit' => $detail2['persen_penyulit'],
                                                            'data-index' =>  $i,
                                                            'data-is_cyto' => 0,
                                                            'label' => '&nbsp;&nbsp; <span class="kode-tindakan hidden">' . $detail2['kode'] . ' - </span>' . $detail2['daftartindakan_nama'],
                                                            'value' => $detail2['daftartindakan_id'],
                                                            'data-is_paketfisio' => ArrayHelper::getValue($detail2, 'is_paketfisio') ? 'true' : false,
                                                            'data-parentdaftartindakan_id' => ArrayHelper::getValue($detail2, 'parentdaftartindakan_id'),
                                                            'data-paketfisio_jumlah' => ArrayHelper::getValue($detail2, 'paketfisio_jumlah'),
                                                            'data-paketfisio_frekuensi' => ArrayHelper::getValue($detail2, 'paketfisio_frekuensi'),
                                                            'disabled' => false,
                                                            'data-is_approve_edit' => 0,
                                                        ]) ?>
                                                    </p>
                                                <?php
                                                    $i++;
                                                } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php }} ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-left">

</div>
<?php
$phpVars = [
    'konfigApproveFisio' => $konfigApproveFisio
];
$this->registerJsVar('phpVars', $phpVars);
$this->registerJs($this->render('__modal_order_penunjang.js'), View::POS_END, 'jsModal')
?>