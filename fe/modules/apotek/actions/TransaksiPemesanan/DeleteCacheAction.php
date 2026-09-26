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

class DeleteCacheAction extends Action {
    public function run($id = null) {
        $id = DocoHelpers::decrypt($id);
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
        if ($cacheObatAlkes !== false) {
            if (isset($cacheObatAlkes[$id])) {
                unset($cacheObatAlkes[$id]);
                Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, $cacheObatAlkes);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }
}