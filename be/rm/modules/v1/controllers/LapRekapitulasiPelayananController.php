<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Instalasi;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DataPasienPerdokter;

class LapRekapitulasiPelayananController extends DocoActiveController
{
    /**
     * @inheritdoc
     */
	public $modelClass = 'app\modules\v1\models\LaporanRekapKunjunganPerpoli';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['index'] = ['GET'];
        $verbs['summary'] = ['GET'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        $request    = Yii::$app->request;
        $getRequest = $request->get();

        $query = $this->generateData($getRequest);

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function actionSummary()
    {
        $request    = Yii::$app->request;
        $getRequest = $request->get();

        return $this->generateSummary($getRequest);
    }

    public function actionGetDataPasien()
    {
        $request = Yii::$app->request;
        $instalasi_id = $request->get('instalasi_id');
        $ruangan_id = $request->get('ruangan_id');
        $pegawai_id = $request->get('pegawai_id');
        $status_bayar = $request->get('status_bayar');
        $status_periksa = $request->get('status_periksa');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');  
     
        $model = new DataPasienPerdokter;
        $query = $model::find()->andWhere([
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id,
            'pegawai_id' => $pegawai_id
        ]);

        if ($status_bayar && $status_bayar != 'undefined') {
            $query->andWhere(['status_bayar' => $status_bayar]);
        }

        if ($status_periksa && $status_periksa != 'undefined') {
            $query->andWhere(['status_periksa' => $status_periksa]);
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $startdate, $enddate]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetNama()
    {
        $getRequest = Yii::$app->request->get();
        $instalasi_id = $getRequest['instalasi_id'];
        $ruangan_id = $getRequest['ruangan_id'];

        $instalasi_nama = Instalasi::findOne($instalasi_id)->instalasi_nama;
        $ruangan_nama = Ruangan::findOne($ruangan_id)->ruangan_nama;

        return [
            'instalasi_nama' => $instalasi_nama,
            'ruangan_nama' => $ruangan_nama
        ];
    }

    /**
    * @controller actionExportPdf
    * @attribute #pdfrekapperpoli# => Table
    * @attribute #tgl_pendaftaran# => Filter tanggal pendaftaran
    * @attribute #instalasi# => Filter instalasi
    * @attribute #ruangan# => Filter ruangan
    * @attribute #dokter# => Filter dokter
    * @attribute #status_bayar# => Filter status bayar
    * @attribute #status_periksa# => Filter status periksa
    **/
    public function actionExportPdf() 
    {
        $getRequest = Yii::$app->request->get();
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $instalasi = '-';
        $ruangan = '-';
        $dokter = '-';
        $statusBayar = '-';
        $statusPeriksa = '-';
        $print = new DocoPrint();
        $data = $this->generateData($getRequest);

        if (isset($getRequest['advanced-filter'])) {
            if (isset($getRequest['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $getRequest['advanced-filter']['tgl_pendaftaran']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
            }

            if (isset($getRequest['advanced-filter']['instalasi_id'])) {
                $instalasi = Instalasi::findOne($getRequest['advanced-filter']['instalasi_id'])->instalasi_nama;
            }

            if (isset($getRequest['advanced-filter']['ruangan_id'])) {
                $ruangan = Ruangan::findOne($getRequest['advanced-filter']['ruangan_id'])->ruangan_nama;
            }

            if (isset($getRequest['advanced-filter']['pegawai_id'])) {
                $dokter = Pegawai::findOne($getRequest['advanced-filter']['pegawai_id'])->nama_pegawai;
            }

            if (isset($getRequest['advanced-filter']['status_bayar'])) {
                $statusBayar = Lookup::findOne($getRequest['advanced-filter']['status_bayar'])->lookup_name;
            }

            if (isset($getRequest['advanced-filter']['status_periksa'])) {
                $statusPeriksa = Lookup::findOne($getRequest['advanced-filter']['status_periksa'])->lookup_name;
            }
        }

        $print->attributes = [
            '#title#' => 'LAPORAN REKAP KUNJUNGAN PERPOLI',
            '#tgl_pendaftaran#' => $start.' - '.$end,
            '#instalasi#' => $instalasi,
            '#ruangan#' => $ruangan,
            '#dokter#' => $dokter,
            '#status_bayar#' => $statusBayar,
            '#status_periksa#' => $statusPeriksa,
            '#pdfrekapperpoli#' => $this->renderPartial('index', [
                'data' => $data['data']
            ]),
        ];
        $print->Output();
    }

    public function actionExportExcel()
    {
        $getRequest        = Yii::$app->request->get();
        $start             = date('Y-m-d');
        $end               = date('Y-m-d');
        $instalasi         = '-';
        $ruangan           = '-';
        $dokter            = '-';
        $statusBayar       = '-';
        $statusPeriksa     = '-';
        $data              = array();
        $generateData      = $this->generateData($getRequest);
        $tempData['data']  = $generateData->all();

        if (isset($getRequest['advanced-filter'])) {
            if (isset($getRequest['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $getRequest['advanced-filter']['tgl_pendaftaran']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
            }

            if (isset($getRequest['advanced-filter']['instalasi_id'])) {
                $instalasi = Instalasi::findOne($getRequest['advanced-filter']['instalasi_id'])->instalasi_nama;
            }

            if (isset($getRequest['advanced-filter']['ruangan_id'])) {
                $ruangan = Ruangan::findOne($getRequest['advanced-filter']['ruangan_id'])->ruangan_nama;
            }

            if (isset($getRequest['advanced-filter']['pegawai_id'])) {
                $dokter = Pegawai::findOne($getRequest['advanced-filter']['pegawai_id'])->nama_pegawai;
            }

            if (isset($getRequest['advanced-filter']['status_bayar'])) {
                $statusBayar = Lookup::findOne($getRequest['advanced-filter']['status_bayar'])->lookup_name;
            }

            if (isset($getRequest['advanced-filter']['status_periksa'])) {
                $statusPeriksa = Lookup::findOne($getRequest['advanced-filter']['status_periksa'])->lookup_name;
            }
        }

        if (!empty($tempData['data'])) {
            foreach ($tempData['data'] as $key => $value) {
                $data[$key]['Instalasi']     = $value['instalasi_nama'];
                $data[$key]['Ruangan']       = $value['ruangan_nama'];
                $data[$key]['Dokter']        = $value['nama_dokter'];
                $data[$key]['Pasien Baru']   = $value['jumlah_pasien_baru'] ? $value['jumlah_pasien_baru'] : 0;
                $data[$key]['Pasien Lama']   = $value['jumlah_pasien_lama'] ? $value['jumlah_pasien_lama'] : 0;
                $data[$key]['Jumlah Pasien'] = $value['jumlah_pasien'] ? $value['jumlah_pasien'] : 0;
            }
        }

        $header = [
            'Tanggal Pendaftaran' => $start.' - '.$end,
            'Instalasi' => $instalasi,
            'Ruangan' => $ruangan,
            'Dokter' => $dokter,
            'Status Pembayaran' => $statusBayar,
            'Status Pemeriksaan' => $statusPeriksa
        ];
        $filePath = DocoHelpers::exportExcel(
            'LAPORAN REKAPITULASI PER UNIT PELAYANAN',
            $data,
            $header,
            [
                'uploadPath' => './uploads'
            ],
            [],
            [],
            true
        );
        $filePath->save('php://output');
        die;
    }

    public function actionGenerateApi()
    {
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()
        ->andWhere([
            'instalasi_id' => [1,2,3],
            'is_active' => true,
            'is_deleted' => false,
            'is_pelayanan' => true,
            'is_penunjang' => false
        ]);
        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
            'pagination' => false
        ]);

        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find() 
        ->andWhere([
            'instalasi_id' => [1,2,3] 
        ]);
        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
            'pagination' => false
        ]);

        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER]);
        $queryDokter = DocoRestActiveFilter::advancedFilter($modelDokter, $queryDokter);
        $queryDokter = new ActiveDataProvider([
            'query' => $queryDokter,
            'pagination' => false
        ]);

        $modelStatusBayar = new Lookup;
        $queryStatusBayar = $modelStatusBayar::find()->where(['lookup_type' => Lookup::STATUS_BAYAR]);
        $queryStatusBayar = DocoRestActiveFilter::advancedFilter($modelStatusBayar, $queryStatusBayar);
        $queryStatusBayar = new ActiveDataProvider([
            'query' => $queryStatusBayar,
            'pagination' => false
        ]);

        $modelStatusPeriksa = new Lookup;
        $queryStatusPeriksa = $modelStatusPeriksa::find()->where(['lookup_type' => Lookup::STATUS_PERIKSA]);
        $queryStatusPeriksa = DocoRestActiveFilter::advancedFilter($modelStatusPeriksa, $queryStatusPeriksa);
        $queryStatusPeriksa = new ActiveDataProvider([
            'query' => $queryStatusPeriksa,
            'pagination' => false
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'dokter' => $queryDokter->getModels(),
            'statusBayar' => $queryStatusBayar->getModels(),
            'statusPeriksa' => $queryStatusPeriksa->getModels()
        ];
    }

    public function actionListPegawai()
    {
        $model = new Pegawai;
        $query = $model::find()->joinWith(['jabatan']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetTotal($status_pasien, $total, $type = null)
    {
        if($type == 'pasien_baru') {
            if($status_pasien == 'Pasien Baru') {
                $total_pasien =  $total;
            } else {
                $total_pasien = 0;
            }
        }
        else {
            if($status_pasien == 'Pasien Lama') {
                $total_pasien =  $total;
            } else {
                $total_pasien = 0;
            }
        }

        return $total_pasien;
    }

    private function generateData($filter)
    {
        $start          = date('Y-m-d');
        $end            = date('Y-m-d');
        $status_bayar   = null;
        $status_periksa = null;

        if (!empty($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];

            if (!empty($advancedFilter['tgl_pendaftaran'])) {
                $helper = new DocoHelpers;
                $tglPendaftaranRange = $helper->parsingRangeDate($filter['advanced-filter']['tgl_pendaftaran']);
    
                $start = $tglPendaftaranRange['startDate'];
                $end   = $tglPendaftaranRange['endDate'];
            }
        }

        $query = (new \yii\db\Query())
        ->select([
            "instalasi_id",
            "ruangan_id",
            "pegawai_id",
            "instalasi_nama",
            "ruangan_nama",
            "nama_dokter",
            "coalesce(count(*), 0) as jumlah_pasien",
            "coalesce(count(*) filter (where status_pasien_id = '310'), 0) as jumlah_pasien_baru",
            "coalesce(count(*) filter (where status_pasien_id = '311'), 0) as jumlah_pasien_lama"
        ])
        ->from('datapasienperdokter_v');

        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('tgl_pendaftaran', $filter['advanced-filter'])) {
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
                $query->andWhere(['BETWEEN', 'datapasienperdokter_v.tgl_pendaftaran', $start, $end]);
            } else {
                $query->andWhere(['BETWEEN', 'datapasienperdokter_v.tgl_pendaftaran', $start, $end]);
            }

            if (array_key_exists('instalasi_id', $filter['advanced-filter'])) {
                $instalasi_id = ['instalasi_id' => $filter['advanced-filter']['instalasi_id']];
                unset($_GET['advanced-filter']['instalasi_id']);
                $query->andWhere(['datapasienperdokter_v.instalasi_id' => $instalasi_id]);
            }

            if (array_key_exists('ruangan_id', $filter['advanced-filter'])) {
                $ruangan_id = ['ruangan_id' => $filter['advanced-filter']['ruangan_id']];
                unset($_GET['advanced-filter']['ruangan_id']);
                $query->andWhere(['datapasienperdokter_v.ruangan_id' => $ruangan_id]);
            }

            if (array_key_exists('pegawai_id', $filter['advanced-filter'])) {
                $pegawai_id = ['pegawai_id' => $filter['advanced-filter']['pegawai_id']];
                unset($_GET['advanced-filter']['pegawai_id']);
                $query->andWhere(['datapasienperdokter_v.pegawai_id' => $pegawai_id]);
            }

            if (array_key_exists('status_bayar', $filter['advanced-filter'])) {
                $status_bayar = ['status_bayar' => $filter['advanced-filter']['status_bayar']];
                unset($_GET['advanced-filter']['status_bayar']);
                $query->andWhere(['datapasienperdokter_v.status_bayar' => $status_bayar]);
            }

            if (array_key_exists('status_periksa', $filter['advanced-filter'])) {
                $status_periksa = ['status_periksa' => $filter['advanced-filter']['status_periksa']];
                unset($_GET['advanced-filter']['status_periksa']);
                $query->andWhere(['datapasienperdokter_v.status_periksa' => $status_periksa]);
            }
        } else {
            $query->andWhere(['BETWEEN', 'datapasienperdokter_v.tgl_pendaftaran', $start, $end]);
            $query->andWhere(['datapasienperdokter_v.instalasi_id' => [1, 2, 3] ]);
        }

        $query->groupBy('datapasienperdokter_v.instalasi_id,
			datapasienperdokter_v.ruangan_id,
			datapasienperdokter_v.pegawai_id,
			datapasienperdokter_v.instalasi_nama, 
			datapasienperdokter_v.ruangan_nama, 
			datapasienperdokter_v.nama_dokter'
		);

        return $query;
    }

    private function generateSummary($filter)
    {
        $start          = date('Y-m-d');
        $end            = date('Y-m-d');
        $status_bayar   = null;
        $status_periksa = null;
        
        if (!empty($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];

            if (!empty($advancedFilter['tgl_pendaftaran'])) {
                $helper = new DocoHelpers;
                $tglPendaftaranRange = $helper->parsingRangeDate($filter['advanced-filter']['tgl_pendaftaran']);
    
                $start = $tglPendaftaranRange['startDate'];
                $end   = $tglPendaftaranRange['endDate'];
            }
        }

        $query = (new \yii\db\Query())
        ->select([
            "instalasi_id",
            "ruangan_id",
            "pegawai_id",
            "instalasi_nama",
            "ruangan_nama",
            "nama_dokter",
            "coalesce(count(*), 0) as jumlah_pasien",
            "coalesce(count(*) filter (where status_pasien_id = '310'), 0) as jumlah_pasien_baru",
            "coalesce(count(*) filter (where status_pasien_id = '311'), 0) as jumlah_pasien_lama"
        ])
        ->from('datapasienperdokter_v');

        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('tgl_pendaftaran', $filter['advanced-filter'])) {
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
                $query->andWhere(['BETWEEN', 'datapasienperdokter_v.tgl_pendaftaran', $start, $end]);
            } else {
                $query->andWhere(['BETWEEN', 'datapasienperdokter_v.tgl_pendaftaran', $start, $end]);
            }

            if (array_key_exists('instalasi_id', $filter['advanced-filter'])) {
                $instalasi_id = ['instalasi_id' => $filter['advanced-filter']['instalasi_id']];
                unset($_GET['advanced-filter']['instalasi_id']);
                $query->andWhere(['datapasienperdokter_v.instalasi_id' => $instalasi_id]);
            }

            if (array_key_exists('ruangan_id', $filter['advanced-filter'])) {
                $ruangan_id = ['ruangan_id' => $filter['advanced-filter']['ruangan_id']];
                unset($_GET['advanced-filter']['ruangan_id']);
                $query->andWhere(['datapasienperdokter_v.ruangan_id' => $ruangan_id]);
            }

            if (array_key_exists('pegawai_id', $filter['advanced-filter'])) {
                $pegawai_id = ['pegawai_id' => $filter['advanced-filter']['pegawai_id']];
                unset($_GET['advanced-filter']['pegawai_id']);
                $query->andWhere(['datapasienperdokter_v.pegawai_id' => $pegawai_id]);
            }

            if (array_key_exists('status_bayar', $filter['advanced-filter'])) {
                $status_bayar = ['status_bayar' => $filter['advanced-filter']['status_bayar']];
                unset($_GET['advanced-filter']['status_bayar']);
                $query->andWhere(['datapasienperdokter_v.status_bayar' => $status_bayar]);
            }

            if (array_key_exists('status_periksa', $filter['advanced-filter'])) {
                $status_periksa = ['status_periksa' => $filter['advanced-filter']['status_periksa']];
                unset($_GET['advanced-filter']['status_periksa']);
                $query->andWhere(['datapasienperdokter_v.status_periksa' => $status_periksa]);
            }
        } else {
            $query->andWhere(['BETWEEN', 'datapasienperdokter_v.tgl_pendaftaran', $start, $end]);
            $query->andWhere(['datapasienperdokter_v.instalasi_id' => [1, 2, 3] ]);
        }

        $query->groupBy('datapasienperdokter_v.instalasi_id,
			datapasienperdokter_v.ruangan_id,
			datapasienperdokter_v.pegawai_id,
			datapasienperdokter_v.instalasi_nama, 
			datapasienperdokter_v.ruangan_nama, 
			datapasienperdokter_v.nama_dokter'
		);

        return [
            'count' => [
                'jumlah_pasien' => $query->sum('jumlah_pasien'),
                'pasien_baru'   => $query->sum('jumlah_pasien_baru'),
                'pasien_lama'   => $query->sum('jumlah_pasien_lama'),
            ]
        ];
    }
}