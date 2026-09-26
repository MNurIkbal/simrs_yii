<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use yii\helpers\Html;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class ListDataMarginAction extends Action {
    protected $_module = '/master/margin-harga/';

    public function run($data, $no_urut, $draw) {
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $result['draw'] = $draw;
        $result['data'] = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $no_urut++;
                $primaryKey = DocoHelpers::encrypt($key);
                $dataCache = [
                    'rowNum' => $no_urut,
                    'harga_min' => DocoHelpers::formatNumber($value['harga_min']),
                    'harga_max' => DocoHelpers::formatNumber($value['harga_max']),
                    'margin' => $value['margin'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'class' => 'btn btn-danger btn-sm delete-cache',
                            'data-id' => $primaryKey,
                            'data-margin' => !empty($value['konfigmargin_id']) ? $value['konfigmargin_id'] : '-',
                            'data-action' => Url::to([$this->_module .'delete-cache', 'id' => $primaryKey
                            ]),
                        ]
                    )
                ];

                $resetCache[$value['margin']] = [
                    'harga_min' => $value['harga_min'],
                    'harga_max' => $value['harga_max'],
                    'margin' => $value['margin'],
                ];
                $result['data'][] = $dataCache;
            }
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = $no_urut;
            $result['draw'] = $draw;
        }
        return $result;
    }
}