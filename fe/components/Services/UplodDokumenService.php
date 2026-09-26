<?php

namespace app\components\Services;

use Yii;
use app\components\Services\BaseService;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Html;

class UplodDokumenService extends BaseService
{
   public function __construct()
   {
      $this->service = Yii::$app->docoRest->rm;
   }

   public function uploadDokumen($params)
   {
      $response = $this->service->post('allow/upload-dokumen-pasien', [
         'form_params' => $params
      ]);
      $bodyAkses = json_decode($response->getBody(), true);
      return $bodyAkses;
   }

   public function uploadFile($file, $path, $fileName, $no_pendaftaran)
   {
      // Init
      ini_set('post_max_size', '20M');
      ini_set('upload_max_size', '20M');

      if (!empty($file)) {
         $webroot = \Yii::getAlias('@baseFileUrl');
         $size = $file->size;
         $ext = end(explode(".", $file->name));
         if ($size > DocoConstants::MAX_UPLOAD_DOKUMEN) {
            return [
               'title' => 'Proses gagal !',
               'text' => 'File maksimal 20 mb !',
               'status' => false
            ];
         }
         if(DocoHelpers::uploadFileFtp(self::konfigFtp(), $path, $file, $fileName)){
            return [
               'file_name' => $fileName,
               'status' => true
            ];
         } else {
            if (!file_exists($webroot . $path)) {
               mkdir($webroot . $path, 0777, true);
            }
            $file->saveAs($webroot . $path . $fileName);

            return [
               'file_name' => $fileName,
               'status' => true
            ];
         }
      } else {
         return [
            'file_name' => '',
            'status' => false,
            'title' => '',
            'text' => '',
         ];
      }
   }

   public function getDokumenList($payload)
   {
        $request = Yii::$app->request;
        $result = $this->guzzleExec($this->service, [
         'url' => 'allow/get-dokumen-list',
         'payload' => [
            'query' => $payload
         ]
      ]);
      $no = 0;
      $row = [];
      $isHide = $request->get('isHide');
      $isHide = $isHide == 1 ? true : false;

      if (!empty($result['data'])) {
         foreach ($result['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['nama_dokumen'] = isset($value['nama_dokumen']) ? $value['nama_dokumen'] : '';
            $value['nama_pegawai'] = isset($value['nama_pegawai']) ? $value['nama_pegawai'] : '';
            $value['doc_date'] = isset($value['doc_date']) ? date('d-M-Y', strtotime($value['doc_date'])) : ''; 
            $value['ruangan_nama'] = isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $buttonDelete = '';
            if(!$isHide) {
                $buttonDelete = Html::button(
                    '<i class="fa fa-trash"></i>',
                    [
                       'class' => 'btn btn-sm btn-danger delete mb-2',
                       'data-id' => $value['dokumenupload_id'],
                       'data-parent' => $value['filename']
                    ]
                 );
            }
            $value['aksi'] = $buttonDelete;
            
            $fileType = end(explode('.', $value['filename']));
            if (in_array($fileType, $payload['fileAction']['detail'])) {
               $value['aksi'] .= Html::button(
                  '<i class="fa fa-eye"></i>',
                  [
                     'class' => 'btn btn-info btn-sm btn-view-img mb-2 ml-2',
                     'data-toggle' => 'modal',
                     'data-target' => '#modal-preview-img',
                     'action' => '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id']
                  ]
               );
            } else if (in_array($fileType, $payload['fileAction']['preview'])) {
               $value['aksi'] .= Html::button(
                  '<i class="fa fa-eye"></i>',
                  [
                     'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan preview mb-2 ml-2',
                     'data-target' => '#modal-preview',
                     'data-url' => '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id'] . '&filetype=pdf',
                  ]
               );
            } else if (in_array($fileType, $payload['fileAction']['download'])) {
               $value['aksi'] .= Html::a(
                  '<i class="fa fa-download"></i>',
                  '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id'] . '&filetype=' . $fileType,
                  [
                     'class' => 'btn btn-info btn-sm mb-2 ml-2',
                     'target' => '_blank'
                  ]
               );
            } else {
               $value['aksi'] .= Html::a(
                  '<i class="fa fa-download"></i>',
                  '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id'] . '&filetype=doc', // force set params filetype as doc agar bisa di download
                  [
                     'class' => 'btn btn-info btn-sm mb-2 ml-2',
                     'target' => '_blank'
                  ]
               );
            }

