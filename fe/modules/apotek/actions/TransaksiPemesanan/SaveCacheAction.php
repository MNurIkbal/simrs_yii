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
use Doco\apotek\models\TransaksiPemesananForm;

class SaveCacheAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new TransaksiPemesananForm();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $cacheKonv = Yii::$app->cache->get('konvert-satuan');
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $model->load($request->post());

        if ($model->validate()) {
            $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
            $satuan = $model->satuan;
            $qty = $model->qty;
            $satuanKecil = $request->post('satuankecil_id');
            $model->stok = $request->post('stok_asli');
            if ($cacheObatAlkes == false) {
                Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, []);
                $cacheObatAlkes = [];
            }

            if (!isset($cacheObatAlkes[$model->obat_alkes])) {
                $cacheObatAlkes[$model->obat_alkes] = [];
            }

            $permintaan = isset($cacheObatAlkes[$model->obat_alkes]['permintaan'])
                ? $cacheObatAlkes[$model->obat_alkes]['permintaan'] : 0;
            $totalPermintaan = $permintaan + $model->qty;
            if ($totalPermintaan > $model->stok) {
                header('Content-type: application/json');
                http_response_code(500);
                return DocoHelpers::response([
                    'response' => [
                        'text' => Yii::t('fe', 'Stok tidak mencukupi'),
                        'title' => 'Terjadi Kesalahan!']
                ], 422);
            }
            if (isset($cacheKonv[$model->obat_alkes][$satuan])) {
                $totalPermintaan = ($cacheKonv[$model->obat_alkes][$satuan] * $model->qty) + $permintaan;
            }
            $satuanBesarId = $request->post('satuan_pesan_id');
            $satuanKecilId = $request->post('satuankecil_id');
            $satuanBesar = isset($cacheKonv[$model->obat_alkes][$satuan])
                ? $cacheKonv[$model->obat_alkes][$satuan]
                : 0;
            $hasilSatuanBesar = $satuanBesar != 0 ? round($totalPermintaan / $satuanBesar, 2) : 0;
            $id_satuan_pesan = $request->post('satuan_pesan_id');
            $id_satuan_besar = $request->post('satuanbesar_id');
            $setCache = [
                'obatalkes_id' => $request->post('id'),
                'text' => $request->post('text'),
                'satuankecil_id' => $request->post('satuankecil_id'),
                'satuankecil_nama' => $request->post('satuankecil_nama'),
                'satuanbesar_id' => $request->post('satuan_pesan_id'),
                'satuanbesar_nama' => $request->post('satuan_pesan_nama'),
                'instalasi_id' => $model->instalasi_tujuan,
                'ruangan_id' => $model->ruangan_tujuan,
                'qty_pesan' => ($id_satuan_pesan == $id_satuan_besar) ? $hasilSatuanBesar : $totalPermintaan,
                'qty_besar' => $hasilSatuanBesar,
                'qty_kecil' => $totalPermintaan,
                'permintaan' => $totalPermintaan,
                'tanggal_kirim' => $model->tanggal_kirim
            ];
            $cacheObatAlkes[$model->obat_alkes] = $setCache;
            $cacheObatAlkes = Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, $cacheObatAlkes);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];

            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $formName);
        }
    }
}