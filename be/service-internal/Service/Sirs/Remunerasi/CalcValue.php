<?php

namespace Integrasi\Service\Sirs\Remunerasi;

use app\components\DocoController;
use Yii;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPegawai;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPegawaiCalc;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunTarget;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPointView;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunKmk;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunKelompok;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunTindakan;
use Integrasi\Service\Sirs\Models\Jabatan;
use Integrasi\Service\Sirs\Models\Lookup;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;

class CalcValue extends \Integrasi\Contracts\DocoImplement
{
    protected $listPoint;
    protected $lookup;

    public function execute()
    {
        $this->getListPoint();
        $this->getLookup();

        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getDataAttibutes($this->filter, $this->get);
        $isMappingNamaTindakan = ArrayHelper::getValue($this->lookup, DocoConstants::REMUN_CALC_MAPPING);

        if($isMappingNamaTindakan == 'true') {
            $attributes = $this->mappingTindakan($attributes);
        }

        $calculation = $this->calc($attributes);
        $cacheFiles->set($this->unique_str, $calculation);
        
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Berhasil menyiapkan data.',
                'progress' => 70
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-Remunerasi-CalcValue',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function getDataAttibutes($filter, $get)
    {
        $model = new RemunPegawai;
        $query = $model::find();
        $bulan = date('m');
        $tahun = date('Y');
        $ids = ArrayHelper::getValue($get, 'ids', []);
        $periodeRemun = ArrayHelper::getValue($filter, 'periode_remun');
        $namaPegawai = ArrayHelper::getValue($filter, 'nama_pegawai');
        $jabatan = ArrayHelper::getValue($filter, 'jabatan_nama');
        $nik = ArrayHelper::getValue($filter, 'nik');
        
        if (!empty($ids)) {
            return $query->where(['remunpegawai_id' => $ids])->asArray()->all();
        } else {
            if (!empty($periodeRemun)) {
                $explode = explode("-", $periodeRemun);
                if (count($explode) == 2) {
                    $bulan = ArrayHelper::getValue($explode, 1);
                    $tahun = ArrayHelper::getValue($explode, 0);
                }
                unset($periodeRemun);
            }

            $query->andWhere([
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
                'is_deleted' => false
            ]);

            if (!empty($namaPegawai)) {
                $query->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($namaPegawai)]);
            }
            
            if (!empty($jabatan)) {
                $query->andWhere(['ILIKE', 'LOWER(jabatan_nama)', strtolower($jabatan)]);
            }
            
            if (!empty($nik)) {
                $query->andWhere(['ILIKE', 'nik', strtolower($nik)]);
            }

            return $query->asArray()->all();
        }
    }

    protected function getListPoint()
    {
        $listPoint = [];
        $othersTindakan = [
            'Ronde Besar',
            'Koord lap jaga Bangsal',
            'Rapat Koord Pelayanan',
            'Audit Medik',
            'Penelitian',
            'Presentasi',
            'Rapat Direksi'
        ];

        $getListPoint = RemunPointView::find()
            ->select(['remunkelompok_nama', 'point'])
            ->asArray()
            ->all();

        foreach ($getListPoint as $pointKey => $pointValue) {
            if(in_array($pointValue['remunkelompok_nama'], $othersTindakan)) {
                $arrKey = strtolower(str_replace(' ', '_', $pointValue['remunkelompok_nama']));
                $listPoint[$arrKey] = $pointValue['point'];
            } else {
                $listPoint[$pointValue['remunkelompok_nama']] = $pointValue['point'];
            }
        }

        $this->listPoint = $listPoint;
        
        return true;
    }

    protected function getLookup()
    {
        $tmpResult = [];
        $typeList = [
            DocoConstants::REMUN_CALC_MAPPING,
            DocoConstants::REMUN_PENGALI_JUMLAH_POINT,
            DocoConstants::REMUN_PENGALI_KEPATUHAN,
            DocoConstants::REMUN_PENGALI_KWALITAS,
            DocoConstants::REMUN_PENGALI_TARGET_IKI,
            DocoConstants::REMUN_PENGURANG_TIDAK_APEL
        ];

        $getLookup = Lookup::find()
            ->select(['lookup_type', 'lookup_value'])
            ->where(['IN', 'lookup_type', $typeList])
            ->asArray()
            ->all();
        
        foreach ($getLookup as $value) {
            $tmpResult[$value['lookup_type']] = $value['lookup_value'];
        }

        $this->lookup = $tmpResult;

        return true;
    }

    protected function getListKelompokRemun()
    {
        $listKelompokRemun = [];
        $getListKelompokRemun = RemunKelompok::find()
            ->select(['remunkelompok_id', 'remunkelompok_nama'])
            ->asArray()
            ->all();

        foreach ($getListKelompokRemun as $key => $value) {
            $listKelompokRemun[$value['remunkelompok_id']] = $value;
        }

        return $listKelompokRemun;
    }

    protected function getListTindakanRemun()
    {
        $listTindakanRemun = [];
        $getListTindakanRemun = RemunTindakan::find()
            ->select(['daftartindakan_nama', 'remunkelompok_id'])
            ->asArray()
            ->all();

        foreach ($getListTindakanRemun as $key => $value) {
            $listTindakanRemun[$value['daftartindakan_nama']] = $value;
        }
        return $listTindakanRemun;
    }

    protected function mappingTindakan($data)
    {
        $listKelompokRemun = self::getListKelompokRemun();
        $listTindakanRemun = self::getListTindakanRemun();

        foreach ($data as $key => $value) {
            $formTindakan = $this->createFormTindakan($listKelompokRemun);
            
            if ($value['detail_tindakan'] == null && $value['detail_absen'] == null) {
                continue;
            }

            $tindakan = json_decode($value['detail_tindakan'], true)['detail_tindakan'];

            foreach ($tindakan as $tindakanKey => $tindakanVal) {
                if (
                    in_array($tindakanVal['tindakan_nama'], array_keys($listTindakanRemun))
                    && $listTindakanRemun[$tindakanVal['tindakan_nama']]['remunkelompok_id'] != null
                ) {
                    $formTindakan[$listTindakanRemun[$tindakanVal['tindakan_nama']]['remunkelompok_id']]['qty'] += $tindakanVal['qty'];
                }
            }

            $data[$key]['detail_tindakan'] = json_encode(['detail_tindakan' => $formTindakan]);
        }

        return $data;
    }

    protected function createFormTindakan($listKelompokRemun)
    {
        $formTindakan = [];

        foreach ($listKelompokRemun as $key => $value) {
            $block = [
                "tindakan_kode" => "XXX",
                "tindakan_nama" => $value['remunkelompok_nama'],
                "qty" => 0
            ];

            $formTindakan[$value['remunkelompok_id']] = $block;
        }

        return $formTindakan;
    }


    protected function calc($data)
    {
        $result = [];
        $othersTindakan = [
            'ronde_besar',
            'koord_lap_jaga_bangsal',
            'rapat_koord_pelayanan',
            'audit_medik',
            'penelitian',
            'presentasi',
            'rapat_direksi'
        ];

        $listPoint = $this->listPoint;

        foreach ($data as $key => $value) {
            $tmp = [];
            $realisasiPoint = 0;

            if(
                $value['detail_tindakan'] == null
                && $value['detail_absen'] == null
            ) {
                continue;
            }

            // remove previous data if exist
            if(
                $value['remunpegawai_id'] != null
                && $value['periode_bulan'] != null
                && $value['periode_tahun'] != null
            ) {
                $lastCalc = RemunPegawaiCalc::find()
                ->where(['remunpegawai_id' => $value['remunpegawai_id']])
                ->andWhere(['periode_bulan' => $value['periode_bulan']])
                ->andWhere(['periode_tahun' => $value['periode_tahun']])
                ->one();

                if (!empty($lastCalc)) {
                    $lastCalc->is_deleted = true;
                    $lastCalc->is_active = false;
                    $lastCalc->deleted_date = date('Y-m-d H:i:s');
                    $lastCalc->save();
                }
            }

            $getTarget = $this->getTarget(
                            ArrayHelper::getValue($value, 'jabatan_kode'), 
                            ArrayHelper::getValue($value, 'grading'), 
                            ArrayHelper::getValue($value, 'posisi')
                        );

            $detail_tindakan = json_decode($value['detail_tindakan'], true)['detail_tindakan'];
            $calcPointTindakan = $this->calcDetailTindakan($detail_tindakan);
            $realisasiPoint += $calcPointTindakan['point_tindakan'];

            $absensi = json_decode($value['detail_absen'], true)['detail_absen'];
            $calcAbsensiDeduction = $this->calcAbsensiDeduction($absensi);
            $absensiDeduction = $calcAbsensiDeduction['total_deduction'];

            $kwalitas = round(
                (ArrayHelper::getValue($this->lookup, DocoConstants::REMUN_PENGALI_KWALITAS) * (80 / 100)), 2
            ); // constant value
            
            $kepatuhan = round(
                (ArrayHelper::getValue($this->lookup, DocoConstants::REMUN_PENGALI_KEPATUHAN) * (80 / 100)), 2
            ); // constant value

            if($value['jabatan_nama'] != null) {
                $pointManajerial = $this->getTarget(ArrayHelper::getValue($value, 'jabatan_nama'));
            }

            $temp['remunpegawai_id'] = ArrayHelper::getValue($value, 'remunpegawai_id');
            $temp['periode_bulan'] = ArrayHelper::getValue($value, 'periode_bulan');
            $temp['periode_tahun'] = ArrayHelper::getValue($value, 'periode_tahun');
            $temp['target_point'] = ArrayHelper::getValue($getTarget, 'point');
            $temp['target_nominal'] = ArrayHelper::getValue($getTarget, 'nominal');
            $temp['detail_tindakan'] = json_encode(ArrayHelper::getValue($calcPointTindakan, 'detail_tindakan'));

            foreach ($othersTindakan as $otherValue) {
                $qty = ArrayHelper::getValue($value, $otherValue);
                $basePoint = ArrayHelper::getValue($listPoint, $otherValue);
                $otherPoint = $qty * $basePoint;
                
                $temp[$otherValue] = $otherPoint;
                $realisasiPoint += $otherPoint;
            }

            $jumlahPoint = round(
                $realisasiPoint / (ArrayHelper::getValue($this->lookup, DocoConstants::REMUN_PENGALI_JUMLAH_POINT) + $kwalitas + $kepatuhan)
            );
            $totalPoint = $jumlahPoint + $pointManajerial['point'];
            $tidakApel = ArrayHelper::getValue($value, 'tidak_apel') * ArrayHelper::getValue($this->lookup, DocoConstants::REMUN_PENGURANG_TIDAK_APEL);
            $iki = round($totalPoint / ArrayHelper::getValue($getTarget, 'point'), 2);
            $nominal = (
                ($iki * ArrayHelper::getValue($getTarget, 'nominal')) 
                * ((100 - $absensiDeduction) / 100)
            ) - $tidakApel;
            $imbalJasa = $nominal - ArrayHelper::getValue($getTarget, 'max_kmk');

            if($imbalJasa < 0) {
                $imbalJasa = 0;
            }

            $temp['detail_absen'] = json_encode(ArrayHelper::getValue($calcAbsensiDeduction, 'absensi'));
            $temp['kwalitas'] = $kwalitas;
            $temp['kepatuhan'] = $kepatuhan;
            $temp['jumlah_point'] = $jumlahPoint;
            $temp['manajerial'] = ArrayHelper::getValue($pointManajerial, 'point');
            $temp['total_point'] = $totalPoint;
            $temp['total_potongan'] = $absensiDeduction;
            $temp['tidak_apel'] = $tidakApel;
            $temp['iki'] = $iki;
            $temp['nominal'] = $nominal;
            $temp['max_kmk'] = ArrayHelper::getValue($getTarget, 'max_kmk');
            $temp['imbal_jasa'] = $imbalJasa;
            $temp['created_date'] = date('Y-m-d H:i:s');
            $temp['is_active'] = true;
            $temp['is_deleted'] = false;

            $result[] = $temp;

            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-excel:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'calculation',
                    'messageProcess' => '',
                    'progress' => ''
                ]),
            ]);
        }

        return $result;
    }

    protected function getTarget($jabatan, $grading = null, $posisi = null)
    {
        $target = 0;
        $maxKmk = 0;
        $nominal = 0;

        $dataJabatan = Jabatan::find()->where(['jabatan_nama' => $jabatan])->one();
        $targetMap = RemunTarget::find()->where(['jabatan_id' => $dataJabatan->jabatan_id])->one();
        $kmkMap = RemunKmk::find()->where(['jabatan_id' => $dataJabatan->jabatan_id])->one();

        if($targetMap != null) {
            $target = $targetMap->target;
            $nominal = $targetMap->target * ArrayHelper::getValue($this->lookup, DocoConstants::REMUN_PENGALI_TARGET_IKI);
        }

        if ($kmkMap != null) {
            $maxKmk = $kmkMap->max_kmk;
        }

        return [
            'point' => $target,
            'nominal' => $nominal,
            'max_kmk' => $maxKmk
        ];
    }

    protected function calcDetailTindakan($data)
    {
        $pointTindakan = 0;
        $listPoint = $this->listPoint;

        foreach ($data as $key => $value) {
            $basePoint = isset($listPoint[$value['tindakan_nama']]) ? $listPoint[$value['tindakan_nama']] : 0;
            $point = $value['qty'] * $basePoint;

            $data[$key]['point'] = $point;
            
            $pointTindakan += $point;
        }

        return [
            'point_tindakan' => $pointTindakan,
            'detail_tindakan' => $data
        ];
    }

    protected function calcAbsensiDeduction($data)
    {
        if(is_array($data)) {
            $absensi = $data[0];
        } else {
            $absensi = $data;
        }

        $totalDeduction = 0;
        $deductionPrecentage = [
            "1_sd_30"       => 0.5,
            "31_sd_60"      => 1,
            "61_sd_90"      => 1.25,
            "lebih_dari_90" => 1.5,
            "tidak_finger"   => 1.5,
            'pengecualian_absen' => 3
        ];

        foreach ($absensi as $key => $value) {
            if(is_array($value)) {
                foreach ($value[0] as $vKey => $vVal) {
                    $keyName = $vKey . '_percentage';
                    $percentage = $vVal * $deductionPrecentage[$vKey];
                    $absensi[$key][0][$keyName] = $percentage;

                    $totalDeduction += $percentage;
                }
            } else {
                $keyName = $key . '_percentage';
                $percentage = $absensi[$key] * $deductionPrecentage[$key];
                $absensi[$keyName] = $percentage;

                $totalDeduction += $percentage;
            }
        }

        $result = array($absensi);

        return [
            'total_deduction' => $totalDeduction,
            'absensi' => $result
        ];
    }
}
