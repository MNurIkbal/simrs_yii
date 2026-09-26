<?php

namespace app\modules\v1\actions\PaketFisio;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use yii\db\Exception as DBException;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\DaftarPaketFisio;
use app\modules\v1\models\PemeriksaanFisio;
use app\modules\v1\models\JenisPemeriksaanFisio;
use app\modules\v1\models\DaftarPaketFisioDetail;
use Doco\Repositories\LookUpTransaksiRepositories;
use app\modules\v1\Exceptions\PaketFisio\BaseCurrentException;

class DeleteDataAction extends BaseCurrentAction
{
    private function deleteDaftarPaketFisio($daftarPaketFisioId)
    {
        $daftarPaketFisio = DaftarPaketFisio::find()
            ->where(['daftarpaketfisio_id' => $daftarPaketFisioId])
            ->one();
        if (!$daftarPaketFisio) throw new BaseCurrentException("Data tidak ditemukan (DaftarPaketFisio)", 1);
        $isDeleted = $daftarPaketFisio->delete();
        if (!$isDeleted) throw new BaseCurrentException("Gagal delete data (DaftarPaketFisio)", 1);
        return $daftarPaketFisio;
    }

    private function deleteParentDaftarTindakan($parentDaftarTindakanId)
    {
        $daftarTindakanParent = DaftarTindakan::find()
            ->where(['daftartindakan_id' => $parentDaftarTindakanId])
            ->one();
        if (!$daftarTindakanParent) throw new BaseCurrentException("Data tidak ditemukan (DaftarTindakan)", 1);
        $isDeleted = $daftarTindakanParent->delete();
        if (!$isDeleted) throw new BaseCurrentException("Gagal delete data (DaftarTindakan)", 1);
        return $daftarTindakanParent;
    }

    private function deletePemeriksaanFisioParent($daftarTindakanParentId)
    {
        $pemeriksaanFisio = PemeriksaanFisio::find()
            ->where(['daftartindakan_id' => $daftarTindakanParentId])
            ->one();
        if (!$pemeriksaanFisio) throw new BaseCurrentException("Data tidak ditemukan (PemeriksaanFisio)", 1);
        $isDeleted = $pemeriksaanFisio->delete();
        if (!$isDeleted) throw new BaseCurrentException("Gagal delete data (PemeriksaanFisio)", 1);
        return $pemeriksaanFisio;
    }

    private function deleteJenisPemeriksaanFisio($jenisPemeriksaanFisioId)
    {
        $jenisPemeriksaanFisio = JenisPemeriksaanFisio::find()
            ->where(['jenispemeriksaanfisio_id' => $jenisPemeriksaanFisioId])
            ->one();
        if (!$jenisPemeriksaanFisio) throw new BaseCurrentException("Data tidak ditemukan (JenisPemeriksaanFisio)", 1);
        $isDeleted = $jenisPemeriksaanFisio->delete();
        if (!$isDeleted) throw new BaseCurrentException("Gagal delete data (JenisPemeriksaanFisio)", 1);
        return $jenisPemeriksaanFisio;
    }

    private function deleteDaftarPaketFisioDetail($daftarPaketFisioId)
    {
        $daftarPaketFisioDetails = DaftarPaketFisioDetail::find()
            ->where(['daftarpaketfisio_id' => $daftarPaketFisioId])
            ->all();
        if (!$daftarPaketFisioDetails) throw new BaseCurrentException("Data tidak ditemukan (DaftarPaketFisioDetail)", 1);
        foreach ($daftarPaketFisioDetails as $key => $value) {
            $daftarPaketFisioDetailId = ArrayHelper::getValue($value, 'daftarpaketfisiodet_id');
            $daftarPaketFisioDetail = DaftarPaketFisioDetail::find()
                ->where(['daftarpaketfisiodet_id' => $daftarPaketFisioDetailId])
                ->one();
            $daftarPaketFisioDetail->delete();
        }
        return $daftarPaketFisioDetail;
    }

    private function executeSave($daftarPaketFisioId)
    {
        $daftarPaketFisio = $this->deleteDaftarPaketFisio($daftarPaketFisioId);
        $parentDaftarTindakanId = ArrayHelper::getValue($daftarPaketFisio, 'parent_id');
        $parentDaftarTindakan = $this->deleteParentDaftarTindakan($parentDaftarTindakanId);
        $parentPemeriksaanFisio = $this->deletePemeriksaanFisioParent($parentDaftarTindakanId);
        $jenisPemeriksaanFisioId = ArrayHelper::getValue($parentPemeriksaanFisio, 'jenispemeriksaanfisio_id');
        $jenisPemeriksaanFisio = $this->deleteJenisPemeriksaanFisio($jenisPemeriksaanFisioId);
        $daftarPaketFisioDetail = $this->deleteDaftarPaketFisioDetail($daftarPaketFisioId);
        return true;
    }

    public function run()
    {
        $request = Yii::$app->request;
        $helpers = new DocoHelpers;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $daftarPaketFisioId = $request->post('daftarpaketfisio_id');
            $result = $this->executeSave($daftarPaketFisioId);
            $transaction->commit();
            return $this->customResponseTindakan('Hapus Paket Berhasil');
        } catch (BaseCurrentException $e) {
            $helpers->logError($e);
            $transaction->rollBack();
            return $this->customResponseTindakan($e->getMessage(), 422);
        } catch (DBException $e) {
            $helpers->logError($e);
            $transaction->rollBack();
            return $this->customResponseTindakan($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            $transaction->rollBack();
        }
    }
}
