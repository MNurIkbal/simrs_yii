<?php

namespace app\modules\v1\services;

use app\modules\v1\services\Contracts\DiagnosaInterface;
use Yii;
use app\modules\v1\models\DiagnosaView;
use Doco\components\DocoConstants;
use app\modules\v1\models\Pegawai;


class DiagnosaService implements DiagnosaInterface
{
    public $query;
    public $type;
    public $limit;
    public $offset;
    public $tabularlist_versi;

    public function __construct() 
    {
        $request = Yii::$app->request;
        $this->query = $request->get('q','-');
        $this->type = $request->get('type',DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK);
        $this->limit = $request->get('limit',10);
        $this->offset = $request->get('offset',0);
    }

    public function getDiagnosa()
    {
        try {
            $this->setTabularlistVersi();
            $this->kelompokPerawat();
            
            $model = DiagnosaView::find()->andWhere(['is_deleted' => false, 'is_active' => true]);

            if (isset($this->query) && $this->query != '') {
                $model->andWhere(['ilike', 'diagnosa_nama', $this->query]);
                $model->orWhere(['ilike', 'diagnosa_kode', $this->query]);
            }

            if (isset($this->tabularlist_versi) && $this->tabularlist_versi != '') {
                $model->andWhere(['tabularlist_versi' => $this->tabularlist_versi]);
            }

            return $model->offset($this->offset)->limit($this->limit)->all();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function setTabularlistVersi()
    {
        if ($this->type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK) {
            $this->tabularlist_versi = 'ICD X';
        } else if($this->type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA) {
            $this->tabularlist_versi = 'ICD X';
        } else if($this->type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
            $this->tabularlist_versi = 'ICD X';
        } else if($this->type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA) {
            $this->tabularlist_versi = 'ICD X';
        } else {
            $this->tabularlist_versi = 'ICD IX';
        }
    }

    public function kelompokPerawat()
    {
         // get kelompok pegawai id
        $employeeRecord = Pegawai::find(true)->select(['pegawai_id', 'kelompokpegawai_id'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->asArray()->one();
        if ($employeeRecord['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
            $this->tabularlist_versi = 'ICD_KEP';
        }
    }
}
