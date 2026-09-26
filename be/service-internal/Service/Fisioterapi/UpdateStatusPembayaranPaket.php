<?php

namespace Integrasi\Service\Fisioterapi;


use Yii;
use Exception;
use yii\helpers\ArrayHelper;
use Integrasi\Components\Repositories\LookUpTransaksiRepositories;
use Integrasi\Service\Fisioterapi\Models\Pendaftaran;
use Integrasi\Service\Fisioterapi\Models\ProgramTerapiRajal;
use Integrasi\Service\Fisioterapi\Models\TindakanPelayanan;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\BatchUpdate;

class UpdateStatusPembayaranPaket extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $lookUpTransaksi = new LookUpTransaksiRepositories;
        $instalasiFisio = $lookUpTransaksi->getInstalasiIdFisio();
        $pendaftaranId = $this->pendaftaran_id;
        $pasienmasukpenunjangId = $this->pasienmasukpenunjang_id;
        // Validate Instalasi
        if(empty($instalasiFisio)){
            throw new Exception("Tidak ada instalasi fisioterapi");
        }
        // Validate Payload
        if (empty($pendaftaranId) || empty($pasienmasukpenunjangId)) {
            throw new Exception("Payload Tidak Sesuai");
        }
        // Validate Tindakan Pelayanan
        $tindakanPelayanan = TindakanPelayanan::find()->
        where(['pendaftaran_id' => $pendaftaranId])
        ->andWhere(['pasienmasukpenunjang_id' => $pasienmasukpenunjangId])
        ->one();
        
        if (empty($tindakanPelayanan)) {
            throw new Exception("Pasien Tidak Mempunyai Tagihan");
        }
        
        // Instalasi From Pendaftaran
        $tipeInstalasi = $tindakanPelayanan->instalasi_id;
        // Validate Instalasi Rawat Jalan
        if ($tipeInstalasi == $instalasiFisio) {
            $programterapiId = $tindakanPelayanan->programterapi_id;

            if(is_null($programterapiId)){
                throw new Exception("Billing Tidak Memiliki programterapi_id");
            }

            $childs = ProgramTerapiRajal::find()
            ->where(['programterapi_id' => $programterapiId])
            ->asArray()
            ->all();

            if(count($childs) < 1){
                throw new Exception("Pasien Tidak Memiliki Pendaftaran Penunjang");
            }else if(count($childs) > 0){
               return $this->updateStatusPaketFisioRajal($childs, $tindakanPelayanan);
            }
           
        }else {
            throw new Exception("Hanya Untuk Instalasi Fisio");
        }
    }

    /**
     * @method updateStatusPaketFisioRajal
     * @param Integer $childs
     * @param Integer $tindakanPelayanan
     */
    private function updateStatusPaketFisioRajal($childs, $tindakanPelayanan)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaranTempIds = [];
                foreach ($childs as $key => $value) {
                    $pendaftaranTempIds[] = ArrayHelper::getValue($value, 'pendaftaran_id');
                }
                $pendaftarans = Pendaftaran::find()
                ->where(['in', 'pendaftaran_id', $pendaftaranTempIds])
                ->asArray()
                ->all();
                $pendaftaransFixed = [];
                foreach ($pendaftarans as $k => $v){
                    $statusBayar = ArrayHelper::getValue($v, 'status_bayar');
                    if($statusBayar == DocoConstants::BELUM_LUNAS){
                        $pendaftaransFixed[] = ArrayHelper::getValue($v,'pendaftaran_id');
                    }
                }
                $batchUpdate = (new BatchUpdate(Pendaftaran::tableName(), function($query) use ($pendaftaransFixed, $tindakanPelayanan) {
                    $listKey = [];
                    $statusBayar = DocoConstants::BELUM_LUNAS;
                    foreach ($pendaftaransFixed as $ky => $val) {
                        $tindakanSudahBayarId = ArrayHelper::getValue($tindakanPelayanan, 'tindakansudahbayar_id');
                        if(!is_null($tindakanSudahBayarId)){
                            $statusBayar = DocoConstants::LUNAS;
                            $query->set([
                                'status_bayar' => $statusBayar,
                                'carabayar_id' => ArrayHelper::getValue($tindakanPelayanan, 'carabayar_id'),
                                'penjamin_id' =>  ArrayHelper::getValue($tindakanPelayanan, 'penjamin_id'),
                            ], "pendaftaran_id = {$val}",$ky);
                        }
                       
                        array_push($listKey, $val);
                        if(count($listKey) > 0){
                            $id = implode(", ", $listKey);
                            $query->where('pendaftaran_id in('.$id.')');
                        }
                }
                }))->execute();
                $transaction->commit();

                return json_encode([
                    'service' => 'Fisioterapi-UpdateStatusProgramClose',
                    'payload' => $this->attributes,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'response' => 'Status Pembayaran Berhasil di Update'
                ]);
           
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
