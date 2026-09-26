<?php

namespace app\modules\v1\repositories;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use SirsCore\features\FeatureTindakanBmhp;

use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoResepturDetailView;
use app\components\ApotekComponent;

use app\modules\v1\models\Reseptur;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\Loket;
use app\modules\v1\models\InfoPenjualanResepDetailView;
use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\PembatalanResep;
use app\modules\v1\models\ObatAlkesPasien;

use app\modules\v1\models\RiwayatAlergiView;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\InfoStokObatAlkesFn;

class ResepRepository
{
	public $_response;

    /*
    *cari data resep berdasarkan noresep
    *jika noresep adalah reseptur tanpa penjualanresep_id (resep dari RI/RD belum diserahkan)
    *   maka update status reseptur & ambil data obat dari resepturdetail
    *jika noresep adalah reseptur dengan penjualanresep_id maka resep dari RJ
    *   maka 
    *jika noresep adalah penjualanresep maka resep langsung dari apotek
    *jika tidak ditemukan respon 500
    *
    *jika belum diserahkan
    *  maka racikan stokfisikout, non racikan returnavail
    *jika telah diserahkan
    *  maka racikan tetap, non racikan stokfisikin
    */
	public function batal($no_resep)
	{
		$connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $reseptur = Reseptur::findOne(['noresep'=>$no_resep]);

            if(!is_null($reseptur)){

                $this->batalkanReseptur($reseptur);

                if(empty($reseptur->penjualanresep_id)){

                    $this->updateStokByReseptur($reseptur);
                    $transaction->commit();
                    $this->setCompletedResponse([
                        'title' => 'Berhasil Dibatalkan',
                        'message' => 'Pembatalan Reseptur berhasil!',
                    ]);
                    return true;
                }else{
                    $this->validasiCloseBill($reseptur['pendaftaran_id']);
                }

                $penjualanResep = PenjualanResep::findOne(['penjualanresep_id'=>$reseptur->penjualanresep_id]);
                
            } else {
                $penjualanResep = PenjualanResep::findOne(['noresep'=>$no_resep]);

                if(isset($penjualanResep['pendaftaran_id'])) {
                    $this->validasiCloseBill($penjualanResep['pendaftaran_id']);
                }

                if(isset($penjualanResep['reseptur_id'])) {
                    $reseptur = Reseptur::findOne(['reseptur_id' => $penjualanResep['reseptur_id']]);
                    $this->batalkanReseptur($reseptur);
                }
            }

            if(is_null($penjualanResep))
                throw new \Exception("Data Resep Tidak Ditemukan", 1);

            if($penjualanResep->status_bayar == DocoConstants::LUNAS)
                throw new \Exception("Penjualan Resep Telah Dibayar", 1);
                
            if($penjualanResep->status_reseptur == DocoConstants::VAR_B_R)
                throw new \Exception("Penjualan Resep Telah Dibatalkan", 1);

            $pembatalanResep = new PembatalanResep;
            $pembatalanResep->penjualanresep_id = $penjualanResep->penjualanresep_id;
            $pembatalanResep->tgl_pembatalan = date('Y-m-d H:i:s');
            $pembatalanResep->petugas_batal_id = Yii::$app->jwt->user->loginpemakai_id;
            if(!$pembatalanResep->save()){
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'data' => $pembatalanResep->errors
                ];
            }

            $this->updateStokByPenjualanResep($penjualanResep,$pembatalanResep);
            
            $penjualanResep->pembatalanresep_id = $pembatalanResep->getPrimaryKey();
            $penjualanResep->status_reseptur = DocoConstants::VAR_B_R;
            $penjualanResep->is_deleted = TRUE;
            
            if(!is_null($penjualanResep->reseptur_kronis_asal_id) || !is_null($penjualanResep->resep_kronis_asal_id) || !is_null($penjualanResep->hasil_resep_kronis_id)) {
                $tipeResep = !is_null($penjualanResep->hasil_resep_kronis_id) ? 'asal' : 'hasil';
                if($tipeResep == 'asal') {
                    $this->resetKronisReferenceAsal($penjualanResep->hasil_resep_kronis_id);
                } else {
                    $this->resetKronisReferenceHasil($penjualanResep->reseptur_kronis_asal_id, $penjualanResep->resep_kronis_asal_id);
                }
            }
            
