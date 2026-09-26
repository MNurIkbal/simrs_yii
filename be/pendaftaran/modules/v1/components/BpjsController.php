<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 13:25
 * @Mod: Ardi Pratama Septiadi
 * @Modif: Setyabudi
 */

namespace app\modules\v1\components;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\Antrian;
use Doco\models\antrian\AntrianjknV;
use yii\helpers\ArrayHelper;
use app\modules\v1\payload\BpjsNewForm;
use app\modules\v1\payload\RujukanForm;

use app\modules\v1\models\Bpjs;
use app\modules\v1\models\BpjsJkn;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\LoginJknR;

class BpjsController extends DocoActiveController
{

    protected function saveBpjs(BpjsNewForm $payloadBpjs, $id, $pasienBaru, $isRanap = false, $isUpdate = false)
    {
        if ($pasienBaru) {
            $getPasien = "
                SELECT
                    pasien_m.no_rekam_medik
                FROM pendaftaran_t
                JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                WHERE pendaftaran_id = {$id}
            ";
            $queryPasien = Yii::$app->db->createCommand($getPasien)->queryOne();
            $payloadBpjs->no_rekam_medik = isset($queryPasien['no_rekam_medik']) ? $queryPasien['no_rekam_medik'] : null;
        }
        $namaUser = Yii::$app->jwt->user->nama_pemakai;
        // Case untuk create sep dari edit pendaftaran
        if ($isUpdate) {
            $modelBpjs = Bpjs::find()
            ->where(['pendaftaran_id' => $id])
            ->orderBy(['bpjs_id'=> SORT_DESC ])
            ->one();
            if (empty($modelBpjs)) {
                $modelBpjs = new Bpjs;
            }
        } else {
            $modelBpjs = new Bpjs;
        }

        $sepNew['noKartu'] = $payloadBpjs->no_kartu;
        $sepNew['tglSep'] = date('Y-m-d', strtotime($payloadBpjs->tanggal_sep));
        $sepNew['jnsPelayanan'] = $payloadBpjs->jenis_pelayanan;
        $sepNew['klsRawat'] = $payloadBpjs->kelas_rawat;
        $sepNew['noMR'] = $payloadBpjs->no_rekam_medik;
        $sepNew['asalRujukan'] = $payloadBpjs->asal_rujukan;
        $sepNew['tglRujukan'] = date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan));
        $sepNew['noRujukan'] = $payloadBpjs->no_rujukan;
        $sepNew['catatan'] = $payloadBpjs->catatan_sep;
        $sepNew['diagAwal'] = $payloadBpjs->diagnosa_awal;
        $sepNew['tujuan'] = isset($payloadBpjs->poli_tujuan) ? $payloadBpjs->poli_tujuan : 'IGD';
        $sepNew['eksekutif'] = !empty($payloadBpjs->poli_eksekutif) ? $payloadBpjs->poli_eksekutif : "0";
        $sepNew['cob'] = !empty($payloadBpjs->cob) ? $payloadBpjs->cob : '0';
        $sepNew['katarak'] = $payloadBpjs->katarak;
        $sepNew['lakaLantas'] = $payloadBpjs->kasus_kecelakaan;
        $sepNew['noSurat'] = isset($payloadBpjs->no_surat_kontrol) ? $payloadBpjs->no_surat_kontrol : '';
        $sepNew['kodeDPJP'] = isset($payloadBpjs->kode_dpjp) ? $payloadBpjs->kode_dpjp : '';
        $sepNew['penjamin'] = '';

        if (!$payloadBpjs->status_suplesi) {
            $penjamin = '';
            if ($payloadBpjs->kasus_kecelakaan == '1') { // kecekalaan lalu lintas dan bukan kecelakaan kerja
                $penjamin = '1';
            }  elseif ($payloadBpjs->kasus_kecelakaan == '2') {
                $penjamin = '1,2';
            } elseif ($payloadBpjs->kasus_kecelakaan == '3') {
                $penjamin = '2,3';
            }
            $sepNew['penjamin'] = $penjamin;
        }

        $sepNew['tglKejadian'] = date('Y-m-d', strtotime($payloadBpjs->tanggal_kejadian));
        $sepNew['no_lp'] = ArrayHelper::getValue($payloadBpjs, 'no_lp');
        $sepNew['keterangan'] = $payloadBpjs->keterangan;
        $sepNew['suplesi'] = $payloadBpjs->status_suplesi;
        $sepNew['noSepSuplesi'] = $payloadBpjs->no_sep_suplesi;
        $sepNew['kdPropinsi'] = $payloadBpjs->kode_provinsi;
        $sepNew['kdKabupaten'] = $payloadBpjs->kode_kabupaten;
        $sepNew['kdKecamatan'] = $payloadBpjs->kode_kecamatan;
        $sepNew['noSurat'] = isset($payloadBpjs->no_surat_kontrol) ? $payloadBpjs->no_surat_kontrol : '';
        $sepNew['kodeDPJP'] = isset($payloadBpjs->kode_dpjp) ? $payloadBpjs->kode_dpjp : '';
        $sepNew['noTelp'] = $payloadBpjs->no_telp;
        $sepNew['user'] = Yii::$app->jwt->user->nama_pemakai;
        $sepNew['ppkRujukan'] = !empty($payloadBpjs->ppk_rujukan) ? $payloadBpjs->ppk_rujukan : $modelBpjs->ppkPelayanan;
        if($payloadBpjs->jenis_pelayanan == 2) {
            $sepNew['kode_dpjp_melayani'] = $payloadBpjs->kode_dpjp_melayani;
            $sepNew['klsRawatNaik'] = "";
            $sepNew['pembiayaan'] = "";
            $sepNew['penanggungJawab'] = "";
        } else {
            $sepNew['kode_dpjp_melayani'] = "";
            $sepNew['klsRawatNaik'] = $payloadBpjs->klsRawatNaik;
            $sepNew['pembiayaan'] = $payloadBpjs->pembiayaan;
            $sepNew['penanggungJawab'] = $payloadBpjs->penanggungJawab;
        }
        $sepNew['tujuanKunj'] = $payloadBpjs->tujuanKunj != null ? $payloadBpjs->tujuanKunj : "0";
        $sepNew['flagProcedure'] = $payloadBpjs->flagProcedure != null ? $payloadBpjs->flagProcedure : "";
        $sepNew['kdPenunjang'] = $payloadBpjs->kdPenunjang != null ? $payloadBpjs->kdPenunjang : "";
        $sepNew['assesmentPel'] = $payloadBpjs->assesmentPel != null ? $payloadBpjs->assesmentPel : "";


        // Yii::error(json_encode($sepNew));

        $modelBpjs->t_sep_new = $sepNew;
        $result = $modelBpjs->createSepNew();
        if (isset($result['metaData']['code'])) {
            if ($result['metaData']['code'] == 200) {
                $noSep = isset($result['response']['sep']['noSep']) ? $result['response']['sep']['noSep'] : null;
                $namaPeserta = isset($result['response']['sep']['peserta']['nama']) ? $result['response']['sep']['peserta']['nama'] : null;
                $modelBpjs->tglsep = $sepNew['tglSep'];
                $modelBpjs->nosep = $noSep;
                $modelBpjs->nokartuasuransi = $sepNew['noKartu'];
                $modelBpjs->tglrujukan = $sepNew['tglRujukan'];
                $modelBpjs->norujukan = $sepNew['noRujukan'];
                $modelBpjs->ppkrujukan = $sepNew['ppkRujukan'];
                $modelBpjs->ppkpelayanan = $modelBpjs->ppkPelayanan;
                $modelBpjs->jnspelayanan = $sepNew['jnsPelayanan'];
                $modelBpjs->catatansep = $sepNew['catatan'];
                $modelBpjs->diagnosaawal = $sepNew['diagAwal'];
                $modelBpjs->politujuan = $sepNew['tujuan'];
                $modelBpjs->klsrawat = $sepNew['klsRawat'];
                $modelBpjs->nama_peserta = $namaPeserta;
                $modelBpjs->lakalantas = $sepNew['lakaLantas'];
                $modelBpjs->asal_rujukan = isset($payloadBpjs->asal_rujukan) ? $payloadBpjs->asal_rujukan : null;
                $modelBpjs->additional_data = json_encode($result['response']);
                $modelBpjs->additional_request = $modelBpjs->t_sep_new;
                $modelBpjs->pendaftaran_id = $id;
                $modelBpjs->kode_dpjp_melayani = $payloadBpjs->kode_dpjp_melayani;
                $modelBpjs->nama_dpjp_melayani = $payloadBpjs->nama_dpjp_melayani;
                $modelBpjs->kode_ppk_perujuk = $payloadBpjs->kode_ppk_perujuk;
                $modelBpjs->nama_ppk_perujuk = $payloadBpjs->nama_ppk_perujuk;
                $modelBpjs->klsrawatnaik = $sepNew['klsRawatNaik'];
                $modelBpjs->pembiayaan = $sepNew['pembiayaan'];
                $modelBpjs->penanggung_jawab = $sepNew['penanggungJawab'];
                $modelBpjs->tujuan_kunj = $sepNew['tujuanKunj'];
                $modelBpjs->flag_procedure = $sepNew['flagProcedure'];
                $modelBpjs->kd_penunjang = $sepNew['kdPenunjang'];
                $modelBpjs->assesment_pel = $sepNew['assesmentPel'];
                $modelBpjs->kode_dpjp_spri = isset($payloadBpjs->kode_dpjp_spri) ? $payloadBpjs->kode_dpjp_spri : null;
                $modelBpjs->nama_dpjp_spri = isset($payloadBpjs->nama_dpjp_spri) ? $payloadBpjs->nama_dpjp_spri : null;
                $modelBpjs->no_surat_kontrol = isset($payloadBpjs->no_surat_kontrol) ? $payloadBpjs->no_surat_kontrol : null;
                $modelBpjs->no_lp_manual = ArrayHelper::getValue($payloadBpjs, 'no_lp');
                $modelBpjs->info_response = isset($payloadBpjs->info_response) ? is_array($payloadBpjs->info_response) ? json_encode($payloadBpjs->info_response) : $payloadBpjs->info_response : null;
                if ($modelBpjs->save(false)) {
                    $bpjsId = $modelBpjs->bpjs_id;
                    $qPendaftaran = "
                        UPDATE pendaftaran_t SET bpjs_id = {$bpjsId}
                        WHERE pendaftaran_id = {$id}
                    ";
                    if ($isRanap) {
                        $qPendaftaran = "
                            UPDATE pasienadmisi_t SET bpjs_id = {$bpjsId}
                            WHERE pendaftaran_id = {$id}
                        ";
                    }
                    $qUpdatePendaftaran = Yii::$app->db->createCommand($qPendaftaran)->execute();
                }
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses BPJS Gagal!',
                    'text' => $result['metaData']['message'],
                    'flag' => 'bpjs_error'
                ];
            }
        }
        return true;
    }

    protected function saveRujukan($nosep, $payloadRujukan)
    {
        $modelBpjs = new Bpjs;
        $namaUser = Yii::$app->jwt->user->nama_pemakai;

        // Set variabel ke rujukan
        $rujukan['noSep'] = $nosep;
        $rujukan['tglRujukan'] = $payloadRujukan['tanggal_rujukan'];
        $rujukan['ppkDirujuk'] = $payloadRujukan['dirujukke'];
        $rujukan['jnsPelayanan'] = $payloadRujukan['jenis_pelayanan_bpjs'];
        $rujukan['catatan'] = $payloadRujukan['catatan_rujukan'];
        $rujukan['diagRujukan'] = $payloadRujukan['diagnosa_rujukan'];
        $rujukan['tipeRujukan'] = $payloadRujukan['rujukan'];
        // $rujukan['poliRujukan'] = $payloadRujukan['poli_rujukan'];
        $rujukan['poliRujukan'] =$payloadRujukan['jenis_pelayanan_bpjs']=='1'?'': $payloadRujukan['kode_spesialis'];
        $rujukan['spesialis'] = $payloadRujukan['spesialis'];
        $rujukan['kodeSpesialis'] = $payloadRujukan['kode_spesialis'];
        $rujukan['tglRencanaKunjungan'] = $payloadRujukan['tanggal_rencana_kunjungan'];
        $rujukan['user'] = $namaUser;

        // Set ke model
        $modelBpjs->t_rujukan = $rujukan;

        // Save
        $result = $modelBpjs->createRujukan();

        if (isset($result['metaData']['code'])) {
            if ($result['metaData']['code'] == 200) {
                $data = $result['response'];

                return [
                    'status' => 200,
                    'message' => 'Berhasil membuat rujukan.',
                    'data' => $data,
                    'request' => $modelBpjs->t_rujukan
                ];
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses BPJS Gagal!',
                    'text' => $result,
                    'flag' => 'bpjs_error'
                ];
            }
        }

        return true;
    }

    //** Save Only to table bpjs_t without send it to bpjs*/
    protected function saveBpjsWithouBridging(BpjsNewForm $payloadBpjs, $id, $pasienBaru, $isRanap = false)
    {
        $getPasien = Pendaftaran::find()
            ->select([
                'pasien.pasien_id as pasien_id',
                'pasien.no_rekam_medik as no_rekam_medik',
                'pasien.nama_pasien as nama_pasien',
            ])
            ->where(['pendaftaran_id' => $id])
            ->join('JOIN', 'pasien_m pasien',"pasien.pasien_id = pendaftaran_t.pasien_id")
            ->asArray()
            ->one();
        $payloadBpjs->no_rekam_medik = isset($getPasien['no_rekam_medik']) ? $getPasien['no_rekam_medik'] : null;
        $namaUser = Yii::$app->jwt->user->nama_pemakai;
        $modelBpjs = new Bpjs;
        $sepNew['noKartu'] = $payloadBpjs->no_kartu;
        $sepNew['tglSep'] = date('Y-m-d', strtotime($payloadBpjs->tanggal_sep));
        $sepNew['jnsPelayanan'] = $payloadBpjs->jenis_pelayanan;
        $sepNew['klsRawat'] = $payloadBpjs->kelas_rawat;
        $sepNew['noMR'] = $payloadBpjs->no_rekam_medik;
        $sepNew['asalRujukan'] = $payloadBpjs->asal_rujukan;
        $sepNew['tglRujukan'] = date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan));
        $sepNew['noRujukan'] = $payloadBpjs->no_rujukan;
        $sepNew['catatan'] = $payloadBpjs->catatan_sep;
        $sepNew['diagAwal'] = $payloadBpjs->diagnosa_awal;
        $sepNew['tujuan'] = isset($payloadBpjs->poli_tujuan) ? $payloadBpjs->poli_tujuan : 'IGD';
        $sepNew['eksekutif'] = !empty($payloadBpjs->poli_eksekutif) ? $payloadBpjs->poli_eksekutif : "0";
        $sepNew['cob'] = !empty($payloadBpjs->cob) ? $payloadBpjs->cob : '0';
        $sepNew['katarak'] = $payloadBpjs->katarak;
        $sepNew['lakaLantas'] = $payloadBpjs->kasus_kecelakaan;
        $sepNew['noSurat'] = isset($payloadBpjs->no_surat_kontrol) ? $payloadBpjs->no_surat_kontrol : '';
        $sepNew['kodeDPJP'] = isset($payloadBpjs->kode_dpjp) ? $payloadBpjs->kode_dpjp : '';
        $sepNew['penjamin'] = '';

        if (!$payloadBpjs->status_suplesi) {
            $penjamin = '';
            if ($payloadBpjs->kasus_kecelakaan == '1') { // kecekalaan lalu lintas dan bukan kecelakaan kerja
                $penjamin = '1';
            }  elseif ($payloadBpjs->kasus_kecelakaan == '2') {
                $penjamin = '1,2';
            } elseif ($payloadBpjs->kasus_kecelakaan == '3') {
                $penjamin = '2,3';
            }
            $sepNew['penjamin'] = $penjamin;
        }

        $sepNew['tglKejadian'] = date('Y-m-d', strtotime($payloadBpjs->tanggal_kejadian));
        $sepNew['keterangan'] = $payloadBpjs->keterangan;
        $sepNew['suplesi'] = $payloadBpjs->status_suplesi;
        $sepNew['noSepSuplesi'] = $payloadBpjs->no_sep_suplesi;
        $sepNew['kdPropinsi'] = $payloadBpjs->kode_provinsi;
        $sepNew['kdKabupaten'] = $payloadBpjs->kode_kabupaten;
        $sepNew['kdKecamatan'] = $payloadBpjs->kode_kecamatan;
        $sepNew['noSurat'] = isset($payloadBpjs->no_surat_kontrol) ? $payloadBpjs->no_surat_kontrol : '';
        $sepNew['kodeDPJP'] = isset($payloadBpjs->kode_dpjp) ? $payloadBpjs->kode_dpjp : '';
        $sepNew['noTelp'] = $payloadBpjs->no_telp;
        $sepNew['user'] = Yii::$app->jwt->user->nama_pemakai;
        $sepNew['ppkRujukan'] = !empty($payloadBpjs->ppk_rujukan) ? $payloadBpjs->ppk_rujukan : $modelBpjs->ppkPelayanan;
        if($payloadBpjs->jenis_pelayanan == 2) {
            $sepNew['kode_dpjp_melayani'] = $payloadBpjs->kode_dpjp_melayani;
            $sepNew['klsRawatNaik'] = "";
            $sepNew['pembiayaan'] = "";
            $sepNew['penanggungJawab'] = "";
        } else {
            $sepNew['kode_dpjp_melayani'] = "";
            $sepNew['klsRawatNaik'] = $payloadBpjs->klsRawatNaik;
            $sepNew['pembiayaan'] = $payloadBpjs->pembiayaan;
            $sepNew['penanggungJawab'] = $payloadBpjs->penanggungJawab;
        }
        $sepNew['tujuanKunj'] = $payloadBpjs->tujuanKunj;
        $sepNew['flagProcedure'] = $payloadBpjs->flagProcedure;
        $sepNew['kdPenunjang'] = $payloadBpjs->kdPenunjang;
        $sepNew['assesmentPel'] = $payloadBpjs->assesmentPel;

        $noSep = null;
        $namaPeserta = $getPasien['nama_pasien'];
        $modelBpjs->tglsep = $sepNew['tglSep'];
        $modelBpjs->nosep = $noSep;
        $modelBpjs->nokartuasuransi = $sepNew['noKartu'];
        $modelBpjs->tglrujukan = $sepNew['tglRujukan'];
        $modelBpjs->norujukan = $sepNew['noRujukan'];
        $modelBpjs->ppkrujukan = $sepNew['ppkRujukan'];
        $modelBpjs->ppkpelayanan = $modelBpjs->ppkPelayanan;
        $modelBpjs->jnspelayanan = $sepNew['jnsPelayanan'];
        $modelBpjs->catatansep = $sepNew['catatan'];
        $modelBpjs->diagnosaawal = $sepNew['diagAwal'];
        $modelBpjs->politujuan = $sepNew['tujuan'];
        $modelBpjs->klsrawat = $sepNew['klsRawat'];
        $modelBpjs->nama_peserta = $namaPeserta;
        $modelBpjs->lakalantas = $sepNew['lakaLantas'];
        $modelBpjs->asal_rujukan = isset($payloadBpjs->asal_rujukan) ? $payloadBpjs->asal_rujukan : null;
        $modelBpjs->additional_data = null;
        $modelBpjs->additional_request = json_encode($sepNew);
        $modelBpjs->pendaftaran_id = $id;
        $modelBpjs->kode_dpjp_melayani = $payloadBpjs->kode_dpjp_melayani;
        $modelBpjs->nama_dpjp_melayani = $payloadBpjs->nama_dpjp_melayani;
        $modelBpjs->kode_ppk_perujuk = $payloadBpjs->kode_ppk_perujuk;
        $modelBpjs->nama_ppk_perujuk = $payloadBpjs->nama_ppk_perujuk;
        // $modelBpjs->nama_peserta = $payloadBpjs->nama_peserta;
        $modelBpjs->no_perusahaan = (isset($payloadBpjs->nomorpokokperusahaan)) ? $payloadBpjs->nomorpokokperusahaan : null;
        $modelBpjs->nama_perusahaan = (isset($payloadBpjs->namaperusahaan)) ? $payloadBpjs->namaperusahaan : null;
        $modelBpjs->klsrawatnaik = $sepNew['klsRawatNaik'];
        $modelBpjs->pembiayaan = $sepNew['pembiayaan'];
        $modelBpjs->penanggung_jawab = $sepNew['penanggungJawab'];
        $modelBpjs->tujuan_kunj = $sepNew['tujuanKunj'];
        $modelBpjs->flag_procedure = $sepNew['flagProcedure'];
        $modelBpjs->kd_penunjang = $sepNew['kdPenunjang'];
        $modelBpjs->assesment_pel = $sepNew['assesmentPel'];
        $modelBpjs->kode_dpjp_spri = isset($payloadBpjs->kode_dpjp_spri) ? $payloadBpjs->kode_dpjp_spri : null;
        $modelBpjs->nama_dpjp_spri = isset($payloadBpjs->nama_dpjp_spri) ? $payloadBpjs->nama_dpjp_spri : null;
        if ($modelBpjs->save(false)) {
            $bpjsId = $modelBpjs->bpjs_id;
            $qPendaftaran = "
                UPDATE pendaftaran_t SET bpjs_id = {$bpjsId}
                WHERE pendaftaran_id = {$id}
            ";
            if ($isRanap) {
                $qPendaftaran = "
                    UPDATE pasienadmisi_t SET bpjs_id = {$bpjsId}
                    WHERE pendaftaran_id = {$id}
                ";
            }
            $qUpdatePendaftaran = Yii::$app->db->createCommand($qPendaftaran)->execute();
        }

        return true;
    }

    public function updateAntrianJkn($pendaftaranol_id = null, $waktu = null, $taskid = null){
        $date = date('Y-m-d H:i:s');
        $waktu = empty($waktu) ? strtotime($date) * 1000 : strtotime($waktu) * 1000;

        if (empty($pendaftaranol_id)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Pendaftaranol ID tidak boleh kosong!'
            ];
        }

        if (empty($taskid)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Task ID tidak boleh kosong!'
            ];
        }

        $pendaftaranol = PendaftaranOnline::find()->select([
            'pendaftaranol_id',
            'no_pendaftaranol',
            'jenis_reservasi',
        ])->where(['jenis_reservasi' => DocoConstants::JENIS_RSV_JKN])
        ->andWhere(['pendaftaranol_id' => $pendaftaranol_id])
        ->asArray()->one();

        if (!empty($pendaftaranol)){
            $model = new BpjsJkn;
            $model->antrian_jkn = [
                'kodebooking' => $pendaftaranol['no_pendaftaranol'],
                'taskid' => $taskid,
                'waktu' => $waktu
            ];
            $response = $model->updateAntrianJkn();

            $responseCode = ArrayHelper::getValue($response, 'metadata', null);
            if ($responseCode) {
                if ($response['metadata']['code'] !== 200) {
                    return [
                        'status' => 422,
                        'title' => 'Proses BPJS Gagal!',
                        'text' => $response['metadata']['message'],
                    ];
                }
            }
        } else {
            return [
                'status' => 422,
                'title' => 'Proses BPJS Gagal!',
                'text' => 'Data tidak ditemukan!'
            ];
        }

        return true;
    }

    public function saveAntrol($payloadBpjs,$idPendaftaran,$pasienBaru)
    {
        $dataPendaftaran = Pendaftaran::findOne($idPendaftaran);
        $noPendaftaran = $dataPendaftaran->no_pendaftaran;

        $pendaftaranol = PendaftaranOnline::find()->select([
            'pendaftaranol_id',
        ])
        ->andWhere(['pendaftaran_id' =>$idPendaftaran])
        ->one();
        if(!is_null($pendaftaranol)){
            return true;
        }

        $antrianJkn = AntrianjknV::find()
                        ->where([
                            'pendaftaran_id' => $dataPendaftaran->pendaftaran_id
                        ])->asArray()->one();
        
        $tglestimasi = date('Y-m-d');
        $jamestimasi = date('H:i:s');
        $waktuestimasi = $tglestimasi . " " . $jamestimasi;
        $date_utc = new \DateTime($waktuestimasi, new \DateTimeZone("UTC"));
        $time_utc = $date_utc->format('Y-m-d H:i:s');
        $estimasi = strval(strtotime($time_utc)-strtotime('1970-01-01 00:00:00'));
        
        //$nik = '';
        //if (!empty($antrianJkn['additional_pasien'])) {
        //    $additionalPasien = json_decode($antrianJkn['additional_pasien'], true);
        //    foreach ($additionalPasien as $key => $value) {
        //        if ($value['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) $nik = $value['no_identitas_pasien'];
        //    }
        //}

        $infoBpjs = [];
        if (!empty($payloadBpjs->info_response) && !is_array($payloadBpjs->info_response)) {
            $infoBpjs = json_decode($payloadBpjs->info_response,true);
        }

        $nik = isset($infoBpjs['peserta']['nik']) ? $infoBpjs['peserta']['nik'] : '-';

        $jadwaldokter_id = ArrayHelper::getValue($antrianJkn,'jadwaldokter_id');
        $getDataAdditional = $this->_getInfoPendaftaran($idPendaftaran,$jadwaldokter_id);
        $tanggalperiksa = date('Y-m-d', strtotime(ArrayHelper::getValue($antrianJkn,'tanggal_periksa')));
        $jamMulai = ArrayHelper::getValue($getDataAdditional,'jammulai');
        $jamTutup = ArrayHelper::getValue($getDataAdditional,'jamtutup');
        
        $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0);
        $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotajkn', 0);
        // $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0) + ArrayHelper::getValue($getDataAdditional,'kuotanonjkn', 0);
        // $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn', 0) + ArrayHelper::getValue($getDataAdditional,'sisakuotajkn',0);
        $spm = ArrayHelper::getValue($getDataAdditional,'estimasidilayani', 15);
        $noUrut = ($totalAntrian - $sisaAntrian);
        /**
         * jika waktu ambil lebih dari waktu mulai praktek
         * maka
         *  jika timeslot yg sudah diambil < waktu ambil antrian
         *  maka
         *      ambil timeslot setelah jam ambil
         * jika tidak 
         *      jam mulai + (spm * nourut)
         */
        $jamSekarang = date('H:i:s');
        $tglJamSekarang = $tanggalperiksa . ' ' . $jamSekarang;
        $tglDilayani = $tanggalperiksa . ' ' . $jamMulai;
        $urutanAntrianDiambil = $noUrut>0 ? $noUrut-1 : $noUrut;
        $currentTimeSlot = (date_create($tglDilayani)->getTimestamp()) + (
            (($spm * $urutanAntrianDiambil) * 60)
        );

        $timestampsecond = (date_create($tglDilayani)->getTimestamp()) + (
            (($spm * $noUrut) * 60)
        );

        if(strtotime($tglJamSekarang) > strtotime($tglDilayani)){
            if($currentTimeSlot < strtotime($tglJamSekarang)){
                $interval = date_diff(date_create($tglDilayani),date_create($tglJamSekarang));
                $urutanSekarang = ceil(((($interval->h * 60)+$interval->i) / $spm));

                $timestampsecond = (date_create($tglDilayani)->getTimestamp()) + (
                    (($spm * $urutanSekarang) * 60)
                );
            }
        }

        $estimasiDilayani = $timestampsecond * 1000;

        $jenisKunjunganJkn = !empty($payloadBpjs->no_surat_kontrol) ? 3 : ArrayHelper::getValue($antrianJkn,'jeniskunjungan');
        $nomorReferensiJkn = !empty($payloadBpjs->no_surat_kontrol) ? $payloadBpjs->no_surat_kontrol : ArrayHelper::getValue($antrianJkn,'nomorreferensi');

        $poliRujukan = ArrayHelper::getValue($infoBpjs,'rujukan.poliRujukan.kode',null);
        $lastPoli = ArrayHelper::getValue($infoBpjs,'lastPoli',null);
        $jumlahSep = 0;
        if(!is_null($lastPoli)){
            $wsJumlahSep = (new Bpjs)->jumlahSEP([
                'jenisRujukan' => ArrayHelper::getValue($payloadBpjs,'asal_rujukan'),
                'noRujukan' => ArrayHelper::getValue($antrianJkn,'nomorreferensi')
            ]);
            $jumlahSep = ArrayHelper::getValue($wsJumlahSep,'response.jumlahSEP',0);
        }
        
        if($jumlahSep >= 1 && !is_null($poliRujukan)){
            if($poliRujukan == ArrayHelper::getValue($getDataAdditional,'kodepoli')){
                $jenisKunjunganJkn = 3;
            }else if($poliRujukan != ArrayHelper::getValue($getDataAdditional,'kodepoli')){
                $jenisKunjunganJkn = 2;
            }
        }

        $request = [
            'kodebooking' => ArrayHelper::getValue($antrianJkn,'kodebooking'),
            'jenispasien' => ArrayHelper::getValue($antrianJkn,'jenispasien'),
            'nomorkartu' => ArrayHelper::getValue($antrianJkn,'nomorkartu'),
            'nik' => $nik ? $nik : '',
            'nohp' => ArrayHelper::getValue($antrianJkn,'no_telepon_pasien'),
            'kodepoli' => ArrayHelper::getValue($getDataAdditional,'kodepoli'),
            'namapoli' => ArrayHelper::getValue($getDataAdditional,'namapoli'),
            'pasienbaru' => ($pasienBaru) ? 1 : 0,
            'norm' => ArrayHelper::getValue($antrianJkn,'no_rekam_medik'),
            'tanggalperiksa' => $tanggalperiksa,
            'kodedokter' => ArrayHelper::getValue($getDataAdditional,'kodedokter'),
            'namadokter' => ArrayHelper::getValue($getDataAdditional,'namadokter'),
            'jampraktek' => ArrayHelper::getValue($getDataAdditional,'jampraktek'),
            'jeniskunjungan' => $jenisKunjunganJkn,
            'nomorreferensi' => $nomorReferensiJkn,
            'nomorantrean' => ArrayHelper::getValue($antrianJkn,'nomorantrean'),
            'angkaantrean' => ArrayHelper::getValue($antrianJkn,'angkaantrean'),
            'estimasidilayani' => $estimasiDilayani,
            'sisakuotajkn' => ArrayHelper::getValue($getDataAdditional,'sisakuotajkn'),
            'kuotajkn' => ArrayHelper::getValue($getDataAdditional,'kuotajkn'),
            'sisakuotanonjkn' => ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn'),
            'kuotanonjkn' => ArrayHelper::getValue($getDataAdditional,'kuotanonjkn'),
            'keterangan' => ArrayHelper::getValue($antrianJkn,'keterangan'),
        ];

        $simpanAntrian = (new BpjsJkn)->simpanAntrianJkn($request);
        $responseAntrian = ArrayHelper::getValue($simpanAntrian,'metadata.code');
        $this->setLogs($simpanAntrian, $request, $idPendaftaran);
        if($responseAntrian !=200){
            $messageAntrian = ArrayHelper::getValue($simpanAntrian,'metadata.message');
            return [
                'status'=>422,
                'title'=>'Proses BPJS Gagal',
                'text'=>'Gagal Bridging Antrean - '.@$messageAntrian
            ];
        } else {
            Yii::$app->request->setBodyParams([
                'kodebooking' => ArrayHelper::getValue($antrianJkn,'kodebooking')
            ]);
            Yii::$app->runAction('/v1/sync-bpjs/antrian-per-kode-booking');
        }

        return true;
    }

    public function updateAntrolSelesaiAdmisi($idPendaftaran,$pasienBaru)
    {
        $dataPendaftaran = Pendaftaran::findOne($idPendaftaran);

        $antrianJkn = AntrianjknV::find()
                        ->where([
                            'antrian_id'=>$dataPendaftaran->antrian_id,
                            'pendaftaran_id'=>$dataPendaftaran->pendaftaran_id
                        ])->asArray()->one();
        $newDate = date('Y-m-d H:i:s');

        $modelBpjs = new BpjsJkn;

        if($pasienBaru){
            $task_list = [1,2,3];
        }else{
            $task_list = [3];
        }

        foreach($task_list as $_task){
            $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
            $time_utc = $date_utc->format('Y-m-d H:i:s');
            $waktu = strval(strtotime($time_utc)-strtotime('1970-01-01 00:00:00'));
            $waktu_ms = DocoHelpers::formatToMilisecond($waktu, 'milidetik');
            $modelBpjs->antrian_jkn = [
                'kodebooking' => ArrayHelper::getValue($antrianJkn,'kodebooking'),
                'taskid' => $_task,
                'waktu' => $waktu_ms
            ];
            $updateAntrolAdmisi = $modelBpjs->updateAntrianJkn();

            $responseAntrian = ArrayHelper::getValue($updateAntrolAdmisi,'metadata.code');
            if($responseAntrian !=200){
                $messageAntrian = ArrayHelper::getValue($updateAntrolAdmisi,'metadata.message');
                return [
                    'status'=>422,
                    'title'=>'Proses BPJS Gagal',
                    'text'=>'Gagal Update Task - '.@$messageAntrian
                ];
            }
        }
        

        return true;
    }

    public function cancelAntrol($idPendaftaran, $keterangan = '')
    {
        $dataPendaftaran = Pendaftaran::findOne($idPendaftaran);

        $antrianJkn = AntrianjknV::find()
                        ->where([
                            'pendaftaran_id'=>$dataPendaftaran->pendaftaran_id
                        ])->asArray()->one();
        $newDate = date('Y-m-d H:i:s');

        $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
        $time_utc = $date_utc->format('Y-m-d H:i:s');
        $waktu = strval(strtotime($time_utc)-strtotime('1970-01-01 00:00:00'));
        $waktu_ms = DocoHelpers::formatToMilisecond($waktu, 'milidetik');
        
        $modelBpjs = new BpjsJkn;
        $modelBpjs->antrian_jkn = [
            'kodebooking' => ArrayHelper::getValue($antrianJkn,'kodebooking'),
            'keterangan' => $keterangan,
        ];
        $cancelAntrian = $modelBpjs->batalAntrianJkn();
        $this->setLogs($cancelAntrian, $modelBpjs->antrian_jkn, $idPendaftaran);
        $responseAntrian = ArrayHelper::getValue($cancelAntrian,'metadata.code');
        if($responseAntrian !=200){
            $messageAntrian = ArrayHelper::getValue($cancelAntrian,'metadata.message');
            return [
                'status'=>422,
                'title'=>'Proses BPJS Gagal',
                'text'=>'Gagal Batal Antrean - '.@$messageAntrian
            ];
        }

        return true;
    }

    protected function _getInfoPendaftaran($pendaftaran_id, $jadwaldokter_id)
    {
        $today_id = date('N') + 74;
        $dataPendaftaran = Yii::$app->db->createCommand("
            select 
                pt.pasien_id,
                pt.pegawai_id,
                pt.ruangan_id,
                pm.kode_dokter_bpjs ,
                pm.nama_pegawai  ,
                rm.kode_ruangan_bpjs ,
                rm.ruangan_nama,
	            pt.tgl_pendaftaran,
                pt.antrian_id
            from pendaftaran_t pt  
            join pegawai_m pm on pm.pegawai_id  = pt.pegawai_id 
            join ruangan_m rm on rm.ruangan_id = pt.ruangan_id 
            where  pt.pendaftaran_id = {$pendaftaran_id};
        ")->queryOne();

        $dataJadwal = Yii::$app->db->createCommand("
            select 
                COALESCE(jm.kuota_bpjs_offline,0) as kuota_bpjs_offline,
                COALESCE(jm.kuota_bpjs_online,0) as kuota_bpjs_online,
                COALESCE(jm.kuota_nonbpjs_offline,0) as kuota_nonbpjs_offline,
                COALESCE(jm.kuota_nonbpjs_online,0) as kuota_nonbpjs_online,
                jm.jadwaldokter_mulai,
                jm.jadwaldokter_tutup,
                jm.jumlah_loaddokter as estimasidilayani
            from jadwaldokter_m jm  
            where  jm.jadwaldokter_id = {$jadwaldokter_id};
        ")->queryOne();

        $pegawai_id = isset($dataPendaftaran['pegawai_id']) ? $dataPendaftaran['pegawai_id'] : 0 ;
        $ruangan_id = isset($dataPendaftaran['ruangan_id']) ? $dataPendaftaran['ruangan_id'] : 0;
        $tgl_pendaftaran = isset($dataPendaftaran['tgl_pendaftaran']) ? $dataPendaftaran['tgl_pendaftaran'] : 0;
        // $jadwaldokter_id = isset($dataPendaftaran['jadwaldokter_id']) ? $dataPendaftaran['jadwaldokter_id'] : 0;
        $antrian_id = isset($dataPendaftaran['antrian_id']) ? $dataPendaftaran['antrian_id'] : 0;
        $waktu_pendaftaran = date('H:i:s', strtotime($tgl_pendaftaran));

        $tgl_reservasi = date('Y-m-d',strtotime($dataPendaftaran['tgl_pendaftaran']));

        $dataAntrian = Yii::$app->db->createCommand("
            SELECT 
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id=418 or carabayar_id=6)) as jumlah_antrian_bpjs_online,
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id=418 or carabayar_id=6)) as jumlah_antrian_bpjs_offline,
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id<>418 or carabayar_id<>6)) as jumlah_antrian_nonbpjs_online,  
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id<>418 or carabayar_id<>6)) as jumlah_antrian_nonbpjs_offline
            FROM antrian_t 
            WHERE jenisantrian_id = 312
            AND antrian_t.jadwaldokter_id = :jadwaldokter_id
            AND tgl_antrian::date = :tgl_reservasi
            AND antrian_id <> :antrian_id
            AND antrian_t.is_deleted is false 
        ")
        ->bindValue(':jadwaldokter_id',$jadwaldokter_id)
        ->bindValue(':tgl_reservasi',$tgl_reservasi)
        ->bindValue(':antrian_id',$antrian_id);
        $dataAntrian = $dataAntrian->queryOne();

        $kuota_bpjs_offline = ArrayHelper::getValue($dataJadwal,'kuota_bpjs_offline',0);
        $kuota_bpjs_online= ArrayHelper::getValue($dataJadwal,'kuota_bpjs_online',0);
        $kuota_nonbpjs_offline= ArrayHelper::getValue($dataJadwal,'kuota_nonbpjs_offline',0);
        $kuota_nonbpjs_online= ArrayHelper::getValue($dataJadwal,'kuota_nonbpjs_online',0);
        $kuotajkn = $kuota_bpjs_online + $kuota_bpjs_offline;
        $kuotanonjkn = $kuota_nonbpjs_offline + $kuota_nonbpjs_online;
        $jumlah_antrian_bpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_online',0);
        $jumlah_antrian_bpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_offline',0);
        $jumlah_antrian_nonbpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_online',0);
        $jumlah_antrian_nonbpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_offline',0);
        $sisakuotanonjkn = $kuotanonjkn - ($jumlah_antrian_nonbpjs_online + $jumlah_antrian_nonbpjs_offline);
        $sisakuotajkn = $kuotajkn - ($jumlah_antrian_bpjs_online + $jumlah_antrian_bpjs_offline);

        $jam_mulai = !empty($dataJadwal['jadwaldokter_mulai']) 
                        ? date('H:i', strtotime($dataJadwal['jadwaldokter_mulai'])) : '00:00';
        $jam_tutup = !empty($dataJadwal['jadwaldokter_tutup']) 
                        ? date('H:i', strtotime($dataJadwal['jadwaldokter_tutup'])) : '00:00';
        return [
            'kodepoli' => isset($dataPendaftaran['kode_ruangan_bpjs']) ? $dataPendaftaran['kode_ruangan_bpjs'] : '-',
            'namapoli' => isset($dataPendaftaran['ruangan_nama']) ? $dataPendaftaran['ruangan_nama'] : '-',
            'kodedokter' => isset($dataPendaftaran['kode_dokter_bpjs']) ? $dataPendaftaran['kode_dokter_bpjs'] : '-',
            'namadokter' => isset($dataPendaftaran['nama_pegawai']) ? $dataPendaftaran['nama_pegawai'] : '-',
            'jampraktek' => $jam_mulai . '-' . $jam_tutup,
            'kuotajkn' => $kuotajkn,
            'kuotanonjkn' => $kuotanonjkn,
            'sisakuotajkn' => $sisakuotajkn,
            'sisakuotanonjkn' => $sisakuotanonjkn,
            'jammulai' => $jam_mulai,
            'jamtutup' => $jam_tutup,
            'tgl_pendaftaran' => $tgl_pendaftaran,
            'estimasidilayani' => isset($dataJadwal['estimasidilayani']) ? $dataJadwal['estimasidilayani'] : 6,
        ];
    }

    protected function _getInfoPendaftaranLama($pendaftaranol_t)
    {
        $today_id = date('N') + 74;
        $dataPendaftaran = Yii::$app->db->createCommand("
            select 
                pt.pasien_id,
                pt.pegawai_id,
                pt.ruangan_id,
                pm.kode_dokter_bpjs ,
                pm.nama_pegawai  ,
                rm.kode_ruangan_bpjs ,
                rm.ruangan_nama,
	            pt.tgl_pendaftaran 
            from pendaftaran_t pt  
            join pegawai_m pm on pm.pegawai_id  = pt.pegawai_id 
            join ruangan_m rm on rm.ruangan_id = pt.ruangan_id 
            where  pt.pendaftaran_id = {$pendaftaran_id};
        ")->queryOne();

        $pegawai_id = isset($dataPendaftaran['pegawai_id']) ? $dataPendaftaran['pegawai_id'] : 0 ;
        $ruangan_id = isset($dataPendaftaran['ruangan_id']) ? $dataPendaftaran['ruangan_id'] : 0;
        $tgl_pendaftaran = isset($dataPendaftaran['tgl_pendaftaran']) ? $dataPendaftaran['tgl_pendaftaran'] : 0;
        $waktu_pendaftaran = date('H:i:s', strtotime($tgl_pendaftaran));

        $jadwalDokter = Yii::$app->db->createCommand("
            select 
                d.jadwaldokter_id ,
                d.jadwaldokter_mulai ,
                d.jadwaldokter_tutup ,
                d.jumlah_loaddokter as estimasidilayani,
                d.kuota_bpjs_online + d.kuota_bpjs_offline AS kuotajkn,
                d.kuota_nonbpjs_online + d.kuota_nonbpjs_offline AS kuotanonjkn,
                kuotadokter_r.kuota_bpjs_online + kuotadokter_r_offline.kuota_bpjs_offline AS sisakuotajkn,
                kuotadokter_r.kuota_nonbpjs_online + kuotadokter_r_offline.kuota_nonbpjs_offline AS sisakuotanonjkn
            from jadwalbukapoli_m j
                RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
                RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id
                JOIN ( SELECT a.kuota_bpjs_online,
                        a.kuota_nonbpjs_online,
                        a.jadwaldokter_id,
                        a.is_online
                    FROM kuotadokter_r a) kuotadokter_r ON d.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online = true
                JOIN ( SELECT a.kuota_bpjs_offline,
                        a.kuota_nonbpjs_offline,
                        a.jadwaldokter_id,
                        a.is_online
                    FROM kuotadokter_r a) kuotadokter_r_offline ON d.jadwaldokter_id = kuotadokter_r_offline.jadwaldokter_id AND kuotadokter_r_offline.is_online = false
            where j.ruangan_id = {$ruangan_id}
                and j.hari = {$today_id}
                and p.pegawai_id = {$pegawai_id}
                and d.is_deleted = false and d.is_active = true
                and p.is_deleted = false and p.is_active = true        
        ")->queryAll();
        /** Proses selected range tanggal ketika ada 2 shift */
        $endBefore = null;
        $selectedJadwal = [];

        foreach ($jadwalDokter as $key => $value) {
            if (!empty($endBefore)) {
                if (strtotime($waktu_pendaftaran) < strtotime($value['jadwaldokter_mulai'])) {
                    $selectedJadwal = $jadwalDokter[$key-1];
                    break;
                }
            }
            
            if (strtotime($waktu_pendaftaran) <= strtotime($value['jadwaldokter_mulai'])
                    || (strtotime($waktu_pendaftaran) >= strtotime($value['jadwaldokter_mulai']) 
                            && strtotime($waktu_pendaftaran) <= ($value['jadwaldokter_tutup']) )) {
                $selectedJadwal = $value;
            }
        
            $endBefore = $value['jadwaldokter_tutup'];
        }

        if (empty($selectedJadwal) && !empty($value)) {
            $selectedJadwal = $value;
        }

        $jam_mulai = !empty($selectedJadwal['jadwaldokter_mulai']) 
                        ? date('H:i', strtotime($selectedJadwal['jadwaldokter_mulai'])) : '00:00';
        $jam_tutup = !empty($selectedJadwal['jadwaldokter_tutup']) 
                        ? date('H:i', strtotime($selectedJadwal['jadwaldokter_tutup'])) : '00:00';

        return [
            'kodepoli' => isset($dataPendaftaran['kode_ruangan_bpjs']) ? $dataPendaftaran['kode_ruangan_bpjs'] : '-',
            'namapoli' => isset($dataPendaftaran['ruangan_nama']) ? $dataPendaftaran['ruangan_nama'] : '-',
            'kodedokter' => isset($dataPendaftaran['kode_dokter_bpjs']) ? $dataPendaftaran['kode_dokter_bpjs'] : '-',
            'namadokter' => isset($dataPendaftaran['nama_pegawai']) ? $dataPendaftaran['nama_pegawai'] : '-',
            'jampraktek' => $jam_mulai . '-' . $jam_tutup,
            'kuotajkn' => isset($selectedJadwal['kuotajkn']) ? $selectedJadwal['kuotajkn'] : 0,
            'kuotanonjkn' => isset($selectedJadwal['kuotanonjkn']) ? $selectedJadwal['kuotanonjkn'] : 0,
            'sisakuotajkn' => isset($selectedJadwal['sisakuotajkn']) ? $selectedJadwal['sisakuotajkn'] : 0,
            'sisakuotanonjkn' => isset($selectedJadwal['sisakuotanonjkn']) ? $selectedJadwal['sisakuotanonjkn'] : 0,
            'jammulai' => $jam_mulai,
            'estimasidilayani' => isset($selectedJadwal['estimasidilayani']) ? $selectedJadwal['estimasidilayani'] : 0,
        ];
    }

    protected function setLogs($response, $data, $idPendaftaran = null)
    {
        $model = new LoginJknR;
        $model->pendaftaran_id = $idPendaftaran;
        $model->state = isset($data['taskid']) ? $data['taskid'] : null;
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = null;
        $model->payload = isset($data) ? json_encode($data) : null;
        $model->sync_respon = isset($response) ? json_encode($response) : null;
    
        $model->save();        
    }
}