            $value['aksi_non_pendaftaran'] = '';
            if(!empty($payload['norm']))
            {
               if (in_array($fileType, $payload['fileAction']['detail'])) {
                  $value['aksi_non_pendaftaran'] .= Html::button(
                     '<i class="fa fa-eye"></i>',
                     [
                        'class' => 'btn btn-info btn-sm btn-view-img mb-2 ml-2',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-preview-img',
                        'action' => '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id']
                     ]
                  );
               } else if (in_array($fileType, $payload['fileAction']['preview'])) {
                  $value['aksi_non_pendaftaran'] .= Html::button(
                     '<i class="fa fa-eye"></i>',
                     [
                        'class' => 'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan preview mb-2 ml-2',
                        'data-target' => '#modal-preview',
                        'data-url' => '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id'] . '&filetype=pdf',
                     ]
                  );
               } else if (in_array($fileType, $payload['fileAction']['download'])) {
                  $value['aksi_non_pendaftaran'] .= Html::a(
                     '<i class="fa fa-download"></i>',
                     '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id'] . '&filetype=' . $fileType,
                     [
                        'class' => 'btn btn-info btn-sm mb-2 ml-2',
                        'target' => '_blank'
                     ]
                  );
               } else {
                  $value['aksi_non_pendaftaran'] .= Html::a(
                     '<i class="fa fa-download"></i>',
                     '/api/upload-dokumen/preview-dokumen?dokumenupload_id=' . $value['dokumenupload_id'] . '&filetype=doc', // force set params filetype as doc agar bisa di download
                     [
                        'class' => 'btn btn-info btn-sm mb-2 ml-2',
                        'target' => '_blank'
                     ]
                  );
               }
            }
            if ($value['is_eklaim'] == true) {
               $value['aksi'] .= Html::button(
                  '<span style="color: white"><b>Dokumen Eklaim</b></span>',
                  [
                     'class' => 'btn btn-sm btn-rounded mb-2 ml-2',
                     'disabled' => false,
                     'style' => 'background-color: #4a6785'
                  ]
               );
            }

            $row[$key] = $value;
         }
         $result['data'] =  $row;
      }

      return isset($result['data']) ? $result : [
         'recordsFiltered' => 0,
         'recordsTotal' => 0,
         'data' => [],
      ];
   }

   public function deleteUpload($pendaftaran_id, $parent)
   {
      $response = $this->service->request('DELETE', 'allow/delete-upload', [
         'query' => ['id' => $pendaftaran_id]
      ]);
      $response = json_decode($response->getBody(), true);
      $file = isset($response['response']['data']) ? $response['response']['data'] : null;
      $path = isset($response['response']['path']) ? $response['response']['path'] : null;
      $webroot = Yii::getAlias('@baseFileUrl');
      if(DocoHelpers::deleteFileFtp(self::konfigFtp(), $path, $file)){
         $response['response'] = [
               'title' => 'Proses Berhasil !',
               'text' => 'Data berhasil dihapus!'
         ];
      } else {
         if (file_exists($webroot . $path . $file)) {
            unlink($webroot . $path . $file);
         }
         $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
         ];
      }

      return DocoHelpers::response($response);
   }

   public function getDetailDokumen()
   {
      $getDataDokumen = $this->guzzleExec($this->service, [
         'url' => 'allow/get-detail-dokumen',
         'payload' => [
            'query' => [
               'dokumenupload_id' => Yii::$app->request->get('dokumenupload_id')
            ],
         ],
      ]);
      $filePath = $getDataDokumen['path'] . $getDataDokumen['filename'];
      $filePathOld = Yii::getAlias('@baseFileUrl') . $getDataDokumen['path'] . $getDataDokumen['filename'];

      return [
         'dataDokumen' => $getDataDokumen,
         'filePath' => $filePath,
         'filePathOld' => $filePathOld
      ];
   }

   public function konfigFtp()
   {
      $env = @parse_ini_file('../config/env/.env', true);
      return [
         'host' => isset($env['konfigftp']) ? $env['konfigftp']['host'] : null,
         'user' => isset($env['konfigftp']) ? $env['konfigftp']['username'] : null,
         'password' => isset($env['konfigftp']) ? $env['konfigftp']['password'] : null,
         'remotePath' => isset($env['konfigftp']) ? $env['konfigftp']['path_doc_upload'] : null
      ];
   }
}
