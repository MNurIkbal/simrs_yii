<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Integrasi\Service\Sirs\Models\LoginJknR;
use Integrasi\Service\Sirs\Models\PendaftaranOl;
use Integrasi\Service\Sirs\Models\BpjsJkn;
use Integrasi\Service\Sirs\Models\AntrianJknV;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoHelpers;

class AddAntrianOffJkn extends \Integrasi\Contracts\DocoImplement
{
    static protected $cons_ids;
    static protected $secret_keys;
    static protected $version = 1.0;
    public $url;
    public $cons_id;
    public $secret_key;
    public $ppkPelayanan;
    public $user_key;
    static protected $user_keys;
    static protected $pendaftaran_id;
    

    public function execute()
    {
        self::$pendaftaran_id = isset($this->result['id']) ? $this->result['id'] : null;
        if (self::$pendaftaran_id) self::$pendaftaran_id = DocoHelpers::decrypt(self::$pendaftaran_id);
        $task_id = isset($this->result['taskid']) ? $this->result['taskid'] : $this->taskid;
        $date = date('Y-m-d H:i:s');
        $waktu = DocoHelpers::generateTimeStamp($date);

        $is_reservasi = $this->validatePendaftaranOl(self::$pendaftaran_id);
        if($is_reservasi){
            $response = [
                'message'=> 'Pasien bukan dari pendaftaran offline'
            ];
            return $this->setResponse($response);
        }

        $instalasiRajal = Yii::$app->db->createCommand("select instalasi_id from pendaftaran_t where pendaftaran_id = :pendaftaran_id")->bindValue(":pendaftaran_id",self::$pendaftaran_id)->queryScalar();
        if($instalasiRajal != 1){
            $response = [
                'message'=> 'Pasien bukan dari pendaftaran rawat jalan'
            ];
            return $this->setResponse($response);
        }

        $antrianRes = $this->sendToJkn(self::$pendaftaran_id);
        $payloadAntrian = ArrayHelper::getValue($antrianRes,'payload',[]);
        if(is_array($payloadAntrian) && count($payloadAntrian) !== 0){
            $this->setLogs($antrianRes['jkn'], $antrianRes['payload']);
            $this->updateAdditionalJkn($antrianRes);
        }

        $data_pendaftaran = $antrianRes['response'];
        $kodebooking = isset($data_pendaftaran['kodebooking']) ? $data_pendaftaran['kodebooking'] : null;

        if (!empty($task_id)) {
            $waktuTambahan = 0;
            foreach ($task_id as $value) {
                if (ArrayHelper::getValue($payloadAntrian, 'status_pasien', 1) == 0 && in_array($value, [1, 2])) {
                    continue;
                }

                if (in_array($value, [4, 5, 6, 7])) {
                    continue;
                }
                
                if ($waktuTambahan > 0) {
                    $date = date('Y-m-d H:i:s', strtotime("+" . $waktuTambahan . " seconds"));
                    $waktu = DocoHelpers::generateTimeStamp($date);
                }

                $data = [
                    'kodebooking' => $kodebooking,
                    'taskid' => $value,
                    'waktu' => $waktu
                ];
                
                $result = (new BpjsJkn)->updateAntrianJkn($data);
                $response[] = $result;
                $this->updateAntrianPasien($data_pendaftaran, $value);
                $this->setLogs($result, $data);

                $waktuTambahan = $waktuTambahan + 5;
            }
        }
            
        // $data = [
        //     'kodebooking' => $kodebooking,
        //     'taskid' => $task_id,
        //     'waktu' => $waktu
        // ];
            
        // $response = (new BpjsJkn)->updateAntrianJkn($data);
        // $this->updateAntrianPasien($data_pendaftaran, $task_id);
        // $this->setLogs($response, $data);

        $lastResponse = [
            'data_antrian' => [
                'response' => $antrianRes['jkn'],
                'payload' => $antrianRes['payload']
            ],
            'data_update_task' => isset($response) ? $response : [],
            'time_zone' => date_default_timezone_get()
        ];
        
        return $this->setResponse($lastResponse);
    }

