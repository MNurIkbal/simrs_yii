<?php

use yii\db\Migration;

/**
 * Class m201102_074023_migrate_mhkn_20201102_pasientitipanlive_function
 */
class m201102_074023_migrate_mhkn_20201102_pasientitipanlive_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_inap\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE

    vadmisi VARCHAR;
    vmasukkamar VARCHAR;
    paramJson VARCHAR;
    dataAsuransi VARCHAR;
    vpasienadmisi_id int4;
    vuser_id int4;
    vpendaftaran_id int4;
    vcarabayar_id int4;
    vpenjamin_id int4;
    vkettempattidur_id int4; 
    vkelahiran_id int4;
    vpendaftaranasal_id int4;
    noAsuransi VARCHAR;
    idAsuransi INTEGER;
    idPasienAsuransi INTEGER;
    -- Count Tagihan
    countTagihan INTEGER;
    dataKarcis VARCHAR;
    instalasiId INTEGER;
BEGIN
    paramJson := NEW.additional_data;
    vuser_id := NEW.last_modified_by;
    vpendaftaran_id := NEW.pendaftaran_id;
    vcarabayar_id := NEW.carabayar_id;
    vpenjamin_id := NEW.penjamin_id;
    instalasiId := NEW.instalasi_id;
  dataKarcis := paramJson::json->>'tarif';
    countTagihan := json_array_length(dataKarcis::json);
    vadmisi := paramJson::json->>'pasien_admisi';
    vmasukkamar := paramJson::json->>'masuk_kamar';
    dataAsuransi := paramJson::json->>'asuransi';
    vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
    vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
    
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
                        carabayar_id
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
                        NEW.carabayar_id
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
                        
                END IF;
         END IF;
 
 
     -- Insert Admisi
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            instalasiId := 3;
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
                            is_aps,
                            created_by,
                            asuransipasien_id,
                                                        kamar_titipan_id,
                                                        kelas_ditagihkan_id,
                                                        ruangan_titipan_id
         ) VALUES (
                            vpendaftaran_id,
                            (vadmisi::json->>'carabayar_id')::INTEGER,
              (vadmisi::json->>'penjamin_id')::INTEGER,
                            (vadmisi::json->>'ruangan_id')::INTEGER,
                            (vadmisi::json->>'pasien_id')::INTEGER,
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
                            (vadmisi::json->>'is_aps')::BOOL,
                            vuser_id,
                            idAsuransi,
                                                         (vadmisi::json->>'kamar_titipan_id')::INTEGER,
                                                         (vadmisi::json->>'kelas_ditagihkan_id')::INTEGER,
                                                         (vadmisi::json->>'ruangan_titipan_id')::INTEGER
                    
                            
         ) RETURNING pasienadmisi_id INTO vpasienadmisi_id;
    
--          UPDATE pendaftaran_t
--          SET pasienadmisi_id = vpasienadmisi_id
--          WHERE pendaftaran_id = vpendaftaran_id;
            
            NEW.pasienadmisi_id = vpasienadmisi_id;
            NEW.is_ranap = true;
            
--          UPDATE pendaftaran_t
--          SET is_ranap = TRUE 
--          WHERE pendaftaran_id = vpendaftaranasal_id;
            
     END IF;

         -- Tagihan Karcis
--          IF (countTagihan > 0) THEN
--                 INSERT INTO tindakanpelayanan_t (
--                                 kelaspelayanan_id,
--                                 pasien_id,
--                                 instalasi_id,
--                                 daftartindakan_id,
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
--                                 instalasiId,
--                                 daftartindakan_id,
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
                            vuser_id
         ) ;
     
     SELECT 
            CASE pasien_m.jeniskelamin::int4
                WHEN 15 THEN 4
                ELSE 3
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
            SET pendaftaranbaru_id = vpendaftaran_id,
                    last_modified_by = vuser_id,
                    last_modified_date = CURRENT_TIMESTAMP
            WHERE kelahiranbayi_id = vkelahiran_id;
        END IF;
     END IF;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
        ");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

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
    
    -- Count Tagihan
    countTagihan INTEGER;
    countPenunjang INTEGER;
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
BEGIN
    vcarabayar_id := NEW.carabayar_id;
    vpenjamin_id := NEW.penjamin_id;
    
        SELECT 
            CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(substring(no_pendaftaran FROM '[0-9]+')), 6) AS INT), 0) + 1 AS                VARCHAR(6)), 6, '0')) last_no
        INTO 
             vNumber
        FROM pendaftaran_t where instalasi_id = NEW.instalasi_id;
    
        SELECT 
                instalasi_singkatan 
    INTO 
       vPrefix 
    FROM instalasi_m WHERE instalasi_id = NEW.instalasi_id;

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
                            nama_bin,
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
                                                        additional_pasien
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
                            dataPasien::json->>'nama_bin',
                            
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
                                                        dataPasien::json->>'additional_identitas'
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
            nama_bin = (dataPasien::json->>'nama_bin'),
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
            additional_pasien = (dataPasien::json->>'additional_identitas')
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
                        carabayar_id
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
                        NEW.carabayar_id
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
                    created_by
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
                    NEW.created_by
                ) RETURNING penanggungjawab_id INTO penanggungId;
                                NEW.penanggungjawab_id = penanggungId;
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
                     
         -- Set Antrian 
         IF (dataAntrian::json->>'jenisantrian_id' IS NOT NULL AND NEW.antrian_id IS NULL) THEN
                     --get konfigantrian
                    SELECT konfigantrian_id INTO v_konfigantrian
                    from konfigantrian_m
                    WHERE jenisantrian_id = (dataAntrian::json->>'jenisantrian_id')::INTEGER 
                    and konfigantrian_m.is_deleted=FALSE 
                    and konfigantrian_m.is_active=true
                    limit 1;
 
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
                                konfigantrian_id
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
                                v_konfigantrian
             ) RETURNING antrian_id INTO antrianId;
                NEW.antrian_id = antrianId;
                
                --- Ini Kondisi Penunjang GET nomor antrian untuk pasien masuk penunjang
                IF (NEW.carabayar_id != 5 AND countPenunjang > 0) THEN
                        SELECT 
                            no_antrian
                        INTO 
                            noAntrian
                        FROM antrian_t WHERE antrian_id = antrianId;
                END IF;
         ELSE
                -- Update Antrian Pendaftaran
                UPDATE antrian_t SET 
                    pendaftaran_id = NEW.pendaftaran_id, 
                    pasien_id = NEW.pasien_id 
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
                    UPDATE antrian_t SET 
                        pendaftaran_id = NEW.pendaftaran_id, 
                        pasien_id = NEW.pasien_id, 
                        ruangan_id = NEW.ruangan_id,
                        carabayar_id = NEW.carabayar_id,
                        is_active = isActive
                    WHERE antrianasal_id = NEW.antrian_id;  
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
        IF (countPenunjang > 0) then
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
                        created_by
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
                        NEW.created_by
                ) RETURNING pasienmasukpenunjang_id INTO idPenunjang;
                
