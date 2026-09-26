<?php

/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoDaftarPasienV;
use SirsCore\models\KonfigSystem;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class RmGetStatusPendaftaranBaruProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected function processFlow()
    {
        $request = Yii::$app->request;

        $dateRequest = $request->get('date_request');

        $validateDate = !empty($dateRequest) ? date('Y-m-d H:i:s', strtotime($dateRequest)) : date('Y-m-d H:i:s');

        $model = new InfoDaftarPasienV;
        $query = $model::find()->select(['pendaftaran_id','pasien_id','nosep','status_pasien']);
        $query->where(['not', ['pendaftaran_id' => null]]);
        // ini perubahan masih kasar dikarenakan ada permintaan dadakan di RS prima 03-07-2023
        $query->andWhere(['status_pasien'=>DocoConstants::VAR_PAS_L]);

        $query->andWhere(['>','tgl_pendaftaran', $validateDate]);

        
        // Perubahan Konsep Prima 04-07-2023 jika di akses lebih dari 1 PC
        // $cache = Yii::$app->cache;
        // $dataCache = $cache->get('last-pendaftaran');

        // if (gettype($dataCache) != "integer" || $dataCache === false) {
        //     $dataCache = 0;
        // }

        // if($dataCache == 0){
        //     $query->orderBy(['pendaftaran_id'=>SORT_DESC]);
        //     $query->limit(1);
        // } else {
        //     $query->andWhere(['>','pendaftaran_id',$dataCache]);
        //     $query->limit(10);
        // }
        $query->orderBy(['pendaftaran_id'=>SORT_ASC]);
        $pendaftaran = $query->asArray()->all();

        $pendaftaranIds = ArrayHelper::getColumn($pendaftaran,'pendaftaran_id');

        $is_new_registration = FALSE;
        if(!empty($pendaftaran)) {
            $is_new_registration = TRUE;
        }

        $konfigSystem = KonfigSystem::find()->where(['is_deleted' => false])->asArray()->one();

        return [
            'pendaftaran' => $pendaftaran,
            'is_new_registration' => $is_new_registration,
            'autoprint_port' => isset($konfigSystem['auto_print_port']) ? $konfigSystem['auto_print_port'] : null,
            'is_print_automatic' => isset($konfigSystem['is_print_automatic']) ? $konfigSystem['is_print_automatic'] : false,
            'date' => $validateDate
        ];
    }
}