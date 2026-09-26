<?php

namespace Doco\Repositories;

use Yii;

class RegionRepositories
{
    /**
     * Retrieve data region by type
     *
     * @param String $type type of request region: province,city,district,village
     * @param Integer $foreignId foreign id (if province not needed)
     * @return Array
     * @author Tsani Nashrullah
     **/
    public function regionByType($type, $foreignId = null, $isDropdownReq = false)
    {
        $result = [];
        $additionalAlias = [
            'id' => $isDropdownReq ? '' : ' as id',
            'text' => $isDropdownReq ? '' : ' as text',
            'kode' => $isDropdownReq ? '' : ' as kode',
        ];
        switch ($type) {
            case 'province':
                $result = (new \yii\db\Query())
                    ->from('propinsi_m')
                    ->andWhere(['is_active' => 't', 'is_deleted' => 'f'])
                    ->select([
                        'propinsi_id' . $additionalAlias['id'], 
                        'propinsi_nama' . $additionalAlias['text'],
                        'kode_propinsi' . $additionalAlias['kode'],
                    ])
                    ->all();
                break;
            case 'city':
                $result = (new \yii\db\Query())
                    ->from('kabupaten_m')
                    ->andWhere(['is_active' => 't', 'is_deleted' => 'f', 'propinsi_id' => $foreignId])
                    ->select([
                        'kabupaten_id' . $additionalAlias['id'], 
                        'kabupaten_nama' . $additionalAlias['text'],
                        'kode_kabupaten' . $additionalAlias['kode'],
                    ])
                    ->all();
                break;
            case 'district':
                $result = (new \yii\db\Query())
                    ->from('kecamatan_m')
                    ->andWhere(['is_active' => 't', 'is_deleted' => 'f', 'kabupaten_id' => $foreignId])
                    ->select([
                        'kecamatan_id' . $additionalAlias['id'], 
                        'kecamatan_nama' . $additionalAlias['text'],
                        'kode_kecamatan' . $additionalAlias['kode'],
                    ])
                    ->all();
                break;
            case 'village':
                $result = (new \yii\db\Query())
                    ->from('kelurahan_m')
                    ->andWhere(['is_active' => 't', 'is_deleted' => 'f', 'kecamatan_id' => $foreignId])
                    ->select([
                        'kelurahan_id' . $additionalAlias['id'], 
                        'kelurahan_nama' . $additionalAlias['text'],
                        'kode_kelurahan' . $additionalAlias['kode'],
                    ])
                    ->all();
                break;
        }
        return $result;
    }
}
