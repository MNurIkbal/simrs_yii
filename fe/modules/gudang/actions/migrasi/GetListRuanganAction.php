<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\migrasi;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;

class GetListRuanganAction extends Action {
    public function run() {
        $result = [
            'output' => [
                ['id' => 1, 'text' => 'Ruangan A'],
                ['id' => 2, 'text' => 'Ruangan B'],
                ['id' => 3, 'text' => 'Ruangan C'],
                ['id' => 4, 'text' => 'Ruangan D'],
            ],
            'selected' => 1
        ];

        return json_encode($result);
    }
}