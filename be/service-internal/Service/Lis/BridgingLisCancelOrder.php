<?php

namespace Integrasi\Service\Lis;

use Doco\models\Penjamin;
use Doco\models\TindakanPelayanan;
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
use phpDocumentor\Reflection\Types\Array_;

class BridgingLisCancelOrder extends \Integrasi\Contracts\DocoImplement
{   
    protected $keyConfig = 'lisintegration';

    public function execute()
    {
        $penunjangId = (int) $this->pasienmasukpenunjang_id;
        $regisId = (int) $this->pendaftaran_id;
        $unitId = (int) $this->pasienkirimkeunitlain_id;
        $pembayaranId = (int) $this->pembayaran_id;
        $caraBayar = (int) $this->carabayar_id;
        $penjaminId = (int) $this->penjamin_id;
        $detailTindakan = $this->detail_tindakan;
        $noRekamMedik = null;
        $id = ArrayHelper::getValue($this->result, 'id');
        $pembayaranIds = (int) ArrayHelper::getValue($this->result, 'pembayaran_id');
        $response = [];
        $insertLog = null;

        // Ini Trigger dari pendafatran penunjang
        if (!empty($id)) {
            $regisId = DocoHelpers::decrypt($id);
        }

        // Ini Trigger ketika batal bayar
        if (! empty($pembayaranId)) {
            $response = self::batalOrderFromPembayaran($regisId);
            $insertLog = $response;
        }

        // Ini Trigger ketika batal pemeriksaan
        if(! empty($penunjangId) || ! empty($detailTindakan)) {
            $response = self::batalOrderFromPemeriksaan($penunjangId, $detailTindakan);
            $insertLog = $response;
        }

        // Ini Trigger ketika edit pendaftaran hanya carabayar_id
        if(! empty($caraBayar) && $penjaminId == null) {
            $response = self::batalFromEditPendaftaran($regisId);
            $insertLog = $response;
        }

        // Ini Trigger ketika edit pendaftaran tapi ganti penjamin
        if(! empty($penjaminId) && ! empty($caraBayar)) {
            $response = self::updatePenjaminFromPendaftaran($regisId);
            $insertLog = $response;
        }
    
        if(! empty($insertLog)) {
            IntegrasiRoche::batchInsert($insertLog);
        }

        return json_encode([
            'service' => 'Lis-BridgingLisCancelOrder',
            'payload' => $this->attributes,
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s'),

        ]);      
    }

    private function getRekapLIS($pasienmasukpenunjangId, $pendaftaranId)
    {
        if (!empty($pasienmasukpenunjangId) || !empty($pendaftaranId)) {
            $getRekapLIS = IntegrasiRoche::find()
            ->where([
                'is_sent' => true,
                'is_sending' => true,
                'state' => null
            ]);

            if (!empty($penunjangId)) {
                $getRekapLIS->andWhere([
                    'pasienmasukpenunjang_id' => $pasienmasukpenunjangId
                ]);
            }

            if (!empty($pendaftaranId)) {
                $getRekapLIS->andWhere([
                    'pendaftaran_id' => $pendaftaranId
                ]);
            }

            return $getRekapLIS->asArray()->one();
        }
    }