--                              INSERT INTO tindakanpelayanan_t (
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
                                                        ruangan_titipan_id
                    
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
                                                         (vadmisi::json->>'ruangan_titipan_id')::INTEGER
                            
                    
                            
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
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
            
        ");

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tarifkomponenkamarrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'kamar\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric, "kamarruangan_jenis" int4, "kamarruangan_jenis_nama" varchar, "status_isi" bool, "jeniskasuspenyakit_id" int4, "jeniskasuspenyakit_nama" varchar, "kettempattidur_id" int4, "kettempattidur_nama" varchar, "kode_warna" varchar, "kettempattidur_warna" varchar, "kamartempattidur_id" int4, "no_tempattidur" varchar, "isi_jk" int4) AS $BODY$ 
DECLARE vpenjamin_id int4;
BEGIN
    IF(xruangan_id = 0)
    THEN
        xruangan_id := NULL;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        xkelaspelayanan_id := NULL;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;
    
    RETURN QUERY 
    SELECT *FROM (
        SELECT      
            \'kamar\'::text AS jenis,
            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            kamarruangan_m.ruangan_id as ruangan_id,
            ruangan_m.ruangan_nama as ruangan_nama,
            ruangan_m.instalasi_id as instalasi_id,
            instalasi_m.instalasi_nama as instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
             perdatarif_m.perdanama_sk as perdanama_sk,
            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id,
            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
            COALESCE(tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id,
            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            tariftindakan_m.komponentarif_id as komponentarif_id,
            komponentarif_m.komponentarif_nama as komponentarif_nama,
            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan,
            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan,
            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan,
            NULL::BOOLEAN AS is_default,
            daftartindakan_m.is_akomodasi as is_akomodasi,
            penjamin_m.carabayar_id as carabayar_id,
            daftartindakan_m.is_konsultasi as is_konsultasi,
            kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
            tariftindakan_m.kamarruangan_id as kamarruangan_id,
            NULL::int4 as ambulan_id,
            NULL::VARCHAR as no_polisi,
            NULL::integer as kelompokpemeriksaanlab_id,
            NULL::character varying AS nama_kelompok,
            NULL::integer AS jenispemeriksaanlab_id,
            NULL::character varying AS jenispemeriksaanlab_nama,
            NULL::integer AS pemeriksaanlab_id,
            NULL::character varying AS pemeriksaanlab_nama,
            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit ,
            kamarruangan_m.kamarruangan_jenis,
            fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS kamarruangan_jenis_nama,
            kamartempattidur_m.status_isi,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kettempattidur_m.kettempattidur_id,
            kettempattidur_m.kettempattidur_nama,
            kettempattidur_m.kode_warna,
            kettempattidur_m.kettempattidur_warna,
            kamartempattidur_m.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            ( SELECT pasien_m.jeniskelamin
           FROM ((pendaftaran_t
             JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
          WHERE ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id) AND (pasienadmisi_t.pasienpulang_id IS NULL) AND (pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441])))
         LIMIT 1)::int4 AS isi_jk
         FROM tariftindakan_m
         JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
         JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
         JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
         LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
         JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
         JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
         JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
         JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
         JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id
         LEFT JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         LEFT JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id
         LEFT JOIN (
                SELECT tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
             JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE 
                tariftindakan_m.is_deleted = false AND
                tariftindakan_m.is_active = true AND
                tariftindakan_m.tarifparent_id IS NULL AND
                kamarruangan_m.is_deleted = FALSE AND
                kamarruangan_m.is_active = TRUE AND
                perdatarif_m.is_active = true AND
                tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
         ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
        WHERE 
            tariftindakan_m.is_deleted = false AND
            tariftindakan_m.is_active = true AND
            tariftindakan_m.tarifparent_id IS NULL AND
            kamarruangan_m.is_deleted = FALSE AND
            kamarruangan_m.is_active = TRUE AND
            perdatarif_m.is_active = true AND
            tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
    ) AS x
    WHERE x.ruangan_id = COALESCE(xruangan_id, x.ruangan_id)
    AND x.kelaspelayanan_id = COALESCE(xkelaspelayanan_id, x.kelaspelayanan_id)
    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kamarruangan_jenis, x.kamarruangan_jenis_nama , x.status_isi, x.jeniskasuspenyakit_id, x.jeniskasuspenyakit_nama, x.kettempattidur_id, x.kettempattidur_nama, x.kode_warna, x.kettempattidur_warna, x.kamartempattidur_id  , x.no_tempattidur , x.isi_jk;
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
            
        ');

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tarifkomponenrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'pelayanan\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric) AS $BODY$ 

            DECLARE 

                vpenjamin_id int4;

            BEGIN

                IF(xruangan_id = 0)
                THEN
                    SELECT default_ruangan INTO xruangan_id
                    FROM konfigtarif_k
                    WHERE konfigtarif_id = 1;
                END IF;
                
                IF(xkelaspelayanan_id = 0)
                THEN
                    SELECT default_kelas INTO xkelaspelayanan_id
                    FROM konfigtarif_k
                    WHERE konfigtarif_id = 1;
                END IF;
                
                SELECT default_penjamin INTO vpenjamin_id
                FROM konfigtarif_k
                WHERE konfigtarif_id = 1;
                
                IF(xpenjamin_id = vpenjamin_id) 
                    THEN xpenjamin_id := 0;
                END IF;
                
                
            IF(xtipe = \'pelayanan\')
                THEN
                    RETURN QUERY 
                        SELECT * FROM 
                            (SELECT \'tindakan\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id,
                            r_tindakan.ruangan_nama,
                            r_tindakan.instalasi_id,
                            ins_tindakan.instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
                            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                            daftartindakan_m.kelompoktindakan_id,
                            kelompoktindakan_m.kelompoktindakan_nama,
                            daftartindakan_m.kategoritindakan_id,
                            kategoritindakan_m.kategoritindakan_nama,
                            COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            tariftindakan_m.komponentarif_id,
                            komponentarif_m.komponentarif_nama,
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default,
                            daftartindakan_m.is_akomodasi,
                            penjamin_m.carabayar_id,
                            daftartindakan_m.is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            NULL::int4 as kamarruangan_id,
                            NULL::int4 as ambulan_id,
                            NULL::VARCHAR as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                        FROM tariftindakan_m
                            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                            JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
                            JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
                            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id          
                            LEFT JOIN   (SELECT tariftindakan_m.daftartindakan_id,
                                                tariftindakan_m.tariftindakan_id,
                                                tariftindakan_m.kelaspelayanan_id,
                                                tariftindakan_m.penjamin_id,
                                                tariftindakan_m.harga_tariftindakan,
                                                tariftindakan_m.persencyto_tindakan,
                                                tariftindakan_m.persendiskon_tindakan,
                                                tariftindakan_m.perdatarif_id,
                                                penjamin_m.penjamin_nama,
                                                tariftindakan_m.komponentarif_id
                                        FROM tariftindakan_m
                                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                        WHERE komponentarif_m.is_deleted IS FALSE
                                            AND perdatarif_m.is_active = TRUE 
                                            AND tariftindakan_m.is_deleted = FALSE 
                                            AND tariftindakan_m.is_active = TRUE 
                                            AND tariftindakan_m.tarifparent_id IS NULL
                                            AND tariftindakan_m.komponentarif_id <> 6
                                            AND tariftindakan_m.penjamin_id = xpenjamin_id
                                        ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                        WHERE tindakanruangan_mp.is_deleted = false 
                            AND komponentarif_m.is_deleted IS FALSE
                            AND perdatarif_m.is_active = true 
                            AND tariftindakan_m.is_deleted = false 
                            AND tariftindakan_m.is_active = true 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id

                   
            UNION ALL
                    SELECT \'paket\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            paketruangan_mp.ruangan_id,
                            r_paket.ruangan_nama,
                            r_paket.instalasi_id,
                            ins_paket.instalasi_nama,
                            paketruangan_mp.ruangan_id AS ruanganpaket_id,
                            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
                            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                            NULL::integer AS kelompoktindakan_id,
                            NULL::character varying AS kelompoktindakan_nama,
                            NULL::integer AS kategoritindakan_id,
                            NULL::character varying AS kategoritindakan_nama,
                            tariftindakan_m.daftartindakan_id AS daftartindakan_id,
                            NULL::character varying AS daftartindakan_nama,
                            tariftindakan_m.tipepaket_id,
                            tipepaket_m.tipepaket_nama,
                            tariftindakan_m.komponentarif_id,
                            komponentarif_m.komponentarif_nama,
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                            paketruangan_mp.is_default,
                            NULL::boolean AS is_akomodasi,
                            penjamin_m.carabayar_id,
                            false AS is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            NULL::int4 as kamarruangan_id,
                            NULL::int4 as ambulan_id,
                            NULL::VARCHAR as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                    FROM tariftindakan_m
                      JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                      JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                      JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                      JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                       JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                    AND perdatarif_m.is_active = TRUE
                                                                    AND perdatarif_m.is_deleted = FALSE
                      JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                                                                            AND paketruangan_mp.is_deleted = FALSE 
                      JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
                      JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
                      LEFT JOIN   (SELECT tariftindakan_m.tipepaket_id,
                                                                        tariftindakan_m.daftartindakan_id,
                                          tariftindakan_m.tariftindakan_id,
                                          tariftindakan_m.kelaspelayanan_id,
                                          tariftindakan_m.penjamin_id,
                                          tariftindakan_m.harga_tariftindakan,
                                          tariftindakan_m.persencyto_tindakan,
                                          tariftindakan_m.persendiskon_tindakan,
                                          tariftindakan_m.perdatarif_id,
                                          penjamin_m.penjamin_nama,
                                          tariftindakan_m.komponentarif_id
                                  FROM tariftindakan_m
                                   JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                and perdatarif_m.is_active = TRUE
                                                                                                and perdatarif_m.is_deleted = FALSE
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE tariftindakan_m.is_deleted = FALSE 
                                                                AND tariftindakan_m.is_active = TRUE 
                                                                AND tariftindakan_m.komponentarif_id <> 6
                                                                AND tariftindakan_m.penjamin_id = xpenjamin_id
                                  ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id
                                                                                        AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                                                                                        AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                    WHERE tariftindakan_m.is_deleted = false 
                      AND tariftindakan_m.is_active = true 
                      AND tariftindakan_m.komponentarif_id <> 6
                      AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.ruangan_id = xruangan_id
                      AND x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

            ELSEIF(xtipe = \'kamar\')
                THEN
                    RETURN QUERY 
                    SELECT * FROM 
                      (SELECT \'kamar\'::text AS jenis,
                              COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                              kamarruangan_m.ruangan_id as ruangan_id,
                              ruangan_m.ruangan_nama as ruangan_nama,
                              ruangan_m.instalasi_id as instalasi_id,
                              instalasi_m.instalasi_nama as instalasi_nama,
                              NULL::integer AS ruanganpaket_id,
                              NULL::character varying AS ruanganpaket_nama,
                              COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
                              perdatarif_m.perdanama_sk as perdanama_sk,
                              tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                              kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                              COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id,
                              COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                              daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                              kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
                              daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                              kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
                              COALESCE(tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id,
                              daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                              NULL::integer AS tipepaket_id,
                              NULL::character varying AS tipepaket_nama,
                              tariftindakan_m.komponentarif_id as komponentarif_id,
                              komponentarif_m.komponentarif_nama as komponentarif_nama,
                              COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan,
                              COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan,
                              COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan,
                              NULL::BOOLEAN AS is_default,
                              daftartindakan_m.is_akomodasi as is_akomodasi,
                              penjamin_m.carabayar_id as carabayar_id,
                              daftartindakan_m.is_konsultasi as is_konsultasi,
                              kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
                              tariftindakan_m.kamarruangan_id as kamarruangan_id,
                              NULL::int4 as ambulan_id,
                              NULL::VARCHAR as no_polisi,
                              NULL::integer as kelompokpemeriksaanlab_id,
                              NULL::character varying AS nama_kelompok,
                              NULL::integer AS jenispemeriksaanlab_id,
                              NULL::character varying AS jenispemeriksaanlab_nama,
                              NULL::integer AS pemeriksaanlab_id,
                              NULL::character varying AS pemeriksaanlab_nama,
                              COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                      FROM tariftindakan_m
                        JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                        LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                        JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                          tariftindakan_m.tariftindakan_id,
                                          tariftindakan_m.kelaspelayanan_id,
                                          tariftindakan_m.penjamin_id,
                                          tariftindakan_m.harga_tariftindakan,
                                          tariftindakan_m.persencyto_tindakan,
                                          tariftindakan_m.persendiskon_tindakan,
                                          tariftindakan_m.perdatarif_id,
                                          penjamin_m.penjamin_nama,
                                          tariftindakan_m.komponentarif_id,
                                          tariftindakan_m.kamarruangan_id
                                  FROM tariftindakan_m
                                    JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                                    JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                                    LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                    JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                                    JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                                  WHERE tariftindakan_m.is_deleted = FALSE 
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL 
                                    AND kamarruangan_m.is_deleted = FALSE 
                                    AND kamarruangan_m.is_active = TRUE 
                                    AND perdatarif_m.is_active = true 
                                    AND tariftindakan_m.komponentarif_id <> 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                                                AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
                      WHERE tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND kamarruangan_m.is_deleted = FALSE 
                        AND kamarruangan_m.is_active = TRUE 
                        AND perdatarif_m.is_active = TRUE 
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                      ) AS x
                  WHERE x.ruangan_id = xruangan_id
                    AND x.kelaspelayanan_id = xkelaspelayanan_id
                  GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                
            ELSEIF(xtipe = \'ambulan\')
              THEN
                RETURN QUERY 
                  SELECT * FROM 
                    (SELECT \'ambulan\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id,
                            NULL::integer as ruangan_id,
                            NULL::character varying as ruangan_nama,
                            NULL::integer as instalasi_id,
                            NULL::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            COALESCE(tariftindakan_m.kelaspelayanan_id, 3)::integer as kelaspelayanan_id,
                            COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'Kelas 3\')::character varying AS kelaspelayanan_nama,
                            COALESCE(tariftindakan_m.penjamin_id, 1)::integer AS penjamin_id ,
                            COALESCE(penjamin_m.penjamin_nama, \'Perseorangan\')::character varying AS penjamin_nama,
                            daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                            null::VARCHAR as kelompoktindakan_nama,
                            daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                            null::VARCHAR as kategoritindakan_nama,
                            ambulandetail_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            COALESCE(tariftindakan_m.komponentarif_id, 6) as komponentarif_id,
                            COALESCE(komponentarif_m.komponentarif_nama, \'Total Tarif\')::character varying as komponentarif_nama,
                            COALESCE(tarif_penjamin.harga_tariftindakan, COALESCE((tariftindakan_m.harga_tariftindakan), 0)) AS harga_tariftindakan,
                            COALESCE(tarif_penjamin.persencyto_tindakan, COALESCE((tariftindakan_m.persencyto_tindakan), 0)) AS persencyto_tindakan,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, COALESCE((tariftindakan_m.persendiskon_tindakan), 0)) AS persencyto_tindakan,
                            ambulandetail_m.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            penjamin_m.carabayar_id as carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            tariftindakan_m.kamarruangan_id as kamarruangan_id,
                            ambulan_m.ambulan_id as ambulan_id,
                            ambulan_m.no_polisi as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                    FROM ambulan_m
                      JOIN ambulandetail_m ON ambulan_m.ambulan_id = ambulandetail_m.ambulan_id
                      JOIN daftartindakan_m ON ambulandetail_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                      LEFT JOIN tariftindakan_m ON ambulandetail_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
                      JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                      LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                      LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                      JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                      LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id
                                FROM tariftindakan_m
                                  JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                  JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                  JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                WHERE komponentarif_m.is_deleted IS FALSE
                                    AND perdatarif_m.is_active = TRUE 
                                    AND tariftindakan_m.is_deleted = FALSE 
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id <> 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                                ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                   AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                        WHERE ambulan_m.is_deleted = FALSE 
                          AND ambulan_m.is_active = TRUE
                          AND ambulandetail_m.is_deleted = FALSE 
                          AND ambulandetail_m.is_active = TRUE 
                          AND tariftindakan_m.is_deleted = FALSE 
                          AND tariftindakan_m.is_active = TRUE 
                          AND perdatarif_m.is_active = TRUE 
                          AND perdatarif_m.is_deleted = FALSE 
                          AND tariftindakan_m.komponentarif_id <> 6 
                          AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                  WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

            ELSEIF(xtipe = \'penunjang\')
              THEN
                RETURN QUERY
                  SELECT * FROM 
                    (SELECT \'lab\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            null::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying kategoritindakan_nama,
                            tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            tariftindakan_m.komponentarif_id as komponentarif_id,
                            komponentarif_m.komponentarif_nama as komponentarif_nama,
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            penjamin_m.carabayar_id as carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            pemeriksaanlab_m.kelompokpemeriksaanlab_id::int4,
                            kelompokpemeriksaanlab_m.nama_kelompok::character varying ,
                            pemeriksaanlab_m.jenispemeriksaanlab_id::int4,
                            jenispemeriksaanlab_m.jenispemeriksaanlab_nama::character varying ,
                            pemeriksaanlab_m.pemeriksaanlab_id::int4,
                            pemeriksaanlab_m.pemeriksaanlab_nama::character varying ,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
                    FROM tariftindakan_m
                      JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                      JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                            AND pemeriksaanlab_m.is_deleted = FALSE
                      JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                      JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
                      JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                      JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                        AND perdatarif_m.is_deleted = FALSE 
                                        AND perdatarif_m.is_active = TRUE
                      JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                      JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                      JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                              AND tindakanruangan_mp.is_deleted = FALSE
                      JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                      LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id
                                FROM tariftindakan_m
                                  JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                  JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                  JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                      AND perdatarif_m.is_active = TRUE 
                      AND tariftindakan_m.is_deleted = FALSE 
                      AND tariftindakan_m.is_active = TRUE 
                      AND tariftindakan_m.komponentarif_id <> 6
                      AND tariftindakan_m.penjamin_id = xpenjamin_id
                    ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                    WHERE  tariftindakan_m.is_deleted = FALSE 
                      AND tariftindakan_m.is_active = TRUE 
                      AND tariftindakan_m.tarifparent_id IS NULL 
                      AND perdatarif_m.is_active = TRUE 
                      AND tariftindakan_m.komponentarif_id <> 6
                      AND tariftindakan_m.penjamin_id = vpenjamin_id
            UNION ALL         
                SELECT 
                \'paket_lab\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying AS instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::integer as kelompoktindakan_id,
                null::character varying  as kelompoktindakan_nama,
                null::integer as kategoritindakan_id,
                null::character varying  as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id  AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                tariftindakan_m.tipepaket_id  AS tipepaket_id,
                tipepaket_m.tipepaket_nama AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                paketruangan_mp.is_default AS is_default,
                null::boolean as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                null::boolean as is_konsultasi,
                null::character varying  as  kamarruangan_nokamar,
                null::integer as kamarruangan_id,
                null::integer as ambulan_id,
                null::character varying  as no_polisi,
                kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id as kelompokpemeriksaanlab_id,
                kelompokpemeriksaanlab_m.nama_kelompok AS nama_kelompok,
                pemeriksaanlab_m.jenispemeriksaanlab_id AS jenispemeriksaanlab_id,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenispemeriksaanlab_nama,
                pemeriksaanlab_m.pemeriksaanlab_id AS pemeriksaanlab_id,
                pemeriksaanlab_m.pemeriksaanlab_nama AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, 0::numeric) AS persen_penyulit  
            FROM tariftindakan_m
                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
              JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                   AND paketruangan_mp.is_deleted = FALSE
              JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
              JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
              JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                 AND perdatarif_m.is_deleted = FALSE 
                               AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                    and pemeriksaanlab_m.is_deleted = FALSE
                LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                LEFT JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
                LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                                                  tariftindakan_m.tariftindakan_id,
                                                  tariftindakan_m.kelaspelayanan_id,
                                                  tariftindakan_m.penjamin_id,
                                                  tariftindakan_m.harga_tariftindakan,
                                                  tariftindakan_m.persencyto_tindakan,
                                                  tariftindakan_m.persendiskon_tindakan,
                                                  tariftindakan_m.perdatarif_id,
                                                  penjamin_m.penjamin_nama,
                                                  tariftindakan_m.komponentarif_id,
                                                  pemeriksaanlab_m.kelompokpemeriksaanlab_id,
                                                  pemeriksaanlab_m.jenispemeriksaanlab_id,
                                                  pemeriksaanlab_m.pemeriksaanlab_id
                                    FROM tariftindakan_m
                                      JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                         AND perdatarif_m.is_deleted = FALSE 
                                           AND perdatarif_m.is_active = TRUE
                                        JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                                                                    AND pemeriksaanlab_m.is_deleted = FALSE
                                    WHERE   tariftindakan_m.is_deleted = FALSE 
                          AND tariftindakan_m.is_active = TRUE 
                          AND   perdatarif_m.is_active = TRUE 
                          AND tariftindakan_m.komponentarif_id <> 6
                                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                                  ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                                    AND pemeriksaanlab_m.pemeriksaanlab_id = tarif_penjamin.pemeriksaanlab_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = vpenjamin_id 
            UNION ALL
              SELECT  \'rad\'::text AS jenis,
                      COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                      tindakanruangan_mp.ruangan_id as ruangan_id,
                      ruangan_m.ruangan_nama as ruangan_nama,
                      ruangan_m.instalasi_id as instalasi_id,
                      NULL::character varying as instalasi_nama,
                      NULL::integer AS ruanganpaket_id,
                      NULL::character varying AS ruanganpaket_nama,
                      tariftindakan_m.perdatarif_id AS perdatarif_id ,
                      perdatarif_m.perdanama_sk as perdanama_sk,
                      tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                      kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                      COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                      COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                      NULL::integer as kelompoktindakan_id,
                      NULL::character varying as kelompoktindakan_nama,
                      NULL::integer as kategoritindakan_id,
                      NULL::character varying as kategoritindakan_nama,
                      tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                      daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                      NULL::integer AS tipepaket_id,
                      NULL::character varying AS tipepaket_nama,
                      tariftindakan_m.komponentarif_id as komponentarif_id,
                      komponentarif_m.komponentarif_nama as komponentarif_nama,
                      COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                      COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                      COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                      tindakanruangan_mp.is_default AS is_default,
                      daftartindakan_m.is_akomodasi as is_akomodasi,
                      penjamin_m.carabayar_id as carabayar_id,
                      daftartindakan_m.is_konsultasi as is_konsultasi,
                      NULL::character varying as  kamarruangan_nokamar,
                      NULL::integer as kamarruangan_id,
                      NULL::integer as ambulan_id,
                      NULL::character varying as no_polisi,
                      pemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
                      kelompokpemeriksaanrad_m.nama_kelompok as nama_kelompok,
                      pemeriksaanrad_m.jenispemeriksaanrad_id as jenispemeriksaanlab_id,
                      jenispemeriksaanrad_m.jenispemeriksaanrad_nama as jenispemeriksaanlab_nama,
                      pemeriksaanrad_m.pemeriksaanradiologi_id as pemeriksaanlab_id,
                      pemeriksaanrad_m.pemeriksaanrad_nama  as pemeriksaanlab_nama,
                      COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
              FROM tariftindakan_m
                JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                      AND pemeriksaanrad_m.is_deleted = FALSE
                JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                  AND perdatarif_m.is_deleted = FALSE 
                                  AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                        AND tindakanruangan_mp.is_deleted = FALSE
                JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                  tariftindakan_m.tariftindakan_id,
                                  tariftindakan_m.kelaspelayanan_id,
                                  tariftindakan_m.penjamin_id,
                                  tariftindakan_m.harga_tariftindakan,
                                  tariftindakan_m.persencyto_tindakan,
                                  tariftindakan_m.persendiskon_tindakan,
                                  tariftindakan_m.perdatarif_id,
                                  penjamin_m.penjamin_nama,
                                  tariftindakan_m.komponentarif_id
                          FROM tariftindakan_m
                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                          WHERE komponentarif_m.is_deleted IS FALSE
                            AND perdatarif_m.is_active = TRUE 
                            AND tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = xpenjamin_id
                          ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
              WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
            UNION ALL
              SELECT  \'paket_rad\'::text AS jenis,
                        COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                        paketruangan_mp.ruangan_id as ruangan_id,
                        ruangan_m.ruangan_nama as ruangan_nama,
                        ruangan_m.instalasi_id as instalasi_id,
                        null::VARCHAR as instalasi_nama,
                        NULL::INTEGER AS ruanganpaket_id,
                        NULL::character varying AS ruanganpaket_nama,
                        tariftindakan_m.perdatarif_id AS perdatarif_id ,
                        perdatarif_m.perdanama_sk as perdanama_sk,
                        tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                        COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                        COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                        null::INTEGER as kelompoktindakan_id,
                        null::VARCHAR as kelompoktindakan_nama,
                        null::INTEGER as kategoritindakan_id,
                        null::VARCHAR as kategoritindakan_nama,
                        tariftindakan_m.daftartindakan_id  AS daftartindakan_id ,
                        tipepaket_m.tipepaket_nama as daftartindakan_nama,
                        tariftindakan_m.tipepaket_id  AS tipepaket_id,
                        tipepaket_m.tipepaket_nama AS tipepaket_nama,
                        tariftindakan_m.komponentarif_id as komponentarif_id,
                        komponentarif_m.komponentarif_nama as komponentarif_nama,
                        COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                        COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                        COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                        paketruangan_mp.is_default AS is_default,
                        null::BOOLEAN as is_akomodasi,
                        penjamin_m.carabayar_id as carabayar_id,
                        null::BOOLEAN as is_konsultasi,
                        null::VARCHAR as  kamarruangan_nokamar,
                        null::INTEGER as kamarruangan_id,
                        null::INTEGER as ambulan_id,
                        null::VARCHAR as no_polisi,
                        kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
                        kelompokpemeriksaanrad_m.nama_kelompok AS nama_kelompok,
                        pemeriksaanrad_m.jenispemeriksaanrad_id AS jenispemeriksaanlab_id,
                        jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenispemeriksaanlab_nama,
                        pemeriksaanrad_m.pemeriksaanradiologi_id AS pemeriksaanlab_id,
                        pemeriksaanrad_m.pemeriksaanrad_nama AS pemeriksaanlab_nama,
                        COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit      
              FROM tariftindakan_m
                    JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                    JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                                             AND paketruangan_mp.is_deleted = FALSE
                    JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                      AND perdatarif_m.is_deleted = FALSE 
                                                      AND perdatarif_m.is_active = TRUE
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                    JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                AND pemeriksaanrad_m.is_deleted = FALSE
                    LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                    LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id   
                    LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                                                        tariftindakan_m.tariftindakan_id,
                                                        tariftindakan_m.kelaspelayanan_id,
                                                        tariftindakan_m.penjamin_id,
                                                        tariftindakan_m.harga_tariftindakan,
                                                        tariftindakan_m.persencyto_tindakan,
                                                        tariftindakan_m.persendiskon_tindakan,
                                                        tariftindakan_m.perdatarif_id,
                                                        penjamin_m.penjamin_nama,
                                                        tariftindakan_m.komponentarif_id,
                                                        pemeriksaanrad_m.kelompokpemeriksaanrad_id,
                                                        pemeriksaanrad_m.jenispemeriksaanrad_id,
                                                        pemeriksaanrad_m.pemeriksaanradiologi_id
                                        FROM tariftindakan_m
                                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                                AND perdatarif_m.is_deleted = FALSE 
                                                                                AND perdatarif_m.is_active = TRUE
                                            JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                                        AND pemeriksaanrad_m.is_deleted = FALSE
                                        WHERE tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND perdatarif_m.is_active = TRUE 
                            AND tariftindakan_m.komponentarif_id <> 6 
                            AND tariftindakan_m.penjamin_id = xpenjamin_id
                                        ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                         AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                                         AND pemeriksaanrad_m.pemeriksaanradiologi_id = tarif_penjamin.pemeriksaanradiologi_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = vpenjamin_id                  
            UNION ALL
              SELECT \'operasi\'::text AS jenis,
                     COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                     tindakanruangan_mp.ruangan_id as ruangan_id,
                     ruangan_m.ruangan_nama as ruangan_nama,
                     ruangan_m.instalasi_id as instalasi_id,
                     NULL::character varying instalasi_nama,
                     NULL::integer AS ruanganpaket_id,
                     NULL::character varying AS ruanganpaket_nama,
                     tariftindakan_m.perdatarif_id AS perdatarif_id ,
                     perdatarif_m.perdanama_sk as perdanama_sk,
                     tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                     kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                     COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                     COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                     null::integer as kelompoktindakan_id,
                     NULL::character varying as kelompoktindakan_nama,
                     null::integer as kategoritindakan_id,
                     NULL::character varying as kategoritindakan_nama,
                     tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                     daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                     NULL::integer AS tipepaket_id,
                     NULL::character varying AS tipepaket_nama,
                     tariftindakan_m.komponentarif_id as komponentarif_id,
                     komponentarif_m.komponentarif_nama as komponentarif_nama,
                     COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                     COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                     COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                     tindakanruangan_mp.is_default AS is_default,
                     daftartindakan_m.is_akomodasi as is_akomodasi,
                     penjamin_m.carabayar_id as carabayar_id,
                     daftartindakan_m.is_konsultasi as is_konsultasi,
                     NULL::character varying as  kamarruangan_nokamar,
                     null::integer as kamarruangan_id,
                     null::integer as ambulan_id,
                     NULL::character varying as no_polisi,
                     operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
                     golonganoperasi_m.golonganoperasi_nama as nama_kelompok,
                     operasi_m.kegiatanoperasi_id as jenispemeriksaanlab_id,
                     kegiatanoperasi_m.kegiatanoperasi_nama as jenispemeriksaanlab_nama,
                     operasi_m.operasi_id as pemeriksaanlab_id,
                     operasi_m.operasi_nama  as pemeriksaanlab_nama,
                     COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
              FROM tariftindakan_m
                JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                               AND operasi_m.is_deleted = FALSE
                JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
                JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                  AND perdatarif_m.is_deleted = FALSE 
                                  AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                        AND tindakanruangan_mp.is_deleted = FALSE
                JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                  tariftindakan_m.tariftindakan_id,
                                  tariftindakan_m.kelaspelayanan_id,
                                  tariftindakan_m.penjamin_id,
                                  tariftindakan_m.harga_tariftindakan,
                                  tariftindakan_m.persencyto_tindakan,
                                  tariftindakan_m.persendiskon_tindakan,
                                  tariftindakan_m.perdatarif_id,
                                  penjamin_m.penjamin_nama,
                                  tariftindakan_m.komponentarif_id
                          FROM tariftindakan_m
                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                          WHERE komponentarif_m.is_deleted IS FALSE
                            AND perdatarif_m.is_active = TRUE 
                            AND tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id
                          ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                     AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                          WHERE tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND perdatarif_m.is_active = TRUE 
                            AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = xpenjamin_id
            UNION ALL
              SELECT  \'paket_operasi\'::text AS jenis,
                      COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                      paketruangan_mp.ruangan_id as ruangan_id,
                      ruangan_m.ruangan_nama as ruangan_nama,
                      ruangan_m.instalasi_id as instalasi_id,
                      null::VARCHAR as instalasi_nama,
                      NULL::integer AS ruanganpaket_id,
                      NULL::character varying AS ruanganpaket_nama,
                      tariftindakan_m.perdatarif_id AS perdatarif_id ,
                      perdatarif_m.perdanama_sk as perdanama_sk,
                      tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                      kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                      COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                      COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                      null::INTEGER as kelompoktindakan_id,
                      null::VARCHAR as kelompoktindakan_nama,
                      null::INTEGER as kategoritindakan_id,
                      null::VARCHAR as kategoritindakan_nama,
                      null::INTEGER AS daftartindakan_id ,
                      null::VARCHAR as daftartindakan_nama,
                      tariftindakan_m.daftartindakan_id  AS tipepaket_id,
                      tipepaket_m.tipepaket_nama AS tipepaket_nama,
                      tariftindakan_m.komponentarif_id as komponentarif_id,
                      komponentarif_m.komponentarif_nama as komponentarif_nama,
                      COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                      COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                      COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                      paketruangan_mp.is_default AS is_default,
                      null::BOOLEAN as is_akomodasi,
                      penjamin_m.carabayar_id as carabayar_id,
                      null::BOOLEAN as is_konsultasi,
                      null::VARCHAR as  kamarruangan_nokamar,
                      null::INTEGER as kamarruangan_id,
                      null::INTEGER as ambulan_id,
                      null::VARCHAR as no_polisi,
                      operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
                      golonganoperasi_m.golonganoperasi_nama AS nama_kelompok,
                      operasi_m.kegiatanoperasi_id AS jenispemeriksaanlab_id,
                      kegiatanoperasi_m.kegiatanoperasi_nama AS jenispemeriksaanlab_nama,
                      operasi_m.operasi_id AS pemeriksaanlab_id,
                      operasi_m.operasi_nama AS pemeriksaanlab_nama,
                      COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                FROM tariftindakan_m
                    JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                    JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                     AND paketruangan_mp.is_deleted = FALSE
                    JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                           AND perdatarif_m.is_deleted = FALSE 
                                 AND perdatarif_m.is_active = TRUE
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                    JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                               AND operasi_m.is_deleted = FALSE
                    LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                    LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id          
                    LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                                    tariftindakan_m.tariftindakan_id,
                                    tariftindakan_m.kelaspelayanan_id,
                                    tariftindakan_m.penjamin_id,
                                    tariftindakan_m.harga_tariftindakan,
                                    tariftindakan_m.persencyto_tindakan,
                                    tariftindakan_m.persendiskon_tindakan,
                                    tariftindakan_m.perdatarif_id,
                                    penjamin_m.penjamin_nama,
                                    tariftindakan_m.komponentarif_id,
                                    operasi_m.golonganoperasi_id,
                                    operasi_m.kegiatanoperasi_id,
                                    operasi_m.operasi_id
                            FROM tariftindakan_m
                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                      AND perdatarif_m.is_deleted = FALSE 
                                              AND perdatarif_m.is_active = TRUE
                                JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                                                             AND operasi_m.is_deleted = FALSE
                            WHERE   tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND perdatarif_m.is_active = TRUE 
                            AND tariftindakan_m.komponentarif_id <> 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                                  ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                   AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                   AND operasi_m.operasi_id = tarif_penjamin.operasi_id
                WHERE  tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = vpenjamin_id 
            ) x
            WHERE x.ruangan_id = xruangan_id
              AND x.kelaspelayanan_id = xkelaspelayanan_id
            GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                ELSEIF(xtipe = \'makanan\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                        SELECT
                            \'makanan\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            null::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying kategoritindakan_nama,
                            tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            tariftindakan_m.komponentarif_id as komponentarif_id,
                            komponentarif_m.komponentarif_nama as komponentarif_nama,
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            penjamin_m.carabayar_id as carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            makanandiet_m.jenisdiet_id::int4 AS kelompokpemeriksaanlab_id,
                            jenisdiet_m.jenisdiet_nama::character varying nama_kelompok ,
                            makanandiet_m.jenisdiet_id::int4 AS jenispemeriksaanlab_id,
                            jenisdiet_m.jenisdiet_nama::character varying AS jenispemeriksaanlab_nama ,
                            makanandiet_m.makanandiet_id::int4 AS pemeriksaanlab_id,
                            makanandiet_m.makanandiet_nama::character varying AS pemeriksaanlab_nama ,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
                        FROM tariftindakan_m
                        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN makanandiet_m ON tariftindakan_m.daftartindakan_id = makanandiet_m.daftartindakan_id AND makanandiet_m.is_deleted = FALSE
                        JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                        AND perdatarif_m.is_deleted = FALSE 
                                                                        AND perdatarif_m.is_active = TRUE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                    AND tindakanruangan_mp.is_deleted = FALSE
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                                        tariftindakan_m.tariftindakan_id,
                                                                        tariftindakan_m.kelaspelayanan_id,
                                                                        tariftindakan_m.penjamin_id,
                                                                        tariftindakan_m.harga_tariftindakan,
                                                                        tariftindakan_m.persencyto_tindakan,
                                                                        tariftindakan_m.persendiskon_tindakan,
                                                                        tariftindakan_m.perdatarif_id,
                                                                        penjamin_m.penjamin_nama,
                                                                        tariftindakan_m.komponentarif_id
                                    FROM tariftindakan_m
                                                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                    AND perdatarif_m.is_active = TRUE 
                                                                                                    AND perdatarif_m.is_deleted = FALSE
                                                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                            WHERE tariftindakan_m.is_deleted = FALSE 
                                      AND tariftindakan_m.is_active = TRUE 
                                      AND tariftindakan_m.tarifparent_id IS NULL
                                      AND tariftindakan_m.komponentarif_id <> 6
                                      AND tariftindakan_m.penjamin_id = xpenjamin_id
                                   ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                     WHERE tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL 
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    AND x.ruangan_id = xruangan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                
            END IF;
            END; 
            $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
            
        ');

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tariftotalrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'pelayanan\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric) AS $BODY$ 
            DECLARE vpenjamin_id int4;
            BEGIN
                IF(xruangan_id = 0)
                THEN
                    SELECT default_ruangan INTO xruangan_id
                    FROM konfigtarif_k
                    WHERE konfigtarif_id = 1;
                END IF;
                
                IF(xkelaspelayanan_id = 0)
                THEN
                    SELECT default_kelas INTO xkelaspelayanan_id
                    FROM konfigtarif_k
                    WHERE konfigtarif_id = 1;
                END IF;
                
                SELECT default_penjamin INTO vpenjamin_id
                FROM konfigtarif_k
                WHERE konfigtarif_id = 1;
                
                IF(xpenjamin_id = vpenjamin_id)
                THEN
                    xpenjamin_id := 0;
                END IF;
                
                IF(xtipe = \'pelayanan\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                        SELECT \'tindakan\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id,
                            r_tindakan.ruangan_nama,
                            r_tindakan.instalasi_id,
                            ins_tindakan.instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
                            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                            daftartindakan_m.kelompoktindakan_id,
                            kelompoktindakan_m.kelompoktindakan_nama,
                            daftartindakan_m.kategoritindakan_id,
                            kategoritindakan_m.kategoritindakan_nama,
                            COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            tariftindakan_m.komponentarif_id,
                            komponentarif_m.komponentarif_nama,
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default,
                            daftartindakan_m.is_akomodasi,
                            penjamin_m.carabayar_id,
                            daftartindakan_m.is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            NULL::int4 as kamarruangan_id,
                            NULL::int4 as ambulan_id,
                            NULL::VARCHAR as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                         FROM tariftindakan_m
                             JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                             JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                             JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
                             JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
                             LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id           
                             LEFT JOIN (
                                    SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id
                                    FROM tariftindakan_m
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE komponentarif_m.is_deleted IS FALSE
                                    AND perdatarif_m.is_active = true 
                                    AND tariftindakan_m.is_deleted = false 
                                    AND tariftindakan_m.is_active = true 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                            WHERE tindakanruangan_mp.is_deleted = false 
                            AND komponentarif_m.is_deleted IS FALSE
                            AND perdatarif_m.is_active = true 
                            AND tariftindakan_m.is_deleted = false 
                            AND tariftindakan_m.is_active = true 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id = 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id
                    UNION ALL
                     SELECT \'paket\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            paketruangan_mp.ruangan_id,
                            r_paket.ruangan_nama,
                            r_paket.instalasi_id,
                            ins_paket.instalasi_nama,
                            paketruangan_mp.ruangan_id AS ruanganpaket_id,
                            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
                            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                            NULL::integer AS kelompoktindakan_id,
                            NULL::character varying AS kelompoktindakan_nama,
                            NULL::integer AS kategoritindakan_id,
                            NULL::character varying AS kategoritindakan_nama,
                            NULL::integer AS daftartindakan_id,
                            NULL::character varying AS daftartindakan_nama,
                            tariftindakan_m.tipepaket_id,
                            tipepaket_m.tipepaket_nama,
                            tariftindakan_m.komponentarif_id,
                            komponentarif_m.komponentarif_nama,
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                            paketruangan_mp.is_default,
                            NULL::boolean AS is_akomodasi,
                            penjamin_m.carabayar_id,
                            false AS is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            NULL::int4 as kamarruangan_id,
                            NULL::int4 as ambulan_id,
                            NULL::VARCHAR as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                         FROM tariftindakan_m
                             JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                AND tipepaket_m.is_deleted= FALSE
                                                                                AND tipepaket_m.is_active = TRUE
                             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                    AND perdatarif_m.is_active = TRUE 
                             JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                                                                                        AND paketruangan_mp.is_deleted = FALSE
                             JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
                             JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
                             LEFT JOIN (
                                    SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id,
                                        tariftindakan_m.tipepaket_id
                                    FROM tariftindakan_m
                                                            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                            AND tipepaket_m.is_deleted= FALSE
                                                                                            AND tipepaket_m.is_active = TRUE
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                AND perdatarif_m.is_active = TRUE 
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE tariftindakan_m.is_deleted = FALSE
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                        WHERE tariftindakan_m.is_deleted = false 
                        AND tariftindakan_m.is_active = true 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                    UNION ALL
                     SELECT \'paket\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            paketruangan_mp.ruangan_id,
                            r_paket.ruangan_nama,
                            r_paket.instalasi_id,
                            ins_paket.instalasi_nama,
                            paketruangan_mp.ruangan_id AS ruanganpaket_id,
                            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
                            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                            NULL::integer AS kelompoktindakan_id,
                            NULL::character varying AS kelompoktindakan_nama,
                            NULL::integer AS kategoritindakan_id,
                            NULL::character varying AS kategoritindakan_nama,
                            NULL::integer AS daftartindakan_id,
                            NULL::character varying AS daftartindakan_nama,
                            tariftindakan_m.tipepaket_id,
                            tipepaket_m.tipepaket_nama,
                            tariftindakan_m.komponentarif_id,
                            komponentarif_m.komponentarif_nama,
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                            paketruangan_mp.is_default,
                            NULL::boolean AS is_akomodasi,
                            penjamin_m.carabayar_id,
                            false AS is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            NULL::int4 as kamarruangan_id,
                            NULL::int4 as ambulan_id,
                            NULL::VARCHAR as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                         FROM tariftindakan_m
                             JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                AND tipepaket_m.is_deleted= FALSE
                                                                                AND tipepaket_m.is_active = TRUE
                             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                             JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                             JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
                                                                                            AND paketruangan_mp.is_deleted = FALSE 
                             JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
                             LEFT JOIN (
                                    SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tipepaket_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id
                                    FROM tariftindakan_m
                                                            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                            AND tipepaket_m.is_deleted= FALSE
                                                                                            AND tipepaket_m.is_active = TRUE
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                AND perdatarif_m.is_deleted= FALSE
                                                                                                AND perdatarif_m.is_active = TRUE
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE tariftindakan_m.is_deleted = false 
                                    AND tariftindakan_m.is_active = true 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                        WHERE tariftindakan_m.is_deleted = false 
                        AND tariftindakan_m.is_active = true 
                        AND tariftindakan_m.tarifparent_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.ruangan_id = xruangan_id
                    AND x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                ELSEIF(xtipe = \'kamar\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                            SELECT      
                                \'kamar\'::text AS jenis,
                                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                                kamarruangan_m.ruangan_id as ruangan_id,
                                ruangan_m.ruangan_nama as ruangan_nama,
                                ruangan_m.instalasi_id as instalasi_id,
                                instalasi_m.instalasi_nama as instalasi_nama,
                                NULL::integer AS ruanganpaket_id,
                                NULL::character varying AS ruanganpaket_nama,
                                COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
                                 perdatarif_m.perdanama_sk as perdanama_sk,
                                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id,
                                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                                daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                                kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
                                daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                                kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
                                COALESCE(tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id,
                                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                                NULL::integer AS tipepaket_id,
                                NULL::character varying AS tipepaket_nama,
                                tariftindakan_m.komponentarif_id as komponentarif_id,
                                komponentarif_m.komponentarif_nama as komponentarif_nama,
                                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan,
                                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan,
                                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan,
                                NULL::BOOLEAN AS is_default,
                                daftartindakan_m.is_akomodasi as is_akomodasi,
                                penjamin_m.carabayar_id as carabayar_id,
                                daftartindakan_m.is_konsultasi as is_konsultasi,
                                kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
                                tariftindakan_m.kamarruangan_id as kamarruangan_id,
                                NULL::int4 as ambulan_id,
                                NULL::VARCHAR as no_polisi,
                                NULL::integer as kelompokpemeriksaanlab_id,
                                NULL::character varying AS nama_kelompok,
                                NULL::integer AS jenispemeriksaanlab_id,
                                NULL::character varying AS jenispemeriksaanlab_nama,
                                NULL::integer AS pemeriksaanlab_id,
                                NULL::character varying AS pemeriksaanlab_nama,
                                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                         FROM tariftindakan_m
                             JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                             JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                             LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                             JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                             LEFT JOIN (
                                    SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id,
                                        tariftindakan_m.kamarruangan_id
                                    FROM tariftindakan_m
                                 JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                                 JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                 JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                                 LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                 JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                                    WHERE 
                                    tariftindakan_m.is_deleted = false AND
                                    tariftindakan_m.is_active = true AND
                                    tariftindakan_m.tarifparent_id IS NULL AND
                                    kamarruangan_m.is_deleted = FALSE AND
                                    kamarruangan_m.is_active = TRUE AND
                                    perdatarif_m.is_active = true AND
                                    tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
                        WHERE 
                                tariftindakan_m.is_deleted = false AND
                                tariftindakan_m.is_active = true AND
                                tariftindakan_m.tarifparent_id IS NULL AND
                                kamarruangan_m.is_deleted = FALSE AND
                                kamarruangan_m.is_active = TRUE AND
                                perdatarif_m.is_active = true AND
                                tariftindakan_m.komponentarif_id = 6
                                AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.ruangan_id = xruangan_id
                    AND x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                ELSEIF(xtipe = \'ambulan\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                        SELECT 
                            \'ambulan\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id,
                            NULL::integer as ruangan_id,
                            NULL::character varying as ruangan_nama,
                            NULL::integer as instalasi_id,
                            NULL::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            COALESCE(tariftindakan_m.kelaspelayanan_id, 3)::integer as kelaspelayanan_id,
                             COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'Kelas 3\')::character varying AS kelaspelayanan_nama,
                             COALESCE(tariftindakan_m.penjamin_id, 1)::integer AS penjamin_id ,
                            COALESCE(penjamin_m.penjamin_nama, \'Perseorangan\')::character varying AS penjamin_nama,
                            daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                            null::VARCHAR as kelompoktindakan_nama,
                            daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                            null::VARCHAR as kategoritindakan_nama,
                            ambulandetail_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                             COALESCE(tariftindakan_m.komponentarif_id, 6) as komponentarif_id,
                            COALESCE(komponentarif_m.komponentarif_nama, \'Total Tarif\')::character varying as komponentarif_nama,
                            COALESCE(tarif_penjamin.harga_tariftindakan, COALESCE((tariftindakan_m.harga_tariftindakan), 0)) AS harga_tariftindakan,
                            COALESCE(tarif_penjamin.persencyto_tindakan, COALESCE((tariftindakan_m.persencyto_tindakan), 0)) AS persencyto_tindakan,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, COALESCE((tariftindakan_m.persendiskon_tindakan), 0)) AS persencyto_tindakan,
                            ambulandetail_m.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            penjamin_m.carabayar_id as carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            tariftindakan_m.kamarruangan_id as kamarruangan_id,
                            ambulan_m.ambulan_id as ambulan_id,
                            ambulan_m.no_polisi as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
                         FROM ambulan_m
                             JOIN ambulandetail_m ON ambulan_m.ambulan_id = ambulandetail_m.ambulan_id
                             JOIN daftartindakan_m ON ambulandetail_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             LEFT JOIN tariftindakan_m ON ambulandetail_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
                             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                             LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                             LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                             LEFT JOIN (
                                    SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id
                                    FROM tariftindakan_m
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE komponentarif_m.is_deleted IS FALSE
                                    AND perdatarif_m.is_active = true 
                                    AND tariftindakan_m.is_deleted = false 
                                    AND tariftindakan_m.is_active = true 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                        WHERE 
                             ambulan_m.is_deleted = false AND
                             ambulan_m.is_active = true AND
                             ambulandetail_m.is_deleted = false AND
                             ambulandetail_m.is_active = true AND
                             tariftindakan_m.is_deleted=false AND
                             tariftindakan_m.is_active=true AND
                             perdatarif_m.is_active = true AND 
                             perdatarif_m.is_deleted = false AND
                             tariftindakan_m.komponentarif_id = 6 AND
                             tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                ELSEIF(xtipe = \'penunjang\')
                THEN
                    RETURN QUERY
                    SELECT *FROM (
                         SELECT
                            \'lab\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            null::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying kategoritindakan_nama,
                            tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            tariftindakan_m.komponentarif_id as komponentarif_id,
                            komponentarif_m.komponentarif_nama as komponentarif_nama,
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            penjamin_m.carabayar_id as carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            pemeriksaanlab_m.kelompokpemeriksaanlab_id::int4,
                            kelompokpemeriksaanlab_m.nama_kelompok::character varying ,
                            pemeriksaanlab_m.jenispemeriksaanlab_id::int4,
                            jenispemeriksaanlab_m.jenispemeriksaanlab_nama::character varying ,
                            pemeriksaanlab_m.pemeriksaanlab_id::int4,
                            pemeriksaanlab_m.pemeriksaanlab_nama::character varying ,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
                      FROM tariftindakan_m
                        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                                                                AND pemeriksaanlab_m.is_deleted = FALSE
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                        JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                        AND perdatarif_m.is_deleted = FALSE 
                                                                        AND perdatarif_m.is_active = TRUE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                    AND tindakanruangan_mp.is_deleted = FALSE
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                                        tariftindakan_m.tariftindakan_id,
                                                                        tariftindakan_m.kelaspelayanan_id,
                                                                        tariftindakan_m.penjamin_id,
                                                                        tariftindakan_m.harga_tariftindakan,
                                                                        tariftindakan_m.persencyto_tindakan,
                                                                        tariftindakan_m.persendiskon_tindakan,
                                                                        tariftindakan_m.perdatarif_id,
                                                                        penjamin_m.penjamin_nama,
                                                                        tariftindakan_m.komponentarif_id
                                    FROM tariftindakan_m
                                                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                    AND perdatarif_m.is_active = TRUE 
                                                                                                    AND perdatarif_m.is_deleted = FALSE
                                                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                            WHERE tariftindakan_m.is_deleted = FALSE 
                                      AND tariftindakan_m.is_active = TRUE 
                                      AND tariftindakan_m.tarifparent_id IS NULL
                                      AND tariftindakan_m.komponentarif_id = 6
                                      AND tariftindakan_m.penjamin_id = xpenjamin_id
                                   ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                     WHERE tariftindakan_m.is_deleted = FALSE 
                                 AND tariftindakan_m.is_active = TRUE 
                                 AND tariftindakan_m.tarifparent_id IS NULL 
                                 AND tariftindakan_m.komponentarif_id = 6
                       AND tariftindakan_m.penjamin_id = vpenjamin_id
            UNION ALL
                SELECT  \'rad\'::text AS jenis,
                                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                                tindakanruangan_mp.ruangan_id as ruangan_id,
                                ruangan_m.ruangan_nama as ruangan_nama,
                                ruangan_m.instalasi_id as instalasi_id,
                                NULL::character varying as instalasi_nama,
                                NULL::integer AS ruanganpaket_id,
                                NULL::character varying AS ruanganpaket_nama,
                                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                                perdatarif_m.perdanama_sk as perdanama_sk,
                                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                                NULL::integer as kelompoktindakan_id,
                                NULL::character varying as kelompoktindakan_nama,
                                NULL::integer as kategoritindakan_id,
                                NULL::character varying as kategoritindakan_nama,
                                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                                NULL::integer AS tipepaket_id,
                                NULL::character varying AS tipepaket_nama,
                                tariftindakan_m.komponentarif_id as komponentarif_id,
                                komponentarif_m.komponentarif_nama as komponentarif_nama,
                                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                                tindakanruangan_mp.is_default AS is_default,
                                daftartindakan_m.is_akomodasi as is_akomodasi,
                                penjamin_m.carabayar_id as carabayar_id,
                                daftartindakan_m.is_konsultasi as is_konsultasi,
                                NULL::character varying as  kamarruangan_nokamar,
                                NULL::integer as kamarruangan_id,
                                NULL::integer as ambulan_id,
                                NULL::character varying as no_polisi,
                                pemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
                                kelompokpemeriksaanrad_m.nama_kelompok as nama_kelompok,
                                pemeriksaanrad_m.jenispemeriksaanrad_id as jenispemeriksaanlab_id,
                                jenispemeriksaanrad_m.jenispemeriksaanrad_nama as jenispemeriksaanlab_nama,
                                pemeriksaanrad_m.pemeriksaanradiologi_id as pemeriksaanlab_id,
                                pemeriksaanrad_m.pemeriksaanrad_nama  as pemeriksaanlab_nama,
                                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
                            FROM tariftindakan_m
                                JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                            AND pemeriksaanrad_m.is_deleted = FALSE
                                JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                                JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                    AND perdatarif_m.is_deleted = FALSE 
                                                                    AND perdatarif_m.is_active = TRUE
                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                                JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                AND tindakanruangan_mp.is_deleted = FALSE
                                JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                                LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                                    tariftindakan_m.tariftindakan_id,
                                                                    tariftindakan_m.kelaspelayanan_id,
                                                                    tariftindakan_m.penjamin_id,
                                                                    tariftindakan_m.harga_tariftindakan,
                                                                    tariftindakan_m.persencyto_tindakan,
                                                                    tariftindakan_m.persendiskon_tindakan,
                                                                    tariftindakan_m.perdatarif_id,
                                                                    penjamin_m.penjamin_nama,
                                                                    tariftindakan_m.komponentarif_id
                                                            FROM tariftindakan_m
                                                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                    AND perdatarif_m.is_active = TRUE
                                                                                                    AND perdatarif_m.is_deleted = FALSE
                                                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                            WHERE tariftindakan_m.is_deleted = FALSE 
                                                                AND tariftindakan_m.is_active = TRUE 
                                                                AND tariftindakan_m.tarifparent_id IS NULL
                                                                AND tariftindakan_m.komponentarif_id = 6
                                                                AND tariftindakan_m.penjamin_id = xpenjamin_id
                                                        ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                        AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                        WHERE tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND tariftindakan_m.tarifparent_id IS NULL 
                            AND tariftindakan_m.komponentarif_id = 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id
            UNION ALL
               SELECT \'operasi\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            NULL::character varying instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                            null::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            null::integer as kategoritindakan_id,
                            NULL::character varying as kategoritindakan_nama,
                            tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            tariftindakan_m.komponentarif_id as komponentarif_id,
                            komponentarif_m.komponentarif_nama as komponentarif_nama,
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            penjamin_m.carabayar_id as carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            null::integer as kamarruangan_id,
                            null::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
                            golonganoperasi_m.golonganoperasi_nama as nama_kelompok,
                            operasi_m.kegiatanoperasi_id as jenispemeriksaanlab_id,
                            kegiatanoperasi_m.kegiatanoperasi_nama as jenispemeriksaanlab_nama,
                            operasi_m.operasi_id as pemeriksaanlab_id,
                            operasi_m.operasi_nama  as pemeriksaanlab_nama,
                             COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
                             FROM ((((((((((tariftindakan_m
                                 JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                                 JOIN operasi_m ON (((tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id) AND (operasi_m.is_deleted = false))))
                                 JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
                                 JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
                                 JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                                 JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
                                 JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
                                 JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
                                 JOIN tindakanruangan_mp ON (((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id) AND (tindakanruangan_mp.is_deleted = false))))
                                 JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
                                 LEFT JOIN (
                                                SELECT tariftindakan_m.daftartindakan_id,
                                                    tariftindakan_m.tariftindakan_id,
                                                    tariftindakan_m.kelaspelayanan_id,
                                                    tariftindakan_m.penjamin_id,
                                                    tariftindakan_m.harga_tariftindakan,
                                                    tariftindakan_m.persencyto_tindakan,
                                                    tariftindakan_m.persendiskon_tindakan,
                                                    tariftindakan_m.perdatarif_id,
                                                    penjamin_m.penjamin_nama,
                                                    tariftindakan_m.komponentarif_id
                                                FROM tariftindakan_m
                                                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                WHERE komponentarif_m.is_deleted IS FALSE
                                                AND perdatarif_m.is_active = true 
                                                AND tariftindakan_m.is_deleted = false 
                                                AND tariftindakan_m.is_active = true 
                                                AND tariftindakan_m.tarifparent_id IS NULL
                                                AND tariftindakan_m.komponentarif_id = 6
                                                AND tariftindakan_m.penjamin_id = xpenjamin_id
                                         ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                            WHERE  tariftindakan_m.is_deleted = false AND
                                     tariftindakan_m.is_active = true AND
                                     tariftindakan_m.tarifparent_id IS NULL AND
                                     perdatarif_m.is_active = true AND
                                     tariftindakan_m.komponentarif_id = 6
                                     AND tariftindakan_m.penjamin_id = vpenjamin_id
            UNION ALL
            SELECT 
                    CASE
                            WHEN (ruangan_m.instalasi_id = 4) THEN \'paket_lab\'
                            WHEN (ruangan_m.instalasi_id = 5) THEN \'paket_rad\'
                            WHEN (ruangan_m.instalasi_id = 21) THEN \'paket_mcu\'
                            ELSE \'paket_operasi\'
                    END AS jenis,
                    COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                    paketruangan_mp.ruangan_id as ruangan_id,
                    ruangan_m.ruangan_nama as ruangan_nama,
                    ruangan_m.instalasi_id as instalasi_id,
                    null::character varying AS instalasi_nama,
                    NULL::integer AS ruanganpaket_id,
                    NULL::character varying AS ruanganpaket_nama,
                    tariftindakan_m.perdatarif_id AS perdatarif_id ,
                    perdatarif_m.perdanama_sk as perdanama_sk,
                    tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                    COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                    COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                    null::integer as kelompoktindakan_id,
                    null::character varying  as kelompoktindakan_nama,
                    null::integer as kategoritindakan_id,
                    null::character varying  as kategoritindakan_nama,
                    tariftindakan_m.tipepaket_id  AS daftartindakan_id ,
                    tipepaket_m.tipepaket_nama as daftartindakan_nama,
                    tariftindakan_m.tipepaket_id  AS tipepaket_id,
                    tipepaket_m.tipepaket_nama AS tipepaket_nama,
                    tariftindakan_m.komponentarif_id as komponentarif_id,
                    komponentarif_m.komponentarif_nama as komponentarif_nama,
                    COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                    COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                    COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                    paketruangan_mp.is_default AS is_default,
                    null::boolean as is_akomodasi,
                    penjamin_m.carabayar_id as carabayar_id,
                    null::boolean as is_konsultasi,
                    null::character varying  as  kamarruangan_nokamar,
                    null::integer as kamarruangan_id,
                    null::integer as ambulan_id,
                    null::character varying  as no_polisi,
                    null as kelompokpemeriksaanlab_id,
                    null AS nama_kelompok,
                    null AS jenispemeriksaanlab_id,
                    null AS jenispemeriksaanlab_nama,
                    null AS pemeriksaanlab_id,
                    null AS pemeriksaanlab_nama,
                    COALESCE(tariftindakan_m.persen_penyulit, 0::numeric) AS persen_penyulit  
            FROM tariftindakan_m
                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id                 
                JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                                             AND paketruangan_mp.is_deleted = FALSE
                JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                        AND perdatarif_m.is_deleted = FALSE 
                                                        AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                                                        tariftindakan_m.tariftindakan_id,
                                                        tariftindakan_m.kelaspelayanan_id,
                                                        tariftindakan_m.penjamin_id,
                                                        tariftindakan_m.harga_tariftindakan,
                                                        tariftindakan_m.persencyto_tindakan,
                                                        tariftindakan_m.persendiskon_tindakan,
                                                        tariftindakan_m.perdatarif_id,
                                                        tariftindakan_m.komponentarif_id,
                                                        penjamin_m.penjamin_nama
                                        FROM tariftindakan_m
                                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                                AND perdatarif_m.is_deleted = FALSE 
                                                                                AND perdatarif_m.is_active = TRUE
                          WHERE tariftindakan_m.is_deleted = FALSE 
                                                AND tariftindakan_m.is_active = TRUE 
                                                AND tariftindakan_m.komponentarif_id = 6
                              AND tariftindakan_m.penjamin_id = xpenjamin_id
                                        ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                         AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                    WHERE tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND perdatarif_m.is_active = TRUE 
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id          
            ) x
              WHERE x.ruangan_id = xruangan_id
                AND x.kelaspelayanan_id = xkelaspelayanan_id
              GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                
                ELSEIF(xtipe = \'makanan\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                        SELECT
                            \'makanan\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            null::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying kategoritindakan_nama,
                            tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            tariftindakan_m.komponentarif_id as komponentarif_id,
                            komponentarif_m.komponentarif_nama as komponentarif_nama,
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            penjamin_m.carabayar_id as carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            makanandiet_m.jenisdiet_id::int4 AS kelompokpemeriksaanlab_id,
                            jenisdiet_m.jenisdiet_nama::character varying nama_kelompok ,
                            makanandiet_m.jenisdiet_id::int4 AS jenispemeriksaanlab_id,
                            jenisdiet_m.jenisdiet_nama::character varying AS jenispemeriksaanlab_nama ,
                            makanandiet_m.makanandiet_id::int4 AS pemeriksaanlab_id,
                            makanandiet_m.makanandiet_nama::character varying AS pemeriksaanlab_nama ,
                            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
                        FROM tariftindakan_m
                        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN makanandiet_m ON tariftindakan_m.daftartindakan_id = makanandiet_m.daftartindakan_id AND makanandiet_m.is_deleted = FALSE
                        JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                        AND perdatarif_m.is_deleted = FALSE 
                                                                        AND perdatarif_m.is_active = TRUE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                    AND tindakanruangan_mp.is_deleted = FALSE
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                                        tariftindakan_m.tariftindakan_id,
                                                                        tariftindakan_m.kelaspelayanan_id,
                                                                        tariftindakan_m.penjamin_id,
                                                                        tariftindakan_m.harga_tariftindakan,
                                                                        tariftindakan_m.persencyto_tindakan,
                                                                        tariftindakan_m.persendiskon_tindakan,
                                                                        tariftindakan_m.perdatarif_id,
                                                                        penjamin_m.penjamin_nama,
                                                                        tariftindakan_m.komponentarif_id
                                    FROM tariftindakan_m
                                                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                    AND perdatarif_m.is_active = TRUE 
                                                                                                    AND perdatarif_m.is_deleted = FALSE
                                                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                            WHERE tariftindakan_m.is_deleted = FALSE 
                                      AND tariftindakan_m.is_active = TRUE 
                                      AND tariftindakan_m.tarifparent_id IS NULL
                                      AND tariftindakan_m.komponentarif_id = 6
                                      AND tariftindakan_m.penjamin_id = xpenjamin_id
                                   ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                     WHERE tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL 
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                            AND x.ruangan_id = xruangan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                END IF;
            END; 
            $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
            
        ');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201102_074023_migrate_mhkn_20201102_pasientitipanlive_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201102_074023_migrate_mhkn_20201102_pasientitipanlive_function cannot be reverted.\n";

        return false;
    }
    */
}
