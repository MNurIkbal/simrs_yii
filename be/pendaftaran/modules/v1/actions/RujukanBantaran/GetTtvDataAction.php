<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use app\modules\v1\models\RujukanBantaran;

class GetTtvDataAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $pengajuan_id = $request->get('pengajuan_id');
        
        // Validation
        if (empty($pengajuan_id)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Pengajuan ID harus diisi.'];
        }
        
        try {
            // Get rujukan bantaran to retrieve pendaftaran_id
            $rujukan = RujukanBantaran::find()
                ->select(['rujukanbantaran_id', 'pendaftaran_id'])
                ->andWhere(['pengajuan_id' => $pengajuan_id, 'is_deleted' => false])
                ->asArray()
                ->one();
            
            if (!$rujukan) {
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Rujukan bantaran tidak ditemukan.'];
            }
            
            if (empty($rujukan['pendaftaran_id'])) {
                Yii::$app->response->statusCode = 422;
                return ['message' => 'Pendaftaran ID tidak ditemukan untuk rujukan ini.'];
            }
            
            $pendaftaran_id = $rujukan['pendaftaran_id'];
            
            // Get TTV data from rajal service
            $restRajal = Yii::$app->docoRest->rajal;
            $response = $restRajal->get(
                'monitoring-ttv/get-data', 
                ['query' => ['pendaftaran_id' => $pendaftaran_id, 'is_filter_date' => false]]
            );
            $body = json_decode($response->getBody(), true);
            
            // Return only the response data without pagination
            if (isset($body['response'])) {
                return $body['response'];
            }
            
            return $body;
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Yii::error('GetTtvDataAction - RequestException: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Gagal mengambil data Monitoring TTV: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Yii::error('GetTtvDataAction - Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}