    /**
     * @author Maulana Muhammad Rizky
     * Function ini merupakan trigger pembatalan yang berasal dari pembayaran (KASIR)
     * 
     * @param regisId int
     */
    private function batalOrderFromPembayaran($regisId) 
    {
        $logServiceInternal = [];
        $integrasi = GenerateDataPatientLabRepositories::getData(null, $regisId, null,null); 
        $isAps = false;
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
        if(! empty($integrasi)) {
            foreach ($integrasi as $key => $value) {
                $tmp = [];
                $groupCaraBayar = ArrayHelper::getValue($value, 'groupcarabayar_id');
                $instalasi = ArrayHelper::getValue($value,'instalasi_id');
                $isAps = ArrayHelper::getValue($value, 'is_aps', false);

                /**
                 * Kondisi Ketika Aps dan bayar Umum Harus Bayar Dulu
                 * Kondisi Rajal dan bayar Umum Ketika Approve  
                 */
                $pasienAps = ($groupCaraBayar == DocoConstants::GROUP_UMUM && !empty($isAps));
                $pasienUMUMRJ = ($groupCaraBayar == DocoConstants::GROUP_UMUM && $instalasi == DocoConstants::INST_ID_RJ);
                $pasienRD = ($instalasi == DocoConstants::INST_ID_RD);
                $pasienRI = ($instalasi == DocoConstants::INST_ID_RI);
                
                if($pasienRD || $pasienRI) continue;

                $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');       
                $doctor = ArrayHelper::getValue($value, 'ref_doctor_name');   

                $pasienClass = $instalasi == DocoConstants::INST_ID_RI ? 'IP' : 'OP';
                $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
                $pasienmasukpenunjangId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
                $dataExistingRekap = $this->getRekapLIS($pasienmasukpenunjangId, $pendaftaranId);

                if(! empty($dataExistingRekap)) {

                    $obj = new LisObject;
                    /*Pid*/ 
                    $obj->username = $baseConfig['username'];
                    $obj->key = $baseConfig['key'];
                    $obj->pmrn = ArrayHelper::getValue($value, 'no_rekam_medik');
                    $obj->pname = ArrayHelper::getValue($value, 'nama_pasien');
                    $obj->sex = ArrayHelper::getValue($value, 'gender') == 'M' ? 'L' : 'P';
                    $obj->birthdt =  date('d.m.Y',strtotime(ArrayHelper::getValue($value, 'date_of_birth')));
                    $obj->address = ArrayHelper::getValue($value, 'address');
                    $obj->notlp = ArrayHelper::getValue($value, 'no_telepon_pasien');

                    if(!empty($value['tests'])){
                        foreach(json_decode($value['tests']) as $tes){
                            $tmp[] = ArrayHelper::getValue($tes, 'id');
                        }
                    }

                    $obj->ordercontrol = 'D';

                    $obj->ptype = $pasienClass;
                    $obj->regno = ArrayHelper::getValue($value, 'no_pendaftaran'); 
                    $obj->orderlab = ArrayHelper::getValue($value, 'order_no');
                    $obj->providerid = $penjaminId;
                    $obj->providername = ArrayHelper::getValue($value, 'penjamin_nama'); 
                    $obj->orderdate = date('d.m.Y H:i:s',strtotime(ArrayHelper::getValue($value, 'order_time')));
                    $obj->clinicalid = ArrayHelper::getValue($value, 'ref_doctor_id');
                    $obj->clinicalname = $doctor;
                    $obj->bangsalid = ArrayHelper::getValue($value, 'location_id');
                    $obj->bangsalname = ArrayHelper::getValue($value, 'location_name');
                    if($instalasi == DocoConstants::INST_ID_RI){
                        $obj->badid = ArrayHelper::getValue($value, 'kamarruangan_kode');
                        $obj->badname = ArrayHelper::getValue($value, 'no_tempattidur');
                    }else{
                        $obj->badid = '0000';
                        $obj->badname = '0000';
                    }
                    $obj->classid = ArrayHelper::getValue($value, 'patient_class');
                    $obj->classname = ArrayHelper::getValue($value, 'patient_class_name');
                    $obj->cito = ArrayHelper::getValue($value, 'priority') == 'S' ? 'Y' : 'N';
                    $obj->medlegal = 'N';
                    $obj->userid = ArrayHelper::getValue($value, 'pasien_id');
                    $objectTest = (object) $tmp;
                    $obj->ordertest = $objectTest;

                    $payload = $obj->buildArray();
                    $sendData[] = $payload;

                    $response = (new LisService)->cancelOrder($payload, function ($data) {
                        return $data;
                    }, 'POST');
                    $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                    $responUpdate = json_encode($response);

                    $logServiceInternal[] = [
                        'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                        'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                        'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                        'payload' => json_encode($payload),
                        'is_sent' => true,
                        'is_sending' => true,
                        'id_sync_sercon' => $prosesId,
                        'sync_respon' => json_encode($responUpdate),
                        'state' => 'delete'
                    ];  
                }
            }
        }

        return $logServiceInternal;
    }

