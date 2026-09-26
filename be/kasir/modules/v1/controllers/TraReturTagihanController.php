<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\ReturBayarPelayanan;
use app\modules\v1\models\TandaBuktiBayar;
use app\modules\v1\models\TandaBuktiKeluar;
use app\modules\v1\models\Shift;

class TraReturTagihanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ReturBayarPelayanan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["view"] = ["GET"];
        $verbs["create"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'tandabuktibayar_t,tandabuktikeluar_t');

        $model = new ReturBayarPelayanan;
        $query = $model::find()
            ->joinWith(['tandaBuktiBayar' => function($query){
                $query->from('tandabuktibayar_t');
            }])
            ->joinWith(['tandaBuktiKeluar' => function($query){
                $query->from('tandabuktikeluar_t');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionView($id)
    {
        $model = new ReturBayarPelayanan;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        if ($post) {

            $modelReturBayarPelayanan = new ReturBayarPelayanan;
            $modelReturBayarPelayanan->attributes = $post['returbayarpelayanan_t'];
            $modelReturBayarPelayanan->no_returbayar = (string) rand(1, 999999); // Noted Default generated
            $modelReturBayarPelayanan->total_tindakanretur = 0;
            $modelReturBayarPelayanan->tgl_returpelayanan = date("Y-m-d H:i:s");
            
            if($modelReturBayarPelayanan->save()) {
                $returbayarpelayanan_id = $modelReturBayarPelayanan->returbayarpelayanan_id;
            
                $modelTandaBuktiBayar = TandaBuktiBayar::findOne($post['returbayarpelayanan_t']['tandabuktibayar_id']);
                $modelTandaBuktiBayar->no_rek = $post['tandabuktibayar_t']['no_rek'];
                $modelTandaBuktiBayar->namapemilik_rek = $post['tandabuktibayar_t']['namapemilik_rek'];
                $modelTandaBuktiBayar->returbayarpelayanan_id = $returbayarpelayanan_id;
                
                if ($modelTandaBuktiBayar->update()) {
                    
                    $modelTandaBuktiKeluar = new TandaBuktiKeluar;
                    $modelTandaBuktiKeluar->returbayarpelayanan_id = $returbayarpelayanan_id;
                    $modelTandaBuktiKeluar->ruangan_id = $post['returbayarpelayanan_t']['ruangan_id']; 
                    $modelTandaBuktiKeluar->shift_id = Shift::getCurrentShiftId();
                    $modelTandaBuktiKeluar->no_kaskeluar = (string) rand(1, 999999); // Noted Default generated
                    $modelTandaBuktiKeluar->tahun = date("Y");
                    $modelTandaBuktiKeluar->tgl_kaskeluar = date("Y-m-d H:i:s");

                    if ($modelTandaBuktiKeluar->save()) {
                        $tandabuktikeluar_id = $modelTandaBuktiKeluar->tandabuktikeluar_id;
                        
                        $modelReturBayarPelayanan = ReturBayarPelayanan::findOne($returbayarpelayanan_id);
                        $modelReturBayarPelayanan->tandabuktikeluar_id = $modelTandaBuktiKeluar->tandabuktikeluar_id;
                        
                        if ($modelReturBayarPelayanan->update()) {
                            return [$modelReturBayarPelayanan, $modelTandaBuktiBayar, $modelTandaBuktiKeluar];
                        } else {
                            return $modelReturBayarPelayanan->errors;
                        }
                    } else {
                        return $modelTandaBuktiKeluar->errors;
                    }
                } else {
                    return $modelTandaBuktiBayar->errors;
                }
            } else {
                return $modelReturBayarPelayanan->errors;
            }
        }
    }
}