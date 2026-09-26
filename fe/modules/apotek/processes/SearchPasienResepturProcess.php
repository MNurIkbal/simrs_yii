<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\apotek\processes;

use Yii;
use yii\web\Response;
use app\components\DocoConstants;

class SearchPasienResepturProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $payload = $request->get();
        $term = $request->get('no_pendaftaran');
        try {
            $response = Yii::$app->docoRest->apotek->get('allow/search-data-pendaftaran?', [
                'form_params' => [],
                'query' => [
                    'term' => $term,
                    'page' => $payload['page']
                ],
            ]);
            $body = json_decode($response->getBody(), true);

            $data = $body['response']['data'];

            $list_no_duplicate = [];
            foreach ($data as $pendaftaran) {
                $list_no_duplicate[$pendaftaran['pendaftaran_id']] = $pendaftaran;
            }
            $data = $list_no_duplicate;


            $list = [];
            foreach ($data as $row_item) {
                $is_disabled = false;

                if ($row_item['is_freezebill'] == TRUE) {
                    $is_disabled = true;
                }

                if ($row_item['is_rd'] == TRUE && !empty($row_item['pasienadmisi_id']) && empty($row_item['pasienpulangri_id'])) {
                    $is_active = true;
                } else if ((empty($row_item['pasienpulang_id']) && empty($row_item['pasienpulangri_id']))) {
                    $is_active = true;
                } else {
                    $is_active = false;
                }

                $ket_freezebill = ($row_item['is_freezebill'] == TRUE) ? '[F] ' : '';
                $item = [
                    "id" => $row_item['pendaftaran_id'],
                    "text" => $ket_freezebill . $row_item['no_pendaftaran'] . " / " . $row_item['no_rekam_medik'] . " / " . $row_item['nama_pasien'],
                    "data" => $row_item,
                    "disabled" => $is_disabled,
                    "is_pasien_aktif" => $is_active
                ];
                $list[] = $item;
            }

            return [
                'list' => $list,
                'data' => $data,
                'more' => isset($body['response']['data']) ? count($body['response']['data']) >= 10 : false,
                'payload' => $request->get(),
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
