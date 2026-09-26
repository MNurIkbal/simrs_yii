<?php

/**
 *  CLONE FROM PENDAFTARAN
 * @Author: Rizal
 * @Date:   2018-07-26 14:53:35
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-24 11:22:57
 */

use app\components\DocoConstants;
use yii\helpers\Html;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="list_checkbox">
            <div class="row">
                <?php
                if($result){
                    foreach ($result as $header => $detail_header) {
                ?>
                    <div class="col-sm-3">
                    <table id="table-checkbox" class="table datatable-basic table-striped table-hover dataTable no-footer">
                <?php
                    echo '<thead><tr class="bg-inverse"><th>'. $header .'</th></tr></thead>';
                    if(!empty($detail_header)) {
                        foreach ($detail_header as $key => $detail2) {
                                echo '<tr class="row-default"><td>
                                <input 
                                    type = "checkbox"
                                    id = "' . $detail2['daftartindakan_id'] . '"
                                    class = "cb_penunjang"
                                    data-jenis                     ="' . $detail2['jenis'] . '"
                                    data-tariftindakan_id          ="' . $detail2['tariftindakan_id'] . '"
                                    data-ruangan_id                ="' . $detail2['ruangan_id'] . '"
                                    data-ruangan_nama              ="' . $detail2['ruangan_nama'] . '"
                                    data-instalasi_id              ="' . $detail2['instalasi_id'] . '"
                                    data-instalasi_nama            ="' . $detail2['instalasi_nama'] . '"
                                    data-ruanganpaket_id           ="' . $detail2['ruanganpaket_id'] . '"
                                    data-ruanganpaket_nama         ="' . $detail2['ruanganpaket_nama'] . '"
                                    data-perdatarif_id             ="' . $detail2['perdatarif_id'] . '"
                                    data-perdanama_sk              ="' . $detail2['perdanama_sk'] . '"
                                    data-kelaspelayanan_id         ="' . $detail2['kelaspelayanan_id'] . '"
                                    data-kelaspelayanan_nama       ="' . $detail2['kelaspelayanan_nama'] . '"
                                    data-penjamin_id               ="' . $detail2['penjamin_id'] . '"
                                    data-penjamin_nama             ="' . $detail2['penjamin_nama'] . '"
                                    data-kelompoktindakan_id       ="' . $detail2['kelompoktindakan_id'] . '"
                                    data-kelompoktindakan_nama     ="' . $detail2['kelompoktindakan_nama'] . '"
                                    data-kategoritindakan_id       ="' . $detail2['kategoritindakan_id'] . '"
                                    data-kategoritindakan_nama     ="' . $detail2['kategoritindakan_nama'] . '"
                                    data-daftartindakan_id         ="' . $detail2['daftartindakan_id'] . '"
                                    data-daftartindakan_nama       ="' . $detail2['daftartindakan_nama'] . '"
                                    data-tipepaket_id              ="' . $detail2['tipepaket_id'] . '"
                                    data-tipepaket_nama            ="' . $detail2['tipepaket_nama'] . '"
                                    data-komponentarif_id          ="' . $detail2['komponentarif_id'] . '"
                                    data-komponentarif_nama        ="' . $detail2['komponentarif_nama'] . '"
                                    data-harga_tariftindakan       ="' . $detail2['harga_tariftindakan'] . '"
                                    data-persencyto_tindakan       ="' . $detail2['persencyto_tindakan'] . '"
                                    data-persendiskon_tindakan     ="' . $detail2['persendiskon_tindakan'] . '"
                                    data-is_default                ="' . $detail2['is_default'] . '"
                                    data-is_akomodasi              ="' . $detail2['is_akomodasi'] . '"
                                    data-carabayar_id              ="' . $detail2['carabayar_id'] . '"
                                    data-is_konsultasi             ="' . $detail2['is_konsultasi'] . '"
                                    data-kamarruangan_nokamar      ="' . $detail2['kamarruangan_nokamar'] . '"
                                    data-kamarruangan_id           ="' . $detail2['kamarruangan_id'] . '"
                                    data-ambulan_id                ="' . $detail2['ambulan_id'] . '"
                                    data-no_polisi                 ="' . $detail2['no_polisi'] . '"
                                    data-kelompokpemeriksaanlab_id ="' . $detail2['kelompokpemeriksaanlab_id'] . '"
                                    data-nama_kelompok             ="' . $detail2['nama_kelompok'] . '"
                                    data-jenispemeriksaanlab_id    ="' . $detail2['jenispemeriksaanlab_id'] . '"
                                    data-jenispemeriksaanlab_nama  ="' . $detail2['jenispemeriksaanlab_nama'] . '"
                                    data-pemeriksaanlab_id         ="' . $detail2['pemeriksaanlab_id'] . '"
                                    data-pemeriksaanlab_nama       ="' . $detail2['pemeriksaanlab_nama'] . '"
                                    data-persen_penyulit           ="' . $detail2['persen_penyulit'] . '"
                                    data-is_cyto = "0"
                                > '
                                . $detail2['daftartindakan_nama'] .
                                '</td></tr>';
                            }
                        }
                        ?>
                    </table>
                    <br>
                    </div>
                <?php
                    }
                }
                else{
                    echo '<p style="text-align:center">Tidak Ada Data.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-left">
    
</div>
<?php 

$this->registerJs($this->render('js/__modal_order.js'), View::POS_END, 'jsModal')

?>