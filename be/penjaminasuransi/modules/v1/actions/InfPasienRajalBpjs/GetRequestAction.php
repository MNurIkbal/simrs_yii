<?php

namespace app\modules\v1\actions\InfPasienRajalBpjs;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoPasienBpjsView;
use app\modules\v1\models\InfoPasienBpjsDiagnosaView;
use app\modules\v1\models\InfoPasienBpjsKlaimView;
use app\modules\v1\models\InfoKlaimInacbg;
use app\modules\v1\models\KoreksiDiagnosaView;
use app\modules\v1\models\KoreksiDiagnosa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KlaimInacbg;
use app\modules\v1\models\KlaimInacbgDetail;
use app\modules\v1\models\KlaimInacbgGroup;
use app\modules\v1\models\SyKunjunganView;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\SyKunjunganDetailView;
use app\modules\v1\models\SyBagian;
use app\modules\v1\models\InfoDiagnosa;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\SyKunjunganAdjusmentDetail;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoMessages;
use app\modules\v1\exceptions\BaseCurrentException;
use Doco\models\Lookup;

class GetRequestAction extends BaseCurrentAction
{
    public function run()
    {
        $helper = new DocoHelpers();
        $request = Yii::$app->request;
        $ruangan = $statusKunjungan = $carabayar = [];
        try {
            $ruangan = Ruangan::find()
                ->select([
                    'instalasi_id',
                    'ruangan_id',
                    'ruangan_nama',
                    'ruangan_singkatan',
                ])
                ->asArray()
                ->all();
            if ($ruangan) {
                $ruangan = ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama');
            }
            $caraBayar = CaraBayar::find()
                ->select(['carabayar_id', 'carabayar_nama', 'carabayar_namalainnya'])
                ->orderBy(['carabayar_nama' => SORT_ASC])
                ->asArray()
                ->all();
            if ($caraBayar) {
                $caraBayar = ArrayHelper::map($carabayar, 'carabayar_id', 'carabayar_nama');
            }
            $statusKunjungan = Lookup::find()
                ->where(['lookup_type' => 'status_verifikasi'])
                ->asArray()
                ->all();
            if ($statusKunjungan) {
                $statusKunjungan = ArrayHelper::map($statusKunjungan, 'lookup_id', 'lookup_name');
            }
            $response = [
                'ruangan' => $ruangan,
                'status_kunjungan' => $statusKunjungan,
                'carabayar' => $carabayar,
            ];
            return $helper->callBack(DocoMessages::KEY_SUC_SYSTEM_DATA, [
                'text' => 'Berhasil get data filter',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            $helper->logError($e);
            return $helper->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => $e->getMessage(),
            ]);
        } catch (BaseCurrentException $e) {
            $helper->logError($e);
            return $helper->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => $e->getMessage(),
            ]);
        }
    }
}
