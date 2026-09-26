<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiMutasi;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\services\InfMutasiObatalkesService;
use app\components\DocoDatatableHelper;

class DetailPenerimaanPesananAction extends Action {
    protected $_title = "";

    public function run($id,$pesanan=null)
    {
    	$id = DocoHelpers::decrypt($id);
        $filter = DocoDatatableHelper::convertToRestfulParams(Yii::$app->request->get());
        if(!empty($pesanan)){
            $pesanan = DocoHelpers::decrypt($pesanan);
        }

        try{
            $response = Yii::$app->docoRest->apotek->get('inf-mutasi-obatalkes/detail-mutasi-pesanan',[
                'query'=> array_merge($filter, [
                    'pesanobatalkes_id' => $id
                ]),
            ]);

            $response = json_decode($response->getBody(), true);
            if(!empty($pesanan)) {
                $body = $response['response']['data'];
            } else {
                $body = $response['response']['detail'];
            }

            $request = Yii::$app->request;
            $no =  $request->get('start',1);;
            $data = [];
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        
            foreach ($body as $key => $value) {
                $no++;
                $primaryKey = isset($value['mutasiobatdetail_id']) ? DocoHelpers::encrypt($value['mutasiobatdetail_id']) : DocoHelpers::encrypt($value['pesanobatalkes_id']);
                $primary = isset($value['mutasiobatdetail_id']) ? DocoHelpers::encrypt($value['mutasiobatdetail_id']) : DocoHelpers::encrypt($value['pesanobatalkes_id']);
                unset($value['mutasiobatdetail_id']);
                $value['rowNum'] = $no;
                
                if(empty($value['jumlah_input']) || is_null($value['jumlah_input']) || $value['jumlah_input'] == 0){
                    $jum_mutasi =0;
                    $value['konv'] = 0;
                }else{
                    $konv = isset($value['mutasiobatdetail_id']) ? ($value['jumlah_pesan'] / $value['jumlah_input']) : 1;
                    $jum_mutasi = isset($value['jumlah_mutasi']) ? $value['jumlah_mutasi'] / $konv : 0;
                    $value['konv'] = $konv;
                }
                $value['jumlah_mutasi'] = isset($value['jumlah_mutasi']) ? DocoHelpers::formatNumber($jum_mutasi) : "-";
                $value['jumlah_input'] = isset($value['jumlah_input']) ? DocoHelpers::formatNumber($value['jumlah_input']) : DocoHelpers::formatNumber($value['qty_besar']);
                $value['satuanbesar_nama'] = isset($value['satuanbesar_nama']) ? $value['satuanbesar_nama'] : $value['satuan_besar'];
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] =  ArrayHelper::getValue($response, 'response._meta.totalCount', 0);
            $result['recordsFiltered'] =  ArrayHelper::getValue($response, 'response._meta.totalCount', 0);
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
            return $result;
        }
    }
}