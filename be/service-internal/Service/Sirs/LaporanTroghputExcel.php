<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanThruputFn;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanTroghputExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
		set_time_limit(0);

        $dom = date('t', strtotime(sprintf('%04d-%02d-01', $this->year, $this->month)));

        $start_date = $this->year .'-'. $this->month .'-01';
        $end_date = $this->year .'-'. $this->month .'-'. $dom;
        $data = LaporanThruputFn::getData($start_date, $end_date);

        $cacheFiles = Yii::$app->cacheFiles;

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);

        $cacheFiles->set($this->unique_str, $data);

        return json_encode([
            'service' => 'Sirs-LaporanTroghputExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }
}
