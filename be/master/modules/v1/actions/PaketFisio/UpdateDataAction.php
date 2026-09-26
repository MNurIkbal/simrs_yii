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
use app\modules\v1\Exceptions\PaketFisio\PayloadException;
use app\modules\v1\Exceptions\PaketFisio\BaseCurrentException;
use app\modules\v1\Exceptions\PaketFisio\UniqueTindakanException;
use app\modules\v1\Exceptions\PaketFisio\EmptyListTindakanException;
use app\modules\v1\Exceptions\PaketFisio\GuardedKodePaketFisioException;
use app\modules\v1\Exceptions\PaketFisio\GuardedNamaPaketFisioException;
use app\modules\v1\Exceptions\PaketFisio\UniquePemeriksaanFisioException;
use app\modules\v1\Exceptions\PaketFisio\UniqueJenisPemeriksaanFisioException;
use app\modules\v1\Exceptions\PaketFisio\UniqueJumlahPilihanException;
use app\modules\v1\Exceptions\PaketFisio\UniqueFrekuensiException;

class UpdateDataAction extends BaseCurrentAction
{
    private function getKategoriTindakanFisio()
    {
        $keyId = LookUpTransaksiRepositories::getTindakanKategoriFisio();
        return $keyId;
    }

    private function getKelompokTindakanFisio()
    {
        $keyId = LookUpTransaksiRepositories::getTindakanKelompokFisio();
        return $keyId;
    }

    private function getDefaultKelompokPemeriksaanFisio()
    {
        $keyId = LookUpTransaksiRepositories::getDefaultKelompokPemeriksaanFisio();
        return $keyId;
    }

