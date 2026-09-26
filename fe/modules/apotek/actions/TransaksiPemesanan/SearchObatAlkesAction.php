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

class SearchObatAlkesAction extends Action {
    public function run() {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = Yii::$app->docoRest->apotek->get('allow/list-stok-apotek', [
                'query' => [
                    'ruangan_id' => $request->get('ruangan_id'),
                    'term' => $request->get('term')
                ]
            ]);
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $nama = empty($value['obatalkes_namalain']) ? $value['obatalkes_nama'] : $value['obatalkes_namalain'];
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_kode'] . ' - ' . $nama,
                    'stok' => $value['qty_tersedia'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $value['satuan'],
                    'harga_netto' => $value['harganetto']
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }
}