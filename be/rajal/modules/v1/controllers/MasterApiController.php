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
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\RuanganView;

use app\modules\v1\models\DokterView;
use app\modules\v1\models\TarifTotalRs;

class MasterApiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Lookup';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-jadwal-poli"] = ["GET"];
        $verbs["get-all-dokter"] = ["GET"];
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
         * orderby => [['nama_kolom', ASC/DESC]]
         * ---------------------------------------------------------------------
         * 
         */
        $request = Yii::$app->request;
        return [
            'get-jadwal-poli' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new Ruangan,
                'selected' => [
                    'ruangan_id',
                    'ruangan_nama',
                ],
                'field_search' => [
                    'ruangan_nama',
                ],
                'default_where' => [
                    ['instalasi_id', 1],
                    ['is_active', true],
                    ['is_deleted', false],
                ]
            ],
            'get-all-dokter' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new PegawaiMasterView,
                'selected' => [
                    'pegawai_id',
                    'nama_pegawai',
                ],
                'field_search' => [
                    'nama_pegawai',
                ],
                'default_where' => [
                    ['kelompokpegawai_id', 1],
                ]
            ],
            'list-depo' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new RuanganView,
                'selected' => [
                    'ruangan_id',
                    'ruangan_nama',
                ],
                'field_search' => [
                    'ruangan_nama',
                ],
                'default_where' => [
                    ['instalasi_id', DocoConstants::INSTALASI_FARMASI],
                    ['is_active', true],
                ],
                'orderby' => [['ruangan_nama', 'ASC']]
            ],
            'get-all-new-dokter' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new DokterView(),
                'selected' => [
                    'pegawai_id',
                    'nama_pegawai',
                ],
                'field_search' => [
                    'nama_pegawai',
                ],
                'orderby' => [
                    ['nama_pegawai', 'ASC']
                ],
                'groupby' => [
                    'pegawai_id',
                    'nama_pegawai',
                ],
            ],
            'get-pemberi-instruksi' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new DokterView(),
                'selected' => [
                    'pegawai_id',
                    'nama_pegawai',
                ],
                'field_search' => [
                    'nama_pegawai',
                ],
                'default_where' => [
                    ['instalasi_id', DocoConstants::INST_ID_RD],
                    ['ruangan_id', $request->get('ruangan_id')],
                ],
                'orderby' => [
                    ['nama_pegawai', 'ASC']
                ],
                'groupby' => [
                    'pegawai_id',
                    'nama_pegawai',
                ],
            ],
            'get-fee-konsul' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => (new TarifTotalRs([
                    'extParam' => [
                        $request->get('ruangan_id'),
                        $request->get('penjamin_id'),
                        $request->get('kelaspelayanan_id'), 
                    ]
                ])),
                'selected' => [
                    'daftartindakan_id',
                    'daftartindakan_nama',
                ],
                'field_search' => [
                    'daftartindakan_nama',
                ],
                'default_where' => [
                    ['is_konsultasi', true],
                ],
                'orderby' => [
                    ['daftartindakan_nama', 'ASC']
                ],
                'groupby' => [
                    'daftartindakan_id',
                    'daftartindakan_nama',
                ],
            ],
            'get-ruangan' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new RuanganView(),
                'selected' => [
                    'ruangan_id',
                    'ruangan_nama',
                ],
                'default_where' => [
                    ['instalasi_id', 1],
                    ['is_active', true],
                ],
                'field_search' => [
                    'ruangan_nama',
                ],
                'orderby' => [
                    ['ruangan_nama', 'ASC']
                ],
                'groupby' => [
                    'ruangan_id',
                    'ruangan_nama',
                ],
            ],
        ];
    }
}