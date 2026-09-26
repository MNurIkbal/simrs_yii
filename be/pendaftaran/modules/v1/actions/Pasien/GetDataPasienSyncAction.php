<?php

namespace app\modules\v1\actions\Pasien;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Exception as DBException;
use app\modules\v1\models\ServiceCategory;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pasien;
use yii\helpers\ArrayHelper;

class GetDataPasienSyncAction extends BaseCurrentAction
{
    public function run()
    {
        $helpers = new DocoHelpers;
        $request = Yii::$app->request;
        $noRm = $request->get('no_rm');
        $tanggalLahir = $request->get('tgl_lahir');
        $betweenCreatedAt = $request->get('between_created_at');
        $startCreatedAt = $request->get('start_created_at');
        $endCreatedAt = $request->get('end_created_at');
        $isEmptyBetween = !$startCreatedAt || !$endCreatedAt;
        try {
            if ($isEmptyBetween) {
                return $helpers->response('start_created_at and end_created_at is required', 400);
            }
            $query = Pasien::find(true);
            $query->select([
                'pasien_id',
                'no_rekam_medik',
                'tgl_rekam_medik',
                'nama_pasien',
                'tanggal_lahir',
                'tempat_lahir',
                'alamat_pasien',
                'jeniskelamin',
                'no_mobile_pasien',
            ]);
            if ($noRm) $query->andWhere(['no_rekam_medik' => $noRm]);
            if ($tanggalLahir) $query->andWhere(['tanggal_lahir' => $tanggalLahir]);
            $startCreatedAt = $startCreatedAt . " 00:00:00";
            $endCreatedAt = $endCreatedAt . " 23:59:59";
            $query->andWhere(['between', 'created_date', $startCreatedAt, $endCreatedAt]);
            $resultQuery = $query->asArray()->all();
            return $resultQuery;
        } catch (DBException $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        }
    }
}
