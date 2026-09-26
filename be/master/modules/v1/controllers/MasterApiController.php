<?php
/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\Traits\Select2Trait;

use Doco\actions\GetDataAction;
use app\modules\v1\models\Bank;
use app\modules\v1\models\KomponenTarif;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\KamarRuangan;
use Doco\models\KelasPelayanan;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\KonfigGudang;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\RuanganView;

class MasterApiController extends DocoActiveController
{
    use Select2Trait;

    public $modelClass = 'app\modules\v1\models\Lookup';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["save"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        /**
         * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
         * 
         * DATA ATTRIBUTE YANG BISA DIGUNAKAN
         * 
         * ---------------------------------------------------------------------
         * selected : kolom yg akan ditampilkan
         * contoh penggunaan : 
         * selected => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * field_search : filter kolom berdasarkan pencarian / term equals 1 char
         * contoh penggunaan : 
         * field_search => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * is_where : filter kolom berdasarkan pencarian / term equals 1 word
         * contoh penggunaan : 
         * is_where => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * default_where : filter kolom berdasarkan 2 parameter (nama_kolom, value)
         * contoh penggunaan : 
         * default_where => ['nama_kolom', 'value'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * other_where : filter kolom berdasarkan 3 parameter (query filter, nama_kolom, value)
         * contoh penggunaan :
         * other_where => ['ILIKE/WHERE/LIKE/ETC', 'nama_kolom', 'value'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * orderby : sorting berdasarkan 2 parameter (nama kolom, ASC/DESC)
         * contoh penggunaan :
         * orderby => ['nama_kolom', ASC/DESC]
         * ---------------------------------------------------------------------
         * 
         */
        return [
            'get-data-bank' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Bank,
                'selected' => [
                    'bank_id AS id',
                    'nama_bank as text',
                    'bank_id',
                    'nama_bank',
                ],
                'field_search' => [
                    'nama_bank',
                ],
            ],
            'get-list-komponen' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new KomponenTarif,
                'selected' => [
                    'komponentarif_id AS id',
                    'komponentarif_nama as text',
                    'komponentarif_id',
                    'komponentarif_nama',
                    'komponentarif_kode',
                ],
                'field_search' => [
                    'komponentarif_nama',
                    'komponentarif_kode',
                ],
            ],
            'get-list-dokter' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Pegawai,
                'selected' => [
                    'pegawai_id AS id',
                    'nama_pegawai as text',
                    'pegawai_id',
                    'nama_pegawai',
                ],
                'field_search' => [
                    'nama_pegawai',
                ],
                'default_where' => [
                    ['kelompokpegawai_id', 1],
                    ['is_active', true],
                    ['is_deleted', false]
                ]
            ],
            'get-list-kamar' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new KamarRuangan,
                'selected' => [
                    'kamarruangan_id AS id',
                    'kamarruangan_nokamar as text',
                    'kamarruangan_id',
                    'kamarruangan_nokamar',
                ],
                'field_search' => [
                    'kamarruangan_nokamar',
                ],
                'default_where' => [
                    ['is_active', true],
                    ['is_deleted', false]
                ]
            ],
            'get-list-kelas' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new KelasPelayanan,
                'selected' => [
                    'kelaspelayanan_id AS id',
                    'kelaspelayanan_nama as text',
                    'kelaspelayanan_id',
                    'kelaspelayanan_nama',
                ],
                'field_search' => [
                    'kelaspelayanan_nama',
                ],
                'default_where' => [
                    ['is_active', true],
                    ['is_deleted', false]
                ]
            ],
            'get-list-carabayar' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new CaraBayar,
                'selected' => [
                    'carabayar_id AS id',
                    'carabayar_nama as text',
                    'carabayar_id',
                    'carabayar_nama',
                ],
                'field_search' => [
                    'carabayar_nama',
                ],
                'default_where' => [
                    ['is_active', true],
                    ['is_deleted', false]
                ]
            ],
            'get-list-penjamin' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Penjamin,
                'selected' => [
                    'penjamin_id AS id',
                    'penjamin_nama as text',
                    'penjamin_id',
                    'penjamin_nama',
                ],
                'field_search' => [
                    'penjamin_nama',
                ],
                'default_where' => [
                    ['is_active', true],
                    ['is_deleted', false],
                    ['carabayar_id', Yii::$app->request->get('carabayar_id', null)]
                ]
            ],
            'get-konfig-farmasi' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new KonfigFarmasi,
                'selected' => [
                    '*',
                ],
                'default_where' => [
                    ['konfigfarmasi_id', 1]
                ],
                'type' => '',
                'one' => true
            ],
            'get-instalasi' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Instalasi,
                'selected' => [
                    'instalasi_id',
                    'instalasi_nama'
                ],
                'default_where' => [
                    ['is_active', true],
                    ['is_deleted', false],
                ],
                'type' => ''
            ],
            'get-ruangan' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Ruangan,
                'selected' => [
                    'ruangan_id',
                    'ruangan_nama'
                ],
                'default_where' => [
                    ['is_active', true],
                    ['is_deleted', false],
                    ['instalasi_id',Yii::$app->request->get('instalasi_id',null)],
                ],
                'field_search' => [
                    'ruangan_nama',
                ],
            ],
            'get-konfig-gudang' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new KonfigGudang,
                'selected' => [
                    '*',
                ],
                'default_where' => [
                    ['konfiggudang_id', 1]
                ],
                'type' => '',
                'one' => true
            ],
            'get-list-instalasi-ruangan' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new RuanganView,
                'selected' => [
                    'instalasi_id', 
                    'instalasi_nama', 
                    'ruangan_id', 
                    'ruangan_nama'
                ],
                'field_search' => [
                    'instalasi_nama',
                    'ruangan_nama',
                ],
            ],
        ];
    }
}
