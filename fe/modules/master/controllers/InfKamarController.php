<?php 

/**
 * @author Iqbal Qurahman
 * @todo Dashboard Kamar
 * @copyright 03 September 2019
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Doco\master\models\KamarForm;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class InfKamarController extends DocoController
{

    protected $_title = "Dashboard Kamar";
    protected $_module = '/master/inf-kamar';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }

    /*dashboar*/
    public function actionDashboard(){
        $namaRS = Yii::$app->docoVars->identity("nama_rumahsakit");
        $requests = Yii::$app->request;
        $post = $requests->post();
        
        if ($requests->post()) {
            $kelas_pelayanan = DocoHelpers::decrypt($post['kelas_pelayanan']);
            $ruangan = DocoHelpers::decrypt($post['ruangan']);
            $request = $this->_restMaster->post('allow/get-dashboard-kamar-ranap', [
                        'form_params' => ['kelas_pelayanan'=> $kelas_pelayanan,
                                        'ruangan'=>$ruangan
                        ]
                    ]);
            $body = json_decode($request->getBody(), true);
            $metadata = $body['metadata'];
            $response = $body['response'];
            
        } else {
            $request = $this->_restMaster->post('allow/get-dashboard-kamar-ranap', [
                            'form_params' => ['kelas_pelayanan'=> 0,
                                        'ruangan'=>0]
                        ]);
            $body = json_decode($request->getBody(), true);
            $metadata = $body['metadata'];
            $response = $body['response'];            
        }

        $getTempatTidur = $response['warna_tempat_tidur'];
        $getKelasPelayanan = $response['kelas_pelayanan'];
        $getRuanganFilter = $response['ruangan_filter'];
        $getKelasPelayananHeader = $response['kelas_pelayanan_header'];
        $getKelasPelayananFilter = $response['kelas_filter'];
        $getDasboarKamar = $response['dashboard_kamar'];
        
        // return json_encode($getDasboarKamar);
        // echo "<pre>";var_dump($getDasboarKamar);die();

        $imgQueue = Url::to('@web/media/img/icon-antrian/queue.png');
        $imgDisplay = Url::to('@web/media/img/icon-antrian/display-icon.png');
        $imgLoading = Url::to('@web/media/img/icon-app/image-loading.gif');

        $list_jenis_kelas_first = [
                'name_header' => '<br><b>'.Yii::t('fe', 'Kelas Pelayanan').'</b><br>'.Yii::t('fe', 'Semua Kelas'),
                'name' => Yii::t('fe', 'Semua Kelas'),
                'icon' => '<img src="'.$imgDisplay.'" >',
                'url' => Url::to([$this->_module .'/kelas-pelayanan','jenis_id' => 'all-kelas' ]),
            ];
        $ls_display = [];
        foreach ($getKelasPelayanan as $key => $value) {
            $keyJenisAntrian = DocoHelpers::encrypt($value['kelaspelayanan_id']);
            $list_jenis_kelas[] = [
                'name_header' => '<br><b>'.Yii::t('fe', 'Kelas Pelayanan').'</b><br>'.Yii::t('fe', $value['kelaspelayanan_nama']),
                'name' => Yii::t('fe', $value['kelaspelayanan_nama']),
                'icon' => '<img src="'.$imgDisplay.'" >',
                'url' => Url::to([$this->_module .'/kelas-pelayanan','jenis_id' => $keyJenisAntrian ]),
            ];
            $ls_display[$value['kelaspelayanan_id']] = $value['kelaspelayanan_nama'];

        }
        
        array_push($list_jenis_kelas, $list_jenis_kelas_first);
        $count = 0;
        $idx = 0;
        $newList = [];
        foreach ($list_jenis_kelas as $key => $value) {
            $newList[$idx][] = $value;
            if ($count == 2) {
                $count = -1;
                $idx++;
            }
            $count++;
        }
        $list_jenis_kelas = $newList;
        
        $filterRuangan = [];
        foreach ($getRuanganFilter as $k => $v) {
            $idk = DocoHelpers::encrypt($k);
            $filterRuangan[$idk] = $v;
        }

        $filterKelasPelayananHeader = [];
        foreach ($getKelasPelayananFilter as $x => $y) {
            $idx = DocoHelpers::encrypt($x);
            $filterKelasPelayananHeader[$idx] = $y;
        }
        return $this->render('dashboard', get_defined_vars());
    }

    /*dashboar Bhayangkara*/
    public function actionDashboardBhayangkara(){
        $namaRS = Yii::$app->docoVars->identity("nama_rumahsakit");
        $listGroupKelas = array(
                      array(
                        "kamars" => array( 
                                            array('kamarDetails' => array('id_kamar_detail' => 1,
                                                                    'name' => 'zzz',
                                                                    'kamarDetails' =>  array('id_kamar_detail' => 1
                                                                                            )
                                                                    ),

                                                ),
                        ),
                        "id_kamar_group_kelas" => "Alfred Hitchcockx",
                        "nama_group_kelas" => 'Super Vip',
                        "id_kamar_detail" => array('id' => 1,
                                                    'name' => 'zzz'
                                                    ),
                      ),
                    
                    );
        return $this->render('dashboard_bhayangkara', get_defined_vars());
    }

}
