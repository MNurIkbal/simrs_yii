<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\models\Notifikasi;
use Doco\models\NotifikasiStatus;
use Doco\models\CpptView;
use Doco\models\NotifikasiCpptFn;
use Doco\models\InfoKunjunganRiView;
use Doco\models\PasienMasukPenunjang;
use Doco\models\Pendaftaran;
use Doco\Notifications\GeneralNotification;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionTest()
    {
        $test = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) {
            $request->sendTo([
                'IntegrateByNoResep' => [
                    'noresep' => 'RSP2019411351',
                ]
            ]);
        })->execute();
        return $test;
    }

    /**
     * Read notification
     * 
     * @param String $notifikasi_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionReadNotification()
    {
        $notifikasi_id = Yii::$app->request->post('notifikasi_id', null);
        $userId = Yii::$app->jwt->user->loginpemakai_id;
        if (!empty($notifikasi_id) && !empty($userId)) {
            Notifikasi::readNotification($notifikasi_id);
            GeneralNotification::updateNotificationByUser();
            return $this->responseJson(200, 'Proses berhasil');
        } else {
            return $this->responseJson(400, 'Notifikasi tidak boleh kosong');
        }
    }

    /**
     * Refresh notification by user
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionRefreshNotification()
    {
        GeneralNotification::updateNotificationByUser();
        return $this->responseJson(200, 'Proses berhasil');
    }

    /**
     * This function will return list of pendaftaran
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUnfinishedSoap()
    {
        $employeeId = Yii::$app->jwt->user->pegawai_id;
        $type = strtoupper(Yii::$app->request->get('type', null));
       
        $types = ['RJ', 'RD', 'RI'];
        if (empty($employeeId) || empty($type)) {
            return $this->responseJson(400, empty($type) ? 'Tipe tidak boleh kosong' : 'Pegawai ID tidak ditemukan');
        } elseif (!in_array($type, $types)) {
            return $this->responseJson(400, 'Tipe tidak sesuai');
        } else {
            $result = [];
            $cpptData = NotifikasiCpptFn::getListNotifikasiCppt(Yii::$app->jwt->user->pegawai_id)
                ->orderBy(['tgl_cppt' => SORT_DESC])
                ->andWhere([
                    'jenis_pendaftaran' => $type
                ])
                ->asArray()
                ->all();
            $modul = [
                'RJ' => [
                    'modul' => 'rajal',
                    'url' => '/rajal/pemeriksaan/periksa?id=#id#&cpptId=#cpptId#',
                    'without-cppt' => '/rajal/pemeriksaan/periksa?id=#id#',
                ],
                'RD' => [
                    'modul' => 'igd',
                    'url' => '/igd/pemeriksaan-igd/periksa?id=#id#&cpptId=#cpptId#',
                    'without-cppt' => '/igd/pemeriksaan-igd/periksa?id=#id#',
                ],
                'RI' => [
                    'modul' => 'ranap',
                    'url' => '/ranap/pemeriksaan-rawat-inap/periksa?id=#id#&cpptId=#cpptId#',
                    'without-cppt' => '/ranap/pemeriksaan-rawat-inap/periksa?id=#id#',
                ]
            ];
            foreach($cpptData as $key => $item){
                $urlReplaced = [
                    'url' => $modul[$item['jenis_pendaftaran']]['url']
                ];
                if ( is_null($item['cppt_id']) ) {
                    $urlReplaced = [
                        'url' => $modul[$item['jenis_pendaftaran']]['without-cppt']
                    ];
                }
                $item['url'] = $this->helper->crossUrl('jumpto',[
                    'ruangan_id' => $item['ruangan_id'],
                    'instalasi_id' => $item['instalasi_id'],
                    'modul' => $modul[$item['jenis_pendaftaran']]['modul'],
                    'url' => str_replace(['#id#','#cpptId#'], [ $this->helper->encrypt($item['pendaftaran_id']) , $this->helper->encrypt($item['cppt_id'])], $urlReplaced['url'])
                ]);
                $result[] = $item;
            }
            return $result;
        }
    }

    public function actionUnfinishedRm()
    {
        $employeeId = Yii::$app->jwt->user->pegawai_id;
        $type = strtoupper(Yii::$app->request->get('type', null));
        $result = [];
        if (empty($employeeId)) {
            return $this->responseJson(400, 'Pegawai ID tidak ditemukan');
        } else if($type == 'RI') {
            $rmData = InfoKunjunganRiView::unfinishedRm(Yii::$app->jwt->user->pegawai_id)
                ->select([
                    'nama_pasien',
                    'no_pendaftaran',
                    'pendaftaran_id',
                    'tgl_pendaftaran',
                    'ruangan_id',
                    'instalasi_id',
                ])
                ->orderBy(['tgl_pendaftaran' => SORT_DESC])
                ->asArray()
                ->all();
            $modul = [
                'RJ' => [
                    'modul' => 'rajal',
                    'url' => '/rajal/pemeriksaan/periksa?id=#id#'
                ],
                'RD' => [
                    'modul' => 'igd',
                    'url' => '/igd/pemeriksaan-igd/periksa?id=#id#'
                ],
                'RI' => [
                    'modul' => 'ranap',
                    'url' => '/ranap/pemeriksaan-rawat-inap/periksa?id=#id#&resume=true'
                ]
            ];
            foreach($rmData as $key => $item){
                $item['url'] = $this->helper->crossUrl('jumpto',[
                    'ruangan_id' => $item['ruangan_id'],
                    'instalasi_id' => $item['instalasi_id'],
                    'modul' => $modul['RI']['modul'],
                    'url' => str_replace(['#id#'], [ $this->helper->encrypt($item['pendaftaran_id'])], $modul['RI']['url'])
                ]);
                $result[] = $item;
            }
        }
        return $result;
    }

    public function actionGetSoap()
    {
        $request = Yii::$app->request;
        $id_pegawai = $request->get('id_pegawai');
        $kelompokpegawai_id = $request->get('kelompokpegawai_id');

        $draftRm = [
            'ri' => 0,
        ];
        if ($kelompokpegawai_id == DocoConstants::KELOMPOK_PEGAWAI_DOKTER) {
            $draftRm = InfoKunjunganRiView::unfinishedRm($id_pegawai)
                ->select([
                    new \yii\db\Expression("count(pendaftaran_id) as ri"),
                ])
                ->asArray()
                ->one();

        }

        $draftSoap = [
            'rd' => 0,
            'ri' => 0,
            'rj' => 0,
        ];

        if ($kelompokpegawai_id == DocoConstants::KELOMPOK_PEGAWAI_DOKTER) {
            $draftSoap = NotifikasiCpptFn::getCountNotifikasiCppt($id_pegawai);
        }
     
        $data = [
            "draftSoap" => $draftSoap,
            "draftRm" => $draftRm,
        ];
        
        return $data;
    }

    public function actionGetDataPenunjang($id)
    {
        $data = PasienMasukPenunjang::find()->select(['pasienmasukpenunjang_t.*', new \yii\db\Expression('CASE WHEN pt.pasienadmisi_id is null THEN false ELSE true END as is_ranap')])
                ->leftJoin('pasienadmisi_t pt', 'pt.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id')
                ->where(['pasienmasukpenunjang_id' => $id])->asArray()->one();
        return [
            'data' => $data
        ];
    }

    public function actionGetPenunjangByPendaftaran($id, $no_masukpenunjang)
    {
        $data = PasienMasukPenunjang::find()->select(['pasienmasukpenunjang_t.*', new \yii\db\Expression('CASE WHEN pt.pasienadmisi_id is null THEN false ELSE true END as is_ranap')])
                ->leftJoin('pasienadmisi_t pt', 'pt.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id')
                ->where(['pasienmasukpenunjang_t.pendaftaran_id' => $id, 'pasienmasukpenunjang_t.no_masukpenunjang' => $no_masukpenunjang])->asArray()->one();
        return [
            'data' => $data
        ];
    }
}
