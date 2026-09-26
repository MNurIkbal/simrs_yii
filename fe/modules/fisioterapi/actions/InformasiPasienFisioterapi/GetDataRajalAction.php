<?php

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataRajalAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter_status = ArrayHelper::getValue($filter, 'advanced-filter.status_periksa_id', null);
        if (is_null($filter_status)) {
            $filter['advanced-filter']['status_periksa_id'] = 1;
        } elseif ($filter_status == 'Semua') {
            unset($filter['advanced-filter']['status_periksa_id']);
        }
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/index', ['query' => $filter]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                $value['id'] = $primaryKey;
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['pendaftaran_id_enc'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['programterapi_id_enc'] = DocoHelpers::encrypt($value['programterapi_id']);
                $value['programterapi_id'] = DocoHelpers::encrypt($value['programterapi_id']);
                $value['pendaftaran_id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['tgl_pendaftaran'] = date("d-m-Y H:i:s", strtotime($value["tgl_pendaftaran"]));
                $value['jeniskelamin'] = DocoHelpers::genderCode($value['jeniskelamin_id']);
                $value['tgl_lahir'] = date("d-M-Y", strtotime($value["tanggal_lahir"]));
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
