<?php
namespace app\modules\v1\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPermintaanBmhpView;
use app\modules\v1\models\InfoPermintaanBmhpDetailView;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\KonfigFarmasi;
use SirsCore\businessLogic\StokObatAlkes as BLStokObatAlkes;
use Doco\models\Pendaftaran;
use yii\helpers\ArrayHelper;


class ApproveBmhpAction extends Action
{
    public function run($pendaftaran_id, $ruangan_id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $_POST['ruangan_id'] = $ruangan_id;
            if(!empty($pendaftaran_id)) {
                $dataPendaftaran = Pendaftaran::findOne($pendaftaran_id);
                $isCloseBill = ArrayHelper::getValue($dataPendaftaran, 'is_close_bill', false);
                if($isCloseBill) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'message' => "Pasien sudah dilakukan proses Lock Bill."
                    ];
                }
            }

            $bmhp_detail = InfoPermintaanBmhpDetailView::find()
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'status_bmhp' => DocoConstants::BMHP_BELUM_VERIFIKASI,
                    'ruangan_tujuan' => $ruangan_id
                ])
                ->asArray()->all();

            foreach ($bmhp_detail as $bmhp) {
                $to_stok_obat_alkes[] = [
                    'obatalkes_id' => $bmhp['obatalkes_id'],
                    'qty_satuanpakai' => $bmhp['qty_obat'],
                    'obatalkespasien_id' => $bmhp['obatalkespasien_id'],
                    'satuankecil_id' => $bmhp['satuankecil_id'],
                ];

                $to_obat_alkes_pasien[] = $bmhp['obatalkespasien_id'];
            }

            if (count($to_obat_alkes_pasien)) {
                $execute_stock = $this->executeStock($to_stok_obat_alkes);
                if ($execute_stock !== true) {
                    throw new \Exception("Transaksi Pengurangan Stok, Gagal!", 1);
                }
            }

            // Update Status
            $obat_pasien = ObatAlkesPasien::updateAll(
                    ['status_bmhp' => DocoConstants::BMHP_SUDAH_VERIFIKASI],
                    ['obatalkespasien_id' => $to_obat_alkes_pasien]
                );

            $transaction->commit();

            return ['message' => 'Transaksi Berhasil'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function getKonfigFarmasi()
    {
        $today =  date('Y-m-d');
        return KonfigFarmasi::find()->where(['and', "tglberlaku >= '$today'"])->one();
    }

    public function getInventoryManagement()
    {
        $konfig = $this->getKonfigFarmasi();

        $currentMethode = BLStokObatAlkes::FEFO;
        if ($konfig) {
            $currentMethode = isset($konfig['metodeantrian']) ?
                strtoupper($konfig['metodeantrian']) :
                BLStokObatAlkes::FEFO;
        }

        return $currentMethode;
    }

    public function executeStock($data, $date = null)
    {
        $date = is_null($date) ? date('Y-m-d H:i:s') : $date;
        $methode = $this->getInventoryManagement();
        $result = false;
        if ($methode === BLStokObatAlkes::FEFO) {
            $result = BLStokObatAlkes::methodeFEFO($data,$date);
        } elseif ($methode === BLStokObatAlkes::FIFO) {
            $result = BLStokObatAlkes::methodeFEFO($data,$date);
        } else {
            $result = false;
        }
        return $result;
    }
}
