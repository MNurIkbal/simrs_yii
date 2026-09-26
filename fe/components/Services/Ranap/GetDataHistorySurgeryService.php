<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\components\Services\Ranap;

use Yii;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataHistorySurgeryService extends BaseCurrentService
{
    public function execute()
    {
        try {
            $request = Yii::$app->request;
            $norm    = DocoHelpers::decrypt($request->get('norm'));
            $draw    = $request->get('draw', 1);
            $result  =  [];
            $result['data']            = [];
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restRanap->request('get', 'riwayat-pasien/data-history-surgery', [
                'query' => [
                    'norm'   => $norm,
                    'filter' => $filter,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $no   = $request->get('start', 1);
            $row  = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum']             = $no;
                $value['tanggal_permintaan'] = isset($value['tanggal_permintaan']) ? date('d-m-Y', strtotime($value['tanggal_permintaan'])) : '-';
                $value['tanggal_disetujui']  = isset($value['tanggal_disetujui']) ? date('d-m-Y H:i:s', strtotime($value['tanggal_disetujui'])) : '-';
                $value['disetujui_oleh']     = isset($value['disetujui_oleh']) ? $value['disetujui_oleh'] : '-';
                $row[$key]                   = $value;
            }
            $result['data'] = $row;
            $result['recordsTotal'] = $body['response']['recordsTotal'];
            $result['recordsFiltered'] = $body['response']['recordsFiltered'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
