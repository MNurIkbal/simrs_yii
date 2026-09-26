<?php

namespace Extensions\kasir;

use Yii;
use app\modules\v1\businessLogic\TagihanHelper;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\DaftarTindakan;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\models\kasir\MasterTarifTindakanView;
use Doco\processes\PathDetailRincianProcess;

class DetailRincianAdhy extends PathDetailRincianProcess
{
    protected $dokPath = 'detail-rincian-adhy';

    protected function processFlow()
    {
        return parent::processFlow();
    }

    private function getDataAdmin()
    {
        $confSistem = Cache::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $model = new DaftarTindakan;
        $query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();
        return isset($query['daftartindakan_nama']) ? $query['daftartindakan_nama'] : 'Administration Fee';
    }

    protected function getData()
    {
        $request = $this->_requestData;
        $id = $request->get('id', null);
        $this->id = $id;
        $infoPasien = $this->getDataPendaftaranRincian($id);
        $penjaminId = !empty($infoPasien['penjamin_id']) ? $infoPasien['penjamin_id'] : null;
        $admisiId = !empty($infoPasien['pasienadmisi_id']) ? $infoPasien['pasienadmisi_id'] : null;
        $kelasPelayananId = !empty($infoPasien['kelaspelayanan_id']) ? $infoPasien['kelaspelayanan_id'] : null;
        $kelasDitagihkanId = !empty($infoPasien['kelas_ditagihkan_id']) ? $infoPasien['kelas_ditagihkan_id'] : null;
        $akomodasi = [
            'tindakan_akomodasi' => null,
            'total_akomodasi' => null,
            'kelompok_tindakan' => null
        ];
        if(!empty($admisiId)){
            $akomodasi = $this->getAkomodasi($admisiId);
        }
        $this->infoPasien = $infoPasien;
        $historyPindahKamar = $this->getHistoryPindahKamar($id);
        if(!empty($kelasDitagihkanId)) {
            $kelasPelayananId = $kelasDitagihkanId;
        }

        if(!empty($historyPindahKamar)) {
            $kelasPelayananId = isset($historyPindahKamar['kelaspelayanan_id']) ? $historyPindahKamar['kelaspelayanan_id'] : '';
            $kelasDitagihkanId = isset($historyPindahKamar['kelas_ditagihkan_id']) ? $historyPindahKamar['kelas_ditagihkan_id'] : '';
            $penjaminId = isset($historyPindahKamar['penjamin_id']) ? $historyPindahKamar['penjamin_id'] : '';
            if(!empty($kelasDitagihkanId)) {
                $kelasPelayananId = $kelasDitagihkanId;
            }
        }

        $total_jpk = 0;
        $jpk_id = json_decode((new DocoConstansId)->actionGetAdditional("JPK"));
        $tindakan = (new DocoConstansId)->actionGetAdditional('tindakan_keperawatan');
        $tindakan = json_decode($tindakan, true);
        $this->tindakan = $tindakan;
        $qDetail = $this->getDataDetailTagihan($id);
        $detail = $data_admin = [];
        $subTotal = 0;
        foreach($qDetail as $value){
            $total = isset($value['sub_total']) ? $value['sub_total'] : 0;
            $subTotal += $total;
            $pelayanan = !empty($value['pelayanan']) ? $value['pelayanan'] : null;
            $ruangan = !empty($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : null;
            $tindakan_obat_id = isset($value['tindakan_obat_id']) ? $value['tindakan_obat_id'] : null;
            $kelompokTindakanNama = !empty($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : null;
            $isObat = isset($value['is_obat']) ? $value['is_obat'] : false;
            $value['detail_tindakan'] = null;
            if ($value['instalasi_id'] == DocoConstants::INST_ID_MCU && !$isObat) {
                $value['detail_tindakan'] = $this->getTarifDataView($value['tindakan_obat_id'], $value['carabayar_pelayanan_id']);
            }
            if(!empty($pelayanan)) {
                $detail[$pelayanan][$ruangan][$kelompokTindakanNama][] = $value;
            }
            if(!empty($tindakan_obat_id) && in_array($tindakan_obat_id, $jpk_id)){
                $total_jpk += $total;
            }
        }

        $this->detail = $detail;
        $total_tagihan_admin = $subTotal - $total_jpk;
        $biayaAdmin = TagihanHelper::getBiayaAdmin($id, $penjaminId, $kelasPelayananId, $total_tagihan_admin, $admisiId);
        $data_admin = '';
        if( $biayaAdmin > 0 &&  !empty($admisiId)){
            $data_admin = $this->getDataAdmin();
        }
        $this->biayaAdmin = $biayaAdmin;
        $this->data_admin = $data_admin;
        $this->akomodasi = $akomodasi;
    }

    protected function masterTarifTindakanData()
    {
        $model = new MasterTarifTindakanView;
        return $model;
    }

    protected function getTarifDataView($paketId, $carabayarId = null)
    {
        $model = $this->masterTarifTindakanData();
        $query = $model::find();
        $query->where(['tindakan_paket_id' => $paketId]);
        $query->andWhere(['!=', 'komponentarif_id', DocoConstants::KOMPONEN_TARIF]);
        if (!empty($carabayarId)) {
            $query->andWhere(['carabayar_id' => $carabayarId]);
        }
        $getDetail = $query->all();

        if (!empty($carabayarId) && empty($getDetail)) {
            $query = $model::find();
            $query->where(['tindakan_paket_id' => $paketId]);
            $query->andWhere(['!=', 'komponentarif_id', DocoConstants::KOMPONEN_TARIF]);
            $getDetail = $query->all();
        }

        $arrResult = [];
        if (isset($getDetail)) {
            $arrData = [];
            foreach ($getDetail as $key => $value) {
                $arrData[$value['daftartindakan_id']][] = [
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'daftartindakan_nama' => $value['daftartindakan_nama'],
                        'harga_tariftindakan' => $value['harga_tariftindakan']
                    ];
            }
            
            foreach ($arrData as $k => $val) {
                $totalHargaTarifTindakan = 0;
                foreach ($val as $x => $v) {
                    $totalHargaTarifTindakan += $v['harga_tariftindakan'];
                }
                $arrResult[$k] = [
                    'daftartindakan_id' => $v['daftartindakan_id'],
                    'daftartindakan_nama' => $v['daftartindakan_nama'],
                    'harga_tariftindakan' => $totalHargaTarifTindakan
                ];
            }
        }
        return $arrResult;
    }
}