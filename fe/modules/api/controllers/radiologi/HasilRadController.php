<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\api\controllers\radiologi;

use Yii;
use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\Services\Radiologi\CetakHasilService;

class HasilRadController extends DocoController
{
    protected $allowAction = ['*'];

    public function actionIndex()
    {
        $path = Yii::getAlias("@download") . "/Cetak_Pemeriksaan.pdf";
        $response = (new CetakHasilService)->execute($path);
        $resultService = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path, $resultService);
    }
}