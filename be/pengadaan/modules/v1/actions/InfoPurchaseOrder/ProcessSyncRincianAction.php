<?php

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use Doco\Services\InternalService;
use yii\helpers\ArrayHelper;

class ProcessSyncRincianAction extends Action {
    protected $dataRincian = 'DataRincian';
    protected $cetakRincian = 'CetakRincian';
    protected $uploadRincian = 'UploadRincian';

    public function run()
    {
        $request = Yii::$app->request;
        $params = $request->get('params', []);
        $no_transaksi = ArrayHelper::getValue($params, 'no_transaksi', []);
        $type_po = ArrayHelper::getValue($params, 'type_po', []);
        $is_kop = ArrayHelper::getValue($params, 'is_kop', false);
        $randString = $request->get('randString');
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        if ($is_kop) {
            $this->dataRincian = 'DataRincianKop';
            $this->cetakRincian = 'CetakRincianKop';
            $this->uploadRincian = 'UploadRincianKop';        
        }
        
        $fetchLimit = 20;
        $countData = count($no_transaksi);
        $totalPerPage = ceil($countData / $fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [
                $this->dataRincian => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'no_transaksi' => $no_transaksi,
                    'type_po' => $type_po
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                $this->cetakRincian => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                $this->uploadRincian => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);
        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
            'is_kop' => $is_kop
        ];
    }
}
