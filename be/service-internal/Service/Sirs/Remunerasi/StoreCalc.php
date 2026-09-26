<?php

namespace Integrasi\Service\Sirs\Remunerasi;

use Yii;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPegawaiCalc;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPegawai;

class StoreCalc extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $response = $this->storeData();
        
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-Remunerasi-StoreCalc',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    private function storeData()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $dataRow = $cacheFiles->get($this->unique_str);

        foreach ($dataRow as $key => $value) {
            $remunPegawai = RemunPegawai::findOne($value['remunpegawai_id']);
            $remunPegawai->estimasi_remun = $value['imbal_jasa'];
            $remunPegawai->update();

            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-excel:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'updateValue',
                    'messageProcess' => '',
                    'progress' => ''
                ]),
            ]);
        }
        
        RemunPegawaiCalc::batchInsert($dataRow, false);
        
        return true;
    }
}
