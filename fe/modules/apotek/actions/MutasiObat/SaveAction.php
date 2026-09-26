<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\MutasiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers; 

class SaveAction extends Action {

	public function run()
	{
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $request = Yii::$app->request;
        $post = $request->post();
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
        $response['response'] = [
            'text' => 'Obat alkes harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;

        if ($cacheObatAlkes) {
            try {
                $postData = [
                	'mutasi' => [
	                    'tanggal_kirim' => $post['tanggal_kirim'],
	                    'keterangan' => $post['keterangan'],
	                    'ruanganasal_id' => Yii::$app->docoVars->workspace('ruangan_id'),
	                    'ruangantujuan_id' => $post['ruangantujuan_id']
	                ],
                    'mutasiDetail' => $cacheObatAlkes
                ];
                $result = Yii::$app->docoRest->apotek->request('POST', 'mutasi-obat/langsung', [
                    'json' => $postData
                ]);

                $result = json_decode($result->getBody(), true);
                $response['response'] = $result;
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'Obat alkes berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'id' => isset($result['response']['id']) ? $result['response']['id'] : '',
                    'nomor' => isset($result['response']['nomor']) ? $result['response']['nomor'] : '',
                ];
                Yii::$app->cache->delete('pemesanan-obat-' . $id_pegawai. $ruangan_id);
                // delete cache obat
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistemss';
            } catch (\Exception $e) {
                $response['response']['text'] = 'Terjadi kesalah pada sistems';
            }
        }
        return DocoHelpers::response($response, $codeHttp);
	}
}