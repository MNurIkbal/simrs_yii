<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\modules\v1\models\InvalidBills_fn;
use app\modules\v1\models\Pendaftaran;
use Doco\components\BatchUpdate;
use Doco\components\DocoConstants;
use Exception;
use yii\helpers\ArrayHelper;

class CronValidateTagihanPasienController extends Controller
{
    public function actionIndex($startDate = null, $endDate = null)
    {
        Yii::info('starting cron-validate-tagihan-pasien!');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $startDate = empty($startDate) ? date('Y-m-d', strtotime('-1 month')) : date('Y-m-d', strtotime($startDate));
            $endDate = empty($endDate) ? date('Y-m-d', strtotime('-1 day')) : date('Y-m-d', strtotime($endDate));
            
            $model = (new InvalidBills_fn([
                'extParam' => [
                    $startDate, 
                    $endDate
                ]
            ]));
            $bills = $model::find()->all();
            if ($bills) {
                $pendaftaran_ids = ArrayHelper::getColumn($bills, 'pendaftaran_id');
                $updatedCount = (new BatchUpdate(Pendaftaran::tableName(), function($query) use ($pendaftaran_ids) {
                    $pendaftarans = Pendaftaran::find()
                        ->where(['IN', 'pendaftaran_id', $pendaftaran_ids])
                        ->andWhere(['status_bayar' => DocoConstants::BELUM_LUNAS])
                        ->all();
                    $pendaftarans = ArrayHelper::index($pendaftarans, 'pendaftaran_id');
                    $ids = [];
                    foreach($pendaftarans as $id => $value) {
                        $condition = 'pendaftaran_id = ' . $id . ' AND status_bayar = ' . DocoConstants::BELUM_LUNAS;
                        // $condition = "pendaftaran_id = {$id}";
                        $query->set([
                            'status_bayar' => DocoConstants::LUNAS
                        ], $condition, $id);
                        array_push($ids, $id);
                    }
                    $str_ids = implode(", ", $ids);
                    $query->where("pendaftaran_id IN({$str_ids})");
                }))->execute();
                if ($updatedCount) {
                    $transaction->commit();
                } else {
                    throw new Exception('cron-validate-tagihan-pasien error: no data updated!');
                }
            }
            Yii::info('end cron-validate-tagihan-pasien!');
        } catch(\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);
        } catch(\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);
        }
    }
}