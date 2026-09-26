<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\InfopasienSudahSoapDetailV;
use app\modules\v1\models\InfopasienSudahSoapFn;
use app\modules\v1\models\InfopasienSudahSoapV;
use app\modules\v1\models\InforuanganinstalasipegV;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class LapPengisianSoapDokterController extends DocoActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = '';
    const INSTALASI_ID = 'instalasi_id';
    const RUANGAN_ID = 'ruangan_id';
    const PEGAWAI_ID = 'pegawai_id';
    public static $LIST_INSTALASI = [
        DocoConstants::INST_ID_RJ,
        DocoConstants::INST_ID_RD,  
        DocoConstants::INST_ID_RI,
    ];


    public function verbs()
    {
        $verbs = parent::verbs();

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
        $request = Yii::$app->request;
        $getRequest = $request->get();
        
        return $this->generateData($getRequest);
    }

    public function actionGetDataPasien()
    {
        $request = Yii::$app->request;
        $data = $tmpDetail = [];
        $no_pendaftar = null;
        $instalasi_id = $request->get(self::INSTALASI_ID);
        $ruangan_id = $request->get(self::RUANGAN_ID);
        $pegawai_id = $request->get(self::PEGAWAI_ID);
        $tgl_pendaftaran = $request->get('tgl_pendaftaran');
        $explode = explode('/', $tgl_pendaftaran);
                
        if (count($explode) == 2) {
            $start = date('Y-m-d', strtotime($explode[0]));
            $end = date('Y-m-d', strtotime($explode[1]));
        } else {
            $start = date('Y-m-d');
            $end = date('Y-m-d');
        }

        unset($_GET['advanced-filter']['tgl_pendaftaran']); 

        $model = new InfopasienSudahSoapDetailV;
        $query = $model::find()->andWhere([
            self::INSTALASI_ID => $instalasi_id,
            self::RUANGAN_ID => $ruangan_id,
            self::PEGAWAI_ID => $pegawai_id
        ]);

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => false]);
        $tmpData = $dataProvider->getModels();
        $detail = $query->asArray()->all();
        
        // Count Soap dokter
        foreach($detail as $k => $v) {
            $peg = $v[self::PEGAWAI_ID];
            $rua = $v[self::RUANGAN_ID];
            $no_rm = $v['no_rm'];
            $count = 1;
            if(!isset($tmpDetail[$peg][$rua][$no_rm])) {
                $tmpDetail[$peg][$rua][$no_rm]['jumlah_soap'] = $count;
                
            } else {
                $tmpDetail[$peg][$rua][$no_rm]['jumlah_soap']++;

            }
        }

        if (!empty($tmpData)) {
            foreach ($tmpData as $key => $value) {
                $pegawaiId = $value[self::PEGAWAI_ID];
                $ruangan_id= $value[self::RUANGAN_ID];
                $noRM= $value['no_rm'];
                $jumlahSoap = 0;
                if(array_key_exists($pegawaiId, $tmpDetail)) {
                    foreach($tmpDetail as $k => $v) {
                        if($k == $pegawaiId) {
                            foreach($v as $kk => $vv) {
                                if($kk == $ruangan_id){
                                    foreach ($vv as $key_rm => $no_rm) {
                                        if ($key_rm == $noRM) {
                                            $jumlahSoap = $no_rm['jumlah_soap'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($jumlahSoap != 0) {
                        if ($no_pendaftar != $value['no_pendaftaran']) {
                            $no_pendaftar = $value['no_pendaftaran'];
                            $data[$key]['no_pendaftaran'] = $value['no_pendaftaran'];
                            $data[$key]['no_rm'] = $value['no_rm'];
                            $data[$key]['nama_pasien'] = $value['nama_pasien'];
                            $data[$key]['tgl_pendaftaran'] = $value['tgl_pendaftaran'];
                            $data[$key]['jumlah_soap'] = $jumlahSoap;
                        }
                    }
                }
            }

        }

        return new ArrayDataProvider([
            'allModels' => $data,
        ]);
    }

    public function actionGetNama()
    {
        $getRequest = Yii::$app->request->get();
        $instalasi_id = $getRequest[self::INSTALASI_ID];
        $ruangan_id = $getRequest[self::RUANGAN_ID];
        $pegawai = $getRequest['pegawai'];

        $dokter = Pegawai::findOne($pegawai)->nama_pegawai;
        $instalasi_nama = Instalasi::findOne($instalasi_id)->instalasi_nama;
        $ruangan_nama = Ruangan::findOne($ruangan_id)->ruangan_nama;

        return [
            'instalasi_nama' => $instalasi_nama,
            'ruangan_nama' => $ruangan_nama,
            'dokter' => $dokter,
        ];
    }

    public function actionExportExcel()
    {
        $jenisTxt = 'SOAP';
        $title = 'LAPORAN PENGISIAN SOAP DOKTER';
        try {
            $request = Yii::$app->request->get();;
            $start = date('Y-m-d');
            $end = date('Y-m-d');
            $instalasi = '-';
            $ruangan = '-';
            $dokter = '-';
            $tempData = $this->generateData($request, true);

            if (isset($request['advanced-filter'])) {
                if (isset($request['advanced-filter']['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                    
                    if (count($explode) == 2) {
                        $start = date('Y-m-d', strtotime($explode[0]));
                        $end = date('Y-m-d', strtotime($explode[1]));
                    }
                }
    
                if (isset($request['advanced-filter'][self::INSTALASI_ID])) {
                    $instalasi = Instalasi::findOne($request['advanced-filter'][self::INSTALASI_ID])->instalasi_nama;
                }
    
                if (isset($request['advanced-filter'][self::RUANGAN_ID])) {
                    $ruangan = Ruangan::findOne($request['advanced-filter'][self::RUANGAN_ID])->ruangan_nama;
                }
    
                if (isset($request['advanced-filter'][self::PEGAWAI_ID])) {
                    $dokter = Pegawai::findOne($request['advanced-filter'][self::PEGAWAI_ID])->nama_pegawai;
                }
    
                if (isset($request['advanced-filter']['jenis_laporan'])) {
                    $jenis_laporan = $request['advanced-filter']['jenis_laporan'];
                }
            }

            $data = [];
            if (!empty($tempData)) {
                $counter = 0;
                foreach ($tempData as $index => $value) {
                    $data[$counter][\Yii::t('app', 'Instalasi')] = $value["instalasi_nama"];
                    $data[$counter][\Yii::t('app', 'Ruangan')] = $value["ruangan_nama"];
                    $data[$counter][\Yii::t('app', 'Dokter')] = $value["nama_dokter"];
                    $data[$counter][\Yii::t('app', 'Jumlah SOAP')] = $value["jumlah_soap"];
                    $data[$counter][\Yii::t('app', 'Jumlah Pasien')] = $value["jumlah_pasien"];
                    $counter++;
                }
            }
            $header = [
                'Laporan' => $jenisTxt,
                'Tanggal Pendaftaran' => $start.' - '.$end,
                'Instalasi' => $instalasi,
                'Ruangan' => $ruangan,
                'Dokter' => $dokter,
            ];

            $header = array_filter($header);

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [],[], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcelDetail($pegawai_id, $instalasi_id, $ruangan_id, $tgl_pendaftaran)
    {
        $jenisTxt = 'SOAP';
        $title = 'LAPORAN DETAIL PENGISIAN SOAP DOKTER';
        try {
            $request = Yii::$app->request->get();
            $no_pendaftar = null;
            $getData = [];
            $explode = explode('/', $tgl_pendaftaran);
            if (count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));
            } else {
                $start = date('Y-m-d');
                $end = date('Y-m-d');
            }
            unset($_GET['advanced-filter']['tgl_pendaftaran']); 

            $model = new InfopasienSudahSoapDetailV;
            $query = $model::find()->andWhere([
                self::INSTALASI_ID => $instalasi_id,
                self::RUANGAN_ID => $ruangan_id,
                self::PEGAWAI_ID => $pegawai_id
            ]);
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => false]);
            $tmpData = $dataProvider->getModels();
            $detail = $query->asArray()->all();

            foreach($detail as $k => $v) {
                $peg = $v[self::PEGAWAI_ID];
                $rua = $v[self::RUANGAN_ID];
                $no_rm = $v['no_rm'];
                $count = 1;
                if(!isset($tmpDetail[$peg][$rua][$no_rm])) {
                    $tmpDetail[$peg][$rua][$no_rm]['jumlah_soap'] = $count;
                    
                } else {
                    $tmpDetail[$peg][$rua][$no_rm]['jumlah_soap']++;
    
                }
            }
    
            if (!empty($tmpData)) {
                foreach ($tmpData as $key => $value) {
                    $pegawaiId = $value[self::PEGAWAI_ID];
                    $ruangan_id= $value[self::RUANGAN_ID];
                    $noRM= $value['no_rm'];
                    $jumlahSoap = 0;
                    if(array_key_exists($pegawaiId, $tmpDetail)) {
                        foreach($tmpDetail as $k => $v) {
                            if($k == $pegawaiId) {
                                foreach($v as $kk => $vv) {
                                    if($kk == $ruangan_id){
                                        foreach ($vv as $key_rm => $no_rm) {
                                            if ($key_rm == $noRM) {
                                                $jumlahSoap = $no_rm['jumlah_soap'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if($jumlahSoap != 0) {
                            if ($no_pendaftar != $value['no_pendaftaran']) {
                                $no_pendaftar = $value['no_pendaftaran'];
                                $getData[$key]['no_pendaftaran'] = $value['no_pendaftaran'];
                                $getData[$key]['no_rm'] = $value['no_rm'];
                                $getData[$key]['nama_pasien'] = $value['nama_pasien'];
                                $getData[$key]['tgl_pendaftaran'] = $value['tgl_pendaftaran'];
                                $getData[$key]['jumlah_soap'] = $jumlahSoap;
                            }
                        }
                    }
                }
    
            }

            $data = [];
            if (!empty($getData)) {
                $counter = 0;
                foreach ($getData as $index => $value) {
                    $data[$counter][\Yii::t('app', 'No Pendaftaran')] = $value["no_pendaftaran"];
                    $data[$counter][\Yii::t('app', 'No Rekam Medik')] = $value["no_rm"];
                    $data[$counter][\Yii::t('app', 'Nama')] = $value["nama_pasien"];
                    $data[$counter][\Yii::t('app', 'Tanggal Pendaftaran')] = $value["tgl_pendaftaran"];
                    $data[$counter][\Yii::t('app', 'Jumlah SOAP')] = $value["jumlah_soap"];
                    $counter++;
                }
            }
            $header = [
                'Laporan' => 'SOAP',
                'Tanggal Pendaftaran' => $start.' - '.$end,
            ];

            $header = array_filter($header);

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [],[], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGenerateApi()
    {
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()
        ->where(['in', self::INSTALASI_ID,self::$LIST_INSTALASI]);

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
            'pagination' => false
        ]);

        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()
        ->where(['in', self::INSTALASI_ID,self::$LIST_INSTALASI]);

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

        $modelJenisLaporan = new Lookup;
        $qryJenisLaporan = $modelJenisLaporan::find()->where(['lookup_type' => Lookup::JENIS_LAPORAN]);
        $qryJenisLaporan = DocoRestActiveFilter::advancedFilter($modelJenisLaporan, $qryJenisLaporan);
        $qryJenisLaporan = new ActiveDataProvider([
            'query' => $qryJenisLaporan,
            'pagination' => false
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'dokter' => $queryDokter->getModels(),
            'jenisLaporan' => $qryJenisLaporan->getModels(),
        ];
    }

    private function generateData($filter, $isExport = false)
    {
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $data = $jumlahPasien = $tmpDetail = $getData = [];
        $jenis_laporan = DocoConstants::L_T_SOAP_DOKTER;
        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('tgl_pendaftaran', $filter['advanced-filter'])) {
                $explode = explode(' - ', $filter['advanced-filter']['tgl_pendaftaran']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }

            if (array_key_exists('jenis_laporan', $filter['advanced-filter'])) {
                $jenis_laporan = $filter['advanced-filter']['jenis_laporan'];
                unset($_GET['advanced-filter']['jenis_laporan']);
            }
        }

        $where_instalasi = $where_pegawai = $where_ruangan = '';
        $request = Yii::$app->request;
        if ($request->get('advanced-filter')) {
            $advancedFilter = $request->get('advanced-filter');
            if (isset($advancedFilter['instalasi_id'])) {
                $instalasi_id = $advancedFilter['instalasi_id'];
                $where_instalasi = "AND instalasi_id = '{$instalasi_id}'";
            }
            if (isset($advancedFilter['ruangan_id'])) {
                $ruangan_id = $advancedFilter['ruangan_id'];
                $where_ruangan = "AND ruangan_id = '{$ruangan_id}'";
            }
            if (isset($advancedFilter['pegawai_id'])) {
                $pegawai_id = $advancedFilter['pegawai_id'];
                $where_pegawai = "AND pegawai_id = '{$pegawai_id}'";
            }
        }
        $command = Yii::$app->db->createCommand("SELECT
               instalasi_id,
                instalasi,
                ruangan_id,
                ruangan,
                pegawai_id,
                dokter,
                count(jumlah_pasien) as jumlah_pasien,
                sum(jumlah_soap) as jumlah_soap
            FROM infopasiensudahsoap_fn(:xstart_date, :xend_date)
            WHERE instalasi_id In (1,2,3)
            $where_instalasi
            $where_ruangan
            $where_pegawai
            GROUP BY instalasi_id, ruangan_id, ruangan, pegawai_id, dokter,instalasi
            Order By  dokter asc
        ")
        ->bindParam(':xstart_date', $start)
        ->bindParam(':xend_date', $end);

        
        $pasienSudahSoap = $command->queryAll();

        if (!empty($pasienSudahSoap)) {
            foreach ($pasienSudahSoap as $key => $value) {
                $data[$key][self::INSTALASI_ID] = $value[self::INSTALASI_ID];
                $data[$key]['instalasi_nama'] = $value['instalasi'];
                $data[$key][self::RUANGAN_ID] = $value[self::RUANGAN_ID];
                $data[$key]['ruangan_nama'] = $value['ruangan'];
                $data[$key][self::PEGAWAI_ID] = $value[self::PEGAWAI_ID];
                $data[$key]['nama_dokter'] = $value['dokter'];
                $data[$key]['tgl_pendaftaran'] = $start.'/'.$end;
                $data[$key]['jumlah_soap'] = $value['jumlah_soap'];
                $data[$key]['jumlah_pasien'] = $value['jumlah_pasien'];
                $data[$key]['jenis_laporan'] = $jenis_laporan;
            }
        }
        if($isExport) {
            return $data;
        } else {
            return new ArrayDataProvider([
                'allModels' => $data, 
                'pagination' => [
                    'pageSize' => $filter['per-page'],
                ],
            ]);
        }
    }
}