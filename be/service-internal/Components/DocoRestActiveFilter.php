<?php

namespace Integrasi\Components;

use Yii;

class DocoRestActiveFilter
{
    public static function advancedFilter($model, $query, $filter)
    {
        $q = isset($filter['q']) ? trim($filter['q']) : null;
        $advancedFilters =  isset($filter['advanced-filter']) ? $filter['advanced-filter'] : [];
        $filters = isset($filter['filters']) ? $filter['filters'] : '';
        $order = isset($filter['order']) ? $filter['order'] : '';
        $columns = explode(',', $filters);

        $tableName = $model->getTableSchema()->name;
        foreach($advancedFilters as $column => $search) {
            if (is_array($search)) {
                $query->andWhere(['IN', $tableName.".".$column, $search]);
                continue;
            }
            $search = trim($search);
            if (preg_match("/.+/i", $search))
                if (!preg_match("/(loading.*|processing.*)/i", $search)) {
                    $type = null;
                    $tableColumns = $model->getTableSchema()->columns;
                    if (isset($tableColumns[$column]))
                        $type = $tableColumns[$column]->type;

                    if ($type == 'string' || $type == 'text') {
                        $query->andWhere(['ILIKE',$tableName.".".$column,$search]);
                    } elseif (in_array($type, ['integer', 'numeric', 'bigint'])) {
                        if ((int) $search) {
                            $query->andWhere([$tableName.".".$column => $search]);
                        }
                    } elseif(in_array($type,['double'])){
                        $query->andWhere([$tableName.".".$column => $search]);
                    }elseif ($type == 'boolean') {
                        $query->andWhere([$tableName.".".$column => $search]);
                    } elseif (preg_match("/\./i", $column)){
                        $arrcol = explode(".", $column);

                        if(count($arrcol) > 0){
                            $tableJoinSchema = Yii::$app->db->schema->getTableSchema($arrcol[0]);
                            $tableJoinColumns = $tableJoinSchema->columns;

                            if (isset($tableJoinColumns[$arrcol[1]]))
                                $type = $tableJoinColumns[$arrcol[1]]->type;

                            if ($type == 'string' || $type == 'text') {
                                $query->andWhere(['ILIKE',$arrcol[0].".".$arrcol[1],$search]);
                            } elseif (in_array($type, ['integer', 'numeric'])) {
                                if ((int) $search) {
                                    $query->andWhere([$arrcol[0].".".$arrcol[1] => $search]);
                                }
                            } elseif(in_array($type,['double'])){
                                $query->andWhere([$arrcol[0].".".$arrcol[1] => $search]);
                            }elseif ($type == 'boolean') {
                                $query->andWhere([$arrcol[0].".".$arrcol[1] => $search]);
                            }
                        }
                    }
                }
        }
        foreach ($columns as $column)
            if (trim($column))
                if (preg_match("/.+/i", $q))
                    if (!preg_match("/(loading.*|processing.*)/i", $q)) {
                        $type = null;
                        $tableColumns = (array) $model->getTableSchema()->columns;
                        if (isset($tableColumns[$column]))
                            $type = $tableColumns[$column]->type;

                        if ($type == 'string' || $type == 'text') {
                            $query->orWhere(['ILIKE',$tableName.".".$column,$q]);
                        } elseif (in_array($type, ['integer', 'numeric', 'bigint'])) {
                            if ((int) $q) {
                                $query->orWhere([$tableName.".".$column => $q]);
                            }
                        } elseif ($type == 'boolean') {
                            $query->orWhere([$tableName.".".$column => $q]);
                        }
                    }

        if ($order) {
            if(!preg_match("/\./i", $order))
                $order = "{$tableName}.{$order}";
            $query->orderBy($order);
        }

        return $query;
    }
}
