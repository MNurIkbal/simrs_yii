<?php

namespace app\modules\v1\traits;

use app\modules\v1\models\PreAnesthetic;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\models\Ruangan;
use Exception;
use yii\helpers\ArrayHelper;

trait PreAnestheticTrait {

    public function actionGetPreAnesthetic($id = null)
    {
        $request = Yii::$app->request;
        try {
            $tanggal_operasi = PreAnesthetic::getDatePasienOperasi($id);
            $result  = PreAnesthetic::find()->where(['pasienmasukpenunjang_id' => $id])->one();
            $ruangan = $this->getPreAnestheticRuangan();
            $status  = $this->getStatusPreAnesthetic();
            return [
                'data' => $result,
                'addtional_data' => [
                    "ruangan"=> $ruangan,
                    "status_anesthetic" => $status,
                    "tanggal_operasi" => $tanggal_operasi
                ],
            ];
        }catch(\Exception $e) {
            return [];
        }catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function getPreAnestheticRuangan()
    {
        try {
            $ruangan_anestesi = DocoConstansId::actionGetAdditional('ruangan_anestesi', true);

            $result = Ruangan::find()->where(['in', 'ruangan_id', $ruangan_anestesi])->select(['ruangan_nama','ruangan_id'])->asArray()->all();
            
            return [
                ArrayHelper::map($result, 'ruangan_id', 'ruangan_nama'),
            ];
        }catch(\Exception $e) {
            return [
                'data' => [],
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSavePreAnesthetic()
    {
        $request = Yii::$app->request;
        $id = $request->post('pasienmasukpenunjang_id');
        $model = PreAnesthetic::find()->where(['pasienmasukpenunjang_id' => $id ])->one();
        
        try {
            if ($model == NULL ||empty($model)) {
                $newModel = new PreAnesthetic();
                $newModel->attributes = $request->post();
                $newModel->save();

            }else{
                $model->attributes = $request->post();
                $model->save();                
            }
            return ["message" => "Update data successfully"];

        }catch(Exception $e) {
            return $e->getMessage();
        }
    }

    private function getStatusPreAnesthetic()
    {
        $surgical = $this->getLookupDemand(DocoConstants::STRING_ST_PREANESTHETIC['surgical_status']);
        $statusAsa = $this->getLookupDemand(DocoConstants::STRING_ST_PREANESTHETIC['status_asa']);

        return [
            'surgical_status' => $surgical,
            'status_asa' => $statusAsa
        ];
    }

    private function getLookupDemand($lookup_kode)
    {
        $lookup = $this->getLookup();
        $list_loookup = $lookup->select(['lookup_id', 'lookup_name','lookup_type', 'lookup_value'])
            ->where([
              'lookup_type' => $lookup_kode,
            ])->asArray()->all();

        return $list_loookup;
    }
}
    