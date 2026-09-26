<?php

use yii\db\Migration;

/**
 * Class m200122_025700_trigger_pendaftaran_1793
 */
class m200122_025700_trigger_pendaftaran_1793 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP TRIGGER if exists pendaftaran_t ON public.pendaftaran_t;');

         $this->execute('DROP FUNCTION if exists public.pendaftaran_t();');

         $this->execute("
            CREATE OR REPLACE FUNCTION public.pendaftaran_t()
  RETURNS trigger AS
\$BODY\$

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
        IF (countPenunjang > 0) THEN
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
                        477,
                        NEW.ruangan_id,
                        NEW.instalasi_id,
                        NEW.created_by
                ) RETURNING pasienmasukpenunjang_id INTO idPenunjang;
                INSERT INTO tindakanpelayanan_t (
                                pasienmasukpenunjang_id,
                                kelaspelayanan_id,
                                pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                tipepaket_id,
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
                                additional_data,
                                created_by
             ) SELECT 
                                idPenunjang as pasienmasukpenunjang_id,
                                kelaspelayanan_id,
                                NEW.pasien_id as pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                tipepaket_id,
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
                                additional_data,
                                NEW.created_by as created_by
                FROM json_populate_recordset(null::tindakanpelayanan_t,vPenunjang::json);
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
                            asuransipasien_id
                    
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
                            (vadmisi::json->>'status_verifikasi')::INTEGER,
                            (vadmisi::json->>'is_skd')::BOOL,
                            (vadmisi::json->>'is_pasientitipan')::BOOL,
                            NEW.created_by,
                            (vadmisi::json->>'is_aps')::BOOL,
                            idAsuransi
                            
                    
                            
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
         IF (countTagihan > 0) THEN
                INSERT INTO tindakanpelayanan_t (
                                kelaspelayanan_id,
                                pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                                                tipepaket_id,
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
                                instalasi_id,
                                daftartindakan_id,
                                                                tipepaket_id,
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

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('ALTER FUNCTION public.pendaftaran_t()
  OWNER TO postgres;');

         $this->execute('CREATE TRIGGER pendaftaran_t
                  BEFORE INSERT
                  ON public.pendaftaran_t
                  FOR EACH ROW
                  EXECUTE PROCEDURE public.pendaftaran_t();
                ');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200122_025700_trigger_pendaftaran_1793 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_025700_trigger_pendaftaran_1793 cannot be reverted.\n";

        return false;
    }
    */
}
