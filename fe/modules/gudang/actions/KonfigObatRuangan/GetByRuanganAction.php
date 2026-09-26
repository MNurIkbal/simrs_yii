<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\actions\KonfigObatRuangan;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetByRuanganAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(!isset($filter['advanced-filter']['ruangan_id'])) {
            $filter['advanced-filter']['ruangan_id'] = $ruangan_id;
        }

        $data = $result = [];
        $result['data'] = $data;
        $result['draw'] = $request->get('draw', 1);
        $result['recordsTotal'] = 0;

        try {
            $response = Yii::$app->docoRest->gudang->get('konfig-obat-ruangan/get-by-ruangan', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $url = Url::home().(Yii::$app->controller->module->id).'/konfig-obat-ruangan/mapping?id='.$value['konfigrak_id'].'&ruangan_id='.$filter['advanced-filter']['ruangan_id'];
                $value['aksi'] = "<a type='button' action='".$url."' data-target='#modal_backdrop' data-options='link' data-toggle='modal' data-width='50%'><i class='fa fa-pencil'></i></a>";
                $value['min_stok'] = isset($value['min_stok']) ? $value['min_stok'] : '-';
                $value['max_stok'] = isset($value['max_stok']) ? $value['max_stok'] : '-';
                $value['nama_laci'] = isset($value['nama_laci']) ? $value['nama_laci'] : '-';
                $value['nama_rak'] = isset($value['nama_rak']) ? $value['nama_rak'] : '-';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['message'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }
}