    /**
     * @author Maulana Muhammad Rizky
     * Function ini merupakan batal order yang berasal dari pemeriksaan
     * Contoh ketika batal pemeriksaan satu tindakan.
     * 
     * @param penunjangId int
     */
    private function batalOrderFromPemeriksaan($penunjangId = null, $detailTindakan = null)
    {
        $tmpPasienMasukPenunjang = [];
        $logServiceInternal = [];

        if($detailTindakan != '' || !empty($detailTindakan)) {
            foreach ($detailTindakan as $key => $value) {
                if(isset($value['tindakanpelayanan_id'])) {
                    $dataTindakan = TindakanPelayanan::find(true)
                    ->where(
                    [
                        'tindakanpelayanan_id' => $value['tindakanpelayanan_id'], 
                    ])
                    ->asArray()
                    ->one();
                    
                    if(! empty($dataTindakan)) {
                        if(! in_array($dataTindakan['pasienmasukpenunjang_id'], $tmpPasienMasukPenunjang)) {
                            $tmpPasienMasukPenunjang[] = $dataTindakan['pasienmasukpenunjang_id'];
                        }
                    }
                }
            }

            if(empty($tmpPasienMasukPenunjang)) {
                $dataTindakan = TindakanPelayanan::find(true)
                ->where(
                [
                    'tindakanpelayanan_id' => $detailTindakan['tindakanpelayanan_id'], 
                ])
                ->asArray()
                ->one();
                if(! empty($dataTindakan)) {
                    if(! in_array($dataTindakan['pasienmasukpenunjang_id'], $tmpPasienMasukPenunjang)) {
                        $tmpPasienMasukPenunjang[] = $dataTindakan['pasienmasukpenunjang_id'];
                    }
                }
            }
        }

        foreach ($tmpPasienMasukPenunjang as $key => $value) {
            $integrasi = GenerateDataPatientLabRepositories::getData($value, null, null,null); 
            $isAps = false;
            $params = Yii::$app->params['iniFile'];
            $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
            if(! empty($integrasi)) {
                foreach ($integrasi as $key => $value) {
                    $tmp = [];
                    $groupCaraBayar = ArrayHelper::getValue($value, 'groupcarabayar_id');
                    $instalasi = ArrayHelper::getValue($value,'instalasi_id');
                    $isAps = ArrayHelper::getValue($value, 'is_aps', false);
    
                    /**
                     * Kondisi Ketika Aps dan bayar Umum Harus Bayar Dulu
                     * Kondisi Rajal dan bayar Umum Ketika Approve  
                     */
                    $pasienAps = ($groupCaraBayar == DocoConstants::GROUP_UMUM && !empty($isAps));
                    $pasienUMUMRJ = ($groupCaraBayar == DocoConstants::GROUP_UMUM && $instalasi == DocoConstants::INST_ID_RJ);
    
                    if($pasienAps || $pasienUMUMRJ) continue;
                    
                    $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');       
                    $doctor = ArrayHelper::getValue($value, 'ref_doctor_name');   
    
                    $pasienClass = $instalasi == DocoConstants::INST_ID_RI ? 'IP' : 'OP';
                    $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
                    $pasienmasukpenunjangId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
                    $dataExistingRekap = $this->getRekapLIS($pasienmasukpenunjangId, $pendaftaranId);
    
                    if(! empty($dataExistingRekap)) {
    
                        $obj = new LisObject;
                        /*Pid*/ 
                        $obj->username = $baseConfig['username'];
                        $obj->key = $baseConfig['key'];
                        $obj->pmrn = ArrayHelper::getValue($value, 'no_rekam_medik');
                        $obj->pname = ArrayHelper::getValue($value, 'nama_pasien');
                        $obj->sex = ArrayHelper::getValue($value, 'gender') == 'M' ? 'L' : 'P';
                        $obj->birthdt =  date('d.m.Y',strtotime(ArrayHelper::getValue($value, 'date_of_birth')));
                        $obj->address = ArrayHelper::getValue($value, 'address');
                        $obj->notlp = ArrayHelper::getValue($value, 'no_telepon_pasien');
                        if(!empty($value['tests'])){
                            foreach(json_decode($value['tests']) as $tes){
                                $tmp[] = ArrayHelper::getValue($tes, 'id');
                            }
                        }
    
                        $countTindakan = count($tmp);
                        // Kondisi ketika 
                        if($countTindakan == 0) {
                            $obj->ordercontrol = 'D';
                            $stateType = 'delete';
    
                            if(!empty($value['remove_tests'])){
                                foreach(json_decode($value['remove_tests']) as $tes){
                                    $tmp[] = ArrayHelper::getValue($tes, 'id');
                                }
                            }
        
                        }else{
                            $obj->ordercontrol = 'U';
                            $stateType = 'update';
                        }
    
                        $obj->ptype = $pasienClass;
                        $obj->regno = ArrayHelper::getValue($value, 'no_pendaftaran'); 
                        $obj->orderlab = ArrayHelper::getValue($value, 'order_no');
                        $obj->providerid = $penjaminId;
                        $obj->providername = ArrayHelper::getValue($value, 'penjamin_nama'); 
                        $obj->orderdate = date('d.m.Y H:i:s',strtotime(ArrayHelper::getValue($value, 'order_time')));
                        $obj->clinicalid = ArrayHelper::getValue($value, 'ref_doctor_id');
                        $obj->clinicalname = $doctor;
                        $obj->bangsalid = ArrayHelper::getValue($value, 'location_id');
                        $obj->bangsalname = ArrayHelper::getValue($value, 'location_name');
                        if($instalasi == DocoConstants::INST_ID_RI){
                            $obj->badid = ArrayHelper::getValue($value, 'kamarruangan_kode');
                            $obj->badname = ArrayHelper::getValue($value, 'no_tempattidur');
                        }else{
                            $obj->badid = '0000';
                            $obj->badname = '0000';
                        }
                        $obj->classid = ArrayHelper::getValue($value, 'patient_class');
                        $obj->classname = ArrayHelper::getValue($value, 'patient_class_name');
                        $obj->cito = ArrayHelper::getValue($value, 'priority') == 'S' ? 'Y' : 'N';
                        $obj->medlegal = 'N';
                        $obj->userid = ArrayHelper::getValue($value, 'pasien_id');
                        $objectTest = (object) $tmp;
                        $obj->ordertest = $objectTest;
    
                        $payload = $obj->buildArray();
                        $sendData[] = $payload;
    
                        $response = (new LisService)->cancelOrder($payload, function ($data) {
                            return $data;
                        }, 'POST');
                        $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                        $is_sent = ArrayHelper::getValue($response, 'Results.0.data.0.is_sent', false);
                        $responUpdate = $response;
                        
    
                        $logServiceInternal[] = [
                            'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                            'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                            'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                            'payload' => json_encode($payload),
                            'is_sent' => $is_sent,
                            'is_sending' => true,
                            'id_sync_sercon' => $prosesId,
                            'sync_respon' => json_encode($responUpdate),
                            'state' => $stateType
                        ];  
                    }
                }
            }
        }

        return $logServiceInternal;
    }

