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

class SaveAction extends Action {
    /**
     * @todo save pemesanan obat alkes
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function run() {
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $request = Yii::$app->request;
        $post = $request->post();
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
        $response['response'] = [
            'text' => 'Obat alkes harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;

        if ($cacheObatAlkes) {
            try {
                $postData = [
                    'tanggal_kirim' => $post['tanggal_kirim'],
                    'keterangan_pesan' => $post['keterangan'],
                    'ruangan_id_pemesanan' => Yii::$app->docoVars->workspace('ruangan_id'),
                    'list_obat' => $cacheObatAlkes
                ];

                $result = Yii::$app->docoRest->apotek->request('POST', 'transaksi-pemesanan/save-obat-alkes', [
                    'form_params' => $postData
                ]);

                $result = json_decode($result->getBody(), true);
                $response['response'] = $result;
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'Obat alkes berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'id' => isset($result['response']['id']) ? $result['response']['id'] : '',
                    'nomor' => isset($result['response']['nomor']) ? $result['response']['nomor'] : '',
                ];
                Yii::$app->cache->delete('pemesanan-obat-' . $id_pegawai. $ruangan_id);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistemss';
            }
        }
        return DocoHelpers::response($response, $codeHttp);
    }
}