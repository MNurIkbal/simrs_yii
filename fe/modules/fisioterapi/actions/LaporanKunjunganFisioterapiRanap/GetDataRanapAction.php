<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap;

use app\components\DocoConstants;
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
        $helper = new DocoHelpers;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $no = $request->get('start', 1);
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'laporan-kunjungan-fisioterapi-ranap/get-data-ranap',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            $dataResponse = ArrayHelper::getValue($response, 'data');
            $data = [];
            foreach ($dataResponse as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['pendaftaran_id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['tgl_permintaan'] = date("d-m-Y H:i:s", strtotime($value["tgl_permintaan"]));
                $value['tanggal_lahir'] = date("d-M-Y", strtotime($value["tanggal_lahir"]));
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($response, '_meta.totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($response, '_meta.totalCount');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
