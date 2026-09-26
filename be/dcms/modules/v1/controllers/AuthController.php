<?php

namespace app\modules\v1\controllers;

use Yii;
use app\models\LoginForm;
use app\models\LoginMobileForm;
use Doco\models\Loginpemakai;
use Doco\models\LoginMobile;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use yii\rest\Controller;
use yii\db\Query;
use yii\web\Response;
use Doco\models\Modul;
use Doco\models\KelompokMenu;
use Doco\models\KelompokMenuGroup;
use Doco\models\LupaPassword;

//extend docoConst
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoAes;
use Doco\components\DocoHtml;
use Doco\models\CpptView;
use Doco\models\Notifikasi;
use Doco\models\InfoKunjunganRiView;
use Doco\models\KonfigSystem;
use Doco\models\LookupTransaksi;
use Doco\models\ModulExternal;
use yii\helpers\ArrayHelper;

class AuthController extends Controller
{

    protected $_baseMenu;
    protected $_tmpMenu;
    protected $_parent = [];
    protected $_skipMenu = [];

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

    public function actionGatewayToken()
    {
        $post = Yii::$app->request->post();
        $token = Null;
        $signer = new Sha256();
        $model = new LoginForm();
        $loginPost["LoginForm"] = $post;
        $active_workspace = [];
        $rooms = $modul_pemakai = $core_menu = [];
        $user_identity = [];
        $allRooms = $allModul = [];

        if ($model->load($loginPost) && $model->login()) {
            $loginPemakai = Loginpemakai::find()->select([
                'loginpemakai_k.loginpemakai_id',
                'loginpemakai_k.pegawai_id',
                'loginpemakai_k.pasien_id',
                'loginpemakai_k.nama_pemakai',
                'loginpemakai_k.photouser',
                'loginpemakai_k.additional_data',
            ])->joinWith([
                'ruangPemakai' => function ($query) {
                    $query->select([
                        'ruanganpemakai_k.ruangan_id',
                        'ruanganpemakai_k.loginpemakai_id',
                    ])->joinWith([
                        'ruangan' => function ($query) {
                            $query->select([
                                'ruangan_m.ruangan_id',
                                'ruangan_m.instalasi_id',
                                'ruangan_m.ruangan_nama',
                                'ruangan_m.ruangan_namalainnya',
                                'ruangan_m.is_modul',
                            ])->orderBy([
                                'ruangan_m.ruangan_nama' => SORT_ASC,
                                'ruangan_m.ruangan_namalainnya' => SORT_ASC      
                                ]);
                        }
                    ]);
                },
                'aksesPengguna' => function ($query) {
                    $query->select([
                        'aksespengguna_k.aksespengguna_id',
                        'aksespengguna_k.peranpengguna_id',
                        'aksespengguna_k.loginpemakai_id',
                        'peranpengguna_k.peranpengguna_akses',
                        'peranpengguna_k.peranpengguna_menu',
                        'peranpengguna_k.is_exception',
                        'peranpengguna_k.modul_id',
                        'modul_k.modul_nama',
                        'modul_k.modul_namalainnya',
                        'modul_k.modul_fungsi',
                        'modul_k.url_modul',
                        'modul_k.icon_modul',
                        'modul_k.modul_key',
                        'modul_k.modul_urutan',
                        'modul_k.modul_kategori',
                        'modul_k.imagemodul',
                        'modul_k.is_active'
                    ])->joinWith([
                        'peranPengguna' => function ($query) {
                            $query->select([
                                'peranpengguna_k.modul_id',
                                'peranpengguna_k.peranpengguna_id',
                                'peranpengguna_k.peranpenggunanamalain',
                                'peranpengguna_k.is_exception',
                                'modul_k.modul_id'
                            ])->joinWith([
                                'modulInstalasi' => function ($query) {
                                    $query->select([
                                        'modulinstalasi_mp.modul_id',
                                        'modulinstalasi_mp.instalasi_id',
                                    ])->joinWith([
                                        'instalasi' => function ($query) {
                                            $query->select([
                                                'instalasi_m.instalasi_id',
                                                'instalasi_m.instalasi_nama',
                                                'instalasi_m.instalasi_namalainnya',
                                            ]);
                                        }
                                    ]);
                                },
                                'modul' => function ($query) {
                                    $query->select([
                                        'modul_k.modul_id'
                                    ]);
                                }
                            ]);
                        }
                    ]);
                },
                'pegawai' => function ($query) {
                    $query->select([
                        'pegawai_m.pegawai_id',
                        'pegawai_m.nomorindukpegawai',
                        'pegawai_m.gelardepan',
                        'pegawai_m.gelarbelakang',
                        'pegawai_m.nama_pegawai',
                        'pegawai_m.kelompokpegawai_id',
                        'pegawai_m.tanda_tangan',
                        'pegawai_m.spesialis_id',
                    ])->joinWith([
                        'kelompokPegawai' => function ($query) {
                            $query->select([
                                'kelompokpegawai_m.kelompokpegawai_id',
                                'kelompokpegawai_m.kelompokpegawai_nama',
                                'kelompokpegawai_m.kelompokpegawai_namalainnya',
                                'kelompokpegawai_m.kelompokpegawai_fungsi',
                                'kelompokpegawai_m.additional_data',
                            ]);
                        }
                    ]);
                },
            ])->where(['loginpemakai_k.loginpemakai_id' => $model->loginpemakai_id])->one();
            
            $user_identity = [
                'id' => $loginPemakai->loginpemakai_id,
                'loginpemakai_id' => $loginPemakai->loginpemakai_id,
                'id_pegawai' => $loginPemakai->pegawai_id,
                'id_pasien' => $loginPemakai->pasien_id,
                'nama' => $loginPemakai->nama_pemakai,
                'nama_pegawai' => !empty($loginPemakai->pegawai) 
                        ? $loginPemakai->pegawai->nama_pegawai : null,
                'photouser' => $loginPemakai->photouser,
                'kelompokpegawai_id' => !empty($loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_id) 
                                    ? $loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_id: 0,
                'kelompokpegawai_nama' => !empty($loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_nama) 
                                    ? $loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_nama: 0,
                'kelompokpegawai_namalainnya' => !empty($loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_namalainnya) 
                                    ? $loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_namalainnya: 0,
                'gelarbelakang' => !empty($loginPemakai->pegawai->gelarbelakang) ? $loginPemakai->pegawai->gelarbelakang : null,
                'spesialis_id' => !empty($loginPemakai->pegawai->spesialis_id) ? $loginPemakai->pegawai->spesialis_id : null,
            ]; 

            $rooms = $modul_pemakai = [];
            if (!empty($loginPemakai->ruangPemakai)) {
                foreach ($loginPemakai->ruangPemakai as $value) {
                    if (!empty($value->ruangan)) {
                        $allRooms[] = $value->ruangan->ruangan_id;
                        $rooms[$value->ruangan->instalasi_id][] = [
                            'id' => $value->ruangan->ruangan_id,
                            'name' => $value->ruangan->ruangan_nama,
                            // penambahan untuk kebutuhan skip pemilihan ruangan di fe (antrian) 
                            // ali.padilah@docotel.com
                            'is_modul' => $value->ruangan->is_modul
                        ];
                    }
                }
            }
            
            $baseOfMenu = KelompokMenuGroup::find()
                                ->select([
                                    'kelompokmenu_id',
                                    'encode(data::bytea, \'base64\') as data_new'
                                ])->asArray()->all();
            $kelompokMenu = KelompokMenu::find()->where(['is_active' => true])->orderBy(['urutan' => SORT_ASC])->asArray()->all();

            $dataChaceMenu = $dataChaceAksi = [];
            $menuPeranCache = [];
            // Untuk Menormalkan data chace menu
            // Set $this->_baseMenu
            foreach ($baseOfMenu as $val) {
                $data = unserialize(base64_decode($val['data_new']));
                $this->normalizationMenu($data);
            }
            $is_all_expertise_lab = false;

            $module_aktif = DocoConstants::VAR_EXCEPT_MODUL;
            $rolesAssign = [];
            if (!empty($loginPemakai->aksesPengguna)) {
                foreach ($loginPemakai->aksesPengguna as $value) {
                    if ($value->is_exception) {
                        $is_all_expertise_lab = true;
                    }
                    $module_aktif[] = $value->modul_id;

                    $aksesPengguna = !empty($value->peranpengguna_akses) 
                                        ? unserialize($value->peranpengguna_akses) 
                                        : [];
                    $aksesMenu = !empty($value->peranpengguna_menu) 
                                    ? unserialize($value->peranpengguna_menu) 
                                    : [];

                    $core_menu[$value->modul_id] = [
                        'module_id' => $value->modul_id,
                        'module_name' => $value->modul_nama,
                        'menu' => $this->generateAkses($aksesPengguna, $kelompokMenu),
                        'akses' => $this->aksesFront($aksesMenu)
                    ];

                    $url_modul = strtolower(trim($value->url_modul));
                    $url_modul = (substr($url_modul,0,1) == '/' ? substr($url_modul,1) : $url_modul);
                    $moduleSlug = str_replace(array(' ','-','/'),'',$url_modul);

                    $modul_pemakai[$value->modul_id] = [
                        'name' => $value->modul_nama,
                        'icon' => $value->icon_modul,
                        'moduleID' => $value->modul_id,
                        'url' => $value->url_modul,
                        'slug' => $moduleSlug,
                        'installation' => []
                    ];

                    $is_show = true;
                    if (!empty($value->peranPengguna->modul->modul_key) 
                        && $value->peranPengguna->modul->modul_key == 'antrian') {
                        $is_show = false;
                    }
                    $rolesAssign[] = $value->peranPengguna->peranpenggunanamalain;
                    if (!empty($value->peranPengguna->modulInstalasi)) {
                        foreach ($value->peranPengguna->modulInstalasi as $val) {
                            $modul_pemakai[$value->modul_id]['installation'][] = [
                                'id' => $val->instalasi->instalasi_id,
                                'name' => $val->instalasi->instalasi_nama,
                                'rooms' => isset($rooms[$val->instalasi->instalasi_id]) && $is_show
                                            ? $rooms[$val->instalasi->instalasi_id] 
                                            : []
                            ];
                        }
                    }
                }
            }
            $user_identity['roles'] = $rolesAssign;
            $allModul = Modul::find()->select([
                'modul_id',
                'modul_nama',
                'url_modul',
                'icon_modul',
                'modul_key',
            ])->where(['not in','modul_id',$module_aktif])->asArray()->all();
            $draftSoap = [
                'rd' => 0,
                'ri' => 0,
                'rj' => 0,
            ];
            if ($user_identity['kelompokpegawai_namalainnya'] == 't_medis') {
                $draftSoap = CpptView::unfinishedSoap($user_identity['id_pegawai'])
                    ->select([
                        new \yii\db\Expression("sum(CASE when tipe_pendaftaran='RD' THEN 1 ELSE 0 END) as rd"),
                        new \yii\db\Expression("sum(CASE when tipe_pendaftaran='RI' THEN 1 ELSE 0 END) as ri"),
                        new \yii\db\Expression("sum(CASE when tipe_pendaftaran='RJ' THEN 1 ELSE 0 END) as rj"),
                    ])
                    ->asArray()
                    ->one();
            }

            $draftRm = [
                'ri' => 0,
            ];
            if ($user_identity['kelompokpegawai_namalainnya'] == 't_medis') {
                $draftRm = InfoKunjunganRiView::unfinishedRm($user_identity['id_pegawai'])
                    ->select([
                        new \yii\db\Expression("count(pendaftaran_id) as ri"),
                    ])
                    ->asArray()
                    ->one();

            }

            $user_token = $loginPemakai->additional_data;
            $active_workspace = [
                'instalasi_id' => null,
                'instalasi_name' => null,
                'modul_id' => null,
                'modul_name' => null,
                'ruangan_id' => null,
                'ruangan_name' => null
            ];
            
            $contentlength = 32;
            if (function_exists('random_bytes'))
            {
                //PHP 7.0 and up
                $bytes = random_bytes($contentlength);
                $a = bin2hex($bytes);
            }
            else
            {
                //PHP 7.0 and below. no mcrypt extension for PHP 7.2 and up.
                $a = @mcrypt_create_iv($contentlength, MCRYPT_DEV_URANDOM);
            }
            $uniqueToken = hash('sha256', $a, false);

            $token = (New Builder())
                ->setHeader("alg", "HS256") // Configures the expiration time of the token (exp claim)
                ->setHeader("typ", "JWT") // Configures the expiration time of the token (exp claim)
                ->set('id', $user_identity['id']) // Configures a new claim, called "uid"
                ->set('access_token', $user_token) // Configures a new claim, called "uid"
                ->set('iss', Yii::$app->request->post('appid', 0))
                ->set('jti',$uniqueToken) //Configures unique token related to this jwt.
                ->set('is_mobile', 0) // Configures request is not from mobile device
                ->set('is_all_expertise_lab', $is_all_expertise_lab)
                ->set('signature_path', !empty($loginPemakai->pegawai) ? $loginPemakai->pegawai->tanda_tangan : '')
                ->sign($signer, 'secret') // creates a signature using "testing" as key
                ->getToken(); // Retrieves the generated token

            ob_start();
            echo $token; // The string representation of the object is a JWT string (pretty easy, right?)
            $token = ob_get_clean();
            // add notification bucket
            $allNotification = Notifikasi::generalUserNotification($user_identity['id']);
            $allNotification['user_id'] = $user_identity['id'];
        } else {
            return [
                'status' => 422,
                'data' => $model->errors
            ];
        }

        $accessData = [
            "uid" => isset($user_identity['id']) ? $user_identity['id'] : null,
            "access_token" => $token,
            "user_identity" => $user_identity,
            "workspace" => $modul_pemakai,
            "active_workspace" => $active_workspace,
            'menus' => $core_menu,
            "all_modul" => empty($allModul) ? [] : $allModul,
            "all_rooms" => $allRooms,
            "notifications" => $allNotification,
            "draftSoap" => $draftSoap,
            "draftRm" => $draftRm
        ];

        $aCache = Yii::$app->accessCache;
        
        $atDuration = (3600*24*30); //Match with fe session duration (ref: fe/models/LoginForm.php line 100)
        $key = ('access_'.$loginPemakai->loginpemakai_id);
        $aCache->set($key,$accessData,$atDuration);

        Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S, function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        }, $atDuration);

        Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S, function ($cache) {
            return LookupTransaksi::find()->asArray()->all();
        }, $atDuration);

        return ArrayHelper::filter($accessData, ['uid', 'access_token']);
    }

    public function actionGetToken()
    {
        $post = Yii::$app->request->post();

        if (isset($post['is_mobile']) && $post['is_mobile'] == true) {
            $return = $this->getMobileToken($post);
        } else {
            $return = $this->getWebToken($post);
        }

        return $return;
    }

    public function actionCekUser()
    {
        $request = Yii::$app->request;
        $model = new LoginForm();
        $loginPost["LoginForm"] = $request->post();

        if ($model->load($loginPost) && $model->validate()) {
            return 'success';
        } else {
            return [
                'status' => 422,
                'data' => $model->errors
            ];
        }
    }

    public function actionGetMobileToken()
    {
        $getBody = Yii::$app->request->getRawBody();
        $post = json_decode($getBody, true);
        if (isset($post['is_mobile']) && $post['is_mobile'] == true) {
            $return = $this->getMobileToken($post);
        } else {
            $return = $this->getWebToken($post);
        }

        return $return;
    }

    /**
    * @todo Fungsi untuk pendaftaran user baru
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionDaftar()
    {
        $post = Yii::$app->request->post();
        $model = new LoginMobile;

        if ($model->load($post, '')) {
            if ($model->validate()) {
                // $aes = new DocoAes();

                // $aes->data = $model->katakunci_pemakai;
                // $aes->key = DocoConstants::VAR_DAFTAR_KEY;
                // $aes->setMethode(128, 'CBC');

                // if ($aes->decrypt() == '') {
                //     return [
                //         'data' => ['katakunci_pemakai' => [Yii::t('app', 'Password belum terenkripsi.')]],
                //         'status' => 422
                //     ];
                // }

                // $model->katakunci_pemakai = $aes->decrypt();
                $nama_pemakai = preg_replace('/\s+/', '', $model->nama_pemakai);
                $model->nama_pemakai = $nama_pemakai;
                $model->katakunci_pemakai = DocoHelpers::aes128Decrypt(DocoConstants::VAR_DAFTAR_KEY, $model->katakunci_pemakai);
                $model->katakunci_pemakai = \Yii::$app->security->generatePasswordHash($model->katakunci_pemakai);

                if ($model->save()) {
                    $mailer = Yii::$app->mailer->compose('registrasi_berhasil', [
                        'nama_pemakai' => $model->nama_pemakai,
                        'email' => $model->email,
                    ])
                    ->setFrom('dev.docotel@gmail.com')
                    ->setSubject(Yii::t('app', 'Registrasi User Mobile BRIMOB.'))
                    ->setTo($post['email']);
                    
                    if ($mailer->send()) {
                        \Yii::$app->response->statusCode = 200;
                        return [
                            'status' => 200,
                            'message' => 'Data berhasil disimpan!'
                        ];
                    } else {
                        \Yii::$app->response->statusCode = 422;
                        return [
                            'status' => 422,
                            'message' => $this->parseErrors($mailer->errors, true),
                        ];
                    }
                } else {
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'status' => 422,
                        'message' => $this->parseErrors($model->errors, true),
                    ];
                }
            } else {
                \Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'message' => $this->parseErrors($model->errors, true),
                ];
            }
        }
    }

    /**
    * @todo Fungsi untuk ganti password ketika user lupa password
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionLupaPassword() {
        try {
            $post = Yii::$app->request->post();
            $modelUser = LoginMobile::find()->where(['email' => $post['email']])->one();

            if ($modelUser == '') {
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'User dengan email '.$post['email'].' belum terdaftar.'),
                ];
            }

            $modelLupaPassword = new LupaPassword;
            $modelLupaPassword->loginmobile_id = $modelUser->loginmobile_id;
            $modelLupaPassword->email = $modelUser->email;
            $modelLupaPassword->is_konfirmasi = false;

            if ($modelLupaPassword->save()) {
                $generated_password = mt_rand(100000, 999999);

                $mailer = Yii::$app->mailer->compose($post['type'], [
                    'id' => DocoHelpers::encrypt($modelLupaPassword->lupapass_id),
                    'nama_pemakai' => $modelUser->nama_pemakai,
                    'email' => $modelUser->email,
                    'generated_password' => $generated_password,
                ])
                ->setFrom('dev.docotel@gmail.com')
                ->setSubject(Yii::t('app', 'Konfirmasi Lupa Password.'))
                ->setTo($post['email']);
                
                if ($mailer->send()) {
                    \Yii::$app->response->statusCode = 200;
                    return [
                        'status' => 200,
                        'message' => Yii::t('app', 'Link verifikasi telah dikirim ke '.$post['email']),
                    ];
                } else {
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'status' => 422,
                        'message' => $this->parseErrors($mailer->errors, true),
                    ];
                }
            } else {
                \Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'message' => $this->parseErrors($modelLupaPassword->errors, true),
                ];
            }
        }
        catch (\Swift_TransportException $e) {
            return json_decode($e->getMessage(), true);
        }
    }

    /**
    * @todo Fungsi untuk ganti password ketika user lupa password
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionResetPassword($id = null, $pass = null) {
        try {
            $id = DocoHelpers::decrypt($id);
            $pass_baru = DocoHelpers::decrypt($pass);
            $model = LupaPassword::find()->where(['lupapass_id' => $id, 'is_konfirmasi' => false])->one();

            if ($model == '') {
                \Yii::$app->response->statusCode = 500;
                echo DocoHtml::templateReturnPendaftaranOnline(500,Yii::t('app', 'Data lupa password tidak ditemukan/sudah pernah di konfirmasi.'));exit;
                // return [
                //     'status' => 500,
                //     'message' => Yii::t('app', 'Data lupa password tidak ditemukan/sudah pernah di konfirmasi.'),
                // ];
            }

            $modelUser = LoginMobile::findOne($model->loginmobile_id);

            if ($modelUser == '') {
                \Yii::$app->response->statusCode = 500;
                echo DocoHtml::templateReturnPendaftaranOnline(500,Yii::t('app', 'Data user tidak ditemukan.'));exit;

                // return [
                //     'status' => 500,
                //     'message' => Yii::t('app', 'Data user tidak ditemukan.'),
                // ];
            }

            $modelUser->katakunci_pemakai = \Yii::$app->security->generatePasswordHash($pass_baru);

            if (!$modelUser->validate()) {
                \Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'message' => $this->parseErrors($modelUser->errors, true),
                ];
            }

            if ($modelUser->save()) {
                $model->pass_baru = \Yii::$app->security->generatePasswordHash($pass_baru);
                $model->is_konfirmasi = true;

                if ($model->save()) {
                    \Yii::$app->response->statusCode = 200;
                    echo DocoHtml::templateReturnPendaftaranOnline(200,Yii::t('app', 'Password Anda telah diubah, silakan login di mobile apps BRIMOB.'));exit;
                    // return [
                    //     'status' => 200,
                    //     'message' => Yii::t('app', 'Password Anda telah diubah, silakan login di mobile apps BRIMOB.'),
                    // ];
                }
            } else {
                \Yii::$app->response->statusCode = 422;

                echo DocoHtml::templateReturnPendaftaranOnline(422,$this->parseErrors($modelUser->errors, true));exit;
                // return [
                //     'status' => 422,
                //     'message' => $this->parseErrors($modelUser->errors, true),
                // ];
            }

            return Yii::t('app', 'Redirect ke aplikasi android');
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;

            echo DocoHtml::templateReturnPendaftaranOnline(500,$e->getMessage());exit;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;

            echo DocoHtml::templateReturnPendaftaranOnline(500,$e->getMessage());exit;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function aksesFront(array $data)
    {
        $aksesFrontEnd = [];
        foreach ($data as $key => $value) {
            if (!empty($this->_baseMenu[$key])) {
                $data = $this->_baseMenu[$key];
                if (!empty($data['menu_url'])) {
                    $newUrl = explode("/", $data['menu_url']);
                    $modul = '/' . (isset($newUrl[1]) ? $newUrl[1] : '');
                    if ($modul) {
                        $constroller = $modul .  (isset($newUrl[2]) ? '/' . $newUrl[2] : '');
                        $constroller = preg_replace('/[?](?<=\?).*/','',$constroller);
                        $aksesFrontEnd[$constroller] = $value;
                    } 
                }
            }
        }
        $aksesFrontEnd['/dcms/profile'] = [
            'create',
            'update'
        ];
        return $aksesFrontEnd;
    }


    private function getData($id = null)
    {
        $modul = Modul::find()
                        ->select([
                                'modul_k.modul_id',
                                'modul_k.modul_nama',
                                'modul_k.modul_namalainnya',
                                'modul_k.modul_fungsi',
                                'modul_k.url_modul',
                                'modul_k.icon_modul',
                                'modul_k.modul_key',
                                'modul_k.modul_urutan',
                                'modul_k.modul_kategori',
                                'modul_k.imagemodul',
                                'modul_k.is_active'
                        ]);
        if ($id) {
            $modul->where(['modul_id' => $id]);
        }

        return $modul;
    }

    public function generateAkses(array $data, array $kel_menu)
    {
        $menuPeranCache = $checkParent = [];
        $this->_parent = [];
        $this->_skipMenu = [];
        foreach ($data as $key => $value) {
            if (isset($this->_baseMenu[$key])) {
                $data_menu = $this->_baseMenu[$key];
                if (!empty($data_menu['parentmenu_id']) && !isset($checkParent[$data_menu['parentmenu_id']])) {
                    $data = $this->_baseMenu[$data_menu['parentmenu_id']];
                    $this->checkParent($data);
                    $checkParent[$data_menu['parentmenu_id']] = true;
                }
            }
        }
        // Refreseh _tmpMenu
        $this->_tmpMenu = [];
        $this->parsingMenus();
        foreach ($kel_menu as $key => $value) {
            $menuPeranCache[] = [
                'kelmenu_id' => $value['kelmenu_id'],
                'kelmenu_nama' => $value['kelmenu_nama'],
                'kelmenu_url' => $value['kelmenu_url'],
                'kelmenu_icon' => $value['kelmenu_icon'],
                'item' => isset($this->_tmpMenu[$value['kelmenu_id']]) 
                                ? $this->_tmpMenu[$value['kelmenu_id']] 
                                : []
            ];
        }

        return $menuPeranCache;
    }

    /**
    ** @var integer $id_parent
    * @return array|void
    **/
    private function parsingMenus($id_parent = 0)
    {
        if (isset($this->_parent[$id_parent])) {
            if (!$id_parent) {
                foreach ($this->_parent[$id_parent] as $value) {
                    $kelmenu_id = $value['kelmenu_id'];
                    if (!isset($this->_tmpMenu[$kelmenu_id])) {
                        $this->_tmpMenu[$kelmenu_id] = [
                            'kelompokmenu_id' => $kelmenu_id,
                            'data' => []
                        ];
                    }
                    $dataChild = $this->parsingMenus($value['menu_id']);
                    $value['item'] = [];
                    if ($dataChild) {
                        $value['item'] = $dataChild;
                    }
                    $this->_tmpMenu[$kelmenu_id]['data'][] = $value;
                }
            } else {
                $data_return = [];
                foreach ($this->_parent[$id_parent] as $value) {
                    $dataChild = $this->parsingMenus($value['menu_id']);
                    $value['item'] = [];

                    if ($dataChild) {
                        $value['item'] = $dataChild;
                    }

                    $data_return[] = $value;
                }

                return $data_return;
            }
        }

        return false;
    }

    /**
    * @var array $data
    * @return void
    **/
    private function checkParent($data)
    {
        if (empty($data['groupmenu_id']) || !isset($this->_skipMenu[$data['menu_id']])) {
            if (!empty($data['groupmenu_id'])) {
                $this->_parent[$data['groupmenu_id']][] = $data;
                $this->_skipMenu[$data['menu_id']] = true;
            } 
            if (!empty($this->_baseMenu[$data['parentmenu_id']])) {
                $data = $this->_baseMenu[$data['parentmenu_id']];
            }

            if (!empty($this->_baseMenu[$data['groupmenu_id']])) {
                $data = $this->_baseMenu[$data['groupmenu_id']];
            }

            if (empty($data['groupmenu_id']) && !isset($this->_parent[0][$data['menu_id']])) {
                $this->_parent[0][$data['menu_id']] = $data;
            } else {
                if (!empty($data['groupmenu_id']) && !isset($this->_skipMenu[$data['menu_id']])) {
                    $this->_parent[$data['groupmenu_id']][] = $data;
                }

                $this->_skipMenu[$data['menu_id']] = true;
                if (isset($this->_baseMenu[$data['groupmenu_id']])) {
                    $this->checkParent($this->_baseMenu[$data['groupmenu_id']]);
                }
            }
        }
    }

    /**
    * @var array $data 
    * @return array $this->_tmpMenu
    **/
    private function normalizationMenu(array $data, $no_action = true)
    {
        foreach ($data as $key => $value) {
            $value_item = $value_action = [];

            if (!empty($value['item'])) {
                $this->normalizationMenu($value['item'],$no_action);
                unset($value['item']);
            }

            if (!empty($value['action']) && $no_action) {
                $this->normalizationMenu($value['action'], $no_action);
                unset($value['action']);
            }
            $this->_baseMenu[$value['menu_id']] = $value;
        }

        return $this->_baseMenu;
    }

    /**
    * @todo Fungsi untuk mendapatkan token website
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function getWebToken(array $post)
    {
        $token = Null;
        $signer = new Sha256();
        $model = new LoginForm();
        $loginPost["LoginForm"] = $post;
        $active_workspace = [];
        $rooms = $modul_pemakai = $core_menu = [];
        $user_identity = [];
        $allRooms = $allModul = [];

        if ($model->load($loginPost) && $model->login()) {
            $loginPemakai = Loginpemakai::find()->select([
                'loginpemakai_k.loginpemakai_id',
                'loginpemakai_k.pegawai_id',
                'loginpemakai_k.pasien_id',
                'loginpemakai_k.nama_pemakai',
                'loginpemakai_k.photouser',
                'loginpemakai_k.additional_data',
            ])->joinWith([
                'ruangPemakai' => function ($query) {
                    $query->select([
                        'ruanganpemakai_k.ruangan_id',
                        'ruanganpemakai_k.loginpemakai_id',
                    ])->joinWith([
                        'ruangan' => function ($query) {
                            $query->select([
                                'ruangan_m.ruangan_id',
                                'ruangan_m.instalasi_id',
                                'ruangan_m.ruangan_nama',
                                'ruangan_m.ruangan_namalainnya',
                                'ruangan_m.is_modul',
                            ])->orderBy([
                                'ruangan_m.ruangan_nama' => SORT_ASC,
                                'ruangan_m.ruangan_namalainnya' => SORT_ASC      
                                ]);
                        }
                    ]);
                },
                'aksesPengguna' => function ($query) {
                    $query->select([
                        'aksespengguna_k.aksespengguna_id',
                        'aksespengguna_k.peranpengguna_id',
                        'aksespengguna_k.loginpemakai_id',
                        'peranpengguna_k.peranpengguna_akses',
                        'peranpengguna_k.peranpengguna_menu',
                        'peranpengguna_k.is_exception',
                        'peranpengguna_k.modul_id',
                        'modul_k.modul_nama',
                        'modul_k.modul_namalainnya',
                        'modul_k.modul_fungsi',
                        'modul_k.url_modul',
                        'modul_k.icon_modul',
                        'modul_k.modul_key',
                        'modul_k.modul_urutan',
                        'modul_k.modul_kategori',
                        'modul_k.imagemodul',
                        'modul_k.url_page',
                        'modul_k.is_active',
                        'modul_k.is_external_link',
                        'modul_k.open_newtab',
                    ])->joinWith([
                        'peranPengguna' => function ($query) {
                            $query->select([
                                'peranpengguna_k.modul_id',
                                'peranpengguna_k.peranpengguna_id',
                                'peranpengguna_k.peranpenggunanamalain',
                                'peranpengguna_k.is_exception',
                                'modul_k.modul_id'
                            ])->joinWith([
                                'modulInstalasi' => function ($query) {
                                    $query->select([
                                        'modulinstalasi_mp.modul_id',
                                        'modulinstalasi_mp.instalasi_id',
                                    ])->joinWith([
                                        'instalasi' => function ($query) {
                                            $query->select([
                                                'instalasi_m.instalasi_id',
                                                'instalasi_m.instalasi_nama',
                                                'instalasi_m.instalasi_namalainnya',
                                            ]);
                                        }
                                    ]);
                                },
                                'modul' => function ($query) {
                                    $query->select([
                                        'modul_k.modul_id'
                                    ]);
                                }
                            ]);
                        }
                    ]);
                },
                'pegawai' => function ($query) {
                    $query->select([
                        'pegawai_m.pegawai_id',
                        'pegawai_m.nomorindukpegawai',
                        'pegawai_m.gelardepan',
                        'pegawai_m.gelarbelakang',
                        'pegawai_m.nama_pegawai',
                        'pegawai_m.kelompokpegawai_id',
                        'pegawai_m.tanda_tangan',
                        'pegawai_m.spesialis_id',
                    ])->joinWith([
                        'kelompokPegawai' => function ($query) {
                            $query->select([
                                'kelompokpegawai_m.kelompokpegawai_id',
                                'kelompokpegawai_m.kelompokpegawai_nama',
                                'kelompokpegawai_m.kelompokpegawai_namalainnya',
                                'kelompokpegawai_m.kelompokpegawai_fungsi',
                                'kelompokpegawai_m.additional_data',
                            ]);
                        }
                    ]);
                },
            ])->where(['loginpemakai_k.loginpemakai_id' => $model->loginpemakai_id])->one();
            
            $user_identity = [
                'id' => $loginPemakai->loginpemakai_id,
                'loginpemakai_id' => $loginPemakai->loginpemakai_id,
                'id_pegawai' => $loginPemakai->pegawai_id,
                'id_pasien' => $loginPemakai->pasien_id,
                'nama' => $loginPemakai->nama_pemakai,
                'nama_pegawai' => !empty($loginPemakai->pegawai) 
                        ? $loginPemakai->pegawai->nama_pegawai : null,
                'photouser' => $loginPemakai->photouser,
                'kelompokpegawai_id' => !empty($loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_id) 
                                    ? $loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_id: 0,
                'kelompokpegawai_nama' => !empty($loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_nama) 
                                    ? $loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_nama: 0,
                'kelompokpegawai_namalainnya' => !empty($loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_namalainnya) 
                                    ? $loginPemakai->pegawai->kelompokPegawai->kelompokpegawai_namalainnya: 0,
                'gelarbelakang' => !empty($loginPemakai->pegawai->gelarbelakang) ? $loginPemakai->pegawai->gelarbelakang : null,
                'spesialis_id' => !empty($loginPemakai->pegawai->spesialis_id) ? $loginPemakai->pegawai->spesialis_id : null,
            ]; 

            $rooms = $modul_pemakai = [];
            if (!empty($loginPemakai->ruangPemakai)) {
                foreach ($loginPemakai->ruangPemakai as $value) {
                    if (!empty($value->ruangan)) {
                        $allRooms[] = $value->ruangan->ruangan_id;
                        $rooms[$value->ruangan->instalasi_id][] = [
                            'id' => $value->ruangan->ruangan_id,
                            'name' => $value->ruangan->ruangan_nama,
                            // penambahan untuk kebutuhan skip pemilihan ruangan di fe (antrian) 
                            // ali.padilah@docotel.com
                            'is_modul' => $value->ruangan->is_modul
                        ];
                    }
                }
            }
            
            $baseOfMenu = KelompokMenuGroup::find()
                                ->select([
                                    'kelompokmenu_id',
                                    'encode(data::bytea, \'base64\') as data_new'
                                ])->asArray()->all();
            $kelompokMenu = KelompokMenu::find()->where(['is_active' => true])->orderBy(['urutan' => SORT_ASC])->asArray()->all();

            $dataChaceMenu = $dataChaceAksi = [];
            $menuPeranCache = [];
            // Untuk Menormalkan data chace menu
            // Set $this->_baseMenu
            foreach ($baseOfMenu as $val) {
                $data = unserialize(base64_decode($val['data_new']));
                $this->normalizationMenu($data);
            }
            $is_all_expertise_lab = false;

            $module_aktif = DocoConstants::VAR_EXCEPT_MODUL;
            $rolesAssign = [];
            if (!empty($loginPemakai->aksesPengguna)) {
                foreach ($loginPemakai->aksesPengguna as $value) {
                    if ($value->is_exception) {
                        $is_all_expertise_lab = true;
                    }
                    $module_aktif[] = $value->modul_id;

                    $aksesPengguna = !empty($value->peranpengguna_akses) 
                                        ? unserialize($value->peranpengguna_akses) 
                                        : [];
                    $aksesMenu = !empty($value->peranpengguna_menu) 
                                    ? unserialize($value->peranpengguna_menu) 
                                    : [];

                    $core_menu[$value->modul_id] = [
                        'module_id' => $value->modul_id,
                        'module_name' => $value->modul_nama,
                        'menu' => $this->generateAkses($aksesPengguna, $kelompokMenu),
                        'akses' => $this->aksesFront($aksesMenu)
                    ];

                    $url_modul = strtolower(trim($value->url_modul));
                    $url_modul = (substr($url_modul,0,1) == '/' ? substr($url_modul,1) : $url_modul);
                    $moduleSlug = str_replace(array(' ','-','/'),'',$url_modul);

                    $modul_pemakai[$value->modul_id] = [
                        'name' => $value->modul_nama,
                        'icon' => $value->icon_modul,
                        'moduleID' => $value->modul_id,
                        'url' => $value->url_modul,
                        'page' => $value->url_page,
                        'slug' => $moduleSlug,
                        'is_external_link' => $value->is_external_link,
                        'open_newtab' => $value->open_newtab,
                        'installation' => []
                    ];

                    $is_show = true;
                    if (!empty($value->peranPengguna->modul->modul_key) 
                        && $value->peranPengguna->modul->modul_key == 'antrian') {
                        $is_show = false;
                    }
                    $rolesAssign[] = $value->peranPengguna->peranpenggunanamalain;
                    if (!empty($value->peranPengguna->modulInstalasi)) {
                        foreach ($value->peranPengguna->modulInstalasi as $val) {
                            $modul_pemakai[$value->modul_id]['installation'][] = [
                                'id' => $val->instalasi->instalasi_id,
                                'name' => $val->instalasi->instalasi_nama,
                                'rooms' => isset($rooms[$val->instalasi->instalasi_id]) && $is_show
                                            ? $rooms[$val->instalasi->instalasi_id] 
                                            : []
                            ];
                        }
                    }
                }
            }

            $user_identity['roles'] = $rolesAssign;
            $allModul = Modul::find()->select([
                'modul_id',
                'modul_nama',
                'url_modul',
                'icon_modul',
                'modul_key',
            ])->where(['not in','modul_id',$module_aktif])->asArray()->all();
            $draftSoap = [
                'rd' => 0,
                'ri' => 0,
                'rj' => 0,
            ];
            if ($user_identity['kelompokpegawai_namalainnya'] == 't_medis') {
                $draftSoap = CpptView::unfinishedSoap($user_identity['id_pegawai'])
                    ->select([
                        new \yii\db\Expression("sum(CASE when tipe_pendaftaran='RD' THEN 1 ELSE 0 END) as rd"),
                        new \yii\db\Expression("sum(CASE when tipe_pendaftaran='RI' THEN 1 ELSE 0 END) as ri"),
                        new \yii\db\Expression("sum(CASE when tipe_pendaftaran='RJ' THEN 1 ELSE 0 END) as rj"),
                    ])
                    ->asArray()
                    ->one();
            }

            $draftRm = [
                'ri' => 0,
            ];
            if ($user_identity['kelompokpegawai_namalainnya'] == 't_medis') {
                $draftRm = InfoKunjunganRiView::unfinishedRm($user_identity['id_pegawai'])
                    ->select([
                        new \yii\db\Expression("count(pendaftaran_id) as ri"),
                    ])
                    ->asArray()
                    ->one();

            }

            $user_token = $loginPemakai->additional_data;
            $active_workspace = [
                'instalasi_id' => null,
                'instalasi_name' => null,
                'modul_id' => null,
                'modul_name' => null,
                'ruangan_id' => null,
                'ruangan_name' => null
            ];
            
            $contentlength = 32;
            if (function_exists('random_bytes'))
            {
                //PHP 7.0 and up
                $bytes = random_bytes($contentlength);
                $a = bin2hex($bytes);
            }
            else
            {
                //PHP 7.0 and below. no mcrypt extension for PHP 7.2 and up.
                $a = @mcrypt_create_iv($contentlength, MCRYPT_DEV_URANDOM);
            }
            $uniqueToken = hash('sha256', $a, false);

            $buildJwt = (New Builder())
                ->setHeader("alg", "HS256") // Configures the expiration time of the token (exp claim)
                ->setHeader("typ", "JWT") // Configures the expiration time of the token (exp claim)
                ->set('id', $user_identity['id']) // Configures a new claim, called "uid"
                ->set('access_token', $user_token) // Configures a new claim, called "uid"
                ->set('jti',$uniqueToken) //Configures unique token related to this jwt.
                ->set('is_mobile', 0) // Configures request is not from mobile device
                ->set('is_all_expertise_lab', $is_all_expertise_lab)
                ->set('signature_path', !empty($loginPemakai->pegawai) ? $loginPemakai->pegawai->tanda_tangan : '');
                
            if(!empty(ArrayHelper::getValue($post, 'set_expire_time'))) {
                $konfig = KonfigSystem::find()->asArray()->one();
                if(isset($konfig['expired_time']) && !empty($konfig['expired_time'])) {
                    $timeExp = $konfig['expired_time'];
                } else {
                    $timeExp = '60';
                }
                $newTime = strtotime(date("Y-m-d H:i:s")." +{$timeExp} minutes");
                $buildJwt->setExpiration($newTime);
            }

            $token = $buildJwt->sign($signer, 'secret')->getToken();

            ob_start();
            echo $token; // The string representation of the object is a JWT string (pretty easy, right?)
            $token = ob_get_clean();
            // add notification bucket
            $allNotification = Notifikasi::generalUserNotification($user_identity['id']);
            $allNotification['user_id'] = $user_identity['id'];
        } else {
            return [
                'status' => 422,
                'data' => $model->errors
            ];
        }

        $accessData = [
            "uid" => isset($user_identity['id']) ? $user_identity['id'] : null,
            "access_token" => $token,
            "user_identity" => $user_identity,
            "workspace" => $modul_pemakai,
            "active_workspace" => $active_workspace,
            'menus' => $core_menu,
            "all_modul" => empty($allModul) ? [] : $allModul,
            "all_rooms" => $allRooms,
            "notifications" => $allNotification,
            "draftSoap" => $draftSoap,
            "draftRm" => $draftRm
        ];

        $aCache = Yii::$app->accessCache;
        
        $atDuration = (3600*24*30); //Match with fe session duration (ref: fe/models/LoginForm.php line 100)
        $key = ('access_'.$loginPemakai->loginpemakai_id);
        $aCache->set($key,$accessData,$atDuration);

        Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S, function ($cache) {
            return KonfigSystem::find()->asArray()->one();
        }, $atDuration);

        Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S, function ($cache) {
            return LookupTransaksi::find()->asArray()->all();
        }, $atDuration);

        return $accessData;
    }

    public function actionCheckAuthorization()
    {
        $post = Yii::$app->request->post();
        $modul_id = Yii::$app->request->post('modul_id');
        $model = new LoginForm();
        $loginPost["LoginForm"] = $post;
        $active_workspace = [];
        $rooms = $modul_pemakai = $core_menu = [];
        $user_identity = [];
        $allRooms = $allModul = [];

        if ($model->load($loginPost) && $model->login()) {
            $loginPemakai = Loginpemakai::find()->select([
                'loginpemakai_k.loginpemakai_id',
                'loginpemakai_k.pegawai_id',
                'loginpemakai_k.pasien_id',
                'loginpemakai_k.nama_pemakai',
                'loginpemakai_k.photouser',
                'loginpemakai_k.additional_data',
            ])->joinWith([
                'aksesPengguna' => function ($query) use ($modul_id) {
                    $query->select([
                        'aksespengguna_k.aksespengguna_id',
                        'aksespengguna_k.peranpengguna_id',
                        'aksespengguna_k.loginpemakai_id',
                        'peranpengguna_k.peranpengguna_akses',
                        'peranpengguna_k.peranpengguna_menu',
                        'peranpengguna_k.modul_id',
                        'modul_k.modul_nama',
                        'modul_k.modul_namalainnya',
                        'modul_k.modul_fungsi',
                        'modul_k.url_modul',
                        'modul_k.icon_modul',
                        'modul_k.modul_key',
                        'modul_k.modul_urutan',
                        'modul_k.modul_kategori',
                        'modul_k.imagemodul',
                        'modul_k.is_active'
                    ])->joinWith([
                        'peranPengguna' => function ($query) {
                            $query->select([
                                'peranpengguna_k.modul_id',
                                'peranpengguna_k.peranpengguna_id',
                                'modul_k.modul_id'
                            ])->joinWith([
                                'modulInstalasi' => function ($query) {
                                    $query->select([
                                        'modulinstalasi_mp.modul_id',
                                        'modulinstalasi_mp.instalasi_id',
                                    ])->joinWith([
                                        'instalasi' => function ($query) {
                                            $query->select([
                                                'instalasi_m.instalasi_id',
                                                'instalasi_m.instalasi_nama',
                                                'instalasi_m.instalasi_namalainnya',
                                            ]);
                                        }
                                    ]);
                                },
                                'modul' => function ($query) {
                                    $query->select([
                                        'modul_k.modul_id'
                                    ]);
                                }
                            ]);
                        }
                    ])->andWhere([
                        'peranpengguna_k.modul_id' => $modul_id,
                    ]);
                },
            ])->where([
                'loginpemakai_k.loginpemakai_id' => $model->loginpemakai_id,
            ])->one();
            
            $baseOfMenu = KelompokMenuGroup::find()
                                ->select([
                                    'kelompokmenu_id',
                                    'encode(data::bytea, \'base64\') as data_new'
                                ])->asArray()->all();
            $kelompokMenu = KelompokMenu::find()->where(['is_active' => true])->asArray()->all();

            $dataChaceMenu = $dataChaceAksi = [];
            $menuPeranCache = [];
            // Untuk Menormalkan data chace menu
            // Set $this->_baseMenu
            foreach ($baseOfMenu as $val) {
                $data = unserialize(base64_decode($val['data_new']));
                $this->normalizationMenu($data);
            }

            $module_aktif = DocoConstants::VAR_EXCEPT_MODUL;
            
            if (!empty($loginPemakai->aksesPengguna)) {
                foreach ($loginPemakai->aksesPengguna as $value) {
                    $module_aktif[] = $value->modul_id;

                    $aksesPengguna = !empty($value->peranpengguna_akses) 
                                        ? unserialize($value->peranpengguna_akses) 
                                        : [];
                    $aksesMenu = !empty($value->peranpengguna_menu) 
                                    ? unserialize($value->peranpengguna_menu) 
                                    : [];

                    $core_menu[$value->modul_id] = [
                        'module_id' => $value->modul_id,
                        'module_name' => $value->modul_nama,
                        'menu' => $this->generateAkses($aksesPengguna, $kelompokMenu),
                        'akses' => $this->aksesFront($aksesMenu)
                    ];
                }
            }

        } else {
            return [
                'status' => 422,
                'data' => $model->errors
            ];
        }

        return [
            "uid" => !empty($loginPemakai->loginpemakai_id) ? $loginPemakai->loginpemakai_id : null,
            "user_identity" => $user_identity,
            'menus' => $core_menu,
        ];
    }

    /**
    * @todo Fungsi untuk mendapatkan token mobile
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    private function getMobileToken(array $post)
    {
        $model = new LoginMobileForm();
        $signer = new Sha256();
        $user_identity = array();
        $token = Null;
        $loginPost["LoginMobileForm"] = $post;

        if ($model->load($loginPost) && $model->login()) {
            $loginMobile = LoginMobile::find()->select([
                'loginmobile_k.loginmobile_id',
                // 'loginmobile_k.nama_pemakai',
                // 'loginmobile_k.katakunci_pemakai',
                // 'loginmobile_k.email',
                // 'loginmobile_k.statuslogin',
            ])->where(['loginmobile_k.loginmobile_id' => $model->loginmobile_id])->one();
            // set login player
            // $this->setLoginPlayer($model->loginmobile_id, $post);
            $user_identity = [
                'id' => $loginMobile->loginmobile_id,
                // 'nama' => $loginMobile->nama_pemakai,
                // 'email' => $loginMobile->email,
            ];

            $user_token = $loginMobile->akses_token;

            $token = (New Builder())
                ->setHeader("alg", "HS256") // Configures the expiration time of the token (exp claim)
                ->setHeader("typ", "JWT") // Configures the expiration time of the token (exp claim)
                ->set('id', $user_identity['id']) // Configures a new claim, called "uid"
                ->set('access_token', $user_token) // Configures a new claim, called "uid"
                ->set('is_mobile', 1) // Configures a new claim, called "uid"
                ->sign($signer, 'secret') // creates a signature using "testing" as key
                ->getToken(); // Retrieves the generated token

            ob_start();
            echo $token; // The string representation of the object is a JWT string (pretty easy, right?)
            $token = ob_get_clean();
        } else {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $this->parseErrors($model->errors, true)
            ];
        }

        return [
            "uid" => isset($user_identity['id']) ? $user_identity['id'] : null,
            "access_token" => $token,
            "user_identity" => $user_identity,
        ];
    }

    /**
    * @todo Fungsi untuk mendapatkan aes daftar
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionGetAesDaftar()
    {
        // $aes = new DocoAes();
        $password = Yii::$app->request->post('password');
        $type = Yii::$app->request->post('type');
        // $aes->data = $password;
        // $aes->key = DocoConstants::VAR_DAFTAR_KEY;
        // $aes->setMethode(128, 'CBC');

        if ($type == 1) {
            return DocoHelpers::aes128Encrypt(DocoConstants::VAR_DAFTAR_KEY, $password);
            // return $aes->encrypt();
        } else {
            return DocoHelpers::aes128Decrypt(DocoConstants::VAR_DAFTAR_KEY, $password);
            // return $aes->decrypt();
        }
    }

    /**
    * @todo Fungsi untuk mendapatkan aes login
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionGetAesLogin()
    {
        // $aes = new DocoAes();
        $password = Yii::$app->request->post('password');
        $type = Yii::$app->request->post('type');
        // $aes->data = $password;
        // $aes->key = DocoConstants::VAR_LOGIN_KEY;
        // $aes->setMethode(128, 'CBC');

        if ($type == 1) {
            return DocoHelpers::aes128Encrypt(DocoConstants::VAR_LOGIN_KEY, $password);
            // return $aes->encrypt();
        } else {
            return DocoHelpers::aes128Decrypt(DocoConstants::VAR_LOGIN_KEY, $password);
            // return $aes->decrypt();
        }
    }

    /**
     * @todo Action untuk mengubah error model yii menjadi array of errors
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function parseErrors($errors = [], $one_response = false)
    {
        try {
            $new_errors = [];

            if (!empty($errors)) {
                foreach ($errors as $key => $value) {
                    $new_errors[] = isset($value[0]) ? $value[0] : '-';
                }
            }

            if ($one_response) {
                $new_errors = isset($new_errors[0]) ? $new_errors[0] : [];
            }

            return $new_errors;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @author Randy Vianda Putra
    * @todo set status login & player_id
    * @param integer loginmobile_id
    * @param array data post
    */
    private function setLoginPlayer($loginmobile_id, array $post)
    {
        $model = LoginMobile::findOne($loginmobile_id);
        $list_player = $model->player_id;
        $data_player = !empty($list_player)
            ? json_decode($list_player)
            : [];
        if (!in_array($post['player_id'], $data_player)) {
            array_push($data_player, $post['player_id']);
        }
        $players = json_encode($data_player);
        $model->statuslogin = true;
        $model->player_id = $players;
        if ($model->save()) {
            return [
                'status' => 200,
                'message' => 'Set login player berhasil'
            ];
        } else {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $this->parseErrors($model->errors, true)
            ];
        }
    }

    /**
    * @author Randy Vianda Putra
    * @todo set status login & player_id
    * @param integer loginmobile_id
    * @param integer player_id
    */
    public function actionLogout()
    {
        $post = Yii::$app->request->post();
        $loginmobile_id = $post['loginmobile_id'];
        $model = LoginMobile::findOne($loginmobile_id);
        if (!empty($post['player_id']) && !empty($model)) {
            $list_players = json_decode($model->player_id, true);
            $player_id = $post['player_id'];
            if (!empty($list_players) && !empty($list_players)) {
                $key = array_search($player_id, $list_players);
                unset($list_players[$key]);
            }
        }
        $players = [];
        if (count($list_players) >= 1) {
            for ($i=0; $i < count($list_players) + 1; $i++) { 
                if (isset($list_players[$i])) {
                    array_push($players, $list_players[$i]);
                }
            }
        }
        $players = json_encode($players, true);
        $flag_status = (count($list_players) < 1) ? false : true;
        $model->statuslogin = $flag_status;
        $model->player_id = $players;
        if ($model->save()) {
            return [
                'status' => 200,
                'message' => 'Logout berhasil'
            ];
        } else {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $this->parseErrors($model->errors, true)
            ];
        }
    }

    public function actionChangePassword()
    {
        $post = Yii::$app->request->post();
        $loginmobile_id = $post['loginmobile_id'];
        $last_password = $post['password_lama'];
        $new_password = $post['password_baru'];
        $model = LoginMobile::findOne($loginmobile_id);
        $last_password_decrypt = DocoHelpers::aes128Decrypt(DocoConstants::VAR_LOGIN_KEY, $last_password);
        $last_encrypt_hash  = \Yii::$app->security->generatePasswordHash($last_password_decrypt);
        $validate_last_password = \Yii::$app->security->validatePassword($last_password_decrypt, $model->katakunci_pemakai);
        if (!$validate_last_password) {
            \Yii::$app->response->statusCode = 422;
            return [
                'status' => 422,
                'message' => 'Password lama tidak sesuai',
            ];
        } else {
            $new_password_decrypt = DocoHelpers::aes128Decrypt(DocoConstants::VAR_LOGIN_KEY, $new_password);
            $model->katakunci_pemakai = \Yii::$app->security->generatePasswordHash($new_password_decrypt);
            if ($model->save()) {
                return [
                    'status' => 200,
                    'message' => 'Ubah password berhasil'
                ];
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'message' => $this->parseErrors($model->errors, true)
                ];
            }
        }
    }

    /**
     * @todo example privent handling error mobile
     * @author ali.padilah@docotel.com
     */
    public function actionTestMobile()
    {
        try {
            $data = DocoExample::getNone();

            return $data;
        } catch (\yii\db\Exception $e) {
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\base\Exception $e) {
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}