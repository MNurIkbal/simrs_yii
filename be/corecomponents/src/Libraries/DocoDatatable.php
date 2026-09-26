<?php

namespace Doco\Libraries;

use Yii;

/**
 * Datatable class, to generate datatable response
 * @author Tsani Nashrullah
 */
class DocoDatatable
{
    /** @var Array $resultDatatable */
    private $resultDatatable = [];

    /** @var Eloquent $queryEloquent */
    private $queryEloquent;

    /** @var Array $orderedParam
     * type 1 is ASC
     * type 2 is DESC
     * @author Tsani Nashrullah
     */
    private $orderedParam = [
        'key' => '',
        'type' => 'asc',
    ];

    /** @var Integer $limitRecord */
    private $limitPerPage = 10;

    /** @var Integer $pageNumber */
    private $pageNumber = 1;

    /** @var Arrays $payload */
    protected $payload;

    /** @var Function $customFilter */
    protected $customFilter = null;

    /** @var Array $additionalColumn */
    protected $additionalColumn = null;

    /** @var Array $returnNotJson */
    protected $returnJson = true;

    public function __construct($modelClass, $payloadUser = [])
    {
        $payload = empty($payloadUser) ? Yii::$app->request->get() : $payloadUser;
        $this->queryEloquent = $modelClass;
        $this->payload = $payload;
        $this->pageNumber = isset($payload['page']) ? $payload['page'] : 1;
        $this->limitPerPage = isset($payload['per-page']) ? $payload['per-page'] : 10;
        $orderArray = isset($payload['order']) ? explode(" ", $payload['order']) : [];
        if (count($orderArray) == 2 && in_array(strtolower($orderArray[1]), ['asc', 'desc'])) {
            $this->orderedParam = [
                'key' => $orderArray[0],
                'type' => strtolower($orderArray[1]),
            ];
        }
    }

    /**
     * Set value by dot
     *
     * @param Array $arr
     * @param String $path
     * @param String $value
     * @param String $separator
     * @return String/Array
     * @author Tsani Nashrullah
     **/
    private function setKeyByString(&$arr, $path, $value, $separator = '.')
    {
        $keys = explode($separator, $path);
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];

            if (isset($arr[$key]) && !is_array($arr[$key])) {
                $arr[$key] = [];
                $newArray = array_merge($arr[$key], [
                    (isset($keys[$i + 1]) ? $keys[$i + 1] : $key) => (($i + 1) >= count($keys) ? $value : [])
                ]);
            }
            $arr = &$arr[$key];
        }

        $arr = $value;
        return $arr;
    }

    /**
     * Custom filter
     *
     * @author Tsani Nashrullah
     **/
    public function setCustomFilter($whereClause)
    {
        $this->customFilter = $whereClause;
        return $this;
    }

    /**
     * Set additional column
     *
     * @author Tsani Nashrullah
     **/
    public function setAdditionalColumn($additionalColumn)
    {
        $this->additionalColumn = $additionalColumn;
        return $this;
    }

    /**
     * Set returnJson Variable to false
     *
     * @return $this
     * @author Tsani Nashrullah
     **/
    public function arrayOnly()
    {
        $this->returnJson = false;
        return $this;
    }

    /**
     * Execute datatable
     *
     * @return Array
     * @author Tsani Nashrullah
     **/
    public function make()
    {
        if (isset($this->payload['advanced-filter']) && !empty($this->payload['advanced-filter'])) {
            foreach ($this->payload['advanced-filter'] as $fieldName => $fieldValue) {
                if (!is_null($fieldValue) && $fieldValue != "") {
                    $this->queryEloquent->andWhere(['like', 'lower(' . $fieldName . ')', strtolower($fieldValue)]);
                }
            }
        }
        if (!empty($this->customFilter)) {
            call_user_func($this->customFilter, $this->queryEloquent);
        }
        if ($this->orderedParam['key'] !== '') {
            $this->queryEloquent = $this->queryEloquent->orderBy([
                $this->orderedParam['key'] => $this->orderedParam['type'] === 'asc' ? SORT_ASC : SORT_DESC,
            ]);
        }
        $queryTotalRecord = $this->queryEloquent->count();
        $this->resultDatatable = $this->queryEloquent->limit($this->limitPerPage)->offset(($this->pageNumber - 1) * $this->limitPerPage)->asArray()->all();
        if (is_array($this->additionalColumn) && count($this->additionalColumn) > 0 && count($this->resultDatatable) > 0) {
            $keyColumn = array_keys($this->additionalColumn);
            $propertyClass = [
                'pageNumber' => $this->pageNumber,
                'limitPerPage' => $this->limitPerPage,
            ];
            for ($indexRecord = 0; $indexRecord < count($this->resultDatatable); $indexRecord++) {
                if (!isset($this->payload['withoutRowNum'])) {
                    $this->resultDatatable[$indexRecord]['rowNum'] = (($propertyClass['pageNumber'] - 1) * $propertyClass['limitPerPage']) + ($indexRecord + 1);
                }
                for ($indexColumn = 0; $indexColumn < count($this->additionalColumn); $indexColumn++) {
                    $this->setKeyByString($this->resultDatatable[$indexRecord], $keyColumn[$indexColumn], $this->additionalColumn[$keyColumn[$indexColumn]]($this->resultDatatable[$indexRecord], $propertyClass));
                }
            }
        } else {
        }
        return [
            'data' => $this->resultDatatable,
            'recordsFiltered' => $queryTotalRecord,
            'recordsTotal' => $queryTotalRecord,
            'page' => $this->pageNumber,
            'limit' => $this->limitPerPage,
        ];
    }
}
