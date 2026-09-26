<?php

namespace Doco\Traits;

use Doco\models\CaraBayar;
use Doco\models\JenisKasusPenyakit;
use Doco\models\KelasPelayanan;
use Doco\models\Penjamin;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoRestActiveFilter;
use Doco\models\InfoDokterView;
use Doco\models\Pegawai;
use Doco\models\Ruangan;
use Doco\models\WorklistPasien;
use Doco\models\Lookup;
use Doco\models\RuanganPemakai;
use Doco\models\PeranPengguna;
use Yii;

/**
 * Trait of worklist patient
 */
trait WorklistPatientTrait
{
    /**
     * This function will return API datatable
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDatatable()
    {
        $model = new WorklistPasien;
        $limit = Yii::$app->request->get('per-page', 10);
        $page = Yii::$app->request->get('page', 1);
        $order = Yii::$app->request->get('order', null);
        $filter = Yii::$app->request->get('advanced-filter', []);
        $tabType = Yii::$app->request->get('tabType', null);
        $isNosep = Yii::$app->request->get('isNosep', null);
        $isDokter = Yii::$app->request->get('isDokter', false);
        $idRuangan = Yii::$app->request->get('idRuangan', null);
        $modulAccessed = Yii::$app->request->get('modulAccessed', null);
        $userIdentity = Yii::$app->request->get('userIdentity', []);
        $cache = Yii::$app->cache;
        $filteredStatusPeriksaArr = [
            DocoConstants::STATUS_BATAL_PERIKSA,
            DocoConstants::STATUS_RUJUK_RAWAT_INAP,
            DocoConstants::STATUS_PULANG,
            DocoConstants::STATUS_RANAP_BATAL_RAWAT,
            DocoConstants::STATUS_RANAP_PULANG,
            DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,
            DocoConstants::STATUS_PERIKSA_BTL_KUNJ,
            DocoConstants::STATUS_PERIKSA_BTL_KONSUL,
            DocoConstants::STATUS_PERIKSA_BTL_RUJUK_RAWAT_INAP,
            DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES,
            DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK,
            DocoConstants::ST_P_PEN_SDH_OPRS,
            DocoConstants::ST_P_PEN_SDG_OPRS,
            DocoConstants::ST_P_PEN_BTL,
            DocoConstants::ST_P_PEN_BLM_PRKS,
            DocoConstants::ST_P_PEN_AMB_SAMP,
            DocoConstants::ST_P_PEN_SELESAI,
            DocoConstants::ST_P_PEN_PRKS,
        ];
        $filteredStatusPeriksaNamaArr = [
            strtolower(DocoConstants::STATUS_PERIKSA_NAMA_SDH_DIJAWAB)
        ];

        if (!empty($order)) {
            $explodeOrder = explode(" ", $order);
            $orderKey = $explodeOrder[0];
            $orderType = strtolower($explodeOrder[1]);
        }
        $query = WorklistPasien::find()
            ->select([
                'jenis',
                'pendaftaran_id',
                'tgl_pendaftaran',
                'no_pendaftaran',
                'no_rekam_medik',
                'nama_pasien',
                'tanggal_lahir',
                'jk',
                'nama_pegawai',
                'carabayar_id',
                'carabayar_nama',
                'penjamin_id',
                'penjamin_nama',
                'hak_kelas',
                'kelaspelayanan_nama',
                'kelas_tagihan',
                'is_pasientitipan',
                'carabayar_kode_warna',
                'carabayar_warna',
                'status_periksa',
                'status_periksa_nama',
                'is_pasientitipan',
                'is_bayi',
                'is_konsul',
                'is_stoppasientitipan',
                'is_pulang',
                'is_lunas',
                'is_stopakomodasi',
                'pasienmasukpenunjang_id',
                'keterangan_pendaftaran',
                'ruangan_nama',
                'jeniskasuspenyakit_nama',
                'kamarruangan_nokamar',
                'no_tempattidur',
                'kettempattidur_nama',
                'status_kamar',
                'tgl_pindahkamar',
                'rencana_pulang',
                'ruangan_id',
                'instalasi_id',
                'antrian_id',
                'no_antrian',
                'is_isisoap',
                'peg_create_nama',
                'konsulpoli_id',
                'alergi',
                'no_telepon_pasien',
                'no_mobile_pasien',
                'konsulpoli_dokter_nama',
                'pegawai_id',
                'riwayat_alergi',
                'catatanpenting_pasien',
                'status_skrining',
                'nosep',
                'rencanakontrol_id'
            ])
            ->andWhere(['IS NOT', 'status_periksa', null]);
        
        $employeCache = $cache->get("employe-".Yii::$app->jwt->user->pegawai_id);
        if(! empty($employeCache)) {
            $groupEmployee = $employeCache;
        } else {
            $groupEmployee = Pegawai::find()
                ->select(['kelompokpegawai_id', 'spesialis_id'])
                ->andWhere(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])
                ->asArray()
                ->one();
            $cache->set("employe-".Yii::$app->jwt->user->pegawai_id, $groupEmployee, 300);
        }

        if(isset($_GET['advanced-filter'])){
            $uniqueString = json_encode($_GET['advanced-filter']);
            $uniqueString .= '-pegawai-'.Yii::$app->jwt->user->pegawai_id . $tabType;
        }

        // tgl_pendaftaran
        if (isset($filter['status_periksa_nama']) && $filter['status_periksa_nama'] == '- Semua -') {
            $filter['status_periksa_nama'] = '';
            $_GET['advanced-filter']['status_periksa_nama'] = '';
        }
        if (isset($filter['status_periksa_nama']) && $filter['status_periksa_nama'] != '') {
            if ($filter['status_periksa_nama'] != '0'){
                $query->andWhere([
                    '=', 'LOWER(status_periksa_nama)', strtolower($filter['status_periksa_nama'])
                ]);
            }
            unset($_GET['advanced-filter']['status_periksa_nama']);
        } else {
            if($isDokter) {
                if(!empty($tabType) && strtoupper($tabType) == 'OL') {
                    if (($key = array_search(DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES, $filteredStatusPeriksaArr)) !== false) {
                        unset($filteredStatusPeriksaArr[$key]);
                    }
                    array_push($filteredStatusPeriksaArr, DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI);
                }
                $query->andWhere(['NOT IN', 'status_periksa', $filteredStatusPeriksaArr]);
                $query->andWhere([
                    'NOT IN',
                    'LOWER(status_periksa_nama)',
                    $filteredStatusPeriksaNamaArr
                ]);
            } else {
                array_push($filteredStatusPeriksaArr, DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI);
                $query->andWhere([
                    'NOT IN',
                    'status_periksa',
                    $filteredStatusPeriksaArr
                ]);
            }
        }

        if (!empty($groupEmployee) && $groupEmployee['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_DOKTER && !empty($groupEmployee['spesialis_id'])) {
            $query->andWhere("case when jenis IN ('RJ', 'RI', 'MCU', 'OL') then pegawai_id=" . Yii::$app->jwt->user->pegawai_id . " ELSE true END");

            if(isset($_GET['advanced-filter']['pegawai_id'])) {
                if(Yii::$app->jwt->user->pegawai_id == $_GET['advanced-filter']['pegawai_id']) {
                    unset($_GET['advanced-filter']['pegawai_id']);
                }
            }
        }

        if (!empty($groupEmployee) && $groupEmployee['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_DOKTER && empty($groupEmployee['spesialis_id'])) {
            if (!empty($tabType) && $tabType == 'ri') {
                $query->andWhere(['is_konsul' => false]);
            }
        }

        if (!empty($groupEmployee) && $groupEmployee['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
            if (!empty($tabType) && $tabType !== 'mcu') {
                $query->andWhere([
                    'is_konsul' => false,
                    'konsulpoli_id' => NULL,
                ]);
            }

        }
        if (!empty($tabType) && strtoupper($tabType) !== 'ALL') {
            $query->andWhere(['jenis' => strtoupper($tabType)]);
        }

        if (isset($filter['tgl_pendaftaran']) && !empty($filter['tgl_pendaftaran'])) {
            $explodeDate = explode(' - ', $filter['tgl_pendaftaran']);
            $startDate = date("Y-m-d", strtotime($explodeDate[0]));
            $endDate = date("Y-m-d", strtotime($explodeDate[1]));
            $query->andWhere(['between', new \yii\db\Expression('(tgl_pendaftaran::date)'), $startDate, $endDate]);
        } else {
            if(!empty($tabType) && strtoupper($tabType) == 'OL') {
                $startDate = date('Y-m-d 00:00:00');
                $query->andWhere(['>=', 'tgl_pendaftaran', $startDate]);
            } else {
                $startDate = date("Y-m-d", strtotime('-30 days'));
                $endDate = date("Y-m-d", strtotime('now'));
                $query->andWhere(['between', new \yii\db\Expression('(tgl_pendaftaran::date)'), $startDate, $endDate]);
            }
        }

        if (isset($filter['no_pendaftaran']) && !empty($filter['no_pendaftaran'])) {
                $noPendaftaran = $filter['no_pendaftaran'];
            $query->andWhere(['no_pendaftaran' => $noPendaftaran]);
            unset($_GET['advanced-filter']['no_pendaftaran']);
        }

        $ruanganCache = $cache->get("ruangan-".Yii::$app->jwt->user->pegawai_id);
        if (! empty($ruanganCache)) {
            $ruanganPegawai = $ruanganCache;
        } else {
            $ruanganPegawai = RuanganPemakai::find()->select(['ruangan_id'])
                ->where(['loginpemakai_id'=>Yii::$app->jwt->user->loginpemakai_id, 'is_active'=>true, 'is_deleted'=>false])
                ->column();
            $cache->set("ruangan-".Yii::$app->jwt->user->pegawai_id, $ruanganPegawai, 300);
        }

        if(!empty($ruanganPegawai)) {
            $query->andWhere([
                'ruangan_id'=>$ruanganPegawai
            ]);
        }

        $arr_ruangan = array();
        if(isset($filter['ruangan_id']) && !empty($filter['ruangan_id'])) {
            $filterRuangan = $filter['ruangan_id'];

            for($i = 0; $i < count($filterRuangan); $i++) {
                if(is_numeric($filterRuangan[$i])) {
                    $arr_ruangan[] = $filterRuangan[$i];
                }
            }

            if(count($arr_ruangan) > 0) {
                $query->andWhere(['ruangan_id' => $arr_ruangan]);
            }
            unset($_GET['advanced-filter']['ruangan_id']);
        }

        if ($isNosep !=  'null') {
            if ($isNosep == 'false') {
                $query->andWhere(['nosep' => null]);
                $query->andWhere(['carabayar_id' => 6]);
            }
    
            if ($isNosep == 'true') {
                $query->andWhere(['NOT', ['nosep' => null]]);
            }
        }
        
        if(isset($uniqueString)) {
            $totalRecord = $cache->get($uniqueString);
            if($totalRecord == false && $totalRecord !== 0) {
                // $totalRecord = $query->count();
                $totalRecord = 200; // set sementara ke 200 untuk mengurangi beban RPP-1411
                $cache->set($uniqueString, $totalRecord, 120);
            }
        } else {
            // $totalRecord = $query->count();
              $totalRecord = 200; // set sementara ke 200 untuk mengurangi beban RPP-1411
        }

        $caraBayarOrderingGroup = [DocoConstants::GROUP_UMUM, DocoConstants::GROUP_JAMINAN, DocoConstants::GROUP_BPJS];
        $carabayarCache = $cache->get("carabayar-".Yii::$app->jwt->user->pegawai_id);
        if(! empty($carabayarCache)) {
            $resultCaraBayar = $carabayarCache;
        } else {
            $resultCaraBayar = CaraBayar::find()
            ->select([
                'carabayar_id',
                'carabayar_nama',
                'groupcarabayar_id'
            ])
            ->andWhere(["is_active" => true, "is_deleted" => false])
            ->orderBy([
                new \yii\db\Expression("array_position(ARRAY[".implode(',', $caraBayarOrderingGroup)."]::INTEGER[], groupcarabayar_id)"),
                new \yii\db\Expression("carabayar_id ASC")
            ])->asArray()->all();
            $cache->set("carabayar-".Yii::$app->jwt->user->pegawai_id, $resultCaraBayar, 300);
        }

        $arrCaraBayar = array();
        foreach ($resultCaraBayar as $valueCaraBayar) {
            $arrCaraBayar[] = $valueCaraBayar['carabayar_id'];
        }

        $query->offset(($page - 1) * $limit)->limit($limit);
        if (isset($orderKey) && isset($orderType)) {
            $statusOrdering = array_merge(DocoConstants::UrutanStatusAntrian, DocoConstants::UrutanStatusPeriksa);
            $konfigUrutan = self::getKonfigWorklistUrutanPeriksa();
            $customOrders = $this->setCustomOrders($order);
            $defaultOrders = [
                new \yii\db\Expression("array_position(ARRAY[" . implode(',', $statusOrdering) . "]::INTEGER[], status_periksa)"),
                new \yii\db\Expression("array_position(ARRAY[" . implode(',', $arrCaraBayar) . "]::INTEGER[], carabayar_id)"),
            ];
            
            if ($konfigUrutan == 'true') {
                array_unshift($defaultOrders, new \yii\db\Expression($customOrders));
            } else {
                array_push($defaultOrders, new \yii\db\Expression($customOrders));
            }
            $query->orderBy($defaultOrders);
        }
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query); //matiin ini untuk bisa set ordering manual untuk semua instalasi
        
        if (strtoupper($tabType) == 'RI') { // set order by null untuk tab RI RPP-1411
            $query->orderBy('');
        }
        // $query = DocoRestActiveFilter::advancedFilter($model, $query); // nyalakan ini untuk bisa set ordering manual
        if (empty($tabType) || !isset($filter['ruangan_id']) || (isset($filter['ruangan_id']) && empty($filter['ruangan_id'])) ){
            $query->orderBy('');
        }
        $record = $query->asArray()->all();

        $peranCache = $cache->get("peran-".Yii::$app->jwt->user->pegawai_id);
        if(! empty($peranCache)) {
            $peranPengguna = $peranCache;
        } else {
            $peranPengguna = PeranPengguna::find()->select([
                'peranpengguna_menu'
            ])->where(['in','peranpenggunanama', $userIdentity['roles']])
            ->andWhere(['like','peranpengguna_menu','batal-stop-akomodasi'])
            ->asArray()->one();
            $cache->set("peran-".Yii::$app->jwt->user->pegawai_id, $peranPengguna, 300);
        }

        $batalStopAkomodasi = isset($peranPengguna['peranpengguna_menu']) ? false : true;

        return [
            'recordsFiltered' => $totalRecord,
            'recordsTotal' => $totalRecord,
            'data' => $record,
            'batalStopAkomodasi' => $batalStopAkomodasi,
        ];
    }

    /**
     * This function will return all of source data for worklist
     *
     * @param String $type
     * @param String $terms
     * @param Int $page [optional]
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFilters()
    {
        $types = Yii::$app->request->get('types', []);
        if (!is_array($types)) {
            $types = [$types];
        }
        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $isDokter = Yii::$app->request->get('isDokter', false);
        $additionalPayload = Yii::$app->request->get('additionalPayload', []);
        $limit = Yii::$app->request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $lookupData = [];
        $resultData = [];
        $cache = Yii::$app->cache;
        foreach ($types as $eachType) {
            $isInfinityScroll = false;
            $result = null;
            switch ($eachType) {
                case 'dokter':
                    $isInfinityScroll = true;
                    $result = InfoDokterView::find()
                        ->select(['pegawai_id as id', 'nama_pegawai as text']);
                    if (!empty($term)) {
                        $result->andWhere(['like', 'LOWER(nama_pegawai)', $term]);
                    }
                    break;
                case 'kelas_bpjs':
                    $lookupData[] = 'kelas_bpjs';
                    break;
                case 'carabayar':
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_carabayar', function ($cache) {
                        return CaraBayar::find()->select(['carabayar_id as id', 'carabayar_nama as text', 'carabayar_kode_warna'])->andWhere(['is_active' => true]);
                    }, 300);
                    break;
                case 'kelaspelayanan':
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_kelaspelayanan', function ($cache) {
                        return KelasPelayanan::find()->select(['kelaspelayanan_id as id', 'kelaspelayanan_nama as text'])->andWhere(['is_active' => true]);
                    }, 300);
                    break;
                case 'penjamin':
                    $result = Penjamin::find()->select(['carabayar_id', 'penjamin_id as id', 'penjamin_nama as text'])->andWhere(['is_active' => true]);
                    if (isset($additionalPayload['carabayar_id'])) {
                        $result->andWhere(['carabayar_id' => $additionalPayload['carabayar_id']]);
                    }
                    break;
                case 'jeniskasuspenyakit':
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_jeniskasuspenyakit', function ($cache) {
                        return JenisKasusPenyakit::find()->select(['jeniskasuspenyakit_id as id', 'jeniskasuspenyakit_nama as text'])->andWhere(['is_active' => true])->orderBy(['jeniskasuspenyakit_urutan' => SORT_ASC]);
                    }, 300);
                    break;
                case 'ruangan':
                    $ruanganPegawai = RuanganPemakai::find()->select(['ruangan_id'])
                        ->where(['loginpemakai_id'=>Yii::$app->jwt->user->loginpemakai_id, 'is_active'=>true, 'is_deleted'=>false])
                        ->column();

                    $result = Ruangan::find()->select(['ruangan_id as id', 'ruangan_nama as text', 'instalasi_id'])
                        ->andWhere(['in', 'instalasi_id', [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI, DocoConstants::INST_ID_BEDAH, DocoConstants::INST_ID_MCU]]);
                    if(!empty($ruanganPegawai)) {
                        $result->andWhere([
                            'ruangan_id'=>$ruanganPegawai
                        ]);
                    }
                    break;
                case 'status_periksa':
                    $statusPeriksaId = array_merge(DocoConstants::UrutanStatusAntrian, DocoConstants::UrutanStatusPeriksa);
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_lookupm:id#'.implode(',', array_unique($statusPeriksaId)), function ($cache) use($statusPeriksaId) {
                        return Lookup::find()->select([
                            'distinct(INITCAP(lookup_name)) as text',
                            'INITCAP(lookup_name) as id',
                        ])->where([
                            'lookup_id' => $statusPeriksaId
                        ]);
                    }, 300);
                    break;
                case 'status_periksa_rajal':
                    $statusPeriksaId = array_merge(DocoConstants::UrutanStatusAntrianRajal, DocoConstants::UrutanStatusPeriksaRajal);
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_lookupm:id#'.implode(',', array_unique($statusPeriksaId)), function ($cache) use($statusPeriksaId) {
                        return Lookup::find()->select([
                            'distinct(INITCAP(lookup_name)) as text',
                            'INITCAP(lookup_name) as id',
                            'lookup_id'
                        ])->where([
                            'lookup_id' => $statusPeriksaId
                        ]);
                    }, 300);
                    break;
                case 'status_periksa_ranap':
                    $statusPeriksaId = array_merge(DocoConstants::UrutanStatusAntrianRanap, DocoConstants::UrutanStatusPeriksaRanap);
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_lookupm:id#'.implode(',', array_unique($statusPeriksaId)), function ($cache) use($statusPeriksaId) {
                        return Lookup::find()->select([
                            'distinct(INITCAP(lookup_name)) as text',
                            'INITCAP(lookup_name) as id',
                            'lookup_id'
                        ])->where([
                            'lookup_id' => $statusPeriksaId
                        ]);
                    }, 300);
                    break;
                case 'status_periksa_igd':
                    $statusPeriksaId = array_merge(DocoConstants::UrutanStatusAntrianIgd, DocoConstants::UrutanStatusPeriksaIgd);
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_lookupm:id#'.implode(',', array_unique($statusPeriksaId)), function ($cache) use($statusPeriksaId) {
                        return Lookup::find()->select([
                            'distinct(INITCAP(lookup_name)) as text',
                            'INITCAP(lookup_name) as id',
                            'lookup_id'
                        ])->where([
                            'lookup_id' => $statusPeriksaId
                        ]);
                    }, 300);
                    break;
                case 'status_periksa_ot':
                    $statusPeriksaId = array_merge(DocoConstants::UrutanStatusAntrianOt, DocoConstants::UrutanStatusPeriksaOt);
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_lookupm:id#'.implode(',', array_unique($statusPeriksaId)), function ($cache) use($statusPeriksaId) {
                        return Lookup::find()->select([
                            'distinct(INITCAP(lookup_name)) as text',
                            'INITCAP(lookup_name) as id',
                        ])->where([
                            'lookup_id' => $statusPeriksaId
                        ]);
                    }, 300);
                    break;
                case 'status_periksa_mcu':
                    $statusPeriksaId = DocoConstants::UrutanStatusPeriksaMcu;
                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_lookupm:id#'.implode(',', array_unique($statusPeriksaId)), function ($cache) use($statusPeriksaId) {
                        return Lookup::find()->select([
                            'distinct(INITCAP(lookup_name)) as text',
                            'INITCAP(lookup_name) as id',
                        ])->where([
                            'lookup_id' => $statusPeriksaId
                        ]);
                    }, 300);
                    break;
                case 'status_periksa_ol':
                    if ($isDokter) {
                        $statusPeriksaId = array_merge(DocoConstants::UrutanStatusPeriksaOlDokter);
                    }else{
                        $statusPeriksaId = array_merge(DocoConstants::UrutanStatusAntrianOl, DocoConstants::UrutanStatusPeriksaOl);
                    }

                    $result = Yii::$app->cache->getOrSet('worklist-pelayanan_lookupm:id#'.implode(',', array_unique($statusPeriksaId)), function ($cache) use($statusPeriksaId) {
                        return Lookup::find()->select([
                            'distinct(INITCAP(lookup_name)) as text',
                            'INITCAP(lookup_name) as id',
                        ])->where([
                            'lookup_id' => $statusPeriksaId
                        ]);
                    }, 300);
                    break;
            }
            if (!empty($result)) {
                if ($isInfinityScroll) {
                    $result->limit(($limit + 1))->offset($limit * ($page - 1));
                }
                $resultData[$eachType] = $result->asArray()->all();
            } else {
                $resultData[$eachType] = [];
            }
        }
        if (!empty($lookupData)) {
            $lookupData = $this->lookup_type->dataByTypes($lookupData);
        }
        return array_merge($lookupData, $resultData);
    }

    /* function get konfig dari lookuptranssaksi_m
    *  Bisa diimprove lagi biar cuma 1x select bisa ngambil beberapa kode transaksi
    *  tinggal sesuain aja codenya
    */
    public function actionGetWorklistConfig()
    {
        $config['konfig_worklist_semua_pasien_hide_dokter'] = (new DocoConstansId)->actionGetAdditional('konfig_worklist_semua_pasien_hide_dokter');
        return $config;
    }

