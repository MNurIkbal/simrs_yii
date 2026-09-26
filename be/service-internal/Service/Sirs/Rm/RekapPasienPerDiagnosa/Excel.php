<?php

namespace Integrasi\Service\Sirs\Rm\RekapPasienPerDiagnosa;

use Yii;
use Integrasi\Service\Sirs\Models\LaprekappasienperdiagnosadetV;
use Integrasi\Components\DocoRestActiveFilter;

class Excel extends \Integrasi\Contracts\DocoImplement
{
    public $pointer = 0;
    public $no = 1;
    public $tmpCache = [];

    public function execute()
    {
        $this->generateObjectData();
        $cacheFiles = Yii::$app->cacheFiles;
        $cacheFiles->set($this->unique_str . '-' . $this->pointer, $this->tmpCache);

        return json_encode([
            'service' => 'Sirs-RekapPasienPerDiagnosa-Excel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str,
        ]);
    }

    private function generateObjectData()
    {
        switch ($this->type) {
            case 'detail':
                $dataObject = $this->data()->asArray()->all();
                $this->generateExcelDetail($dataObject);
                break;
            default:
                $dataObject = $this->generateDataRekap($this->data()->orderBy(['diagnosa_utama_kode' => SORT_ASC])->asArray()->all());
                $this->generateExcelRekap($dataObject);
                break;
        }
    }

    private function data()
    {
        $request = $this->filter;
        $model = new LaprekappasienperdiagnosadetV;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($request['advanced-filter'])) {
            if (isset($request['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pendaftaran']);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }

    private function generateDataRekap($tmpData)
    {
        $data = $tmp = [];

        if (!empty($tmpData)) {
            foreach ($tmpData as $key => $value) {
                $kodeDiagnosa = $value['diagnosa_utama_kode'];
                $pasienId = $value['pasien_id'];

                if (!isset($tmp[$kodeDiagnosa][$pasienId])) {
                    $tmp[$kodeDiagnosa][$pasienId] = [
                        'diagnosa_utama_kode' => $kodeDiagnosa,
                        'diagnosa_utama' => $value['diagnosa_utama']
                    ];
                }
            }

            foreach ($tmp as $key => $value) {
                foreach ($value as $k => $v) {
                    $kodeDiagnosa = $v['diagnosa_utama_kode'];
                    if (!isset($data[$kodeDiagnosa])) {
                        $data[$kodeDiagnosa] = [
                            'diagnosa_utama_kode' => $kodeDiagnosa,
                            'diagnosa_utama' => $v['diagnosa_utama'],
                            'jumlah_pasien' => 1,
                            'tgl_pendaftaran' => '',
                            'dokterdpjp_nama' => '',
                            'diagnosa_utama_id' => '',
                            'nama_pasien' => '',
                            'no_rekam_medik' => '',
                            'instalasi_id' => '',
                            'ruangan_id' => '',
                            'dokterdpjp_id' => '',
                        ];
                    } else {
                        $data[$kodeDiagnosa]['jumlah_pasien'] += 1;
                    }
                }
            }
        }

        return $data;
    }

    private function generateExcelRekap($data)
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $row = [];
        foreach ($data as $key => $value) {
            $tmp[1] = $this->no;
            $tmp[2] = $value['diagnosa_utama_kode'];
            $tmp[3] = $value['diagnosa_utama'];
            $tmp[4] = $value['jumlah_pasien'];
            $row[] = $tmp;
            $this->tmpCache[] = $tmp;
            if (($this->no % 50) == 0) {
                $this->flagZero();
            }
            $this->no++;
        }
        return $row;
    }

    private function generateExcelDetail($data)
    {
        $row = [];
        foreach ($data as $key => $value) {
            $tmp[1] = $this->no;
            $tmp[2] = $value['nama_pasien'];
            $tmp[3] = $value['no_rekam_medik'];
            $tmp[4] = $value['no_pendaftaran'];
            $tmp[5] = $value['tgl_pendaftaran'];
            $tmp[6] = $value['dokterdpjp_nama'];
            $tmp[7] = $value['instalasi_nama'];
            $tmp[8] = $value['ruangan_nama'];
            $tmp[9] = $value['diagnosa_utama'];
            $tmp[10] = $value['diagnosa_sekunder1'];
            $tmp[11] = $value['diagnosa_sekunder2'];
            $tmp[12] = $value['diagnosa_sekunder3'];
            $row[] = $tmp;
            $this->tmpCache[] = $tmp;
            if (($this->no % 50) == 0) {
                $this->flagZero();
            }
            $this->no++;
        }
    }

    private function flagZero()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        $cacheFiles->set($this->unique_str . '-' . $this->pointer, $this->tmpCache);
        $this->pointer++;
        $this->tmpCache = [];
    }
}
