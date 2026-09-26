<?php
namespace app\modules\v1\actions\InfKartuStok;

use Yii;
use yii\base\Action;
use Doco\Services\InternalService;

class ExportExcelBgProcessAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListDataInformasiKartuStokExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListInformasiKartuStokExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'ext' => '.xlsx',
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListInformasiKartuStokUpload' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filename' => 'informasi-kartu-stok',
                    'ext' => '.xlsx',
                ]
            ]
        ], true);
        return [
            'randString' => $randString
        ];
    }
}