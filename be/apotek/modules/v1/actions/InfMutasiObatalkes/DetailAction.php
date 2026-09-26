<?php
namespace app\modules\v1\actions\InfMutasiObatalkes;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\entities\MutasiObat;
use app\modules\v1\entities\MutasiObatDetail;

class DetailAction extends Action
{
    public function run()
    {
    	$request = Yii::$app->request;
        if($request->get('mutasiobatruangan_id')){
            $modelHeader = MutasiObat::getInfoByIdMutasi($request->get('mutasiobatruangan_id'));
            $modelDetail = MutasiObatDetail::getListByIdMutasi($request->get('mutasiobatruangan_id'));
        }elseif($request->get('nomutasioa')){
            $modelHeader = MutasiObat::getInfoByNoMutasi($request->get('nomutasioa'));
            $modelDetail = MutasiObatDetail::getListByNoMutasi($request->get('nomutasioa'));
        }else{
            $modelHeader = [];
            $modelDetail = [];
        }
        return [
            'header' => $modelHeader,
            'detail' => $modelDetail,
        ];
    }
}