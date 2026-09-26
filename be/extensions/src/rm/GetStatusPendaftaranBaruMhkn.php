<?php

/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\rm;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoDaftarPasienV;
use app\modules\v1\models\Instalasi;

class GetStatusPendaftaranBaruMhkn extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow()
    {
        $instalasi_id = [];
        $instalasi = new Instalasi;
        $instalasi = $instalasi->find()->select([
            'instalasi_id'
        ])->andWhere(['is_pelayanan'=>true]);
        $instalasi->andWhere(['is_penunjang' => true]);
        $data = $instalasi
                ->orderBy('instalasi_id')
                ->all();

        foreach ($data as $key => $value) {
            $instalasi_id[] = $value->instalasi_id;
        }

    	$model = new InfoDaftarPasienV;
        $query = $model::find();
        $query->orderBy(['tgl_pendaftaran'=>SORT_DESC]);

		if (isset($instalasi_id)) {
            $query->andWhere(['not in','instalasi_id', $instalasi_id]);
        }
		
        $query->limit(1);
        $pendaftaran = $query->one();

        $cache = Yii::$app->cache;
        $dataCache = $cache->get('last-pendaftaran');
		if ($dataCache === false || !isset($dataCache['pendaftaran_id'])) {
			$dataCache = ['pendaftaran_id'=>0];
		}

		if($pendaftaran['pendaftaran_id'] != $dataCache['pendaftaran_id']){
			$is_new_registration = TRUE;
		    $cache->set('last-pendaftaran', $pendaftaran);
		}else{
			$is_new_registration = FALSE;
		}
    	return [
    		'data' => $is_new_registration
    	];
    }
}