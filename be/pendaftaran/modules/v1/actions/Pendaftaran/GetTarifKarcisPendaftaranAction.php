<?php

namespace app\modules\v1\actions\Pendaftaran;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Sort;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\controllers\AllowController;
use Doco\components\DocoMessages;

use app\modules\v1\models\DokterV;
use app\modules\v1\models\PegawaiSubSpesialis;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\TarifTotalRsFn;
use app\modules\v1\models\TindakanSpesialis;
use app\modules\v1\payload\TarifPayload;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use SirsCore\features\IntegrasiAkunting;

class GetTarifKarcisPendaftaranAction extends BaseCurrentAction
{
    private function reAssignIsDefaultDokterSpesialis($datas, $payload)
    {
        $newDatas = $datas;
        $pegawaiId = ArrayHelper::getValue($payload, 'dokter_id');
        if (!$pegawaiId) return $datas;
        $daftarTindakanSpesialisDokterIds = (new DocoConstansId)->actionGetAdditional('daftartindakan_spesialis_ids', true);
        $daftarTindakanSubSpesialisDokterIds = (new DocoConstansId)->actionGetAdditional('daftartindakan_subspesialis_ids', true);
        $dafterTindakanUmumIds = (new DocoConstansId)->actionGetAdditional('daftartindakan_umum_ids', true);

        // Get Pegawai (Umum / Spesialis / Sub-Spesialis)
        $pegawai = Pegawai::find()->where(['pegawai_id' => $pegawaiId])->asArray()->one();
        $fieldPegawaiSpesialis = ArrayHelper::getValue($pegawai, 'spesialis_id');
        $isExistDokterSpesialis = false;
        if ($fieldPegawaiSpesialis) $isExistDokterSpesialis = true;
        $pegawaiSubSpesialis = PegawaiSubSpesialis::find()->where(['pegawai_id' => $pegawaiId])->asArray()->all();
        $isExistDokterSubSpesialis = sizeof($pegawaiSubSpesialis) > 0;

        // Get Ruangan and Instalasi Inpatient
        $ruangan = Ruangan::find()
            ->select(['instalasi_id'])
            ->where(['ruangan_id' => $payload->ruangan_id])->asArray()->one();
        $instalasi_id = ArrayHelper::getValue($ruangan, 'instalasi_id');
        $instalasiIdRanap = (new DocoConstansId)->actionGetId('RI');

        // Define List Flagging Tipe
        $isSpesialis = false;
        $isSubSpesialis = false;
        $isUmum = false;
        $isSpesialis = $isExistDokterSpesialis;
        if ($isExistDokterSubSpesialis) {
            $isSpesialis = false;
            $isSubSpesialis = true;
        }
        $isUmum = !$isSpesialis && !$isSubSpesialis;

        // Reset To Unchecked if same in old config (by ruangan)
        foreach ($datas as $k => $v) {
            $currentDaftarTindakan = ArrayHelper::getValue($v, 'daftartindakan_id');
            $isSameForSpesialisUncheck = in_array($currentDaftarTindakan, $daftarTindakanSpesialisDokterIds);
            $isSameForSubSpesialisUncheck = in_array($currentDaftarTindakan, $daftarTindakanSubSpesialisDokterIds);
            $isSameForUmumUncheck = in_array($currentDaftarTindakan, $dafterTindakanUmumIds);
            if ($isSameForSpesialisUncheck) {
                $newDatas[$k]['is_default'] = false;
            }
            if ($isSameForSubSpesialisUncheck) {
                $newDatas[$k]['is_default'] = false;
            }
            if ($isSameForUmumUncheck) {
                $newDatas[$k]['is_default'] = false;
            }
        }

        // Exclude doctor specialis condition for Inpatient Registration
        if ($instalasi_id != $instalasiIdRanap) {
            // Re-assign is_default
            foreach ($datas as $k => $v) {
                $currentDaftarTindakan = ArrayHelper::getValue($v, 'daftartindakan_id');
                $isSameForSpesialisCheck = in_array($currentDaftarTindakan, $daftarTindakanSpesialisDokterIds) && $isSpesialis;
                $isSameForSubSpesialisCheck = in_array($currentDaftarTindakan, $daftarTindakanSubSpesialisDokterIds) && $isSubSpesialis;
                $isSameForUmumCheck = in_array($currentDaftarTindakan, $dafterTindakanUmumIds) && $isUmum;
                if ($isSameForSpesialisCheck) {
                    $newDatas[$k]['is_default'] = true;
                }
                if ($isSameForSubSpesialisCheck) {
                    $newDatas[$k]['is_default'] = true;
                }
                if ($isSameForUmumCheck) {
                    $newDatas[$k]['is_default'] = true;
                }
            }
        }
        return $newDatas;
    }

    private function reSortingByIsDefault($datas, $payload)
    {
        $newDatas = $tmpDef = $tmpNotDef = [];
        foreach ($datas as $k => $v) {
            if (!empty($v['is_default'])) {
                if ($v['dokter_id'] == $payload->dokter_id) {
                    $tmpDef[$v['daftartindakan_id']] = $v;
                } else {
                    if (!isset($tmpDef[$v['daftartindakan_id']])) {
                        $tmpDef[$v['daftartindakan_id']] = $v;
                    }
                }
            } else {
                if ($v['dokter_id'] == $payload->dokter_id) {
                    $tmpNotDef[$v['daftartindakan_id']] = $v;
                } else {
                    if (!isset($tmpNotDef[$v['daftartindakan_id']])) {
                        $tmpNotDef[$v['daftartindakan_id']] = $v;
                    }
                }
            }
        }
        $newDatas = array_merge($tmpDef, $tmpNotDef);
        return $newDatas;
    }

    public function run()
    {
        $request = Yii::$app->request;
        $payload = new TarifPayload;
        $payload->attributes = $request->get();
        $param = ArrayHelper::getValue($request->get(), 'param');
        $type = 'pelayanan';
        $kelompokTindakanIds = (new DocoConstansId)->actionGetAdditional(DocoConstants::KELOMPOK_TINDAKAN_ID, true);
        if (!$payload->validate()) {
            return DocoHelpers::callback(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }
        $model = (new TarifTotalRsFn([
            'extParam' => [
                $payload->ruangan_id,
                $payload->penjamin_id,
                $payload->kelaspelayanan_id,
                $type
            ]
        ]));
        if ($param == 'penunjang' || $param == 'mcu') {
            $query = $model::find();
            if (!empty($kelompokTindakanIds)) {
                foreach ($kelompokTindakanIds as $value) {
                    if (isset($value['operand']) || !empty($value['operand'])) {
                        $query->andWhere([$value['operand'], $value['column'], $value['value']]);
                    } else {
                        $query->andWhere([$value['column'] => $value['value']]);
                    }
                }
            }
            $query->orWhere([
                'kelompoktindakan_id' => $payload->kelompoktindakan_id,
            ]);
        } else {
            $query = $model::find();
            if (!empty($kelompokTindakanIds)) {
                foreach ($kelompokTindakanIds as $value) {
                    if (isset($value['operand']) || !empty($value['operand'])) {
                        $query->andWhere([$value['operand'], $value['column'], $value['value']]);
                    } else {
                        $query->andWhere([$value['column'] => $value['value']]);
                    }
                }
            }
        }
        $data = $query->all();
        $newDatas = $this->reAssignIsDefaultDokterSpesialis($data, $payload);
        $newDatas = $this->reSortingByIsDefault($data, $payload);
        $result = new ArrayDataProvider([
            'allModels' => $newDatas,
            // 'pagination'=> false,
        ]);
        return $result;
    }
}
