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

class GetDataPendaftaranMhkn extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow()
    {
        $model = new InfoDaftarPasienV;
        $query = $model::find();
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

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
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
                $between = true;
            }
            if(isset($_GET['advanced-filter']['status_konfirmasirm'])) {
                $query->andWhere(['status_konfirmasirm_id' => $_GET['advanced-filter']['status_konfirmasirm']]);
                unset($_GET['advanced-filter']['status_konfirmasirm']);
            }
            if(isset($_GET['advanced-filter']['jenis_reservasi_nama'])) {
                $query->andWhere(['jenis_reservasi_id' => $_GET['advanced-filter']['jenis_reservasi_nama']]);
                unset($_GET['advanced-filter']['jenis_reservasi_nama']);
            }
        }
        if (isset($instalasi_id)) {
            $query->andWhere(['not in','instalasi_id', $instalasi_id]);
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->orderBy(['tgl_pendaftaran'=>SORT_DESC]);
    	$query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}