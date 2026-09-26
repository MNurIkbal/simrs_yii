<?php

/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-12-04 14:12:02
 * @Last Modified by:  
 * @Last Modified time: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\RiwayatAnamnesaView;

class AsesmenController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\AsesmenMedis';

    public function actionGetRiwayat($no_rekam_medik, $pendaftaran_id){
        try {
            $query = (new \yii\db\Query())
            ->select([
                RiwayatAnamnesaView::tableName().'.riwayat_penyakitterdahulu',
                RiwayatAnamnesaView::tableName().'.riwayat_penyakitkeluarga',
                RiwayatAnamnesaView::tableName().'.riwayat_imunisasi',
                RiwayatAnamnesaView::tableName().'.riwayat_makanan',
                RiwayatAnamnesaView::tableName().'.riwayat_kelahiran',
                RiwayatAnamnesaView::tableName().'.riwayat_alergiobat',
            ])
            ->from(RiwayatAnamnesaView::tableName())
            ->where([RiwayatAnamnesaView::tableName().'.no_rekam_medik' => $no_rekam_medik])
            ->andWhere(['!=', RiwayatAnamnesaView::tableName().'.pendaftaran_id', $pendaftaran_id])
            ->orderBy([RiwayatAnamnesaView::tableName().'.anamesa_id' => SORT_DESC])
            ->all();

            return $query;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}