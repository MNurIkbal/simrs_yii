<?php

namespace Integrasi\Service\Lis;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use yii\db\Query;

use Integrasi\Components\Repositories\GenerateDataPatientLabRepositories;

use Integrasi\Service\Ris\Models\PemeriksaanPasienRadiologiView;
use Integrasi\Service\Ris\Models\RekapRis;
use Integrasi\Components\Services\LisService;
use Integrasi\Components\Object\LisObject;
use Integrasi\Components\Object\RekapRisObject;
use Integrasi\Service\Roche\Models\IntegrasiRoche;

class BridgingLis extends \Integrasi\Contracts\DocoImplement
{
    protected $keyConfig = 'lisintegration';

    public function execute()
    {
        $penunjangId = (int) $this->pasienmasukpenunjang_id;
        $regisId = (int) $this->pendaftaran_id;
        $unitId = (int) $this->pasienkirimkeunitlain_id;
        $noRekamMedik = null;
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
        $isAps = $this->is_aps;
        $state = $this->state;  
        $id = ArrayHelper::getValue($this->result, 'id');
        $pembayaranIds = (int) ArrayHelper::getValue($this->result, 'pembayaran_id');

        // Ini Trigger dari pendafatran penunjang
        if (!empty($id)) {
            $regisId = DocoHelpers::decrypt($id);
        }

        $integrasi = GenerateDataPatientLabRepositories::getData($penunjangId, $regisId, $unitId, $noRekamMedik); 
        
        $insertLog = [];
        $sendData = [];
        $existingRekap = [];
        $savingRekap = [];
        $response = [];

        foreach ($integrasi as $value) {
            $tmp = []; 
            $groupCaraBayar = ArrayHelper::getValue($value, 'groupcarabayar_id');
            $instalasi = ArrayHelper::getValue($value,'instalasi_id');
            $aps = ArrayHelper::getValue($value,'is_aps');

            /**
             * Kondisi Ketika Aps dan bayar Umum Harus Bayar Dulu
             * Kondisi Rajal dan bayar Umum Ketika Approve  
             */
            $pasienAps = ($groupCaraBayar == DocoConstants::GROUP_UMUM && $aps);
            $pasienUMUMRJ = ($groupCaraBayar == DocoConstants::GROUP_UMUM && $instalasi == DocoConstants::INST_ID_RJ);
            
            if (($pasienAps && !empty($isAps)) || ($pasienUMUMRJ && !empty($state)) ) continue;

            $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');       
            $doctor = ArrayHelper::getValue($value, 'ref_doctor_name');   
            $pasienClass = $instalasi == DocoConstants::INST_ID_RI ? 'IP' : 'OP';
            $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
            $pasienmasukpenunjangId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
            $unitLain = ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id');
            $dataExistingRekap = $this->getRekapLIS($pasienmasukpenunjangId, $pendaftaranId, $unitLain);

            $obj = new LisObject;
            /*Pid*/ 
            $obj->username = !empty($baseConfig['username']) ? $baseConfig['username'] : null;
            $obj->key = !empty($baseConfig['key']) ? $baseConfig['key'] : null;
            $obj->pmrn = ArrayHelper::getValue($value, 'no_rekam_medik');
            $obj->pname = ArrayHelper::getValue($value, 'nama_pasien');
            $obj->sex = ArrayHelper::getValue($value, 'gender') == 'M' ? 'L' : 'P';
            $obj->birthdt =  date('d.m.Y',strtotime(ArrayHelper::getValue($value, 'date_of_birth')));
            $obj->address = ArrayHelper::getValue($value, 'address');
            $obj->notlp = ArrayHelper::getValue($value, 'no_telepon_pasien');
            
            /*obr*/
            if (!empty($dataExistingRekap)) {
                $obj->ordercontrol = 'U';
            }else{
                $obj->ordercontrol = 'N';
            }
            $obj->ptype = $pasienClass;
            $obj->regno = ArrayHelper::getValue($value, 'no_pendaftaran'); 
            $obj->orderlab = ArrayHelper::getValue($value, 'order_no');
            $obj->providerid = $penjaminId;
            $obj->providername = ArrayHelper::getValue($value, 'penjamin_nama'); 
            $obj->orderdate = date('d.m.Y H:i:s',strtotime(ArrayHelper::getValue($value, 'order_time')));
            $clinicalid = ArrayHelper::getValue($value, 'ref_doctor_id');
            if (empty($clinicalid) || $clinicalid == '-' || is_null($clinicalid)) {
                $clinicalid = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'ref_doctor_primary_id','-'));
            }
            $obj->clinicalid = $clinicalid;
            $obj->clinicalname = $doctor;
            $obj->bangsalid = ArrayHelper::getValue($value, 'location_id');
            $obj->bangsalname = ArrayHelper::getValue($value, 'location_name');
            if($instalasi == DocoConstants::INST_ID_RI){
                $obj->bangsalid = ArrayHelper::getValue($value, 'kamarruangan_kode');
                $obj->badid = ArrayHelper::getValue($value, 'kamarruangan_kode');
                $obj->badname = ArrayHelper::getValue($value, 'no_tempattidur');
            }else{
                $obj->badid = '0000';
                $obj->badname = '0000';
            }
            $obj->classid = !empty(ArrayHelper::getValue($value, 'patient_class')) ? ArrayHelper::getValue($value, 'patient_class') : "0";
            $obj->classname = !empty(ArrayHelper::getValue($value, 'patient_class_name')) ? ArrayHelper::getValue($value, 'patient_class_name') : "0";
            $obj->cito = ArrayHelper::getValue($value, 'priority') == 'S' ? 'Y' : 'N';
            $obj->medlegal = 'N';
            $obj->userid = ArrayHelper::getValue($value, 'pasien_id');
            if(!empty($value['tests'])){
                foreach(json_decode($value['tests']) as $tes){
                    $tmp[] = ArrayHelper::getValue($tes, 'id');
                }
            }
            $obj->ordertest = (object) $tmp;

