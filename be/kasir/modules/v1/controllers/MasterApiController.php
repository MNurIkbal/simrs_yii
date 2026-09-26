<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
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
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PemberianPiutangView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\KategoriTransaksi;
use app\modules\v1\models\JenisNonTunai;
use app\modules\v1\models\Bank;
use app\modules\v1\models\JasaDokter;
use app\modules\v1\models\TenagaMedisView;
use app\modules\v1\models\ListEdcView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
class MasterApiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Lookup';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data-tipe-transaksi"] = ["GET"];
        $verbs["get-data-karyawan"] = ["GET"];
        $verbs["get-data-vendor"] = ["GET"];
        $verbs["get-data-pasien"] = ["GET"];
        $verbs["get-data-kategori-trx"] = ["GET"];
        $verbs["get-data-pendaftaran-reseptur"] = ["GET"];
        $verbs["get-data-nontunai"] = ["GET"];
        $verbs["get-data-jasa-dokter"] = ["GET"];
        $verbs["get-data-tenaga-medis"] = ["GET"];
        $verbs["get-data-metode-bayar"] = ["GET"];
        $verbs["get-data-bank"] = ["GET"];
        $verbs["get-data-edc"] = ["GET"];
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
            'get-data-tipe-transaksi' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Lookup,
                'selected' => [
                    'lookup_id AS id',
                    'lookup_name as text',
                    'lookup_id',
                    'lookup_type',
                    'lookup_name',
                    'lookup_value'
                ],
                'field_search' => [
                    'lookup_name',
                    'lookup_value'
                ],
            ],
            'get-data-karyawan' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Pegawai,
                'selected' => [
                    'pegawai_id AS id',
                    'nama_pegawai as text',
                    'pegawai_id',
                    'nama_pegawai',
                    'nomorindukpegawai'
                ],
                'field_search' => [
                    'nama_pegawai',
                    'nomorindukpegawai'
                ],
            ],
            'get-data-vendor' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Supplier,
                'selected' => [
                    'supplier_id AS id',
                    'supplier_nama as text',
                    'supplier_id',
                    'supplier_nama',
                    'supplier_kode',
                    'supplier_namalain',
                    'supplier_alamat'
                ],
                'field_search' => [
                    'supplier_nama',
                    'supplier_kode'
                ],
            ],
            'get-data-pasien' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Pasien,
                'selected' => [
                    'pasien_id AS id',
                    'nama_pasien as text',
                    'pasien_id',
                    'nama_pasien',
                    'no_rekam_medik',
                    'tgl_rekam_medik',
                    'jenisidentitas',
                    'no_identitas_pasien',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'alamat_pasien'
                ],
                'field_search' => [
                    'nama_pasien',
                    'no_rekam_medik'
                ],
            ],
            'get-data-kategori-trx' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new KategoriTransaksi,
                'selected' => [
                    'kategoritransaksi_id AS id',
                    'kategoritransaksi_nama as text',
                    'kategoritransaksi_id',
                    'kategoritransaksi_nama',
                    'kategoritransaksi_kode'
                ],
                'field_search' => [
                    'kategoritransaksi_nama',
                    'kategoritransaksi_kode'
                ],
            ],
            'get-data-pendaftaran-reseptur' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new PemberianPiutangView,
                'selected' => [
                    '*'
                ],
                'field_search' => [
                    'no_pendaftaran',
                    'nama_pasien',
                    'no_rekam_medik',
                    'no_resep'
                ],
            ],
            'get-data-metode-bayar' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Lookup,
                'selected' => [
                    'lookup_id AS id',
                    'lookup_name as text',
                    'lookup_id',
                    'lookup_name',
                ],
                'field_search' => [
                    'lookup_name',
                ],
                'default_where' => [
                    ['lookup_type', 'metode_bayar']
                ]
            ],
            'get-data-nontunai' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new JenisNonTunai,
                'selected' => [
                    'jenisnontunai_id',
                    'kode',
                    'nama',
                    'bank_id',
                ],
                'field_search' => [
                    'kode',
                    'nama'
                ],
            ],
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
            'get-data-jasa-dokter' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new JasaDokter,
                'selected' => [
                    'jasadokter_id AS id',
                    'jasadokter_nama as text',
                    'jasadokter_kode',
                    'jasadokter_id',
                    'jasadokter_nama',
                ],
                'field_search' => [
                    'jasadokter_kode',
                    'jasadokter_nama',
                ],
                'default_where' => [
                    ['is_active', true]
                ]
            ],
            'get-data-tenaga-medis' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new TenagaMedisView,
                'selected' => [
                    'pegawai_id AS id',
                    'nama_pegawai as text',
                    'pegawai_id',
                    'nama_pegawai',
                    'nomorindukpegawai'
                ],
                'field_search' => [
                    'nama_pegawai',
                    'nomorindukpegawai'
                ],
            ],
            'get-data-edc' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new ListEdcView,
                'selected' => [
                    'edclist_id AS id',
                    'edclist_namamesin as text',
                    'edclist_id',
                    'edclist_namamesin',
                    'edclist_kode'
                ],
                'field_search' => [
                    'edclist_namamesin',
                    'edclist_kode'
                ],
                'default_where' => [
                    ['is_active', true]
                ]
            ],
            'get-data-instalasi' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Instalasi,
                'selected' => [
                    'instalasi_id AS id',
                    'instalasi_nama as text',
                    'instalasi_id',
                    'instalasi_nama',
                ],
                'field_search' => [
                    'instalasi_nama',
                ],
                'orderby' => [
                    ['instalasi_nama', 'ASC']
                ]
            ],
            'get-data-ruangan' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Ruangan,
                'selected' => [
                    'ruangan_id AS id',
                    'ruangan_nama as text',
                    'ruangan_id',
                    'ruangan_nama',
                ],
                'field_search' => [
                    'ruangan_nama',
                ],
                'orderby' => [
                    ['ruangan_nama', 'ASC']
                ]
            ],
            'get-data-metode-transaksi' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Lookup,
                'selected' => [
                    'lookup_value AS id',
                    'lookup_name as text',
                    'lookup_id',
                    'lookup_name',
                    'lookup_value',
                ],
                'field_search' => [
                    'lookup_name',
                ],
                'default_where' => [
                    ['lookup_type', 'metode_transaksi']
                ]
            ],
        ];
    }
}