<?php

namespace Doco\classes;

use Yii;

class selectModel {
	public static function getData($model, $option, $type, $one)
	{
		$params = Yii::$app->request;
	    $term = null;

	    if ($params->get('term')) {
	        $term = $params->get('term');
	    }

	    $page = $params->get('page', 0);
        $limit = $params->get('limit', 10);
        $offset = $params->get('offset', ($page - 1) * 10);

	    $query = $model::find();

	    if (isset($option['_SELECT']) && !empty($option['_SELECT'])) {
	        $query->select($option['_SELECT']);
	    }

	    if ($term) {
	        foreach ($option as $key => $value) {
	            foreach ($option[$key] as $key1 => $value1) {
	                if (($key === 'ILIKE') && !empty($option['ILIKE'])) {
	                    $query->orWhere([$key, 'LOWER(' . $value1 . ')', $term]);
	                }
	                if (($key === 'WHERE') && !empty($option['WHERE'])) {
	                    $query->andWhere(['LOWER(' . $value1 . ')' => strtolower($term)]);
	                }
	            }
	        }
	    }

	    foreach ($option as $key => $value) {
	        foreach ($option[$key] as $key1 => $value1) {
	            if (($key === 'DEFAULT') && !empty($option['DEFAULT'])) {
	                $query->andWhere([$value1[0] => $value1[1]]);
	            }
	            if (($key === 'OTHER') && !empty($option['OTHER'])) {
	                $query->andWhere([$value1[0], $value1[1], $value1[2]]);
	            }
	            if (($key === 'ORDERBY') && !empty($option['ORDERBY'])) {
	                $query->orderby([$value1[0] => $value1[1]]);
	            }
	            if (($key === 'FILTERWHERE') && !empty($option['FILTERWHERE'])) {
	                $query->andFilterWhere([
	                    'AND',
	                    [$value1[0], $value1[1], $value1[2]]
	                ]);
	            }
	        }
	    }

	    if (isset($option['GROUPBY']) && !empty($option['GROUPBY'])) {
	        $query->groupBy($option['GROUPBY']);
	    }

	    if($type == 'select2') {
	    	return $query->offset($offset)->limit($limit)->asArray()->all();
	    }

	    if($one) {
	        return $query->one();
	    }

        return $query->asArray()->all();
	}
}
