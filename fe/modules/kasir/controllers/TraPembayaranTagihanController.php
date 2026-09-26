<?php 

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Pembayaran Apotek
 * @copyright 22 January 2018 aweutist
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\kasir\models\TransaksiApotekForm;

class TraPembayaranTagihanController extends DocoController
{
    
    protected $_title = "Pembayaran tagihan pasien";
    protected $_module = '/kasir/tra-pembayaran-tagihan';
    protected $_restKasir; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
    }

    
    public function actionPasien()
    {
        $title = $this->_title;
        $model = new TransaksiApotekForm;
        // $cache = Yii::$app->cache;
        // $data = $this->getResep();
        // $temp_cache = [];
        // $no_resep = $cache->get('no_resep');
        // if ($no_resep === false) {
        //     $no = 0;
        //     foreach ($data['data_resep'] as $key => $value) {
        //         $list_cache[$value['noresep']] = [
        //             'reseptur_id' => $value['reseptur_id'],
        //             'no_resep' => $value['noresep'],
        //             'tgl_resep' => $value['tglresep'],
        //             'nama_pasien' => $value['nama_pasien'],
        //             'no_pendaftaran' => $value['no_pendaftaran'],
        //             'nama_dokter' => $value['nama_pegawai'],
        //             'instalasi' => $value['instalasireseptur_nama'],
        //             'ruangan' => $value['ruangan_nama']
        //         ];
        //         $no++;
        //     }
        //     if ($no) {
        //         $listResep = json_encode($list_cache);
        //         Yii::$app->cache->set('no_resep', $listResep, 30);
        //     }
        // }
        // $no_resep = $cache->get('no_resep');

        return $this->render('pasien', get_defined_vars());
    }


}