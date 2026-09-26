<?php

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataRanapAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter_status = ArrayHelper::getValue($filter, 'advanced-filter.status_periksa_id', null);
        if (is_null($filter_status)) {
            unset($filter['advanced-filter']['status_periksa_id']);
        } elseif ($filter_status == 'Semua') {
            unset($filter['advanced-filter']['status_periksa_id']);
        }
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/get-data-ranap', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no   = $request->get('start', 1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                $value['id'] = $primaryKey;
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['tgl_pendaftaran'] =  date("d-m-Y H:i:s", strtotime($value["tgl_pendaftaran"]));
                $value['jeniskelamin'] = DocoHelpers::genderCode($value['jeniskelamin_id']);
                $value['tgl_lahir'] = date("d-M-Y", strtotime($value["tanggal_lahir"]));
                $value['pendaftaran_id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['programterapi_id'] = DocoHelpers::encrypt($value['programterapi_id']);
                $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
                $kamar = ArrayHelper::getValue($value, 'kamar');
                $noTempatTidur = ArrayHelper::getValue($value, 'no_tempattidur');
                $value['noRuangannya'] = "<b>" . $ruanganNama . "</b>" . " <br> " . $kamar . " - " . $noTempatTidur;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
