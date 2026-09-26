<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\JenisDarah;
use app\modules\v1\models\Lookup;

use yii\helpers\ArrayHelper;
use yii\db\Query;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-konfigantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-layarantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-group-konfig"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionGetJenisDarah($param = [])
    {
        try {
            $jenis_darah = JenisDarah::find()->all();
        } catch (Exception $e) {
            return [
                "status" => 422,
                "message" => $e->getMessage()
            ];
        }

        return $jenis_darah;
    }

    public function actionGetLookup($type, $limit = 0)
    {
        try {
            $lookup = Lookup::find()->where([
                "lookup_type" => $type
            ]);

            if ($limit > 0) {
                $lookup->limit($limit);
            }

            return $lookup->all();
        } catch (Exception $e) {
            return [
                "status" => 422,
                "message" => $e->getMessage()
            ];
        }
    }

    public function actionGetGolonganJenisDarah()
    {
        $golongandarah = $this->actionGetLookup("golongan_darah");
        $jenis_darah = $this->actionGetJenisDarah(["params" => true]);

        $list_goldar[] = [
            "id" => "",
            "text" => "------ Pilih ------"
        ];
        foreach ($golongandarah as $row => $value) {
            $list_goldar[] = [
                "id" => $value->lookup_id,
                "text" => $value->lookup_value
            ];
        }

        $list_jd[] = [
            "id" => "",
            "text" => "------ Pilih ------"
        ];
        foreach ($jenis_darah as $key => $jd) {
            $list_jd[] = [
                "id" => $jd->jenisdarah_id,
                "text" => $jd->jenisdarah_nama
            ];
        }

        return [
            "golongandarah" => $list_goldar,
            "jenis_darah" => $list_jd,
        ];
    }


}