    /**
     * @author Maulana Muhammad Rizky
     * Function ini merupakan batal order dan tapi dari pendaftaran 
     */
    private function batalFromEditPendaftaran($pendaftaranId) 
    {
        $logServiceInternal = [];
        $integrasi = GenerateDataPatientLabRepositories::getData(null, $pendaftaranId, null,null); 
        $isAps = false;
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
        if(! empty($integrasi)) {
            foreach ($integrasi as $key => $value) {
                if(isset($value['tests'])) {
                    $tmp = [];
                    $groupCaraBayar = ArrayHelper::getValue($value, 'groupcarabayar_id');
                    $instalasi = ArrayHelper::getValue($value,'instalasi_id');
                    $isAps = ArrayHelper::getValue($value, 'is_aps', false);
        
                    /**
                     * Kondisi Ketika Aps dan bayar Umum Harus Bayar Dulu
                     * Kondisi Rajal dan bayar Umum Ketika Approve  
                     */
                    $pasienAps = ($groupCaraBayar == DocoConstants::GROUP_UMUM && !empty($isAps));
                    $pasienUMUMRJ = ($groupCaraBayar == DocoConstants::GROUP_UMUM && $instalasi == DocoConstants::INST_ID_RJ);
                    $pasienRJ = ($instalasi == DocoConstants::INST_ID_RJ);
                    $caraBayarUmum = DocoConstants::VAR_UMUM;
                    $pasienRD = ($instalasi == DocoConstants::INST_ID_RD);
                    $pasienRI = ($instalasi == DocoConstants::INST_ID_RI);
                    
                    $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');                         
                    $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');                         
                    $doctor = ArrayHelper::getValue($value, 'ref_doctor_name');   ;   
        
                    $pasienClass = $instalasi == DocoConstants::INST_ID_RI ? 'IP' : 'OP';
                    $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
                    $pasienmasukpenunjangId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
                    $dataExistingRekap = $this->getRekapLIS($pasienmasukpenunjangId, $pendaftaranId);

                    $obj = new LisObject;
                    /*Pid*/ 
                    $obj->username = $baseConfig['username'];
                    $obj->key = $baseConfig['key'];
                    $obj->pmrn = ArrayHelper::getValue($value, 'no_rekam_medik');
                    $obj->pname = ArrayHelper::getValue($value, 'nama_pasien');
                    $obj->sex = ArrayHelper::getValue($value, 'gender') == 'M' ? 'L' : 'P';
                    $obj->birthdt =  date('d.m.Y',strtotime(ArrayHelper::getValue($value, 'date_of_birth')));
                    $obj->address = ArrayHelper::getValue($value, 'address');
                    $obj->notlp = ArrayHelper::getValue($value, 'no_telepon_pasien');

                    if(!empty($value['tests'])){
                        foreach(json_decode($value['tests']) as $tes){
                            $tmp[] = ArrayHelper::getValue($tes, 'id');
                        }
                    }

                    // Kondisi ketika cara bayar umum
                    if($this->carabayar_id == $caraBayarUmum) {
                        if($pasienRI || $pasienRD) {
                            $obj->ordercontrol = 'U';
                            $stateType = 'update';
                        } else {
                            $obj->ordercontrol = 'D';
                            $stateType = 'delete';
                            if($this->penjamin_lama != '') {
                                $penjaminValue = Penjamin::find()->select(['penjamin_id', 'penjamin_nama'])->where(['penjamin_id' => $this->penjamin_lama])->asArray()->one();
                                $penjaminId = ArrayHelper::getValue($penjaminValue, 'penjamin_id');
                                $penjaminNama = ArrayHelper::getValue($penjaminValue, 'penjamin_nama');
                            }
                        }
                    }else{
                        if($pasienRI || $pasienRD) {
                            $obj->ordercontrol = 'U';
                            $stateType = 'update';
                        } else {
                            $obj->ordercontrol = 'N';
                            $stateType = null;
                        }
                    }

                    $obj->ptype = $pasienClass;
                    $obj->regno = ArrayHelper::getValue($value, 'no_pendaftaran'); 
                    $obj->orderlab = ArrayHelper::getValue($value, 'order_no');
                    $obj->providerid = $penjaminId;
                    $obj->providername = $penjaminNama;
                    $obj->orderdate = date('d.m.Y H:i:s',strtotime(ArrayHelper::getValue($value, 'order_time')));
                    $obj->clinicalid = ArrayHelper::getValue($value, 'ref_doctor_id');
                    $obj->clinicalname = $doctor;
                    $obj->bangsalid = ArrayHelper::getValue($value, 'location_id');
                    $obj->bangsalname = ArrayHelper::getValue($value, 'location_name');
                    if($instalasi == DocoConstants::INST_ID_RI){
                        $obj->badid = ArrayHelper::getValue($value, 'kamarruangan_kode');
                        $obj->badname = ArrayHelper::getValue($value, 'no_tempattidur');
                    }else{
                        $obj->badid = '0000';
                        $obj->badname = '0000';
                    }
                    $obj->classid = ArrayHelper::getValue($value, 'patient_class');
                    $obj->classname = ArrayHelper::getValue($value, 'patient_class_name');
                    $obj->cito = ArrayHelper::getValue($value, 'priority') == 'S' ? 'Y' : 'N';
                    $obj->medlegal = 'N';
                    $obj->userid = ArrayHelper::getValue($value, 'pasien_id');
                    $objectTest = (object) $tmp;
                    $obj->ordertest = $objectTest;

                    $payload = $obj->buildArray();
                    $sendData[] = $payload;

                    // Ketika kondisi data ada pada integrasi_roche_r dengan kondisi sudah terkirim ke LIS.
                    if(! empty($dataExistingRekap)) {
                        // Ketika kondisi data sudah ada tetapi berubah penjamin berkali - kali
                        if($isAps || $pasienRJ) {
                            continue;
                        } else {
                            if($this->carabayar_id == $caraBayarUmum) { 
                                if($pasienRI || $pasienRD) {
                                    // Kondisi update order ketika pasien RI dan RD
                                    $response = (new LisService)->orderLis($payload, function ($data) {
                                        return $data;
                                    }, 'POST');
                                } else {
                                    // Kondisi cancel order ketika bukan pasien RI dan RD
                                    $response = (new LisService)->cancelOrder($payload, function ($data) {
                                        return $data;
                                    }, 'POST');
                                }
                            } else {
                                $response = (new LisService)->orderLis($payload, function ($data) {
                                    return $data;
                                }, 'POST');
                            }
    
                            $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                            $responUpdate = json_encode($response);
    
                            $logServiceInternal[] = [
                                'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                                'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                                'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                                'payload' => json_encode($payload),
                                'is_sent' => true,
                                'is_sending' => true,
                                'id_sync_sercon' => $prosesId,
                                'sync_respon' => $responUpdate,
                                'state' => $stateType
                            ]; 
                        }

                    } else {
                        // Data order dapat di buat ketika pasien jenisnya asuransi
                        if($isAps || $pasienRJ) continue;

                        $response = (new LisService)->orderLis($payload, function ($data) {
                            return $data;
                        }, 'POST');

                        $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                        $responseCreate = json_encode($response);
                        $is_sent = ArrayHelper::getValue($response, 'Results.0.data.0.is_error', false);
                        $existingRekap[] = $dataExistingRekap;
        
                        $logServiceInternal[] = [
                            'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                            'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                            'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                            'payload' => json_encode($payload),
                            'is_sent' => $is_sent,
                            'is_sending' => true,
                            'id_sync_sercon' => $prosesId,
                            'sync_respon' => $responseCreate,
                        ];  
                    }
                }
            } 
        }
        return $logServiceInternal;
    }


