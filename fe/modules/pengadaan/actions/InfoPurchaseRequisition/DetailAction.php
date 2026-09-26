<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @Last Modified by:   Muhamad Lukman Hakim
 * @Last Modified time: 2021-02-25 17:30:00
 */

namespace Doco\pengadaan\actions\InfoPurchaseRequisition;

use Yii;
use yii\base\Action;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class DetailAction extends Action {
    public function run($type, $id) {
        $title = 'Detail Purchase Request';
        $module = $this->controller->_module;

        try{
            $request = Yii::$app->docoRest->pengadaan
                        ->get('allow/detail-pr',[
                            'query' => [
                                'id' => DocoHelpers::decrypt($id),
                                'type' => $type 
                            ]
                        ]);
            $response = json_decode($request->getBody(), true);

            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $showBtnCancel = true;
            $showBtnGenerate = false;
            $showBtnApprove = false;
            $header = $response['response']['data']['header'];
            $detail = $response['response']['data']['detail'];

            if($ruangan_id == DocoConstants::RUANGAN_PENGADAAN ||
                $header['status'] == DocoConstants::VAR_SUDAH_PO ||
                $header['status'] == DocoConstants::VAR_CANCEL_PR){
                $showBtnCancel = false;
            }
            
            if($header['created_date'] != null) {
                $header['created_date'] = date(
                    'd-m-Y H:i:s', 
                    strtotime($header['created_date'])
                );
            }

            if ($header['tgl_approve'] != null) {
                $header['tgl_approve'] = date(
                    'd-m-Y H:i:s',
                    strtotime($header['tgl_approve'])
                );
            }

            if($ruangan_id == DocoConstants::RUANGAN_PENGADAAN) {
                if($header['status'] == DocoConstants::VAR_SUDAH_PO || $header['status'] == DocoConstants::VAR_CANCEL_PR) {
                    $showBtnGenerate = false;
                } else {
                    $showBtnGenerate = true;
                }
            }

            if($header['status'] == DocoConstants::VAR_BELUM_APPROVED) {
                $showBtnApprove = true;
            }

            $detail = $this->castingNumber($detail);
        }catch(RequestException $e){
            throw $e;
        }

        return $this->controller->render('detail', get_defined_vars());
    }

    private function castingNumber($detail)
    {
        $arr_key = [
            'last_7', 
            'last_14',
            'last_30',
            'ssmin',
            'stok_gudang',
            'stok_farmasi',
            'stok_ruanganlain',
            'qty_outstanding',
            'qty_sugesstion'
        ];

        foreach($detail as $key => $value) {
            foreach($value as $itemKey => $itemValue) {
                if($itemValue == null) {
                    $detail[$key][$itemKey] = '-';
                    continue;
                }

                if(in_array($itemKey, $arr_key) && $itemValue != null) {
                    if($value['nilai_konversi'] > 1) {
                        $itemValue = $itemValue / $value['nilai_konversi'];
                        $detail[$key][$itemKey] = DocoHelpers::formatNumber($itemValue) . ' ' . $value['satuan'];
                    } else {
                        $detail[$key][$itemKey] = DocoHelpers::formatNumber($itemValue) . ' ' . $value['satuan_stok'];
                    }
                }

                if($itemKey == 'qty_pr') {
                    $detail[$key][$itemKey] = DocoHelpers::formatNumber($itemValue) . ' ' . $value['satuan'];
                }

                if($itemKey == 'doi') {
                    $detail[$key][$itemKey] = DocoHelpers::formatNumber($itemValue);
                }
            }
        }

        return $detail;
    }
}
