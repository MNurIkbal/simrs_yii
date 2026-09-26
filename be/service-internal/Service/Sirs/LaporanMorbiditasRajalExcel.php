<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\MorbiditasRajalHeaderView;
use Integrasi\Service\Sirs\Models\MorbiditasRajalDetailView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanMorbiditasRajalExcel extends \Integrasi\Contracts\DocoImplement
{
    const _LAKI = '_LAKI';
    const _PEREMPUAN = '_PEREMPUAN';

    public function execute()
    {
        ini_set('memory_limit', '-1');
		set_time_limit(0);
        
    	$cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        $data = $this->loadData();
        $dataColumns = $data['columns'];
        $countGol = count($data['header']);

        $loopCheckHeader = 0;
        foreach($data['header'] as $v) {
            if($loopCheckHeader == 0) {
                $listGol[] = [
                    'label' => $v,
                    'startfrom' => 5,
                    'colspan' => 2
                ];
            } else {
                $listGol[] = [
                    'label' => $v,
                    'colspan' => 2
                ];
            }
            $loopCheckHeader++;
        }

        $loopCheckGol = 0;
        foreach($dataColumns as $k => $v) {
            if ($k >= 4 && $k < (($countGol * 2) + 4) ) {
                if($loopCheckGol == 0) {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'startfrom' => 5,
                        'data' => $v['data']
                    ];
                } else {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'data' => $v['data']
                    ];
                }
                $loopCheckGol++;
            }
        }

        $row_excel = 0;
        foreach($data['data'] as $k => $v) {
                $no = 5;
                $tmp[1] = $v['no'];
                $tmp[2] = $v['no_dtd'];
                $tmp[3] = $v['note_dtd'];
                $tmp[4] = $v['diagnosa_nama'];
                for($i=0; $i < count($jkGolUmur); $i++) {
                    $text = str_replace(' ', '_', $jkGolUmur[$i]['data']);
                    $tmp[$no] = $v[$text];
                    $no++;
                }
                $tmp[$no] = $v['lk'];
                $tmp[$no+1] = $v['pr'];
                $tmp[$no+2] = $v['jml_kasus_baru'];
                $tmp[$no+3] = $v['jml_kunjungan'];
                $tmpCache[] = $tmp;
                if (($row_excel%50) == 0) 
                {
                    Yii::$app->redis->executeCommand('PUBLISH', [
                       'channel' => 'export-excel:'.$this->unique_str,
                       'message' => json_encode(['unique_process' => $this->unique_str]),
                    ]);
                    $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
                    $prefix++;
                    $tmpCache = [];
                }
                $row_excel++;
        }

        $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);

        return json_encode([
            'service' => 'Sirs-LaporanMorbiditasRajalExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $listUmur = $tmp = $tmpDataHeader = $listGolongan = $data = $header = $columns = $tmpKunjungan = [];
        $staticHeaderAwal = [
            'no' => 'no',
            'no_dtd' => 'no_dtd',
            'note_dtd' => 'note_dtd',
            'diagnosa_nama' => 'diagnosa_nama',
        ];
        $staticHeaderAkhir = [
            'lk' => 'lk',
            'pr' => 'pr',
            'jml_kasus_baru' => 'jml_kasus_baru',
            'jml_kunjungan' => 'jml_kunjungan',
        ];

        $bulan = ArrayHelper::getValue($request,'bulan');
        $tahun = ArrayHelper::getValue($request,'tahun');
        $instalasi_id = ArrayHelper::getValue($request,'instalasi_id');

        $modelheader = new MorbiditasRajalHeaderView;
        $queryheader = $modelheader::find();
        $header = $queryheader->where(['kolom' => 'vertikal'])
        ->orderBy([
            'dtd_noterperinci' => SORT_ASC,
            'diagnosa_kode' => SORT_ASC,
        ])->all();

        $query = "
            SELECT 
                pasien_id,
                golonganumur_id,
                jeniskelamin,
                jenis_kelamin,
                carakeluar_id,
                carakeluar_nama,
                diagnosa_id,
                diagnosa_nama,
                dtd_noterperinci,
				no_dtd
            FROM rl4_b_morbiditasrawatjalandetailexcel_v 
            WHERE (EXTRACT(year FROM tgl_pendaftaran) = '{$tahun}')";
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
            FROM rl4_b_morbiditasrawatjalanexcel_v
            WHERE kolom='horizontal'
            ORDER BY golonganumur_id
        ";
        $listUmur = Yii::$app->db->createCommand("{$qryUmur} ")->queryAll();

        /** Generate all diagnosa */
        foreach($header as $k => $v) {
            $diagId = $v['diagnosa_id'];
            $dtdNo = $v['no_dtd'];
            $dtdNote = $v['dtd_noterperinci'];
            $diagNama = $v['diagnosa_nama'];

            foreach($listUmur as $w => $x) {
                $golUmurId = $x['golonganumur_id'];
                $golUmurNama = str_replace(' ', '_', $x['golonganumur_namalainnya']);
                    
                if(!isset($tmp[$diagId])) {
                    $tmp[$diagId]['no'] = 0;
                    $tmp[$diagId]['diagnosa_id'] = $diagId;
                    $tmp[$diagId]['no_dtd'] = $dtdNo;
                    $tmp[$diagId]['note_dtd'] = $dtdNote;
                    $tmp[$diagId]['diagnosa_nama'] = $diagNama;
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
                    
                /** Set Header Golongan Umur */
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

        /** Generate data morbiditas rajal to tmp*/
        foreach($listUmur as $k => $v) {
            $golUmurId = $v['golonganumur_id'];
            $golUmurNama = str_replace(' ', '_', $v['golonganumur_namalainnya']);

            foreach($resQueryDetail as $y => $z) {
                $diagId = $z['diagnosa_id'];
                $pasien_id = $z['pasien_id'];
                
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
                case 'jml_kasus_baru':
                    $title = 'Jumlah Kasus Baru (23 + 24)';
                    break;
                case 'jml_kunjungan':
                    $title = 'Jumlah Kunjungan';
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
            
        $no = 1;
        foreach($tmp as $k => $v) {
            $v['no'] = $no++;
            $data[] = $v;
        }

        return [
            'header' => $tmpDataHeader,
            'columns' => $columns,
            'data' => $data,
            'profil' => $this->getProfil()
        ];
    }

    private function getProfil()
    {
        $profil = Yii::$app->db->createCommand("
            SELECT nokode_rumahsakit, nama_rumahsakit FROM profilrumahsakit_m
        ")->queryOne();

        return $profil;
    }
}