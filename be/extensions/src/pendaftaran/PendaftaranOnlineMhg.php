<?php
/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\pendaftaran;

use Yii;
use yii\helpers\ArrayHelper;

use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\Services\RegistrationService;
use Doco\exceptions\ValidationException;
use Doco\models\pendaftaran\PendaftaranOnline;

class PendaftaranOnlineMhg extends \Doco\processes\PendaftaranOnlineProcess 
{
    protected function processFlow()
    {
        $reservasiPoliklinik = false;
        $saved = false;
        $isSmh = false;
        
        if (!empty(json_decode(Yii::$app->request->rawBody, true))) {
            $post = json_decode(Yii::$app->request->rawBody, true);
        } else {
            $post = Yii::$app->request->post();
            $reservasiPoliklinik = true;
        }

        $is_from_antrian = isset($post['is_from_antrian']) ? true: false;
        $new_patient_portal = isset($post['new_patient_portal']) && $post['new_patient_portal'] == true ? true: false;
        $this->isOnline = isset($post['is_online']) && !empty($post['is_online']) ? $post['is_online']: true;
        $this->startDBTransaction();

        $model = new PendaftaranOnline();
        $this->setToModel($model, $post);
        $this->setToGlobal($model, $post);
        
        $additional = ArrayHelper::getValue($post, 'additional_data');

        if($model->no_rekam_medik != null) {
            $getPasien = $this->getPasienFromRm($model->no_rekam_medik);
            if(!empty($getPasien)) {
                $model->pasien_id = $getPasien['pasien_id'];
            }
        }

        /**
         * ?@ : Appointment from MHG
         */
        if(isset($post['sirs_slot_id']) && !empty($post['sirs_slot_id'])) { // SMH
            $isSmh = true;
            $this->rsvSmh($model, $post);
            if($model->jadwaldokter_id != null){
                $this->validateKuotaDokter($model, $this->jadwalChange);
            } else {
                $this->validateKuotaPoli($model);
            }
        } else  if ($this->isJkn) { // JKN
            $this->rsvJkn($model, $post);
            $this->validateKuotaJkn($model, $is_from_antrian);
        } else { // RSV SIRS
            if ($this->konfig['is_support_jkn'] && $new_patient_portal == false) {
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

        if ($model->save()) {
            // $this->commitDBTransaction();
            // $this->startDBTransaction();
            if($this->isJkn) {
                return $this->saveJkn($model, $post);
            } else {
                if ($this->konfig['is_support_jkn'] && !$isSmh) {
                    $jknRes = $this->sendToJkn($model->pendaftaranol_id);
                    if ($this->groupCarabayar == DocoConstants::GROUP_BPJS) {
                        if (isset($jknRes['jkn']['metadata'])) {
                            if ($jknRes['jkn']['metadata']['code'] != 200 || $this->skipErrorJkn == FALSE) {
                                $this->cancelDBTransaction();
                                return $this->validationJknAntrian(false, $jknRes['jkn']['metadata']['message']);
                            }
                        } else {
                            $this->cancelDBTransaction();
                            return $this->validationJknAntrian(false, $jknRes['jkn']['metaData']['message']);
                        }
                    }
                    $res_jkn = [
                        'response' => $jknRes['jkn'],
                        'payload' => $jknRes['payload'],
                        'model' => $model->attributes
                    ];
                    if(is_null($model->estimasidilayani)){
                        $model->estimasidilayani = ArrayHelper::getValue($jknRes,'payload.estimasidilayani');
                    }
                    $model->additional_jkn = json_encode($res_jkn);
                    $model->save();

                    $this->updateAdditionalJkn($jknRes, $model->pendaftaranol_id);
                }

                if ($isSmh) {
                    $kuotaReservasi =  $this->_getInfoPendaftaran($model->pendaftaranol_id);

                    if ($kuotaReservasi['jumlah_antrian_nonbpjs_online'] <= 0) {
                        $this->cancelDBTransaction();
                        $nameDokter = $kuotaReservasi['namadokter'];
                        
                        throw new ValidationException(422, $this->_error, [
                            'text' => Yii::t('app', 'Kuota Dokter '.$nameDokter.' sudah habis !.'),
                        ]);
                    }
                }

                $this->commitDBTransaction();
                if ($reservasiPoliklinik) {
                    return [
                        'data' => $this->actionGetRiwayatPendaftaran($model->pendaftaranol_id, $model->created_by, $additional),
                        'message' => Yii::t('app', 'Data Berhasil di simpan.')
                    ];
                }
    
                $model = $this->actionGetRiwayatPendaftaran($model->pendaftaranol_id, $model->created_by, $additional);
                Yii::error('kesini');
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

    /**
     * @method method ini dipanggil kalau regis/rsv via SMH
     * @param object $model (Awas ini pointer ya!!!)
     * @param array $post dari request
     * 
     * @return void
     */
    protected function rsvSmh(&$model, $post)
    {
        $getDataSlot = [];
        $today = date('Y-m-d', strtotime('NOW'));
        $model->jenis_reservasi = DocoConstants::JENIS_RESERVASI_APPOINMENT;
        $slotId = $post['sirs_slot_id'];
        $doctorCode = strtolower(ArrayHelper::getValue($post, 'doctor_code'));
        $maxReservasi = ArrayHelper::getValue($this->konfig, 'reservasi_akhir');

        if(empty($slotId) || empty($doctorCode) || empty($model->tgl_pendaftaranol)) {
            throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => Yii::t('app', 'Parameter Tidak Lengkap.'),
            ]);
        }

        if(!is_null($maxReservasi)) {
            $dateMax = date('Y-m-d', strtotime(' + ' .$maxReservasi. ' days'));
            $daftarOl = date('Y-m-d', strtotime($model->tgl_pendaftaranol));
            if($daftarOl > $dateMax) {
                throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                    'text' => Yii::t('app', 'Tanggal appointment melebihi waktu yang di tentukan.'),
                ]);
            }
        }

        /**
         * ?? : check changing schedule
         */
        if($daftarOl == $today) {
            $checkChangeSchedule = $this->_db->createCommand("
            SELECT COUNT
                ( slotjadwaldokter_id )  as data
            FROM
                slotjadwaldokter_r 
            WHERE
                slotjadwaldokter_id = {$slotId}
            ")->queryOne();

            if($checkChangeSchedule['data'] > 0) {
                $this->jadwalChange = true;
            }
        }

        if($this->jadwalChange) {
            $checkSlotAvail = (new PendaftaranOnline)->checkSlotAvailSmhChangeSchedule(date('Y-m-d', strtotime($model->tgl_pendaftaranol)), $slotId, $doctorCode);
        } else {
            $checkSlotAvail = (new PendaftaranOnline)->checkSlotAvailSmh(date('Y-m-d', strtotime($model->tgl_pendaftaranol)), $slotId, $doctorCode);
        }

        if(!empty($checkSlotAvail)) {
            throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => Yii::t('app', 'Slot sudah dibooking.'),
            ]);
        }
        
        if($this->jadwalChange) {
            $getDataSlot = (new PendaftaranOnline)->getDataSlotChangeSchedule($slotId, $this->hariId, $doctorCode);
        } else {
            $getDataSlot = (new PendaftaranOnline)->getDataSlot($slotId, $this->hariId, $doctorCode);
        }

        if(empty($getDataSlot)) {
            throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => Yii::t('app', 'Slot Dokter tidak ditemukan.'),
            ]);
        }
        $model->jadwaldokter_id = $getDataSlot['jadwaldokter_id'];
        $model->jadwalbukapoli_id = $getDataSlot['jadwalbukapoli_id'];
        $model->ruangan_id = $getDataSlot['ruangan_id'];
        $model->jam_kunjungan = $getDataSlot['jam_mulai']. ' - ' . $getDataSlot['jam_selesai'];
        $model->pegawai_id = $getDataSlot['pegawai_id'];
        $this->slotSequence = $getDataSlot['slot_sequence'];
        $this->gorupCarabayar = DocoConstants::GROUP_UMUM; //private
        $this->setPrefixAntrian();
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
                if(isset($post['sirs_slot_id']) && isset($this->slotSequence)) {
                    $this->noUrut = $this->prefixKonfig.str_pad($this->slotSequence, 3, '0', STR_PAD_LEFT);
                    
                    //* cek nomor antrian is used
                    $checkNoUrutAvail = (new \yii\db\Query())
                    ->select([
                        'antrian_id'
                    ])
                    ->from('antrian_t')
                    ->where([
                        '"tgl_antrian"::date'=> date('Y-m-d', strtotime($model->tgl_pendaftaranol)),
                        'ruangan_id' => $model->ruangan_id,
                        'pegawai_id' => $model->pegawai_id,
                        'jadwaldokter_id' => $model->jadwaldokter_id,
                    ])
                    ->andWhere(['like', 'LOWER(no_antrian)', strtolower($this->noUrut)])
                    ->count();

                    if($checkNoUrutAvail > 0) {
                        throw new ValidationException(422, DocoMessages::KEY_DYNAMIC_STATUS, [
                            'text' => Yii::t('app', 'Slot sudah digunakan.'),
                        ]);
                    }
                    
                    // return [
                    //     'status' => 422,
                    //     'title'  => 'Proses Gagal',
                    //     'message'   => Yii::t('app', 'Slot sudah digunakan.'),
                    //     'payload' => $post
                    // ];
                } else {
                    foreach($getNoUrut as $key => $value) {
                        if($loop == 0) {
                            $this->noUrut = $key;
                            $loop++;
                        } else {
                            break;
                        }
                    }
                }
            } else {
                throw new ValidationException(500, DocoMessages::KEY_DYNAMIC_STATUS, [
                    'text' => Yii::t('app', 'Nomor urut tidak tersedia.'),
                ]);
            }
        }
    }
}