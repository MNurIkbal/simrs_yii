<?php

namespace app\modules\v1\controllers;

use Yii;
use app\models\LoginForm;
use app\models\Loginpemakai;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use yii\rest\Controller;
use yii\db\Query;
use yii\web\Response;

class AuthController extends Controller
{
    public $serializer = [
        'class' => '\Doco\components\DocoSerializer',
        'collectionEnvelope' => 'data',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/xml'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["generate-access-token"] = ["POST"];
        return $verbs;
    }

    public function actionGetToken()
    {
        $user_id = null;
        $token = Null;
        $assigned_workspace = array();

        $signer = new Sha256();
        $model = new LoginForm();
        $post = Yii::$app->request->post();
        $loginPost["LoginForm"] = $post;
        $workspace = array();
        $active_workspace = array();

        if ($model->load($loginPost) && $model->login()) {
            $user = (new Query())
                ->select('
                    loginpemakai_k.loginpemakai_id, loginpemakai_k.additional_data, loginpemakai_k.akses_token,
                    pegawai_m.pegawai_id, pegawai_m.nama_pegawai,
                    ruangan_m.ruangan_id, ruangan_m.ruangan_nama,
                    instalasi_m.instalasi_id, instalasi_m.instalasi_nama,
                    modul_k.modul_id, modul_k.modul_nama
                ')
                ->from('loginpemakai_k')
                
                // Ruangan
                ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = loginpemakai_k.pegawai_id')
                ->leftJoin('ruanganpegawai_mp', 'ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id')
                ->leftJoin('ruangan_m', 'ruangan_m.ruangan_id = ruanganpegawai_mp.ruangan_id')
                ->leftJoin('instalasi_m', 'instalasi_m.instalasi_id = ruangan_m.instalasi_id')
                ->leftJoin('modulinstalasi_mp', 'modulinstalasi_mp.instalasi_id = instalasi_m.instalasi_id')

                // Aplikasi
                ->leftJoin('aksespengguna_k', 'aksespengguna_k.loginpemakai_id = loginpemakai_k.loginpemakai_id')

                // Ruangan & Aplikasi
                ->leftJoin('modul_k', 'modul_k.modul_id = aksespengguna_k.modul_id')
                // ->leftJoin('modul_k', 'modul_k.modul_id = aksespengguna_k.modul_id AND modul_k.modul_id = modulinstalasi_mp.modul_id')

                ->where(['nama_pemakai'=>$post["username"]])
                ->all();
            
            $ruangan = array();
            foreach($user as $u){
                $user_id = $u['loginpemakai_id'];
                $user_token = $u['additional_data'];
                // $ruangan[$u['instalasi_nama']][] = [$u['ruangan_id'] => $u['ruangan_nama']];
                $ruangan[$u['instalasi_id'].'##'.$u['instalasi_nama']][] = ['ruangan_id' => $u['ruangan_id'], 'ruangan_nama' => $u['ruangan_nama']];
            }

            foreach($ruangan as $key => $val){
                $instalasi = explode('##', $key);
                $instalasi_id = $instalasi[0];
                $instalasi_nama = $instalasi[1];
                $workspace[] = [
                    "instalasi_id" => $instalasi_id,
                    "instalasi_nama" => $instalasi_nama,
                    "ruangan" => $val
                ];
            }

            if(count($workspace)) {
                $active_workspace = $workspace[0];
                $active_ruangan = $active_workspace['ruangan'];
                $active_workspace['ruangan_id'] = $active_ruangan[0]['ruangan_id'];
                $active_workspace['ruangan_nama'] = $active_ruangan[0]['ruangan_nama'];
                unset($active_workspace['ruangan']);
            }

            $token = (New Builder())
                ->setHeader("alg", "HS256") // Configures the expiration time of the token (exp claim)
                ->setHeader("typ", "JWT") // Configures the expiration time of the token (exp claim)
                ->set('id', $user_id) // Configures a new claim, called "uid"
                ->set('access_token', $user_token) // Configures a new claim, called "uid"
                ->sign($signer, 'secret') // creates a signature using "testing" as key
                ->getToken(); // Retrieves the generated token

            ob_start();
            echo $token; // The string representation of the object is a JWT string (pretty easy, right?)
            $token = ob_get_clean();
        }

        return [
            "uid" => $user_id,
            "access_token" => $token,
            "workspace" => $workspace,
            "active_workspace" => $active_workspace
        ];
    }
}