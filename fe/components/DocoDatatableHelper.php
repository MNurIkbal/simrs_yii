<?php

namespace app\components;

use yii\helpers\ArrayHelper;

class DocoDatatableHelper
{
    public static function convertToRestfulParams($datatableParams)
    {
        $yiiRestfulParams = [];
        $q = ArrayHelper::getValue($datatableParams, 'search.value', []);
        $columns = ArrayHelper::getValue($datatableParams, 'columns', []);
        $advancedFilters = [];
        $filters = [];

        if (count($columns)) {
            foreach ($columns as $column) {
                if ($column['searchable'] == 'true') {
                    $filters[] = $column['name'] ? $column['name'] : $column['data'];
                    if (preg_match("/.+/i", $column['search']['value'])) {
                        if ($column['name']) {
                            $advancedFilters[$column['name']] = $column['search']['value'];
                        } else {
                            $advancedFilters[$column['data']] = $column['search']['value'];
                        }
                    }
                }
            }
        }


        $order_list = [];
        if (isset($datatableParams['order'])) {
            foreach ($datatableParams['order'] as $order) {
                $sortIndex = isset($order['column']) ? $order['column'] : '';
                $sortDir = isset($order['dir']) ? $order['dir'] == 'asc' ? 'ASC' : 'DESC' : '';
                $sortColumn = $columns[$sortIndex]['name'] ? $columns[$sortIndex]['name'] : $columns[$sortIndex]['data'];

                $order_list[] = !empty($sortColumn) ? $sortColumn . ' ' . $sortDir : '';
            }
        }

        /*$sortIndex = isset($datatableParams['order']) ? $datatableParams['order'][0]['column'] : '';
        $sortDir = isset($datatableParams['order']) ? $datatableParams['order'][0]['dir'] == 'asc'? 'ASC' : 'DESC' : '';
        $sortColumn = isset($datatableParams['order']) ? $columns[$sortIndex]['name']
                                ? $columns[$sortIndex]['name']
                                : $columns[$sortIndex]['data']
                            : '';*/
        $start = ArrayHelper::getValue($datatableParams, 'start', 0);
        $length = ArrayHelper::getValue($datatableParams, 'length', 0);
        $yiiRestfulParams['page'] = $start == 0 ? 1 : $start / $length + 1;
        $yiiRestfulParams['per-page'] = $length;
        // $yiiRestfulParams['order'] = !empty($sortColumn) ? $sortColumn.' '.$sortDir : '';
        $yiiRestfulParams['order'] = implode(", ", $order_list);
        $yiiRestfulParams['q'] = $q;
        $yiiRestfulParams['filters'] = implode(',', $filters);
        $yiiRestfulParams['advanced-filter'] = $advancedFilters;
        return $yiiRestfulParams;
    }

    public static function convertToRestfulParamsClientSide($datatableParams)
    {
        $yiiRestfulParams = [];
        $q = $datatableParams['search']['value'];
        $columns = ArrayHelper::getValue($datatableParams, 'columns', []);
        $advancedFilters = [];
        $filters = [];
        if (count($columns)) {
            foreach ($columns as $column) {
                if ($column['searchable'] == 'true') {
                    $filters[] = $column['name'] ? $column['name'] : $column['data'];
                    if (preg_match("/.+/i", $column['search']['value'])) {
                        if ($column['name']) {
                            $advancedFilters[$column['name']] = $column['search']['value'];
                        } else {
                            $advancedFilters[$column['data']] = $column['search']['value'];
                        }
                    }
                }
            }
        }
        $order_list = [];
        if (isset($datatableParams['order'])) {
            foreach ($datatableParams['order'] as $order) {
                $sortIndex = isset($order['column']) ? $order['column'] : '';
                $sortDir = isset($order['dir']) ? $order['dir'] == 'asc' ? 'ASC' : 'DESC' : '';
                $sortColumn = $columns[$sortIndex]['name'] ? $columns[$sortIndex]['name'] : $columns[$sortIndex]['data'];
                $order_list[] = !empty($sortColumn) ? $sortColumn . ' ' . $sortDir : '';
            }
        }
        /*$sortIndex = isset($datatableParams['order']) ? $datatableParams['order'][0]['column'] : '';
        $sortDir = isset($datatableParams['order']) ? $datatableParams['order'][0]['dir'] == 'asc'? 'ASC' : 'DESC' : '';
        $sortColumn = isset($datatableParams['order']) ? $columns[$sortIndex]['name']
                                ? $columns[$sortIndex]['name']
                                : $columns[$sortIndex]['data']
                            : '';*/
        $yiiRestfulParams['page'] = $datatableParams['start'] == 0 ? 1 : $datatableParams['start'] / $datatableParams['length'] + 1;
        $yiiRestfulParams['per-page'] = $datatableParams['length'];
        // $yiiRestfulParams['order'] = !empty($sortColumn) ? $sortColumn.' '.$sortDir : '';
        $yiiRestfulParams['order'] = implode(", ", $order_list);
        $yiiRestfulParams['q'] = $q;
        $yiiRestfulParams['filters'] = implode(',', $filters);
        $yiiRestfulParams['advanced-filter'] = $advancedFilters;
        return $yiiRestfulParams;
    }

    /**
     * This function will return rest params for datatable
     * 
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function advancedFilterParam()
    {
        $yiiRestfulParams = [];
        $datatableParams = \Yii::$app->request->get();
        if (\Yii::$app->request->isPost) {
            $datatableParams = \Yii::$app->request->post();
        }
        $advancedFilterForm = isset($datatableParams['advancedFilter']) ? $datatableParams['advancedFilter'] : [];
        $columns = ArrayHelper::getValue($datatableParams, 'columns', []);
        $advancedFilters = [];
        $filters = [];

        if (!empty($advancedFilterForm)) {
            foreach ($advancedFilterForm as $key => $column) {
                $advancedFilters[$key] = $column;
            }
        }

        $order_list = [];
        if (isset($datatableParams['order'])) {
            foreach ($datatableParams['order'] as $order) {
                $sortIndex = isset($order['column']) ? $order['column'] : '';
                $sortDir = isset($order['dir']) ? $order['dir'] == 'asc' ? 'ASC' : 'DESC' : '';
                $sortColumn = $columns[$sortIndex]['name'] ? $columns[$sortIndex]['name'] : $columns[$sortIndex]['data'];

                $order_list[] = !empty($sortColumn) ? $sortColumn . ' ' . $sortDir : '';
            }
        }

        $yiiRestfulParams['page'] = ArrayHelper::getValue($datatableParams, 'start', 0) == 0 ? 1 : ArrayHelper::getValue($datatableParams, 'start', 0) / ArrayHelper::getValue($datatableParams, 'length', 0) + 1;
        $yiiRestfulParams['per-page'] = ArrayHelper::getValue($datatableParams, 'length', 0);
        $yiiRestfulParams['order'] = implode(", ", $order_list);
        $yiiRestfulParams['filters'] = implode(',', $filters);
        $yiiRestfulParams['advanced-filter'] = $advancedFilters;
        return $yiiRestfulParams;
    }
}
