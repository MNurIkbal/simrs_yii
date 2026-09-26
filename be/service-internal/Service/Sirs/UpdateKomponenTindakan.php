<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Sirs\Models\TindakanPelayanan;

class UpdateKomponenTindakan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->update_data;

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $pelayananId = isset($value['pelayanan_id']) ? $value['pelayanan_id'] : null;
                $percentUpdate = isset($value['percentUpdate']) ? $value['percentUpdate'] : 0;
                $pendaftaran_id = isset($value['pendaftaran_id']) ? $value['pendaftaran_id'] : 0;
                $user_id = isset($value['user_id']) ? $value['user_id'] : 0;
                if (!empty($pelayananId)) {
                    $qModel = TindakanPelayanan::find()->select([
                        'additional_data'
                    ])->andWhere([
                        'tindakanpelayanan_id' => $pelayananId,
                        'pendaftaran_id' => $pendaftaran_id,
                    ])->asArray()->one();

                    if (!empty($qModel)) {
                        $additional_data = !empty($qModel['additional_data']) ? json_decode($qModel['additional_data'], true) : [];
                        if (isset($additional_data['list_komponen'])) {
                            $komponen = $additional_data['list_komponen'];
                            foreach ($komponen as $item) {
                                $tarif_kompsatuan = isset($item['tarif_kompsatuan']) ? $item['tarif_kompsatuan'] : 0;
                                $tarif_tindakankomp = isset($item['tarif_tindakankomp']) ? $item['tarif_tindakankomp'] : 0;
                                $tarifcyto_tindakankomp = isset($item['tarifcyto_tindakankomp']) ? $item['tarifcyto_tindakankomp'] : 0;
                                $tarifpenyulit_komponen = isset($item['tarifpenyulit_komponen']) ? $item['tarifpenyulit_komponen'] : 0;
                                $komponentarif_id = isset($item['komponentarif_id']) ? $item['komponentarif_id'] : 0;

                                $dataUpdate = [
                                    'tarif_kompsatuan' => $tarif_kompsatuan * $percentUpdate,
                                    'tarif_tindakankomp' => $tarif_tindakankomp * $percentUpdate,
                                    'tarifcyto_tindakankomp' => $tarifcyto_tindakankomp * $percentUpdate,
                                    'tarifpenyulit_komponen' => $tarifpenyulit_komponen * $percentUpdate,
                                    'last_modified_by' => $user_id,
                                ];

                                $this->updateTindakan($dataUpdate, [
                                    'tindakanpelayanan_id' => $pelayananId,
                                    'komponentarif_id' => $komponentarif_id
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return json_encode([
            'service' => 'Sirs-UpdateKomponenTindakan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function updateTindakan($attributes = [], $cond)
    {
        $default = array_merge([
            'modified_count' => new \yii\db\Expression('COALESCE(modified_count,0) + 1'),
            'last_modified_date' => date('Y-m-d H:i:s', time()),
        ], $attributes);
        Yii::$app->db->createCommand()
            ->update('tindakankomponen_t', $default, $cond)
            ->execute();
    }

}