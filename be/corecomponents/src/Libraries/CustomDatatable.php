<?php
namespace Doco\Libraries;

/**
 * Datatable class, to generate datatable response
 * @author Tsani Nashrullah
 */
class CustomDatatable
{
    /** @var Array $resultDatatable */
    private $resultDatatable = [];

    /** @var Eloquent $queryEloquent */
    private $queryEloquent;

    /** @var String $searchKey */
    private $searchKey = '';

    /** @var Array $orderedParam
     * type 1 is ASC
     * type 2 is DESC
     * @author Tsani Nashrullah
     */
    private $orderedParam = [
        'key' => '',
        'type' => '1',
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

    public function __construct($modelClass, $payload)
    {
        $this->queryEloquent = $modelClass;
        $this->searchKey = isset($payload['searchKey']) ? $payload['searchKey'] : null;
        $this->payload = $payload;
        $this->pageNumber = $payload['page'];
        $this->limitPerPage = isset($payload['limit']) ? $payload['limit'] : 10;
        if (isset($payload['orderedParam']['key']) && $payload['orderedParam']['key'] !== '' && in_array($payload['orderedParam']['type'], ['1', '2'])) {
            $this->orderedParam = $payload['orderedParam'];
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
        if (!is_null($this->searchKey)) {
            // $stringExplode = preg_split("/(&|\/)/", $this->searchKey);
            // for ($i = 0; $i < count($stringExplode); $i++) {
            $eachSearchKey = strtolower(trim($this->searchKey));
            if ($eachSearchKey !== '') {
                for ($i = 0; $i < count($this->payload['columns']); $i++) {
                    if (isset($this->payload['columns'][$i]['searchable']) && (bool) $this->payload['columns'][$i]['searchable']) {
                        $this->queryEloquent->orWhere(['like', 'lower(' . $this->payload['columns'][$i]['data'] . ')', $eachSearchKey]);
                    }
                }
                if (!is_null($this->customFilter)) {
                    call_user_func($this->customFilter, $this->queryEloquent, $eachSearchKey);
                }
            }
            // }
        }
        if ($this->orderedParam['key'] !== '') {
            $this->queryEloquent = $this->queryEloquent->orderBy([
                $this->orderedParam['key'] => $this->orderedParam['type'] === '1' ? SORT_ASC : SORT_DESC,
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
            for ($indexColumn = 0; $indexColumn < count($this->additionalColumn); $indexColumn++) {
                for ($indexRecord = 0; $indexRecord < count($this->resultDatatable); $indexRecord++) {
                    $this->setKeyByString($this->resultDatatable[$indexRecord], $keyColumn[$indexColumn], $this->additionalColumn[$keyColumn[$indexColumn]]($this->resultDatatable[$indexRecord], $propertyClass));
                }
            }
        }
        return [
            'records' => $this->resultDatatable,
            'totalRecords' => $queryTotalRecord,
            'page' => $this->pageNumber,
            'limit' => $this->limitPerPage,
        ];
    }
}
