<?php

namespace Doco\Libraries;

use Doco\Repositories\RootRepositories;
use Doco\components\DocoHelpers;

use Yii;

class DocoTableSP
{
    /** @var String $spName */
    private $spName = null;

    /** @var Array $param */
    protected $param = [];

    /** @var Array $originParamRequest */
    protected $originParamRequest = [];

    /** @var Integer $limit */
    protected $limit;

    /** @var Integer $page */
    protected $page;

    /** @var Array $additionalColumn */
    protected $additionalColumn = [];

    /** @var Array $unsetVar */
    protected $unsetVar = null;

    /** @var Boolean $withRowNum */
    protected $withRowNum = true;

    /** @var Boolean $withRowNum */
    protected $withOrder = true;

    function __construct($spName, $param, $additionalFilter, $option = ['withOrder' => true])
    {
        $this->spName = $spName;
        if (isset($option['withOrder']) && $option['withOrder']) {
            $this->withOrder = true;
            $this->order = !isset($param['order']) || (isset($param['order']) && is_null($param['order'])) ? '' : $param['order'];
        } else {
            $this->withOrder = false;
            $this->order = null;
        }
        if (isset($option['rowNum'])) {
            $this->withRowNum = $option['rowNum'];
        }
        $this->limit = isset($param['per-page']) ? $param['per-page'] : 10;
        $this->page = isset($param['page']) ? $param['page'] : 1;
        $this->originParamRequest = $param;
        $this->param = $additionalFilter;
    }

    /**
     * set additional column
     *
     * @param Func $additionalColumn
     * @return Class
     * @author Tsani Nashrullah
     **/
    public function setAdditionalColumn($additionalColumn)
    {
        $this->additionalColumn = $additionalColumn;
        return $this;
    }
    /**
     * set unsetvar
     *
     * @param Array $unserVar
     * @return Class
     * @author Tsani Nashrullah
     **/
    public function unsetVar($unsetVar)
    {
        $this->unsetVar = $unsetVar;
        return $this;
    }

    /**
     * This function render result of count
     *
     * @param String $type || 'count' or 'rows'
     * @return Array/Integer
     * @author Tsani Nashrullah
     **/
    private function executeDatatable($type = 'rows')
    {
        $result = [];
        if ($type === 'rows') {
            $param = [
                'rows',
                $this->limit,
                ($this->page - 1) * $this->limit
            ];
            if ($this->withOrder) {
                $param['order'] = $this->order;
            }
            // $this->order
            $result = (new RootRepositories)->executeSp([
                'command' => $this->spName,
                'param' => array_merge($this->param, $param),
                'allRecord' => true,
            ]);
        } else if ($type === 'count') {
            $param = [
                'count',
                NULL,
                NULL
            ];
            if ($this->withOrder) {
                $param['order'] = $this->order;
            }
            $result = (new RootRepositories)->executeSp([
                'command' => $this->spName,
                'param' => array_merge($this->param, $param),
            ]);
            if (isset($result['count'])) {
                $result = $result['count'];
            } else {
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * render Datatable SP
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function make()
    {
        $recordsTotal = $this->executeDatatable('count');
        $recordsFiltered = $recordsTotal;
        $records = $this->executeDatatable();
        if (((is_array($this->additionalColumn) && count($this->additionalColumn) > 0) || (is_array($this->unsetVar) && count($this->unsetVar) > 0) || $this->withRowNum) && count($records) > 0) {
            if (is_array($this->additionalColumn) && count($this->additionalColumn) > 0 || $this->withRowNum) {
                $keyColumn = array_keys($this->additionalColumn);
                $no = 1;
                for ($indexRecord = 0; $indexRecord < count($records); $indexRecord++) {
                    if ($this->withRowNum) {
                        $records[$indexRecord]['rowNum'] = (($this->page - 1) * $this->limit) + $no;
                        $no += 1;
                    }
                    if (is_array($this->additionalColumn) && count($this->additionalColumn) > 0) {
                        for ($indexColumn = 0; $indexColumn < count($this->additionalColumn); $indexColumn++) {
                            (new DocoHelpers)->setKeyByString($records[$indexRecord], $keyColumn[$indexColumn], $this->additionalColumn[$keyColumn[$indexColumn]]($records[$indexRecord]));
                        }
                    }
                }
            }

            if (is_array($this->unsetVar) && count($this->unsetVar) > 0) {
                for ($indexColumn = 0; $indexColumn < count($this->unsetVar); $indexColumn++) {
                    for ($indexRecord = 0; $indexRecord < count($records); $indexRecord++) {
                        unset($records[$indexRecord][$this->unsetVar[$indexColumn]]);
                    }
                }
            }
        }
        $data = $records;
        return compact('data', 'recordsFiltered', 'recordsTotal');
    }

    /**
     * function to mapping payload
     * 
     * @param Array $payload
     * @param Array $payloadDatatable
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function mapPayload($payload, $payloadDatatable)
    {
        $result = [];
        foreach ($payloadDatatable as $keyPayload => $datatableParam) {
            if (is_callable($datatableParam)) {
                $result = array_merge($result, $datatableParam(isset($payload[$keyPayload]) ? $payload[$keyPayload] : null));
            } else if (!is_callable($datatableParam) && isset($payload[$datatableParam])) {
                $result[$datatableParam] = $payload[$datatableParam];
            } else {
                $result[$datatableParam] = null;
            }
        }
        return $result;
    }
}