    /**
     * @author Maulana Muhammad Rizky
     * Function ini merupakan update penjamin dari pendaftaran dengan kondisi cara bayar sama.
     */
    private function updatePenjaminFromPendaftaran($pendaftaranId) 
    {
        $logServiceInternal = [];
        $integrasi = GenerateDataPatientLabRepositories::getData(null, $pendaftaranId, null,null); 
        $isAps = false;
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
        if(! empty($integrasi)) {
            foreach ($integrasi as $key => $value) {
                if(isset($value['tests'])) {
                    $tmp = [];
                    $groupCaraBayar = ArrayHelper::getValue($value, 'groupcarabayar_id');
                    $instalasi = ArrayHelper::getValue($value,'instalasi_id');
                    $isAps = ArrayHelper::getValue($value, 'is_aps', false);
        
                    /**
                     * Kondisi Ketika Aps dan bayar Umum Harus Bayar Dulu
                     * Kondisi Rajal dan bayar Umum Ketika Approve  
                     */
                    $pasienAps = ($groupCaraBayar == DocoConstants::GROUP_UMUM && !empty($isAps));
                    $pasienUMUMRJ = ($groupCaraBayar == DocoConstants::GROUP_UMUM && $instalasi == DocoConstants::INST_ID_RJ);
                    $pasienRJ = ($instalasi == DocoConstants::INST_ID_RJ);
                    $caraBayarUmum = DocoConstants::VAR_UMUM;
                    $pasienAdmisi = ArrayHelper::getValue($value, 'pasienadmisi_id');
                    
                    $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');       
                    $doctor = ArrayHelper::getValue($value, 'ref_doctor_name');   
        
                    $pasienClass = $instalasi == DocoConstants::INST_ID_RI ? 'IP' : 'OP';
                    $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
                    $pasienmasukpenunjangId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
                    $dataExistingRekap = $this->getRekapLIS($pasienmasukpenunjangId, $pendaftaranId);

                    $obj = new LisObject;
                    /*Pid*/ 
                    $obj->username = $baseConfig['username'];
                    $obj->key = $baseConfig['key'];
                    $obj->pmrn = ArrayHelper::getValue($value, 'no_rekam_medik');
                    $obj->pname = ArrayHelper::getValue($value, 'nama_pasien');
                    $obj->sex = ArrayHelper::getValue($value, 'gender') == 'M' ? 'L' : 'P';
                    $obj->birthdt =  date('d.m.Y',strtotime(ArrayHelper::getValue($value, 'date_of_birth')));
                    $obj->address = ArrayHelper::getValue($value, 'address');
                    $obj->notlp = ArrayHelper::getValue($value, 'no_telepon_pasien');
                    if(!empty($value['tests'])){
                        foreach(json_decode($value['tests']) as $tes){
                            $tmp[] = ArrayHelper::getValue($tes, 'id');
                        }
                    }

                    $obj->ordercontrol = 'U';
                    $stateType = 'update';

                    $obj->ptype = $pasienClass;
                    $obj->regno = ArrayHelper::getValue($value, 'no_pendaftaran'); 
                    $obj->orderlab = ArrayHelper::getValue($value, 'order_no');
                    if($instalasi == DocoConstants::INST_ID_RD) {
                        if(!empty($pasienAdmisi)) {
                            $penjaminRanap = ArrayHelper::getValue($value, 'penjamin_ranap');
                            $penjaminNama = Penjamin::find()->select(['penjamin_id', 'penjamin_nama'])->where(['penjamin_id' => $penjaminRanap])->asArray()->one();
                            $obj->providerid = ArrayHelper::getValue($penjaminNama, 'penjamin_id');
                            $obj->providername = ArrayHelper::getValue($penjaminNama, 'penjamin_nama'); 
                        } else {
                            $obj->providerid = $penjaminId;
                            $obj->providername = ArrayHelper::getValue($value, 'penjamin_nama'); 
                        }
                    } else {
                        $obj->providerid = $penjaminId;
                        $obj->providername = ArrayHelper::getValue($value, 'penjamin_nama'); 
                    }
                    $obj->orderdate = date('d.m.Y H:i:s',strtotime(ArrayHelper::getValue($value, 'order_time')));
                    $obj->clinicalid = ArrayHelper::getValue($value, 'ref_doctor_id');
                    $obj->clinicalname = $doctor;
                    $obj->bangsalid = ArrayHelper::getValue($value, 'location_id');
                    $obj->bangsalname = ArrayHelper::getValue($value, 'location_name');
                    if($instalasi == DocoConstants::INST_ID_RI){
                        $obj->badid = ArrayHelper::getValue($value, 'kamarruangan_kode');
                        $obj->badname = ArrayHelper::getValue($value, 'no_tempattidur');
                    }else{
                        $obj->badid = '0000';
                        $obj->badname = '0000';
                    }
                    $obj->classid = ArrayHelper::getValue($value, 'patient_class');
                    $obj->classname = ArrayHelper::getValue($value, 'patient_class_name');
                    $obj->cito = ArrayHelper::getValue($value, 'priority') == 'S' ? 'Y' : 'N';
                    $obj->medlegal = 'N';
                    $obj->userid = ArrayHelper::getValue($value, 'pasien_id');
                    $objectTest = (object) $tmp;
                    $obj->ordertest = $objectTest;

                    $payload = $obj->buildArray();
                    $sendData[] = $payload;
                    
                    // Kondisi ketika cara bayar umum 
                    if($this->carabayar_id == $caraBayarUmum) {

                        if($isAps || $pasienRJ) continue;

                        // Kondisi pasien RI dan RD saja.
                        if(! empty($dataExistingRekap)) {

                            $response = (new LisService)->orderLis($payload, function ($data) {
                                return $data;
                            }, 'POST');
    
                            $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                            $responUpdate = json_encode($response);
                            $logServiceInternal[] = [
                                'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                                'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                                'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                                'payload' => json_encode($payload),
                                'is_sent' => true,
                                'is_sending' => true,
                                'id_sync_sercon' => $prosesId,
                                'sync_respon' => $responUpdate,
                                'state' => $stateType
                            ]; 
                        }
                    } else {
                    
                        // Ketika kondisi data ada pada integrasi_roche_r dengan kondisi sudah terkirim ke LIS.
                        if($isAps || $pasienRJ) continue;

                        if(! empty($dataExistingRekap)) {

                            $response = (new LisService)->orderLis($payload, function ($data) {
                                return $data;
                            }, 'POST');
    
                            $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                            $responUpdate = json_encode($response);
    
                            $logServiceInternal[] = [
                                'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                                'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                                'pasienkirimkeunitlain_id' => ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id'),
                                'payload' => json_encode($payload),
                                'is_sent' => true,
                                'is_sending' => true,
                                'id_sync_sercon' => $prosesId,
                                'sync_respon' => $responUpdate,
                                'state' => $stateType
                            ]; 
                        }
                    }
                }
            } 
        }
        return $logServiceInternal;
    }
}