<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Expression;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\RujukanBantaran;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;

class GetCpptDataAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $rujukanbantaran_id = $request->get('rujukanbantaran_id');
        $pengajuan_id = $request->get('pengajuan_id', null);
        
        try {
            // Get pendaftaran_id from rujukan bantaran
            $conditions = ['rujukanbantaran_id' => $rujukanbantaran_id, 'is_deleted' => false];
            if(!empty($pengajuan_id)) {
                $conditions = ['pengajuan_id' => $pengajuan_id];
            }
            $rujukan = RujukanBantaran::find()
                ->select(['pendaftaran_id'])
                ->andWhere($conditions)
                ->asArray()
                ->one();
            
            if (!$rujukan || empty($rujukan['pendaftaran_id'])) {
                return [
                    'data' => [],
                    'message' => 'Data rujukan bantaran tidak ditemukan atau belum memiliki pendaftaran.'
                ];
            }
            
            // Get CPPT data
            $cpptData = Cppt::find()
                ->andWhere(['pendaftaran_id' => $rujukan['pendaftaran_id'], 'is_deleted' => false])
                ->orderBy(['tgl_cppt' => SORT_DESC, 'cppt_id' => SORT_DESC])
                ->asArray()
                ->all();
            
            return [
                'data' => $cpptData,
                'message' => 'Data berhasil diambil.'
            ];
            
        } catch (\yii\db\Exception $e) {
            Yii::error('GetCpptDataAction - Database Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan database: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Yii::error('GetCpptDataAction - Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}