            $penjualanResep->reseptur_kronis_asal_id = NULL;
            $penjualanResep->resep_kronis_asal_id = NULL;
            $penjualanResep->hasil_resep_kronis_id = NULL;
            
            if(!$penjualanResep->save()){
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'data' => $penjualanResep->errors
                ];
            }

            $this->setCompletedResponse([
                'title' => 'Berhasil Dibatalkan',
                'text' => 'Pembatalan Resep berhasil!',
            ]);
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->setCompletedResponse([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);
            \Yii::$app->response->statusCode = 422;
            $this->setCompletedResponse([
                'title' => 'Gagal',
                'message' => $e->getMessage(),
                'text' => $e->getMessage()
            ]);
        }
	}

    protected function batalkanReseptur($reseptur)
    {
        if($reseptur->status_reseptur == DocoConstants::VAR_B_R) {
            $this->setCompletedResponse([
                'title' => 'Berhasil Dibatalkan',
                'text' => 'Pembatalan Resep berhasil!',
            ]);
        }
        
        $reseptur->status_reseptur = DocoConstants::VAR_B_R;
        $reseptur->hasil_resep_kronis_id = NULL;

        if(!$reseptur->save()) {
            throw new \Exception("Gagal Membatalkan Reseptur", 1);
        }
    }

    protected function resetKronisReferenceAsal($hasil_resep_kronis_id) {
        $penjualanResep = PenjualanResep::find()->where([
            'penjualanresep_id' => $hasil_resep_kronis_id,
            'status_reseptur' => DocoConstants::RESEPTUR_SUDAH_DIPROSES
        ])->one();

        $reseptur = Reseptur::find()->where([
            'reseptur_id' => $penjualanResep->reseptur_kronis_asal_id,
            'status_reseptur' => DocoConstants::RESEPTUR_SUDAH_DIPROSES
        ])->one();
        
        if(!empty($reseptur)) {
            $reseptur->hasil_resep_kronis_id = NULL;
            if(!$reseptur->save()) throw new \Exception("Gagal reset reference kronis reseptur", 1);
        }

        if(!empty($penjualanResep)) {
            $penjualanResep->resep_kronis_asal_id = NULL;
            $penjualanResep->reseptur_kronis_asal_id = NULL;
            if(!$penjualanResep->save()) throw new \Exception("Gagal reset reference kronis resep", 1);
        }
    }

    protected function resetKronisReferenceHasil($reseptur_kronis_asal_id, $resep_kronis_asal_id) {
        $penjualanResep = PenjualanResep::find()->where([
            'penjualanresep_id' => $resep_kronis_asal_id
        ])->one();

        $reseptur = Reseptur::find()->where([
            'reseptur_id' => $reseptur_kronis_asal_id
        ])->one();
        
        if(!empty($reseptur)) {
            $reseptur->hasil_resep_kronis_id = NULL;
            if(!$reseptur->save()) throw new \Exception("Gagal reset reference kronis reseptur", 1);
        }

        if(!empty($penjualanResep)) {
            $penjualanResep->hasil_resep_kronis_id = NULL;
            if(!$penjualanResep->save()) throw new \Exception("Gagal reset reference kronis resep", 1);
        }
    }

    protected function updateStokByReseptur($reseptur)
    {
        $_POST['ruangan_id'] = $reseptur->ruangan_id;
        $_listObatAlkes = ResepturDetail::find()->where(['reseptur_id'=>$reseptur->reseptur_id])->asArray()->all();

        $_listObatAlkesRacikan = $_listObatAlkesNonRacikan = $listResepturDetailRacikan = [];
        foreach ($_listObatAlkes as $_vlistObatAlkes) {
            if($_vlistObatAlkes['racikan_id'] == 1){
                $qty_ = is_null($_vlistObatAlkes['det_konversi']) ? $_vlistObatAlkes['qty_konversi'] : $_vlistObatAlkes['det_konversi'];
                $_listObatAlkesRacikan[$_vlistObatAlkes['obatalkes_id']][] = floatval($qty_);
                $listResepturDetailRacikan[] = $_vlistObatAlkes;
            }else{
                $qty_ = is_null($_vlistObatAlkes['det_konversi']) ? $_vlistObatAlkes['qty_konversi'] : $_vlistObatAlkes['det_konversi'];
                $_listObatAlkesNonRacikan[$_vlistObatAlkes['obatalkes_id']][] = floatval($qty_);
            }
        }

        $_listObatAlkesRacikan = $this->sumArrayByKey($_listObatAlkesRacikan);
        $_listObatAlkesNonRacikan = $this->sumArrayByKey($_listObatAlkesNonRacikan);

        $_detailDataObat = [];
        foreach ($listResepturDetailRacikan as $key => $value) {
            $_detailDataObat[$key] = [
                'obatalkes_id' => $value['obatalkes_id'],
                'qty' => is_null($value['det_konversi']) ? $value['qty_konversi'] : $value['det_konversi'],
                'satuankecil_id' => $value['satuankecil_id'],
                'harganetto' => $value['harganetto_reseptur']
            ];
        }
        if(count($_listObatAlkesRacikan)>0) $this->keluarkanStokFisik($_listObatAlkesRacikan,$reseptur->ruangan_id,$_detailDataObat);
    }

    protected function updateStokByPenjualanResep($penjualanResep,$pembatalanResep)
    {
        if(!$penjualanResep instanceof PenjualanResep) throw new \Exception("Parameter Salah", 1);
        
        $_POST['ruangan_id'] = $penjualanResep->ruangan_id;

        $_listObatAlkes = ObatAlkesPasien::find()->where(['penjualanresep_id' => $penjualanResep->penjualanresep_id])->asArray()->all();

        $_listObatAlkesRacikan = $_listObatAlkesNonRacikan = [];
        $listOAPRacikan = $listOAPNonRacikan = [];

        foreach ($_listObatAlkes as $_vlistObatAlkes) {
            if($_vlistObatAlkes['racikan_id'] == 1){
                $qty_ = is_null($_vlistObatAlkes['det_konversi']) ? $_vlistObatAlkes['qty_konversi'] : $_vlistObatAlkes['det_konversi'];
                $_listObatAlkesRacikan[$_vlistObatAlkes['obatalkes_id']][] = floatval($qty_);
                $listOAPRacikan[] = $_vlistObatAlkes;
            }else{
                $qty_ = is_null($_vlistObatAlkes['det_konversi']) ? $_vlistObatAlkes['qty_konversi'] : $_vlistObatAlkes['det_konversi'];
                $_listObatAlkesNonRacikan[$_vlistObatAlkes['obatalkes_id']][] = floatval($qty_);
                $listOAPNonRacikan[] = $_vlistObatAlkes;
            }
        }

        $_listObatAlkesRacikan = $this->sumArrayByKey($_listObatAlkesRacikan);
        $_listObatAlkesNonRacikan = $this->sumArrayByKey($_listObatAlkesNonRacikan);

        $_detailDataObat = [];
        foreach ($listOAPRacikan as $key => $value) {
            $_detailDataObat[$key] = [
                'obatalkes_id' => $value['obatalkes_id'],
                'qty' => is_null($value['det_konversi']) ? $value['qty_konversi'] : $value['det_konversi'],
                'satuankecil_id' => $value['satuankecil_id'],
                'harganetto' => $value['harganetto_oa'],
                'obatalkespasien_id' => $value['obatalkespasien_id']
            ];
        }

        if($penjualanResep->status_reseptur != DocoConstants::RESEPTUR_DISERAHKAN){
            if(count($_listObatAlkesRacikan)>0) $this->keluarkanStokFisik($_listObatAlkesRacikan,$penjualanResep->ruangan_id,$_detailDataObat,false);
            if(count($_listObatAlkesNonRacikan)>0) $this->kembalikanStokTersedia($_listObatAlkesNonRacikan,$penjualanResep->ruangan_id);
        }elseif ($penjualanResep->status_reseptur == DocoConstants::RESEPTUR_DISERAHKAN) {
            if(count($_listObatAlkesNonRacikan)>0) $this->kembalikanStokFisik($_listObatAlkesNonRacikan,$penjualanResep->ruangan_id,$listOAPNonRacikan,$pembatalanResep);
        }

        ObatAlkesPasien::updateAll(['is_deleted'=>TRUE],['penjualanresep_id'=>$penjualanResep->penjualanresep_id]);
    }

    protected function sumArrayByKey($array)
    {
        if(is_array($array) && count($array)>0){
            foreach ($array as $key => $value) {
                if(is_array($value)) $array[$key] = array_sum($value);
            }
        }

        return $array;
    }

    /*
    *   example 
        $listObat = [
            {obatalkes_id} => {qty transaksi}
        ];
        $listObat = [
            49 => 2,
            33 => 3
        ];
    *
    */
    protected function keluarkanStokFisik($listObat, $ruanganID, $detailObat, $stok = true)
    {
        foreach ($listObat as $key => $value) {
            $listObat[$key] = ceil($value);
        }
        $obat = array_keys($listObat);
        $dataStokR = $this->getDataStokR($obat,$ruanganID);

        foreach ($dataStokR as $k => $rowStokR) {
            $dataStokR[$k]['qty_dipesan_update'] = (int) $rowStokR['qty_dipesan'] - $listObat[$rowStokR['obatalkes_id']];
            $dataStokR[$k]['qty_tersedia_update'] = (int) $rowStokR['qty_tersedia'] + $listObat[$rowStokR['obatalkes_id']];
            $dataStokR[$k]['ruangan_id'] = $ruanganID;
        }

        $qty_update = [
                'qty_dipesan' => array_column($dataStokR, 'qty_dipesan_update'),
                'qty_tersedia' => array_column($dataStokR, 'qty_tersedia_update')
            ];

        $updateCondition = [
            'obatalkes_id' => array_column($dataStokR, 'obatalkes_id'),
            'ruangan_id' => array_column($dataStokR, 'ruangan_id')
        ];

        ApotekComponent::updateMultiple('stokobatalkes_r', $qty_update, $updateCondition);

        $detailTrans = [];
        foreach ($detailObat as $key => $value) {
            $detailTrans[] = [
                'obatalkes_id' => $value['obatalkes_id'],
                'qty_satuanpakai' => ceil(floatval($value['qty'])),
                'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                'harganetto' => empty($value['harganetto']) ? 0 : $value['harganetto'],
                'obatalkespasien_id' => isset($value['obatalkespasien_id'])?$value['obatalkespasien_id']:null,
                'persendiscount' => 0,
                'persenppn' => 0,
                'persenmargin' => 0,
                'jmldiscount' => 0,
                'jmlmargin' => 0,
                'jmlppn' => 0
            ];
        }
        if(is_array($detailTrans) && count($detailTrans)>0 && $stok){
            $potongStok = FeatureTindakanBmhp::stokObatAlkes($detailTrans, false);
            if(!$potongStok) throw new \Exception("Potong Stok Gagal", 1);
        }
    }

    protected function kembalikanStokTersedia($listObat, $ruanganID)
    {
        foreach ($listObat as $key => $value) {
            $listObat[$key] = ceil($value);
        }
        $obat = array_keys($listObat);
        $dataStokR = $this->getDataStokR($obat,$ruanganID);
        
        foreach ($dataStokR as $k => $rowStokR) {
            $dataStokR[$k]['qty_dipesan_update'] = (int) $rowStokR['qty_dipesan'] - $listObat[$rowStokR['obatalkes_id']];
            $dataStokR[$k]['qty_tersedia_update'] = (int) $rowStokR['qty_tersedia'] + $listObat[$rowStokR['obatalkes_id']];
            $dataStokR[$k]['ruangan_id'] = $ruanganID;
        }

        $qty_update = [
                'qty_dipesan' => array_column($dataStokR, 'qty_dipesan_update'),
                'qty_tersedia' => array_column($dataStokR, 'qty_tersedia_update')
            ];

        $updateCondition = [
            'obatalkes_id' => array_column($dataStokR, 'obatalkes_id'),
            'ruangan_id' => array_column($dataStokR, 'ruangan_id')
        ];

        ApotekComponent::updateMultiple('stokobatalkes_r', $qty_update, $updateCondition);
    }

    protected function kembalikanStokFisik($listObat,$ruanganID,$listOAP,$pembatalanResep)
    {
        foreach ($listObat as $key => $value) {
            $listObat[$key] = ceil($value);
        }
        $obat = array_keys($listObat);

        $dataMasterObatAlkes = [];
        if(is_array($obat) && count($obat)>0){
            $impObat = "(".implode(',', $obat).")";
            $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata FROM obatalkes_m WHERE obatalkes_id IN {$impObat}";
            $dataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
            if(is_array($dataMasterObatAlkes) && count($dataMasterObatAlkes)>0) $dataMasterObatAlkes = array_column($dataMasterObatAlkes, 'hargaratarata','obatalkes_id');
        }

        $listOfOAnonracikan = array_column($listOAP, 'obatalkespasien_id');
        $stokCardBySoldDrugs = StokObatAlkes::find()->where(['IN','obatalkespasien_id',$listOfOAnonracikan])->asArray()->all();

        $stokCardOuts = $stokCardIns = [];
        foreach ($stokCardBySoldDrugs as $stokCard) {
            if($stokCard['qtystok_out']>0){
                $stokCardOuts[] = $stokCard;
            }else{
                $stokCardIns[$stokCard['obatalkes_id']][$stokCard['tglkadaluarsa']][] = $stokCard;
            }
        }
        $listCanceledStokCard=[];
        foreach ($stokCardOuts as $stokCardOut) {
            $qtyCanceled = $stokCardOut['qtystok_out'];
            if(isset($stokCardIns[$stokCardOut['obatalkes_id']][$stokCardOut['tglkadaluarsa']]) && is_array($stokCardIns[$stokCardOut['obatalkes_id']][$stokCardOut['tglkadaluarsa']])){
                $_stokCardInGroupByDate = $stokCardIns[$stokCardOut['obatalkes_id']][$stokCardOut['tglkadaluarsa']];
                foreach ($_stokCardInGroupByDate as $_stokCardIn) {
                    $qtyCanceled = $qtyCanceled - $_stokCardIn['qtystok_in'];
                }
            }
            $listCanceledStokCard[] = [
                'ruangan_id' => $stokCardOut['ruangan_id'],
                'obatalkespasien_id' => $stokCardOut['obatalkespasien_id'],
                'pembatalanresep_id' => $pembatalanResep->getPrimaryKey(),
                'obatalkes_id' => $stokCardOut['obatalkes_id'],
                'satuankecil_id' => $stokCardOut['satuankecil_id'],
                'tglkadaluarsa' => $stokCardOut['tglkadaluarsa'],
                'nobatch' => $stokCardOut['nobatch'],
                'tglstok_in' => date('Y-m-d H:i:s'),
                'qtystok_in' => $qtyCanceled,
                'stokoa_aktif' => true,
                // 'stokobatalkesasal_id' => $stokCardOut['stokobatalkesasal_id'],
                'created_by' => Yii::$app->jwt->user->loginpemakai_id,
                'qtystok_out' => 0,
                'harganetto' => $stokCardOut['harganetto'],
                'persendiscount' => $stokCardOut['persendiscount'],
                'persenppn' => $stokCardOut['persenppn'],
                'persenmargin' => $stokCardOut['persenmargin'],
                'jmlmargin' => $stokCardOut['jmlmargin'],
                'jmldiscount' => $stokCardOut['jmldiscount'],
                'jmlppn' => $stokCardOut['jmlppn']
            ];
        }
        if(count($listCanceledStokCard)>0){
            foreach ($listCanceledStokCard as $k => $v) {
                $listCanceledStokCard[$k]['harga_netto_avg'] = isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0;
            }   
            StokObatAlkes::batchInsert($listCanceledStokCard);
        }
    }

    protected function getDataStokR($listObat,$ruangan_id)
    {
        if(!is_array($listObat)) return [];
        if(count($listObat)<1) return [];
        $inCondition = "(" . implode(", ", $listObat) . ")";
        $str = "SELECT
                    qty_dipesan, qty_tersedia, obatalkes_id
                FROM stokobatalkes_r
                WHERE obatalkes_id IN $inCondition
                    AND ruangan_id = $ruangan_id";
        $stok_obat = Yii::$app->db->createCommand($str)->queryAll();
        return $stok_obat;
    }

	public function setCompletedResponse($data)
	{
		$this->_response = $data;
	}

	public function completedResponse()
	{
		return $this->_response;
	}
    
    public function validasiCloseBill($pendaftaran_id)
    {
        $dataPendaftaran = Pendaftaran::findOne($pendaftaran_id);
        $isCloseBill = ArrayHelper::getValue($dataPendaftaran, 'is_close_bill', false);
        if($isCloseBill) {
            \Yii::$app->response->statusCode = 422;
            throw new \Exception('Pasien sudah dilakukan proses Lock Bill.', 1);
        }
    }
}
