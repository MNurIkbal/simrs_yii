<?php
/**
 * @author: arief saputra
 * @update : iqbal@docotel.com
 * @description: Info tarif Pelayanan
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use yii\web\HttpException;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\TariftindakanruanganV;
use app\modules\v1\models\TarifTindakanRuanganDetailView;

class InfTarifPelayananController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TariftindakanruanganV';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        // unset($actions['view']);
        return $actions;
    }

    public function getData()
    {
        $data = TariftindakanruanganV::find();
        return $data;
    }

    private function requestFilter($request, $query){
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){      
            /*if (isset($advancedFilters['carabayar_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($advancedFilters['carabayar_nama']) ]);
            }            
            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
            }
            */
        }

        return $query;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new TariftindakanruanganV;
            $query = $model::find();
            
            $getQueryFilter = $this->requestFilter($request, $query);
            
            $query = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
            $query->orderby(['carabayar_nama'=> SORT_ASC]);
            
            return new ActiveDataProvider([
                'query' => $query,
            ]);

        } catch (\yii\db\Exception $e) {
            return [ 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetKomponenTarif()
    {
        try {
            $request = Yii::$app->request;
            $tariftindakan_id   = $request->get('tariftindakan_id');
            $ruangan_id         = $request->get('ruangan_id');
            $daftartindakan_id  = $request->get('daftartindakan_id');

            $modelTarifTindakan = new TariftindakanruanganV;
            $queryTarifTindakan = $modelTarifTindakan::find();
            $queryTarifTindakan->where(['tariftindakan_id' =>$tariftindakan_id,
                            'ruangan_id' =>$ruangan_id,
                            'daftartindakan_id' =>$daftartindakan_id
                            ]);
            $headers = $queryTarifTindakan->one();

            $modelTarifTDetail = new TarifTindakanRuanganDetailView;
            $queryTarifTDetail = $modelTarifTDetail::find();
            $queryTarifTDetail->where(['daftartindakan_id' =>$headers->daftartindakan_id,
                            'kelaspelayanan_id' =>$headers->kelaspelayanan_id,
                            'penjamin_id' =>$headers->penjamin_id
                            ]);
            $data = $queryTarifTDetail->all();

            $result = ['header' =>$headers,
                        'data' =>$data 
                    ];
            return DocoHelpers::response($result);
        }  catch (\yii\db\Exception $e) {
            return [ 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    /*
     * get komponen tarif code old
     */
    public function actionKomponenTarif()
    {
        $request = Yii::$app->request;
        $returnData = (new \yii\db\Query())
                        ->select([
                                't3.komponentarif_id',
                                't3.komponentarif_nama',
                                't.harga_tariftindakan',
                        ])->from('tariftindakan_m t')
                        ->join('JOIN', 'tindakanruangan_mp t2','t2.daftartindakan_id = t.daftartindakan_id')
                        ->join('JOIN', 'komponentarif_m t3','t3.komponentarif_id = t.komponentarif_id');
        
        $returnData->andWhere(['t2.ruangan_id' => $request->post('ruangan_id')]);
        $returnData->andWhere(['t.daftartindakan_id' => $request->post('daftartindakan_id')]);
        $returnData->andWhere(['t.kelaspelayanan_id' => $request->post('kelaspelayanan_id')]);

        return [
                'data' => $returnData->all(),
                'count' => $returnData->count()
            ];
    }
}