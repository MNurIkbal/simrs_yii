<?php

namespace app\modules\v1\repositories;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use SirsCore\features\FeatureTindakanBmhp;

use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoResepturDetailView;
use app\components\ApotekComponent;

use app\modules\v1\models\Reseptur;
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

class ApproveResepRepository
{
	public $_response;

    public function batal_approve($no_resep)
	{
		$connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $reseptur = Reseptur::findOne(['noresep'=>$no_resep]);

            // kalau reseptur
            if(!is_null($reseptur)){

                $this->batalkanApprove($reseptur);

                if(empty($reseptur->penjualanresep_id)){

                    $this->updateStokByReseptur($reseptur);
                    $transaction->commit();
                    $this->setCompletedResponse([
                        'title' => 'Berhasil Dibatalkan',
                        'message' => 'Pembatalan Approve Reseptur berhasil!',
                    ]);
                    return true;
                }

                $penjualanResep = PenjualanResep::findOne(['penjualanresep_id'=>$reseptur->penjualanresep_id]);
                // $penjualanResep->is_deleted = TRUE;
                
            } else {

                $penjualanResep = PenjualanResep::findOne(['noresep'=>$no_resep]);

                if(isset($penjualanResep['reseptur_id'])) {
                    $reseptur = Reseptur::findOne(['reseptur_id' => $penjualanResep['reseptur_id']]);
                    $this->batalkanApprove($reseptur);
                }
            }

            if(is_null($penjualanResep))
                throw new \Exception("Data Resep Tidak Ditemukan", 1);

            if($penjualanResep->status_bayar == DocoConstants::LUNAS)
                throw new \Exception("Penjualan Resep Telah Dibayar", 1);
                
            if($penjualanResep->status_reseptur == DocoConstants::RESEPTUR_BELUM_DIPROSES)
                throw new \Exception("Approve Resep Telah Dibatalkan", 1);
            
                
            $this->updateStokByPenjualanResep($penjualanResep);
            
            $reseptur = Reseptur::find()->where(['penjualanresep_id'=>$penjualanResep->penjualanresep_id])->one();

            if(!is_null($reseptur)){
                $penjualanResep->is_deleted = TRUE;
                $reseptur->penjualanresep_id = null;
                if(!$reseptur->save()){
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'data' => $reseptur->errors
                    ];
                }
            }

