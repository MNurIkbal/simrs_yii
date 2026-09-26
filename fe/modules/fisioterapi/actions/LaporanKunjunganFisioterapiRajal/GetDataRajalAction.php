<?php

namespace Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal;

use app\components\DocoConstants;
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
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('laporan-kunjungan-fisioterapi-rajal/get-data-rajal', ['query' => $filter]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['pendaftaran_id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['tgl_pendaftaran'] = date("d-m-Y H:i:s", strtotime($value["tgl_pendaftaran"]));
                $value['tanggal_lahir'] = date("d-M-Y", strtotime($value["tanggal_lahir"]));
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
