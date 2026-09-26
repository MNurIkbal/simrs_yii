<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Sirs\Models\SensuspasienranapR;
use Integrasi\Service\Sirs\Models\SensusPasienRanapRecalculateView;
use Integrasi\Service\Sirs\Models\MasukKamar;
use Integrasi\Service\Sirs\Models\PindahKamar;

class RecalculateSensusRanap extends \Integrasi\Contracts\DocoImplement
{
    /* var Get pasien sedang rawat untuk di hitung sebagai pasien awal*/
    public $is_baca_pasiensedangrawat;

    public function execute()
    {
        $isRecalculateAll = isset($this->attributes['isRecalculateAll']) ? $this->attributes['isRecalculateAll'] : false;
        $dateStart = isset($this->attributes['date_start']) ? date('Y-m-d', strtotime($this->attributes['date_start'])): null;
        $konfig = Cache::getKonfigSystem();
        $this->is_baca_pasiensedangrawat = isset($konfig['is_baca_pasiensedangrawat']) ? $konfig['is_baca_pasiensedangrawat'] : null;
        $model = new SensuspasienranapR;
        $data = $tmpData = $baseData = $updatedData = [];
        $isKonfig = false;
        $isDaily = true;

        if(!is_null($konfig['set_tgl_sensus']) && !$dateStart) {
            $dateStart = $konfig['set_tgl_sensus'];
            $isDaily = false;
            $isKonfig = true;
        }

        $pindahKamar = $this->generateCasePindahKamar();

        $tmpData = $this->generateDataAwal($dateStart, $isKonfig, $isDaily);
        // $dataRekap = $this->getDataRekapan($dateStart);
        $queryRekap = SensusPasienRanapRecalculateView::find();
        if ($isDaily) {
            $queryRekap->where(['>=', 'tgl_sensus', $dateStart]);
        }
        $dataRekap =  $queryRekap->asArray()->all();
        $getPasienMasuk =  $model->getDataPasienMasuk($dateStart);
        // return json_encode([$getPasienMasuk]);
        $getPasienPindahan = $model->getDataPasienPindahan($dateStart, $isKonfig);
        $getPasienKeluar = $model->getDataPasienKeluarHidup($dateStart,$isKonfig);
        $getPasienKeluarPindahan = $model->getDataPasienKeluarPindahan($dateStart, $isKonfig);
        $getPasienMeninggalKur48 = $model->getDataPasienMeninggalKur48($dateStart);
        $getPasienMeninggalLeb48 = $model->getDataPasienMeninggalLeb48($dateStart);

        if ($isDaily) {
            $baseData = $this->generateBaseData($dateStart);
            $lastId = $this->getLastIdSensus();
            $lastId++;
        }

        foreach($dataRekap as $key => $value) {
            $tgl = $value['tgl_sensus'];
            $ruanganId = $value['ruangan_id'];
            $kp = $value['kelaspelayanan_id'];
            $newDataMasuk = $newDataPindahan = $newDataKeluar= $newDataKeluarPindahan= $newDataMeninggalKur48= $newDataMeninggalLeb48 = $pasienAkhir = $pasienSebelumnya = 0;
            if(isset($getPasienMasuk[$ruanganId][$kp][$tgl])) {
                // $data[$ruanganId][$kp][$tgl] = $value['pasien_masuk'] + $getPasienMasuk[$ruanganId][$kp][$tgl]['pasien_masuk'];
                $newDataMasuk = $getPasienMasuk[$ruanganId][$kp][$tgl]['pasien_masuk'];
            }
            if(isset($getPasienPindahan[$ruanganId][$kp][$tgl])) {
                $newDataPindahan = $getPasienPindahan[$ruanganId][$kp][$tgl]['pasien_pindahan'];
            }
            if(isset($getPasienKeluar[$ruanganId][$kp][$tgl])) {
                $newDataKeluar = $getPasienKeluar[$ruanganId][$kp][$tgl]['pasien_keluarhidup'];
            }
            if(isset($getPasienKeluarPindahan[$ruanganId][$kp][$tgl])) {
                $newDataKeluarPindahan = $getPasienKeluarPindahan[$ruanganId][$kp][$tgl]['pasien_keluardipindahkan'];
            }
            if(isset($getPasienMeninggalKur48[$ruanganId][$kp][$tgl])) {
                $newDataMeninggalKur48 = $getPasienMeninggalKur48[$ruanganId][$kp][$tgl]['pasien_keluarmeninggalkur48'];
            }
            if(isset($getPasienMeninggalLeb48[$ruanganId][$kp][$tgl])) {
                $newDataMeninggalLeb48 = $getPasienMeninggalLeb48[$ruanganId][$kp][$tgl]['pasien_keluarmeninggalleb48'];
            }

            $getPasienSebelumnya = $this->searchLastValueByKey($tmpData,$ruanganId, $kp);

            if(!empty($getPasienSebelumnya)) {
                $pasienSebelumnya = $getPasienSebelumnya['pasien_akhir'];
            }

            if ($isDaily) {
                if (isset($baseData[$ruanganId][$kp][$tgl]['pasien_awal'])) {
                    $pasienSebelumnya = $baseData[$ruanganId][$kp][$tgl]['pasien_awal'];
                }
                $getPasienSebelumnya = $this->searchLastValueByKey($baseData, $ruanganId, $kp, $tgl);
                if(!empty($getPasienSebelumnya)) {
                    $pasienSebelumnya = $getPasienSebelumnya['pasien_akhir'];
                }
            }
            
            $pasienAkhir = ($pasienSebelumnya + $newDataMasuk + $newDataPindahan) - ($newDataKeluar + $newDataKeluarPindahan + $newDataMeninggalKur48 + $newDataMeninggalLeb48);

            if(!isset($tmpData[$ruanganId][$kp][$tgl])) {
                $tmpData[$ruanganId][$kp][$tgl] = [
                    'tgl_sensus' => $tgl,
                    'kelaspelayanan_id' => $kp,
                    'ruangan_id' => $ruanganId,
                    'pasien_awal' => $pasienSebelumnya,
                    'pasien_masuk' => $newDataMasuk,
                    'pasien_pindahan' => $newDataPindahan,
                    'pasien_keluarhidup' => $newDataKeluar,
                    'pasien_keluardipindahkan' => $newDataKeluarPindahan,
                    'pasien_keluarmeninggalkur48' => $newDataMeninggalKur48,
                    'pasien_keluarmeninggalleb48' => $newDataMeninggalLeb48,
                    'pasien_akhir' => $pasienAkhir
                ];
            }
        }

        if($tmpData) {
            $i = 1;
            foreach($tmpData as $keyRuangan) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $value) {
                            $data[] = [
                                'id' => $i,
                                'tgl_sensus' => $value['tgl_sensus'],
                                'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                                'ruangan_id' => $value['ruangan_id'],
                                'pasien_awal' => $value['pasien_awal'],
                                'pasien_masuk' => $value['pasien_masuk'],
                                'pasien_pindahan' => $value['pasien_pindahan'],
                                'pasien_keluarhidup' => $value['pasien_keluarhidup'],
                                'pasien_keluardipindahkan' => $value['pasien_keluardipindahkan'],
                                'pasien_keluarmeninggalkur48' => $value['pasien_keluarmeninggalkur48'],
                                'pasien_keluarmeninggalleb48' => $value['pasien_keluarmeninggalleb48'],
                                'pasien_akhir' =>  $value['pasien_akhir']
                            ];
                        $i++;

                        if ($isDaily) {
                            $tgl = $value['tgl_sensus'];
                            $ruanganId = $value['ruangan_id'];
                            $kp = $value['kelaspelayanan_id'];
                            if (isset($baseData[$ruanganId][$kp][$tgl]['id'])) {
                                $id = $baseData[$ruanganId][$kp][$tgl]['id'];
                            } else {
                                $id = $lastId;
                                $lastId++;
                            }

                            $baseData[$ruanganId][$kp][$tgl] = [
                                'id' => $id,
                                'tgl_sensus' => $value['tgl_sensus'],
                                'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                                'ruangan_id' => $value['ruangan_id'],
                                'pasien_awal' => $value['pasien_awal'],
                                'pasien_masuk' => $value['pasien_masuk'],
                                'pasien_pindahan' => $value['pasien_pindahan'],
                                'pasien_keluarhidup' => $value['pasien_keluarhidup'],
                                'pasien_keluardipindahkan' => $value['pasien_keluardipindahkan'],
                                'pasien_keluarmeninggalkur48' => $value['pasien_keluarmeninggalkur48'],
                                'pasien_keluarmeninggalleb48' => $value['pasien_keluarmeninggalleb48'],
                                'pasien_akhir' =>  $value['pasien_akhir']
                            ];
                        }
                    }
                }
            }
        }

        if ($isDaily) {
            if ($baseData) {
                $updatedData = $this->generateDailyData($baseData);
                $lastNumber = $lastId - 1;
            }
            if ($updatedData) {
                $this->insertData($updatedData, $lastNumber);
            }
        } else {
            if ($data) {
                $lastNumber = $i - 1;
                $this->insertData($data, $lastNumber);
            }
        }

        return json_encode([
            'service' => 'Sirs-recalculate-sensus-ranap',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function insertData($data, $lastNumber) {
        Yii::$app->db->createCommand()->truncateTable('sensuspasienranap_r')->execute();
        SensuspasienranapR::batchInsert($data, false);
        $sequence = new Expression("SELECT setval('public.sensuspasienranap_r_id_seq', {$lastNumber}, TRUE);");
        Yii::$app->db->createCommand($sequence)->execute();
    }

    /**
     * @method select all tanggal sensus
     *  ambil tanggal nya untuk base looping semua data sensus
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    private function getDataRekapan(string $date)
    {
        $query = Yii::$app->db->createCommand("
            SELECT 
                tgl_sensus,ruangan_id,kelaspelayanan_id
            FROM sensuspasienranap_r 
            WHERE tgl_sensus::date > '{$date}'
            ORDER BY tgl_sensus
        ")->queryAll();

        return $query;
    }

    /**
     * @method generate data awal
     *  set data sensus di tanggal konfignya untuk dibuat base array nya
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    private function generateDataAwal(string $dateStart, bool $iskonfig, bool $isDaily)
    {
        $model = new SensuspasienranapR;
        $pasienAwal = [];
        $pasienMasuk = $model->getDataPasienMasuk($dateStart, $iskonfig, $isDaily);
        $getPasienPindahan = $model->getDataPasienPindahan($dateStart, $iskonfig, true);
        $getPasienKeluar = $model->getDataPasienKeluarHidup($dateStart,$iskonfig, true);
        $getPasienKeluarPindahan = $model->getDataPasienKeluarPindahan($dateStart, $iskonfig, true);
        $getPasienMeninggalKur48 = $model->getDataPasienMeninggalKur48($dateStart, $iskonfig, true);
        $getPasienMeninggalLeb48 = $model->getDataPasienMeninggalLeb48($dateStart, $iskonfig, true);

        if($this->is_baca_pasiensedangrawat == true) {
            $pasienAwal = $model->getDataPasienSedangRawat($dateStart) ?: [];
        }

        if(!empty($pasienMasuk)) {
            foreach($pasienMasuk as $keyRuangan ) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $v) {
                        if(isset($pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart])) {
                            $pasienAkhir = $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] + $v['pasien_masuk'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_masuk'] = $v['pasien_masuk'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] = $pasienAkhir;
                        } else {
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart] = [
                                'tgl_sensus' => $dateStart,
                                'kelaspelayanan_id' => $v['kelaspelayanan_id'],
                                'ruangan_id' => $v['ruangan_id'],
                                'pasien_awal' => 0,
                                'pasien_masuk' => $v['pasien_masuk'],
                                'pasien_pindahan' => 0,
                                'pasien_keluarhidup' => 0,
                                'pasien_keluardipindahkan' => 0,
                                'pasien_keluarmeninggalkur48' => 0,
                                'pasien_keluarmeninggalleb48' => 0,
                                'pasien_akhir' => $v['pasien_masuk']
                            ];
                        }
                    }
                }
            }
        }
        if(!empty($getPasienPindahan)) {
            foreach($getPasienPindahan as $keyRuangan ) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $v) {
                        if(isset($pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart])) {
                            $pasienAkhir = $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] + $v['pasien_pindahan'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_pindahan'] = $v['pasien_pindahan'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] = $pasienAkhir;
                        } else {
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart] = [
                                'tgl_sensus' => $dateStart,
                                'kelaspelayanan_id' => $v['kelaspelayanan_id'],
                                'ruangan_id' => $v['ruangan_id'],
                                'pasien_awal' => 0,
                                'pasien_masuk' => 0,
                                'pasien_pindahan' => $v['pasien_pindahan'],
                                'pasien_keluarhidup' => 0,
                                'pasien_keluardipindahkan' => 0,
                                'pasien_keluarmeninggalkur48' => 0,
                                'pasien_keluarmeninggalleb48' => 0,
                                'pasien_akhir' => $v['pasien_pindahan']
                            ];
                        }
                    }
                }
            }
        }
        if(!empty($getPasienKeluar)) {
            foreach($getPasienKeluar as $keyRuangan ) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $v) {
                        if(isset($pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart])) {
                            $pasienAkhir = $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] - $v['pasien_keluarhidup'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_keluarhidup'] = $v['pasien_keluarhidup'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] = $pasienAkhir;
                        } else {
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart] = [
                                'tgl_sensus' => $dateStart,
                                'kelaspelayanan_id' => $v['kelaspelayanan_id'],
                                'ruangan_id' => $v['ruangan_id'],
                                'pasien_awal' => 0,
                                'pasien_masuk' => 0,
                                'pasien_pindahan' => 0,
                                'pasien_keluarhidup' => $v['pasien_keluarhidup'],
                                'pasien_keluardipindahkan' => 0,
                                'pasien_keluarmeninggalkur48' => 0,
                                'pasien_keluarmeninggalleb48' => 0,
                                'pasien_akhir' =>  0 - $v['pasien_keluarhidup']
                            ];
                        }
                    }
                }
            }
        }
        if(!empty($getPasienKeluarPindahan)) {
            foreach($getPasienKeluarPindahan as $keyRuangan ) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $v) {
                        if(isset($pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart])) {
                            $pasienAkhir = $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] - $v['pasien_keluardipindahkan'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_keluardipindahkan'] = $v['pasien_keluardipindahkan'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] = $pasienAkhir;
                        } else {
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart] = [
                                'tgl_sensus' => $dateStart,
                                'kelaspelayanan_id' => $v['kelaspelayanan_id'],
                                'ruangan_id' => $v['ruangan_id'],
                                'pasien_awal' => 0,
                                'pasien_masuk' => 0,
                                'pasien_pindahan' => 0,
                                'pasien_keluarhidup' => 0,
                                'pasien_keluardipindahkan' => $v['pasien_keluardipindahkan'],
                                'pasien_keluarmeninggalkur48' => 0,
                                'pasien_keluarmeninggalleb48' => 0,
                                'pasien_akhir' =>  0 - $v['pasien_keluardipindahkan']
                            ];
                        }
                    }
                }
            }
        }
        if(!empty($getPasienMeninggalKur48)) {
            foreach($getPasienMeninggalKur48 as $keyRuangan ) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $v) {
                        if(isset($pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart])) {
                            $pasienAkhir = $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] - $v['pasien_keluarmeninggalkur48'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_keluarmeninggalkur48'] = $v['pasien_keluarmeninggalkur48'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] = $pasienAkhir;
                        } else {
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart] = [
                                'tgl_sensus' => $dateStart,
                                'kelaspelayanan_id' => $v['kelaspelayanan_id'],
                                'ruangan_id' => $v['ruangan_id'],
                                'pasien_awal' => 0,
                                'pasien_masuk' => 0,
                                'pasien_pindahan' => 0,
                                'pasien_keluarhidup' => 0,
                                'pasien_keluardipindahkan' => 0,
                                'pasien_keluarmeninggalkur48' => $v['pasien_keluarmeninggalkur48'],
                                'pasien_keluarmeninggalleb48' => 0,
                                'pasien_akhir' =>  0 - $v['pasien_keluarmeninggalkur48']
                            ];
                        }
                    }
                }
            }
        }
        if(!empty($getPasienMeninggalLeb48)) {
            foreach($getPasienMeninggalLeb48 as $keyRuangan ) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $v) {
                        if(isset($pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart])) {
                            $pasienAkhir = $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] - $v['pasien_keluarmeninggalleb48'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_keluarmeninggalleb48'] = $v['pasien_keluarmeninggalleb48'];
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart]['pasien_akhir'] = $pasienAkhir;
                        } else {
                            $pasienAwal[$v['ruangan_id']][$v['kelaspelayanan_id']][$dateStart] = [
                                'tgl_sensus' => $dateStart,
                                'kelaspelayanan_id' => $v['kelaspelayanan_id'],
                                'ruangan_id' => $v['ruangan_id'],
                                'pasien_awal' => 0,
                                'pasien_masuk' => 0,
                                'pasien_pindahan' => 0,
                                'pasien_keluarhidup' => 0,
                                'pasien_keluardipindahkan' => 0,
                                'pasien_keluarmeninggalkur48' => 0,
                                'pasien_keluarmeninggalleb48' => $v['pasien_keluarmeninggalleb48'],
                                'pasien_akhir' =>  0 - $v['pasien_keluarmeninggalleb48']
                            ];
                        }
                    }
                }
            }
        }

        return $pasienAwal;
    }

    private function searchLastValueByKey($array, $ruanganId, $kpId, $tgl = null)
    {
        $listKeyArray = ArrayHelper::getValue($array, $ruanganId.'.'.$kpId);
        if(is_null($listKeyArray)) {
            return [];
        }
        $keyArray = array_keys($listKeyArray);
        $lastIndex = end($keyArray);
        if ($tgl) {
            foreach ($keyArray as $value) {
                if ($value < $tgl) {
                    $lastIndex = $value;
                }
            }
        }
        return $array[$ruanganId][$kpId][$lastIndex];
    }

    private function generateCasePindahKamar()
    {
        $data = [];
        $pasienadmisi = Yii::$app->db->createCommand("
            select pasienadmisi_id, kelaspelayanan_id, ruangan_id from pasienadmisi_t
        ")->queryAll();

        $masukkamar = Yii::$app->db->createCommand("
            select pasienadmisi_id, kelaspelayanan_id, ruangan_id
                from masukkamar_t 
                where masukkamar_id in (select x.masukkamar_id 
                from (select max(masukkamar_id) as masukkamar_id, pasienadmisi_id 
                from masukkamar_t 
                group by pasienadmisi_id) as x)  
        ")->queryAll();

        foreach ($pasienadmisi as $k => $vAdm) {
            foreach ($masukkamar as $x => $vMsk) {
                if ($vAdm['pasienadmisi_id'] == $vMsk['pasienadmisi_id']){
                    if ($vAdm['kelaspelayanan_id'] != $vMsk['kelaspelayanan_id'] || $vAdm['ruangan_id'] != $vMsk['ruangan_id']){
                        $data[] = [
                            'pasienadmisi_id' => $vAdm['pasienadmisi_id']
                        ];
                    }
                }
            }
        }

        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        if (!empty($data)){
            foreach ($data as $key => $value) {
                $getKamar  = Yii::$app->db->createCommand("
                    select *
                        from masukkamar_t 
                        where masukkamar_id in 
                        (select x.masukkamar_id 
                        from (select max(masukkamar_id) as masukkamar_id, pasienadmisi_id 
                        from masukkamar_t 
                        group by pasienadmisi_id) as x)  
                        and pasienadmisi_id = {$value['pasienadmisi_id']}
                ")->queryAll();
    
                $getpasienadmisi = Yii::$app->db->createCommand("
                    select * from pasienadmisi_t where pasienadmisi_id = {$value['pasienadmisi_id']}
                ")->queryAll();
    
                if (isset($getKamar[0]['tgl_keluarkamar'])){
                    $modelMasukKamar = new MasukKamar;
                    $modelMasukKamar->ruangan_id = $getpasienadmisi[0]['ruangan_id'];
                    $modelMasukKamar->carabayar_id = $getKamar[0]['carabayar_id'];
                    $modelMasukKamar->pasienadmisi_id = $getKamar[0]['pasienadmisi_id'];
                    $modelMasukKamar->penjamin_id = $getKamar[0]['penjamin_id'];
                    $modelMasukKamar->kelaspelayanan_id = $getpasienadmisi[0]['kelaspelayanan_id'];
                    $modelMasukKamar->kamartempattidur_id = $getKamar[0]['kamartempattidur_id'];
                    $modelMasukKamar->kamarruangan_id = $getKamar[0]['kamarruangan_id'];
                    $modelMasukKamar->tgl_masukkamar = $getKamar[0]['tgl_keluarkamar'];
                    $modelMasukKamar->jam_masukkamar = date('H:i:s', strtotime($getKamar[0]['tgl_keluarkamar']));
                    $modelMasukKamar->tgl_keluarkamar = $getKamar[0]['tgl_keluarkamar'];
                    $modelMasukKamar->jam_keluarkamar = $getKamar[0]['jam_keluarkamar'];
                    $modelMasukKamar->additional_data = "insert recalculate";
                    $modelMasukKamar->save();
        
                    $modelPindahKamar = new PindahKamar;
                    $modelPindahKamar->kamartempattidur_id = $getpasienadmisi[0]['kamartempattidur_id'];
                    $modelPindahKamar->pendaftaran_id = $getpasienadmisi[0]['pendaftaran_id'];
                    $modelPindahKamar->kamarruangan_id = $getpasienadmisi[0]['kamarruangan_id'];
                    $modelPindahKamar->pegawai_id = $getpasienadmisi[0]['pegawai_id'];
                    $modelPindahKamar->carabayar_id = $getpasienadmisi[0]['carabayar_id'];
                    $modelPindahKamar->ruangan_id = $getpasienadmisi[0]['ruangan_id'];
                    $modelPindahKamar->penjamin_id = $getpasienadmisi[0]['penjamin_id'];
                    $modelPindahKamar->pasienadmisi_id = $getpasienadmisi[0]['pasienadmisi_id'];
                    $modelPindahKamar->kelaspelayanan_id = $getpasienadmisi[0]['kelaspelayanan_id'];
                    $modelPindahKamar->pasien_id = $getpasienadmisi[0]['pasien_id'];
                    $modelPindahKamar->tgl_pindahkamar = $getKamar[0]['tgl_keluarkamar'];
                    $modelPindahKamar->jam_pindahkamar = date('H:i:s', strtotime($getKamar[0]['tgl_keluarkamar']));
                    $modelPindahKamar->additional_data = "insert recalculate";
                    $modelPindahKamar->save();
        
                    $set_masukkamar = MasukKamar::updateAll([
                        'pindahkamar_id' => $modelPindahKamar->pindahkamar_id,
                        'jam_keluarkamar' => date('H:i:s', strtotime($getKamar[0]['tgl_keluarkamar']))
                    ], ['in', 'masukkamar_id', $getKamar[0]['masukkamar_id']]);
                    $modelMasukKamar->pindahkamar_id = $modelPindahKamar->pindahkamar_id;
                }
            }
        }
        
        $transaction->commit();
        return $data;
    }

    private function generateBaseData($dateStart)
    {
        $result = [];
        $query = (new \yii\db\Query())
            ->select(['*'])
            ->from('sensuspasienranap_r');
        $data = $query->all();

        if ($data) {
            $result = ArrayHelper::index($data, 'tgl_sensus', [function ($element) {
                return $element['ruangan_id'];
            }, 'kelaspelayanan_id']);
        }

        return $result;
    }

    private function getLastIdSensus()
    {
        $query = (new \yii\db\Query())
            ->select(['id'])
            ->from('sensuspasienranap_r')
            ->orderBy(['id' => SORT_DESC]);
        $data = $query->one();
        return $data['id'];
    }

    private function generateDailyData($baseData)
    {
        $updatedData = [];
        if($baseData) {
            foreach($baseData as $keyRuangan) {
                foreach($keyRuangan as $keyKelas) {
                    foreach($keyKelas as $tgl => $value) {
                        $updatedData[] = [
                            'id' => $value['id'],
                            'tgl_sensus' => $value['tgl_sensus'],
                            'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                            'ruangan_id' => $value['ruangan_id'],
                            'pasien_awal' => $value['pasien_awal'],
                            'pasien_masuk' => $value['pasien_masuk'],
                            'pasien_pindahan' => $value['pasien_pindahan'],
                            'pasien_keluarhidup' => $value['pasien_keluarhidup'],
                            'pasien_keluardipindahkan' => $value['pasien_keluardipindahkan'],
                            'pasien_keluarmeninggalkur48' => $value['pasien_keluarmeninggalkur48'],
                            'pasien_keluarmeninggalleb48' => $value['pasien_keluarmeninggalleb48'],
                            'pasien_akhir' =>  $value['pasien_akhir']
                        ];
                    }
                }
            }
        }

        return $updatedData;
    }
}