<?php
//Author: Ardi Pratama

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronReSyncController extends Controller
{

    // protected $allowAction = ['*'];
	public function actionPasien()
	{
        try {
            $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $request = $_restPendaftaran->get('pasien-sync/save-batch');

            echo 'Sinkronisasi Pasien Berhasil';
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
	}

	public function actionPendaftaran()
	{
        try {
            $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $request = $_restPendaftaran->get('pendaftaran-sync/save-batch');

            echo 'Sinkronisasi Pendaftaran Berhasil';
        } catch (RequestException $e) {
        	echo $e;
            echo "Proses gagal".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
	}
}