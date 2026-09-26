<?php 

namespace Doco\bedah\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use yii\web\Response;

class MappingPosisiOperasiController extends DocoController
{
    protected $_restBedah;

    public function init()
    {
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
    }

    /**
     * Return view index
     * 
     * @return View
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionIndex()
    {
        $surgeryTeam = $this->guzzleExec($this->_restBedah, [
            'url' => 'mapping-posisi-operasi'
        ]);
        return $this->render('index', [
            'title' => 'Mapping posisi operasi dan tindakan',
            'surgeryTeam' => $surgeryTeam
        ]);
    }

    /**
     * API create mapping
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionCreate()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'mapping-posisi-operasi/create',
            'method' => 'POST',
            'payload' => [
                'form_params' => Yii::$app->request->post()
            ],
            'returnResponse' => true
        ]);
    }

    /**
     * API update mapping
     * 
     * @param String $daftartindakan_id
     * @param String $timoperasi_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdate($daftartindakan_id, $timoperasi_id)
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'mapping-posisi-operasi/update',
            'method' => 'PUT',
            'payload' => [
                'form_params' => Yii::$app->request->post(),
                'query' => compact('daftartindakan_id', 'timoperasi_id')
            ],
            'returnResponse' => true
        ]);
    }

    /**
     * API update status mapping
     * 
     * @param String $daftartindakan_id
     * @param String $timoperasi_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdateStatus($daftartindakan_id, $timoperasi_id)
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'mapping-posisi-operasi/update-status',
            'method' => 'PUT',
            'payload' => [
                'query' => compact('daftartindakan_id', 'timoperasi_id')
            ],
            'returnResponse' => true
        ]);
    }

    /**
     * API datatable
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDatatable()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            return $this->guzzleExec($this->_restBedah, [
                'url' => 'mapping-posisi-operasi/datatable',
                'payload' => [
                    'query' => DocoDatatableHelper::convertToRestfulParams(Yii::$app->request->get()),
                ],
            ]);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * API get tindakan operasi
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionTindakanOperasi()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'allow/tindakan-operasi',
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }
}