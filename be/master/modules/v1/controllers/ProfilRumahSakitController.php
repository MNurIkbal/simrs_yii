<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoJwtHttpBearerAuth;
use app\modules\v1\models\ProfilRumahSakit;
use app\modules\v1\models\ProfilRumahSakitV;
use app\modules\v1\models\Lookup;
use yii\helpers\ArrayHelper;

class ProfilRumahSakitController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ProfilRumahSakit';

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
     * @todo Behaviors untuk skip authenticator di beberapa actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    // public function behaviors()
    // {
    //     $behaviors = parent::behaviors();

    //     $behaviors['authenticator'] = [
    //         'class' => DocoJwtHttpBearerAuth::className(),
    //         'except' => ['get-profil'],
    //     ];

    //     $behaviors['access'] = [
    //         'class' => DocoAccessRule::className(),
    //         'except' => ['get-profil'],
    //     ];

    //     return $behaviors;
    // }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        // try {

            $_GET['expand'] = $request->get('expand', 'propinsi_m, kabupaten_m, kecamatan_m, kelurahan_m');

            $model = new ProfilRumahSakitV;
            $query = $model::find();
            // $query->where(['profilrumahsakit_m.is_deleted' => false]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

        // } catch (\yii\db\Exception $e) {
        //     // Change status code
        //     \Yii::$app->response->statusCode = 500;


        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     // Change status code
        //     \Yii::$app->response->statusCode = 500;


        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    public function actionGetProfil($id = "1")
    {
        // Try catch
        try {
            // Define modelsimr
            $is_mobile = Yii::$app->jwt->is_mobile;
            $ProfilRumahSakit = ProfilRumahSakit::findOne(['profilrs_id' => $id]);
            if(!empty($ProfilRumahSakit)){
                $ProfilRumahSakit->logo_rumahsakit = '/media/img/profil-rs/' . $ProfilRumahSakit->logo_rumahsakit;
                if ($is_mobile) {
                    return $ProfilRumahSakit;
                } else {
                    return $result = [
                        'status' => 200,
                        'data' => $ProfilRumahSakit
                    ];
                }
            }else{
                if ($is_mobile) {
                    return $result = [
                        'status' => 200,
                        'data' => null
                    ];
                } else {
                    return $result = [
                        'status' => 500,
                        'data' => null
                    ];
                }
            }

        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    public function actionCreateProfile()
    {
        $post = \Yii::$app->request->post();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        // return $post;
        try {
            $ProfilRumahSakit = ProfilRumahSakit::findOne(['is_deleted' => true]);
            if (!empty($ProfilRumahSakit)) {
                $tgl_registrasi = date('Y-m-d', strtotime($post["tglregistrasi"]));
                $tgl_suratizin = date('Y-m-d', strtotime($post["tgl_suratizin"]));
                $masaberlaku_dari = date('Y-m-d', strtotime($post["masaberlaku_dari"]));
                $masaberlaku_sampai = date('Y-m-d', strtotime($post["masaberlaku_sampai"]));

                $inputProfilRs = array(
                    "nokode_rumahsakit" => $post["nokode_rumahsakit"],
                    "kelas_rumahsakit" => $post["kelas_rumahsakit"],
                    "tglregistrasi" => $tgl_registrasi,
                    "namadirektur_rumahsakit" => $post["namadirektur_rumahsakit"],
                    "nama_rumahsakit" => $post["nama_rumahsakit"],
                    "nama_penyelenggara" => $post["nama_penyelenggara"],
                    "jenis_rumahsakit" => $post["jenis_rumahsakit"],
                    "alamatlokasi_rumahsakit" => $post["alamatlokasi_rumahsakit"],
                    "no_telp_profilrs" => $post["no_telp_profilrs"],
                    "propinsi_id" => $post["propinsi_id"],
                    "no_faksimili" => $post["no_faksimili"],
                    "kabupaten_id" => $post["kabupaten_id"],
                    "email" => $post["email"],
                    "kecamatan_id" => $post["kecamatan_id"],
                    "notelphumas" => $post["notelphumas"],
                    "kelurahan_id" => $post["kelurahan_id"],
                    "website" => $post["website"],
                    "kode_pos" => $post["kode_pos"],
                    "luastanah" => $post["luastanah"],
                    "luasbangunan" => $post["luasbangunan"],
                    "nomor_suratizin" => $post["nomor_suratizin"],
                    "visi" => $post["visi"],
                    "tgl_suratizin" => $tgl_suratizin,
                    "misi" => $post["misi"],
                    "oleh_suratizin" => $post["oleh_suratizin"],
                    "sifat_suratizin" => $post["sifat_suratizin"],
                    "masaberlaku_dari" => $masaberlaku_dari,
                    "masaberlaku_sampai" => $masaberlaku_sampai,
                    "status_penyelenggara" => $post["status_penyelenggara"],
                    "logo_rumahsakit" => $post["logo_rumahsakit"],
                    "latitude" => $post["latitude"],
                    "longtitude" => $post["longtitude"],
                );
                $ProfilRumahSakit_m = new ProfilRumahSakit;
                $ProfilRumahSakit_m->attributes = $inputProfilRs;

                // return $inputProfilRs;
                if ($ProfilRumahSakit_m->save()) {
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Tersimpan'
                    ];
                } else {
                    $result['status'] = 500;
                    $result['title'] = 'Proses Gagal';
                    $result['text'] = $ProfilRumahSakit->getErrors();
                }

            } else {
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data profile tidak boleh lebih dari 1'
                ];
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateProfile($id){
        $post = \Yii::$app->request->post();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $ProfilRumahSakit = ProfilRumahSakit::findOne(['profilrs_id' => $id]);
            if (!empty($ProfilRumahSakit)) {
                $tgl_registrasi = isset($post["tglregistrasi"]) && !empty($post["tglregistrasi"]) ? date('Y-m-d', strtotime($post["tglregistrasi"])) : null;
                $tgl_suratizin = isset($post["tgl_suratizin"]) && !empty($post["tgl_suratizin"]) ?date('Y-m-d', strtotime($post["tgl_suratizin"])) : null;
                $masaberlaku_dari = isset($post["masaberlaku_dari"]) && !empty($post["masaberlaku_dari"]) ? date('Y-m-d', strtotime($post["masaberlaku_dari"])) : null;
                $masaberlaku_sampai = isset($post["masaberlaku_sampai"]) && !empty($post["masaberlaku_sampai"]) ? date('Y-m-d', strtotime($post["masaberlaku_sampai"])) : null;
                $inputProfilRs = array(
                        "nokode_rumahsakit" => ArrayHelper::getValue($post, 'nokode_rumahsakit'),
                        "kelas_rumahsakit" => ArrayHelper::getValue($post, 'kelas_rumahsakit'),
                        "tglregistrasi" => $tgl_registrasi,
                        "namadirektur_rumahsakit" => ArrayHelper::getValue($post, 'namadirektur_rumahsakit'),
                        "nama_rumahsakit" => ArrayHelper::getValue($post, 'nama_rumahsakit'),
                        "nama_penyelenggara" => ArrayHelper::getValue($post, 'nama_penyelenggara'),
                        "jenis_rumahsakit" => ArrayHelper::getValue($post, 'jenis_rumahsakit'),
                        "alamatlokasi_rumahsakit" => ArrayHelper::getValue($post, 'alamatlokasi_rumahsakit'),
                        "no_telp_profilrs" => ArrayHelper::getValue($post, 'no_telp_profilrs'),
                        "propinsi_id" => ArrayHelper::getValue($post, 'propinsi_id'),
                        "no_faksimili" => ArrayHelper::getValue($post, 'no_faksimili'),
                        "kabupaten_id" => ArrayHelper::getValue($post, 'kabupaten_id'),
                        "email" => ArrayHelper::getValue($post, 'email'),
                        "kecamatan_id" => ArrayHelper::getValue($post, 'kecamatan_id'),
                        "notelphumas" => ArrayHelper::getValue($post, 'notelphumas'),
                        "kelurahan_id" => ArrayHelper::getValue($post, 'kelurahan_id'),
                        "website" => ArrayHelper::getValue($post, 'website'),
                        "kode_pos" => ArrayHelper::getValue($post, 'kode_pos'),
                        "luastanah" => ArrayHelper::getValue($post, 'luastanah'),
                        "luasbangunan" => ArrayHelper::getValue($post, 'luasbangunan'),
                        "nomor_suratizin" => ArrayHelper::getValue($post, 'nomor_suratizin'),
                        "visi" => ArrayHelper::getValue($post, 'visi'),
                        "tgl_suratizin" => $tgl_suratizin,
                        "misi" => ArrayHelper::getValue($post, 'misi'),
                        "oleh_suratizin" => ArrayHelper::getValue($post, 'oleh_suratizin'),
                        "sifat_suratizin" => ArrayHelper::getValue($post, 'sifat_suratizin'),
                        "masaberlaku_dari" => $masaberlaku_dari,
                        "masaberlaku_sampai" => $masaberlaku_sampai,
                        "status_penyelenggara" => ArrayHelper::getValue($post, 'status_penyelenggara'),
                        // "logo_rumahsakit" => $post["logo_rumahsakit"],
                        "latitude" => ArrayHelper::getValue($post, 'latitude'),
                        "longtitude" => ArrayHelper::getValue($post, 'longtitude'),
                        "warna_header" => ArrayHelper::getValue($post, 'warna_header'),
                        "font_header" => ArrayHelper::getValue($post, 'font_header'),
                );

                if(isset($post['logo_rumahsakit'])){
                    if($post['logo_rumahsakit'] == 'setnull'){
                        $inputProfilRs['logo_rumahsakit'] = null;
                    }else{
                        $inputProfilRs['logo_rumahsakit'] = $post["logo_rumahsakit"];
                    }
                }
                if(isset($post['gambar_login'])){
                    if($post['gambar_login'] == 'setnull'){
                        $inputProfilRs['gambar_login'] = null;
                    }else{
                        $inputProfilRs['gambar_login'] = $post["gambar_login"];
                    }
                }
                if(isset($post['background_login'])){
                    if($post['background_login'] == 'setnull'){
                        $inputProfilRs['background_login'] = null;
                    }else{
                        $inputProfilRs['background_login'] = $post["background_login"];
                    }
                }
                if(isset($post['logo_header'])){
                    if($post['logo_header'] == 'setnull'){
                        $inputProfilRs['logo_header'] = null;
                    }else{
                        $inputProfilRs['logo_header'] = $post["logo_header"];
                    }
                }
                $ProfilRumahSakit->attributes = $inputProfilRs;
               
                if ($ProfilRumahSakit->save()) {
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Tersimpan',
                        'data-profil' => $ProfilRumahSakit->attributes
                    ];
                }else{
                    $result['status'] = 500;
                    $result['title'] = 'Proses Gagal';
                    $result['text'] = $ProfilRumahSakit->getErrors();
                }
                
            } else {
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data tidak ditemukan'
                ];
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}