<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\LaporanDropOutPasien;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $helper = new DocoHelpers;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $no = $request->get('start', 1);
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'laporan-drop-out-pasien/get-data',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            $dataResponse = ArrayHelper::getValue($response, 'data');
            $data = [];
            foreach ($dataResponse as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tanggal_lahir'] = date("d-M-Y", strtotime($value["tanggal_lahir"]));
                $value['tgl_rujukan'] = date("d-m-Y H:i:s", strtotime($value["tgl_rujukan"]));
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($response, '_meta.totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($response, '_meta.totalCount');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        }
    }
}
