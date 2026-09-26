<?php

use yii\db\Migration;

/**
 * Class m200729_063124_migrate_mhkn_20200729
 */
class m200729_063124_migrate_mhkn_20200729 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN "kamartempattidur_id" int4;');

        $this->execute('ALTER TABLE "public"."penjualanresep_t" ADD COLUMN "log_user" json;');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pasien_m\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
     vId integer; 
  vPrefix varchar;
  vLast VARCHAR;
  vNumber VARCHAR;
  vIsAps BOOLEAN;
  vdigit_rm INT;
    vprefix_rm VARCHAR;
    vis_rekammedik_aps BOOLEAN;
BEGIN
    vIsAps := NEW.is_aps;               
    
    SELECT digit_rekammedik , is_rekammedik_aps
    INTO vdigit_rm, vis_rekammedik_aps
    FROM konfigsystem_k
    WHERE konfigsystem_id = 1;
    
    
    
    SELECT COALESCE(prefix,'') INTO vprefix_rm
    FROM penomoran_k
    WHERE penomoran_id = 57;
    
    IF(vis_rekammedik_aps IS TRUE )
    THEN
        IF(vIsAps) THEN
                vId := 128;
                SELECT 
                     prefix,
                        ( SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no 
                                FROM penomoran_k where penomoran_id = vId
                        )
                    INTO 
                         vPrefix,
                         vNumber
                    FROM penomoran_k where penomoran_id = vId;

                vLast := vPrefix || vNumber;
                UPDATE penomoran_k SET
                    last_number = vNumber,
                    last_generate = vLast
                WHERE penomoran_id = vId;
                
                NEW.no_rekam_medik := vLast;

                RETURN NEW;
        ELSE
                    vId := 26;
                    SELECT 
                                CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 8) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no
                    INTO 
                                vPrefix
                    FROM penomoran_k where penomoran_id = vId;
                    
                    vPrefix := CONCAT(vprefix_rm,vPrefix);
                    
                    UPDATE penomoran_k SET
                                last_number = vPrefix,
                                last_generate = vPrefix
                    WHERE penomoran_id = vId;
                
                    NEW.no_rekam_medik := vPrefix;

                    RETURN NEW;
        END IF;
    ELSE
        vId := 26;
        SELECT 
                    CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 8) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no
        INTO 
                    vPrefix
        FROM penomoran_k where penomoran_id = vId;
        
        vPrefix := CONCAT(vprefix_rm,vPrefix);
        
        UPDATE penomoran_k SET
                    last_number = vPrefix,
                    last_generate = vPrefix
        WHERE penomoran_id = vId;
    
        NEW.no_rekam_medik := vPrefix;

        RETURN NEW;
    END IF;
    
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_inap\"()
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
         IF (countTagihan > 0) THEN
                INSERT INTO tindakanpelayanan_t (
                                kelaspelayanan_id,
                                pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                carabayar_id,
                                pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                pasienadmisi_id,
                                additional_data,
                                created_by
             ) SELECT 
                                kelaspelayanan_id,
                                NEW.pasien_id as pasien_id,
                                instalasiId,
                                daftartindakan_id,
                                carabayar_id,
                                NEW.pendaftaran_id as pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                NEW.pasienadmisi_id,
                                additional_data,
                                NEW.created_by as created_by
                FROM json_populate_recordset(null::tindakanpelayanan_t,dataKarcis::json);
         END IF;


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
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_t\"()
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
         vPenunjang := paramJson::json->>'tarif_penunjang';
         vadmisi := paramJson::json->>'pasien_admisi';
       vmasukkamar := paramJson::json->>'masuk_kamar';
         countTagihan := json_array_length(dataKarcis::json);
         countPenunjang := json_array_length(vPenunjang::json);
         vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
         vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
         
         IF (dataPasien::json->>'nama_pasien' IS NOT NULL AND NEW.pasien_id IS NULL) THEN
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
                            created_by
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
                            NEW.created_by
                        ) RETURNING pasien_id INTO pasienId;
                        NEW.pasien_id := pasienId;
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
                                                                    created_by
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
                                                                    NEW.created_by as created_by
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
  COST 100;");

        $this->execute('DROP VIEW if exists "public"."invoiceridetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoiceridetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    layanan.layanan_jenis,
    layanan.tgl_pelayanan,
    layanan.tindakan_obat,
    layanan.kelompok,
    layanan.qty,
    layanan.harga_satuan,
    layanan.tarif,
    layanan.uom,
    layanan.ruangan,
    layanan.dokter,
    layanan.is_akomodasi,
    layanan.is_konsultasi,
    layanan.additional_data,
    layanan.kamarruangan_nokamar AS kamar,
    layanan.no_tempattidur AS no_bed,
    layanan.kelaspelayanan_nama AS kelas
   FROM ((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN ( SELECT 'tindakan'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
            kelompoktindakan_m.kelompoktindakan_nama AS kelompok,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_satuan AS harga_satuan,
            tindakanpelayanan_t.tarif_tindakan AS tarif,
            NULL::character varying AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            daftartindakan_m.is_akomodasi,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.additional_data,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM ((((((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
             LEFT JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pegawai_m dok_dpjp ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id)))
             LEFT JOIN masukkamar_t ON (((tindakanpelayanan_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id) AND (tindakanpelayanan_t.ruangan_id = masukkamar_t.ruangan_id))))
             LEFT JOIN kamarruangan_m ON ((masukkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
             LEFT JOIN kamartempattidur_m ON ((masukkamar_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
             LEFT JOIN kelaspelayanan_m ON ((masukkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT 'obat'::text AS layanan_jenis,
            obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
            jenisobatalkes_m.jenisobatalkes_nama AS kelompok,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.hargasatuan_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            satuanunit_m.satuanunit_nama AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            false AS is_akomodasi,
            false AS is_konsultasi,
            obatalkespasien_t.additional_data,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            NULL::character varying AS kelaspelayanan_nama
           FROM (((((obatalkespasien_t
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
             LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
             LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pegawai_m dok_dpjp ON ((obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id)))
          WHERE (obatalkespasien_t.is_deleted = false)) layanan ON ((pendaftaran_t.pendaftaran_id = layanan.pendaftaran_id)));");

        $this->execute('DROP VIEW if  exists "public"."invoicesudahbayardetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoicesudahbayardetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.pelayanan_id,
    tagihan.pasien_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN (pasien_m.nama_pasien IS NULL) THEN (tagihan.nama_pembeli)::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    pasien_m.tanggal_lahir,
    tagihan.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    (tagihan.tarif_satuan)::integer AS tarif_satuan,
    tagihan.qty,
    (tagihan.sub_total)::integer AS sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.tgl_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_tinpelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
    tagihan.penjamin_tinpelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.jeniskasuspenyakit_id,
    tagihan.pembayaranpelayanan_id,
    tagihan.biaya_administrasi,
    tagihan.e_collection,
    tagihan.nama_pemrekening,
    tagihan.no_rekening,
    tagihan.carabayar_pelayanan_id,
    tagihan.carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    tagihan.penjamin_pelayanan,
    (tagihan.tarif_cyto)::integer AS tarif_cyto,
    tagihan.tandabuktibayar_id,
    tagihan.jeniskasuspenyakit_nama,
    tagihan.penjualanresep_id,
    tagihan.is_konsultasi,
    dok_tindakan.nama_pegawai AS dokter_tindakan,
    tagihan.pembayaran_id,
    tagihan.satuan_kecil AS uom
   FROM (((((((( SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil
           FROM (((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN tindakanpelayanan_t ON (((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
             JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil
           FROM ((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM (((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
          WHERE (obatalkespasien_t.is_deleted = false)
        UNION ALL
         SELECT penjualanresep_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            penjualanresep_t.noresep AS no_pendaftaran,
            NULL::character varying AS umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            NULL::integer AS jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            NULL::character varying AS jeniskasuspenyakit_nama,
            obatalkespasien_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM (((((((obatalkespasien_t
             JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
          WHERE ((penjualanresep_t.jenispenjualan)::integer <> 344)) tagihan
     LEFT JOIN ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN carabayar_m ON ((tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m dok_tindakan ON ((tagihan.doktertindakan_id = dok_tindakan.pegawai_id)));");

        $this->execute("
            CREATE VIEW \"public\".\"invoiceobat_v\" AS  SELECT pembayaranpelayanan_t.pembayaran_id,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nama_pegawai
            ELSE penjualanresep_t.nama_pembeli
        END AS nama_pasien,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.no_rekam_medik
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nomorindukpegawai
            ELSE NULL::character varying
        END AS no_rekam_medik,
    pendaftaran_t.umur,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.tanggal_lahir
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.tgl_lahirpegawai
            ELSE NULL::date
        END AS tgl_lahir,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN fgetnamalookup((pasien_m.jeniskelamin)::integer)
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN fgetnamalookup((resep_karyawan.jeniskelamin)::integer)
            ELSE NULL::character varying
        END AS jenis_kelamin,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_1.ruangan_nama
            ELSE ruangan_2.ruangan_nama
        END AS ruangan_nama,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    dok_admisi.nama_pegawai AS dok_admisi,
    dok_resep.nama_pegawai AS dok_resep,
    pembayaranpelayanan_t.total_biayaoa AS total_tagihan_obat,
    pembayaran_diskon.komponen,
    pembayaran_diskon.total_diskon
   FROM ((((((((((((((pembayaranpelayanan_t
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN obatsudahbayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = obatsudahbayar_t.pembayaranpelayanan_id)))
     JOIN ( SELECT obatalkespasien_t_1.obatsudahbayar_id,
            obatalkespasien_t_1.penjualanresep_id,
            obatalkespasien_t_1.obatalkespasien_id
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE (obatalkespasien_t_1.is_deleted = false)
          GROUP BY obatalkespasien_t_1.obatsudahbayar_id, obatalkespasien_t_1.penjualanresep_id, obatalkespasien_t_1.obatalkespasien_id) obatalkespasien_t ON (((obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id) AND (obatsudahbayar_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id))))
     JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m resep_karyawan ON ((penjualanresep_t.karyawan_id = resep_karyawan.pegawai_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     LEFT JOIN pegawai_m dok_resep ON ((penjualanresep_t.pegawai_id = dok_resep.pegawai_id)))
     LEFT JOIN ruangan_m ruangan_1 ON ((pendaftaran_t.ruangan_id = ruangan_1.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_2 ON ((pasienadmisi_t.ruangan_id = ruangan_2.ruangan_id)))
     LEFT JOIN ( SELECT pembayarandiskon_t.pembayaran_id,
            komponentarif_m.komponentarif_nama AS komponen,
            sum(pembayarandiskon_t.total_diskon) AS total_diskon
           FROM (pembayarandiskon_t
             LEFT JOIN komponentarif_m ON ((pembayarandiskon_t.komponentarif_id = komponentarif_m.komponentarif_id)))
          GROUP BY pembayarandiskon_t.pembayaran_id, komponentarif_m.komponentarif_nama) pembayaran_diskon ON ((pembayaran_t.pembayaran_id = pembayaran_diskon.pembayaran_id)))
  GROUP BY pembayaranpelayanan_t.pembayaran_id, pembayaranpelayanan_t.no_pembayaran, pembayaranpelayanan_t.tgl_pembayaran, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nama_pegawai
            ELSE penjualanresep_t.nama_pembeli
        END,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.no_rekam_medik
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nomorindukpegawai
            ELSE NULL::character varying
        END, pendaftaran_t.umur,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.tanggal_lahir
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.tgl_lahirpegawai
            ELSE NULL::date
        END,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN fgetnamalookup((pasien_m.jeniskelamin)::integer)
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN fgetnamalookup((resep_karyawan.jeniskelamin)::integer)
            ELSE NULL::character varying
        END,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_1.ruangan_nama
            ELSE ruangan_2.ruangan_nama
        END, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, dok_resep.nama_pegawai, pembayaranpelayanan_t.total_biayaoa, pembayaran_diskon.komponen, pembayaran_diskon.total_diskon;");

        $this->execute("
            CREATE VIEW \"public\".\"invoiceobatdetail_v\" AS  SELECT pembayaranpelayanan_t.pembayaran_id,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    penjualanresep_t.noresep AS no_resep,
    detail_obat.obatalkes_nama,
    detail_obat.qty,
    detail_obat.uom,
    detail_obat.tarif
   FROM ((((pembayaranpelayanan_t
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN obatsudahbayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = obatsudahbayar_t.pembayaranpelayanan_id)))
     JOIN ( SELECT obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.penjualanresep_id,
            obatalkespasien_t.obatalkespasien_id,
            obatalkes_m.obatalkes_nama,
                CASE
                    WHEN (obatalkespasien_t.det = (0)::double precision) THEN obatalkespasien_t.det
                    WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.hargajual_oa AS tarif,
            sat_kecil.satuanunit_nama AS uom
           FROM ((obatalkespasien_t
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             LEFT JOIN satuanunit_m sat_kecil ON ((obatalkespasien_t.satuankecil_id = sat_kecil.satuanunit_id)))
          WHERE (obatalkespasien_t.is_deleted = false)) detail_obat ON (((obatsudahbayar_t.obatsudahbayar_id = detail_obat.obatsudahbayar_id) AND (obatsudahbayar_t.obatalkespasien_id = detail_obat.obatalkespasien_id))))
     JOIN penjualanresep_t ON ((detail_obat.penjualanresep_id = penjualanresep_t.penjualanresep_id)));");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_purchasereq\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 67; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    
BEGIN
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vDate,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vDate || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;
    
    NEW.no_pr = v_Nomor;

    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "no_purchasereq" BEFORE INSERT ON "public"."purchasereq_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."generate_purchasereq"();');

      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_063124_migrate_mhkn_20200729 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_063124_migrate_mhkn_20200729 cannot be reverted.\n";

        return false;
    }
    */
}
