<?php

use yii\db\Migration;

/**
 * Class m230926_091329_migrate_GLBJ268_pendaftaran_t
 */
class m230926_091329_migrate_GLBJ268_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query =
            "CREATE OR REPLACE FUNCTION public.pendaftaran_t()
        RETURNS trigger
        LANGUAGE plpgsql
       AS \$function\$
              
              DECLARE
                  vPrefix VARCHAR;
                vNumber VARCHAR;
                  -- Penambahan Triger Dari Yaya
                  -- Tanggal 08-08-2019
                  dataAntrian VARCHAR;
                  dataKarcis VARCHAR;
                  dataRujukan VARCHAR;
                  dataPenanggung VARCHAR;
                  dataAsuransi VARCHAR;
                  dataPasien VARCHAR;
                  dataOrderMcu VARCHAR;
                  dataOrderLab VARCHAR;
              
                  paramJson VARCHAR;
                  
                  -- Get New Pasien
                  pasienId INTEGER;
                  
                  noAsuransi VARCHAR;
                  idAsuransi INTEGER;
                  idPasienAsuransi INTEGER;
                  -- Generate Id
                  rujukanId INTEGER;
                  antrianId INTEGER;
                  penanggungId INTEGER;
                  
                  -- Antrian Active
                  isActive BOOLEAN;
                  isGenerateConfig BOOLEAN;
                  -- Count Tagihan
                  countTagihan INTEGER;
                  countPenunjang INTEGER;
                      countException INTEGER;
                  --antrian
                  v_konfigantrian INTEGER;
                  
                  -- Kebutuhan untuk penunjang
                  vPenunjang VARCHAR;
                  noAntrian VARCHAR;
                  idPenunjang INTEGER;
                  
                  -- Pendaftaran Online
                  
                  -- Update untuk pasienadmisi ranap
                  vadmisi VARCHAR;
                  vmasukkamar VARCHAR;
                  vpasienadmisi_id int4;
                  vuser_id int4;
                  vpendaftaran_id int4;
                  vcarabayar_id int4;
                  vpenjamin_id int4;
                  vkettempattidur_id int4; 
                  vkelahiran_id int4;
                  vpendaftaranasal_id int4;
                      
                      -- Update kebutuhan BSL
                      noRekamMedik VARCHAR;
                      dataRekamMedik VARCHAR;
                      
                      -- Kebutuhan antrian
                      vnourut BOOLEAN;
                      
                      -- Kebutuhan SY Penanggung Biaya
                      dataPenanggungBiaya VARCHAR;
                      idPenanggungBiaya INTEGER;
                      penanggungbiayaId INTEGER;
                      
                      -- Kebutuhan SY Keluarga Pasien
                      dataKeluargaPasien VARCHAR;
                      idKeluargaPasien INTEGER;
                      keluargapasienId INTEGER;
                      
                      -- Multi Payer 3462
                      dataAdditionalPayer VARCHAR;
                      isMultiPayer BOOLEAN;
                      
                      noAsuransi1 VARCHAR;
                  idAsuransi1 INTEGER;
                  idPasienAsuransi1 INTEGER;
                      
                      noAsuransi2 VARCHAR;
                  idAsuransi2 INTEGER;
                  idPasienAsuransi2 INTEGER;
                      
                      idInstalasi INTEGER;
                      IsPenunjang BOOLEAN;
                      antrianIdPoli INTEGER;
                      
                      vnorekammedik VARCHAR;
                      dataAntrianJkn VARCHAR;
                      
                      
              BEGIN
                  vcarabayar_id := NEW.carabayar_id;
                  vpenjamin_id := NEW.penjamin_id;
                      
                  
       -- SELECT 
       --     CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(substring(no_pendaftaran FROM '[0-9]+')), 6) AS INT), 0) + 1 AS                VARCHAR(6)), 6, '0')) last_no
       -- INTO 
       --         vNumber
       -- FROM pendaftaran_t where instalasi_id = NEW.instalasi_id;
                  
       -- SELECT 
       --     (RIGHT('0' || date_part('YEAR',now()),2) ||
       --     RIGHT('0' || date_part('month',now()),2) ||
       --     RIGHT('0' || date_part('DAY',now()),2) ||
       --     CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(right(no_pendaftaran,10)), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
       -- INTO 
       --     vNumber
       -- FROM pendaftaran_t
       -- WHERE instalasi_id = NEW.instalasi_id 
       -- AND pendaftaran_t.tgl_pendaftaran::DATE = CURRENT_DATE;
                      
                                  SELECT 
                              instalasi_singkatan 
                                  INTO 
                                      vPrefix 
                                  FROM instalasi_m WHERE instalasi_id = NEW.instalasi_id;
                                 
                                 select get_sequence_pendaftaran_t(vPrefix) into vNumber;
                      
                      
                      NEW.no_pendaftaran := TRIM(vPrefix) || vNumber;
                      
                      -- Generate Form Pendaftaran
                       paramJson := NEW.additional_data;  
                       dataKarcis := paramJson::json->>'tarif';
                       dataRujukan := paramJson::json->>'rujukan';
                       dataAntrian := paramJson::json->>'antrian';
                       dataPenanggung := paramJson::json->>'penanggung_jawab';
                       dataOrderMcu = paramJson::json->>'order_mcu';
                       dataAsuransi := paramJson::json->>'asuransi';
                       dataPasien := paramJson::json->>'pasien';
                       dataRekamMedik := paramJson::json->>'no_rekam_medik';
                       vPenunjang := paramJson::json->>'tarif_penunjang';
                       vadmisi := paramJson::json->>'pasien_admisi';
                       vmasukkamar := paramJson::json->>'masuk_kamar';
                       countTagihan := json_array_length(dataKarcis::json);
                       countPenunjang := json_array_length(vPenunjang::json);
                       vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
                       vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
                       dataPenanggungBiaya := paramJson::json->>'penanggungbiaya';
                       dataKeluargaPasien := paramJson::json->>'keluargapasien';
                       dataAdditionalPayer := paramJson::json->>'additional_payer';
                       dataAntrianJkn := paramJson::json->>'antrian_jkn';
                       dataOrderLab := paramJson::json->>'exception_penunjang';
                       countException := json_array_length(dataOrderLab::json);
                               
                               
                       IF (dataPasien::json->>'nama_pasien' IS NOT NULL AND NEW.pasien_id IS NULL) THEN
                                  noRekamMedik := dataRekamMedik::json->>'no_rekam_medik';
                
                                  IF NOT EXISTS (
                                      SELECT 1 FROM pasien_m
                                      WHERE no_rekam_medik = noRekamMedik
                                      LIMIT 1
                                  )
                                  THEN
                                      INSERT INTO pasien_m (
                                          tgl_rekam_medik,
                                          jenisidentitas,
                                          no_identitas_pasien,
                                          namadepan,
                                          nama_pasien,
                                          nama_panggilan,
                                          jeniskelamin,
                                          tempat_lahir,
                                          tanggal_lahir,
                                          golonganumur_id,
                                          alamat_pasien,
                                          rt,
                                          rw,
                                          propinsi_id,
                                          kabupaten_id,
                                          kecamatan_id,
                                          kelurahan_id,
                                          pendidikan_id,
                                          pekerjaan_id,
                                          suku_id,
                                          statusperkawinan,
                                          agama,
                                          golongandarah,
                                          rhesus,
                                          anakke,
                                          jumlah_bersaudara,
                                          no_telepon_pasien,
                                          no_mobile_pasien,
                                          warga_negara,
                                          photopasien,
                                          alamatemail,
                                          nama_ibu,
                                          nama_ayah,
                                          statusrekammedis,
                                          alamat_sekarang,
                                          is_aps,
                                          created_by,
                                                                      additional_pasien,
                                                                      bahasa_sehari,
                                                                      catatanpenting_pasien,
                                                                      departemen,
                                                                      posisi_bagian,
                                                                      nama_perusahaan,
                                                                      nomorindukpegawai
                                      ) VALUES (
                                          (dataPasien::json->>'tgl_rekam_medik')::DATE,
                                          
                                          (CASE
                                              dataPasien::json->>'jenisidentitas'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'jenisidentitas')::INTEGER END),
                                          
                                          dataPasien::json->>'no_identitas_pasien',
                                          
                                          (CASE
                                              dataPasien::json->>'namadepan'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'namadepan')::INTEGER END),
                                          
                                          dataPasien::json->>'nama_pasien',
                                          dataPasien::json->>'nama_panggilan',
                                          
                                          (CASE
                                              dataPasien::json->>'jeniskelamin'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'jeniskelamin')::INTEGER END),
                                          
                                          dataPasien::json->>'tempat_lahir',
                                          (dataPasien::json->>'tanggal_lahir')::DATE,
                                          (dataPasien::json->>'golonganumur_id')::INTEGER,
                                          (dataPasien::json->>'alamat_pasien'),
                                          (CASE
                                              dataPasien::json->>'rt'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'rt')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'rw'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'rw')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'propinsi_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'propinsi_id')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'kabupaten_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'kabupaten_id')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'kecamatan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'kecamatan_id')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'kelurahan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'kelurahan_id')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'pendidikan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'pendidikan_id')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'pekerjaan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'pekerjaan_id')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'suku_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'suku_id')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'statusperkawinan'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'statusperkawinan')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'agama'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'agama')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'golongandarah'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'golongandarah')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'rhesus'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'rhesus')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'anakke'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'anakke')::INTEGER END),
                                          (CASE
                                              dataPasien::json->>'jumlah_bersaudara'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'jumlah_bersaudara')::INTEGER END),
                                          dataPasien::json->>'no_telepon_pasien',
                                          dataPasien::json->>'no_mobile_pasien',
                                          (CASE
                                              dataPasien::json->>'warga_negara'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'warga_negara')::INTEGER END),
                                          
                                          dataPasien::json->>'photopasien',
                                          dataPasien::json->>'alamatemail',
                                          dataPasien::json->>'nama_ibu',
                                          dataPasien::json->>'nama_ayah',
                                          336,
                                          dataPasien::json->>'alamat_sekarang',
                                          (dataPasien::json->>'is_aps')::BOOLEAN,
                                          NEW.created_by,
                                                                      dataPasien::json->>'additional_identitas',
                                                                      (dataPasien::json->>'bahasa_sehari')::INTEGER,
                                                                      dataPasien::json->>'catatanpenting_pasien',
                                                                      dataPasien::json->>'departemen',
                                                                      dataPasien::json->>'posisi_bagian',
                                                                      dataPasien::json->>'nama_perusahaan',
                                                                      dataPasien::json->>'nomorindukpegawai'
                                      ) RETURNING pasien_id INTO pasienId;
                                      NEW.pasien_id := pasienId;
                                                              ELSE
                                              SELECT 
                                  pasien_id
                              INTO
                                  pasienId
                              FROM pasien_m
                                              WHERE no_rekam_medik = noRekamMedik;
                              NEW.pasien_id := pasienId;
                  UPDATE pasien_m
                  SET 
                          --tgl_rekam_medik = (dataPasien::json->>'tgl_rekam_medik')::DATE,
                          jenisidentitas = (CASE
                                              dataPasien::json->>'jenisidentitas'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'jenisidentitas')::INTEGER END),
                    no_identitas_pasien = (dataPasien::json->>'no_identitas_pasien'),
                          namadepan = (CASE
                                              dataPasien::json->>'namadepan'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'namadepan')::INTEGER END),
                          nama_pasien = (dataPasien::json->>'nama_pasien'),
                          nama_panggilan = (dataPasien::json->>'nama_panggilan'),
                          jeniskelamin = (CASE
                                              dataPasien::json->>'jeniskelamin'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'jeniskelamin')::INTEGER END),
                          tempat_lahir = (dataPasien::json->>'tempat_lahir'),
                          tanggal_lahir = (dataPasien::json->>'tanggal_lahir')::DATE,
                          golonganumur_id = (dataPasien::json->>'golonganumur_id')::INTEGER,
                          alamat_pasien = (dataPasien::json->>'alamat_pasien'),
                          rt = (CASE
                                              dataPasien::json->>'rt'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'rt')::INTEGER END),
                          rw = (CASE
                                              dataPasien::json->>'rw'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'rw')::INTEGER END),
                          propinsi_id = (CASE
                                              dataPasien::json->>'propinsi_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'propinsi_id')::INTEGER END),
                          kabupaten_id = (CASE
                                              dataPasien::json->>'kabupaten_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'kabupaten_id')::INTEGER END),
                          kecamatan_id = (CASE
                                              dataPasien::json->>'kecamatan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'kecamatan_id')::INTEGER END),
                          kelurahan_id = (CASE
                                              dataPasien::json->>'kelurahan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'kelurahan_id')::INTEGER END),
                          pendidikan_id = (CASE
                                              dataPasien::json->>'pendidikan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'pendidikan_id')::INTEGER END),
                          pekerjaan_id = (CASE
                                              dataPasien::json->>'pekerjaan_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'pekerjaan_id')::INTEGER END),
                          suku_id = (CASE
                                              dataPasien::json->>'suku_id'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'suku_id')::INTEGER END),
                          statusperkawinan = (CASE
                                              dataPasien::json->>'statusperkawinan'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'statusperkawinan')::INTEGER END),
                          agama = (CASE
                                              dataPasien::json->>'agama'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'agama')::INTEGER END),
                          golongandarah = (CASE
                                              dataPasien::json->>'golongandarah'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'golongandarah')::INTEGER END),
                          rhesus = (CASE
                                              dataPasien::json->>'rhesus'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'rhesus')::INTEGER END),
                          anakke = (CASE
                                              dataPasien::json->>'anakke'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'anakke')::INTEGER END),
                          jumlah_bersaudara = (CASE
                                              dataPasien::json->>'jumlah_bersaudara'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'jumlah_bersaudara')::INTEGER END),
                          no_telepon_pasien = (dataPasien::json->>'no_telepon_pasien'),
                          no_mobile_pasien = (dataPasien::json->>'no_mobile_pasien'),
                          warga_negara = (CASE
                                              dataPasien::json->>'warga_negara'
                                          WHEN NULL 
                                          THEN NULL 
                                          ELSE (dataPasien::json->>'warga_negara')::INTEGER END),
                          photopasien = (dataPasien::json->>'photopasien'),
                          alamatemail = (dataPasien::json->>'alamatemail'),
                          nama_ibu = (dataPasien::json->>'nama_ibu'),
                          nama_ayah = (dataPasien::json->>'nama_ayah'),
                          statusrekammedis = 366,
                          alamat_sekarang = (dataPasien::json->>'alamat_sekarang'),
                          is_aps =  (dataPasien::json->>'is_aps')::BOOLEAN,
                          --created_by = (NEW.created_by),
                          last_modified_by = (NEW.created_by),
                          additional_pasien = (dataPasien::json->>'additional_identitas'),
                          bahasa_sehari = (dataPasien::json->>'bahasa_sehari'),
                          catatanpenting_pasien = (dataPasien::json->>'catatanpenting_pasien')
                  WHERE no_rekam_medik = noRekamMedik;
                END IF;
              END IF;
                       
                       IF (dataAsuransi::json->>'nokartuasuransi' IS NOT NULL) THEN
                              noAsuransi := dataAsuransi::json->>'nokartuasuransi';
                              SELECT 
                                  asuransipasien_id,
                                  pasien_id
                              INTO
                                  idAsuransi,
                                  idPasienAsuransi
                              FROM asuransipasien_m
                              WHERE nokartuasuransi = noAsuransi
                              AND penjamin_id = NEW.penjamin_id
                              AND pasien_id = NEW.pasien_id
                              AND carabayar_id = NEW.carabayar_id;
                              
              --              IF (idPasienAsuransi != NEW.pasien_id) THEN
              -- -- Sementara case asuranasi
              -- --                   RAISE EXCEPTION 'Duplicate No Asuransi: %', dataAsuransi::json->>'nokartuasuransi' 
              -- --                           USING HINT = 'No Asuransi Sudah digunakan';
              --              ELSE
                              IF (idAsuransi IS NULL) THEN
                                  INSERT INTO asuransipasien_m (
                                      kelastanggunganasuransi_id,
                                      namapemilikasuransi,
                                      namaperusahaan,
                                      nokartuasuransi,
                                      nomorpokokperusahaan,
                                      status_konfirmasi,
                                      tgl_konfirmasi,
                                      created_by,
                                      pasien_id,
                                      penjamin_id,
                                      carabayar_id,
                                                              masaberlakukartu,
                                                              nama_asuransi,
                                                              penjamingrade_id
                                  ) VALUES (
                                      (CASE dataAsuransi::json->>'kelastanggungan_id'
                                              WHEN '' THEN
                                                  NULL
                                              ELSE
                                                  (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                                      END),
                                      dataAsuransi::json->>'namapemilikasuransi',
                                      dataAsuransi::json->>'namaperusahaan',
                                      dataAsuransi::json->>'nokartuasuransi',
                                      dataAsuransi::json->>'nomorpokokperusahaan',
                                      dataAsuransi::json->>'status_konfirmasi',
                                      (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                                      NEW.created_by,
                                      NEW.pasien_id,
                                      NEW.penjamin_id,
                                      NEW.carabayar_id,
                                                              (dataAsuransi::json->>'masaberlakukartu')::DATE,
                                                              dataAsuransi::json->>'nama_asuransi',
              -- 												NEW.penjamingrade_id
                                                              (dataAsuransi::json->>'penjamingrade_id')::INTEGER
                                  ) RETURNING asuransipasien_id INTO idAsuransi;
                                  
                                  NEW.asuransipasien_id = idAsuransi;
                              ELSE 
                                      UPDATE asuransipasien_m SET 
                                          kelastanggunganasuransi_id = (CASE dataAsuransi::json->>'kelastanggungan_id'
                                              WHEN '' THEN
                                                  NULL
                                              ELSE
                                                  (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                                          END), 
                                          namapemilikasuransi = dataAsuransi::json->>'namapemilikasuransi',
                                          namaperusahaan = dataAsuransi::json->>'namaperusahaan',
                                          nomorpokokperusahaan = dataAsuransi::json->>'nomorpokokperusahaan',
                                          status_konfirmasi = dataAsuransi::json->>'status_konfirmasi',
                                          tgl_konfirmasi = (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                                          last_modified_by = NEW.created_by
                                      WHERE asuransipasien_id = idAsuransi;           
                                      
                                      NEW.asuransipasien_id = idAsuransi;
                              END IF;
              --              END IF;
                       END IF;
              
                       IF (dataPenanggung::json->>'pj_pengantar' IS NOT NULL) THEN
                              INSERT INTO penanggungjawab_m (
                                  pengantar,
                                  jenisidentitas,
                                  no_identitas,
                                  hubungankeluarga,
                                  penanggungjawab_nama,
                                  penanggungjawab_tempatlahir,
                                  penanggungjawab_tgllahir,
                                  penanggungjawab_jeniskelamin,
                                  penanggungjawab_alamat,
                                  penanggungjawab_notelp,
                                  penanggungjawab_nohp,
                                  pasien_id,
                                  created_by,
                                                      pj_namadepan,
                                                      pj_propinsi_id,
                                                      pj_kabupaten_id,
                                                      pj_kecamatan_id,
                                                      pj_kelurahan_id,
                                                      pj_pekerjaan_id,
                                                      pj_rt,
                                                      pj_rw
                              ) VALUES (
                                  dataPenanggung::json->>'pj_pengantar',
                                  dataPenanggung::json->>'pj_jenis_identitas',
                                  dataPenanggung::json->>'pj_no_identitas',
                                  dataPenanggung::json->>'pj_hubungan',
                                  dataPenanggung::json->>'pj_nama',
                                  dataPenanggung::json->>'pj_tempat_lahir',
                                  (dataPenanggung::json->>'pj_tanggal_lahir')::DATE,
                                  dataPenanggung::json->>'pj_jk',
                                  dataPenanggung::json->>'pj_alamat',
                                  dataPenanggung::json->>'pj_no_telepon',
                                  dataPenanggung::json->>'pj_no_telepon',
                                  NEW.pasien_id,
                                  NEW.created_by,
                                                      (dataPenanggung::json->>'pj_namadepan')::INTEGER,
                                                      (dataPenanggung::json->>'pj_propinsi_id')::INTEGER,
                                                      (dataPenanggung::json->>'pj_kabupaten_id')::INTEGER,
                                                      (dataPenanggung::json->>'pj_kecamatan_id')::INTEGER,
                                                      (dataPenanggung::json->>'pj_kelurahan_id')::INTEGER,
                                                      (dataPenanggung::json->>'pj_pekerjaan_id')::INT,
              -- 										(CASE
              --                             dataPenanggung::json->>'pj_pekerjaan_id'
              --                             WHEN NULL 
              --                             THEN NULL 
              --                             ELSE (dataPenanggung::json->>'pj_pekerjaan_id')::INTEGER END),
                                                      dataPenanggung::json->>'pj_rt',
                                                      dataPenanggung::json->>'pj_rw'
                              ) RETURNING penanggungjawab_id INTO penanggungId;
                                              NEW.penanggungjawab_id = penanggungId;
                       END IF;
                               
                               -- Kebutuhan Santo Yusup penanggungbiaya_t
                               IF (dataPenanggungBiaya::json->>'carabayar_id' IS NOT NULL) THEN
                              INSERT INTO penanggungbiaya_t (
                                 -- penanggungbiaya_id,
                                  pasien_id,
                                  carabayar_id,
                                  penanggungbiaya_nama,
                                  namabagian,
                                  noindukkaryawan,
                                  jpkm,
                                  instansi
                              ) VALUES (
                                  --dataPenanggungBiaya::json->>'penanggungbiaya_id',
                                  --dataPenanggungBiaya::json->>'pasien_id',
                                                      --NEW.penanggungbiaya_id,
                                                      NEW.pasien_id,
                                                      --(dataAntrian::json->>'ruangan_id')::INTEGER,
                                  (dataPenanggungBiaya::json->>'carabayar_id')::INTEGER,
                                  dataPenanggungBiaya::json->>'penanggungbiaya_nama',
                                  dataPenanggungBiaya::json->>'namabagian',
                                  dataPenanggungBiaya::json->>'noindukkaryawan',
                                  dataPenanggungBiaya::json->>'jpkm',
                                  dataPenanggungBiaya::json->>'instansi'
                              ) RETURNING penanggungbiaya_id INTO penanggungbiayaId;
                                              NEW.penanggungbiaya_id = penanggungbiayaId;
              -- 								ELSE 
              --                         UPDATE penanggungbiaya_t SET 
              --                             penanggungbiaya_nama = dataPenanggungBiaya::json->>'penanggungbiaya_nama',
              --                             namabagian = dataPenanggungBiaya::json->>'namabagian',
              --                             noindukkaryawan = dataPenanggungBiaya::json->>'noindukkaryawan',
              --                             jpkm = dataPenanggungBiaya::json->>'jpkm',
              -- 														instansi = dataPenanggungBiaya::json->>'instansi'
              --                         WHERE penanggungbiaya_id = idPenanggungBiaya;           
              --                         
              --                         NEW.penanggungbiaya_id = idPenanggungBiaya;
              --                 END IF;
                       END IF;
                               
                               -- Kebutuhan Santo Yusup keluargapasien_t
                               IF (dataKeluargaPasien::json->>'keluarga_nama' IS NOT NULL) THEN
                              INSERT INTO keluargapasien_t (
                                                      keluarga_nama,
                                                      keluarga_jk,
                                                      keluarga_hubungan,
                                                      keluarga_alamat,
                                                      keluarga_no_telepon,
                                                      keluarga_namadepan,
                                                      keluarga_propinsi_id,
                                                      keluarga_kabupaten_id,
                                                      keluarga_kecamatan_id,
                                                      keluarga_kelurahan_id,
                                                      keluarga_pekerjaan_id,
                                                      keluarga_rt,
                                                      keluarga_rw,
                                  pasien_id
                              ) VALUES (
                                  dataKeluargaPasien::json->>'keluarga_nama',
                                  dataKeluargaPasien::json->>'keluarga_jk',
                                  dataKeluargaPasien::json->>'keluarga_hubungan',
                                                      dataKeluargaPasien::json->>'keluarga_alamat',
                                                      dataKeluargaPasien::json->>'keluarga_no_telepon',
                                                      dataKeluargaPasien::json->>'keluarga_namadepan',
                                                      (dataKeluargaPasien::json->>'keluarga_propinsi_id')::INTEGER,
                                                      (dataKeluargaPasien::json->>'keluarga_kabupaten_id')::INTEGER,
                                                      (dataKeluargaPasien::json->>'keluarga_kecamatan_id')::INTEGER,
                                                      (dataKeluargaPasien::json->>'keluarga_kelurahan_id')::INTEGER,
                                                      (dataKeluargaPasien::json->>'keluarga_pekerjaan_id')::INTEGER,
                                  dataKeluargaPasien::json->>'keluarga_rt',
                                  dataKeluargaPasien::json->>'keluarga_rw',
                                                      NEW.pasien_id
                              ) ;
              -- 								RETURNING keluargapasien_id INTO keluargapasienId;
              --                                 NEW.keluargapasien_id = keluargapasienId;
                                  END IF;
                                  
                                  -- MultiPayer 1--
              -- 								SELECT pendaftaran_t.is_multipayer INTO isMultiPayer
              -- 										FROM pendaftaran_t
              -- 										LIMIT 1; 
                                                      
                                        IF (dataAdditionalPayer::json->>'add_nokartuasuransi_1' IS NOT NULL) THEN
                              noAsuransi1 := dataAdditionalPayer::json->>'add_nokartuasuransi_1';
                              SELECT 
                                  asuransipasien_id,
                                  pasien_id
                              INTO
                                  idAsuransi1,
                                  idPasienAsuransi1
                              FROM asuransipasien_m
                              WHERE nokartuasuransi = noAsuransi1
                              AND penjamin_id = NEW.penjamin_id
                              AND pasien_id = NEW.pasien_id
                              AND carabayar_id = NEW.carabayar_id;
                                              
                              IF (idAsuransi1 IS NULL) THEN
                                  INSERT INTO asuransipasien_m (
                                      kelastanggunganasuransi_id,
                                      namapemilikasuransi,
                                      namaperusahaan,
                                      nokartuasuransi,
                                      nomorpokokperusahaan,
                                      status_konfirmasi,
                                      tgl_konfirmasi,
                                      created_by,
                                      pasien_id,
                                      penjamin_id,
                                      carabayar_id,
                                                              penjamingrade_id
                                  ) VALUES (
                                      (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_1'
                                              WHEN '' THEN
                                                  NULL
                                              ELSE
                                                  (dataAdditionalPayer::json->>'add_kelastanggungan_id_1')::INTEGER
                                      END),
                                      dataAdditionalPayer::json->>'add_namapemilikasuransi_1',
                                      dataAdditionalPayer::json->>'add_namaperusahaan_1',
                                      dataAdditionalPayer::json->>'add_nokartuasuransi_1',
                                      dataAdditionalPayer::json->>'add_nomorpokokperusahaan_1',
                                      dataAdditionalPayer::json->>'add_status_konfirmasi_1',
                                      (dataAdditionalPayer::json->>'add_tgl_konfirmasi_1')::DATE,
                                      NEW.created_by,
                                      NEW.pasien_id,
                                      NEW.penjamin_id,
                                      NEW.carabayar_id,
                                                              (dataAdditionalPayer::json->>'add_penjamingrade_id_1')::INTEGER
                                  ) RETURNING asuransipasien_id INTO idAsuransi1;
                                  
              --                     NEW.asuransipasien_id = idAsuransi1;
                              ELSE 
                                      UPDATE asuransipasien_m SET 
                                          kelastanggunganasuransi_id = (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_1'
                                              WHEN '' THEN
                                                  NULL
                                              ELSE
                                                  (dataAdditionalPayer::json->>'add_kelastanggungan_id_1')::INTEGER
                                          END), 
                                          namapemilikasuransi = dataAdditionalPayer::json->>'add_namapemilikasuransi_1',
                                          namaperusahaan = dataAdditionalPayer::json->>'add_namaperusahaan_1',
                                          nomorpokokperusahaan = dataAdditionalPayer::json->>'add_nomorpokokperusahaan_1',
                                          status_konfirmasi = dataAdditionalPayer::json->>'add_status_konfirmasi_1',
                                          tgl_konfirmasi = (dataAdditionalPayer::json->>'add_tgl_konfirmasi_1')::DATE,
                                          last_modified_by = NEW.created_by
                                      WHERE asuransipasien_id = idAsuransi1;           
                                      
              --                         NEW.asuransipasien_id = idAsuransi1;
                              END IF;
                       END IF;
                               
                                               -- MultiPayer 2 --
              -- 								SELECT pendaftaran_t.is_multipayer INTO isMultiPayer
              -- 										FROM pendaftaran_t
              -- 										LIMIT 1; 
                                                      
                                        IF (dataAdditionalPayer::json->>'add_nokartuasuransi_2' IS NOT NULL) THEN
                              noAsuransi2 := dataAdditionalPayer::json->>'add_nokartuasuransi_2';
                              SELECT 
                                  asuransipasien_id,
                                  pasien_id
                              INTO
                                  idAsuransi2,
                                  idPasienAsuransi2
                              FROM asuransipasien_m
                              WHERE nokartuasuransi = noAsuransi2
                              AND penjamin_id = NEW.penjamin_id
                              AND pasien_id = NEW.pasien_id
                              AND carabayar_id = NEW.carabayar_id;
                                              
                              IF (idAsuransi2 IS NULL) THEN
                                  INSERT INTO asuransipasien_m (
                                      kelastanggunganasuransi_id,
                                      namapemilikasuransi,
                                      namaperusahaan,
                                      nokartuasuransi,
                                      nomorpokokperusahaan,
                                      status_konfirmasi,
                                      tgl_konfirmasi,
                                      created_by,
                                      pasien_id,
                                      penjamin_id,
                                      carabayar_id,
                                                              penjamingrade_id
                                  ) VALUES (
                                      (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_2'
                                              WHEN '' THEN
                                                  NULL
                                              ELSE
                                                  (dataAdditionalPayer::json->>'add_kelastanggungan_id_2')::INTEGER
                                      END),
                                      dataAdditionalPayer::json->>'add_namapemilikasuransi_2',
                                      dataAdditionalPayer::json->>'add_namaperusahaan_2',
                                      dataAdditionalPayer::json->>'add_nokartuasuransi_2',
                                      dataAdditionalPayer::json->>'add_nomorpokokperusahaan_2',
                                      dataAdditionalPayer::json->>'add_status_konfirmasi_2',
                                      (dataAdditionalPayer::json->>'add_tgl_konfirmasi_2')::DATE,
                                      NEW.created_by,
                                      NEW.pasien_id,
                                      NEW.penjamin_id,
                                      NEW.carabayar_id,
                                                              (dataAdditionalPayer::json->>'add_penjamingrade_id_2')::INTEGER
                                  ) RETURNING asuransipasien_id INTO idAsuransi2;
                                  
              --                     NEW.asuransipasien_id = idAsuransi2;
                              ELSE 
                                      UPDATE asuransipasien_m SET 
                                          kelastanggunganasuransi_id = (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_2'
                                              WHEN '' THEN
                                                  NULL
                                              ELSE
                                                  (dataAdditionalPayer::json->>'add_kelastanggungan_id_2')::INTEGER
                                          END), 
                                          namapemilikasuransi = dataAdditionalPayer::json->>'add_namapemilikasuransi_2',
                                          namaperusahaan = dataAdditionalPayer::json->>'add_namaperusahaan_2',
                                          nomorpokokperusahaan = dataAdditionalPayer::json->>'add_nomorpokokperusahaan_2',
                                          status_konfirmasi = dataAdditionalPayer::json->>'add_status_konfirmasi_2',
                                          tgl_konfirmasi = (dataAdditionalPayer::json->>'add_tgl_konfirmasi_2')::DATE,
                                          last_modified_by = NEW.created_by
                                      WHERE asuransipasien_id = idAsuransi2;           
                                      
              --                         NEW.asuransipasien_id = idAsuransi2;
                              END IF;
                       END IF;
                                  
                                              -- Multi Payer 1
                               IF (dataAdditionalPayer::json->>'add_carabayar_id_1' IS NOT NULL) THEN
                              INSERT INTO pendaftaran_multipayer_t (
                                                      asuransipasien_id,
                                                      pendaftaran_id,
                                                      pasien_id,
                                                      penjamin_id,
                                                      carabayar_id,
                                                      bpjs_id
                              ) VALUES (
                                                      idAsuransi1,
                                                      NEW.pendaftaran_id,
                                                      NEW.pasien_id,
                                                      --NEW.penjamin_id,
                                                      (dataAdditionalPayer::json->>'add_penjamin_id_1')::INTEGER,
                                                      --NEW.carabayar_id
                                                      (dataAdditionalPayer::json->>'add_carabayar_id_1')::INTEGER,
                                                      NEW.bpjs_id
                              ) ;
                                  END IF;
                                  
                                  -- Multi Payer 2
                               IF (dataAdditionalPayer::json->>'add_carabayar_id_2' IS NOT NULL) THEN
                              INSERT INTO pendaftaran_multipayer_t (
                                                      asuransipasien_id,
                                                      pendaftaran_id,
                                                      pasien_id,
                                                      penjamin_id,
                                                      carabayar_id,
                                                      bpjs_id
                              ) VALUES (
                                                      idAsuransi2,
                                                      NEW.pendaftaran_id,
                                                      NEW.pasien_id,
                                                      --NEW.penjamin_id,
                                                      (dataAdditionalPayer::json->>'add_penjamin_id_2')::INTEGER,
                                                      --NEW.carabayar_id
                                                      (dataAdditionalPayer::json->>'add_carabayar_id_2')::INTEGER,
                                                      NEW.bpjs_id
                              ) ;
                                  END IF;
                       
                       
                               -- Order MCU
                                   IF (dataOrderMcu::json->>'penunjang' IS NOT NULL) THEN
                                          IF (json_array_length((dataOrderMcu::json->>'penunjang')::json) > 0) THEN
                                                  INSERT INTO pasienmasukpenunjang_t (
                                                                                  kelaspelayanan_id,
                                                                                  jeniskasuspenyakit_id,
                                                                                  pasienadmisi_id,
                                                                                  pegawai_id,
                                                                                  ruangan_id,
                                                                                  pasien_id,
                                                                                  pendaftaran_id,
                                                                                  ruanganasal_id,
                                                                                  tglmasukpenunjang,
                                                                                  kunjungan,
                                                                                  status_periksa,
                                                                                  is_bayar,
                                                                                  instalasiasal_id,
                                                                                  no_antrian,
                                                                                  created_by
                                           ) SELECT 
                                                                                  kelaspelayanan_id,
                                                                                  jeniskasuspenyakit_id,
                                                                                  NEW.pasienadmisi_id,
                                                                                  pegawai_id,
                                                                                  ruangan_id,
                                                                                  NEW.pasien_id,
                                                                                  NEW.pendaftaran_id,
                                                                                  ruanganasal_id,
                                                                                  tglmasukpenunjang,
                                                                                  kunjungan,
                                                                                  status_periksa,
                                                                                  is_bayar,
                                                                                  instalasiasal_id,
                                                                                  no_antrian,
                                                                                  NEW.created_by as created_by
                                                  FROM json_populate_recordset(null::pasienmasukpenunjang_t,(dataOrderMcu::json->>'penunjang')::json);
                                          END IF;
                                   END IF;
                                   
                                   -- Konsul Poli
                                   IF (dataOrderMcu::json->>'konsul' IS NOT NULL) THEN
                                          IF (json_array_length((dataOrderMcu::json->>'konsul')::json) > 0) THEN
                                                  INSERT INTO konsulpoli_t (
                                                                                  ruangan_id,
                                                                                  pegawai_id,
                                                                                  tindakanpelayanan_id,
                                                                                  pendaftaran_id,
                                                                                  pasien_id,
                                                                                  tgl_konsulpoli,
                                                                                  asalpoliklinikkonsul_id,
                                                                                  status_periksa,
                                                                                  no_antriankonsul,
                                                                                  created_by,
                                                                                                                                                      pendaftaranbaru_id,
                                                                                                                                                      status_approve
                                           ) SELECT 
                                                                                  ruangan_id,
                                                                                  pegawai_id,
                                                                                  tindakanpelayanan_id,
                                                                                  NEW.pendaftaran_id,
                                                                                  NEW.pasien_id,
                                                                                  tgl_konsulpoli,
                                                                                  asalpoliklinikkonsul_id,
                                                                                  status_periksa,
                                                                                  no_antriankonsul,
                                                                                  NEW.created_by as created_by,
                                                                                                                                                      NEW.pendaftaran_id,
                                                                                                                                                      565
                                                  FROM json_populate_recordset(null::konsulpoli_t,(dataOrderMcu::json->>'konsul')::json);
                                          END IF;
                                   END IF; 
                                  
                       -- TEST PENAMBAHAN DSV-645
       --				SELECT konfigsystem_k.is_generate_no_antrian INTO isGenerateConfig
       --                FROM konfigsystem_k
       --                LIMIT 1; 
       --                            
                       -- Set Antrian 
                       IF (dataAntrian::json->>'jenisantrian_id' IS NOT NULL AND NEW.antrian_id IS null) THEN
                                   --get konfigantrian
                                  SELECT konfigantrian_id INTO v_konfigantrian
                                  from konfigantrian_m
                                  WHERE jenisantrian_id = (dataAntrian::json->>'jenisantrian_id')::INTEGER 
                                  and konfigantrian_m.is_deleted=FALSE 
                                  and konfigantrian_m.is_active=true
                                  limit 1;
                                                      
                                                      SELECT konfigsystem_k.is_nourut INTO vnourut
                                                      FROM konfigsystem_k
                                                      LIMIT 1; 
                                  
                                      IF (vnourut=TRUE) THEN
                                                  INSERT INTO antrian_t (
                                                                                  pasien_id,
                                                                                  ruangan_id,
                                                                                  carabayar_id,
                                                                                  pendaftaran_id,
                                                                                  tgl_antrian,
                                                                                  penjamin_id,
                                                                                  pegawai_id,
                                                                                  status_pasien,
                                                                                  jenisantrian_id,
                                                                                  is_active,
                                                                                  created_by,
                                                                                  konfigantrian_id,
                                                                                  no_antrian,
                                                                                  jadwaldokter_id,
                                                                                  slot_sequence,
                                                                                  groupcarabayar_id
                                           ) VALUES (
                                                                                  NEW.pasien_id,
                                                                                  (dataAntrian::json->>'ruangan_id')::INTEGER,
                                                                                  (dataAntrian::json->>'carabayar_id')::INTEGER,
                                                                                  NEW.pendaftaran_id,
                                                                                  (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                                                                  (dataAntrian::json->>'penjamin_id')::INTEGER,
                                                                                  (dataAntrian::json->>'pegawai_id')::INTEGER,
                                                                                  (dataAntrian::json->>'status_pasien')::INTEGER,
                                                                                  (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                                                                  FALSE,
                                                                                  NEW.created_by,
                                                                                  v_konfigantrian,
                                                                                  (dataAntrian::json->>'no_antrian')::VARCHAR,
                                                                                  (dataAntrian::json->>'jadwaldokter_id')::INTEGER,
                                                                                  (dataAntrian::json->>'slot_sequence')::INTEGER,
                                                                                  (dataAntrian::json->>'groupcarabayar_id')::INTEGER
                                           ) RETURNING antrian_id INTO antrianId;
                                       ELSE
                                       INSERT INTO antrian_t (
                                              pasien_id,
                                              ruangan_id,
                                              carabayar_id,
                                              pendaftaran_id,
                                              tgl_antrian,
                                              penjamin_id,
                                              pegawai_id,
                                              status_pasien,
                                              jenisantrian_id,
                                              is_active,
                                              created_by,
                                              konfigantrian_id,
                                                                              jadwaldokter_id,
                                                                                  slot_sequence,
                                                                                  groupcarabayar_id
                                          ) VALUES (
                                              NEW.pasien_id,
                                              (dataAntrian::json->>'ruangan_id')::INTEGER,
                                              (dataAntrian::json->>'carabayar_id')::INTEGER,
                                              NEW.pendaftaran_id,
                                              (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                              (dataAntrian::json->>'penjamin_id')::INTEGER,
                                              (dataAntrian::json->>'pegawai_id')::INTEGER,
                                              (dataAntrian::json->>'status_pasien')::INTEGER,
                                              (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                              FALSE,
                                              NEW.created_by,
                                              v_konfigantrian,
                                                                              (dataAntrian::json->>'jadwaldokter_id')::INTEGER,
                                                                                  (dataAntrian::json->>'slot_sequence')::INTEGER,
                                                                                  (dataAntrian::json->>'groupcarabayar_id')::INTEGER
                                          ) RETURNING antrian_id INTO antrianId;
                                       END IF;
                                       
                              NEW.antrian_id = antrianId;
                                              
                                              -- Antrian JKN DAN NON JKN Pendaftaran Offline
                                              
                                              IF (NEW.instalasi_id::INT = 1 AND paramJson::json->>'pendaftaranol_id' IS NULL) THEN
              -- 								IF (vinstalasi=1 AND paramJson::json->>'pendaftaranol_id' IS NULL) THEN
                                              
                                              SELECT no_rekam_medik INTO vnorekammedik
                                              FROM pasien_m
                                              WHERE pasien_id = new.pasien_id;
                                              
                                              INSERT INTO antrianjkn_r (
              --                                 pendaftaranol_id,
                                              antrian_id,
                                              tanggal_periksa,
                                              nomorkartu,
                                              jenis_cara_bayar,
                                              jeniskunjungan,
                                              nomorreferensi,
              --                                 keterangan,
                                              no_rekam_medik,
                                                                              pendaftaran_id
                                                                              ) VALUES (
              -- 																new.pendaftaranol_id,
                                                                              new.antrian_id,
                                                                              new.tgl_pendaftaran,
                                                                              (dataAntrianJkn::json->>'nomorkartu')::VARCHAR,
                                                                              (dataAntrianJkn::json->>'jenis_cara_bayar')::INTEGER,
                                                                              (dataAntrianJkn::json->>'jeniskunjungan')::INTEGER,
                                                                              (dataAntrianJkn::json->>'nomorreferensi')::VARCHAR,
              -- 																(dataAntrianJkn::json->>'keterangan')::VARCHAR,
              -- 																(dataAntrianJkn::json->>'no_rekam_medik')::VARCHAR,
                                                                              vnorekammedik,
                                                                              new.pendaftaran_id
                                                                              );
                                              END IF;
                                              
                              
                              --- Ini Kondisi Penunjang GET nomor antrian untuk pasien masuk penunjang
                              IF (NEW.carabayar_id != 5 AND countPenunjang > 0) THEN
                                      SELECT 
                                          no_antrian
                                      INTO 
                                          noAntrian
                                      FROM antrian_t WHERE antrian_id = antrianId;
                              END IF;
                                              
                                              -- MCU --
                                                      IF(NEW.instalasi_id::INT = 21) THEN
                                                          UPDATE antrian_t SET 
                                                      no_antrian = '-'
                                  WHERE antrian_id = NEW.antrian_id; 
                                                      END IF;
                                              
                       else
                              -- Update Antrian Pendaftaran
                              UPDATE antrian_t SET 
                                  pendaftaran_id = NEW.pendaftaran_id, 
                                  pasien_id = NEW.pasien_id,
                                                      slot_sequence = (dataAntrian::json->>'slot_sequence')::INTEGER,
                                                      groupcarabayar_id = (dataAntrian::json->>'groupcarabayar_id')::INTEGER
                              WHERE antrian_id = NEW.antrian_id;
                                              
                              
                              IF (countPenunjang > 0) THEN
                                      IF (NEW.carabayar_id != 5) THEN
                                          SELECT 
                                              no_antrian
                                          INTO 
                                              noAntrian
                                          FROM antrian_t WHERE antrian_id = NEW.antrian_id;
                                      END IF;
                              ELSE
                                  -- Validasi Untuk Non Penunjang 
                                  isActive := false;
                                  IF (NEW.carabayar_id != 5 OR countTagihan <= 0) THEN
                                      isActive := true;
                                  END IF;
                                                      
                                  -- Update Pendaftan Poli
                                                      
                                                      SELECT konfigsystem_k.is_nourut INTO vnourut
                                                      FROM konfigsystem_k
                                                      LIMIT 1; 
                                  
                                  IF (vnourut=TRUE) THEN
                                  UPDATE antrian_t SET 
                                      pendaftaran_id = NEW.pendaftaran_id, 
                                      pasien_id = NEW.pasien_id, 
                                      ruangan_id = NEW.ruangan_id,
                                      carabayar_id = NEW.carabayar_id,
                                      is_active = isActive,
                                      no_antrian = (dataAntrian::json->>'no_antrian')::VARCHAR,
                                         jadwaldokter_id = CASE 
                                         WHEN (dataAntrian::json->>'jadwaldokter_id')::INTEGER IS NULL
                                         THEN 
                                           jadwaldokter_id
                                         ELSE 
                                           (dataAntrian::json->>'jadwaldokter_id')::INTEGER
                                      END,
                                      jadwalbukapoli_id = CASE 
                                         WHEN (dataAntrian::json->>'jadwalbukapoli_id')::INTEGER IS NULL
                                         THEN 
                                           jadwalbukapoli_id
                                         ELSE 
                                           (dataAntrian::json->>'jadwalbukapoli_id')::INTEGER
                                      END
                                  WHERE antrianasal_id = NEW.antrian_id; 
                                                      
                                                      -- MCU --
                                                      IF(NEW.instalasi_id = 21) THEN
                                                          UPDATE antrian_t SET 
                                                      no_antrian = '-'
                                  WHERE antrianasal_id = NEW.antrian_id; 
                                                      END IF;
                                                      
                                                      -- update id antrian 177 menjadi id 312
                                                      SELECT antrian_id INTO antrianIdPoli 
                                                      FROM antrian_t
                                                          WHERE antrianasal_id = NEW.antrian_id; 
                                                          IF (antrianIdPoli IS NOT NULL) THEN  
                                                              NEW.antrian_id = antrianIdPoli;
                                                          END IF;
                                  ELSE
                                                      UPDATE antrian_t SET 
                                      pendaftaran_id = NEW.pendaftaran_id, 
                                      pasien_id = NEW.pasien_id, 
                                      ruangan_id = NEW.ruangan_id,
                                      carabayar_id = NEW.carabayar_id,
                                      is_active = isActive,
                                        jadwaldokter_id= CASE 
                                         WHEN (dataAntrian::json->>'jadwaldokter_id')::INTEGER IS NULL
                                         THEN 
                                           jadwaldokter_id
                                         ELSE 
                                           (dataAntrian::json->>'jadwaldokter_id')::INTEGER
                                      END,
                                      jadwalbukapoli_id = CASE 
                                         WHEN (dataAntrian::json->>'jadwalbukapoli_id')::INTEGER IS NULL
                                         THEN 
                                           jadwalbukapoli_id
                                         ELSE 
                                           (dataAntrian::json->>'jadwalbukapoli_id')::INTEGER
                                      END
                                  WHERE antrianasal_id = NEW.antrian_id; 
                                                      
                                                      -- MCU --
                                                      IF(NEW.instalasi_id = 21) THEN
                                                      UPDATE antrian_t SET 
                                                      no_antrian = '-'
                                  WHERE antrianasal_id = NEW.antrian_id; 
                                                      END IF;
                                                      
                                                      -- update id antrian 177 menjadi id 312
                                                      SELECT antrian_id INTO antrianIdPoli 
                                                      FROM antrian_t
                                                          WHERE antrianasal_id = NEW.antrian_id; 
                                                          IF (antrianIdPoli IS NOT NULL) THEN  
                                                              NEW.antrian_id = antrianIdPoli;
                                                          END IF;
                                                      END IF;
                              END IF;
                       END IF;
                       
                       -- Set Rujukan Jika Ada
                       IF (dataRujukan::json->>'rujukandari_id' IS NOT NULL) THEN
                              INSERT INTO rujukan_t (
                                              asalrujukan_id,
                                              rujukandari_id,
                                              diagnosa_id,
                                              no_rujukan,
                                              nama_perujuk,
                                              tanggal_rujukan,
                                              created_by
                           ) VALUES (
                                              (dataRujukan::json->>'asalrujukan_id')::INTEGER,
                                              (dataRujukan::json->>'rujukandari_id')::INTEGER,
                                              (CASE
                                                  dataRujukan::json->>'diagnosa_id'
                                              WHEN NULL 
                                              THEN NULL 
                                              ELSE (dataPasien::json->>'diagnosa_id')::INTEGER END),
                                              dataRujukan::json->>'no_rujukan',
                                              dataRujukan::json->>'nama_perujuk',
                                              (dataRujukan::json->>'tanggal_rujukan')::TIMESTAMP,
                                              NEW.created_by
                           ) RETURNING rujukan_id INTO rujukanId;
                              NEW.rujukan_id = rujukanId;
                       END IF;
                       
                  --- Kondisi Pendaftaran Penunjang      
                      IF (countPenunjang > 0) 
                      THEN 
                          IF (countException > 0)
                          THEN
                              INSERT INTO pasienmasukpenunjang_t (
                                  kelaspelayanan_id,
                                  jeniskasuspenyakit_id,
                                  pegawai_id,
                                  ruangan_id,
                                  pasien_id,
                                  pendaftaran_id,
                                  tglmasukpenunjang,
                                  no_antrian,
                                  status_periksa,
                                  ruanganasal_id,
                                  instalasiasal_id,
                                  created_by,
                                  pasienadmisi_id,
                                  is_exception
                              )
                              -- ) VALUES (
                              SELECT 
                                  NEW.kelaspelayanan_id,
                                  NEW.jeniskasuspenyakit_id,
                                  NEW.pegawai_id,
                                  NEW.ruangan_id,
                                  NEW.pasien_id,
                                  NEW.pendaftaran_id,
                                  NEW.tgl_pendaftaran,
                                  noAntrian,
                                  CASE new.instalasi_id::int4
                                  WHEN 12 THEN 488
                                  ELSE 477
                                  end,
                                  --                        477,
                                  NEW.ruangan_id,
                                  new.instalasi_id,
                                  NEW.created_by,
                                  NEW.pasienadmisi_id,
                                  is_exception
                              FROM json_populate_recordset(NULL::pasienmasukpenunjang_t,dataOrderLab::json);
                          ELSE
                              INSERT INTO pasienmasukpenunjang_t (
                                  kelaspelayanan_id,
                                  jeniskasuspenyakit_id,
                                  pegawai_id,
                                  ruangan_id,
                                  pasien_id,
                                  pendaftaran_id,
                                  tglmasukpenunjang,
                                  no_antrian,
                                  status_periksa,
                                  ruanganasal_id,
                                  instalasiasal_id,
                                  created_by,
                                  pasienadmisi_id
                              ) VALUES (
                                  NEW.kelaspelayanan_id,
                                  NEW.jeniskasuspenyakit_id,
                                  NEW.pegawai_id,
                                  NEW.ruangan_id,
                                  NEW.pasien_id,
                                  NEW.pendaftaran_id,
                                  NEW.tgl_pendaftaran,
                                  noAntrian,
                                  CASE new.instalasi_id::int4
                                  WHEN 12 THEN 488
                                  ELSE 477
                                  end,
                                  --                        477,
                                  NEW.ruangan_id,
                                  new.instalasi_id,
                                  NEW.created_by,
                                  NEW.pasienadmisi_id
                              ) RETURNING pasienmasukpenunjang_id INTO idPenunjang;
                          END IF;
                      ELSE
                          SELECT 
                              is_penunjang,
                              instalasi_id
                          INTO 
                              IsPenunjang,
                              idInstalasi
                          FROM instalasi_m
                          WHERE instalasi_id = NEW.instalasi_id;
                      
                          IF (IsPenunjang = TRUE AND idInstalasi <> 12) 
                          THEN
                              INSERT INTO pasienmasukpenunjang_t (
                              kelaspelayanan_id,
                              jeniskasuspenyakit_id,
                              pegawai_id,
                              ruangan_id,
                              pasien_id,
                              pendaftaran_id,
                              tglmasukpenunjang,
                              no_antrian,
                              status_periksa,
                              ruanganasal_id,
                              instalasiasal_id,
                              created_by,
                              pasienadmisi_id
                              ) VALUES (
                              NEW.kelaspelayanan_id,
                              NEW.jeniskasuspenyakit_id,
                              NEW.pegawai_id,
                              NEW.ruangan_id,
                              NEW.pasien_id,
                              NEW.pendaftaran_id,
                              NEW.tgl_pendaftaran,
                              noAntrian,
                              CASE new.instalasi_id::int4
                              WHEN 12 THEN 488
                              ELSE 477
                              end,
                              --                        477,
                              NEW.ruangan_id,
                              new.instalasi_id,
                              NEW.created_by,
                              NEW.pasienadmisi_id
                              ) RETURNING pasienmasukpenunjang_id INTO idPenunjang;
                          END IF;
                                      
                      -- 								INSERT INTO tindakanpelayanan_t (
                      --                                 pasienmasukpenunjang_id,
                      --                                 kelaspelayanan_id,
                      --                                 pasien_id,
                      --                                 instalasi_id,
                      --                                 daftartindakan_id,
                      --                                 tipepaket_id,
                      --                                 carabayar_id,
                      --                                 pendaftaran_id,
                      --                                 jeniskasuspenyakit_id,
                      --                                 ruangan_id,
                      --                                 penjamin_id,
                      --                                 tgl_tindakan,
                      --                                 dokterpenanggungjawab_id,
                      --                                 tarif_satuan,
                      --                                 qty_tindakan,
                      --                                 tarif_tindakan,
                      --                                 tarifcyto_tindakan,
                      --                                 cyto_tindakan,
                      --                                 discount_tindakan,
                      --                                 additional_data,
                      --                                 created_by
                      --              ) SELECT 
                      --                                 idPenunjang as pasienmasukpenunjang_id,
                      --                                 kelaspelayanan_id,
                      --                                 NEW.pasien_id as pasien_id,
                      --                                 instalasi_id,
                      --                                 daftartindakan_id,
                      --                                 tipepaket_id,
                      --                                 carabayar_id,
                      --                                 NEW.pendaftaran_id as pendaftaran_id,
                      --                                 jeniskasuspenyakit_id,
                      --                                 ruangan_id,
                      --                                 penjamin_id,
                      --                                 tgl_tindakan,
                      --                                 dokterpenanggungjawab_id,
                      --                                 tarif_satuan,
                      --                                 qty_tindakan,
                      --                                 tarif_tindakan,
                      --                                 tarifcyto_tindakan,
                      --                                 cyto_tindakan,
                      --                                 discount_tindakan,
                      --                                 additional_data,
                      --                                 NEW.created_by as created_by
                      --                 FROM json_populate_recordset(null::tindakanpelayanan_t,vPenunjang::json);
                      END IF;
                  
                      -- Pendafatran Online
                      IF (paramJson::json->>'pendaftaranol_id' IS NOT NULL) THEN
                          UPDATE pendaftaranol_t
                              SET pendaftaran_id = NEW.pendaftaran_id,
                              status_daftar_ol = 565
                          WHERE pendaftaranol_id = (paramJson::json->>'pendaftaranol_id')::INTEGER;           
                      END IF;
                  
                  -- Insert Admisi
                   IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
                          INSERT INTO pasienadmisi_t (
                                          pendaftaran_id,
                                          carabayar_id,
                                          penjamin_id,
                                          ruangan_id,
                                          pasien_id,
                                          kamarruangan_id,
                                          kamartempattidur_id,
                                          kelaspelayanan_id,
                                          pegawai_id,
                                          tgl_admisi,
                                          tgl_pendaftaran,
                                          kunjungan,
                                          bpjs_id,
                                          status_ranap,
                                          status_verifikasi,
                                          is_skd,
                                          is_pasientitipan,
                                          created_by,
                                          is_aps,
                                          asuransipasien_id,
                                                                      kamar_titipan_id,
                                                                      kelas_ditagihkan_id,
                                                                      ruangan_titipan_id,
                                                                      limit_tagihan,
                                                                      hakkelas_id,
                                                                      kelaspermintaan_id,
                                                                      dokterpengirim_id,
                                                                      dokterkonsul_id
                                  
                       ) VALUES (
                                          NEW.pendaftaran_id,
                                          vcarabayar_id,
                                          vpenjamin_id,
                                          (vadmisi::json->>'ruangan_id')::INTEGER,
                                          NEW.pasien_id,
                                          (vadmisi::json->>'kamarruangan_id')::INTEGER,
                                          (vadmisi::json->>'kamartempattidur_id')::INTEGER,
                                          (vadmisi::json->>'kelaspelayanan_id')::INTEGER,
                                          (vadmisi::json->>'pegawai_id')::INTEGER,
                                          (vadmisi::json->>'tgl_admisi')::TIMESTAMP,
                                          (vadmisi::json->>'tgl_pendaftaran')::TIMESTAMP,
                                          (vadmisi::json->>'kunjungan')::INTEGER,
                                          (vadmisi::json->>'bpjs_id')::INTEGER,
                                          (vadmisi::json->>'status_ranap')::INTEGER,
                                          549,
                                          (vadmisi::json->>'is_skd')::BOOL,
                                          (vadmisi::json->>'is_pasientitipan')::BOOL,
                                          NEW.created_by,
                                          (vadmisi::json->>'is_aps')::BOOL,
                                          idAsuransi,
                                                                      (vadmisi::json->>'kamar_titipan_id')::INTEGER,
                                                                       (vadmisi::json->>'kelas_ditagihkan_id')::INTEGER,
                                                                       (vadmisi::json->>'ruangan_titipan_id')::INTEGER,
                                                                       (vadmisi::json->>'limit_tagihan')::float8,
                                                                       (vadmisi::json->>'hakkelas_id')::INTEGER,
                                                                       (vadmisi::json->>'kelaspermintaan_id')::INTEGER,
                                           (vadmisi::json->>'dokterpengirim_id')::INTEGER,
                                                                       (vadmisi::json->>'dokterkonsul_id')
                                          
                                  
                                          
                       ) RETURNING pasienadmisi_id INTO vpasienadmisi_id;
                          
                          NEW.pasienadmisi_id = vpasienadmisi_id;
                      
              
                          UPDATE pendaftaran_t
                          SET is_ranap = TRUE
                          WHERE pendaftaran_id = vpendaftaranasal_id;
                           -- Insert Masuk Kamar
                          IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
                              INSERT INTO masukkamar_t (
                                              pasienadmisi_id,
                                              carabayar_id,
                                              penjamin_id,
                                              ruangan_id,
                                              pegawai_id,
                                              kelaspelayanan_id,
                                              kamartempattidur_id,
                                              kamarruangan_id,
                                              tgl_masukkamar,
                                              jam_masukkamar,
                                              created_by
                           ) VALUES (
                                              vpasienadmisi_id,
                                              vcarabayar_id,
                                              vpenjamin_id,
                                              (vmasukkamar::json->>'ruangan_id')::INTEGER,
                                              (vmasukkamar::json->>'pegawai_id')::INTEGER,
                                              (vmasukkamar::json->>'kelaspelayanan_id')::INTEGER,
                                              (vmasukkamar::json->>'kamartempattidur_id')::INTEGER,
                                              (vmasukkamar::json->>'kamarruangan_id')::INTEGER,
                                              (vmasukkamar::json->>'tgl_masukkamar')::DATE,
                                              (vmasukkamar::json->>'jam_masukkamar')::TIME,
                                              NEW.created_by
                           ) ;
                          END IF;
                           
                          SELECT 
                              CASE pasien_m.jeniskelamin::int4
                                  WHEN 15 THEN 4
                                                      WHEN 16 THEN 3
                                                  ELSE 6
                              END INTO vkettempattidur_id
                          FROM pasien_m
                          WHERE pasien_id = (vadmisi::json->>'pasien_id')::INTEGER;
              
                          UPDATE kamartempattidur_m
                          SET status_isi = TRUE,
                                      kettempattidur_id = vkettempattidur_id
                          WHERE kamartempattidur_id = (vadmisi::json->>'kamartempattidur_id')::INTEGER;
              
                          IF(COALESCE(vkelahiran_id,0) <> 0)
                          THEN
                              UPDATE kelahiranbayi_t
                              SET pendaftaranbaru_id = NEW.pendaftaran_id,
                                      last_modified_by = NEW.created_by,
                                      last_modified_date = CURRENT_TIMESTAMP
                              WHERE kelahiranbayi_id = vkelahiran_id;
                          END IF;
                          
                   END IF;
                      
                      
                       -- Tagihan Karcis
              --          IF (countTagihan > 0) THEN
              --                 INSERT INTO tindakanpelayanan_t (
              --                                 kelaspelayanan_id,
              --                                 pasien_id,
              --                                 instalasi_id,
              --                                 daftartindakan_id,
              --                                                                 tipepaket_id,
              --                                 carabayar_id,
              --                                 pendaftaran_id,
              --                                 jeniskasuspenyakit_id,
              --                                 ruangan_id,
              --                                 penjamin_id,
              --                                 tgl_tindakan,
              --                                 dokterpenanggungjawab_id,
              --                                 tarif_satuan,
              --                                 qty_tindakan,
              --                                 tarif_tindakan,
              --                                 tarifcyto_tindakan,
              --                                 cyto_tindakan,
              --                                 discount_tindakan,
              --                                 pasienadmisi_id,
              --                                 additional_data,
              --                                 created_by
              --              ) SELECT 
              --                                 kelaspelayanan_id,
              --                                 NEW.pasien_id as pasien_id,
              --                                 instalasi_id,
              --                                 daftartindakan_id,
              --                                                                 tipepaket_id,
              --                                 carabayar_id,
              --                                 NEW.pendaftaran_id as pendaftaran_id,
              --                                 jeniskasuspenyakit_id,
              --                                 ruangan_id,
              --                                 penjamin_id,
              --                                 tgl_tindakan,
              --                                 dokterpenanggungjawab_id,
              --                                 tarif_satuan,
              --                                 qty_tindakan,
              --                                 tarif_tindakan,
              --                                 tarifcyto_tindakan,
              --                                 cyto_tindakan,
              --                                 discount_tindakan,
              --                 NEW.pasienadmisi_id,
              --                                 additional_data,
              --                                 NEW.created_by as created_by
              --                 FROM json_populate_recordset(null::tindakanpelayanan_t,dataKarcis::json);
              --          END IF;
              
              RETURN NEW;
              END
              \$function\$
       ;
       ";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230926_091329_migrate_GLBJ268_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_091329_migrate_GLBJ268_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
