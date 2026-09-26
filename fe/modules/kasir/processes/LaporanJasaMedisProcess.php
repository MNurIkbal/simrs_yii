<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;
use Yii;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanJasaMedisProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $path = 'index';
        $endPoint = "lap-rekap-jasa-dokter/index";
        return [
			'path' => $path,
			'endPoint' => $endPoint,
		];
    }
}