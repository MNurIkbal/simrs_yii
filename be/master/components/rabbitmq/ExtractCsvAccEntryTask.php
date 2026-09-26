<?php

namespace app\components\rabbitmq;

use Yii;
use Doco\rabbitmq\task\BaseTask;
use Doco\components\DocoHelpers;

class ExtractCsvAccEntryTask extends BaseTask
{
    public $key_ftp = 'konfigftpakunting';
    
    public function processFlow($data)
    {
        try {
            $tablename = (new $this->components)->getTableName();
            $header = (new $this->components)->headerCsv();
            if($this->using_date == TRUE) {
                $body = (new $this->components)->extractCsv($this->date,$this->sync_type);
            }else{
                $body = (new $this->components)->extractCsv($this->sync_type);
            }

            $tanggal = $this->using_date ? date('ymd',strtotime($this->date)) : date('ymd');
            $filename = $tablename.'_'.$tanggal;

            $params = Yii::$app->params['iniFile'];
            $host = isset($params[$this->key_ftp]) ? $params[$this->key_ftp]['host'] : null;
            $user = isset($params[$this->key_ftp]) ? $params[$this->key_ftp]['username'] : null;
            $password = isset($params[$this->key_ftp]) ? $params[$this->key_ftp]['password'] : null;
            $ftpbasedir = isset($params[$this->key_ftp]) ? $params[$this->key_ftp]['path'] : '';
            $ftpConn = ftp_connect($host);  
            $login = ftp_login($ftpConn, $user, $password);
            ftp_set_option($ftpConn, FTP_USEPASVADDRESS, false);
            ftp_pasv($ftpConn, true);
            ftp_chdir($ftpConn, $ftpbasedir);
            $remote_folder = "jurnal/".$tanggal;
            $dirExistsJurnal = ftp_nlist($ftpConn, "jurnal");
            if ($dirExistsJurnal == false) {
                @ftp_mkdir($ftpConn, "jurnal");
            }
            ftp_chdir($ftpConn, "jurnal");
            $dirExistsDate = ftp_nlist($ftpConn, $tanggal);
            if ($dirExistsDate == false) {
                @ftp_mkdir($ftpConn, $tanggal);
            }
            ftp_chdir($ftpConn, $tanggal);
            $remote_file = $filename.".csv";

            $randString = DocoHelpers::generateRandomString();
            $resourcename = "web/uploads/".$filename."-". $randString .".csv";
            $fp = fopen($resourcename, 'w');
            $enclosure = '"';

            fputs($fp,implode(',',array_map(function($value){ return "\"".$value."\""; },$header))."\n");
            foreach ($body as $row) {
                $fields = [];
                foreach ($row as $item) {
                    $val = str_replace('"','""',$item);
                    $fields[] = sprintf('%s%s%s',$enclosure,$val,$enclosure);
                }
                fputs($fp,implode(',',$fields)."\n");
            }
            if(ftp_put($ftpConn, $remote_file , $resourcename, FTP_ASCII)) {
                unlink($resourcename);
            }

            fclose($fp);

            ftp_close($ftpConn);
        }catch (\Exception $e){
            Yii::warning($e->getMessage());
        }
        return ['message'=>'oke'];
    }
}