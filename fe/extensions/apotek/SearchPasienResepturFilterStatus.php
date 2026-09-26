<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\extensions\apotek;

use Yii;
use yii\web\Response;
use app\components\DocoConstants;

class SearchPasienResepturFilterStatus extends \app\components\DocoBaseProcessExtension
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
                $status_pasien = "";

                switch ($row_item['status_periksa']) {
                    case DocoConstants::STATUS_PERIKSA_BTL_KUNJUNGAN:
                        $is_disabled = true;
                        $status_pasien = " [BATAL KUNJUNGAN]";
                        break;

                    case DocoConstants::STATUS_PERIKSA_BTL_PERIKSA:
                        $is_disabled = true;
                        $status_pasien = " [BATAL PERIKSA]";
                        break;

                    case DocoConstants::STATUS_PERIKSA_PULANG:
                        $is_disabled = true;
                        $status_pasien = " [PULANG]";
                        break;

                    case DocoConstants::STATUS_RANAP_BATAL_RAWAT:
                        $is_disabled = true;
                        $status_pasien = " [BATAL RAWAT]";
                        break;

                    default:
                        $is_disabled = false;
                        $status_pasien = "";
                        break;
                }

                if ($row_item['is_freezebill'] == TRUE) {
                    $is_disabled = true;
                }

                if ($row_item['is_stopakomodasi'] == TRUE) {
                    $is_disabled = true;
                    $status_pasien = " [STOP AKOMODASI]";
                }

                if (
                    $row_item['is_rd'] == TRUE && 
                    !empty($row_item['pasienadmisi_id']) && 
                    empty($row_item['pasienpulangri_id'])
                ) {
                    $is_active = true;
                } else if (
                        empty($row_item['pasienpulang_id']) && 
                        empty($row_item['pasienpulangri_id'])
                ) {
                    $is_active = true;
                } else {
                    $is_active = false;
                }

                $ket_freezebill = ($row_item['is_freezebill'] == TRUE) ? '[F] ' : '';
                $item = [
                    "id" => $row_item['pendaftaran_id'],
                    "text" => $ket_freezebill . $row_item['no_pendaftaran'] . " / " . $row_item['no_rekam_medik'] . " / " . $row_item['nama_pasien'] . $status_pasien,
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
