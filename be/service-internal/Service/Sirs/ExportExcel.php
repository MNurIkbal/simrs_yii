<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportExcel extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $headerExcel = is_array($this->headerExcel) ? $this->headerExcel : []; 
        $header = is_array($this->header)? $this->header : []; 
        $footer = is_array($this->footer) ? $this->footer : []; 
        $options = is_array($this->options) ? $this->options : []; 
        $options = is_array($this->options) ? $this->options : []; 
        $customData = false;
        if($this->customData) {
            $customData = true;
        }

        $row = $tmp = [];
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang menyiapkan file excel.',
                'progress' => 80
            ]),
        ]);

        $dataRow = $cacheFiles->get($this->unique_str);
        if(!$customData) {
            if (!empty($dataRow)) {
                $counter = 0;
                foreach ($dataRow as $index => $value) {
                    foreach($header as $row){
                        $title = isset($row['title']) ? $row['title'] : '';
                        $data = isset($row['data']) ? $row['data'] : '';
                        $visible = isset($row['visible']) ? $row['visible'] : true;
                        if($visible) {
                            $tmp[$index][$title] = $value[$data];
                        }
                    }
                }
                $row = $tmp;
            }
        }
        else {
            $row = $dataRow;
        }
        
        $path = 'uploads/'. $this->unique_str .'.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel.',
                'progress' => 85
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel($this->title, $row, $headerExcel, $options, $footer, [], true);
        
        $filePath->save($path);

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil.',
                'progress' => 90
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-ExportExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}