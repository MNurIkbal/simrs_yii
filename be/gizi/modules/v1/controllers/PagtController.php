<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Aris
 * @Date:   2020-10-20 23:50:21
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\Pagt;
use app\modules\v1\models\PagtMonev;
use app\modules\v1\models\CpptAdime;
use app\modules\v1\models\CpptGiziView;
use app\modules\v1\models\AsesmenAwalRI;
use app\modules\v1\models\BodyMassIndex;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\InfoPermintaanMakanView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\models\Cppt;

use Doco\Services\KasirService;

class PagtController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\AsesmenAwalGizi';
    protected $_title = 'Pagt';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * Function for handle default data Asesmen Keperawatan
     *
     * @param Integer pendaftaran_id
     * @return Array
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionIndex()
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(404, 'Data Tidak Ditemukan');
        }
        $getPagt = Pagt::find()->where(['pendaftaran_id' => $pendaftaran_id])->orderBy(['created_date' => SORT_DESC])->asArray()->one();
        $getPagtMonev = null;
        if (!is_null($getPagt)) {
            $getPagtMonev = PagtMonev::find()->where(['pagt_id' => $getPagt['pagt_id']])->orderBy([
                'pagtmonev_id' => SORT_DESC
            ])->limit(3)->asArray()->all();
        } else {
            $getPagt = [];
            $asesmenKeperawatan = AsesmenAwalRI::find()
                ->select(['additional_data'])
                ->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();

            if(isset($asesmenKeperawatan['additional_data']) && !empty($asesmenKeperawatan['additional_data'])) {
                $additional_data = json_decode($asesmenKeperawatan['additional_data'], true);
                $alergi = implode(',', [@$additional_data['alergi_obat'], @$additional_data['alergi_lainnya']]);
                $getPagt = array_merge($getPagt, [
                    'imt'=> @$additional_data['imt'],
                    'bb' => @$additional_data['berat_badan'],
                    'tb' => @$additional_data['tinggi_badan'],
                    'pandangan_alergi' => str_replace(',', ', ', $alergi),
                    'alergi' => str_replace(',', ', ', $alergi),
                ]);
            }

            $asesmenMedis = AsesmenMedis::find()
                ->select(['imt', 'berat_badan', 'tinggi_badan', 'td_systolic', 'td_diastolic'])
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            if(!empty($asesmenMedis)) {
                $getPagt = array_merge($getPagt, [
                    'imt'=> @$asesmenMedis['imt'],
                    'bb' => @$asesmenMedis['berat_badan'],
                    'tb' => @$asesmenMedis['tinggi_badan'],
                    'tekanan_darah'=> implode('/', [
                       @$asesmenMedis['td_systolic'],
                       @$asesmenMedis['td_diastolic'],
                    ]),
                ]);
            }

            $cppt = Cppt::find()->select(['a_diag_utama'])
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->andWhere(['NOT', ['a_diag_utama' => null]])
                ->orderBy(['tgl_cppt' => SORT_DESC])
                ->asArray()->one();

            if(!empty($cppt)) {
                $diag = json_decode($cppt['a_diag_utama'], true);
                $getPagt = array_merge($getPagt, [
                    'diagnosa_medis'=> isset($diag['nama']) ? @$diag['nama'] : @$diag['text'],
                ]);
            }

            $gizi = InfoPermintaanMakanView::find()->select(['catatan_diet'])
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            if(!empty($gizi)) {
                $getPagt = array_merge($getPagt, [
                    'diet'=> @$gizi['catatan_diet'],
                    'diet_dijalankan'=> @$gizi['catatan_diet'],
                    'diet_diberikan'=> @$gizi['catatan_diet'],
                ]);
            }
        }

        $data_bmi = $this->getOrSetCache(DocoConstants::VAR_CACHE_BMI, BodyMassIndex::find()->orderBy(['bmi_minimum' => SORT_ASC]));

        return [
            'pagt'      => $getPagt,
            'pagtmonev' => $getPagtMonev,
            'databmi'   => $data_bmi,
        ];
    }

    public function actionSavePagt()
    {
        $request  = Yii::$app->request;
        $dataPagt = $request->post('formdata', []);

        $dataPagtMonev  = isset($dataPagt['pagt_monev']) && !empty($dataPagt['pagt_monev']) ? $dataPagt['pagt_monev'] : [];
        $pendaftaran_id = !isset($dataPagt['pendaftaran_id']) || empty($dataPagt['pendaftaran_id']) ? null : $dataPagt['pendaftaran_id'];

        $transaction = Yii::$app->db->beginTransaction();
        $model = new Pagt;
        $model->attributes = $dataPagt;
        $model->tgl_kajian = empty($model->tgl_kajian) ? date('Y-m-d H:i:s') : $model->tgl_kajian;
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        $pagt_id = $model->getPrimaryKey();
        if( !empty($dataPagtMonev) ){
            $modelPagtMonev = new PagtMonev;
            $modelPagtMonev->pagt_id = $pagt_id;
            $modelPagtMonev->tgl_monev = date('Y-m-d H:i:s');
            $modelPagtMonev->attributes = $dataPagtMonev;
            if (!$modelPagtMonev->save()) {
                $transaction->rollBack();
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
        }

        $cppt = CpptAdime::find()->select('cpptadime_id')
            ->where(['pagt_id' => $pagt_id])->one();
        // if(empty($cppt)) {
            $arrayException = ['tgl_kajian', 'tgl_monev', 'tgl_asupanmakanan'];
            $cpptView = CpptGiziView::find()
                ->where(['pagt_id' => $pagt_id])
                ->asArray()->one();

            foreach($cpptView as $index => $value){
                if( !in_array($index, $arrayException) ) {
                    $cpptView[$index] = is_null($value) || empty($value) ? '-' : $value;
                }
            }
            $cppt = new CpptAdime;
            $cppt->attributes = [
                'pendaftaran_id' => $pendaftaran_id,
                // 'pasienadmisi_id' => null,
                'pagt_id' => $pagt_id,
                'asesmen_gizi' => $this->renderPartial('_asesmen_gizi', ['cppt' => $cpptView]),
                'diagnosa_gizi' => $this->renderPartial('_diagnosa_gizi', ['cppt' => $cpptView]),
                'intervensi_gizi' => $this->renderPartial('_intervensi_gizi', ['cppt' => $cpptView]),
                'monitoring' => $this->renderPartial('_monitoring', ['cppt' => $cpptView]),
                'evaluasi' => $this->renderPartial('_evaluasi', ['cppt' => $cpptView]),
                'suggestion' => false,
            ];
            if(!$cppt->save()) {
                $transaction->rollBack();
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
        // }
        $transaction->commit();
        $this->integrasiJasaAsuhan($pendaftaran_id);
        return $this->responseJson(200, 'Simpan PAGT Berhasil!');
    }

    /**
     * function for handle show history monev
     *
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetHistoryMonev()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if( is_null($pendaftaran_id) ){
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }

        $getHistory = PagtMonev::find()
                 ->select([
                    'pagtmonev_t.*'
                ])
                ->leftJoin('pagt_t', 'pagt_t.pagt_id = pagtmonev_t.pagt_id')
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->limit(2)
                ->orderBy(['tgl_monev' => SORT_DESC,'pagtmonev_id' => SORT_DESC])
                ->asArray()
                ->all();

        return [
            'history' => $getHistory,
        ];
    }

    public function integrasiJasaAsuhan($pendaftaran_id)
    {
        $jasaAsuhanGiziCache = $this->getOrSetCache(DocoConstants::K_T_JAG, (new \Doco\models\ConstantsId)->find()->select([
            'kode_id'
        ])->where([
                'kode_transaksi' => DocoConstants::K_T_JAG
            ]), false);
        if( !isset($jasaAsuhanGiziCache['kode_id']) || empty($jasaAsuhanGiziCache['kode_id'])){
            Yii::error([
                'log-pagt-kasir' => 'Const Jasa Asuhan Gizi Belum Ditambahkan'
            ]);
            return true;
        }
        $getPendaftaran = (new \app\modules\v1\models\PasienAdmisi)->find()->select([
            'pasienadmisi_t.pendaftaran_id',
            'pasienadmisi_t.pasienadmisi_id',
            'pasienadmisi_t.kelaspelayanan_id',
            'pasienadmisi_t.penjamin_id',
            'pendaftaran_t.no_pendaftaran'
        ])
        ->leftJoin('pendaftaran_t', 'pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id')->where([
            'pasienadmisi_t.pendaftaran_id' => $pendaftaran_id
        ])->asArray()->one();

        $checkIntegrasiJasa = (new \Doco\models\TindakanPelayanan)->find()
        ->select([
            'tindakanpelayanan_id',
        ])
        ->where([
            'pendaftaran_id' => $getPendaftaran['pendaftaran_id'],
            'pasienadmisi_id' => $getPendaftaran['pasienadmisi_id'],
            'kelaspelayanan_id' => $getPendaftaran['kelaspelayanan_id'],
            'penjamin_id' => $getPendaftaran['penjamin_id'],
            'ruangan_id' => Yii::$app->jwt->ruangan_id,
            'instalasi_id' => Yii::$app->jwt->instalasi_id,
            'daftartindakan_id' => $jasaAsuhanGiziCache['kode_id']
        ])->andWhere([
            'between', 'created_date', date('Y-m-d 00:00:00'), date('Y-m-d 23:58:00')
        ])
        ->one();
        if( $checkIntegrasiJasa ){
            return true;
        }
        return (new KasirService)->tagihan($getPendaftaran, [
            [
                'dokter_id' => Yii::$app->jwt->user->pegawai_id,
                'qty' => 1,
                'daftartindakan_id' => $jasaAsuhanGiziCache['kode_id'],
            ]
        ]);
    }
}
