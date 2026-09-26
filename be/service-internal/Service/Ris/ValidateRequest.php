<?php

namespace Integrasi\Service\Ris;

use Yii;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Ris\Models\HasilPemeriksaanRad;
use yii\db\Query;

class ValidateRequest extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $tindakanId = (int) $this->tindakanpelayanan_id;
        $connection = Yii::$app->db;
        if (!empty($tindakanId)) {
            $qHasil = $connection->createCommand("
                SELECT 
                    hasilpemeriksaanrad_id
                FROM hasilpemeriksaanrad_t
                WHERE tindakanpelayanan_id = {$tindakanId}
                ORDER BY hasilpemeriksaanrad_id DESC
            ")->queryAll();

            if (!empty($qHasil)) {
                $listHasilDel = [];
                foreach ($qHasil as $key => $value) {
                    if (!empty($key)) {
                        $listHasilDel[] = $value['hasilpemeriksaanrad_id'];
                    }
                }
            }
            HasilPemeriksaanRad::updateAll([
                'is_deleted' => true
            ], [
                'hasilpemeriksaanrad_id' => $listHasilDel
            ]);
        }
        return json_encode([
            'service' => 'Ris-ValidateRequest',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

}