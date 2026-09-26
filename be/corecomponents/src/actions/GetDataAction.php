<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * @Last Modified: Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * @Last Modified Date: 2021-04-04
 * 
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\actions;

use Doco\components\DocoHelpers;
use Yii;
use yii\web\ViewAction;
use Doco\classes\selectModel;


class GetDataAction extends ViewAction
{
	public $model = null;
	public $selected = [];
    public $field_search = [];
    public $is_where = [];
    public $default_where = [];
    public $other_where = [];
    public $orderby = [];
    public $groupby = [];
    public $type = "select2";
    public $one = false;

	public function run()
    {
        $model = $this->model;
        $selected = $this->selected;
        $field_search = $this->field_search;
        $is_where = $this->is_where;
        $default_where = $this->default_where;
        $other_where = $this->other_where;
        $orderby = $this->orderby;
        $groupby = $this->groupby;
        $type = $this->type;
        $one = $this->one;
 
        $option = [
            '_SELECT' => $selected, // kolom yg akan ditampilkan
            'ILIKE' => $field_search, // filter kolom berdasarkan pencarian / term equals 1 char
            'WHERE' => $is_where, // filter kolom berdasarkan pencarian / term equals 1 word
            'DEFAULT' => $default_where, // filter kolom berdasarkan 2 parameter (nama_kolom, value)
            'OTHER' => $other_where, // filter kolom berdasarkan 3 parameter (query filter, nama_kolom, value)
            'ORDERBY' => $orderby, // sorting berdasarkan 2 parameter (nama kolom, ASC/DESC)
            'GROUPBY' => $groupby, // grouping data
        ];

        $response = selectModel::getData($model, $option, $type, $one);

        return $response;
    }
}
