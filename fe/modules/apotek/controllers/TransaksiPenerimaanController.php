<?php 

/**
 * @author Randy Vianda Putra
 * @todo Informasi Mutasi
 * @copyright 17 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;

class TransaksiPenerimaanController extends DocoController
{
    
    protected $_title = "Penerimaan Obat Alkes";
    protected $_module = '/apotek/transaksi-penerimaan';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    
    public function actionIndex()
    {
        $title = $this->_title;
        $model = new InformasiForm;
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

        return $this->render('index', get_defined_vars());
    }

    public function actionDetail()
    {
        $title = 'Detail Mutasi Obat Alkes';
        
        return $this->render('detail', get_defined_vars());
    }

    public function actionPenerimaan()
    {
        $title = 'Penerimaan Obat Alkes';
        
        return $this->render('detail', get_defined_vars());
    }

}