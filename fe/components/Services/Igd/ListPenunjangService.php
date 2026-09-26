<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\components\Services\Igd;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class ListPenunjangService extends BaseCurrentService
{
    public function execute()
    {
        try {
            $infoPasien = null;
            $listRad    = [];
            $pendaftaranId = Yii::$app->request->get('id', null);
            $pasienAdmisiId = Yii::$app->request->get('pasienadmisi_id', null);
            $noRm = Yii::$app->request->get('norm', null);
            $response   = Yii::$app->docoRest->igd->get('riwayat-pasien/list-penunjang-radiologi', [
                'query' => [
                    'pendaftaran_id'  => DocoHelpers::decrypt($pendaftaranId),
                    'pasienadmisi_id' => DocoHelpers::decrypt($pasienAdmisiId),
                    'no_rekam_medik'  => DocoHelpers::decrypt($noRm),
                    'type'            => Yii::$app->request->get('type', 'hasil'),
                    'instalasi_id'    => Yii::$app->request->get('instalasi_id', null)
                ],
                'form_params' => []
            ]);
            $body           = json_decode($response->getBody(), true);
            $data_radiologi = $body['response']['data_radiologi'];
            $infoPasien     = $body['response']['data_pasien'];
            $arr_map_rad    = [];
            foreach ($data_radiologi as $val_data_rad) {
                $arr_map_rad[$val_data_rad['no_rujukan']][] = $val_data_rad;
            }
            $res_map_rad = [];
            foreach ($arr_map_rad as $key => $value) {
                $strIds = '';
                foreach ($value as $k => $v) {
                    $strIds .= DocoHelpers::encrypt($v['hasilpemeriksaanrad_id']) . '-' . DocoHelpers::encrypt($v['pasienmasukpenunjang_id']) . '_';
                }
                $arr_v = [];
                foreach ($value as $x => $z) {
                    $z['penunjang_pemeriksaanrad'] = $strIds;
                    $arr_v[] = $z;
                }
                $res_map_rad[$key] = $arr_v;
            }
            $listRad = $res_map_rad;
        } catch (RequestException $e) {
            $infoPasien = null;
            $this->logError($e);
            $listRad = [];
        } catch (\Exception $e) {
            $infoPasien = null;
            $this->logError($e);
            $listRad = [];
        }
        return [
            'infoPasien'     => $infoPasien,
            'listRad'        => $listRad,
            'pendaftaran_id' => $pendaftaranId,
            'noRm'           => $noRm
        ];
    }
}
