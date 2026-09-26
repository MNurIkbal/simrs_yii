<?php

namespace Integrasi\Service\InaBroker;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use yii\db\Query;

use Integrasi\Components\Repositories\GenerateDataPatientRepositories;

use Integrasi\Service\Ris\Models\PemeriksaanPasienRadiologiView;
use Integrasi\Service\Ris\Models\RekapRis;
use Integrasi\Components\Services\RadiologiService;
use Integrasi\Components\Object\InaRisObject;
use Integrasi\Components\Object\RekapRisObject;

class RisBroker extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $penunjangId = (int) $this->pasienmasukpenunjang_id;
        $regisId = (int) $this->pendaftaran_id;
        $unitId = (int) $this->pasienkirimkeunitlain_id;
        $pembayaranId = (int) $this->pembayaran_id;
        $isAps = $this->is_aps;
        $state = $this->state;  
        $id = ArrayHelper::getValue($this->result, 'id');
        $pembayaranIds = (int) ArrayHelper::getValue($this->result, 'pembayaran_id');

        // Ini Trigger dari pendafatran penunjang
        if (!empty($id)) {
            $regisId = DocoHelpers::decrypt($id);
        }

        $integrasi = GenerateDataPatientRepositories::getData($penunjangId, $regisId, $unitId, $pembayaranIds); 
        $listRekap = [];
        $sendData = [];
        $existingRekap = [];
        $savingRekap = [];

        foreach ($integrasi as $value) {
            $jenisKelamin = ArrayHelper::getValue($value, 'jeniskelamin');
            $jenisKelaminKode = "O";
            if($jenisKelamin == DocoConstants::VAR_LK) { // jenis kelamin laki-laki
                $jenisKelaminKode = "M";
            }
            elseif($jenisKelamin == DocoConstants::VAR_PR) { // jenis kelamin perempuan
                $jenisKelaminKode = "F";
            }

            // $status = ArrayHelper::getValue($value, 'status');
            $groupCaraBayar = ArrayHelper::getValue($value, 'groupcarabayar_id');
            $instalasi = ArrayHelper::getValue($value,'instalasi_id');
            /**
             * Kondisi Ketika Aps dan bayar Umum Harus Bayar Dulu
             * Kondisi Rajal dan bayar Umum Ketika Approve  
             */

            $pasienAps = ($groupCaraBayar == DocoConstants::GROUP_UMUM && !empty($isAps));
            $pasienUMUMRJ = ($groupCaraBayar == DocoConstants::GROUP_UMUM && $instalasi == DocoConstants::INST_ID_RJ);
            
            if ($pasienAps || ($pasienUMUMRJ && !empty($state)) ) continue;

            $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');            
            $noPembayaran = ArrayHelper::getValue($value, 'no_pembayaran');
            $diagnosa = isset($value['nama_diagnosa']) 
                            ? json_decode($value['nama_diagnosa'], true) : [];
            $idRekap = ArrayHelper::getValue($value, 'id');

            $diagnosaNama = null;
            if (!empty($diagnosa) || is_array($diagnosa)) {
                $diagnosaNama = isset($diagnosa['text']) ? $diagnosa['text'] : null;
            }

            $pasienClass = $instalasi == DocoConstants::INST_ID_RI 
                                        ? 'I' : (($instalasi == DocoConstants::INST_ID_RD) ? 'E' : 'O');
            $pelayananId = ArrayHelper::getValue($value, 'tindakanpelayanan_id');
            $noMasukpenunjang = ArrayHelper::getValue($value, 'no_masukpenunjang');

            $rekapRis = new RekapRisObject;
            $rekapRis->pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
            $rekapRis->pasienmasukpenunjangId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
            $rekapRis->daftartindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
            $rekapRis->tindakanpelayananId = $pelayananId;
            $rekapRis->noPembayaran = @$noPembayaran;
            $orderNo = $noMasukpenunjang . '-' . $pelayananId;            

            $obj = new InaRisObject;
            $obj->accessionNumber = !empty($pelayananId) ? $pelayananId : 'none';
            $obj->fillerOrderNumber = !empty($orderNo) ? $orderNo : 'none';
            $obj->placerOrderNumber = !empty($noMasukpenunjang) ? $noMasukpenunjang : 'none';
            $obj->admissionId = !empty(ArrayHelper::getValue($value, 'no_pendaftaran')) ? ArrayHelper::getValue($value, 'no_pendaftaran') : 'none';
            $obj->stationAeTitle = 'none';
            $obj->stationName = 'none';
            $obj->modality = !empty(ArrayHelper::getValue($value, 'modality_kode')) ? ArrayHelper::getValue($value, 'modality_kode') : 'none';
            $obj->scheduledDateTime = !empty(ArrayHelper::getValue($value, 'tglmasukpenunjang')) ? ArrayHelper::getValue($value, 'tglmasukpenunjang') : 'none';
            
            /*patient*/
            $obj->mrn = !empty(ArrayHelper::getValue($value, 'no_rekam_medik')) ? ArrayHelper::getValue($value, 'no_rekam_medik') : 'none';
            $obj->otherId = !empty(ArrayHelper::getValue($value, 'pasien_id')) ? ArrayHelper::getValue($value, 'pasien_id') : 'none';
            $obj->patientName = !empty(ArrayHelper::getValue($value, 'nama_pasien')) ? ArrayHelper::getValue($value, 'nama_pasien') : 'none'; // attr send to name
            $obj->dob = !empty(ArrayHelper::getValue($value, 'tanggal_lahir')) ? ArrayHelper::getValue($value, 'tanggal_lahir') : 'none';
            $obj->patientAddress = !empty(ArrayHelper::getValue($value, 'alamat_pasien')) ? ArrayHelper::getValue($value, 'alamat_pasien') : 'none'; // attr send to address
            $obj->patientPhone = !empty(ArrayHelper::getValue($value, 'no_telepon_pasien')) ? ArrayHelper::getValue($value, 'no_telepon_pasien') : 'none'; // attr send to phone
            $obj->sex = $jenisKelaminKode;
            $obj->religion = !empty(ArrayHelper::getValue($value, 'agama')) ? ArrayHelper::getValue($value, 'agama') : 'none';
            $obj->residentCountry = !empty(ArrayHelper::getValue($value, 'warga_negara')) ? ArrayHelper::getValue($value, 'warga_negara') : 'none';
            $obj->state = 'none';
            $obj->allergies = !empty(ArrayHelper::getValue($value, 'alergi')) ? ArrayHelper::getValue($value, 'alergi') : 'none';

            /*referring_physician*/
            $obj->referringPhysicianName = !empty(ArrayHelper::getValue($value, 'dokter_perujuk_nama')) ? ArrayHelper::getValue($value, 'dokter_perujuk_nama') : 'none'; // attr send to name
            $obj->referringPhysicianInstitutionName = !empty(ArrayHelper::getValue($value, 'kode_ruangan')) ? ArrayHelper::getValue($value, 'kode_ruangan') : 'none'; // attr send to institution name, replace station station ae title
            $obj->referringPhysicianAddress = !empty(ArrayHelper::getValue($value, 'alamat_pegawai')) ? ArrayHelper::getValue($value, 'alamat_pegawai') : 'none'; // attr send to address
            $obj->referringPhysicianPhone = !empty(ArrayHelper::getValue($value, 'notelp_pegawai')) ? ArrayHelper::getValue($value, 'notelp_pegawai') : 'none'; // attr send to phone
            
            /*requesting_physician*/
            $obj->requestingPhysicianName = !empty(ArrayHelper::getValue($value, 'dokter_penunjang')) ? ArrayHelper::getValue($value, 'dokter_penunjang') : 'none'; // attr send to name
            $obj->requestingPhysicianInstitutionName = !empty(ArrayHelper::getValue($value, 'kode_ruangan')) ? ArrayHelper::getValue($value, 'kode_ruangan') : 'none'; // attr send to institution name, replace station station ae title
            $obj->requestingPhysicianAddress = !empty(ArrayHelper::getValue($value, 'penunjang_alamat_pegawai')) ? ArrayHelper::getValue($value, 'penunjang_alamat_pegawai') : 'none'; // attr send to address
            $obj->requestingPhysicianPhone = !empty(ArrayHelper::getValue($value, 'penunjang_notelp_pegawai')) ? ArrayHelper::getValue($value, 'penunjang_notelp_pegawai') : 'none'; // attr send to phone

            /*requested_procedure*/
            $obj->requestedProcedureId = !empty($pelayananId) ? $pelayananId : 'none'; // attr send to id
            $obj->requestedProcedureDescription = !empty(ArrayHelper::getValue($value, 'daftartindakan_nama')) ? ArrayHelper::getValue($value, 'daftartindakan_nama') : 'none'; // attr send to description
            $obj->priority = !empty(ArrayHelper::getValue($value, 'cyto_tindakan')) ? ArrayHelper::getValue($value, 'cyto_tindakan') : 'none';
            $obj->location = 'none';
            $obj->smokingStatus = "Unknown";
            $obj->pregnancyStatus = "0004";
            
            /*scheduled_procedure_step*/
            $obj->scheduledProcedureStepId = !empty($pelayananId) ? $pelayananId : 'none';
            $obj->startDateTime = !empty(ArrayHelper::getValue($value, 'tglmasukpenunjang')) ? ArrayHelper::getValue($value, 'tglmasukpenunjang') : 'none';
            $obj->endDateTime = !empty(ArrayHelper::getValue($value, 'tglmasukpenunjang')) ? ArrayHelper::getValue($value, 'tglmasukpenunjang') : 'none';

            $payload = $obj->buildArray();
            $sendData[] = $payload;
            $rekapRis->payload = json_encode($payload);
    
            $dataExistingRekap = $this->getRekapRIS($rekapRis->pasienmasukpenunjangId, $rekapRis->tindakanpelayananId, $rekapRis->daftartindakanId);
            if (!empty($dataExistingRekap)) {
                $response = (new RadiologiService)->inputOrderRadiologi($payload, function ($data) {
                    return $data;
                }, 'POST');
                $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                $responUpdate = json_encode($response);
                
                $update = [];
                $update['payload'] = $rekapRis->payload;
                $update['is_update'] = true;
                $update['id_sync_sercon_update'] = $prosesId;
                $update['no_pembayaran'] = $noPembayaran;
                $update['sync_respon_update'] = $responUpdate;
                RekapRis::updateAll(
                    $update, 
                    ['and', 
                    ['pasienmasukpenunjang_id' => $dataExistingRekap['pasienmasukpenunjang_id']] ,
                    ['tindakanpelayanan_id' => $dataExistingRekap['tindakanpelayanan_id']] ,
                    ['daftartindakan_id' => $dataExistingRekap['daftartindakan_id']] ,
                ]);
                RekapRis::updateAll($update,['id' => $idRekap]);
                $existingRekap[] = $dataExistingRekap;
            }else{
                $response = (new RadiologiService)->inputOrderRadiologi($payload, function ($data) {
                    return $data;
                }, 'POST');
                $rekapRis->idSyncSercon = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                $rekapRis->syncRespon = json_encode($response);
                $rekapRis->isSending = true;                
                $listRekap[] = $rekapRis->buildArray();
            }
        }
        RekapRis::batchInsert($listRekap);

        return json_encode([
            'service' => 'InaBroker-RisBroker',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),

        ]);      
    }

    private function getRekapRIS($penunjangId, $tindakanpelayananId, $daftartindakanId)
    {
        if (!empty($penunjangId) || !empty($tindakanpelayananId) || !empty($daftartindakanId)) {
            $getRekapRIS = RekapRis::find();
            if (!empty($penunjangId)) {
                $getRekapRIS->andWhere([
                    'pasienmasukpenunjang_id' => $penunjangId
                ]);
            }

            if (!empty($tindakanpelayananId)) {
                $getRekapRIS->andWhere([
                    'tindakanpelayanan_id' => $tindakanpelayananId
                ]);
            }

            if (!empty($daftartindakanId)) {
                $getRekapRIS->andWhere([
                    'daftartindakan_id' => $daftartindakanId
                ]);
            }

            return $getRekapRIS->asArray()->one();
        }
    }

}