<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;

class ListDetailAction extends Action {
    public function run($type,$id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try{
            $response = Yii::$app->docoRest->pengadaan
                ->get('purchase-requisition/get-list-detail?type='.$type.'&id='.$id.'&'.http_build_query($yiiRestfulParams), [
                    ['form_params' => []]
                ]);
            $body = json_decode($response->getBody(), True);

            $no = Yii::$app->request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['qty_input'] = (int)$value['qty_input'];
                $status_batal_po = isset($value['status_penerimaan']) && $value['status_penerimaan'] == 'Dibatalkan' ? "(Batal)" : "";
                $value['nomor_po'] = $value['nomor_po']." ".$status_batal_po;
                $value['doi'] = $value['doi'] != null ? $value['doi'] : '-';
                $value['ssmin'] = $value['ssmin'] != null ? number_format(ArrayHelper::getValue($value,'ssmin','-'), 0, ",", ".") .' '. ArrayHelper::getValue($value,'satuan','-') : '-';
                $value['stok'] = number_format(ArrayHelper::getValue($value,'stok','-'), 0, ",", ".") .' '. ArrayHelper::getValue($value,'satuan_stok','-');
                //$value['qty_sugesstion'] != null ? number_format(ArrayHelper::getValue($value,'qty_sugesstion','-'), 0, ",", ".") .' '. ArrayHelper::getValue($value,'satuan','-') : '-';
                $value['qty_sugesstion'] = $this->castingStok($value, $value['qty_sugesstion'], $type);
                $value['qty_pr'] = number_format(round(ArrayHelper::getValue($value,'qty_pr','-')), 0, ",", ".") .' '. ArrayHelper::getValue($value,'satuan','-');
                $value['qty_input'] = number_format(round(ArrayHelper::getValue($value,'qty_input','-')), 0, ",", ".") .' '. ArrayHelper::getValue($value,'satuan','-');
                $value['stok_farmasi'] = $type == DocoConstants::JENIS_OBAT ? $this->castingStok($value, $value['stok_farmasi'], $type) : '-';
                $value['stok_gudang'] = $this->castingStok($value, $value['stok_gudang'], $type);
                $value['stok_ruanganlain'] = $this->castingStok($value, $value['stok_ruanganlain'], $type);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function castingStok($value, $attr, $type) {
        $attr = !empty($attr) ? $attr : (float) 0;
        if($value['nilai_konversi'] > 1) {
            $attr = $attr / $value['nilai_konversi'];
            $attr = DocoHelpers::formatNumber($attr) . ' ' . $value['satuan'];
        } else {
            $attr = DocoHelpers::formatNumber($attr) . ' ' . $value['satuan_stok'];
        }
        return $attr;
    }
}
