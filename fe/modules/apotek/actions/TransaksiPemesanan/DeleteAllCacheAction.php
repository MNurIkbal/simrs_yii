<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiPemesanan;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class DeleteAllCacheAction extends Action {
    public function run() {
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        if(!empty($id_pegawai && $ruangan_id)){
            Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, array());
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus',
                'id_pegawai' => $id_pegawai,
                'ruangan_id' => $ruangan_id,
            ];
            return DocoHelpers::response($response);
        }
    }
}