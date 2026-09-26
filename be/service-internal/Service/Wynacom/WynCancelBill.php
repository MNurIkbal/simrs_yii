<?php

namespace Integrasi\Service\Wynacom;

use Yii;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Wynacom\Models\RekapCancelWyn;
use yii\db\Query;
use Integrasi\Service\Wynacom\Models\InfoPasienLabDetailView;
use Integrasi\Components\Services\WynacomService;
use Integrasi\Service\Wynacom\Models\HasilPemeriksaanLab;
use Integrasi\Service\Wynacom\Models\PasienMasukPenunjang;

class WynCancelBill extends \Integrasi\Contracts\DocoImplement
{

    public function execute()
    {
        $detailTindakan = $this->detail_tindakan;
        $noPendaftaran = $this->no_pendaftaran;
        $UserName = isset($this->user_identity['username']) ? $this->user_identity['username'] : "";
        $UserID = isset($this->user_identity['uid']) ? $this->user_identity['uid'] : "";
        $url = '/CancelOrder';
        $responseWyn = [];
        $payload = [];
        if (!empty($detailTindakan) && is_array($detailTindakan)) {
            foreach( $this->getData() as $row){
                $tp_id = isset($row['daftartindakan_kode']) ?  $row['daftartindakan_kode'] : null;
                $tp_nama = isset($row['daftartindakan_nama']) ?  $row['daftartindakan_nama'] : null;
                $no_masukpenunjang = isset($row['no_masukpenunjang']) ?  $row['no_masukpenunjang'] : null;
                $pasienmasukpenunjang_id = isset($row['pasienmasukpenunjang_id']) ?  $row['pasienmasukpenunjang_id'] : null;
                $tindakanpelayanan_id = isset($row['tindakanpelayanan_id']) ?  $row['tindakanpelayanan_id'] : null;
                if($tp_id != null){
                    $value = [
                        "OrderNumber" => $no_masukpenunjang,
                        "TestID" => $tp_id, 
                        "TestName" =>  $tp_nama,
                        "UserID" => $UserID,
                        "UserName" => $UserName,
                        "Reason" => "Pembatalan transaksi atas permintaan pasien",
                    ];

                    $sendWynacom = (new WynacomService)->sendWynacom($url, $value, function ($data) {
                        return $data;
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
                    $additional_data = [
                        'tindakanpelayanan_id' => $tindakanpelayanan_id
                    ];
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

                    if($status_response){
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
                        else {
                            $header = PasienMasukPenunjang::findOne($pasienmasukpenunjang_id);
                            $model->attributes = $header;
                            $model->pasienmasukpenunjang_id = $pasienmasukpenunjang_id;
                            $model->pasien_id = !empty($header['pasien_id']) ? $header['pasien_id'] : null;
                            $model->pasienadmisi_id = !empty($header['pasienadmisi_id']) ? $header['pasienadmisi_id'] : null;
                            $model->pendaftaran_id = !empty($header['pendaftaran_id']) ? $header['pendaftaran_id'] : null;
                            $model->tindakanpelayanan_id = $tindakanpelayanan_id;
                            $model->status_pemeriksaan = DocoConstants::ST_P_PEN_BTL;
                            if($model->validate()) {
                                $model->save();
                            }
                        }
                    }
                }
            }
        } 

        return json_encode([
            'service' => 'Wynacom-WynCancelBill',
            'payload' =>  json_encode($payload),
            'response' =>  json_encode($response),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
    private function getData()
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
            ])
            ->innerJoin('pendaftaran_t c', 'c.pendaftaran_id = infopasienlabdetail_v.pendaftaran_id')
            ->innerJoin('pasienmasukpenunjang_t d', 'd.pasienmasukpenunjang_id = infopasienlabdetail_v.pasienmasukpenunjang_id')
            ->innerJoin('daftartindakan_m e', 'e.daftartindakan_id = infopasienlabdetail_v.daftartindakan_id');
        if (!empty($ids) ) {
            $qLab->andWhere(['in', 'infopasienlabdetail_v.tindakanpelayanan_id', $ids]);
        }
        if (!empty($registNo) ) {
            $qLab->andWhere(['c.no_pendaftaran' => $registNo]);
        }
        return $qLab->getDataArray();
    }
}