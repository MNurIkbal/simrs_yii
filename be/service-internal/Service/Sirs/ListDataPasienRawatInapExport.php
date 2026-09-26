<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\InfoPasienRanapView;
use Integrasi\Components\DocoConstants;

class ListDataPasienRawatInapExport extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cache = Yii::$app->cache;
        $result = $this->loadData()->asArray()->all();
        $tempRows = [];
        $rowsIndex = 0;

        if (!empty($result)) {

            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-excel:'.$this->unique_str,
                'message' => json_encode([
                    'status' => 'finish',
                    'messageProcess' => 'Mempersiapkan data hasil pencarian',
                    'progress' => 10
                ]),
            ]);

            $crs = count($result);
            for ($idx = 0; $idx < $crs; $idx++) {
                $tmp = [];
                $value = $result[$idx];

                $statusTitipan = '-';
                $isTitip = false;
                $isKonsul = false;
                $statusPasien = 'PASIEN NON KONSUL';
                if ($value['carabayar_id'] == 6) {
                    $statusTitipan = $statusTitipan;
                } else if (!empty($value['is_pasientitipan_pk'])) {
                    if ($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false) {
                        $statusTitipan = $value['kelas_ditagihkan_nama'];
                        $isTitip = true;
                    }
                } else if (empty($value['is_pasientitipan_pk'])) {
                    if ($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false) {
                        $statusTitipan = $value['kelas_ditagihkan_nama'];
                        $isTitip = true;
                    }
                }

                $date1 = date_create(date('Y-m-d', strtotime($value['tgl_admisi'])));
                $date2 = date_create(date('Y-m-d'));
                $diff = date_diff($date1, $date2);
                $hariRawat = $diff->format('%a') + 1;

                if( ($value['jenis_konsul'] == 434 || $value['jenis_konsul'] == 435) && $value['status_konsul'] == 437 ) {
                    $isKonsul = true;
                }

                if($isKonsul == true){
                    $statusPasien = "PASIEN KONSUL";
                } else if ($isTitip == true){
                    $statusPasien = "PASIEN TITIPAN";
                } else if ($value['is_stopakomodasi'] == true) {
                    $statusPasien = "PASIEN STOP AKOMODASI";
                }

                $tmp['Tanggal Masuk'] = date('d M Y H:i:s', strtotime($value['tgl_admisi']));
                $tmp['No Rekam Medik'] = $value['no_rekam_medik'];
                $tmp['No Pendaftaran'] = $value['no_pendaftaran'];
                $tmp['Nama Pasien'] = $value['nama_pasien'];
                $tmp['Jenis Kelamin'] = $value['jenis_kelamin'];
                $tmp['Dokter DPJP'] = $value['dokter_admisi'];
                $tmp['Cara Bayar'] = $value['carabayar_nama'];
                $tmp['Penjamin'] = $value['penjamin_nama'];
                $tmp['Hak Kelas'] = $value['hak_kelas'];
                $tmp['Kelas Pelayanan'] = $value['kelas_pelayanan'];
                $tmp['kelas Tagihan'] = $statusTitipan;
                $tmp['Kasus Penyakit'] = $value['jeniskasuspenyakit_nama'];
                $tmp['Nama Ruangan'] = $value['ruangan_nama'];
                $tmp['No Kamar'] = $value['kamarruangan_nokamar'] . '-' . $value['no_tempattidur'];
                $tmp['Hari Rawat'] = $hariRawat;
                $tmp['Tanggal Pindah'] = isset($value['tgl_pindahkamar']) ? date('d M Y H:i:s', strtotime($value['tgl_pindahkamar'])) : '-';
                $tmp['Rencana Pulang'] = isset($value['rencana_pulang']) ? date('d M Y H:i:s', strtotime($value['rencana_pulang'])) : '-';
                $tmp['Status Pasien'] = $statusPasien;

                $tempRows[] = $tmp;
                $ctr = ($idx+1);
                if (($ctr%100) == 0) {
                    $cache->set($this->unique_str .'-data'. $rowsIndex, $tempRows);
                    $tempRows = [];
                    $rowsIndex++;

                    $progress = round(($ctr / $crs) * 70);
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-excel:'.$this->unique_str,
                        'message' => json_encode([
                            'status' => 'finish',
                            'messageProcess' => 'Mempersiapkan data hasil pencarian: ' . $ctr . '/' . $crs . ' data',
                            'progress' => 10 + $progress
                        ]),
                    ]);

                }
            }
            if (!empty($tempRows)) {
                $cache->set($this->unique_str .'-data'. $rowsIndex, $tempRows);
                $tempRows = [];
                $rowsIndex++;

                $progress = 70;
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:'.$this->unique_str,
                    'message' => json_encode([
                        'status' => 'finish',
                        'messageProcess' => 'Mempersiapkan data hasil pencarian: ' . $crs . '/' . $crs . ' data',
                        'progress' => 10 + $progress
                    ]),
                ]);
            }
            if ($rowsIndex > 0) {
                $cache->set($this->unique_str .'-total-index', $rowsIndex);
                $cache->set($this->unique_str .'-total-data', $crs);
            }
        }

        return json_encode([
            'service' => 'Sirs-ListDataPasienRawatInapExport',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $advanced_filter = isset($request['advanced-filter']) ? $request['advanced-filter'] : [];
        $model = new InfoPasienRanapView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $statusPasien = null;
        $ruanganId = isset($request['ruangan']) ? $request['ruangan'] : null;

        if (!empty($advanced_filter)) {
            if (isset($advanced_filter['tgl_admisi'])) {
                $explodePr = explode(" - ", $advanced_filter['tgl_admisi']);
                if (count($explodePr) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explodePr[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explodePr[1]));
                }
            }

            if (isset($advanced_filter['is_stopakomodasi'])) {
                $statusPasien = $advanced_filter['is_stopakomodasi'];
                unset($advanced_filter['is_stopakomodasi']);
                unset($request['advanced-filter']['is_stopakomodasi']);
            }

            if (isset($advanced_filter['ruangan_id'])) {
                $ruanganId = $advanced_filter['ruangan_id'];
                unset($advanced_filter['ruangan_id']);
                unset($request['advanced-filter']['ruangan_id']);
            }
        }

        $query->andWhere(['not in', 'infopasienri_v.status_ranap', [DocoConstants::STATUS_RANAP_BATAL_RAWAT]]);
        $query->andWhere(['pasienpulang_id' => NULL]);
        $query->andWhere(['between', 'infopasienri_v.tgl_admisi', $start, $end]);
        $query->andWhere(['infopasienri_v.ruangan_id' => $ruanganId]);
        switch ($statusPasien) {
            case 1:
                $query->andWhere(['in', 'infopasienri_v.jenis_konsul', [DocoConstants::JNS_KNSL_1X, DocoConstants::JNS_KNSL_RB]]);
                $query->andWhere(['infopasienri_v.status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU]);
                break;
            case 2:
                $query->andWhere(['or', ['infopasienri_v.jenis_konsul' => null], ['infopasienri_v.jenis_konsul' => DocoConstants::JNS_KNSL_AR]]);
                $query->orWhere(['and', ['IN', 'infopasienri_v.jenis_konsul', [DocoConstants::JNS_KNSL_1X, DocoConstants::JNS_KNSL_RB]], ['not', ['infopasienri_v.status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU]]]);
                break;
            case 3:
                $query->andWhere(['or', ['infopasienri_v.is_pasientitipan_pk' => true], ['infopasienri_v.is_pasientitipan' => true]]);
                $query->andWhere(['infopasienri_v.is_stoppasientitipan' => false]);
                break;
            case 4:
                $query->andWhere(['is_stopakomodasi' => true]);
                break;
            default:
        }

        if (!empty($filter['order'])) {
            $query->orderBy($filter['order']);
        }

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Populate filter',
                'progress' => 6
            ]),
        ]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}