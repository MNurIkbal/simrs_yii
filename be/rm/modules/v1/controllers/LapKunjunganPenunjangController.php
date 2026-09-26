<?php 
/**
 * @author : Ali (ali.padilah@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoKunjunganPenunjang;
use app\modules\v1\models\JenisPemeriksaanPenunjangInstalasiV;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Jabatan;
use yii\helpers\ArrayHelper;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapKunjunganPenunjangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoKunjunganPenunjang';
    public $allowInstalasi = [4,5,7];
    public $allowUnit = [1,2,3,21];
    const LOADING_VALUE = 'Loading ...';

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
        $newActions = [
            // Excel BGProcess
            'download-excel-bgprocess' => 'app\modules\v1\actions\LapKunjunganPenunjang\DownloadExcelBgprocessAction',
            'process-sync-excel-bgprocess' => 'app\modules\v1\actions\LapKunjunganPenunjang\ProcessSyncExcelBgprocessAction',
            'drop-file-excel-bgprocess' => 'app\modules\v1\actions\LapKunjunganPenunjang\DropFileExcelBgprocessAction',
            // PDF BGProcess
            'download-pdf-bgprocess' => 'app\modules\v1\actions\LapKunjunganPenunjang\DownloadPdfBgprocessAction',
            'process-sync-pdf-bgprocess' => 'app\modules\v1\actions\LapKunjunganPenunjang\ProcessSyncPdfBgprocessAction',
            'drop-file-pdf-bgprocess' => 'app\modules\v1\actions\LapKunjunganPenunjang\DropFilePdfBgprocessAction',
            'populate-data-pdf-bgprocess' => 'app\modules\v1\actions\LapKunjunganPenunjang\PopulateDataPdfBgprocessAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $model   = new InfoKunjunganPenunjang;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']);
            }
            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_nama'];
                unset($_GET['advanced-filter']['instalasi_nama']);
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])){
                $_GET['advanced-filter']['penjamin_id'] = $_GET['advanced-filter']['penjamin_nama'];
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['jeniskegiatantindakan_nama']) && $_GET['advanced-filter']['jeniskegiatantindakan_nama'] != self::LOADING_VALUE) {
                $jeniskegiatantindakan_nama = $_GET['advanced-filter']['jeniskegiatantindakan_nama'];
                $query->andFilterWhere(['and',
                    ['ilike','jeniskegiatantindakan_nama', $jeniskegiatantindakan_nama ],
                ]);
                unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
            }

            if(isset($_GET['advanced-filter']['daftartindakan_nama'])) {
                $daftartindakan_nama = $_GET['advanced-filter']['daftartindakan_nama'];
                $query->andFilterWhere(['and',
                    ['ilike','daftartindakan_nama', $daftartindakan_nama ],
                ]);
                unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
            }

            if(isset($_GET['advanced-filter']['unit'])){
                $query->andFilterWhere(['and',
                    ['ilike','unit', $_GET['advanced-filter']['unit'] ],
                ]);
                unset($_GET['advanced-filter']['unit']);
            }
        }

        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetRuanganBy($id)
    {
        $data = Ruangan::find()->where(['instalasi_id' => $id]);

        return $data->all();
    }

    public function actionGetPenjaminBy($id)
    {
        $data = Penjamin::find()->where(['carabayar_id' => $id]);

        return $data->all();
    }

    public function actionGenerateApi()
    {
        // cara bayar
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find()->all();

        // penjamin
        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find()->all();

        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()
        ->where(['in', 'instalasi_id', $this->allowInstalasi])
        ->all();

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()->all();

        // jenis kasus penyakit
        $modelKasusPenyakit = new JenisKasusPenyakit;
        $queryKasusPenyakit = $modelKasusPenyakit::find()->all();

        // dokter PJ
        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER])->all();

        //jenis pemeriksaan
        $modelPemeriksaan = new JenisPemeriksaanPenunjangInstalasiV;
        $queryPemeriksaan = $modelPemeriksaan::find()->all();

        //unit 
        $modelUnit = new Instalasi;
        $queryUnit = $modelUnit::find()
        ->select(['instalasi_id', 'instalasi_nama'])
        ->where(['in', 'instalasi_id', $this->allowUnit])
        ->all();

        // APS unit
        $arrAps = [
            'instalasi_id' => 0,
            'instalasi_nama' => 'APS'
        ];
        array_push($queryUnit, $arrAps);

        return [
            'cara_bayar' => $queryCaraBayar,
            'penjamin' => $queryPenjamin,
            'instalasi' => $queryInstalasi,
            'ruangan' => $queryRuangan,
            'kasus_penyakit' => $queryKasusPenyakit,
            'dokter' => $queryDokter,
            'jenis' => $queryPemeriksaan,
            'unit' => $queryUnit
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        
        $tglmasukpenunjang = "";
        $no_pendaftaran = "";
        $no_rekam_medik = "";
        $nama_pasien = "";
        $unit = "";
        $instalasi_nama = false;
        $ruangan_nama = false;
        $penjamin_nama = false;
        $jeniskegiatantindakan_nama = "";
        $daftartindakan_nama = "";
        $jumlah_tindakan = "";
        $rowJumlah = $this->actionGenerateRowJumlah(false);
        
        $model   = new InfoKunjunganPenunjang;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        //Advanced Filtering
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']);
            }
            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_nama'];
                unset($_GET['advanced-filter']['instalasi_nama']);
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])){
                $_GET['advanced-filter']['penjamin_id'] = $_GET['advanced-filter']['penjamin_nama'];
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['jeniskegiatantindakan_nama']) && $_GET['advanced-filter']['jeniskegiatantindakan_nama'] != self::LOADING_VALUE) {
                $jeniskegiatantindakan_nama = $_GET['advanced-filter']['jeniskegiatantindakan_nama'];
                $query->andFilterWhere(['and',
                    ['ilike','jeniskegiatantindakan_nama', $jeniskegiatantindakan_nama ],
                ]);
                unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
            }

            if(isset($_GET['advanced-filter']['daftartindakan_nama'])) {
                $daftartindakan_nama = $_GET['advanced-filter']['daftartindakan_nama'];
                $query->andFilterWhere(['and',
                    ['ilike','daftartindakan_nama', $daftartindakan_nama ],
                ]);
                unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
            }

            if(isset($_GET['advanced-filter']['unit'])){
                $query->andFilterWhere(['and',
                    ['ilike','unit', $_GET['advanced-filter']['unit'] ],
                ]);
                unset($_GET['advanced-filter']['unit']);
            }
        }

        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        

        $title   = Yii::t('app', 'Laporan Pemeriksaan Penunjang');
        $row     = $profil = $footer = [];
        try {
            $resQueryDetail = $query->all();
            $no     = 1;
            foreach ($resQueryDetail as $value) {
                $tmp[1]  = $no;
                $tmp[2]  = date('d M Y H:i:s', strtotime($value['tglmasukpenunjang']));
                $tmp[3]  = $value['no_pendaftaran'];
                $tmp[4]  = $value['no_rekam_medik'];
                $tmp[5]  = $value['nama_pasien'];
                $tmp[6]  = $value['unit'];
                $tmp[7]  = $value['instalasi_nama'];
                $tmp[8]  = $value['penjamin_nama'];
                $tmp[9] = $value['jeniskegiatantindakan_nama'];
                $tmp[10] = $value['daftartindakan_nama'];
                $tmp[11] = $value['jumlah_tindakan'];
                $row[]   = $tmp;
                if ($instalasi_nama) $instalasi_nama = $value['instalasi_nama'];
                if ($ruangan_nama) $ruangan_nama     = $value['ruangan_nama'];
                if ($penjamin_nama) $penjamin_nama   = $value['penjamin_nama'];
                $no++;
            }
            $header = [
                'Tanggal Masuk' => $start.' Sampai Dengan '.$end,
                'Instalasi' => $instalasi_nama,
                // 'Ruangan' => $ruangan_nama,
                'Jenis Pemeriksaan' => $jeniskegiatantindakan_nama,
                'Nama Pemeriksaan' => $daftartindakan_nama,
                'Penjamin' => $penjamin_nama,
                'Total Jenis Pemeriksaan' => $rowJumlah['jumlah_jeniskegiatan'],
                'Total Nama Pemeriksaan' => $rowJumlah['jumlah_tindakan'],
                'Total Jumlah' => $rowJumlah['jumlah_hasil'],
            ];

        } catch (\Exception $e) {
            $header = [];
            $row = [];
        }
        $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Masuk',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Pendaftaran',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Rekam Medik',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nama Pasien',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Unit',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Instalasi',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Penjamin',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Pemeriksaan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nama Pemeriksaan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jumlah',
                        'rowspan'=>2,
                    ],
                ]
            ];

        $filePath = DocoHelpers::exportExcel($title, $row, $header,array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ), $footer, [], true);
        $filePath->save('php://output');
        die();
        
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #instalasi# => filter instalasi
    * @attribute #ruangan# => filter ruangan
    * @attribute #penjamin# => filter penjamin
    * @attribute #jenis_pemeriksaan# => filter jenis pemeriksaan
    * @attribute #nama_pemeriksaan# => filter nama pemeriksaan
    * @attribute #total_jenis_pemeriksaan# => total jenis pemeriksaan
    * @attribute #total_nama_pemeriksaan# => total nama pemeriksaan
    * @attribute #total_jumlah# => total jumlah
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Laporan Pemeriksaan Penunjang';
        
        $tglmasukpenunjang = "";
        $no_pendaftaran = "";
        $no_rekam_medik = "";
        $nama_pasien = "";
        $unit = "";
        $instalasi_nama = false;
        $ruangan_nama = false;
        $penjamin_nama = false;
        $jeniskegiatantindakan_nama = "";
        $daftartindakan_nama = "";
        $jumlah_tindakan = "";
        $rowJumlah = $this->actionGenerateRowJumlah($request);

        $model   = new InfoKunjunganPenunjang;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        //Advanced Filtering
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']);
            }
            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_nama'];
                unset($_GET['advanced-filter']['instalasi_nama']);
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])){
                $_GET['advanced-filter']['penjamin_id'] = $_GET['advanced-filter']['penjamin_nama'];
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['jeniskegiatantindakan_nama']) && $_GET['advanced-filter']['jeniskegiatantindakan_nama'] != self::LOADING_VALUE) {
                $jeniskegiatantindakan_nama = $_GET['advanced-filter']['jeniskegiatantindakan_nama'];
                $query->andFilterWhere(['and',
                    ['ilike','jeniskegiatantindakan_nama', $jeniskegiatantindakan_nama ],
                ]);
                unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
            }

            if(isset($_GET['advanced-filter']['daftartindakan_nama'])) {
                $daftartindakan_nama = $_GET['advanced-filter']['daftartindakan_nama'];
                $query->andFilterWhere(['and',
                    ['ilike','daftartindakan_nama', $daftartindakan_nama ],
                ]);
                unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
            }

            if(isset($_GET['advanced-filter']['unit'])){
                $query->andFilterWhere(['and',
                    ['ilike','unit', $_GET['advanced-filter']['unit'] ],
                ]);
                unset($_GET['advanced-filter']['unit']);
            }
        }


        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        try {
            $resQueryDetail = $query->all();
            foreach ($resQueryDetail as $value) {
                if ($instalasi_nama) $instalasi_nama = $value['instalasi_nama'];
                if ($ruangan_nama) $ruangan_nama     = $value['ruangan_nama'];
                if ($penjamin_nama) $penjamin_nama   = $value['penjamin_nama'];
            }
        } catch (\Exception $e) {
            
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
            '#instalasi#' => $instalasi_nama,
            '#ruangan#' => $ruangan_nama,
            '#penjamin#' => $penjamin_nama,
            '#jenis_pemeriksaan#' => $jeniskegiatantindakan_nama,
            '#nama_pemeriksaan#' => $daftartindakan_nama,
            '#total_jenis_pemeriksaan#' => $rowJumlah['jumlah_jeniskegiatan'],
            '#total_nama_pemeriksaan#' => $rowJumlah['jumlah_tindakan'],
            '#total_jumlah#' => $rowJumlah['jumlah_hasil'],
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $dataProvider->getModels(),
            ]),
        ];

        $print->Output();
    }

    public function actionListPemeriksaan($instalasi=null) {
        $data = JenisPemeriksaanPenunjangInstalasiV::find();
        if ($instalasi) {
            $data->where(
                [
                    'instalasi_id' => $instalasi
                ]
            );
        }
        $data->orderBy('jenispemeriksaan_nama');

        return ArrayHelper::map($data->all(), 'jenispemeriksaan_id', 'jenispemeriksaan_nama');
    }
    
    public function actionGenerateRowJumlah($isUnset = true)
    {
        $request = Yii::$app->request;
        $conditionQuery = "";
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        // return $request->get();
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                if($isUnset) {
                    unset($_GET['advanced-filter']['tglmasukpenunjang']);
                }
            }

            if(isset($_GET['advanced-filter']['no_pendaftaran'])){
                $conditionQuery .= " AND no_pendaftaran ILIKE '%".$_GET['advanced-filter']['no_pendaftaran']."%'";
                if($isUnset){
                    unset($_GET['advanced-filter']['no_pendaftaran']);
                }
            }

            if(isset($_GET['advanced-filter']['no_rekam_medik'])){
                $conditionQuery .= " AND no_rekam_medik = '".$_GET['advanced-filter']['no_rekam_medik']."' ";
                if($isUnset){
                    unset($_GET['advanced-filter']['no_rekam_medik']);
                }
            }

            if(isset($_GET['advanced-filter']['nama_pasien'])){
                $conditionQuery .= " AND nama_pasien ILIKE '%".$_GET['advanced-filter']['nama_pasien']."%'";
                if($isUnset){
                    unset($_GET['advanced-filter']['nama_pasien']);
                }
            }

            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $conditionQuery .= " AND instalasi_id = ".$_GET['advanced-filter']['instalasi_nama']."";
                if($isUnset){
                    unset($_GET['advanced-filter']['instalasi_nama']);
                }
            }

            if(isset($_GET['advanced-filter']['ruangan_id'])){
                $conditionQuery .= " AND ruangan_id = ".$_GET['advanced-filter']['ruangan_id']."";
                if($isUnset){
                    unset($_GET['advanced-filter']['ruangan_id']);
                }
            }

            if(isset($_GET['advanced-filter']['carabayar_id'])){
                $conditionQuery .= " AND carabayar_id = ".$_GET['advanced-filter']['carabayar_id']."";
                if($isUnset){
                    unset($_GET['advanced-filter']['carabayar_id']);
                }
            }

            if(isset($_GET['advanced-filter']['penjamin_nama']) && $_GET['advanced-filter']['penjamin_nama'] != self::LOADING_VALUE){
                $conditionQuery .= " AND penjamin_id = ".$_GET['advanced-filter']['penjamin_nama']."";
                if($isUnset){
                    unset($_GET['advanced-filter']['penjamin_nama']);
                }
            }

            if(isset($_GET['advanced-filter']['jeniskegiatantindakan_nama']) && $_GET['advanced-filter']['jeniskegiatantindakan_nama'] != self::LOADING_VALUE) {
                $conditionQuery .= " AND jeniskegiatantindakan_nama ILIKE '%".$_GET['advanced-filter']['jeniskegiatantindakan_nama']."%'";
                if($isUnset){
                    unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
                }
            }

            if(isset($_GET['advanced-filter']['daftartindakan_nama'])) {
                $conditionQuery .= " AND daftartindakan_nama ILIKE '%".$_GET['advanced-filter']['daftartindakan_nama']."%'";
                if($isUnset){
                    unset($_GET['advanced-filter']['daftartindakan_nama']);
                }
            }

            if(isset($_GET['advanced-filter']['unit']) && $_GET['advanced-filter']['unit'] != self::LOADING_VALUE){
                $conditionQuery .= " AND unit ILIKE '%".$_GET['advanced-filter']['unit']."%'";
                if($isUnset){
                    unset($_GET['advanced-filter']['unit']);
                }
            }
        }

        $sqlJenisKegiatan = "
            SELECT sum(tmp) as hasil_jenis from (
            SELECT 1 as tmp, jeniskegiatantindakan_nama from laporankunjunganpenunjang_v WHERE tglmasukpenunjang between :dateStart and :dateEnd {$conditionQuery} GROUP BY jeniskegiatantindakan_nama) a";
        $tmpJenisKegiatan = Yii::$app->db->createCommand($sqlJenisKegiatan)
            ->bindValue(':dateStart', $start)
            ->bindValue(':dateEnd', $end)
            ->queryOne();

        $sqlDaftarTindakan = "
            SELECT sum(tmp) as hasil_tindakan from (
            SELECT 1 as tmp, daftartindakan_nama from laporankunjunganpenunjang_v WHERE tglmasukpenunjang between :dateStart and :dateEnd {$conditionQuery} GROUP BY daftartindakan_nama) a";
        $tmpDaftarTindakan = Yii::$app->db->createCommand($sqlDaftarTindakan)
            ->bindValue(':dateStart', $start)
            ->bindValue(':dateEnd', $end)
            ->queryOne();
        
        $sqlJumlahTindakan = "
            SELECT SUM(jumlah_tindakan) as hasil_jumlah_tindakan 
            FROM laporankunjunganpenunjang_v 
            WHERE tglmasukpenunjang between :dateStart and :dateEnd {$conditionQuery}";
        $tmpJumlahTindakan = Yii::$app->db->createCommand($sqlJumlahTindakan)
            ->bindValue(':dateStart', $start)
            ->bindValue(':dateEnd', $end)
            ->queryOne();

        return [
            'jumlah_jeniskegiatan' => $tmpJenisKegiatan['hasil_jenis'] ?: 0,
            'jumlah_tindakan' => $tmpDaftarTindakan['hasil_tindakan'] ?: 0,
            'jumlah_hasil' => $tmpJumlahTindakan['hasil_jumlah_tindakan'] ?: 0
        ];
    }

    /**
     * @controller actionCetakPdfBgprocess
     * @attribute #periode# => Tanggal Periode Cetak
     * @attribute #datatable# => Datatable (List data)
     **/
    public function actionCetakPdfBgprocess()
    {
        // Aksi Kosongan Cetak Bgprocess PDF (Untuk show di dokumen tercetaknya)
        // Nama View Datatable : cetak_pdf_bgprocess.php
        // Pakai action dibawah : 
        // process-sync-pdf-bgprocess
        // populate-data-pdf-bgprocess
        // drop-file-pdf-bgprocess
        // download-pdf-bgprocess
        return true;
    }
}