    private function getPureValuePost()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $pureValue['daftartindakan_id'] = ArrayHelper::getValue($post, 'parent_id');
        $pureValue['kelompoktindakan_id'] = $this->getKelompokTindakanFisio();
        $pureValue['kategoritindakan_id'] = $this->getKategoriTindakanFisio();
        $pureValue['kelompokpemeriksaanfisio_id'] = ArrayHelper::getValue($pureValue, 'kelompokpemeriksaanfisio_id');
        if (!$pureValue['kelompokpemeriksaanfisio_id']) {
            $pureValue['kelompokpemeriksaanfisio_id'] = $this->getDefaultKelompokPemeriksaanFisio();
        }
        $pureValue['daftartindakan_kode'] = ArrayHelper::getValue($post, 'kode_paket');
        $pureValue['daftartindakan_nama'] = ArrayHelper::getValue($post, 'nama_paket');
        $pureValue['daftartindakan_namalainya'] = ArrayHelper::getValue($post, 'namalainya_paket', $pureValue['daftartindakan_nama']);
        if (!$pureValue['daftartindakan_namalainya']) $pureValue['daftartindakan_namalainya'] = $pureValue['daftartindakan_nama'];
        $isActive = ArrayHelper::getValue($post, 'is_active', false);
        $pureValue['is_active'] = ($isActive == 1) ? true : false;
        $pureValue['frekuensi'] = ArrayHelper::getValue($post, 'frekuensi');
        $pureValue['jumlah'] = ArrayHelper::getValue($post, 'jumlah');
        $pureValue['catatan'] = ArrayHelper::getValue($post, 'catatan');
        $pureValue['list_tindakan'] = ArrayHelper::getValue($post, 'list_tindakan', []);
        if (empty($pureValue['list_tindakan'])) {
            throw new EmptyListTindakanException();
        }
        return $pureValue;
    }

    private function updateParentDaftarTindakan($value)
    {
        $kelompokTindakanFisioId = ArrayHelper::getValue($value, 'kelompoktindakan_id');
        $kategoriTindakanFisioId = ArrayHelper::getValue($value, 'kategoritindakan_id');
        $daftarTindakanKode = ArrayHelper::getValue($value, 'daftartindakan_kode');
        $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
        $daftarTindakanNamaLainya = ArrayHelper::getValue($value, 'daftartindakan_namalainya');
        $isActive = ArrayHelper::getValue($value, 'is_active');
        $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
        $daftarTindakanParent = DaftarTindakan::find()->where(['daftartindakan_id' => $daftarTindakanId])->one();
        if ($daftarTindakanKode != $daftarTindakanParent->daftartindakan_kode) {
            throw new GuardedKodePaketFisioException();
        }
        if ($daftarTindakanNama != $daftarTindakanParent->daftartindakan_nama) {
            throw new GuardedNamaPaketFisioException();
        }
        $daftarTindakanParent->kelompoktindakan_id = $kelompokTindakanFisioId;
        $daftarTindakanParent->kategoritindakan_id = $kategoriTindakanFisioId;
        $daftarTindakanParent->daftartindakan_kode = $daftarTindakanKode;
        $daftarTindakanParent->daftartindakan_nama = $daftarTindakanNama;
        $daftarTindakanParent->tindakanmedis_nama = $daftarTindakanNama;
        $daftarTindakanParent->daftartindakan_namalainnya = $daftarTindakanNamaLainya;
        $daftarTindakanParent->is_active = $isActive;
        $daftarTindakanParent->is_paketfisio = true;
        $isUpdateDaftarTindakanParent = $daftarTindakanParent->update();
        if (!$isUpdateDaftarTindakanParent) {
            Yii::error($daftarTindakanParent->errors);
            throw new UniqueTindakanException();
        }
        return $daftarTindakanParent;
    }

    private function updateDaftarPaketFisio($value)
    {
        $parentDaftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
        $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
        $frekuensi = ArrayHelper::getValue($value, 'frekuensi');
        $jumlah = ArrayHelper::getValue($value, 'jumlah');
        $catatan = ArrayHelper::getValue($value, 'catatan');
        $isActive = ArrayHelper::getValue($value, 'is_active');
        $daftarPaketFisio = DaftarPaketFisio::find()->where(['parent_id' => $parentDaftarTindakanId])->one();
        if ($daftarTindakanNama != $daftarPaketFisio->daftarpaketfisio_nama) {
            throw new GuardedNamaPaketFisioException();
        }
        $daftarPaketFisio->parent_id = $parentDaftarTindakanId;
        $daftarPaketFisio->daftarpaketfisio_nama = $daftarTindakanNama;
        $daftarPaketFisio->frekuensi = $frekuensi;
        $daftarPaketFisio->jumlah = $jumlah;
        $daftarPaketFisio->catatan = $catatan;
        $daftarPaketFisio->is_active = $isActive;
        $isUpdateDaftarPaketFisio = $daftarPaketFisio->update();
        if (!$isUpdateDaftarPaketFisio) {
            Yii::error($daftarPaketFisio->errors);
            if(isset($daftarPaketFisio->errors)){
                $frekuensiError = ArrayHelper::getValue($daftarPaketFisio->errors,'frekuensi');
                $jumlahError = ArrayHelper::getValue($daftarPaketFisio->errors,'jumlah');
                if($frekuensiError){
                    throw new UniqueFrekuensiException();
                }else if($jumlahError){
                    throw new UniqueJumlahPilihanException();
                }
            }else{
                throw new UniqueTindakanException();
            }
        }
        return $daftarPaketFisio;
    }

    private function updateDaftarPaketFisioDetail($value)
    {
        $valueDaftarPaketFisioDetail = [];
        $daftarPaketFisioParentId = ArrayHelper::getValue($value, 'daftarpaketfisio_id');
        if (empty($daftarPaketFisioParentId)) {
            throw new PayloadException();
        }
        $listTindakan = ArrayHelper::getValue($value, 'list_tindakan');
        foreach ($listTindakan as $key => $value) {
            $daftarTindakanChildId = ArrayHelper::getValue($value, 'id');
            if (!$daftarTindakanChildId) continue;
            $tempValue = [
                'daftarpaketfisio_id' => $daftarPaketFisioParentId,
                'daftartindakan_id' => $daftarTindakanChildId
            ];
            $valueDaftarPaketFisioDetail[] = $tempValue;
        }
        $model = DaftarPaketFisioDetail::find()
            ->where(['daftarpaketfisio_id' => $daftarPaketFisioParentId])
            ->all();
        foreach ($model as $value) {
            $modelDetail = DaftarPaketFisioDetail::find()
                ->where(['daftarpaketfisiodet_id' => $value->daftarpaketfisiodet_id])
                ->one();
            $modelDetail->delete();
        }
        $insertedNum = DaftarPaketFisioDetail::batchInsert($valueDaftarPaketFisioDetail);
        return $insertedNum;
    }

    private function updateJenisPemeriksaanFisio($valueJenisPemeriksaanFisio)
    {
        $daftarTindakanKode = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'daftartindakan_kode');
        $daftarTindakanNama = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'daftartindakan_nama');
        $daftarTindakanNamaLainnya = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'daftartindakan_namalainya');
        $kelompokPemeriksaanFisioId = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'kelompokpemeriksaanfisio_id');
        $jenisPemeriksaanFisioId = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'jenispemeriksaanfisio_id');
        $jenisPemeriksaanFisio = JenisPemeriksaanFisio::find()->where(['jenispemeriksaanfisio_id' => $jenisPemeriksaanFisioId])->one();        
        if ($daftarTindakanKode != $jenisPemeriksaanFisio->jenispemeriksaanfisio_kode) {
            throw new GuardedKodePaketFisioException();
        }
        if ($daftarTindakanNama != $jenisPemeriksaanFisio->jenispemeriksaanfisio_nama) {
            throw new GuardedNamaPaketFisioException();
        }
        $jenisPemeriksaanFisio->jenispemeriksaanfisio_kode = $daftarTindakanKode;
        $jenisPemeriksaanFisio->jenispemeriksaanfisio_nama = $daftarTindakanNama;
        $jenisPemeriksaanFisio->jenispemeriksaanfisio_namalain = $daftarTindakanNamaLainnya;
        $jenisPemeriksaanFisio->kelompokpemeriksaanfisio_id = $kelompokPemeriksaanFisioId;
        $isUpdated = $jenisPemeriksaanFisio->update();
        if (!$isUpdated) {
            Yii::error($jenisPemeriksaanFisio->errors);
            throw new UniqueJenisPemeriksaanFisioException();
        }
        return $jenisPemeriksaanFisio;
    }

    private function updatePemeriksaanFisioParent($valuePemeriksaanFisio)
    {
        $daftarTindakanId = ArrayHelper::getValue($valuePemeriksaanFisio, 'daftartindakan_id');
        $daftarTindakanKode = ArrayHelper::getValue($valuePemeriksaanFisio, 'daftartindakan_kode');
        $daftarTindakanNama = ArrayHelper::getValue($valuePemeriksaanFisio, 'daftartindakan_nama');
        $kelompokPemeriksaanFisioId = ArrayHelper::getValue($valuePemeriksaanFisio, 'kelompokpemeriksaanfisio_id');
        $tipePaketId = ArrayHelper::getValue($valuePemeriksaanFisio, 'tipepaket_id');
        $pemeriksaanFisio = PemeriksaanFisio::find()->where(['daftartindakan_id' => $daftarTindakanId])->one();        
        if ($daftarTindakanKode != $pemeriksaanFisio->pemeriksaanfisio_kode) {
            throw new GuardedKodePaketFisioException();
        }
        if ($daftarTindakanNama != $pemeriksaanFisio->pemeriksaanfisio_nama) {
            throw new GuardedNamaPaketFisioException();
        }
        $pemeriksaanFisio->daftartindakan_id = $daftarTindakanId;
        $pemeriksaanFisio->pemeriksaanfisio_kode = $daftarTindakanKode;
        $pemeriksaanFisio->pemeriksaanfisio_nama = $daftarTindakanNama;
        $pemeriksaanFisio->kelompokpemeriksaanfisio_id = $kelompokPemeriksaanFisioId;
        $pemeriksaanFisio->tipepaket_id = $tipePaketId;
        $isUpdated = $pemeriksaanFisio->update();
        if (!$isUpdated) {
            Yii::error($pemeriksaanFisio->errors);
            throw new UniquePemeriksaanFisioException();
        }
        return $pemeriksaanFisio;
    }

    private function executeUpdate()
    {
        $pureValue = $this->getPureValuePost();
        $this->updateParentDaftarTindakan($pureValue);
        $daftarPaketFisio = $this->updateDaftarPaketFisio($pureValue);
        $daftarPaketFisioDetail = [];
        $daftarPaketFisioDetail['daftarpaketfisio_id'] = $daftarPaketFisio->daftarpaketfisio_id;
        $payloadUpdateFisioDetail = array_merge($pureValue, $daftarPaketFisioDetail);
        $this->updateDaftarPaketFisioDetail($payloadUpdateFisioDetail);
        $pemeriksaanFisioterapi = $this->updatePemeriksaanFisioParent($pureValue);
        $jenisPemeriksaanFisioterapi = [];
        $jenisPemeriksaanFisioterapi['jenispemeriksaanfisio_id'] = $pemeriksaanFisioterapi->jenispemeriksaanfisio_id;
        $payloadUpdateJenisPemeriksaanFisio = array_merge($pureValue, $jenisPemeriksaanFisioterapi);
        $this->updateJenisPemeriksaanFisio($payloadUpdateJenisPemeriksaanFisio);
        return true;
    }

    public function run()
    {
        $helpers = new DocoHelpers;
        $request = Yii::$app->request;
        $post = $request->post();
        $jumlahPilihan = ArrayHelper::getValue($post, 'jumlah');
        $listTindakan = ArrayHelper::getValue($post, 'list_tindakan', []);
        $daftartindakan_kode = ArrayHelper::getValue($post, 'kode_paket');
        if($jumlahPilihan && $listTindakan){
            if(count($listTindakan) < (int)$jumlahPilihan){
                return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => 'Jumlah Tindakan Harus Lebih Dari / Sama Dengan Jumlah Pilihan.'
                ]);
            };
        }
        if($daftartindakan_kode && strlen($daftartindakan_kode) > 10){
            return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => 'Maksimal Panjang Kode Paket Adalah 10 Karakter.'
            ]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = $this->executeUpdate();
            $transaction->commit();
            return $this->controller->responseJson(200, 'Paket Berhasil Diperbarui', $result);
        } catch (BaseCurrentException $e) {
            $helpers->logError($e);
            $transaction->rollBack();
            return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => $e->getMessage()
            ]);
        } catch (DBException $e) {
            $helpers->logError($e);
            $transaction->rollBack();
            return $helpers->response($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            $transaction->rollBack();
            return $helpers->response($e->getMessage(), 500);
        }
    }
}