            $payload = $obj->buildArray();
            $sendData[] = $payload;
            
            if (!empty($dataExistingRekap)) {
                $response = (new LisService)->orderLis($payload, function ($data) {
                    return $data;
                }, 'POST');
                $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                $responUpdate = json_encode($response);
                
                $update = [];
                $update['id_sync_sercon'] = $prosesId;
                $update['sync_respon'] = $responUpdate;
                IntegrasiRoche::updateAll(
                    $update, 
                    ['and', 
                    ['pasienmasukpenunjang_id' => $dataExistingRekap['pasienmasukpenunjang_id']] ,
                    ['pendaftaran_id' => $dataExistingRekap['pendaftaran_id']] ,
                ]);
                $existingRekap[] = $dataExistingRekap;
            }else{
                $is_sent = false;
                $id_sync_sercon = null;
                try {    
                    $response = (new LisService)->orderLis($payload, function ($data) {
                        return $data;
                    }, 'POST');
                    $result = ArrayHelper::getValue($response, 'Results.0.data.data');
                    $id_sync_sercon = ArrayHelper::getValue($response, 'ProcessUID');
                    $is_error = ArrayHelper::getValue($response, 'Results.0.data.0.is_error', false);
                    $is_sent = $is_error == false ? true : false;
                } catch (\Exception $e) {
                    $response = [
                        'Message' => $e->getMessage(),
                        'File' => $e->getFile(),
                        'Line' => $e->getLine(),
                    ];
                }

                $insertLog[] = [
                    'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                    'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                    'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                    'payload' => json_encode($payload),
                    'is_sent' => $is_sent,
                    'is_sending' => true,
                    'id_sync_sercon' => $id_sync_sercon,
                    'sync_respon' => json_encode($response),
                ];  
            }
        }

        if(!empty($insertLog)) {
            IntegrasiRoche::batchInsert($insertLog);
        }

        return json_encode([
            'service' => 'Lis-BridgingLis',
            'payload' => $this->attributes,
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);      
    }

    private function getRekapLIS($penunjangId, $pendaftaranId, $unitLain)
    {
        if (!empty($penunjangId) || !empty($pendaftaranId)) {
            $getRekapLIS = IntegrasiRoche::find();
            if (!empty($penunjangId)) {
                $getRekapLIS->andWhere([
                    'pasienmasukpenunjang_id' => $penunjangId
                ]);
            }

            if (!empty($pendaftaranId)) {
                $getRekapLIS->andWhere([
                    'pendaftaran_id' => $pendaftaranId
                ]);
            }

            if (!empty($unitLain)) {
                $getRekapLIS->andWhere([
                    'pasienkirimkeunitlain_id' => $unitLain
                ]);
            }

            return $getRekapLIS->asArray()->one();
        }
    }

}