            $penjualanResep->status_reseptur = DocoConstants::RESEPTUR_BELUM_DIPROSES;
            if(!$penjualanResep->save()){
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'data' => $penjualanResep->errors
                ];
            }

            $this->setCompletedResponse([
                'title' => 'Berhasil Dibatalkan',
                'text' => 'Pembatalan Approve Resep berhasil!',
            ]);
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->setCompletedResponse([
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->setCompletedResponse([
                'title' => 'Gagal',
                'message' => $e->getMessage(),
                'text' => $e->getMessage()
            ]);
        }
	}

    protected function batalkanApprove($reseptur)
    {
        if($reseptur->status_reseptur == DocoConstants::RESEPTUR_BELUM_DIPROSES) {
            throw new \Exception("Reseptur Belum Di Approve", 1);
        }
        
        $reseptur->status_reseptur = DocoConstants::RESEPTUR_BELUM_DIPROSES;

        if(!$reseptur->save()) {
            throw new \Exception("Gagal Membatalkan Reseptur", 1);
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
        if(count($_listObatAlkesRacikan)>0) $this->kembalikanStokDipesan($_listObatAlkesRacikan,$reseptur->ruangan_id,$_detailDataObat);
        if(count($_listObatAlkesNonRacikan)>0) $this->kembalikanStokDipesan($_listObatAlkesNonRacikan,$reseptur->ruangan_id);
    }

    protected function updateStokByPenjualanResep($penjualanResep)
    {
        if(!$penjualanResep instanceof PenjualanResep) throw new \Exception("Parameter Salah", 1);
        
        $_POST['ruangan_id'] = $penjualanResep->ruangan_id;

        $_listObatAlkes = ObatAlkesPasien::find()->where(['penjualanresep_id' => $penjualanResep->penjualanresep_id])->asArray()->all();

        $_listObatAlkesRacikan = $_listObatAlkesNonRacikan = [];
        $listOAPRacikan = $listOAPNonRacikan = [];
        // $listOAP =  [];

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
            // $listOAP = $_vlistObatAlkes;
        }

        $_listObatAlkesRacikan = $this->sumArrayByKey($_listObatAlkesRacikan);
        $_listObatAlkesNonRacikan = $this->sumArrayByKey($_listObatAlkesNonRacikan);


        if(count($_listObatAlkesRacikan)>0){
            $this->kembalikanStokDipesan($_listObatAlkesRacikan,$penjualanResep->ruangan_id);
            $this->kembalikanStokFisik($_listObatAlkesRacikan,$penjualanResep->ruangan_id,$listOAPRacikan);
        } 
        if(count($_listObatAlkesNonRacikan)>0){
            $this->kembalikanStokDipesan($_listObatAlkesNonRacikan,$penjualanResep->ruangan_id);
            $this->kembalikanStokFisik($_listObatAlkesRacikan,$penjualanResep->ruangan_id,$listOAPNonRacikan);
        }

        if(isset($penjualanResep->reseptur_id)) {
            ObatAlkesPasien::updateAll(['is_deleted'=>TRUE],['penjualanresep_id'=>$penjualanResep->penjualanresep_id]);
        }
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


    protected function kembalikanStokFisik($listObat,$ruanganID,$listOAP)
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
        /*
        $stokCardBySoldDrugs = StokObatAlkes::find()->where(['IN','obatalkespasien_id',$listOfOAnonracikan])->asArray()->all();
        */

        $impListOa = implode(',', $listOfOAnonracikan);
        $queryStok = "
            SELECT  
            st.obatalkespasien_id,
            st.tglkadaluarsa,
            st.nobatch,
            st.obatalkes_id,
            sum(qtystok_out -qtystok_in) as qty,
            om.harganetto,
            om.satuankecil_id
            FROM stokobatalkes_t st
            left join obatalkes_m om on om.obatalkes_id = st.obatalkes_id 
            where obatalkespasien_id in ($impListOa)
            group by st.tglkadaluarsa ,st.obatalkes_id ,st.obatalkespasien_id ,st.nobatch ,om.harganetto, om.satuankecil_id
        ";
        $stokCardBySoldDrugs = Yii::$app->db->createCommand($queryStok)->queryAll();

        $stokCardOuts = $stokCardIns = [];
        /*
        foreach ($stokCardBySoldDrugs as $stokCard) {
            if($stokCard['qtystok_out']>0){
                $stokCardOuts[] = $stokCard;
            }else{
                $stokCardIns[$stokCard['obatalkes_id']][$stokCard['tglkadaluarsa']][] = $stokCard;
            }
        }
        */

        $listCanceledStokCard=[];
        foreach ($stokCardBySoldDrugs as $stokCardOut) {
            $qtyCanceled = $stokCardOut['qty'];
            /*
            if(isset($stokCardIns[$stokCardOut['obatalkes_id']][$stokCardOut['tglkadaluarsa']]) && is_array($stokCardIns[$stokCardOut['obatalkes_id']][$stokCardOut['tglkadaluarsa']])){
                $_stokCardInGroupByDate = $stokCardIns[$stokCardOut['obatalkes_id']][$stokCardOut['tglkadaluarsa']];
                foreach ($_stokCardInGroupByDate as $_stokCardIn) {
                    $qtyCanceled = $qtyCanceled - $_stokCardIn['qtystok_in'];
                }
            }
            */

            $listCanceledStokCard[] = [
                'ruangan_id' => $ruanganID,
                'obatalkespasien_id' => $stokCardOut['obatalkespasien_id'],
                // 'pembatalanresep_id' => $pembatalanResep->getPrimaryKey(),
                'obatalkes_id' => $stokCardOut['obatalkes_id'],
                'satuankecil_id' => $stokCardOut['satuankecil_id'],
                'tglkadaluarsa' => $stokCardOut['tglkadaluarsa'],
                'nobatch' => $stokCardOut['nobatch'],
                'tglstok_in' => date('Y-m-d H:i:s'),
                'qtystok_in' => ceil($qtyCanceled),
                'stokoa_aktif' => true,
                // 'stokobatalkesasal_id' => $stokCardOut['stokobatalkesasal_id'],
                'created_by' => Yii::$app->jwt->user->loginpemakai_id,
                'qtystok_out' => 0,
                'harganetto' => $stokCardOut['harganetto'],
                'persendiscount' => 0,
                'persenppn' => 0,
                'persenmargin' => 0,
                'jmlmargin' => 0,
                'jmldiscount' => 0,
                'jmlppn' => 0
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
                    qty_dipesan, qty_tersedia,qty_sisa,obatalkes_id
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

    protected function kembalikanStokDipesan($listObat, $ruanganID)
    {
        foreach ($listObat as $key => $value) {
            $listObat[$key] = ceil($value);
        }
        $obat = array_keys($listObat);
        $dataStokR = $this->getDataStokR($obat,$ruanganID);
        
        foreach ($dataStokR as $k => $rowStokR) {
            $dataStokR[$k]['qty_dipesan_update'] = (int) $rowStokR['qty_dipesan'] + $listObat[$rowStokR['obatalkes_id']];
            $dataStokR[$k]['qty_stok_update'] = (int) $rowStokR['qty_sisa'] - $listObat[$rowStokR['obatalkes_id']];
            $dataStokR[$k]['ruangan_id'] = $ruanganID;
        }

        $qty_update = [
                'qty_dipesan' => array_column($dataStokR, 'qty_dipesan_update'),
                'qty_sisa' => array_column($dataStokR, 'qty_stok_update')
            ];

        $updateCondition = [
            'obatalkes_id' => array_column($dataStokR, 'obatalkes_id'),
            'ruangan_id' => array_column($dataStokR, 'ruangan_id')
        ];

        ApotekComponent::updateMultiple('stokobatalkes_r', $qty_update, $updateCondition);
    }
}
