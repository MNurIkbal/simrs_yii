<?php
/**
 * @author : Dede Herdiana
 * A product of PT. Citraraya Nuasatama
 * Powered by Sirs
 */
 
namespace Extensions\laboratorium;
use Yii;
use Doco\exceptions\ValidationException;
use Doco\components\DocoMessages;
use Doco\Services\KasirService;
use Doco\Services\WynacomService;
use Doco\models\Laboratorium\InfoPasienLabDetailView;
use app\modules\v1\models\RekapCancelWyn;
use app\modules\v1\models\PasienMasukPenunjangT;
use Doco\models\Laboratorium\HasilPemeriksaanLab;
use Doco\components\DocoConstants;

class BatalPemeriksaanLabWyn extends \Doco\processes\BatalPemeriksaanLabProcess
{
    protected $no_pendaftaran;
    protected $pasienmasukpenunjang_id;
    protected $detail_tindakan;
    protected $new_detail_tindakan;
    protected $integration_status = false;
    protected $fail_tindakan;

    protected function validation(){
        $no_pendaftaran = $this->_requestData->post('no_pendaftaran', null);
        $pasienmasukpenunjang_id = $this->_requestData->post('pasienmasukpenunjang_id', null);
        $detail_tindakan = $this->_requestData->post('detail_tindakan', []);
        

        if(empty($pasienmasukpenunjang_id) ){
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Parameter pasienmasukpenunjang_id tidak boleh kosong!"
            ]);
        }

        if(empty($no_pendaftaran) ){
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Parameter no_pendaftaran tidak boleh kosong!"
            ]);
        }

        if( empty($detail_tindakan) ){
            throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
            'text' => "Parameter detail_tindakan tidak boleh kosong!"
            ]);
        }
        $this->no_pendaftaran = $no_pendaftaran;
        $this->detail_tindakan = $detail_tindakan;
        $this->pasienmasukpenunjang_id = $pasienmasukpenunjang_id;
    }

    protected function integration(){
        $jwt = Yii::$app->jwt;

        $detailTindakan = $this->detail_tindakan;
        $noPendaftaran = $this->no_pendaftaran;
        $UserName = $jwt->user->nama_pemakai;
        $UserID = $jwt->user->loginpemakai_id;
        $url = '/CancelOrder';
        $responseWyn = [];
        $payload = [];
        $new_detail_tindakan = [];
        $fail_tindakan = '';

        if (!empty($detailTindakan) && is_array($detailTindakan)) {
            foreach( $this->getData() as $row){
                $tp_id = isset($row['daftartindakan_kode']) ?  $row['daftartindakan_kode'] : null;
                $tp_nama = isset($row['daftartindakan_nama']) ?  $row['daftartindakan_nama'] : null;
                $no_masukpenunjang = isset($row['no_masukpenunjang']) ?  $row['no_masukpenunjang'] : null;
                $pasienmasukpenunjang_id = isset($row['pasienmasukpenunjang_id']) ?  $row['pasienmasukpenunjang_id'] : null;
                $tindakanpelayanan_id = isset($row['tindakanpelayanan_id']) ?  $row['tindakanpelayanan_id'] : null;
                $add_pmp = !empty($row['add_pmp']) ? json_decode($row['add_pmp'], true) :[];
                $add_pkl = !empty($row['add_pkl']) ? json_decode($row['add_pkl'], true) :[];
                $additional_data = !empty($add_pkl) ? $add_pkl : (!empty($add_pmp) ? $add_pmp : []);
                $lisattr = !empty($additional_data['lisattr']) ? $additional_data['lisattr'] : [];
                $received_flag = !empty($lisattr['received_flag']) ? $lisattr['received_flag'] : '';

                if($tp_id != null){
                    $value = [
                        "OrderNumber" => $no_masukpenunjang,
                        "TestID" => $tp_id, 
                        "TestName" =>  $tp_nama,
                        "UserID" => $UserID,
                        "UserName" => $UserName,
                        "Reason" => "Pembatalan transaksi atas permintaan pasien",
                    ];

                    $sendWynacom = (new WynacomService)->sendWynacom($url,$value, function ($data, $result) {
                        return $result;
                    });
                    $responseWyn[] = $sendWynacom;
                    $payload[] = $value;
                    if(!empty($sendWynacom) && is_array($sendWynacom)){
                        $response = $sendWynacom;
                    }else{
                        $response = [
                            "Status" => [
                                "OK"=> false,
                                "Code"=> 0,
                                "Messages"=> "GAGAL"
            
                            ],
                            "OrderNumber"=> $value['OrderNumber'],
                            "TestID"=> $value['TestID'],
                            "TestName"=> $value['TestName'],
                            "UserID"=> $value['UserID'],
                            "UserName"=> $value['UserName'],
                            "Reason"=> $value['Reason']
                        ];
                    }
                    
                    $status_response = isset($response['Status']['OK']) ? $response['Status']['OK'] : true;
                    $messages = isset($response['Status']['Messages']) ? $response['Status']['Messages'] : null;
                    $dataExist = RekapCancelWyn::find()->where(['testid' => $tp_id, 'ordernumber' => $no_masukpenunjang])->one();
                    $dataExist = !empty($dataExist) ? $dataExist : new RekapCancelWyn;
                    if(!empty($response)){
                        $qRekap = $dataExist;
                        $qRekap->status = !empty($response['Status']) ? json_encode($response['Status']) : '' ;
                        $qRekap->ordernumber = isset($response['OrderNumber']) ? $response['OrderNumber'] : null ;
                        $qRekap->testid = isset($response['TestID']) ? $response['TestID'] : null ;
                        $qRekap->testname = isset($response['TestName']) ? $response['TestName'] : null ;
                        $qRekap->userid = isset($response['UserID']) ? $response['UserID'] : null ;
                        $qRekap->username = isset($response['UserName']) ? $response['UserName'] : null ;
                        $qRekap->reason = isset($response['Reason']) ? $response['Reason'] : null ;
                        $qRekap->status_response = $status_response;
                        $qRekap->messages = $messages;
                        $qRekap->additional_data = json_encode($additional_data);
                        $qRekap->save();
                    }

                    if($received_flag != 1){
                        $this->integration_status = true;
                        $new_detail_tindakan[]= [
                            'tindakanpelayanan_id' => $tindakanpelayanan_id,
                        ];
                    }else{
                        if($status_response){
                            $new_detail_tindakan[]= [
                                'tindakanpelayanan_id' => $tindakanpelayanan_id,
                            ];
                            $hasilPemeriksaan = HasilPemeriksaanLab::find()->where([
                                'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                                'tindakanpelayanan_id' => $tindakanpelayanan_id,
                            ])->asArray()->one();
                            
                            $model = empty($hasilPemeriksaan) ? new HasilPemeriksaanLab : $hasilPemeriksaan;
                            if(!empty($hasilPemeriksaan)) {
                                HasilPemeriksaanLab::updateAll(['status_pemeriksaan' => DocoConstants::ST_P_PEN_BTL], [
                                    'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                                    'tindakanpelayanan_id' => $tindakanpelayanan_id,
                                ]);
                            }
                        }else{
                            $this->integration_status = false;
                            $operator = !empty($fail_tindakan) ? ', ' : '';
                            $fail_tindakan .= $operator.$tp_nama;
                        }
                    }
                }
            }
            
            $this->new_detail_tindakan = $new_detail_tindakan;
            $this->fail_tindakan = $fail_tindakan;
        } 
    }

    protected function getData()
    {
        $registNo = $this->no_pendaftaran;

        if (empty($registNo)) return [];
        $ids = [];
        foreach($this->detail_tindakan as $val) {
            $ids[] = isset($val['tindakanpelayanan_id']) ? $val['tindakanpelayanan_id'] : [];
        }

        $qLab = InfoPasienLabDetailView::find(true)
            ->select([
                'infopasienlabdetail_v.tindakanpelayanan_id',
                'infopasienlabdetail_v.daftartindakan_id',
                'infopasienlabdetail_v.daftartindakan_nama',
                'infopasienlabdetail_v.pasienmasukpenunjang_id',
                'c.pendaftaran_id',
                'c.no_pendaftaran',
                'd.no_masukpenunjang',
                'e.daftartindakan_kode',
                'f.additional_data as add_pmp',
                'd.additional_data as add_pkl'
            ])
            ->leftJoin('pendaftaran_t c', 'c.pendaftaran_id = infopasienlabdetail_v.pendaftaran_id')
            ->leftJoin('pasienmasukpenunjang_t d', 'd.pasienmasukpenunjang_id = infopasienlabdetail_v.pasienmasukpenunjang_id')
            ->leftJoin('daftartindakan_m e', 'e.daftartindakan_id = infopasienlabdetail_v.daftartindakan_id')
            ->leftJoin('pasienkirimkeunitlain_t f', 'f.pasienkirimkeunitlain_id = d.pasienkirimkeunitlain_id');
        if (!empty($ids) ) {
            $qLab->andWhere(['in', 'infopasienlabdetail_v.tindakanpelayanan_id', $ids]);
        }
        if (!empty($registNo) ) {
            $qLab->andWhere(['c.no_pendaftaran' => $registNo]);
        }
        return $qLab->asArray()->all();
    }

    protected function processFlow(){
        $this->validation();
        $this->integration();
        // $this->validateIntegration();
        return [
            'status' => $this->integration_status,
            'fail_tindakan' => $this->fail_tindakan,
            'new_detail_tindakan' => $this->new_detail_tindakan
        ];
    }
}