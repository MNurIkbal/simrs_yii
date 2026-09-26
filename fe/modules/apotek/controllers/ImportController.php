<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use app\components\DocoController;

class ImportController extends DocoController {
	protected $allowAction = ['*'];

    public function actions() {
        return [
        	'obat' => 'Doco\apotek\actions\import\ObatAction',
            'download-template' => 'Doco\apotek\actions\import\DownloadTemplateAction'
        ];
    }
}