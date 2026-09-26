<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Sunarko
 * @Date:   2018-06-05 16:21:21
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 11:48:40
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;

use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\InfoPasienBayi;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\SuratKetLahir;
use app\modules\v1\models\SuratketlahirV;
use app\modules\v1\models\Pendaftaran;

use app\components\DocoController;
use app\components\DocoDatatableHelper;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class InfSuratKeteranganBayiController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienBayi';
    protected $_title  = 'Informasi Pasien';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        //$verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        //unset($actions['update']);
        return $actions;
    }

    public function actionViewData($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = "")
    {
        $pasienbayi = InfoPasienBayi::find();
        return $pasienbayi;
    }

    private function listPekerjaan()
    {
        try {
            $datapekerjaan = Pekerjaan::find()
            ->where(['is_deleted' => false, 'is_active' => true])
            ->orderBy(['pekerjaan_nama' => SORT_ASC ])->asArray()->all();
            return $datapekerjaan;
        } catch (Exception $e) {
            return[];
        }
    }

    private function listGolonganDarah()
    {
        try {
            $datapekerjaan = Lookup::find()
            ->where(['lookup_type' => 'golongan_darah'])
            ->andWhere(['is_deleted' => false, 'is_active' => true])
            ->orderBy(['lookup_name' => SORT_ASC ])->asArray()->all();
            return $datapekerjaan;
        } catch (Exception $e) {
            return[];
        }
    }

    private function listHari()
    {
        try {
            $datapekerjaan = Lookup::find()
            ->where(['lookup_type' => 'hari'])
            ->andWhere(['is_deleted' => false, 'is_active' => true])
            ->orderBy(['lookup_id' => SORT_ASC ])->asArray()->all();
            return $datapekerjaan;
        } catch (Exception $e) {
            return[];
        }
    }

    public function actionGetPasien($id)
    {
        //Mengambil Data Bayi
        $databayi = InfoPasienBayi::find()->where([
            'pendaftaran_id'=> $id
        ])->one();

        //Mengambil Data SKL Bayi
        $datasklbayi = SuratKetLahir::find()->where([
            'pendaftaran_id'=> $id
        ])->one();

        //Mengambil Pekerjaan
        $getPekerjaan    = $this->listPekerjaan();
        $resultPekerjaan = ArrayHelper::map($getPekerjaan,'pekerjaan_id','pekerjaan_nama');

        //Mengambil Golongan Darah
        $getGolonganDarah    = $this->listGolonganDarah();
        $resultGolonganDarah = ArrayHelper::map($getGolonganDarah,'lookup_id','lookup_name');

        //Mengambil Hari
        //$getHari    = $this->listHari();
        //$resultHari = ArrayHelper::map($getHari,'lookup_id','lookup_name');

        $data = [   
            'data_pekerjaan'      => $resultPekerjaan,
            'data_golongan_darah' => $resultGolonganDarah,
            //'data_hari'           => $resultHari,
            'data_bayi'           => $databayi,
            'data_skl_bayi'       => $datasklbayi
        ]; 


        return $data;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $model   = new InfoPasienBayi;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        //Advanced Filtering
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end   = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
            }

            if(isset($_GET['advanced-filter']['nama_bayi'])) {
                $nama_bayi = $_GET['advanced-filter']['nama_bayi'];
                $query->andFilterWhere(['or',
                    ['ilike','nama_bayi', $nama_bayi ],
                    ['ilike','rm_bayi', $nama_bayi ]
                ]);
                unset($_GET['advanced-filter']['nama_bayi']);
            }

            if(isset($_GET['advanced-filter']['ruangan'])) {
                $ruangan = $_GET['advanced-filter']['ruangan'];
                $query->andFilterWhere(['or',
                    ['ilike','ruangan', $ruangan ],
                    ['ilike','kamar', $ruangan ],
                    ['ilike','no_tempattidur', $ruangan ]
                ]);
                unset($_GET['advanced-filter']['ruangan']);
            }

            if(isset($_GET['advanced-filter']['nama_ibu'])) {
                $nama_ibu = $_GET['advanced-filter']['nama_ibu'];
                $query->andFilterWhere(['or',
                    ['ilike','nama_ibu', $nama_ibu ],
                    ['ilike','rm_ibu', $nama_ibu ]
                ]);
                unset($_GET['advanced-filter']['nama_ibu']);
            }
        }

        $query->andWhere(['between', 'infopasienbayi_v.tgl_pendaftaran', $start, $end]);

        // return $query->createCommand()->getRawSql();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        //var_dump($_GET);die();
        // return $query->createCommand()->getRawSql();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    //action digunakan untuk dipanggil di url
    public function actionSave()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->post('pendaftaran_id');
            $model = SuratKetLahir::find()->where([
                'pendaftaran_id' => $pendaftaranId
            ])->one();

            if (empty($model)) {
                $model   = new SuratKetLahir;
            }

            $model->attributes=$request->post();
            if($model->save()){
                //update status skl di infopasien bayi
                $model2 = Pendaftaran::find()->where([
                    'pendaftaran_id' => $pendaftaranId
                ])->one();
                $model2->is_skl = true;
                $model2->update(false);
                
                return [
                    'message' => 'success'
                ];
            }
            else{
                return [
                    'status'=>422,
                    'data'=>$model->errors
                ];
            }

        } catch (Exception $e) {
            return['status'=>500,'message'=>$e->getMessage()];
        }
        catch (\yii\db\Exception $e) {
           return['status'=>500,'message'=>$e->getMessage()];
        }
        
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_pasien# => Untuk Menampilkan Tabel pasien ranap
    * @attribute #periode# => untuk menampilkan periode data
    * @attribute #tanggal# => untuk menampilkan tanggal sekarang
    * @attribute #tanggal_cetak# => untuk menampilkan tanggal cetak
    * @attribute #jenis# => untuk menampilkan title
    * @attribute #cetak_oleh# => untuk menampilkan pencetak
    * @attribute #kepala# => untuk menampilkan nama kepala ruangan
    * @attribute #kepalanip# => untuk menampilkan nip kepala ruangan
    
    * @attribute #tahun#              => untuk menampilkan tahun surat
    * @attribute #suratketlahir_id#   => untuk menampilkan id surat kelahiran
    * @attribute #nama_dokter#        => untuk menampilkan nama dokter
    * @attribute #jenis_kelamin_anak# => untuk menampilkan jenis kelamin anak
    * @attribute #nama_ibu#           => untuk menampilkan nama ibu 
    * @attribute #ktp_ibu#            => untuk menampilkan ktp ibu 
    * @attribute #alamat_ibu#         => untuk menampilkan alamat ibu
    * @attribute #pekerjaan_ibu#      => untuk menampilkan pekerjaan ibu
    * @attribute #gol_darah_ibu#      => untuk menampilkan gol darah ibu
    * @attribute #nama_ayah#          => untuk menampilkan nama ayah
    * @attribute #ktp_ayah#           => untuk menampilkan ktp ayah
    * @attribute #alamat_ayah#        => untuk menampilkan alamat ayah
    * @attribute #pekerjaan_ayah#     => untuk menampilkan pekerjaan ayah 
    * @attribute #gol_darah_ayah#     => untuk menampilkan gol darah ayah
    * @attribute #hari_lahir_anak#    => untuk menampilkan hari lahir anak
    * @attribute #tanggal_lahir_anak# => untuk menampilkan tanggal lahir anak
    * @attribute #jam_lahir_anak#     => untuk menampilkan jam lahir anak
    * @attribute #berat_lahir_anak#   => untuk menampilkan berat lahir anak
    * @attribute #panjang_lahir_anak# => untuk menampilkan panjang lahir anak
    * @attribute #kelahiran_anak#     => untuk menampilkan kelahiran anak
    * @attribute #anak_ke#            => untuk menampilkan anak ke
    * @attribute #gol_darah_anak#     => untuk menampilkan gol darah anak
    * @attribute #lokasi#             => untuk menampilkan lokasi ttd
    * @attribute #tanggal#            => untuk menampilkan tanggal ttd
    **/

    public function actionExportPdf($pendaftaran_id){
        $data        = [];
        $model       = new SuratKetLahir;
        $jenis_title = 'SKL Bayi';
       
        $dataPrintSKL = SuratketlahirV::find()->andWhere(['pendaftaran_id'=>$pendaftaran_id])->one();

        $tahun_lahir = date('Y', strtotime($dataPrintSKL['tgl_lahir']));
        $print = new DocoPrint();

        $print->attributes = [
            '#tahun'              => ($tahun_lahir) ? $tahun_lahir : '-',  
            '#suratketlahir_id'   => ($dataPrintSKL) ? $dataPrintSKL['suratketlahir_id'] : '-',  
            '#nama_dokter'        => ($dataPrintSKL) ? $dataPrintSKL['dokter_dpjp'] : '-',   
            '#jenis_kelamin_anak' => ($dataPrintSKL) ? $dataPrintSKL['jeniskelamin_bayi'] : '-',  
            '#nama_ibu'           => ($dataPrintSKL) ? $dataPrintSKL['ibu_nama'] : '-',  
            '#ktp_ibu'            => ($dataPrintSKL) ? $dataPrintSKL['ibu_ktp'] : '-',  
            '#alamat_ibu'         => ($dataPrintSKL) ? $dataPrintSKL['ibu_alamat'] : '-',  
            '#pekerjaan_ibu'      => ($dataPrintSKL) ? $dataPrintSKL['ibu_pekerjaan'] : '-', 
            '#gol_darah_ibu'      => ($dataPrintSKL) ? $dataPrintSKL['ibu_golongandarah'] : '-',  
            '#nama_ayah'          => ($dataPrintSKL) ? $dataPrintSKL['ayah_nama'] : '-',  
            '#ktp_ayah'           => ($dataPrintSKL) ? $dataPrintSKL['ayah_ktp'] : '-',  
            '#alamat_ayah'        => ($dataPrintSKL) ? $dataPrintSKL['ayah_alamat'] : '-',  
            '#pekerjaan_ayah'     => ($dataPrintSKL) ? $dataPrintSKL['ayah_pekerjaan'] : '-',  
            '#gol_darah_ayah'     => ($dataPrintSKL) ? $dataPrintSKL['ayah_golongandarah'] : '-',  
            '#hari_lahir_anak'    => ($dataPrintSKL) ? $dataPrintSKL['hari_lahir'] : '-',  
            '#tanggal_lahir_anak' => ($dataPrintSKL) ? $dataPrintSKL['tgl_lahir'] : '-',  
            '#jam_lahir_anak'     => ($dataPrintSKL) ? $dataPrintSKL['jam_lahir'] : '-',  
            '#berat_lahir_anak'   => ($dataPrintSKL) ? $dataPrintSKL['bb_lahir'] : '-',  
            '#panjang_lahir_anak' => ($dataPrintSKL) ? $dataPrintSKL['panjang_lahir'] : '-',  
            '#kelahiran_anak'     => ($dataPrintSKL) ? $dataPrintSKL['kelahiran'] : '-',  
            '#anak_ke'            => ($dataPrintSKL) ? $dataPrintSKL['anakke'] : '-',  
            '#gol_darah_anak'     => ($dataPrintSKL) ? $dataPrintSKL['golongandarah_bayi'] : '-',  
            '#lokasi'             => 'Bandung',  
            '#tanggal'            => DocoHelpers::convDateTime(date('d M Y')),

        ];

        $print->Output();
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        
        $model   = new InfoPasienBayi;
        $query   = $model::find();
        
        $periode   = '';
        $nama_bayi = '';
        $ruangan   = '';
        $nama_ibu  = '';

        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        //Advanced Filtering
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end   = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
            }

            if(isset($_GET['advanced-filter']['nama_bayi'])) {
                $nama_bayi = $_GET['advanced-filter']['nama_bayi'];
                $query->andFilterWhere(['or',
                    ['ilike','nama_bayi', $nama_bayi ],
                    ['ilike','rm_bayi', $nama_bayi ],
                ]);
                unset($_GET['advanced-filter']['nama_bayi']);
            }

            if(isset($_GET['advanced-filter']['ruangan'])) {
                $ruangan = $_GET['advanced-filter']['ruangan'];
                $query->andFilterWhere(['or',
                    ['ilike','ruangan', $ruangan ],
                    ['ilike','kamar', $ruangan ],
                    ['ilike','no_tempattidur', $ruangan ],
                ]);
                unset($_GET['advanced-filter']['ruangan']);
            }

            if(isset($_GET['advanced-filter']['nama_ibu'])) {
                $nama_ibu = $_GET['advanced-filter']['nama_ibu'];
                $query->andFilterWhere(['or',
                    ['ilike','nama_ibu', $nama_ibu ],
                    ['ilike','rm_ibu', $nama_ibu ],
                ]);
                unset($_GET['advanced-filter']['nama_ibu']);
            }
        }

        $periode    = $start." Sampai Dengan ".$end;
        
        $header = [
            'Tanggal Pendaftaran :' => $periode,
            'No RM/Nama Bayi :'     => $nama_bayi,
            'Ruangan/Kamar/TT :'    => $ruangan,
            'No RM/Nama Ibu :'      => $nama_ibu,
        ];

        $query->andWhere(['between', 'infopasienbayi_v.tgl_pendaftaran', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $title   = Yii::t('app', 'Informasi Surat Keterangan Bayi');
        $row     = $profil = $footer = [];
        try {
            $resQueryDetail = $query->all();
            //$header = [];
            $no     = 1;
            foreach ($resQueryDetail as $value) {
                $tmp[1] = $no;
                $tmp[2] = $value['tgl_pendaftaran'];
                $tmp[3] = $value['rm_bayi'].'/'.$value['nama_bayi'];
                $tmp[4] = $value['tanggal_lahir'];
                $tmp[5] = $value['ruangan'].'/'.$value['kamar'].'/'.$value['no_tempattidur'];
                $tmp[6] = $value['rm_ibu'].'/'.$value['nama_ibu'];
                $tmp[7] = $value['status_skl'];
                $row[]  = $tmp;

                $no++;
            }
        } catch (Exception $e) {
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
                        'label'=>'Tanggal Pendaftaran',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No RM/Nama Bayi',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Lahir',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Ruangan/Kamar/TT',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No RM/Nama Ibu',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Status',
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

}
