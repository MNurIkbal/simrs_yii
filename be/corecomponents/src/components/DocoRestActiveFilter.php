<?php

namespace Doco\components;

use Yii;

class DocoRestActiveFilter
{
    public static function advancedFilter($model, $query)
    {
        $request = Yii::$app->request;
        $q = trim($request->get('q', null));
        $advancedFilters = $request->get('advanced-filter', []);
        $filters = $request->get('filters', '');
        $order = $request->get('order', '');
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
                        } else {
                            // if ((int) $q) {
                                // $query->orWhere([$tableName.".".$column => $q]);
                            // }
                            // if(preg_match("/\./i", $column))
                                // $query->orWhere([$column => $search]);
                        }
                    }

        if ($order) {
            if(!preg_match("/\./i", $order))
                $order = "{$tableName}.{$order}";
            $query->orderBy($order);
        }

        return $query;
    }

    // json baru bisa filter di top level property dan di main model
    public static function filterMutation($model, $query, $options)
    {
        $sub = [];
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $tableName = $model->getTableSchema()->name;
        foreach($advancedFilters as $column => $search) {
            if (array_key_exists($column, $options)) {
                $bindingkey = ':' . preg_replace('/[^a-z0-9]/', '_', $column);
                switch ($options[$column]) {
                    case 'date':
                    case 'time':
                        $val = explode(' - ', $search);
                        if ($options[$column] == 'date') {
                            if (isset($val[1])) {
                                $query->andWhere([
                                    'between',
                                    $column,
                                    date('Y-m-d H:i:s', strtotime($val[0])),
                                    date('Y-m-d H:i:s', strtotime($val[1]))
                                ]);
                            } else {
                                $query->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("date(%s.%s) = %s", $tableName, $column, $bindingkey),
                                        [
                                            $bindingkey => date('Y-m-d', strtotime($val[0]))
                                        ]
                                    )
                                );
                            }
                        } else {
                            if (isset($val[1])) {
                                $query->andWhere([
                                    'between',
                                    $column,
                                    date('H:i', strtotime($val[0])),
                                    date('H:i', strtotime($val[1]))
                                ]);
                            } else {
                                $query->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("(%s.%s)::time = %s", $tableName, $column, $bindingkey),
                                        [
                                            $bindingkey => date('H:i', strtotime($val[0]))
                                        ]
                                    )
                                );
                            }
                        }
                        break;
                    case 'json':
                    case 'json_like':
                    case 'json_date':
                    case 'json_time':
                        $extractCols = explode('.', $column);
                        $key = array_shift($extractCols);
                        if ($options[$column] == 'json_date') {
                            $val = explode(' - ', $search);
                            if (isset($val[1])) {
                                $query->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("to_date(%s.%s->>'%s', 'YYYY-MM-DD HH24:MI:SS') BETWEEN %s AND %s", $tableName, $key, $extractCols[0], $bindingkey . '0', $bindingkey . '1'),
                                        [
                                            $bindingkey . '0' => date('Y-m-d H:i:s', strtotime($val[0])),
                                            $bindingkey . '1' => date('Y-m-d H:i:s', strtotime($val[1]))
                                        ]
                                    )
                                );
                            } else {
                                $query->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("to_date(%s.%s->>'%s', 'YYYY-MM-DD HH24:MI:SS') = %s", $tableName, $key, $extractCols[0], $bindingkey),
                                        [
                                            $bindingkey => date('Y-m-d H:i:s', strtotime($val[0]))
                                        ]
                                    )
                                );
                            }
                        } elseif ($options[$column] == 'json_time') {
                            $val = explode(' - ', $search);
                            if (isset($val[1])) {
                                $query->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("(%s.%s->>'%s')::time BETWEEN %s AND %s", $tableName, $key, $extractCols[0], $bindingkey . '0', $bindingkey . '1'),
                                        [
                                            $bindingkey . '0' => date('H:i', strtotime($val[0])),
                                            $bindingkey . '1' => date('H:i', strtotime($val[1]))
                                        ]
                                    )
                                );
                            } else {
                                $query->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("(%s.%s->>'%s')::time = %s", $tableName, $key, $extractCols[0], $bindingkey),
                                        [
                                            $bindingkey => date('H:i', strtotime($val[0]))
                                        ]
                                    )
                                );
                            }
                        } elseif ($options[$column] == 'json_like') {
                            $query->andWhere(
                                new \yii\db\Expression(
                                    sprintf("%s.%s::json->>'%s' ilike %s", $tableName, $key, $extractCols[0], $bindingkey),
                                    [
                                        $bindingkey => "%{$search}%"
                                    ]
                                )
                            );
                        } else {
                            $query->andWhere(
                                new \yii\db\Expression(
                                    sprintf("%s.%s::json->>'%s' = %s", $tableName, $key, $extractCols[0], $bindingkey),
                                    [
                                        $bindingkey => $search
                                    ]
                                )
                            );
                        }
                        break;
                    case 'json_array':
                    case 'json_array_like':
                    case 'json_array_date':
                    case 'json_array_time':
                        $extractCols = explode('.', $column);
                        $key = array_shift($extractCols);
                        if (!array_key_exists("{$tableName}_{$key}", $sub)) {
                            $sub["{$tableName}_{$key}"] = (new \yii\db\Query())
                                ->select(new \yii\db\Expression('1'))
                                ->from(new \yii\db\Expression(sprintf('json_array_elements(%1$s.%2$s) AS %1$s_%2$s', $tableName, $key)));
                        }
                        if ($options[$column] == 'json_array_date') {
                            $val = explode(' - ', $search);
                            if (isset($val[1])) {
                                $sub["{$tableName}_{$key}"]->andWhere(
                                    new \yii\db\Expression(
                                        sprintf(
                                            "to_date(%s_%s->>'%s', 'YYYY-MM-DD HH24:MI:SS') BETWEEN %s AND %s",
                                            $tableName,
                                            $key,
                                            $extractCols[0],
                                            $bindingkey . '0',
                                            $bindingkey . '1'
                                        ),
                                        [
                                            $bindingkey . '0' => date('Y-m-d H:i:s', strtotime($val[0])),
                                            $bindingkey . '1' => date('Y-m-d H:i:s', strtotime($val[1]))
                                        ]
                                    )
                                );
                            } else {
                                $sub["{$tableName}_{$key}"]->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("to_date(%s_%s->>'%s', 'YYYY-MM-DD HH24:MI:SS') = %s", $tableName, $key, $extractCols[0], $bindingkey),
                                        [
                                            $bindingkey => date('Y-m-d', strtotime($val[0]))
                                        ]
                                    )
                                );
                            }
                        } elseif ($options[$column] == 'json_array_time') {
                            $val = explode(' - ', $search);
                            if (isset($val[1])) {
                                $sub["{$tableName}_{$key}"]->andWhere(
                                    new \yii\db\Expression(
                                        sprintf(
                                            "(%s_%s->>'%s')::time BETWEEN %s AND %s",
                                            $tableName,
                                            $key,
                                            $extractCols[0],
                                            $bindingkey . '0',
                                            $bindingkey . '1'
                                        ),
                                        [
                                            $bindingkey . '0' => date('H:i', strtotime($val[0])),
                                            $bindingkey . '1' => date('H:i', strtotime($val[1]))
                                        ]
                                    )
                                );
                            } else {
                                $sub["{$tableName}_{$key}"]->andWhere(
                                    new \yii\db\Expression(
                                        sprintf("(%s_%s->>'%s')::time = %s", $tableName, $key, $extractCols[0], $bindingkey),
                                        [
                                            $bindingkey => date('H:i', strtotime($val[0]))
                                        ]
                                    )
                                );
                            }
                        } elseif ($options[$column] == 'json_array_like') {
                            $sub["{$tableName}_{$key}"]->andWhere(
                                new \yii\db\Expression(
                                    sprintf("%s_%s->>'%s' ilike %s", $tableName, $key, $extractCols[0], $bindingkey),
                                    [
                                        $bindingkey => "%{$search}%"
                                    ]
                                )
                            );
                        } else {
                            $sub["{$tableName}_{$key}"]->andWhere(
                                new \yii\db\Expression(
                                    sprintf("%s_%s->>'%s' = %s", $tableName, $key, $extractCols[0], $bindingkey),
                                    [
                                        $bindingkey => $search
                                    ]
                                )
                            );
                        }
                        break;
                    default:
                        // skip
                        break;
                }
                unset($_GET['advanced-filter'][$column]);
            }
        }
        foreach ($sub as $exists) {
            $query->andWhere(['exists', $exists]);
        }
        return $query;
    }
}
