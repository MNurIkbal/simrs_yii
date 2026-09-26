<?php

/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\Services\RegistrationService;
use Doco\Services\Cache;

use Doco\models\pendaftaran\PendaftaranOnline;
use Doco\models\pendaftaran\InfoPendaftaranOnlineView;
use Doco\models\pendaftaran\PasienV;
use Doco\models\antrian\KonfigAntrian;
use Doco\models\antrian\AntrianjknV;
use Doco\models\bpjs\Bpjs;
use Doco\models\bpjs\BpjsJkn;
use Doco\models\bpjs\LoginJknR;
use Doco\models\CaraBayar;
use Doco\models\Pegawai;
use Doco\models\Pasien;
use Doco\models\Ruangan;
use Doco\components\DocoMessages;
use Doco\components\constans\StatusReservasi;

class PendaftaranOnlineProcess extends \Doco\components\DocoBaseProcessExtension
{
    /** @var int carbayar group */
    public $groupCarabayar;

    /** @var string prefix antrian*/
    public $prefixKonfig;

    /** @var bool flaging send to jkn */
    public $isJkn;

    /** @var int prefix hari lookup*/
    public $hariId;

    /** @var string slotseq */
    public $slotSequence;

    /** @var string data jkn */
    public $dataJkn;

    /** @var string date */
    public $date;

    /** @var string current date */
    public $currDate;

    /** @var string konfig is_nourut = true / ketika konfig nourut nya memilih manual */
    public $noUrut;

    /** @var array konfig sistem */
    public $konfig;

    /** @var bool if jadwal change */
    public $jadwalChange;

     /** @var array kuota bpjs dan nonbpjs sirs */
    public $kuotaDhelath;

    public $isOnline;
    
    /** @var bool flaging skip error jkn */
    public $skipErrorJkn;

