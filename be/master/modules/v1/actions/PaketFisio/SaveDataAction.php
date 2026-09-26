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
use app\modules\v1\Exceptions\PaketFisio\UniqueFrekuensiException;
use app\modules\v1\Exceptions\PaketFisio\UniqueTindakanException;
use app\modules\v1\Exceptions\PaketFisio\UniqueJumlahPilihanException;
use app\modules\v1\Exceptions\PaketFisio\EmptyListTindakanException;
use app\modules\v1\Exceptions\PaketFisio\UniquePemeriksaanFisioException;
use app\modules\v1\Exceptions\PaketFisio\UniqueJenisPemeriksaanFisioException;

class SaveDataAction extends BaseCurrentAction
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
        $pureValue['kelompoktindakan_id'] = $this->getKelompokTindakanFisio();
        $pureValue['kategoritindakan_id'] = $this->getKategoriTindakanFisio();
        $pureValue['kelompokpemeriksaanfisio_id'] = ArrayHelper::getValue($pureValue, 'kelompokpemeriksaanfisio_id');
        if(!$pureValue['kelompokpemeriksaanfisio_id']) {
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

    private function insertParentDaftarTindakan($value)
    {
        // Muatate Data (Daftar Tindakan, Parent)
        $kelompokTindakanFisioId = ArrayHelper::getValue($value, 'kelompoktindakan_id');
        $kategoriTindakanFisioId = ArrayHelper::getValue($value, 'kategoritindakan_id');
        $daftarTindakanKode = ArrayHelper::getValue($value, 'daftartindakan_kode');
        $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
        $daftarTindakanNamaLainya = ArrayHelper::getValue($value, 'daftartindakan_namalainya');
        $isActive = ArrayHelper::getValue($value, 'is_active');
        $daftarTindakanParent = new DaftarTindakan;
        $daftarTindakanParent->kelompoktindakan_id = $kelompokTindakanFisioId;
        $daftarTindakanParent->kategoritindakan_id = $kategoriTindakanFisioId;
        $daftarTindakanParent->daftartindakan_kode = $daftarTindakanKode;
        $daftarTindakanParent->daftartindakan_nama = $daftarTindakanNama;
        $daftarTindakanParent->tindakanmedis_nama = $daftarTindakanNama;
        $daftarTindakanParent->daftartindakan_namalainnya = $daftarTindakanNamaLainya;
        $daftarTindakanParent->is_active = $isActive;
        $daftarTindakanParent->is_paketfisio = true;
        $isSavedDaftarTindakanParent = $daftarTindakanParent->save();
        $parentId = $daftarTindakanParent->daftartindakan_id;
        if (!$isSavedDaftarTindakanParent) {
            throw new UniqueTindakanException();
        }
        return $daftarTindakanParent;
    }

    private function insertDaftarPaketFisio($value)
    {
        $parentDaftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
        $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
        $frekuensi = ArrayHelper::getValue($value, 'frekuensi');
        $jumlah = ArrayHelper::getValue($value, 'jumlah');
        $catatan = ArrayHelper::getValue($value, 'catatan');
        $isActive = ArrayHelper::getValue($value, 'is_active');
        $daftarPaketFisio = new DaftarPaketFisio;
        $daftarPaketFisio->parent_id = $parentDaftarTindakanId;
        $daftarPaketFisio->daftarpaketfisio_nama = $daftarTindakanNama;
        $daftarPaketFisio->frekuensi = $frekuensi;
        $daftarPaketFisio->jumlah = $jumlah;
        $daftarPaketFisio->catatan = $catatan;
        $daftarPaketFisio->is_active = $isActive;
        $isSavedDaftarPaketFisio  = $daftarPaketFisio->save();
        if (!$isSavedDaftarPaketFisio) {
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

    private function insertDaftarPaketFisioDetail($value)
    {
        $valueDaftarPaketFisioDetail = [];
        $daftarPaketFisioParentId = ArrayHelper::getValue($value, 'daftarpaketfisio_id');
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
        $insertedNum = DaftarPaketFisioDetail::batchInsert($valueDaftarPaketFisioDetail);
        return $insertedNum;
    }

    private function insertJenisPemeriksaanFisio($valueJenisPemeriksaanFisio)
    {
        $jenisPemeriksaanFisio = new JenisPemeriksaanFisio();
        $jenisPemeriksaanFisio->jenispemeriksaanfisio_kode = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'daftartindakan_kode');
        $jenisPemeriksaanFisio->jenispemeriksaanfisio_nama = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'daftartindakan_nama');
        $jenisPemeriksaanFisio->jenispemeriksaanfisio_namalain = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'daftartindakan_namalainya');
        $jenisPemeriksaanFisio->kelompokpemeriksaanfisio_id = ArrayHelper::getValue($valueJenisPemeriksaanFisio, 'kelompokpemeriksaanfisio_id');
        $isSaved = $jenisPemeriksaanFisio->save();
        if (!$isSaved) {
            throw new UniqueJenisPemeriksaanFisioException();
        }
        return $jenisPemeriksaanFisio;
    }

    private function insertPemeriksaanFisioParent($valuePemeriksaanFisio)
    {
        $pemeriksaanFisio = new PemeriksaanFisio();
        $pemeriksaanFisio->daftartindakan_id = ArrayHelper::getValue($valuePemeriksaanFisio, 'daftartindakan_id');
        $pemeriksaanFisio->jenispemeriksaanfisio_id = ArrayHelper::getValue($valuePemeriksaanFisio, 'jenispemeriksaanfisio_id');
        $pemeriksaanFisio->pemeriksaanfisio_kode = ArrayHelper::getValue($valuePemeriksaanFisio, 'daftartindakan_kode');
        $pemeriksaanFisio->pemeriksaanfisio_nama = ArrayHelper::getValue($valuePemeriksaanFisio, 'daftartindakan_nama');
        $pemeriksaanFisio->kelompokpemeriksaanfisio_id = ArrayHelper::getValue($valuePemeriksaanFisio, 'kelompokpemeriksaanfisio_id');
        $pemeriksaanFisio->tipepaket_id = ArrayHelper::getValue($valuePemeriksaanFisio, 'tipepaket_id');
        $isSaved = $pemeriksaanFisio->save();
        if(!$isSaved) {
            throw new UniquePemeriksaanFisioException();
        }
        return $pemeriksaanFisio;
    }

    private function insertPemeriksaanFisioDetail($valuePemeriksaanFisio)
    {
        $valueDaftarPaketFisioDetail = [];
        $listTindakan = ArrayHelper::getValue($valuePemeriksaanFisio, 'list_tindakan');
        foreach ($listTindakan as $key => $value) {
            $daftarTindakanChildId = ArrayHelper::getValue($value, 'id');
            if (!$daftarTindakanChildId) continue;
            $tempValue = [
                'jenispemeriksaanfisio_id' => ArrayHelper::getValue($valuePemeriksaanFisio, 'jenispemeriksaanfisio_id'),
                'kelompokpemeriksaanfisio_id' => ArrayHelper::getValue($valuePemeriksaanFisio, 'kelompokpemeriksaanfisio_id'),
                'daftartindakan_id' => $daftarTindakanChildId,
                'pemeriksaanfisio_kode' => ArrayHelper::getValue($value, 'daftartindakan_kode'),
                'pemeriksaanfisio_nama' => ArrayHelper::getValue($value, 'daftartindakan_nama'),
                'tipepaket_id' => ArrayHelper::getValue($value, 'tipepaket_id'),
            ];
            $valueDaftarPaketFisioDetail[] = $tempValue;
        }
        $insertedNum = PemeriksaanFisio::batchInsert($valueDaftarPaketFisioDetail);
        if(!$insertedNum) {
            throw new UniquePemeriksaanFisioException();
        }
    }

    private function executeSave()
    {
        // Get Data Post
        $pureValue = $this->getPureValuePost();
        // Insert Parent Daftar Tindakan
        $daftarTindakanParent = $this->insertParentDaftarTindakan($pureValue);
        // Insert Daftar Paket Fisio
        $daftarPaketFisioValue['daftartindakan_id'] = $daftarTindakanParent->daftartindakan_id;
        $daftarPaketFisioValue = array_merge($pureValue, $daftarPaketFisioValue);
        $daftarPaketFisio = $this->insertDaftarPaketFisio($daftarPaketFisioValue);
        // Insert Daftar Paket Fisio Detail
        $daftarPaketFisioDetailValue['daftarpaketfisio_id'] = $daftarPaketFisio->daftarpaketfisio_id;
        $daftarPaketFisioDetailValue = array_merge($pureValue, $daftarPaketFisioDetailValue);
        $insertedDetailNum = $this->insertDaftarPaketFisioDetail($daftarPaketFisioDetailValue);
        // Insert Jenis Pemeriksaan Fisio
        $jenisPemeriksaanFisioValue = [];
        $jenisPemeriksaanFisioValue = array_merge($pureValue, $jenisPemeriksaanFisioValue);
        $jenisPemeriksaanFisio = $this->insertJenisPemeriksaanFisio($jenisPemeriksaanFisioValue);
        // Insert Pemeriksaan Fisio
        $pemeriksaanFisioValue['daftartindakan_id'] = $daftarTindakanParent->daftartindakan_id;
        $pemeriksaanFisioValue['jenispemeriksaanfisio_id'] = $jenisPemeriksaanFisio->jenispemeriksaanfisio_id;
        $pemeriksaanFisioValue = array_merge($pureValue, $pemeriksaanFisioValue);
        $pemeriksaanFisio = $this->insertPemeriksaanFisioParent($pemeriksaanFisioValue);
        
        // Tidak di-insertkan : Has duplicate key in daftartindakan_id
        // Insert Pemeriksaan Fisio Detail (Child)
        // $pemeriksaanFisioDetailValue['daftartindakan_id'] = $daftarTindakanParent->daftartindakan_id;
        // $pemeriksaanFisioDetailValue['jenispemeriksaanfisio_id'] = $jenisPemeriksaanFisio->jenispemeriksaanfisio_id;
        // $pemeriksaanFisioDetailValue = array_merge($pureValue, $pemeriksaanFisioDetailValue);
        // $pemeriksaanFisioDetail = $this->insertPemeriksaanFisioDetail($pemeriksaanFisioDetailValue);
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
            $result = $this->executeSave();
            $transaction->commit();
            return $this->controller->responseJson(200, 'Simpan Paket Berhasil', $result);
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
