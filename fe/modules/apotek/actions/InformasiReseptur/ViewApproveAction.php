<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use app\components\helpers\HandlingValueHelper;
use Doco\apotek\components\filler\ResepDetailFiller;

class ViewApproveAction extends Action {
    // $id = resep_id (if reseptur_id is empty) / reseptur_id
    // $nomor = nomor resep / reseptur
    public function run($id, $nomor = null) {
        $title = 'Detail Reseptur Pasien';
        $reseptur_id = DocoHelpers::decrypt($id);
        $data_resep = [];

        $_requestDataResep = Yii::$app->docoRest->apotek->get('allow/info-resep',[
                                'query' => [
                                    'nomor' => $nomor, // nomor resep / reseptur
                                ]
                            ]);
        $_responDataResep = json_decode($_requestDataResep->getBody(),true);
        $data_resep = $_responDataResep['response']['data'];
        $data = $_responDataResep['response'];

        $tb = HandlingValueHelper::nullValue($data_resep['tinggi_badan']);
        $bb = HandlingValueHelper::nullValue($data_resep['berat_badan']);

        $no_resep =  HandlingValueHelper::compareValue([
                        'no_resep' => $data_resep['no_resep'],
                        'no_reseptur' => $data_resep['no_reseptur']
                        ]);
        $no_pendaftaran = HandlingValueHelper::nullValue($data_resep['no_pendaftaran']);
        $nomor = DocoHelpers::encrypt(HandlingValueHelper::nullValue($nomor));
        $nama_pasien = $data_resep['nama'];
        $pasienId = $data_resep['pasien_id'];
        $nama_pegawai = HandlingValueHelper::nullValue($data_resep['nama_pegawai']);

        //sanitize
        $nama_pasien = strip_tags(str_replace("&nbsp;", " ", htmlentities($nama_pasien)));
        $nama_pegawai = strip_tags(str_replace("&nbsp;", " ", htmlentities($nama_pegawai)));

        $alamat = htmlentities(HandlingValueHelper::nullValue($data_resep['alamat_pasien']));
        $instalasi_nama = HandlingValueHelper::compareValue([
                            'instalasi_resep' => $data_resep['instalasi_resep'],
                            'instalasi_reseptur' => $data_resep['instalasi_reseptur']
                            ]);
        $ruangan_nama = HandlingValueHelper::compareValue([
                        'ruangan_tujuan' => $data_resep['ruangan_tujuan'],
                        'ruangan_reseptur' => $data_resep['ruangan_reseptur']
                        ]);
        $carabayar_nama = HandlingValueHelper::nullValue($data_resep['carabayar_nama']);
        $diagnosa = !empty($data_resep['diagnosa_id']) ? $data_resep['diagnosa_nama'] :
                    $data_resep['diagnosa_text'];
        $alergi = !empty($data['data_alergi']) ? $data['data_alergi'] : null;
        $penjamin_nama = HandlingValueHelper::nullValue($data_resep['penjamin_nama']);
        $iter = !empty($data_resep['iter']) ? $data_resep['iter'] : 0;
        $catatan = HandlingValueHelper::nullValue($data_resep['catatan']);
        $no_rekam_medik = HandlingValueHelper::nullValue($data_resep['no_rekam_medik']);
        $tgl_lahir = !empty($data_resep['tanggal_lahir']) ?
                     date("d-m-Y", strtotime($data_resep['tanggal_lahir'])) : '-';
        $totalhargajual = !empty($data_resep['totalhargajual']) ? DocoHelpers::formatNumber($data_resep['totalhargajual']) : 0;
        $biayaadministrasi = !empty($data_resep['biayaadministrasi']) ? DocoHelpers::formatNumber($data_resep['biayaadministrasi']) : 0;
        $totaltagihan = !empty($data_resep['totaltagihan']) ? DocoHelpers::formatNumber($data_resep['totaltagihan']) : 0;
        $data_racikan = isset($data['data_racikan']) ? $data['data_racikan'] : [] ;
        $status_resep = $data_resep['status_reseptur_id'];
        $pendaftaranId = ArrayHelper::getValue($data_resep, 'pendaftaran_id');

        return $this->controller->render('detail-approve', get_defined_vars());
    }
}