    protected function processFlow()
    {
        $reservasiPoliklinik = false;
        $saved = false;
        $pasien = [];
        if (!empty(json_decode(Yii::$app->request->rawBody, true))) {
            $post = json_decode(Yii::$app->request->rawBody, true); //dari mobile sirs
        } else {
            $post = Yii::$app->request->post();
            $reservasiPoliklinik = true;
        }

        $is_from_antrian = isset($post['is_from_antrian']) ? true: false;
        $this->isOnline = isset($post['is_online']) && !empty($post['is_online']) ? $post['is_online']: true;

        $this->startDBTransaction();

        $model = new PendaftaranOnline();
        $this->setToModel($model, $post);
        $this->setToGlobal($model, $post);
        $additional = ArrayHelper::getValue($post, 'additional_data');
        if($this->isJkn) {
            $this->rsvJkn($model, $post);
            $this->validateKuotaJkn($model);
        } else {
            if ($this->konfig['is_support_jkn']) {
                $this->rsvJknKonfig($model, $post);
            }
            if($model->jadwaldokter_id != null){
                $this->validateKuotaDokter($model, false, $is_from_antrian);
            } else {
                $this->validateKuotaPoli($model);
            }
        }
        
        // penambahan pengecekan pasien_id null untuk pasien baru
        if (!is_null($model->pasien_id)) {
            $this->validatePasien($model);
        };

        if($this->konfig['is_nourut'] == true || $this->konfig['is_nourut'] == 'true') {
            $pickedNoUrut = ArrayHelper::getValue($post, 'nomor_urut');
            $this->getNomorUrut($model, $post, $pickedNoUrut);
        }
        
        /** (New) set ke antrian via trigger */
        $dataAntrian = $this->setAntrian($model);

        $this->setAdditionalData($model, $post , $dataAntrian);

        $model->keterangan = isset($this->konfig['keterangan_jkn']) ? $this->konfig['keterangan_jkn'] : null;

        if ($model->save()) {
            if($this->isJkn) {
                return $this->saveJkn($model, $post);
            } else {
                if ($this->konfig['is_support_jkn']) {
                    $jknRes = $this->sendToJkn($model->pendaftaranol_id);
                    if ($this->groupCarabayar == DocoConstants::GROUP_BPJS) {
                        if (isset($jknRes['jkn']['metadata'])) {
                            if ($jknRes['jkn']['metadata']['code'] != 200 || $this->skipErrorJkn == FALSE) {
                                $this->cancelDBTransaction();
                                return $this->validationJknAntrian(false, $jknRes['jkn']['metadata']['message']);
                            }
                        } else {
                            $this->cancelDBTransaction();
                            return $this->validationJknAntrian(false, 'Gagal Terhubung ke Server BPJS');
                        }
                    }
                    $res_jkn = [
                        'response' => $jknRes['jkn'],
                        'payload' => $jknRes['payload'],
                        'model' => $model->attributes
                    ];
                    $model->estimasidilayani = ArrayHelper::getValue($jknRes,'payload.estimasidilayani');
                    $model->additional_jkn = json_encode($res_jkn);
                    $model->save();

                    $this->updateAdditionalJkn($jknRes, $model->pendaftaranol_id);
                }
                $this->commitDBTransaction();
                if ($reservasiPoliklinik) {
                    return [
                        'data' => $this->actionGetRiwayatPendaftaran($model->pendaftaranol_id, $model->created_by, $additional),
                        'message' => Yii::t('app', 'Data Berhasil di simpan.')
                    ];
                }
    
                $model = $this->actionGetRiwayatPendaftaran($model->pendaftaranol_id, $model->created_by, $additional);
                return DocoHelpers::response($model);
            }
        } else {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => Yii::t('app', 'Data gagal disimpan.'),
            ];
        }
    }

    protected function getIdJadwal() {
        $konfig = $this->actionGetSettingKuota();

        switch ($konfig) {
            case DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK:
                $id = 'jadwalbukapoli_id';
                break;
            case DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER:
                $id = 'jadwaldokter_id';
                break;
            case DocoConstants::VAR_ID_TANPA_KUOTA:
                $id = 'jadwaldokter_id';
                break;
            default:
                $id = 'jadwaldokter_id';
                break;
        }

        return $id;
    }

    protected function actionGetSettingKuota()
    {
        $model = Cache::getKonfigSystem();
        return $model['kuota_antrian'];
    }

    protected function actionGetRiwayatPendaftaran($id = null, $user_id = null, $additional = null)
    {
        $model = InfoPendaftaranOnlineView::find()->select([
            'pendaftaranol_id',
            'pasien_id',
            'ruangan_id',
            'pegawai_id',
            'carabayar_id',
            'no_pendaftaranol',
            'tgl_pendaftaran',
            'tgl_kunjungan',
            'jam_kunjungan',
            'nama_pasien',
            'no_rekam_medik',
            'tanggal_lahir',
            'nama_pegawai',
            'carabayar_nama',
            'ruangan_nama',
            'status_daftar',
            'no_antrian AS no_antrian', //old antrian_dokter
            'antrian_dokter_id',
            'antrian_dokter',
            'nama_pasien_ol',
            'is_postranap'
        ])
        ->orderBy(['pendaftaranol_id' => SORT_DESC])
        ->asArray();

        if ($id) {
            $model->andWhere(['pendaftaranol_id' => $id]);
        }

        if ($user_id) {
            $model->andWhere(['created_by' => $user_id]);
        }

        $pendaftaran_online = $model->all();

        if (!empty($pendaftaran_online)) {
            foreach ($pendaftaran_online as $key => $value) {
                $pendaftaran_online[$key]['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])), false, false);
                $pendaftaran_online[$key]['tgl_pendaftaran'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, false);
                $pendaftaran_online[$key]['tgl_kunjungan'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_kunjungan'])), false, false);
                $jam_kunjungan = explode('-', $value['jam_kunjungan']);
                $pendaftaran_online[$key]['jam_kunjungan'] = date('H:i', strtotime($jam_kunjungan[0])).'-'.date('H:i', strtotime($jam_kunjungan[1]));

                // Generate nomor antrian pendaftaran
                $prefix = substr($value['no_pendaftaranol'], 0, 2);
                $number = substr($value['no_pendaftaranol'], 10);
                $no_antrian_pendaftaran = $prefix.$number;
                $pendaftaran_online[$key]['no_antrian_pendaftaran'] = $no_antrian_pendaftaran;
                $pendaftaran_online[$key]['additional_data'] = $additional;
            }

            return $pendaftaran_online;
        } else {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => Yii::t('app', 'Data riwayat pendaftaran online tidak ditemukan.'),
                'status' => 500
            ];
        }
    }

    protected function setPrefixAntrian()
    {
        $konfigAntrian = KonfigAntrian::find()->where([
            'jenisantrian_id'   => DocoConstants::VAR_JA_P,
            'groupcarabayar_id' => $this->groupCarabayar
        ])->asArray()->one();

        if($konfigAntrian) {
            $this->prefixKonfig = $konfigAntrian['kode_antrian'];
        } else {
            throw new ValidationException(422, $this->_error, [
                'text' => Yii::t('app', 'Konfig antrian tidak ditemukan.'),
            ]);
        }
    }

    protected function getDataJkn()
    {
        return AntrianjknV::find();
    }

    protected function validateKuotaDokter($model, $jadwalChange = false, $is_from_antrian = false)
    {
        if($this->konfig['is_reservasi'] == true) { //pisah kuota
            $attr = 'kuota_online';
        } else {
            $attr = 'kuota_total'; 
        }

        if($jadwalChange == true) {
            if($attr == 'kuota_online') {
                $query = "
                SELECT 
                    A.jadwaldokter_id,
                    A.kuota_masuk - COUNT ( b.jadwaldokter_id ) AS kuota_online 
                FROM
                    kuotadokter_r
                    A LEFT JOIN pendaftaranol_t b ON A.jadwaldokter_id = b.jadwaldokter_id 
                    AND DATE ( b.tgl_pendaftaranol :: DATE ) = '{$model->tgl_pendaftaranol}' AND status_daftar_ol != 566
                WHERE
                    A.jadwaldokter_id = {$model->jadwaldokter_id} 
                    AND is_online = TRUE 
                GROUP BY
                    A.jadwaldokter_id,
                    A.kuota_masuk
                ";
            }
        } else {
            $query = "
                SELECT 
                    j.jadwaldokter_id,
                    j.kuota - count(p.jadwaldokter_id) AS kuota,
                    j.kuota_online -  count(p.jadwaldokter_id) AS kuota_online,
                    j.kuota_total -  count(p.jadwaldokter_id) as kuota_total
                FROM infojadwaldokter_v j
                LEFT JOIN pendaftaranol_t p 
                    ON j.jadwaldokter_id = p.jadwaldokter_id AND DATE(p.tgl_pendaftaranol) = '{$model->tgl_pendaftaranol}' AND status_daftar_ol != 566
                WHERE j.ruangan_id = '{$model->ruangan_id}' AND j.hari_jadwalbuka = '{$this->hariId}' AND j.jadwaldokter_id = '{$model->jadwaldokter_id}'
                GROUP BY j.jadwaldokter_id, j.kuota, j.kuota_online, j.kuota_total
            ";
        }

        $queryAll = Yii::$app->db->createCommand($query)->queryAll();

        if(empty($queryAll) && !isset($queryAll[0])) {
            throw new ValidationException(422, $this->_error, [
                'text' => Yii::t('app', 'Jadwal tidak ditemukan.'),
            ]);
        }

        if ($is_from_antrian == false) {
            if ($queryAll[0][$attr]<1) {
                throw new ValidationException(422, $this->_error, [
                    'text' => Yii::t('app', 'Kuota telah habis.'),
                ]);
            }
        }
        
    }

    protected function validateKuotaPoli($model)
    {
        if($this->konfig['is_reservasi'] == true) { //pisah kuota
            $attr = 'kuota_online';
        } else {
            $attr = 'kuota_total'; 
        }

        $query = "
                SELECT 
                    j.jadwalbukapoli_id,
                    j.maxantrian_poli - count(p.jadwaldokter_id) AS kuota_offline,
                    j.kuota_online -  count(p.jadwaldokter_id) AS kuota_online,
                    (j.maxantrian_poli +  j.kuota_online) - count(p.jadwaldokter_id) as kuota_total
                FROM jadwalbukapoli_m j
                LEFT JOIN pendaftaranol_t p 
                    ON j.jadwalbukapoli_id = p.jadwalbukapoli_id AND DATE(p.tgl_pendaftaranol) = '{$model->tgl_pendaftaranol}'
                WHERE j.ruangan_id = '{$model->ruangan_id}' AND j.hari = '{$this->hariId}' AND j.jadwalbukapoli_id = '{$model->jadwalbukapoli_id}'
                GROUP BY j.jadwalbukapoli_id, j.kuota, j.kuota_online, j.kuota_total
            ";

        $queryAll = Yii::$app->db->createCommand($query)->queryAll();

        if(empty($queryAll) && !isset($queryAll[0])) {
            throw new ValidationException(422, $this->_error, [
                'text' => Yii::t('app', 'Jadwal tidak ditemukan.'),
            ]);
        }

        if ($queryAll[0][$attr]<1) {
            throw new ValidationException(422, $this->_error, [
                'text' => Yii::t('app', 'Kuota telah habis.'),
            ]);
        }
    }

    protected function validateKuotaJkn($model, $is_from_antrian = false)
    {
        $queryAll = (new PendaftaranOnline)->kuotaJkn($model->jadwaldokter_id, $model->ruangan_id, $model->tgl_pendaftaranol);

        if(empty($queryAll) && !isset($queryAll)) {
            throw new ValidationException(422, $this->_error, [
                'text' => Yii::t('app', 'Jadwal tidak ditemukan.'),
            ]);
        }

        if($this->groupCarabayar == DocoConstants::GROUP_UMUM) {
            $attr = 'kuota_nonbpjs_online';
        } else {
            // Ini untuk kondisi pasien bpjs onsite
            if ($is_from_antrian) {
                $attr = 'kuota_bpjs_offline';
            } else {
            // dari mjkn / reservasi bpjs online
                $attr = 'kuota_bpjs_online';
            }
        }

        if ($queryAll[$attr]<1) {
            throw new ValidationException(422, $this->_error, [
                'text' => Yii::t('app', 'Kuota telah habis.'),
            ]);
        }

        $this->kuotaDhelath = $queryAll;
    }

    protected function validatePasien($model)
    {
        $is_mobile = Yii::$app->jwt->is_mobile;
        if($model->jadwaldokter_id != null) {
            $whereClause = [
                'DATE(tgl_pendaftaranol)' => $model->tgl_pendaftaranol,
                'jadwaldokter_id'         => $model->jadwaldokter_id,
                'pasien_id'               => $model->pasien_id,
            ];
        } else {
           $whereClause = [
                'DATE(tgl_pendaftaranol)' => $model->tgl_pendaftaranol,
                'jadwalbukapoli_id'         => $model->jadwalbukapoli_id,
                'pasien_id'               => $model->pasien_id,
            ];
        }

        $queryCek = PendaftaranOnline::find()->select(['pendaftaranol_id', 'pendaftaran_id', 'rm.ruangan_id', 'rm.ruangan_nama'])
                        ->leftJoin('ruangan_m as rm', 'rm.ruangan_id = pendaftaranol_t.ruangan_id')->where($whereClause)->andWhere(['<>', 'status_daftar_ol', StatusReservasi::DITOLAK])->asArray()->one();
        if(!empty($queryCek)) {
            throw new ValidationException(422, $this->_error, [
                'text' => Yii::t('app', 'Pasien ini sudah memiliki reservasi di '.$queryCek['ruangan_nama'].', Silahkan menghubungi Admisi'),
            ]);
        }
    }

    protected function sendToJkn($id)
    {
        $request = [];
        $data = $this->getDataJkn()
            ->where([
                'pendaftaranol_id' => $id
            ])
            ->asArray()
            ->one();

        if($data) {
            // override value
            $getDataAdditional = $this->_getInfoPendaftaran($id);

            if($this->kuotaDhelath != null){
                $data['sisakuotajkn']= $this->kuotaDhelath['kuota_bpjs_online'];
                $data['kuotajkn']= $this->kuotaDhelath['master_kuota_bpjs_online'];
                $data['sisakuotanonjkn']= $this->kuotaDhelath['kuota_nonbpjs_online'];
                $data['kuotanonjkn']= $this->kuotaDhelath['master_kuota_nonbpjs_online'];
            }
            $data['sisakuotajkn']= ArrayHelper::getValue($getDataAdditional,'sisakuotajkn',0);
            $data['kuotajkn']= ArrayHelper::getValue($getDataAdditional,'kuotajkn',0);
            $data['sisakuotanonjkn']= ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn',0);
            $data['kuotanonjkn']= ArrayHelper::getValue($getDataAdditional,'kuotanonjkn',0);
            $tglestimasi = date('Y-m-d', strtotime($getDataAdditional['tgl_pendaftaran']));
            $jamestimasi = date('H:i:s', strtotime($getDataAdditional['jammulai']));
            if($this->isJkn){
                $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0);
                $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotajkn', 0);
            }else{
                $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotanonjkn', 0);
                $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn', 0);
            }
            
            $totalAntrianPoli = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0) + ArrayHelper::getValue($getDataAdditional,'kuotanonjkn', 0);
            $sisaAntrianPoli = ArrayHelper::getValue($getDataAdditional,'sisakuotajkn', 0) + ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn', 0);
            
            $spm = ArrayHelper::getValue($getDataAdditional,'estimasidilayani', 15);
            $waktuestimasi = $tglestimasi . " " . $jamestimasi;
            $noUrut = ($totalAntrian - $sisaAntrian);
            $noUrutPoli = ($totalAntrianPoli - $sisaAntrianPoli);
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
            $tglSekarang = date('Y-m-d');
            if(strtotime($tglestimasi) > strtotime($tglSekarang)){
                $tglJamSekarang = $tglSekarang . ' ' . $jamSekarang;
            }else{
                $tglJamSekarang = $tglestimasi . ' ' . $jamSekarang;
            }
            $waktuestimasi = $tglestimasi . ' ' . $jamestimasi;
            $urutanAntrianDiambil = $noUrut>0 ? $noUrut-1 : $noUrut;
            $currentTimeSlot = (date_create($waktuestimasi)->getTimestamp()) + (
                (($spm * $urutanAntrianDiambil) * 60)
            );

            $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                (($spm * $noUrutPoli) * 60)
            );

            if(strtotime($tglJamSekarang) > strtotime($waktuestimasi)){
                if($currentTimeSlot < strtotime($tglJamSekarang)){
                    $interval = date_diff(date_create($waktuestimasi),date_create($tglJamSekarang));
                    $urutanSekarang = ceil(((($interval->h * 60)+$interval->i) / $spm));

                    $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                        (($spm * $urutanSekarang) * 60)
                    );
                }
            }

            $data['estimasidilayani'] = $timestampsecond * 1000;
            $request = [
                'kodebooking' => $data['kodebooking'],
                'jenispasien' => $data['jenispasien'],
                'nomorkartu' => $data['nomorkartu'] ? $data['nomorkartu'] : '',
                'nik' => $data['no_identitas_pasien'] ? $data['no_identitas_pasien'] : '',
                'nohp' => $data['no_telepon_pasien'] ? $data['no_telepon_pasien'] : '',
                'kodepoli' => $data['kodepoli'],
                'namapoli' => $data['namapoli'],
                'pasienbaru' => $data['status_pasien'],
                'norm' => $data['no_rekam_medik'],
                'tanggalperiksa' => date('Y-m-d', strtotime($data['tanggal_periksa'])),
                'kodedokter' => (int) $data['kodedokter'],
                'namadokter' => $data['namadokter'],
                'jampraktek' => $data['jampraktek'],
                'jeniskunjungan' => $data['jeniskunjungan'] ? (int) $data['jeniskunjungan'] : '',
                'nomorreferensi' => $data['nomorreferensi'],
                'nomorantrean' => $data['nomorantrean'],
                'angkaantrean' => $data['angkaantrean'],
                'estimasidilayani' => $timestampsecond * 1000,
                'sisakuotajkn' => $data['sisakuotajkn'],
                'kuotajkn' => $data['kuotajkn'],
                'sisakuotanonjkn' => $data['sisakuotanonjkn'],
                'kuotanonjkn' => $data['kuotanonjkn'],
                'keterangan' => $data['keterangan'],
            ];

            $send = (new BpjsJkn)->simpanAntrianJkn($request);
            Yii::error([
                'identifier' => 'postman-jkn',
                'data' => $data,
                'response' => $send
            ]);

            $logjknr = new LoginJknR;
            $logjknr->pendaftaranol_id = $id;
            $logjknr->state = null;
            $logjknr->created_date = date('Y-m-d H:i:s');
            $logjknr->created_by = null;
            $logjknr->payload = isset($request) ? json_encode($request) : null;
            $logjknr->sync_respon = isset($send) ? json_encode($send) : null;
            $logjknr->save(false);

            $request['antrian_id'] = @$data['antrian_id'];

        }
        if(empty($data)){

            $logjknr = new LoginJknR;
            $logjknr->pendaftaranol_id = $id;
            $logjknr->state = null;
            $logjknr->created_date = date('Y-m-d H:i:s');
            $logjknr->created_by = null;
            $logjknr->payload = isset($this->dataJkn) ? json_encode($this->dataJkn) : null;
            $logjknr->sync_respon = json_encode([
                'data' => $data,
                'message' => 'Data payload Antrol tidak valid'
            ]);
            $logjknr->save(false);
        }
        return [
            'response' => $data,
            'jkn' => isset($send) ? $send : null,
            'payload' => $request
        ];
    }

    protected function responseJkn($id)
    {
        $response = [];
        $data = $this->getDataJkn()
            ->where([
                'pendaftaranol_id' => $id
            ])
            ->asArray()
            ->one();
            
        if($data) {
            // override value
            $getDataAdditional = $this->_getInfoPendaftaran($id);

            $data['sisakuotajkn']= ArrayHelper::getValue($getDataAdditional,'sisakuotajkn',0);
            $data['kuotajkn']= ArrayHelper::getValue($getDataAdditional,'kuotajkn',0);
            $data['sisakuotanonjkn']= ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn',0);
            $data['kuotanonjkn']= ArrayHelper::getValue($getDataAdditional,'kuotanonjkn',0);
            $tglestimasi = date('Y-m-d', strtotime($getDataAdditional['tgl_pendaftaran']));
            $jamestimasi = date('H:i:s', strtotime($getDataAdditional['jammulai']));
            if($this->isJkn){
                $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0);
                $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotajkn', 0);
            }else{
                $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotanonjkn', 0);
                $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn', 0);
            }
            
            $totalAntrianPoli = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0) + ArrayHelper::getValue($getDataAdditional,'kuotanonjkn', 0);
            $sisaAntrianPoli = ArrayHelper::getValue($getDataAdditional,'sisakuotajkn', 0) + ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn', 0);
            
            $spm = ArrayHelper::getValue($getDataAdditional,'estimasidilayani', 15);
            $waktuestimasi = $tglestimasi . " " . $jamestimasi;
            $noUrut = ($totalAntrian - $sisaAntrian);
            $noUrutPoli = ($totalAntrianPoli - $sisaAntrianPoli);
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
            $tglSekarang = date('Y-m-d');
            if(strtotime($tglestimasi) > strtotime($tglSekarang)){
                $tglJamSekarang = $tglSekarang . ' ' . $jamSekarang;
            }else{
                $tglJamSekarang = $tglestimasi . ' ' . $jamSekarang;
            }
            $waktuestimasi = $tglestimasi . ' ' . $jamestimasi;
            $urutanAntrianDiambil = $noUrut>0 ? $noUrut-1 : $noUrut;
            $currentTimeSlot = (date_create($waktuestimasi)->getTimestamp()) + (
                (($spm * $urutanAntrianDiambil) * 60)
            );

            $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                (($spm * $noUrutPoli) * 60)
            );

            if(strtotime($tglJamSekarang) > strtotime($waktuestimasi)){
                if($currentTimeSlot < strtotime($tglJamSekarang)){
                    $interval = date_diff(date_create($waktuestimasi),date_create($tglJamSekarang));
                    $urutanSekarang = ceil(((($interval->h * 60)+$interval->i) / $spm));

                    $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                        (($spm * $urutanSekarang) * 60)
                    );
                    
                    /**
                     * comment sementara karena error
                     *
                    $dataAntrianPoli = Yii::$app->db->createCommand("
                        SELECT 
                            count(antrian_id) as total_antrian
                        FROM antrian_t 
                        WHERE jenisantrian_id = :jenisantrian_id
                        AND antrian_t.jadwaldokter_id = :jadwaldokter_id
                        AND tgl_antrian::date = :tgl_antrian
                        AND estimasidilayani >= :estimasidilayani
                        AND antrian_t.is_deleted is false 
                    ")
                    ->bindValue(':jenisantrian_id',DocoConstants::VAR_JA_P)
                    ->bindValue(':jadwaldokter_id',$jadwaldokter_id)
                    ->bindValue(':tgl_antrian', $tgl_antrian)
                    ->bindValue(':estimasidilayani', $timestampsecond * 1000);
                    $dataAntrianPoli = $dataAntrianPoli->queryOne();
                    
                    if (ArrayHelper::getValue($dataAntrianPoli, 'total_antrian', 0) > 0) {
                        $urutanSekarang += ArrayHelper::getValue($dataAntrianPoli, 'total_antrian') ;
                        // recalculate timestamp dengan urutan terbaru
                        $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                            (($spm * $urutanSekarang) * 60)
                        );
                    }
                    */
                }
            }

            $data['estimasidilayani'] = $timestampsecond * 1000;

            //$estimasi = DocoHelpers::generateTimeStamp($waktuestimasi);
            $response = [
                'nomorantrean' => $data['nomorantrean'],
                'angkaantrean' => $data['angkaantrean'],
                'kodebooking' => $data['kodebooking'],
                'norm' => $data['no_rekam_medik'],
                'no_rekam_medik' => $data['no_rekam_medik'],
                'namapoli' => $data['namapoli'],
                'namadokter' => $data['namadokter'],
                'estimasidilayani' => $timestampsecond  * 1000,
                'sisakuotajkn' => $data['sisakuotajkn'],
                'kuotajkn' => $data['kuotajkn'],
                'sisakuotanonjkn' => $data['sisakuotanonjkn'],
                'kuotanonjkn' => $data['kuotanonjkn'],
                'keterangan' => $data['keterangan'],
            ];

            Yii::error([
                'identifier' => 'mobile-jkn',
                'data' => $data,
                'response' => $response
            ]);
        }

        return [
            'response' => $response,
            'jkn' => $response,
            'payload' => $data
        ];
    }

    protected function getPasienFromRm($no_rekam_medik) {
        // Remove special characters from no RM
        $noMr = preg_replace('/[^a-zA-Z0-9]/s', '', $no_rekam_medik);
        $data = Pasien::find()
            ->select(['pasien_id', 'no_rekam_medik'])
            // ->where(['no_rekam_medik' => $no_rekam_medik])
            ->andFilterWhere(['or',
            ['no_rekam_medik'=> $noMr],
            ['no_rm_old'=> $noMr]])
            ->asArray()
            ->one();

        return $data;
    }

    protected function validationNewPatient() 
    {
        throw new ValidationException(202, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => Yii::t('app', 'Data pasien ini tidak ditemukan, silahkan Melakukan Registrasi Pasien Baru'),
        ]);
    }

    protected function validationPoliTutup($isJkn = false)
    {
        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => Yii::t('app', 'Pendaftaran ke Poli Ini Sedang Tutup'),
        ]);
    }

    protected function validationJadwalJkn($isJkn = false, $nama_dokter)
    {
        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => Yii::t('app', 'Jadwal Dokter ' .$nama_dokter. 'Tersebut Belum Tersedia, Silahkan Reschedule Tanggal dan Jam Praktek Lainnya'),
        ]);
    }
    
    protected function validationJamPoliTutup($isJkn = false, $data)
    {
        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => Yii::t('app', 'Pendaftaran Ke Poli ' .$data['ruangan_nama']. ' Sudah Tutup Jam ' .@$data['waktu_mulai']. '-' .@$data['waktu_tutup'])
        ]);
    }

    protected function validationSlotDokter($isJkn = false)
    {
        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => Yii::t('app', 'Slot / Waktu pelayanan dokter tidak tersedia.')
        ]);
    }

    protected function validationSlotAvailibility($isJkn = false)
    {
        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => Yii::t('app', 'Slot sudah dibooking.')
        ]);
    }

    protected function validationDoubleReservation($isJkn = false)
    {
        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => Yii::t('app', 'Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal Yang Sama')
        ]);
    }

    protected function validationJknAntrian($isJkn = false, $message)
    {
        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
            'text' => $message
        ]);
        $result = [
            'status' => 201,
            'title'  => 'Proses Gagal',
        ];

        if ($isJkn) {
            $result['message'] = $message;
        } else {
            $result['status'] = 422;
            $result['text'] = $message;
        }
        Yii::error($result);
        return $result;
    }

    protected function updateAdditionalJkn($data, $pendaftaranol_id)
    {
        $additionalJkn = [
            'response' => $data['jkn'],
            'payload' => $data['payload']
        ];
        $additionalJkn = json_encode($additionalJkn);

        Yii::$app->db->createCommand("
            UPDATE antrianjkn_r SET additional_jkn = '{$additionalJkn}' WHERE pendaftaranol_id = ({$pendaftaranol_id})
        ")->execute();
    }

    /**
     * @method method ini dipanggil untuk set var global dipanggil setelah set attr model
     * @param object $model model nya
     * @param array $post dari request
     * 
     * @return void
     */
    protected function setToGlobal($model, $post)
    {
        $tanggal         = $model->tgl_pendaftaranol;
        $this->isJkn = ArrayHelper::getValue($post, 'jkn');
        $this->hariId         = date('N', strtotime($tanggal)) + 74;
        $this->date = date('Y-m-d', strtotime($model->tgl_pendaftaranol));
        $this->currDate = date('Y-m-d', strtotime('NOW'));
        $this->noUrut = null;
        $this->slotSequence = null;
        $this->dataJkn = [];
        $this->konfig = Cache::getKonfigSystem();
        $this->jadwalChange = false;
        $this->skipErrorJkn = ArrayHelper::getValue($post, 'skipErrorJkn');

        if($model->carabayar_id != null) {
            $getGroupCarabayar = CaraBayar::find()
                    ->select(['groupcarabayar_id'])
                    ->where(['carabayar_id' => $model->carabayar_id])
                    ->asArray()
                    ->one();
            $this->groupCarabayar = $getGroupCarabayar['groupcarabayar_id'] ?: DocoConstants::GROUP_UMUM;
        } else {
            $this->groupCarabayar = DocoConstants::GROUP_UMUM; 
        }
    }

    /**
     * @method method ini dipanggil untuk set attr yang gak ke set
     * @param object $model (Awas ini pointer ya)
     * @param array $post dari request
     * 
     * @return void
     */
    protected function setToModel(&$model, $post) 
    {
        $post_rujukan = ArrayHelper::getValue($post, 'no_rujukan');
        $no_rujukan = empty($post_rujukan) ? $this->getRujukanByJenis(@$post['nomorreferensi'],@$post['jeniskunjungan']) : $post_rujukan;

        $tipePasien = empty($post['tipe_pasien']) ? 0 : $post['tipe_pasien'];
        $model->attributes = $post;
        $model->status_pasien = $tipePasien == 0 ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B; 
        $model->status_daftar_ol = DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES; // Belum Diproses
        $model->created_by = ArrayHelper::getValue($post, 'user_id');
        $curTime = date('H:i:s', strtotime('now'));
        $model->tgl_pendaftaranol = date('Y-m-d H:i:s', strtotime($post['tgl_pendaftaranol']. ' ' .$curTime));
        $model->no_rujukan = $no_rujukan;
        // $model->no_bpjs = empty($post['no_bpjs']) ? null : $post['no_bpjs'];
        // $model->no_bpjs = empty($post['nomorkartu']) ? $model->no_bpjs : $post['nomorkartu'];
        $model->no_bpjs = ArrayHelper::getValue($post, 'nomorkartu', isset($post['no_bpjs']) ? $post['no_bpjs'] : null);
        $model->additional_data = null;
        $model->no_rekam_medik = ArrayHelper::getValue($post, 'mrid');
        $model->jenis_reservasi =  ArrayHelper::getValue($post, 'jenis_reservasi', DocoConstants::JENIS_RESERVASI_SIRS);
        $model->is_checkin =  ArrayHelper::getValue($post, 'is_checkin', false);
        $model->tgl_checkin =  ArrayHelper::getValue($post, 'tgl_checkin');
        $model->carabayar_id =  ArrayHelper::getValue($post, 'carabayar_id');
        $model->penjamin_id =  ArrayHelper::getValue($post, 'penjamin_id');
        $model->benefit_code =  ArrayHelper::getValue($post, 'benefit_code');
        $model->transaction_id =  ArrayHelper::getValue($post, 'transaction_id');
        $model->no_asuransi =  ArrayHelper::getValue($post, 'no_asuransi');
        $model->no_telepon_pasien = preg_replace('/\s+/','', ArrayHelper::getValue($post, 'no_telepon_pasien'));
    }

    protected function getRujukanByJenis($nomorreferensi = null, $jeniskunjungan = null)
    {
        $_no_rujukan = $nomorreferensi;
        if(!empty($nomorreferensi) && !empty($jeniskunjungan)){
            if($jeniskunjungan == DocoConstants::RUJUK_KONTROL){
                $result = (new Bpjs)->cariSuratKontrol($nomorreferensi);
                $rujukankontrol = ArrayHelper::getValue($result,'response.sep.provPerujuk.noRujukan');
                if(!empty($rujukankontrol)){
                    $_no_rujukan = $rujukankontrol;
                }
            }
        }

        return $_no_rujukan;
    }

    /**
     * @method method ini dipanggil untuk set data antrian
     * @param object $model model nya
     * 
     * @return array $dataAntrian
     */
    protected function setAntrian($model)
    {
        $dataAntrian = [
            'pasien_id' => !empty($model->pasien_id) ? $model->pasien_id : null,
            'ruangan_id' => $model->ruangan_id,
            'carabayar_id' => !empty($model->carabayar_id) ? $model->carabayar_id : null,
            'pendaftaran_id' => null, // Di Set Di Trigger
            'tgl_antrian' => $model->tgl_pendaftaranol,
            'no_antrian' => !empty($this->noUrut) ? $this->noUrut : null,
            'penjamin_id' => !empty($model->penjamin_id) ? $model->penjamin_id : null,
            'pegawai_id' => !empty($model->pegawai_id) ? $model->pegawai_id : null,
            'status_pasien' => $model->status_pasien,
            'jenisantrian_id' => DocoConstants::VAR_JA_P,
            'is_active' => false,
            'jadwaldokter_id' => null,
            'jadwalbukapoli_id' => null,
            'slot_sequence' => !empty($this->slotSequence) ? $this->slotSequence : null,
            'groupcarabayar_id' => $this->groupCarabayar
        ];

        if($model->jadwaldokter_id != null) {
            $dataAntrian['jadwaldokter_id'] = $model->jadwaldokter_id;
        } else {
            $dataAntrian['jadwalbukapoli_id'] = $model->jadwalbukapoli_id;
        }

        return $dataAntrian;
    }

    /**
     * @method method ini dipanggil ketika konfig nourut/no antrian dilakukan secara manual
     * @param object $model model nya
     * @param array $post dari request
     * 
     * @return void
     */
    protected function getNomorUrut($model, $post, $pickedNoUrut = null)
    {
        if(!empty($pickedNoUrut)) {
            $this->noUrut = $pickedNoUrut;
        } else {
            //** get no urut */
            $getNoUrut = (new RegistrationService)->getListNoUrut([
                'ruangan_id' => $model->ruangan_id,
                'dokter_id' => $model->pegawai_id,
                'date' => $model->tgl_pendaftaranol
            ]);

            if(!empty($getNoUrut)) {
                $loop = 0;
                foreach($getNoUrut as $key => $value) {
                    if($loop == 0) {
                        $this->noUrut = $key;
                        $loop++;
                    } else {
                        break;
                    }
                }
            } else {
                throw new ValidationException(500, DocoMessages::KEY_DYNAMIC_STATUS, [
                    'text' => Yii::t('app', 'Nomor urut tidak tersedia.'),
                ]);
            }
        }
    }

    /**
     * @method method ini dipanggil kalau regis/rsv via mobile jkn
     * @param object $model (Awas ini pointer ya!!!)
     * @param array $post dari request
     * 
     * @return void
     */
    protected function rsvJkn(&$model, $post)
    {
        /*
        *** BPJS mengijinkan reservasi di hari yg sama
        if($this->date == $this->currDate) {
            throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => Yii::t('app', 'Reservasi tidak boleh dilakukan di hari yang sama'),
            ]);
        }
        */

        $dateDiff = (new DocoHelpers)->getDiffDateTime($this->currDate, $this->date, true);
        $batasAkhir = (int) $this->konfig['reservasi_akhir_jkn'] - 1;
        if ($dateDiff['hari'] > $batasAkhir) {
            throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => Yii::t('app', 'Tidak bisa mendaftarkan lebih dari H+'.$batasAkhir.' dari hari ini'),
            ]);
        }

        if(!empty($post['no_rekam_medik'])) {
            $pasien = $this->getPasienFromRm($post['no_rekam_medik']);
            if(!empty($pasien)) {
                $model->no_rekam_medik = $pasien['no_rekam_medik'];
                $model->status_pasien = DocoConstants::VAR_PAS_L;
                $model->pasien_id = $pasien['pasien_id'];
            } else {
                return $this->validationNewPatient();
            }
        } else {
            $peserta = (new Bpjs)->peserta($post['nomorkartu']);
            if(!empty($peserta)) {
                $noMr = ArrayHelper::getValue($peserta, 'response.peserta.mr.noMR');
                if(!empty($noMr)) {
                    $pasien = $this->getPasienFromRm($noMr);
                    if(!empty($pasien)) {
                        $model->no_rekam_medik = $pasien['no_rekam_medik'];
                        $model->status_pasien = DocoConstants::VAR_PAS_L;
                        $model->pasien_id = $pasien['pasien_id'];
                    } else {
                        return $this->validationNewPatient();
                    }
                } else {
                    return $this->validationNewPatient();
                }
            } else {
                return $this->validationNewPatient();
            }
        }

        $dataDokter = Pegawai::find()
            ->where([
                'kode_dokter_bpjs' => $post['kodedokter']
            ])
            ->asArray()
            ->one();
            
        if (empty($dataDokter)) {
            throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => Yii::t('app', 'Kode dokter tidak di temukan di sirs'),
            ]);
        }

        //get jadwal HFIS
        $cekdoker = true;
        $getJadwalHfis = (new BpjsJkn)->referensiJadwalDokterJkn($post['kodepoli'], $this->date);
        if(!empty($getJadwalHfis)) {
            $cekdoker = false;
            if (!empty($getJadwalHfis['response']))
            {
                // foreach ($getJadwalHfis['response'] as $key => $value) {
                //     if ($value['kodedokter'] == $post['kodedokter'] && $value['jadwal'] == $post['jampraktek']){
                        $cekdoker = true;
                //     }
                // }
            }

            if ($cekdoker == false){
                // return $this->validationPoliTutup(true);
            }
        } 

        /**Get jadwal*/
        $jamPraktek = isset($post['jampraktek']) ? explode("-", $post['jampraktek']) : null;
        $jamPraktekMulai = empty($jamPraktek) ? null : date('H:i:s', strtotime($jamPraktek[0]));
        $jamPraktekTutup = empty($jamPraktek) ? null : date('H:i:s', strtotime($jamPraktek[1]));
        $getData = (new PendaftaranOnline)->getJadwalJkn($this->hariId, $post['kodedokter'], $post['kodepoli'], $jamPraktekMulai, $jamPraktekTutup);
        if(!$getData) {
            return $this->validationJadwalJkn(true, $dataDokter['nama_pegawai']);
        } 
        $model->jadwaldokter_id = $getData['jadwaldokter_id'];
        $model->pegawai_id = $getData['pegawai_id'];
        $model->ruangan_id = $getData['ruangan_id'];
        $this->dataJkn = [
            'nomorkartu' => $post['nomorkartu'],
            'jenis_cara_bayar' => $post['jenis_cara_bayar'],
            'nomorreferensi' => $post['nomorreferensi'],
            'keterangan' => isset($this->konfig['keterangan_jkn']) ? $this->konfig['keterangan_jkn'] : null,
            'no_rekam_medik' => !empty($pasien) ? $pasien['no_rekam_medik'] : $post['no_rekam_medik'],
            'jeniskunjungan' => $post['jeniskunjungan'],
        ];

        if($this->date == $this->currDate) {
            $time = date('H:i', strtotime('NOW'));
            if($time > $getData['waktu_tutup']) {
                return $this->validationJamPoliTutup(true, $getData);
            }
            $getSlot = (new PendaftaranOnline)->getLastSequenceJknSameDate($model->jadwaldokter_id, $this->date, $time);
        } else {
            $getSlot = (new PendaftaranOnline)->getLastSequenceJkn($model->jadwaldokter_id, $this->date);
        }

        if(empty($getSlot)) {
            return $this->validationSlotDokter(true);
        }

        $this->slotSequence = $getSlot['slot_sequence'];
        $checkSlotAvail =  (new PendaftaranOnline)->validateSlotJkn($model->jadwaldokter_id, $this->date, $this->slotSequence);

        if(!empty($checkSlotAvail)) {
            return $this->validationSlotAvailibility(true);
        }

        $checkPasienTerdaftar =  (new PendaftaranOnline)->cekPendaftaranPasien($model->no_bpjs, $this->date);

        if(!empty($checkPasienTerdaftar)) {
            return $this->validationDoubleReservation(true);
        }
    }

    /**
     * @method : method ini dipanggil kalau regis/rsv tanpa mobile jkn dengan konfigurasi jkn aktif,
     * hal ini perlu dilakukan karena flow jkn mengharuskan semua data baik yang regis dari mobile jkn/ bukan ttp mengirim data ke server jkn
     * @param object $model (Awas ini pointer ya!!!)
     * @param array $post dari request
     * 
     * @return void
     */
    protected function rsvJknKonfig(&$model, $post)
    {
        if ($model->status_pasien == DocoConstants::VAR_PAS_B) {
            throw new ValidationException(500, DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => 'Tidak dapat membuat reservasi untuk pasien baru.',
                'additional' => [
                    'is_pasien' => true
                ]
            ]);
        }

        $pasien = Pasien::find()
        ->where([
            'pasien_id' => $model->pasien_id
        ])
        ->asArray()
        ->one();

        $model->no_rekam_medik = $pasien['no_rekam_medik'];
        $model->no_bpjs = $pasien['nopeserta_bpjs'];
        $model->no_telepon_pasien = $pasien['no_telepon_pasien'];
        $model->jenisidentitas = $pasien['jenisidentitas'];
        $model->no_identitas_pasien = $pasien['no_identitas_pasien'];
        if (!empty($pasien['additional_pasien'])) {
            $additionalPasien = json_decode($pasien['additional_pasien'], true);
            foreach ($additionalPasien as $key => $value) {
                $model->jenisidentitas = $value['jenisidentitas'];
                $model->no_identitas_pasien = $value['no_identitas_pasien'];
            }
        }
        $jenisKunjungan = $model->jeniskunjungan ? $model->jeniskunjungan : DocoConstants::RUJUK_FKTP_JKN;
        $jenisRsv = $this->groupCarabayar == DocoConstants::GROUP_BPJS ? DocoConstants::J_P_JKN : DocoConstants::J_P_NONJKN;
        $jenisKunjungan = $this->groupCarabayar == DocoConstants::GROUP_BPJS ? $jenisKunjungan : DocoConstants::RUJUK_FKTP_JKN;

        $this->dataJkn = [
            'nomorkartu' => $model->no_bpjs ? $model->no_bpjs : $pasien['nopeserta_bpjs'],
            'jenis_cara_bayar' => $jenisRsv,
            'nomorreferensi' => $model->no_rujukan ? $model->no_rujukan : '',
            'keterangan' => $model->keterangan,
            'no_rekam_medik' => $pasien['no_rekam_medik'],
            'jeniskunjungan' => $jenisKunjungan,
        ];
        $estimasiByJadwal = (new PendaftaranOnline)->getEstimasiByJadwal($model->jadwaldokter_id,$model->tgl_pendaftaranol,$this->isJkn, $model->antrian_id);

        if($this->date == $this->currDate) {
            $time = date('H:i', strtotime('NOW'));
            $getSlot = (new PendaftaranOnline)->getLastSequenceJknSameDate($model->jadwaldokter_id, $this->date, $time);
        } else {
            $getSlot = (new PendaftaranOnline)->getLastSequenceJkn($model->jadwaldokter_id, $this->date);
        }
        $this->slotSequence = isset($getSlot['slot_sequence']) ? $getSlot['slot_sequence'] : null;
        if(empty($getSlot['slot_sequence'])){
            $this->slotSequence = isset($estimasiByJadwal['sequence']) ? $estimasiByJadwal['sequence'] : null;
        }

        $model->estimasidilayani = isset($estimasiByJadwal['estimasidilayani_inmilisecond']) ? $estimasiByJadwal['estimasidilayani_inmilisecond'] : null;

        // JKN validation for carabayar BPJS
        if ($this->groupCarabayar == DocoConstants::GROUP_BPJS) {
            $dataDokter = Pegawai::find()
            ->where([
                'pegawai_id' => $model->pegawai_id
            ])
            ->asArray()
            ->one();

            $dataPoli = Ruangan::find()
            ->where([
                'ruangan_id' => $model->ruangan_id
            ])
            ->asArray()
            ->one();
            
            //get jadwal HFIS
            $cekdoker = true;
            $getJadwalHfis = (new BpjsJkn)->referensiJadwalDokterJkn($dataPoli['kode_ruangan_bpjs'], $this->date);
            if(!empty($getJadwalHfis)) {
                $cekdoker = false;
                if (!empty($getJadwalHfis['response']))
                {
                    $cekdoker = true;
                }

                if ($cekdoker == false){
                    // return $this->validationPoliTutup();
                }
            } 

            /**Get jadwal*/
            $jamPraktek = isset($model->jam_kunjungan) ? explode("-", $model->jam_kunjungan) : null;
            $jamPraktekMulai = empty($jamPraktek) ? null : date('H:i:s', strtotime($jamPraktek[0]));
            $jamPraktekTutup = empty($jamPraktek) ? null : date('H:i:s', strtotime($jamPraktek[1]));
            $getData = (new PendaftaranOnline)->getJadwalJkn($this->hariId, $dataDokter['kode_dokter_bpjs'], $dataPoli['kode_ruangan_bpjs'], $jamPraktekMulai, $jamPraktekTutup);
            if(!$getData) {
                return $this->validationJadwalJkn(false, $dataDokter['nama_pegawai']);
            } 
            if($this->date == $this->currDate) {
                $time = date('H:i', strtotime('NOW'));
                if($time > $getData['waktu_tutup']) {
                    return $this->validationJamPoliTutup(false, $getData);
                }
            }

            if(empty($getSlot)) {
                return $this->validationSlotDokter();
            }
            $checkSlotAvail =  (new PendaftaranOnline)->validateSlotJkn($model->jadwaldokter_id, $this->date, $this->slotSequence);

            if(!empty($checkSlotAvail)) {
                return $this->validationSlotAvailibility();
            }

            $checkPasienTerdaftar =  (new PendaftaranOnline)->cekPendaftaranPasien($model->no_bpjs, $this->date);

            if(!empty($checkPasienTerdaftar)) {
                return $this->validationDoubleReservation();
            }
        }
    }

    /**
     * @method method ini dipanggil kalau regis/rsv via mobile jkn
     * @param object $model (Awas ini pointer ya!!!)
     * @param array $post dari request
     * @param array $dataAntrian dari method setAntrian()
     * 
     * @return void
     */
    protected function setAdditionalData(&$model,$post, $dataAntrian)
    {
        $additionals = [];
        
        $additionals = [
            'antrian' => $dataAntrian,
            'jkn' => $this->dataJkn
        ];

        if(isset($post['asal_rujukan'])) {
            $additionals['bpjs'] = [
                'no_kartu' => $post['no_bpjs'],
                'asal_rujukan' => $post['asal_rujukan'],
            ];
        }

        $model->additional_data = json_encode($additionals);
    }

    /**
     * @method method ini digunakan untuk save pendaftaran dari jkn
     * @param object $model 
     * 
     * @return array
     */
    protected function saveJkn($model, $post)
    {
        $str = strtolower("PostmanRuntime");
        $pattern = "/postman/i";

        $is_from_antrian = isset($post['is_from_antrian']) ? true: false;
        
        if ((isset($post['http_agent']) && preg_match($pattern, $post['http_agent']) == 1) || $is_from_antrian) { //dari postman
            $jknRes = $this->sendToJkn($model->pendaftaranol_id);
            if (isset($jknRes['jkn']['metadata']) || $this->skipErrorJkn == true) {
                if ((isset($jknRes['jkn']['metadata']['code']) && $jknRes['jkn']['metadata']['code'] == 200) || $this->skipErrorJkn == TRUE) {
                    $this->updateResJkn($jknRes);
                    $res_jkn = [
                        'response' => $jknRes['jkn'],
                        'payload' => $jknRes['payload'],
                        'model' => $model->attributes
                    ];
                    
                    $model->estimasidilayani = ArrayHelper::getValue($jknRes,'response.estimasidilayani');
                    $model->additional_jkn = json_encode($res_jkn);
                    $model->save();
                    $this->commitDBTransaction();

                    $this->updateAdditionalJkn($jknRes, $model->pendaftaranol_id);
                    return $jknRes;
                } else {
                    $this->cancelDBTransaction();
                    return $this->validationJknAntrian(true, $jknRes['jkn']['metadata']['message']);
                }
            } else {
                $this->cancelDBTransaction();
                return $this->validationJknAntrian(true, 'Gagal Terhubung ke Server BPJS');
            }
        } else {
            $jknRes = $this->responseJkn($model->pendaftaranol_id);
            if (!empty($jknRes)) {
                $this->updateResJkn($jknRes);
                $res_jkn = [
                    'response' => $jknRes['jkn'],
                    'payload' => $jknRes['payload'],
                    'model' => $model
                ];
                $model->estimasidilayani = ArrayHelper::getValue($jknRes,'response.estimasidilayani');
                $model->additional_jkn = json_encode($res_jkn);
                $model->save();
                $this->commitDBTransaction();
                $this->updateAdditionalJkn($jknRes, $model->pendaftaranol_id);
                return $jknRes;
            }
        }
    }

    /**
     * @method method ini digunakan untuk mengurangi kuota di response
     * @param array $jknRes (reference $jknres)
     * 
     * @return void|error
     */
    protected function updateResJkn(&$jknRes)
    {
        if($this->groupCarabayar == DocoConstants::GROUP_UMUM) {
            if($jknRes['response']['sisakuotanonjkn'] <= 0) {
                $this->validationJknAntrian(true, 'Kuota tidak tersedia');
            }
            $jknRes['response']['sisakuotanonjkn']--;
        } else {
            if($jknRes['response']['sisakuotajkn'] <= 0) {
                $this->validationJknAntrian(true, 'Kuota tidak tersedia');
            }
            $jknRes['response']['sisakuotajkn']--;
        }
    }

    protected function _getInfoPendaftaran($pendaftaranol_t)
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
                pt.tgl_pendaftaranol as tgl_pendaftaran,
                pt.jadwaldokter_id,
                pt.antrian_id,
                COALESCE(jm.kuota_bpjs_offline,0) as kuota_bpjs_offline,
                COALESCE(jm.kuota_bpjs_online,0) as kuota_bpjs_online,
                COALESCE(jm.kuota_nonbpjs_offline,0) as kuota_nonbpjs_offline,
                COALESCE(jm.kuota_nonbpjs_online,0) as kuota_nonbpjs_online,
                jm.jadwaldokter_mulai,
                jm.jadwaldokter_tutup,
                jm.jumlah_loaddokter as estimasidilayani
            from pendaftaranol_t pt  
            left join jadwaldokter_m jm on jm.jadwaldokter_id = pt.jadwaldokter_id 
            join pegawai_m pm on pm.pegawai_id  = pt.pegawai_id 
            join ruangan_m rm on rm.ruangan_id = pt.ruangan_id 
            where  pt.pendaftaranol_id = {$pendaftaranol_t};
        ")->queryOne();

        $pegawai_id = isset($dataPendaftaran['pegawai_id']) ? $dataPendaftaran['pegawai_id'] : 0 ;
        $ruangan_id = isset($dataPendaftaran['ruangan_id']) ? $dataPendaftaran['ruangan_id'] : 0;
        $tgl_pendaftaran = isset($dataPendaftaran['tgl_pendaftaran']) ? $dataPendaftaran['tgl_pendaftaran'] : 0;
        $jadwaldokter_id = isset($dataPendaftaran['jadwaldokter_id']) ? $dataPendaftaran['jadwaldokter_id'] : 0;
        $antrian_id = isset($dataPendaftaran['antrian_id']) ? $dataPendaftaran['antrian_id'] : 0;
        $waktu_pendaftaran = date('H:i:s', strtotime($tgl_pendaftaran));

        $tgl_reservasi = date('Y-m-d',strtotime($dataPendaftaran['tgl_pendaftaran']));

        $dataAntrian = Yii::$app->db->createCommand("
            SELECT 
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id=418 or carabayar_id = 6)) as jumlah_antrian_bpjs_online,
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id=418 or carabayar_id = 6)) as jumlah_antrian_bpjs_offline,
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id<>418 or carabayar_id <> 6)) as jumlah_antrian_nonbpjs_online,  
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id<>418 or carabayar_id <> 6)) as jumlah_antrian_nonbpjs_offline
            FROM antrian_t 
            LEFT JOIN (
                SELECT 
                    antrian_id as antrian_id_ol,
                    status_daftar_ol
                FROM pendaftaranol_t
                where is_deleted is false
            ) pt on pt.antrian_id_ol = antrian_t.antrian_id
            WHERE jenisantrian_id = 312
            AND antrian_t.jadwaldokter_id = :jadwaldokter_id
            AND tgl_antrian::date = :tgl_reservasi
            AND antrian_id <> :antrian_id
            AND antrian_t.is_deleted is false 
            AND pt.status_daftar_ol in (:VAR_STATUS_DAFTAR_OL_DISETUJUI, :VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES)
        ")
        ->bindValue(':jadwaldokter_id',$jadwaldokter_id)
        ->bindValue(':tgl_reservasi',$tgl_reservasi)
        ->bindValue(':antrian_id',$antrian_id)
        ->bindValue(':VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES', DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES)
        ->bindValue(':VAR_STATUS_DAFTAR_OL_DISETUJUI', DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI);
        $dataAntrian = $dataAntrian->queryOne();

        $kuota_bpjs_offline = ArrayHelper::getValue($dataPendaftaran,'kuota_bpjs_offline',0);
        $kuota_bpjs_online= ArrayHelper::getValue($dataPendaftaran,'kuota_bpjs_online',0);
        $kuota_nonbpjs_offline= ArrayHelper::getValue($dataPendaftaran,'kuota_nonbpjs_offline',0);
        $kuota_nonbpjs_online= ArrayHelper::getValue($dataPendaftaran,'kuota_nonbpjs_online',0);
        $kuotajkn = $kuota_bpjs_online + $kuota_bpjs_offline;
        $kuotanonjkn = $kuota_nonbpjs_offline + $kuota_nonbpjs_online;
        $jumlah_antrian_bpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_online',0);
        $jumlah_antrian_bpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_offline',0);
        $jumlah_antrian_nonbpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_online',0);
        $jumlah_antrian_nonbpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_offline',0);
        $sisakuotanonjkn = $kuotanonjkn - ($jumlah_antrian_nonbpjs_online + $jumlah_antrian_nonbpjs_offline);
        $sisakuotajkn = $kuotajkn - ($jumlah_antrian_bpjs_online + $jumlah_antrian_bpjs_offline);

        $sisa_kuota_nonbpjs_online = $kuota_nonbpjs_online - $jumlah_antrian_nonbpjs_online;
        $sisa_kuota_nonbpjs_offline = $kuota_nonbpjs_offline - $jumlah_antrian_nonbpjs_offline;
        $sisa_kuota_bpjs_online = $kuota_bpjs_online - $jumlah_antrian_bpjs_online;
        $sisa_kuota_bpjs_offline = $kuota_bpjs_offline - $jumlah_antrian_bpjs_offline;

        $jam_mulai = !empty($dataPendaftaran['jadwaldokter_mulai']) 
                        ? date('H:i', strtotime($dataPendaftaran['jadwaldokter_mulai'])) : '00:00';
        $jam_tutup = !empty($dataPendaftaran['jadwaldokter_tutup']) 
                        ? date('H:i', strtotime($dataPendaftaran['jadwaldokter_tutup'])) : '00:00';
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
            'tgl_pendaftaran' => $tgl_pendaftaran,
            'jumlah_antrian_bpjs_online' => $sisa_kuota_bpjs_online,
            'jumlah_antrian_bpjs_offline' => $sisa_kuota_bpjs_offline,
            'jumlah_antrian_nonbpjs_online' => $sisa_kuota_nonbpjs_online,
            'jumlah_antrian_nonbpjs_offline' => $sisa_kuota_nonbpjs_offline,
            'estimasidilayani' => isset($dataPendaftaran['estimasidilayani']) ? $dataPendaftaran['estimasidilayani'] : 6,
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
	            pt.tgl_pendaftaranol as tgl_pendaftaran
            from pendaftaranol_t pt  
            join pegawai_m pm on pm.pegawai_id  = pt.pegawai_id 
            join ruangan_m rm on rm.ruangan_id = pt.ruangan_id 
            where  pt.pendaftaranol_id = {$pendaftaranol_t};
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
            'tgl_pendaftaran' => $tgl_pendaftaran,
            'estimasidilayani' => isset($selectedJadwal['estimasidilayani']) ? $selectedJadwal['estimasidilayani'] : 0,
        ];
    }
}