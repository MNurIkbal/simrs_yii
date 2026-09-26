<?php 
/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use Doco\models\Pendaftaran;
use Doco\models\ObatAlkesPasien;
use SirsCore\models\StokObatAlkes;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\Services\KasirService;
use Doco\models\LoginForm;

class TindakanBmhpHapusProcess extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow()
	{
		$pendaftaran_id = $this->_requestData->get('pendaftaran_id',null);
		$tindakanpelayanan_id = $this->_requestData->post('tindakanpelayanan_id',null);
		$obatalkespasien_id = $this->_requestData->post('obatalkespasien_id',null);
		$alasan_batal = $this->_requestData->post('alasan_pembatalan', null);
		$password = $this->_requestData->post('deleted_by_password', null);
        $modelLogin = new LoginForm();
        $modelLogin->username = Yii::$app->jwt->user->nama_pemakai;
        $modelLogin->password = $password;
        if(!$modelLogin->validate()) {
            return [
                'status' => 422,
                'text' => 'Password Salah',
            ];
        }

		$this->startDBTransaction();

		$no_pendaftaran = Pendaftaran::find()->select('no_pendaftaran')->where(['pendaftaran_id'=>$pendaftaran_id])->scalar();

		if(is_null($no_pendaftaran)){
			Yii::$app->response->statusCode = 500;
			$this->cancelDBTransaction();
            $e = 'No Pendaftaran Tidak Ditemukan';
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => $e
            ]);
		}

		if(!is_null($tindakanpelayanan_id)){
			$dataDetail['tindakan'] = [
				'tindakanpelayanan_id' => $tindakanpelayanan_id
			];
		}

		if(!is_null($obatalkespasien_id)){
			$dataDetail['obat'] = [
				'obatalkespasien_id' => $obatalkespasien_id
			];
		}

		$tagihanBatal = (new KasirService)->batalTagihan([
			'no_pendaftaran' => $no_pendaftaran,
			'instalasi_id' => Yii::$app->jwt->instalasi_id,
			'ruangan_id' => Yii::$app->jwt->ruangan_id
		],$dataDetail,$alasan_batal);
		if(isset($tagihanBatal['meta']) && $tagihanBatal['meta']['code'] >= 400){
			Yii::$app->response->statusCode = 500;
			$this->cancelDBTransaction();
            $e = isset($tagihanBatal['message']) ? $tagihanBatal['message'] : 'Terjadi Kesalahan API';
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => $e
            ]);
        }

	    if(!is_null($obatalkespasien_id)){
	    	$obatalkespasien = ObatAlkesPasien::find(true)->where(['obatalkespasien_id'=>$obatalkespasien_id])->one();
	    	if($obatalkespasien->ruangan_id == Yii::$app->jwt->ruangan_id || $obatalkespasien->status_bmhp == DocoConstants::BMHP_SUDAH_VERIFIKASI){
	    		$this->scrapStok($obatalkespasien);
	    	}
	    }

		$this->commitDBTransaction();
		return [];
	}

	private function scrapStok($obatalkespasien = null)
    {
    	$obatalkespasien_id = $obatalkespasien->obatalkespasien_id;
        try {
            $connection = Yii::$app->db;
            $data_stok = $connection->createCommand("
                SELECT
                    stokobatalkesasal_id , stokobatalkes_id ,
                    ruangan_id, tglkadaluarsa, nobatch, harganetto, persendiscount, jmldiscount,
                    persenppn, jmlppn, persenmargin, jmlmargin,
                    obatalkespasien_id , obatalkes_id , qtystok_in ,
                    qtystok_out , satuankecil_id
                FROM stokobatalkes_t st
                WHERE obatalkespasien_id = $obatalkespasien_id
                ORDER BY st.tglkadaluarsa ASC, st.tglstok_in ASC
            ")
            ->queryAll();

            $insert_stokobatalkes = [];
            foreach ($data_stok as $obat) {
                $qty_in = $obat['qtystok_out'];

                $insert_stokobatalkes[] = [
                    'ruangan_id'          => $obat['ruangan_id'],
                    'tglstok_in'        => date('Y-m-d H:i:s'),
                    'stokoa_aktif' => true,
                    'qtystok_out'         => 0,
                    'obatalkespasien_id'  => $obatalkespasien_id,
                    'obatalkes_id'        => $obat['obatalkes_id'],
                    'tglkadaluarsa'       => $obat['tglkadaluarsa'],
                    'nobatch'             => $obat['nobatch'],
                    'qtystok_in'          => $qty_in,
                    'satuankecil_id'      => $obat['satuankecil_id'],
                    'harganetto'          => $obat['harganetto'],
                    'persendiscount'      => $obat['persendiscount'],
                    'jmldiscount'         => $obat['jmldiscount'],
                    'persenppn'           => $obat['persenppn'],
                    'jmlppn'              => $obat['jmlppn'],
                    'persenmargin'        => $obat['persenmargin'],
                    'jmlmargin'           => $obat['jmlmargin'],
                ];
            }

            StokObatAlkes::batchInsert($insert_stokobatalkes);
            return true;

        } catch (\Exception $e) {
            Yii::error([
                'log' => $e->getMessage()
            ]);
            throw $e;
        } catch (\yii\db\Exception $e) {
            Yii::error([
                'log' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}