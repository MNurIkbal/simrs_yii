<?php

namespace Doco\Repositories;

use Yii;
use Doco\components\DocoConstants;
use Doco\models\Pendaftaran;
use Doco\models\VitalSign;
use Doco\models\Lookup;
use Doco\models\MetodeGcs;
use yii\helpers\ArrayHelper;

class MonitoringTtvRepositories
{
    public function listLookup() {
        return Lookup::find()
            ->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value'])
            ->where([
                'lookup_type' => ['tingkat_kesadaran', 'jenis_ttv', 'sumber_ttv'], 
                'is_active' => true, 
                'is_deleted' => false
            ])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->all();
    }

    public function metodeGcs()
    {
        $data = [];

        $getData = MetodeGcs::find()
            ->select(['metodegcs_id', 'metodegcs_nilai'])
            ->where(['is_active' => true])
            ->all();

        foreach ($getData as $item) {
            $data[$item->metodegcs_id] = $item->metodegcs_nilai;
        }

        return $data;
    }

    public function getPasienIdByPendaftaranId($pendaftaranId)
    {
        return Pendaftaran::find()
            ->select('pasien_id')
            ->where(['pendaftaran_id' => $pendaftaranId])
            ->scalar();
    }

    public function insertData($data) 
    {
        $vitalSign = new VitalSign();
        $vitalSign->load($data, '');
        $vitalSign->tanggal_ttv = date('Y-m-d H:i:s');
        $vitalSign->created_date = date('Y-m-d H:i:s');
        $vitalSign->is_active = true;
        $vitalSign->is_deleted = false;
        $vitalSign->created_by = Yii::$app->user->id;
        $vitalSign->last_modified_by = null;
        $vitalSign->last_modified_date = null;
        $vitalSign->modified_count = 0;
        $vitalSign->deleted_date = null;
        $vitalSign->deleted_by = null;

        if ($vitalSign->save()) {
            return [
                'status' => 'success',
                'message' => 'TTV data inserted successfully',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to insert TTV data',
            ];
        }
    }

    // $data => array of raw data
    // $type => string of type ['asesmen_keperawatan', 'asesmen_medis']
    // $source => string of source ['rj', 'ri', 'rd']
    public function mapDataTtv($data, $type, $source)
    {
        $result = [];
        $lookup = [];
        $pendaftaranId = ArrayHelper::getValue($data, 'pendaftaran_id', null);
        $pasienId = self::getPasienIdByPendaftaranId($pendaftaranId);
        $data['pasien_id'] = $pasienId;
        
        $getMapping = VitalSign::mapData();
        $map = $getMapping[$type][$source];
        
        // mapping process
        foreach ($map as $key => $value) {
            if (isset($data[$value])) {
                // change comma to dot
                if(strpos($data[$value], ',') !== false) {
                    $data[$value] = str_replace(',', '.', $data[$value]);
                }

                if($data[$value] == '') { 
                    $data[$value] = null;
                }

                if($key === 'sistol') {
                    if (strpos($data[$value], '/') !== false) {
                        $tekananDarah = explode('/', $data[$value]);
                        $result['sistol'] = isset($tekananDarah[0]) ? trim($tekananDarah[0]) : null;
                        $result['diastol'] = isset($tekananDarah[1]) ? trim($tekananDarah[1]) : null;
                        continue;
                    } else {
                        $result['sistol'] = ArrayHelper::getValue($data, $value, null);
                        $result['diastol'] = null;
                        continue;
                    }
                }

                if($key === 'diastol') {
                    if(is_null($result['diastol'])) {
                        $result['diastol'] = ArrayHelper::getValue($data, $value, null);
                    }

                    continue; // skip diastol since it's already handled in sistol
                }

                if($key === 'gcs_e' || $key === 'gcs_v' || $key === 'gcs_m') {
                    // get metode gcs
                    $metodeGcs = self::metodeGcs();
                    $result[$key] = isset($metodeGcs[$data[$value]]) ? $metodeGcs[$data[$value]] : null;
                    continue;
                }
            }

            $result[$key] = ArrayHelper::getValue($data, $value, null);
        }

        $getLookup = self::listLookup();
        foreach ($getLookup as $item) {
            $lookup[$item->lookup_name] = [
                'id' => $item->lookup_id,
                'name' => $item->lookup_value
            ];
        }

        $result['sumberttv_id'] = ArrayHelper::getValue($lookup, $type)['id'];
        $result['sumberttv'] = ArrayHelper::getValue($lookup, $type)['name'];
        $result['pendaftaran_id'] = ArrayHelper::getValue($data, 'pendaftaran_id', null);
        $result['pasien_id'] = ArrayHelper::getValue($data, 'pasien_id', null);

        // default value 
        // because data from asesmen keperawatan & asesmen medis
        // are has no jenis and tingkat kesadaran
        $result['jenisttv_id'] = null;
        $result['jenisttv'] = null;
        $result['tingkatkesadaran_id'] = null;
        $result['tingkatkesadaran'] = null;

        return $result;
    }

    public function checkExistData($vitalSign)
    {
        $isExist = false;

        $existingRecord = VitalSign::find()
            ->where([
                'pendaftaran_id' => $vitalSign['pendaftaran_id'],
                'pasien_id' => $vitalSign['pasien_id'],
            ])
            ->orderBy([
                'vitalsign_id' => SORT_DESC,
            ])
            ->asArray()
            ->one();

        if ($existingRecord) {
            $fieldsToCompare = [
                'sumberttv_id',
                'sistol',
                'diastol',
                'nadi',
                'respirasi',
                'spo2',
                'suhu',
                'tinggi_badan',
                'berat_badan',
                'gcs_e',
                'gcs_v',
                'gcs_m',
            ];

            $allMatch = true;

            foreach ($fieldsToCompare as $field) {
                $existingValue = ArrayHelper::getValue($existingRecord, $field);
                $incomingValue = ArrayHelper::getValue($vitalSign, $field);

                if ($existingValue != $incomingValue) {
                    $allMatch = false;
                }
            }

            if ($allMatch) {
                $isExist = true;
            }
        }

        return $isExist;
    }
}
