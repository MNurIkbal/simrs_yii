<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper;
use Doco\apotek\components\filler\ResepDetailFiller;
use app\components\DocoConstants;

class ViewAction extends Action {
    // $id = resep_id (if reseptur_id is empty) / reseptur_id
    // $nomor = nomor resep / reseptur
    public function run($id, $nomor = null, $jenis = null) {
        $title = 'Detail Reseptur Pasien';
        $id = DocoHelpers::decrypt($id);

        $request = Yii::$app->request;
        $primes  = json_decode(DocoHelpers::decrypt($request->get('primes', '')), true);

        $_responDataResep = $this->controller->getInfoResepData($nomor, $jenis, $primes);

        $data_resep           = ArrayHelper::getValue($_responDataResep, 'response.data', []);
        $info_pasien          = ArrayHelper::getValue($_responDataResep, 'response.info_pasien', []);
        $konfig_farmasi       = ArrayHelper::getValue($_responDataResep, 'response.konfig_farmasi', false);
        $riwayat_personal     = ArrayHelper::getValue($_responDataResep, 'response.riwayat_personal', []);

        $data = $_responDataResep['response'];

        if(is_null($data_resep['reseptur_id'])) {
            $type = 'resep';
            $id = $data_resep['penjualanresep_id'];
        } else {
            $type = 'reseptur';
            $id = $data_resep['reseptur_id'];
        }

        //set disable button kronis
        $button_kronis = $this->isButtonKronisDisable($data_resep, $konfig_farmasi);

        // $tb = HandlingValueHelper::nullValue(ArrayHelper::getValue($info_pasien, 'tinggi_badan'));
        $tb =  HandlingValueHelper::compareValue([
                ArrayHelper::getValue($data_resep, 'tinggi_badan'),
                ArrayHelper::getValue($info_pasien, 'tinggi_badan'),
            ]);
        // $bb = HandlingValueHelper::nullValue(ArrayHelper::getValue($info_pasien, 'berat_badan'));
        $bb =  HandlingValueHelper::compareValue([
                ArrayHelper::getValue($data_resep, 'berat_badan'),
                ArrayHelper::getValue($info_pasien, 'berat_badan'),
            ]);
        $no_resep =  HandlingValueHelper::compareValue([
                'no_resep' => $data_resep['no_resep'],
                'no_reseptur' => $data_resep['no_reseptur']
            ]);
        // $no_pendaftaran = HandlingValueHelper::nullValue(ArrayHelper::getValue($data_resep, 'no_pendaftaran'));
        $no_pendaftaran =  (empty(ArrayHelper::getValue($data_resep, 'resep_kronis_asal_id')) && empty(ArrayHelper::getValue($data_resep, 'reseptur_asal_kronis_id'))) ? 
                    HandlingValueHelper::compareValue([
                            ArrayHelper::getValue($info_pasien, 'no_pendaftaran'),
                            ArrayHelper::getValue($data_resep, 'no_pendaftaran'),
                        ])
                    :
                    '-';

        $nomor = DocoHelpers::encrypt(HandlingValueHelper::nullValue($nomor));
        // $nama_pasien = ArrayHelper::getValue($info_pasien, 'nama_pasien');
        $nama_pasien =  HandlingValueHelper::compareValue([
                ArrayHelper::getValue($info_pasien, 'nama'),
                ArrayHelper::getValue($data_resep, 'nama_pembeli'),
            ]);
        $nama_pegawai = HandlingValueHelper::nullValue(ArrayHelper::getValue($data_resep, 'nama_pegawai'));

        //sanitize
        $nama_pasien = strip_tags(str_replace("&nbsp;", " ", htmlentities($nama_pasien)));
        $nama_pegawai = strip_tags(str_replace("&nbsp;", " ", htmlentities($nama_pegawai)));
        $pasienId = $data_resep['pasien_id'];

        $alamat = htmlentities(HandlingValueHelper::nullValue(ArrayHelper::getValue($info_pasien, 'alamat_pasien')));
        $instalasi_nama = HandlingValueHelper::compareValue([
                            ArrayHelper::getValue($data_resep, 'instalasi_reseptur'),
                            ArrayHelper::getValue($data_resep, 'instalasi_resep'),
                            ]);
        $ruangan_nama = HandlingValueHelper::compareValue([
                          ArrayHelper::getValue($data_resep, 'ruangan_reseptur'),
                          ArrayHelper::getValue($data_resep, 'ruangan_resep'),
                        ]);
        // $carabayar_nama = HandlingValueHelper::nullValue(ArrayHelper::getValue($data_resep, 'carabayar_nama'));
        $carabayar_nama = HandlingValueHelper::compareValue([
                   ArrayHelper::getValue($info_pasien, 'carabayar_nama'),
                   ArrayHelper::getValue($data_resep, 'carabayar_nama'),
                ]);
        $diagnosa = !empty(ArrayHelper::getValue($data_resep, 'diagnosa_id')) ? ArrayHelper::getValue($data_resep, 'diagnosa_nama') :
                    ArrayHelper::getValue($data_resep, 'diagnosa_text');
        // $penjamin_nama = HandlingValueHelper::nullValue(ArrayHelper::getValue($data_resep, 'penjamin_nama'));
        $penjamin_nama = HandlingValueHelper::compareValue([
                            ArrayHelper::getValue($data_resep, 'penjamin_nama'),
                            ArrayHelper::getValue($info_pasien, 'penjamin_nama'),
                        ]);
        $iter = !empty(ArrayHelper::getValue($data_resep, 'iter')) ? $data_resep['iter'] : 0;
        $catatan = HandlingValueHelper::nullValue(ArrayHelper::getValue($data_resep, 'catatan'));
        $no_rekam_medik = HandlingValueHelper::nullValue(ArrayHelper::getValue($info_pasien, 'no_rekam_medik'));
        $tgl_lahir = !empty(ArrayHelper::getValue($info_pasien, 'tanggal_lahir')) ?
                     date("d-m-Y", strtotime(ArrayHelper::getValue($info_pasien, 'tanggal_lahir'))) : '-';
        $totalhargajual = !empty(ArrayHelper::getValue($data_resep, 'totalhargajual')) ? DocoHelpers::formatNumber($data_resep['totalhargajual']) : 0;
        $biayaadministrasi = !empty(ArrayHelper::getValue($data_resep, 'biayaadministrasi')) ? DocoHelpers::formatNumber($data_resep['biayaadministrasi']) : 0;
        $totaltagihan = !empty($data_resep['totaltagihan']) ? DocoHelpers::formatNumber(ArrayHelper::getValue($data_resep, 'totaltagihan')) : 0;
        $data_racikan =  ArrayHelper::getValue($_responDataResep, 'response.data_racikan', []);
        $status_resep = $data_resep['status_reseptur_id'];
        $pasienadmisi_id = $data_resep['pasienadmisi_id'];
        // pcp-10
        $no_telepon_pasien = HandlingValueHelper::nullValue(ArrayHelper::getValue($info_pasien, 'no_telepon_pasien'));
        // $kelaspelayanan_nama = HandlingValueHelper::nullValue(ArrayHelper::getValue($info_pasien, 'kelaspelayanan_nama'));
        $kelaspelayanan_nama = HandlingValueHelper::compareValue([
                                ArrayHelper::getValue($info_pasien, 'kelaspelayanan_nama'),
                                ArrayHelper::getValue($data_resep, 'kelaspelayanan_nama'),
                            ]);
        
        $dpjp_nama = HandlingValueHelper::nullValue(ArrayHelper::getValue($info_pasien, 'dpjp_nama'));
        $kategori_resep_nama = ArrayHelper::getValue($data_resep, 'kategori_resep_nama');

        $akses = Yii::$app->session->get('akses_menu');
        $link = '/apotek/worklist';
        $link_farmasi = '/apotek/worklist-farmasi';
        $action_etiket = 'print-etiket';
        $action_etiket_new = 'print-etiket-new';

        $hasAccess1 = $hasAccess2 = [];
        if(isset($akses[$link])){
            $hasAccess1 = $akses[$link];
        }
        
        if(isset($akses[$link_farmasi])){
            $hasAccess2 =  $akses[$link_farmasi];
        }
        
        $etiket = false;
        $etiket_new = false;
        
        if(in_array($action_etiket, $hasAccess1)){
            $etiket = true;
        }

        if(in_array($action_etiket, $hasAccess2)){
            $etiket = true;
        }


        if(in_array($action_etiket_new, $hasAccess1)){
            $etiket_new = true;
        }
        
        if(in_array($action_etiket_new, $hasAccess1)){
            $etiket_new = true;
        }

        return $this->controller->render('detail', compact(
            'title', 'id', 'type', 'etiket_new', 'etiket', 'hasAccess1', 'hasAccess2', 'action_etiket_new', 'action_etiket', 'link_farmasi', 'link', 'akses', 'button_kronis',
            'tb', 'bb', 'no_resep', 'no_pendaftaran', 'nomor', 'nama_pasien', 'nama_pegawai', 'pasienId', 'alamat', 'instalasi_nama', 'ruangan_nama',
            'carabayar_nama', 'diagnosa', 'penjamin_nama', 'iter', 'catatan', 'no_rekam_medik', 'tgl_lahir', 'totalhargajual', 
            'biayaadministrasi', 'totaltagihan', 'data_racikan', 'status_resep', 'pasienadmisi_id', 'no_telepon_pasien', 'kelaspelayanan_nama',
            'dpjp_nama', 'riwayat_personal', 'kategori_resep_nama'
        ));
    }

    private function isButtonKronisDisable($data_resep, $konfig_farmasi = false) 
    { 
        $konfig_kronis = isset($konfig_farmasi['enable_split_kronis']) ? ($konfig_farmasi['enable_split_kronis'] == true) ? false : true : false;
        // $is_kronis = isset($detail['count']) ? ($detail['count'] == 0) ? true : false : false; //baca is kronis dari datatable
        $resep_kronis_asal_id = isset($data_resep['resep_kronis_asal_id']) ? true : false;
        $hasil_resep_kronis_id = isset($data_resep['hasil_resep_kronis_id']) ? true : false;
        $reseptur_kronis_asal_id = isset($data_resep['reseptur_kronis_asal_id']) ? true : false;
        $hasil_reseptur_kronis = isset($data_resep['hasil_reseptur_kronis']) ? true : false;
        $status_batal = $data_resep['status_reseptur_id'] == DocoConstants::STATUS_RESEPTUR_BATAL ? true : false;


        return ($konfig_kronis || $resep_kronis_asal_id || $hasil_resep_kronis_id || $reseptur_kronis_asal_id || $hasil_reseptur_kronis || $status_batal) == true ? true : false;;
    }
}
