<?php

namespace Integrasi\Service\Mhg;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\Services\MhgService;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use Integrasi\Service\Mhg\Cache\Cache;
use Integrasi\Service\Mhg\Models\PendaftaranT;
use Integrasi\Service\Mhg\Models\PasienInt;

class CreateMasterPatient extends \Integrasi\Contracts\DocoImplement
{

    public function execute()
    {
        $payload = [];
        $result = [];
        $pendaftaran_id = isset ($this->result['id']) ? DocoHelpers::decrypt($this->result['id']) : null;
        $pasien_id = isset($this->result['pas_id']) ? $this->result['pas_id'] : null;
        $jenis_identitas = '';
        $no_identitas = '';
        $state = 'create';
        
            if (!empty($pendaftaran_id)){
                $pendaftaran = PendaftaranT::find()
                            ->andWhere([
                                'pendaftaran_id' => $pendaftaran_id
                            ])
                            ->one();

                if($pendaftaran->status_pasien != DocoConstants::VAR_PAS_B)
                {
                    $result = [
                        'message' => 'Status pasien lama'
                    ];
                    return $this->setResponse($payload, $result);
                }
            }
            
            $pasien_id = isset($pendaftaran->pasien_id) ? $pendaftaran->pasien_id : $pasien_id;
            $pasien = Yii::$app->db->createCommand("
                SELECT 
                * 
                FROM pasien_v 
                WHERE pasien_id = {$pasien_id}
            ")->queryOne();

            if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                $additionalPasien = json_decode($pasien['additional_pasien']);
                $jenis_identitas = $additionalPasien[0]->jenisidentitas;
                $no_identitas = $additionalPasien[0]->no_identitas_pasien;
            }

            $payload = [
                'pasien_id' => $pasien_id,
                'no_rekam_medik' => $pasien['no_rekam_medik'],
                'nama_pasien' => $pasien['nama_pasien'],
                'tanggal_lahir' => $pasien['tanggal_lahir'],
                'status_perkawinan' => isset($pasien['statusperkawinan']) ? $pasien['statusperkawinan'] : '',
                'alamatemail' => isset($pasien['alamatemail']) ? $pasien['alamatemail'] : '',
                'no_telepon_pasien' => isset($pasien['no_telepon_pasien']) ? $pasien['no_telepon_pasien'] : '',
                'no_mobile_pasien' => isset($pasien['no_mobile_pasien']) ? $pasien['no_mobile_pasien'] : '',
                'jenis_kelamin' => isset($pasien['jeniskelamin']) ? $pasien['jeniskelamin'] : '',
                'is_active' => 0,
                'tgl_update_terakhir' => isset($pasien['tgl_update_terakhir']) ? date('Y-m-d', strtotime($pasien['tgl_update_terakhir'])) : '',
                'jenisidentitas' => $jenis_identitas,
                'no_identitas_pasien' => $no_identitas,
                'weight' => '',
                'height' => '',
                'pekerjaan_id' => isset($pasien['pekerjaan_id']) ? $pasien['pekerjaan_id'] : '',
                'agama' => isset($pasien['agama']) ? $pasien['agama'] : '',
                'pendidikan_id' => isset($pasien['pendidikan_id']) ? $pasien['pendidikan_id'] : '',
                'golongandarah' => isset($pasien['golongandarah']) ? $pasien['golongandarah'] : '',
                'warganegara' => isset($pasien['warganegara']) ? $pasien['warganegara'] : '',
                'alamat_pasien' => isset($pasien['alamat_pasien']) ? $pasien['alamat_pasien'] : '-',
                'kode_kelurahan' => isset($pasien['kode_kelurahan_kemendagri']) ? $pasien['kode_kelurahan_kemendagri'] : '',
                'kode_kecamatan' => isset($pasien['kode_kecamatan_kemendagri']) ? $pasien['kode_kecamatan_kemendagri'] : '',
                'kode_kabupaten' => isset($pasien['kode_kabupaten_kemendagri']) ? $pasien['kode_kabupaten_kemendagri'] : '',
                'kode_propinsi' => isset($pasien['kode_propinsi_kemendagri']) ? $pasien['kode_propinsi_kemendagri'] : '',
                'kode_negara' => isset($pasien['kode_negara_kemendagri']) ? $pasien['kode_negara_kemendagri'] : '',
                'kode_pos' => isset($pasien['kode_pos']) ? $pasien['kode_pos'] : '',
                'opor_mr' => '',
            ];
            $result = (new MhgService)->patientCreate($payload);

            $this->setLogs($result, $state, $payload);
        
        return $this->setResponse($payload, $result);
        
    }

    public function setResponse($payload, $result)
    {
        return json_encode([
            'service' => 'Mhg-CreateMasterPatient',
            'payload' => $payload,
            'timestamp' => date('Y-m-d H:i:s'),
            'attributes' => $this->attributes,
            'result' => $result
        ]);
    }

    public function setLogs($result, $state, $payload)
    {
        $response = isset($result['response']) ? $result['response'] : [];
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;

        // $logData[] = [
        //     'pasien_id' => isset($payload['pasien_id']) ? $payload['pasien_id'] : "",
        //     'state' => $state,
        //     'created_date' => date('Y-m-d H:i:s'),
        //     //'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
        //     'payload' => isset($response['payload']) ? json_encode($response['payload']) : null,
        //     'sync_respon' => isset($response['payload']) ? json_encode($response['payload']) : null,
        // ];

        $model = new PasienInt;
        $model->pasien_id = isset($payload['pasien_id']) ? $payload['pasien_id'] : "";
        $model->state = $state;
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = isset($userIdentity['uid']) ? $userIdentity['uid'] : null;
        $model->id_sync_sercon = $uidSercon;
        $model->payload = isset($payload) ? json_encode($payload) : null;
        $model->sync_respon = isset($response) ? json_encode($response) : null;
        $model->save();

        //$this->saveLogs($logData);
    }

    public function saveLogs($data)
    {
        return PasienInt::batchInsert($data);
    }

}