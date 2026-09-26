<?php

/**
 * @Author: Aris
 * @Date:   2019-07-23 14:34:00
 * @Description:
 * Melihat Informasi Indikator Rumah Sakit Per Bulan
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

//use app\modules\rm\models\;

class InfIndikatorRsBulanController extends DocoController
{
    protected $_title      = "Informasi Indikato Rumah Sakit Per Bulan";
    protected $_controller = '/rm/inf-indikator-rs-bulan';
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;

    }

    public function behaviors()
    {
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    /*=================================================================
    =            list info indikator rumah sakit per bulan            =
    =================================================================*/

    public function actionIndex()
    {
        try 
        {
            $title = Yii::t('fe', 'Informasi Indikator Rumah Sakit Per Bulan');
            return $this->render('index', get_defined_vars());
        }
        catch(RequestException $e)
        {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataIndikatorRsBulan()
    {
        $request          = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try 
        {
           $response = $this->_restRm->get('inf-indikator-rs-bulan',
            [
                'query'=>$yiiRestfulParams
            ]);

           $body = json_decode($response->getBody(), TRUE);
           $data = [];
           $no   = $request->get('start');

            $label = [
                'BOR'                        => 'bor',
                'AVLOS'                      => 'avlos',
                'TOI'                        => 'toi',
                'BTO'                        => 'bto',
                'NDR'                        => 'ndr',
                'GDR'                        => 'gdr',
                'HP'                         => 'hari_perawatan',
                'LD'                         => 'lama_dirawat',
                'Jumlah TT Aktif'            => 'jumlah_tempat_tidur',
                'Rata-rata kunjungan harian' => 'avg_pasienrj',
            ];

           $template = [
                'BOR'                        => [],
                'AVLOS'                      => [],
                'TOI'                        => [],
                'BTO'                        => [],
                'NDR'                        => [],
                'GDR'                        => [],
                'HP'                         => [],
                'LD'                         => [],
                'Jumlah TT Aktif'            => [],
                'Rata-rata kunjungan harian' => [],
            ];

            $max = 12;
            $map = [];

            //mengambil value bulan yang ada di db//
            foreach ($body['response']['data'] as $key => $value) :
                $bulan       = (int) $value['bulan'];
                $map[$bulan] = $value;
            endforeach;

            //mengecek bulan 1-12 ada/tidak value//
            $map_total = [];
            for ($x = 0; $x <= $max; $x++) {
                foreach ($template as $key => $value) {
                    if(isset($map[$x + 1])){
                        $template[$key][$x] = $map[$x + 1][$label[$key]];
                        // $template[$key][12] += 1;
                    }
                    else{
                        $template[$key][$x] = 0;
                    }
                }
            }


            foreach ($template as $key => $value) {
                $row = [
                    'name' => $key
                ];
                $total = 0;
                foreach ($value as $k => $v) {
                    $row[$k] = DocoHelpers::formatNumber($v,false,true);
                    $total += $v;
                }
                $row[$k] = DocoHelpers::formatNumber($total/$k,false,true);
                $data[]  = $row;
            }
            $return = 
            [
                'data'            => $data,
                'draw'            => $request->get('draw'),
                'recordsTotal'    => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);

        }
        catch(RequestException $e)
        {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }


}
