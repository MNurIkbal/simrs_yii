<?php
namespace app\modules\v1\businessLogic;

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * A product of PT. CRN
 * Powered by Sirs
 */


use Yii;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\ObatAlkes;
use yii\helpers\ArrayHelper;

class ValidasiStok
{
	public static function serahkan($data)
	{
		if(count($data)<=0 || !is_array($data) ){
			throw new \Exception("Data Serahkan Kosong", 1);
		}
		$oap_ids = ArrayHelper::getColumn($data, 'obatalkespasien_id');
		$consumption = StokObatAlkes::find(false)->where(['IN', 'obatalkespasien_id', $oap_ids])->asArray()->all();
		$consumption = ArrayHelper::index($consumption, null, 'obatalkespasien_id', []);
		
		$obat_ids = ArrayHelper::getColumn($data, 'obatalkes_id');
		$list_obat = ObatAlkes::find()->where(['IN', 'obatalkes_id', $obat_ids])->asArray()->all();
		$list_obat = ArrayHelper::index($list_obat, 'obatalkes_id', []);

		foreach ($data as $transaction) {
			if(isset($transaction['obatalkespasien_id']) && $transaction['qty_satuanpakai'] > 0){
				$oap_id = $transaction['obatalkespasien_id'];
				$savedConsumption = isset($consumption[$oap_id]) ? $consumption[$oap_id] : [];

				if(count($savedConsumption) <=0){
					$obat_id = $transaction['obatalkes_id'];
					$obat = isset($list_obat[$obat_id]) ? $list_obat[$obat_id] : [];
					$obat_nama = isset($obat['obatalkes_nama']) ? $obat['obatalkes_nama'] : null;
					if(!empty($obat_nama)){
						\Yii::$app->response->statusCode = 422;
						return [
							'status' => 422,
							'message' => 'Gagal potong stok obat',
							'text' => "Stok obat {$obat_nama} tidak mencukupi"
						];
					}else{
                    	\Yii::$app->response->statusCode = 422;
						return [
							'status' => 422,
							'message' => 'Gagal potong stok obat',
							'text' => "Pengurangan stok gagal"
						];
					}
				}
			}
		}

		return true;
	}
}