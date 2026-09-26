<?php
/**
 * @author: arief saputra
 * @description: Info tarif Pelayanan
**/

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\TariftindakanruanganV;
use app\modules\v1\models\InfoTarifRs;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class InfTarifPelayananController extends \Doco\components\DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoTarifRs';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["komponen-tarif"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function getData()
    {
      
        $data = InfoTarifRs::find()->select(['*','row_number() over() as number'])->orderby(['penjamin_nama' => SORT_ASC]);
        return $data;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $page = (isset($get['page'])) ? $get['page']: 1;
            $result = $this->getData()->Where(['komponentarif_id' => 6]);
            if($request->post('instalasi_id')) {
                $result->andWhere(['instalasi_id' => $request->post('instalasi_id')]);
            }

            if($request->post('ruangan_id')) {
                $result->andWhere(['ruangan_id' => $request->post('ruangan_id')]);
            }

            if($request->post('kelaspelayanan_id')) {
                $result->andWhere(['kelaspelayanan_id' => $request->post('kelaspelayanan_id')]);
            }

            if($request->post('kelaspelayanan_id')) {
                $result->andWhere(['kelaspelayanan_id' => $request->post('kelaspelayanan_id')]);
            }

            if ($indexing = $request->post('daftartindakan_nama')) {
                $result->andFilterWhere(['ILIKE', 'daftartindakan_nama', $indexing]);
            }

            if($request->post('jenistarif_id')) {
                $result->andWhere(['jenistarif_id' => $request->post('jenistarif_id')]);
            }

            if($request->post('kategoritindakan_id')) {
                $result->andWhere(['kategoritindakan_id' => $request->post('kategoritindakan_id')]);
            } 

            if($request->post('komponentarif_id')) {
                $result->andWhere(['komponentarif_id' => $request->post('komponentarif_id')]);
            }

            if($request->post('tariftindakan_id')) {
                $result->andWhere(['tariftindakan_id' => $request->post('tariftindakan_id')]);
            }

            if($request->post('penjamin_id')) {
                $result->andWhere(['penjamin_id' => $request->post('penjamin_id')]);
            }

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }
            else{
                $result->orderby(['instalasi_nama' => 'asc']);
            }

            if(isset($_GET['advanced-filter'])){            
                $filter = $_GET['advanced-filter'];                                                 
                if(isset($filter['instalasi_nama'])){             
                   $result->andWhere(['instalasi_id' => $filter['instalasi_nama']]);
                }
                
                if(isset($filter['ruangan_nama'])){             
                   $result->andWhere(['ruangan_id' => $filter['ruangan_nama']]);
                }
                if(isset($filter['komponentarif_nama'])){             
                   $result->andWhere(['komponentarif_id' => $filter['komponentarif_nama']]);
                }
                if(isset($filter['kategoritindakan_nama'])){             
                   $result->andWhere(['kategoritindakan_id' => $filter['kategoritindakan_nama']]);
                }
                if(isset($filter['daftartindakan_nama'])){             
                   $result->andWhere(['daftartindakan_id' => $filter['daftartindakan_nama']]);
                }
                if(isset($filter['kelaspelayanan_nama'])){             
                   $result->andWhere(['kelaspelayanan_id' => $filter['kelaspelayanan_nama']]);
                }
                if(isset($filter['penjamin_nama'])){             
                   $result->andWhere(['penjamin_id' => $filter['penjamin_nama']]);
                }
            }

          
            $perPage = (isset($get['per-page'])) ? $get['per-page']: 10;
            $offset = ($page - 1) * $perPage;
            $result = $result
                ->limit($request->post('length',$perPage))
                ->offset($request->post('start',$offset));
            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count(),
                'test_data' => $get,
            ];
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

    public function actionKomponenTarif()
    {
        $request = Yii::$app->request;
        $query = $this->getData()
                 ->andWhere(['ruangan_id' => $request->post('ruangan_id'),
                        'daftartindakan_id' => $request->post('daftartindakan_id'),
                        'penjamin_id' => $request->post('penjamin_id'),
                        'kelaspelayanan_id' => $request->post('kelaspelayanan_id'),
                        'tariftindakan_id' => $request->post('tariftindakan_id')
                    ]);

        return [
                'data' => $query->andWhere(['komponentarif_id' => 6])->one(),
                'query' => $query->andWhere(['!=','komponentarif_id',6])->all(),
                'count' => $query->count(),
                'total' =>($query->andWhere(['!=','komponentarif_id',6])->sum('harga_tariftindakan') != null ) ? $query->andWhere(['!=','komponentarif_id',6])->sum('harga_tariftindakan') : 0
            ];
    }
}