<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\pendaftaran\models\PasienForm;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\web\Response;
use yii\web\UploadedFile;

use app\modules\pendaftaran\models\VerifikasiBantaranForm;

class RujukanBantaranController extends DocoController
{
    protected $_title = "Informasi Reservasi Rujukan Pembantaran";
    protected $_module = 'pendaftaran/rujukan-bantaran/';
    protected $_restPendaftaran;
    protected $_restMaster;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actions() {
        return [
            'index'                 => 'Doco\pendaftaran\actions\RujukanBantaran\IndexAction',
            'get-data'              => 'Doco\pendaftaran\actions\RujukanBantaran\GetDataAction',
            'periksa'               => 'Doco\pendaftaran\actions\RujukanBantaran\PeriksaAction',
            'detail-rujukan'        => 'Doco\pendaftaran\actions\RujukanBantaran\DetailRujukanAction',
            'kirim-dokumen'         => 'Doco\pendaftaran\actions\RujukanBantaran\KirimDokumenAction',
            'upload-dokumen'        => 'Doco\pendaftaran\actions\RujukanBantaran\UploadDokumenAction',
            'verifikasi-rujukan'    => 'Doco\pendaftaran\actions\RujukanBantaran\VerifikasiRujukanAction',
            'batal-verifikasi'      => 'Doco\pendaftaran\actions\RujukanBantaran\BatalVerifikasiAction',
            'tolak-rujukan'         => 'Doco\pendaftaran\actions\RujukanBantaran\TolakRujukanAction',
            'reject-bantaran'       => 'Doco\pendaftaran\actions\RujukanBantaran\RejectBantaranAction',
            'show-attachment'       => 'Doco\pendaftaran\actions\RujukanBantaran\ShowAttachmentAction',
            'approve-bantaran'      => 'Doco\pendaftaran\actions\RujukanBantaran\ApproveBantaranAction',
            'simpan-soap'           => 'Doco\pendaftaran\actions\RujukanBantaran\SimpanSoapAction',
            'get-soap-data'         => 'Doco\pendaftaran\actions\RujukanBantaran\GetSoapDataAction',
            'hapus-soap'            => 'Doco\pendaftaran\actions\RujukanBantaran\HapusSoapAction',
            'simpan-ttv'            => 'Doco\pendaftaran\actions\RujukanBantaran\SimpanTtvAction',
            'get-ttv-data'          => 'Doco\pendaftaran\actions\RujukanBantaran\GetTtvDataAction',
            'hapus-ttv'             => 'Doco\pendaftaran\actions\RujukanBantaran\HapusTtvAction',
            'pemulangan-tahanan'    => 'Doco\pendaftaran\actions\RujukanBantaran\PemulanganTahananAction',
            'print-qr'              => 'Doco\pendaftaran\actions\RujukanBantaran\PrintQrAction'
        ];
    }

    protected function konfigFtp()
    {
        $env = @parse_ini_file('../config/env/.env', true);

        return [
            'host' => isset($env['konfigftpbantaran']) ? $env['konfigftpbantaran']['host'] : null,
            'user' => isset($env['konfigftpbantaran']) ? $env['konfigftpbantaran']['username'] : null,
            'password' => isset($env['konfigftpbantaran']) ? $env['konfigftpbantaran']['password'] : null,
            'remotePath' => isset($env['konfigftpbantaran']) ? $env['konfigftpbantaran']['path'] : null
        ];
    }
}
