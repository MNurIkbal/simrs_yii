<?php

namespace Integrasi\Service\InaCbgs;

use Exception;
use Yii;

use Integrasi\Service\InaCbgs\Models\InfoPasienBpjsView;
use Integrasi\Service\InaCbgs\Models\InfoPasienBpjsKlaim;
use Integrasi\Components\Services\CaseMixService;
use Integrasi\Service\InaCbgs\Cache\Inacbgs;

class Registrasi extends \Integrasi\Contracts\DocoImplement
{

    public function execute()
    {
        $registId = $this->pendaftaran_id;
        $listMapp = Inacbgs::getMappInacbgs();
        $qRegist = InfoPasienBpjsView::find()
                        ->findByRegistId($registId)
                        ->asArray()
                        ->one();

        if (empty($qRegist)) throw new Exception("No pendaftaran tidak di temukan");

        $qDetail = InfoPasienBpjsKlaim::find()
                        ->findByRegistId($registId)
                        ->asArray()
                        ->all();

        $groupTarif = [];
        foreach ($qDetail as $item) {
            $groupName = $item['groupinacbg_nama'];
            $fieldCaseMix = isset($listMapp[$groupName]) ? $listMapp[$groupName] : null;
            if (empty($fieldCaseMix)) continue;

            if (!isset($groupTarif[$fieldCaseMix])) {
                $groupTarif[$fieldCaseMix] = 0;
            }

            $groupTarif[$fieldCaseMix] += $item['tarif_tindakan'];
        }

        $payload = [
            'no_pendaftaran' => $qRegist['no_pendaftaran'],
            'group_tarif' => $groupTarif
        ];

        $caseMix = new CaseMixService;
        $caseMix->xOwner = $this->owner;
        $response = $caseMix->sendDataRegis($payload);
        return json_encode([
            'service' => 'InaCbgs-Registrasi',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }
}