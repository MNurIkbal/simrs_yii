<?php
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use app\modules\v1\models\CpptGiziView;
use app\modules\v1\models\CpptAdime;
use app\modules\v1\models\KonfigSystem;
use Doco\models\Pendaftaran;
use Doco\models\SoapRsView;
use yii\helpers\ArrayHelper;

class CpptGiziController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\AsesmenAwalGizi';
    public $konfigCpptKosong;

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

    public function init()
    {
        parent::init();
        $this->konfigSystemCppt();
    }

    public function konfigSystemCppt() {
        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong'])->asArray()->one();
        $this->konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];
    }

    public function actionGetCpptData()
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id');
        $getCpptData = CpptGiziView::find()
            ->where(['cpptgizi_v.pendaftaran_id' => $pendaftaran_id]);
        $cpptData = [];
        $totalData = $getCpptData->count();

        $dataCppt = $getCpptData->asArray()->all();
        $cpptAdime = CpptAdime::find()
            ->where(['pagt_id' => ArrayHelper::getColumn($dataCppt, 'pagt_id')])
            ->asArray()->all();
        $cpptAdime = ArrayHelper::index($cpptAdime, 'pagt_id');
        
        $arrayException = ['tgl_kajian', 'tgl_monev', 'tgl_asupanmakanan'];
        foreach($dataCppt as $key => $cppt) {
            foreach($cppt as $index => $value){
                if( !in_array($index, $arrayException) ) {
                    $cppt[$index] = is_null($value) || empty($value) ? '-' : $value;
                }
            }
            if(isset($cpptAdime[$cppt['pagt_id']])) {
                $adime = $cpptAdime[$cppt['pagt_id']];
            } else {
                $adime = [
                    'suggestion' => null,
                    'asesmen_gizi' => null,
                    'diagnosa_gizi' => null,
                    'intervensi_gizi' => null,
                    'monitoring' => null,
                    'evaluasi' => null,
                ];
            }
            $cppt = array_merge($cppt, $adime);

            if($cppt['suggestion'] === false) { // bukan suggestion lagi
                $cpptData[] = [
                    'sort' => date('Y-m-d H:i:s', strtotime($cppt['tgl_kajian'])),
                    'tgl_cppt' => date('d-F-Y H:i:s', strtotime($cppt['tgl_kajian'])),
                    'pegawai_nama' => $cppt['pemberi_asuhan'],
                    'hasil_asesmen' => $this->renderPartial('_hasilasesmen', compact('cppt')),
                    'form_asesmen' => [
                        'pagt_id' => $cppt['pagt_id'],
                        'cpptadime_id' => isset($cppt['cpptadime_id']) ? $cppt['cpptadime_id'] : null,
                        'asesmen_gizi' => $cppt['asesmen_gizi'],
                        'diagnosa_gizi' => $cppt['diagnosa_gizi'],
                        'intervensi_gizi' => $cppt['intervensi_gizi'],
                        'monitoring' => $cppt['monitoring'],
                        'evaluasi' => $cppt['evaluasi'],
                    ],
                    'instruksi_ppa' => $cppt['instruksi_ppa'],
                    'verifikasi' => "
                        <button class='btn btn-labeled btn-xs btn-success edit-adime' data-pagt_id='".$cppt['pagt_id']."'><b><i class='fa fa-edit'></i></b> Edit ADIME</button>
                        <br>
                        <button class='btn btn-labeled btn-xs btn-success hidden simpan-adime' data-pagt_id='".$cppt['pagt_id']."'><b><i class='fa fa-floppy-o'></i></b> Simpan</button>
                        <button class='btn btn-labeled btn-xs btn-danger hidden batal-adime' data-pagt_id='".$cppt['pagt_id']."'><b><i class='fa fa-times'></i></b> Batal Edit</button>
                        "
                ];                
            } else {
                $cpptData[] = [
                    'sort' => date('Y-m-d H:i:s', strtotime($cppt['tgl_kajian'])),
                    'tgl_cppt' => date('d-F-Y H:i:s', strtotime($cppt['tgl_kajian'])),
                    'pegawai_nama' => $cppt['pemberi_asuhan'],
                    'form_asesmen' => [
                        'pagt_id' => $cppt['pagt_id'],
                        'cpptadime_id' => isset($cppt['cpptadime_id']) ? $cppt['cpptadime_id'] : null,
                        'asesmen_gizi' => empty($cppt['asesmen_gizi']) ? $this->renderPartial('/pagt/_asesmen_gizi', ['cppt' => $cppt]) : $cppt['asesmen_gizi'],
                        'diagnosa_gizi' => empty($cppt['diagnosa_gizi']) ? $this->renderPartial('/pagt/_diagnosa_gizi', ['cppt' => $cppt]) : $cppt['diagnosa_gizi'],
                        'intervensi_gizi' => empty($cppt['intervensi_gizi']) ? $this->renderPartial('/pagt/_intervensi_gizi', ['cppt' => $cppt]) : $cppt['intervensi_gizi'],
                        'monitoring' => empty($cppt['monitoring']) ? $this->renderPartial('/pagt/_monitoring', ['cppt' => $cppt]) : $cppt['monitoring'],
                        'evaluasi' => empty($cppt['evaluasi']) ? $this->renderPartial('/pagt/_evaluasi', ['cppt' => $cppt]) : $cppt['evaluasi'],
                    ],
                    'instruksi_ppa' => $cppt['instruksi_ppa'],
                    'verifikasi' => "
                        <button class='btn btn-labeled btn-xs btn-success simpan-adime' data-pagt_id='".$cppt['pagt_id']."'><b><i class='fa fa-floppy-o'></i></b> Simpan</button>
                        "                    
                ];

            }
        }

        if (Yii::$app->request->get('pelayanan')) {
            $pend_asal = Pendaftaran::find()
                ->select([
                    new \yii\db\Expression("(additional_data::json->>'pendaftaranasal_id') AS pend_asal")
                ])
                ->where(compact('pendaftaran_id'))
                ->asArray()
                ->one();
    
            $cpptPelayanan = SoapRsView::find()
                ->select([
                    'tgl_soaprj AS tgl_cppt',
                    'a_diag_utama',
                    'a_diag_penyerta',
                    'nama_pegawai',
                    'subject',
                    'object',
                    'planning',
                    'catatan_dokter',
                    'catatan_perawat',
                    'instruksi',
                    'pegawai_instruksi',
                    'ruangan_nama',
                    'no_tempattidur',
                    'kamarruangan_nokamar'
                ])
                ->andWhere(compact('pendaftaran_id'))
                ->orWhere(['pendaftaran_id' => $pend_asal])
                ->orderBy(['tgl_soaprj' => SORT_ASC]);

            if($this->konfigCpptKosong == TRUE) {
                $cpptPelayanan->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
            }
    
            $dataPelayanan = $cpptPelayanan->count();
            $totalData += $dataPelayanan;

            return [
                'data' => $cpptData,
                'data_pelayanan' => $cpptPelayanan->asArray()->all(),
                'total' => $totalData
            ];
        }

        return [
            'data' => $cpptData,
            'total' => $totalData
        ];
    }



    public function actionSimpanAdime()
    {
        $request = Yii::$app->request;
        $dataAdime = $request->post('formdata', []);
        $cppt = new CpptAdime;
        if(isset($dataAdime['cpptadime_id']) && !empty($dataAdime['cpptadime_id'])){
            $cppt = CpptAdime::find()->select('cpptadime_id')
                ->where(['cpptadime_id' => $dataAdime['cpptadime_id']])->one();
        }
        $cppt->attributes = $dataAdime;
        $cppt->suggestion = false;
        if(!$cppt->save()) {
            $transaction->rollBack();
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');   
        }
        return $this->responseJson(200, 'Simpan PAGT Berhasil!');
    }
}