    public function setResponse($response)
    {
        return json_encode([
            'service' => 'Sirs-AddAntrianOffJkn',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    public function setLogs($response, $data)
    {
        $userIdentity = $this->user_identity;
        
        $model = new LoginJknR;
        $model->pendaftaran_id = self::$pendaftaran_id;
        $model->state = isset($data['taskid']) ? $data['taskid'] : null;
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = isset($userIdentity['uid']) ? $userIdentity['uid'] : null;
        $model->payload = isset($data) ? json_encode($data) : null;
        $model->sync_respon = isset($response) ? json_encode($response) : null;
    
        $model->save();
    }

    /**
     * @method update status antrian pasien di tabel antrian_t
     * 
     * @param array $data_pendaftaran
     * @param int $taskId
     * @return boolean
     */
    private function updateAntrianPasien($data_pendaftaran, $taskId)
    {
        $antrianId = $data_pendaftaran['antrian_id'];
        $penId = $data_pendaftaran['pendaftaran_id'];
        if (!empty($antrianId)) {
            switch ((int) $taskId) {
                case (int) DocoConstants::STATUS_DONE_ADMISI:
                    $status_antrian = DocoConstants::STATUS_ANTRIAN_POLI;
                    break;
                case (int) DocoConstants::STATUS_TUNGGU_POLI:
                    $status_antrian = DocoConstants::STATUS_PERIKSA;
                    break;
                case (int) DocoConstants::STATUS_PULANG_POLI:
                    $status_antrian = DocoConstants::STATUS_PULANG;
                    break;
                
                default:
                    $status_antrian = 0;
                    break;
            }

            Yii::$app->db->createCommand("
                UPDATE antrian_t SET status_antrian = ({$status_antrian}) WHERE antrian_id = {$antrianId}
            ")->execute();

            Yii::$app->db->createCommand("
                UPDATE antrianjkn_r SET is_checkin = true WHERE pendaftaran_id = ({$penId})
            ")->execute();
        }

        return true;
    }

    protected function getDataJkn()
    {
        return AntrianjknV::find();
    }

    protected function sendToJkn($id)
    {
        $request = [];
        $data = $this->getDataJkn()
            ->where([
                'pendaftaran_id' => $id
            ])
            ->asArray()
            ->one();
        $noReferensi = isset($data['nomorreferensi']) ? $data['nomorreferensi'] : null;
        if(!empty($data) && !empty($data['pendaftaran_id']) && empty($noReferensi)) {
            $nik = $data['no_identitas_pasien'];
            if (!empty($data['additional_pasien'])) {
                $additionalPasien = json_decode($data['additional_pasien'], true);
                foreach ($additionalPasien as $key => $value) {
                    if ($value['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) $nik = $value['no_identitas_pasien'];
                }
            }
            $getDataAdditional = $this->getInfoPendaftaran($id);
            $tanggalperiksa = date('Y-m-d', strtotime(ArrayHelper::getValue($data,'tanggal_periksa')));
            $jamMulai = ArrayHelper::getValue($getDataAdditional,'jammulai');
            
            $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0) + ArrayHelper::getValue($getDataAdditional,'kuotanonjkn', 0);
            $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn', 0) + ArrayHelper::getValue($getDataAdditional,'sisakuotajkn',0);
    
            $tglDilayani = $tanggalperiksa . ' ' . $jamMulai;
            $noUrut = ($totalAntrian - $sisaAntrian);
            $timestampsecond = (date_create($tglDilayani)->getTimestamp()) + (
                ((15 * $noUrut) * 60)
            );
    
            $estimasiDilayani = $timestampsecond * 1000;
            $noKartu = $data['nomorkartu'] ? $data['nomorkartu'] : '';

            $request = [
                'kodebooking' => $data['kodebooking'],
                'jenispasien' => $data['jenispasien'],
                'nomorkartu' => $data['nomorkartu'] ? $data['nomorkartu'] : '',
                'nik' => $nik ? $nik : $noKartu,
                'nohp' => $data['no_telepon_pasien'] ? $data['no_telepon_pasien'] : '',
                'kodepoli' => isset($getDataAdditional['kodepoli']) ? $getDataAdditional['kodepoli'] : '-',
                'namapoli' => isset($getDataAdditional['namapoli']) ? $getDataAdditional['namapoli'] : '-',
                'pasienbaru' => $data['status_pasien'],
                'norm' => $data['no_rekam_medik'],
                'tanggalperiksa' => date('Y-m-d', strtotime($data['tanggal_periksa'])),
                'kodedokter' => isset($getDataAdditional['kodedokter']) ? $getDataAdditional['kodedokter'] : '-',
                'namadokter' => isset($getDataAdditional['namadokter']) ? $getDataAdditional['namadokter'] : '-',
                'jampraktek' => isset($getDataAdditional['jampraktek']) ? $getDataAdditional['jampraktek'] : '-',
                'jeniskunjungan' => $data['jeniskunjungan'] ? $data['jeniskunjungan'] : '',
                'nomorreferensi' => $data['nomorreferensi'],
                'nomorantrean' => $data['nomorantrean'],
                'angkaantrean' => $data['angkaantrean'],
                'estimasidilayani' => $estimasiDilayani,
                'sisakuotajkn' => isset($getDataAdditional['sisakuotajkn']) ? $getDataAdditional['sisakuotajkn'] : 0,
                'kuotajkn' => isset($getDataAdditional['kuotajkn']) ? $getDataAdditional['kuotajkn'] : 0,
                'sisakuotanonjkn' => isset($getDataAdditional['sisakuotanonjkn']) ? $getDataAdditional['sisakuotanonjkn'] : 0,
                'kuotanonjkn' => isset($getDataAdditional['kuotanonjkn']) ? $getDataAdditional['kuotanonjkn'] : 0,
                'keterangan' => $data['keterangan'],
            ];
            Yii::error(json_encode($data));
            $send = (new BpjsJkn)->simpanAntrianJkn($request);
            
        }

        return [
            'response' => $data,
            'jkn' => isset($send) ? $send : null,
            'payload' => $request
        ];
    }

    protected function validatePendaftaranOl($pendaftaran_id = null)
    {
        if ($pendaftaran_id) {
            $data_ol = PendaftaranOl::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            if (!empty($data_ol)) {
                return true;
            }
        }
        return false;
    }

    protected function updateAdditionalJkn($data)
    {
        $pendaftaranId = self::$pendaftaran_id;
        $additionalJkn = [
            'response' => $data['jkn'],
            'payload' => $data['payload']
        ];
        $additionalJkn = json_encode($additionalJkn);

        Yii::$app->db->createCommand("
            UPDATE antrianjkn_r SET additional_jkn = '{$additionalJkn}' WHERE pendaftaran_id = ({$pendaftaranId})
        ")->execute();
    }

    protected function getInfoPendaftaran($pendaftaran_id)
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
            'jammulai' => $jam_mulai
        ];
    }
}