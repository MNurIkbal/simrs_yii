<?php 

namespace Integrasi\Service\Sirs;


use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\MorbiditasRanapHeaderView;
use Integrasi\Service\Sirs\Models\MorbiditasRanapDetailView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\Models\RLFasilitaasMorbiditasRanapHeader;

class LaporanMorbiditasRanap extends \Integrasi\Contracts\DocoImplement
{
    const _LAKI = '_LAKI';
    const _PEREMPUAN = '_PEREMPUAN';
    const K_MENINGGAL = 4;
    const K_PLG = 1;

    public function execute()
    {
        $request = ArrayHelper::getValue($this->result, 'filter', []);
        $getData =[];
    	ini_set('memory_limit','-1');
        ini_set('max_execution_time', 300);
        $listUmur = $tmp = $tmpDataHeader = $listGolongan = $data = $header = $columns= [];
        $staticHeaderAwal = [
            'no' => 'no',
            'no_dtd' => 'no_dtd',
            'note_dtd' => 'note_dtd',
            'diagnosa_nama' => 'diagnosa_nama',
        ];
        $staticHeaderAkhir = [
            'lk' => 'lk',
            'pr' => 'pr',
            'keluar_hidup' => 'keluar_hidup',
            'keluar_mati' => 'keluar_mati',
            'diagnosa_id' => 'diagnosa_id',
        ];

        $bulan = ArrayHelper::getValue($request,'bulan');
        $tahun = ArrayHelper::getValue($request,'tahun');
        $getOnlyHeader = ArrayHelper::getValue($request,'header');
        $isExcel = false;

        $query = "
            SELECT 
                *
            FROM rl4_a_morbiditasrawatinapdetail_v 
            WHERE (EXTRACT(year FROM tgl_pendaftaran) = '{$tahun}')";
        if (!empty($bulan)) {
            $query .= " AND (EXTRACT(month FROM tgl_pendaftaran)= '{$bulan}')";
        }
        $resQueryDetail = Yii::$app->db->createCommand("{$query} ")->queryAll();

        $qryUmur = "
            SELECT 
                golonganumur_id,
                golonganumur_nama,
                golonganumur_namalainnya
            FROM rl4_a_morbiditasrawatinap_v
            WHERE kolom='horizontal'
            ORDER BY golonganumur_id
        ";
        $listUmur = Yii::$app->db->createCommand("{$qryUmur} ")->queryAll();
        
        foreach($listUmur as $w => $x) {
            $golUmurId = $x['golonganumur_id'];
            $golUmurNama = str_replace(' ', '_', $x['golonganumur_namalainnya']);

            if(!$getOnlyHeader) {

                if($isExcel) {
                    if(!isset($listGolongan[$golUmurNama.self::_LAKI])){
                        $listGolongan[$golUmurNama.self::_LAKI] = null;
                    } 
    
                    if(!isset($listGolongan[$golUmurNama.self::_PEREMPUAN])){
                        $listGolongan[$golUmurNama.self::_PEREMPUAN] = null;
                    }

                    if(!isset($tmpDataHeader[$x['golonganumur_namalainnya']])){
                        $tmpDataHeader[$x['golonganumur_namalainnya']] =  $x['golonganumur_namalainnya'];
                    }
                }

                foreach($resQueryDetail as $y => $z) {
                    
                    $diagId = $z['diagnosa_id'];
                    $dtdNo = $z['no_dtd'];
                    $dtdNote = $z['dtd_noterperinci'];
                    $diagNama = $z['diagnosa_nama'];

                    if(!isset($tmp[$diagId])) {
                        $tmp[$diagId]['no'] = 0;
                        $tmp[$diagId]['diagnosa_id'] = $diagId;
                        $tmp[$diagId]['no_dtd'] = $dtdNo;
                        $tmp[$diagId]['note_dtd'] = $dtdNote;
                        $tmp[$diagId]['diagnosa_nama'] = $diagNama;
                        $tmp[$diagId]['lk'] = 0;
                        $tmp[$diagId]['pr'] = 0;
                        $tmp[$diagId]['keluar_hidup'] = 0;
                        $tmp[$diagId]['keluar_mati'] = 0;
                    }
    
                    if(!isset($tmp[$diagId][$golUmurNama.self::_LAKI])){
                        $tmp[$diagId][$golUmurNama.self::_LAKI] = 0;
                    } 
    
                    if(!isset($tmp[$diagId][$golUmurNama.self::_PEREMPUAN])){
                        $tmp[$diagId][$golUmurNama.self::_PEREMPUAN] = 0;
                    }

                    if($diagId == $z['diagnosa_id'] && $golUmurId == $z['golonganumur_id']) {  
                        if($z['jeniskelamin'] == DocoConstants::VAR_LK) {
                            $tmp[$diagId]['lk']++;
                            if(!isset($tmp[$diagId][$golUmurNama.self::_LAKI])){
                                $tmp[$diagId][$golUmurNama.self::_LAKI] = 1;
                            } else {
                                $tmp[$diagId][$golUmurNama.self::_LAKI] ++;
                            }
                        } else {
                            $tmp[$diagId]['pr']++;
                            if(!isset($tmp[$diagId][$golUmurNama.self::_PEREMPUAN])){
                                $tmp[$diagId][$golUmurNama.self::_PEREMPUAN] = 1;
                            } else { 
                                $tmp[$diagId][$golUmurNama.self::_PEREMPUAN] ++;
                            }
                        }
                        if($z['carakeluar_id'] == self::K_MENINGGAL) {
                            $tmp[$diagId]['keluar_mati'] ++;
                        } else {
                            $tmp[$diagId]['keluar_hidup'] ++;
                        }
                    } 
                }
            } else {
                if(!isset($listGolongan[$golUmurNama.self::_LAKI])){
                    $listGolongan[$golUmurNama.self::_LAKI] = null;
                } 

                if(!isset($listGolongan[$golUmurNama.self::_PEREMPUAN])){
                    $listGolongan[$golUmurNama.self::_PEREMPUAN] = null;
                }

                if(!isset($tmpDataHeader[$x['golonganumur_namalainnya']])){
                    $tmpDataHeader[$x['golonganumur_namalainnya']] =  $x['golonganumur_namalainnya'];
                }
            }
        }
        

        if($getOnlyHeader) {

            $header = array_merge($staticHeaderAwal, $listGolongan);
            $header = array_merge($header, $staticHeaderAkhir);
    
            foreach($header as $k => $v) {
                $visible = true;
                $search = false;
                $title = '';
                $nameLaki = substr($k, -5);
                $nameCewe = substr($k, -10);
    
                switch ($k) {
                    case 'diagnosa_id':
                        $title = 'Diagnosa';
                        $visible = false;
                        $search = true;
                        break;
                    case 'no_dtd':
                        $title = 'No. DTD';
                        break;
                    case 'note_dtd':
                        $title = 'No. Daftar terperinci';
                        break;
                    case 'diagnosa_nama':
                        $title = 'Golongan sebab penyakit';
                        break;
                    case 'lk':
                        $title = 'LK';
                        break;
                    case 'pr':
                        $title = 'PR';
                        break;
                    case 'keluar_hidup':
                        $title = 'Jumlah Pasien Keluar Hidup (23 + 24)';
                        break;
                    case 'keluar_mati':
                        $title = 'Jumlah Pasien Keluar mati';
                        break;
                    default:
                        $title =  $k;
                        break;
                }

                if($nameLaki == self::_LAKI) {
                    $title = 'L';
                }
    
                if($nameCewe == self::_PEREMPUAN) {
                    $title = 'P';
                }
    
                $columns[] =[
                    'title' => ucfirst($title),
                    'data' => $k,
                    'searchable' => $search,
                    'orderable' => false,
                    'visible' => $visible,
                ];
            }
        } else {
            if($isExcel) {
                $header = array_merge($staticHeaderAwal, $listGolongan);
                $header = array_merge($header, $staticHeaderAkhir);
        
                foreach($header as $k => $v) {
                    $visible = true;
                    $search = false;
                    $title = '';
                    $nameLaki = substr($k, -5);
                    $nameCewe = substr($k, -10);
        
                    switch ($k) {
                        case 'no_dtd':
                            $title = 'No. DTD';
                            break;
                        case 'note_dtd':
                            $title = 'No. Daftar terperinci';
                            break;
                        case 'diagnosa_nama':
                            $title = 'Golongan sebab penyakit';
                            break;
                        case 'lk':
                            $title = 'LK';
                            break;
                        case 'pr':
                            $title = 'PR';
                            break;
                        case 'keluar_hidup':
                            $title = 'Jumlah Pasien Keluar Hidup (23 + 24)';
                            break;
                        case 'keluar_mati':
                            $title = 'Jumlah Pasien Keluar mati';
                            break;
                        default:
                            $title =  $k;
                            break;
                    }

                    if($nameLaki == self::_LAKI) {
                        $title = 'L';
                    }
        
                    if($nameCewe == self::_PEREMPUAN) {
                        $title = 'P';
                    }
        
                    $columns[] =[
                        'title' => ucfirst($title),
                        'data' => $k,
                        'searchable' => $search,
                        'orderable' => false,
                        'visible' => $visible,
                    ];
                }
            }
            
            $no = 1;
            foreach($tmp as $k => $v) {
                if($v['keluar_hidup'] != 0 || $v['keluar_mati'] != 0) {
                    $v['no'] = $no++;
                    $data[] = $v;
                }
            }

            if(!$isExcel) {
                $getData['data_provider'] =  new ArrayDataProvider([
                    'allModels' => $data, 
                    'pagination' => [
                        'pageSize' =>  ArrayHelper::getValue($request,'pageSize', '10'),
                    ],
                ]);
                $getData['data'] = $data;
                $getData['status'] = 'Data Siap';
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'laporan-morbiditas-ranap',
                    'message' => json_encode($getData),
                ]);
                return json_encode($getData);
            }
        }

        $getData = [
            'header' => $tmpDataHeader,
            'columns' => $columns,
            'data' => $data,
            'profil' => $this->getProfil(),
            'status' => 'Data Siap',
        ];

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'laporan-morbiditas-ranap',
            'message' => json_encode($getData),
        ]);

        return json_encode([
            'header' => $tmpDataHeader,
            'columns' => $columns,
            'data' => $data,
            'profil' => $this->getProfil()
        ]);

    }

    private function getProfil()
    {
        $profil = Yii::$app->db->createCommand("
            SELECT nokode_rumahsakit, nama_rumahsakit FROM profilrumahsakit_m
        ")->queryOne();

        return $profil;
    }
}