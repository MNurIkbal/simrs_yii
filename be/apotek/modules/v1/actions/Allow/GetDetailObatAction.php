<?php
namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\modules\v1\models\Penjamin;
use app\modules\v1\components\DetailObatQuery;
use app\modules\v1\models\KetersediaanObatView;

class GetDetailObatAction extends Action
{
	public function run(){
		$request = Yii::$app->request;
        try{
            $ruanganId = $request->get('ruangan_id',null);
            $obatAlkesId = $request->get('obatalkes_id',null);
            $penjaminId = $request->get('penjamin_id',null);
            $kelasPelayananId = $request->get('kelaspelayanan_id',null);
            if(is_null($ruanganId) || is_null($obatAlkesId) || is_null($penjaminId) || is_null($kelasPelayananId)){
                return [
                    'data' => [
                        'message' => 'Parameter harus terdiri atas ruangan_id, obatalkes_id, penjamin_id, kelaspelayanan_id',
                        'text' => 'Parameter harus terdiri atas ruangan_id, obatalkes_id, penjamin_id, kelaspelayanan_id',
                        'detail' => null
                    ]
                ];
            }

            if(empty($kelasPelayananId) || empty($penjaminId)) {
                $konfigFarmasi = $this->controller->konfigFarmasi()->one();
                $kelasPelayananId = empty($kelasPelayananId) ? $konfigFarmasi->defaultkelas_id : $kelasPelayananId;
                $penjaminId = empty($penjaminId) ? $konfigFarmasi->defaultkelas_id : $penjaminId;
            }

            $detail = DetailObatQuery::byPenjamin($penjaminId,$kelasPelayananId,$ruanganId,$obatAlkesId);
            $get_stok = KetersediaanObatView::find()->where(['obatalkes_id' => $obatAlkesId,'ruangan_id' => $ruanganId])->one();
            $detail['qty_tersedia'] = $get_stok['qty_stok'];
            return ['data'=>['message'=>'OK','text'=>'OK','detail'=>$detail]];
        }catch(\Exception $e){
            return ['data'=>[
                'message' => 'Terjadi Kesalahan',
                'text' => 'Terjadi Kesalahan',
                'detail'=>null
            ]];
        }
	}
}