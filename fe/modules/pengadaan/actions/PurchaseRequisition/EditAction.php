<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\web\Response;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\PurchaseRequisitionForm;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;

class EditAction extends Action {
    public function run($id) {
        $title = 'Edit Purchase Request';
        $rid = Yii::$app->docoVars->workspace('ruangan_id');
        try{
            $request = Yii::$app->docoRest->pengadaan
                        ->get('purchase-requisition/edit-pr',[
                            'query'=>[
                                'id' => DocoHelpers::decrypt($id),
                                'type' => 'obat',
                                'rid' => $rid
                            ]
                        ]);
            $response = json_decode($request->getBody(), true);
            $header = $response['response']['data']['header'];
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            if($header['ruangan_id'] != $ruangan_id){
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
            }

            $detail = $response['response']['data']['detail'];
            $type = 'obat';
            foreach ($detail as $key => $value) {
                $value['stok_saatini'] = DocoHelpers::formatNumber($value['stok_saatini'], true, false, 3) . " " .$value['satuan_stok'];
                $value['stok_farmasi'] = $type == DocoConstants::JENIS_OBAT ? $this->castingStok($value, $value['qty_farmasi'], $type) : '-';
                $value['stok_gudang'] = $this->castingStok($value, $value['qty_gudang'], $type);
                $value['stok_ruanganlain'] = $this->castingStok($value, $value['qty_lain'], $type);
                $value['stok_sugesstion'] = $this->castingStok($value, $value['qty_sugesstion'], $type);
                // untuk pasing data saja
                $value['ext_st_farmasi'] = $value['qty_farmasi'];
                $value['ext_st_gudang'] = $value['qty_gudang'];
                $value['ext_st_lain'] = $value['qty_lain'];
                $value['ext_qty_sugesstion'] = $value['qty_sugesstion'];
                $detail[$key] = $value;
            }
            $konversi = $response['response']['data']['konversi'];
            $satuan = $response['response']['data']['satuan'];
            $hasil_konversi = $response['response']['data']['hasil_konversi'];

            $is_large_unit_pr = $this->controller->getKonfigFarmasi();
        }catch(RequestException $e){
            throw $e;
        }

        return $this->controller->render('edit', get_defined_vars());
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
