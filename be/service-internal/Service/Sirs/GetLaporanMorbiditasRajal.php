<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;

class GetLaporanMorbiditasRajal extends \Integrasi\Contracts\DocoImplement
{
    const _LAKI = '_LAKI';
    const _PEREMPUAN = '_PEREMPUAN';

    public function execute()
    {
        ini_set('memory_limit', '-1');
		set_time_limit(0);

        $unique_str = ArrayHelper::getValue($this->result, 'unique_str', ''); // unique str diambil dari result karena menggunakan messagebroker
        
    	$cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        $data = $this->loadData();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'get-laporan:'.$unique_str,
            'message' => json_encode([
                 'status' => 'finish', 
                 'messageProcess' => 'Selesai generate data',
                 'data' => $data['data'],
                 'progress' => 100
             ]),
         ]);

        return json_encode([
            'service' => 'Sirs-GetLaporanMorbiditasRajal',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $unique_str
        ]);
    }

    private function loadData()
    {
        $request = ArrayHelper::getValue($this->result, 'filter', '');
        $listUmur = $tmp = $data = $tmpKunjungan = [];
        $bulan = ArrayHelper::getValue($request,'bulan');
        $tahun = ArrayHelper::getValue($request,'tahun');
        $instalasi_id = ArrayHelper::getValue($request,'instalasi_id');
        
        $query = "
            SELECT 
                pasien_id,
                golonganumur_id,
                golonganumur_namalainnya,
                jeniskelamin,
                jenis_kelamin,
                carakeluar_id,
                carakeluar_nama,
                diagnosa_id,
                diagnosa_nama,
                dtd_noterperinci,
				no_dtd
            FROM rl4_b_morbiditasrawatjalandetail_v 
            WHERE diagnosa_id IN 
                (
                    SELECT diagnosa_id 
                    FROM rl4_b_morbiditasrawatjalan_v
                    WHERE kolom = 'vertikal'
                )
            AND (EXTRACT(year FROM tgl_pendaftaran) = '{$tahun}')";
        if (!empty($bulan)) {
            $query .= " AND (EXTRACT(month FROM tgl_pendaftaran)= '{$bulan}')";
        }
        if (!empty($instalasi_id)) {
            $query .= " AND instalasi_id = '{$instalasi_id}'";
        }
        $query .= " ORDER BY dtd_noterperinci";
        $resQueryDetail = Yii::$app->db->createCommand("{$query} ")->queryAll();
        
        $qryUmur = "
            SELECT 
                golonganumur_id,
                golonganumur_nama,
                golonganumur_namalainnya
            FROM rl4_b_morbiditasrawatjalan_v
            WHERE kolom='horizontal'
            ORDER BY golonganumur_id
        ";
        $listUmur = Yii::$app->db->createCommand("{$qryUmur} ")->queryAll();

        foreach($listUmur as $k => $v) {
            $golUmurId = $v['golonganumur_id'];
            $golUmurNama = str_replace(' ', '_', $v['golonganumur_namalainnya']);

            foreach($resQueryDetail as $y => $z) {
                $diagId = $z['diagnosa_id'];
                $pasien_id = $z['pasien_id'];

                if(!isset($tmp[$diagId])) {
                    $tmp[$diagId]['no'] = 0;
                    $tmp[$diagId]['diagnosa_id'] = $diagId;
                    $tmp[$diagId]['no_dtd'] = $z['no_dtd'];
                    $tmp[$diagId]['note_dtd'] = $z['dtd_noterperinci'];
                    $tmp[$diagId]['diagnosa_nama'] = $z['diagnosa_nama'];
                    $tmp[$diagId]['lk'] = 0;
                    $tmp[$diagId]['pr'] = 0;
                    $tmp[$diagId]['jml_kasus_baru'] = 0;
                    $tmp[$diagId]['jml_kunjungan'] = 0;
                }

                if(!isset($tmp[$diagId][$golUmurNama.self::_LAKI])){
                    $tmp[$diagId][$golUmurNama.self::_LAKI] = 0;
                } 

                if(!isset($tmp[$diagId][$golUmurNama.self::_PEREMPUAN])){
                    $tmp[$diagId][$golUmurNama.self::_PEREMPUAN] = 0;
                }
                
                if ($golUmurId == $z['golonganumur_id']) {
                    if(!isset($tmpKunjungan[$pasien_id][$diagId])) {
                        $tmpKunjungan[$pasien_id][$diagId] = true;
                        if($z['jeniskelamin'] == DocoConstants::VAR_LK) {
                            $tmp[$diagId]['lk']++;
                            $tmp[$diagId][$golUmurNama.self::_LAKI] ++;
                        } else {
                            $tmp[$diagId]['pr']++;
                            $tmp[$diagId][$golUmurNama.self::_PEREMPUAN] ++;
                        }
                        $tmp[$diagId]['jml_kasus_baru'] ++;
                    } else {
                        if($z['jeniskelamin'] == DocoConstants::VAR_LK) {
                            $tmp[$diagId][$golUmurNama.self::_LAKI] ++;
                        } else {
                            $tmp[$diagId][$golUmurNama.self::_PEREMPUAN] ++;
                        }
                    }
                    $tmp[$diagId]['jml_kunjungan'] ++;   
                }
            }
        }

        $no = 1;
        foreach($tmp as $k => $v) {
            if($v['jml_kasus_baru'] != 0 || $v['jml_kunjungan'] != 0) {
                $v['no'] = $no++;
                $data[] = $v;
            }
        }

        return [
            'data' => $data,
        ];
    }
}