    public function actionGetKonfigWorklistFilter(){
        $kode = Yii::$app->request->get('kode');
        $config = self::getKonfig($kode);
        return $config;
    }

    private function getKonfigWorklistUrutanPeriksa(){
        $config = self::getKonfig('konfig_worklist_filter_urutan_periksa');
        return $config;
    }

    private function getKonfig($kode){
        $data = (new DocoConstansId)->actionGetAdditional($kode);
        return $data;
    }
    
    /*
    *setCustomOrders untuk custom filter ordering, ketika order lebih dari 2 dan terdapat tgl_pendaftaran,
    * maka tanggal pendaftaran akan dicasting dari datetime ke date, dan penambahan nulls last untuk kebutuhan kosong akan disimpan diakhir 
    */
    private function setCustomOrders($orders) 
    {
        // kudu di explode, karena yii2 request nangkap order dalam bentuk string
        $orders = explode(', ',$orders);
        foreach ($orders as $key => $order) {
            if (count($orders) > 1 && strpos(strtolower($order), 'tgl_pendaftaran') !== FALSE) {
                $order_explode = explode(' ', $order);
                $order_explode[0] .= '::date'; //tambah casting date ketika order lebih dari 2 dan terdapat tgl_pendaftaran (kebutuhan untuk order tanggalnya saja tidak dengan jam dan menit)
                $orders[$key] = implode(' ', $order_explode);
            }
            $orders[$key] .= ' NULLS LAST'; // untuk ngeset yang null bakal jadi terakhir
        }
        $orders = implode(',', $orders);
        return $orders;
    }
}
