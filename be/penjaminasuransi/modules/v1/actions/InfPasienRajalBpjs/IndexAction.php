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
use app\modules\v1\models\DhKunjunganNewView;
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
use app\modules\v1\models\PendaftaranView;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoMessages;
use app\modules\v1\exceptions\BaseCurrentException;
use Doco\components\DocoConstansId;

class IndexAction extends BaseCurrentAction
{
    public function run()
    {
        try {
            $helpers = new DocoHelpers;
            $request = Yii::$app->request;
            $model = new DhKunjunganNewView;
            $query = $model::find();
            $pendaftaranStartDate = date('Y-m-d 00:00:00');
            $pendaftaranEndDate = date('Y-m-d 23:59:59');
            $pulangStartDate = date('Y-m-d 00:00:00');
            $pulangEndDate = date('Y-m-d 23:59:59');
            $advancedFilter = ArrayHelper::getValue($_GET, 'advanced-filter');
            if ($advancedFilter) {
                if (isset($advancedFilter['tgl_pendaftaran']) && !empty($advancedFilter['tgl_pendaftaran'])) {
                    $tglPendaftaran = ArrayHelper::getValue($advancedFilter, 'tgl_pendaftaran');
                    $tglPendaftaranRange = DocoHelpers::parsingRangeDate($tglPendaftaran);
                    $pendaftaranStartDate = ArrayHelper::getValue($tglPendaftaranRange, 'startDate');
                    $pendaftaranEndDate = ArrayHelper::getValue($tglPendaftaranRange, 'endDate');
                    $query->andWhere(['between', 'tgl_pendaftaran', $pendaftaranStartDate, $pendaftaranEndDate]);
                    unset($_GET['advanced-filter']['tgl_pendaftaran']);
                }
                if (isset($advancedFilter['tgl_pulang'])) {
                    $tglPulang = ArrayHelper::getValue($advancedFilter, 'tgl_pulang');
                    $tglPulangRange = DocoHelpers::parsingRangeDate($tglPulang);
                    $pulangStartDate = ArrayHelper::getValue($tglPulangRange, 'startDate');
                    $pulangEndDate = ArrayHelper::getValue($tglPulangRange, 'endDate');
                    unset($_GET['advanced-filter']['tgl_pulang']);
                }
                if (isset($advancedFilter['ruangan_id'])) {
                    $ruanganId = ArrayHelper::getValue($advancedFilter, 'ruangan_id');
                    unset($_GET['advanced-filter']['ruangan_id']);
                    $query->andWhere(['=', 'ruangan_id', $ruanganId]);
                }
                if (isset($advancedFilter['status_kunjungan'])) {
                    $statusKunjunganId = ArrayHelper::getValue($advancedFilter, 'status_kunjungan');
                    unset($_GET['advanced-filter']['status_kunjungan']);
                    $query->andWhere(['=', 'status_kunjungan', $statusKunjunganId]);
                }
                if (isset($advancedFilter['nama_pasien'])) {
                    $dataPasien = ArrayHelper::getValue($advancedFilter, 'nama_pasien');
                    unset($_GET['advanced-filter']['pasien']);
                    $query->andWhere([
                        'or',
                        ['ILIKE', 'nama_pasien', $dataPasien],
                    ]);
                }
                if (isset($advancedFilter['no_rekamedik'])) {
                    $noRekammedik = ArrayHelper::getValue($advancedFilter, 'no_rekamedik');
                    if($noRekammedik != "") {
                        $query->andWhere(['=', 'no_rekammedik', $noRekammedik]);
                    }
                }
            }
            // $riInstalasiId = (new DocoConstansId)->actionGetId('RI');
            $query->andWhere(['!=', 'instalasi_kode', DocoConstants::INSTALASI_RAWAT_INAP]);
            $query->andWhere(['between', 'tgl_pulang', $pulangStartDate, $pulangEndDate]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return $this->controller->activeDataProvider($query);
        } catch (\Exception $e) {
            $helpers->logError($e);
            return $helpers->callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'text' => $e->getMessage(),
            ]);
        } catch (BaseCurrentException $e) {
            $helpers->logError($e);
            return $helpers->callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'text' => $e->getMessage(),
            ]);
        }
    }
}
