<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Worklist;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\WorklistView;
use app\modules\v1\models\WorklistDetailView;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\Pegawai;

class GetDataWorklistAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request->get();
        $identifier = $request['identifier'];
        $status_bayar = null;
        $history = null;

        $header = WorklistView::find()
            ->where(['no_reseptur' => $identifier])
            ->orWhere(['no_resep' => $identifier])
            ->asArray()
            ->one();

        if(empty($header)) {
            return [
                'status' => 422,
                'message' => 'Worklist dengan nomor resep / reseptur ' . $identifier . ' tidak ditemukan.'
            ];
        }

        // casting
        $header['tanggal_lahir'] = date('d M Y', strtotime($header['tanggal_lahir']));
        $header['tanggal'] = is_null($header['tanggal']) ? "-" : date('d M Y H:i:s', strtotime($header['tanggal']));
        $header['no_rm'] = is_null($header['no_rm']) ? "-" : $header['no_rm'];
        $header['no_resep'] = is_null($header['no_resep']) ? "-" : $header['no_resep'];
        $header['no_reseptur'] = is_null($header['no_reseptur']) ? "-" : $header['no_reseptur'];
        $header['dokter'] = is_null($header['dokter']) ? "-" : $header['dokter'];
        $header['diagnosa_utama'] = is_null($header['diagnosa_utama']) ? "-" : $header['diagnosa_utama'];
        $header['no_pendaftaran'] = is_null($header['no_pendaftaran']) ? "-" : $header['no_pendaftaran'];
        $header['nama_pasien'] = is_null($header['nama_pasien']) ? "-" : $header['nama_pasien'];
        $header['tinggi_badan'] = is_null($header['tinggi_badan']) ? "-" : $header['tinggi_badan'];
        $header['berat_badan'] = is_null($header['berat_badan']) ? "-" : $header['berat_badan'];
        $header['umur'] = is_null($header['umur']) ? "-" : $header['umur'];
        $header['alergi'] = is_null($header['alergi']) || $header['alergi'] == "{NULL}" ? "-" : $header['alergi'];
        $header['reseptur_id'] = is_null($header['reseptur_id']) ? $header['penjualanresep_id'] : $header['reseptur_id'];
        $header['pegawai_penginput'] = is_null($header['pegawai_penginput']) ? "-" : $header['pegawai_penginput'];
        $detail = $this->controller->actionDetail($identifier);
        $get_history = !is_null($header['add_penjualaanresep']) ? json_decode($header['add_penjualaanresep'], true) : json_decode($header['add_reseptur'], true);

        $set_history = $this->controller->setWorklistFarmasiLog($get_history, $header);
        $history = $set_history['history'];
        $header['waktu_tunggu'] = is_null($set_history['waktu_tunggu']) ? '0:00:00' : $set_history['waktu_tunggu'];

        return [
            'status' => 200,
            'header' => $header,
            'detail' => $detail,
            'history' => $history
        ];
    }
}
