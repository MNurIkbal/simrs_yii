<?php

use yii\db\Migration;

/**
 * Class m210105_084715_migrate_sy_20210105_trigger_pendaftaran_inap
 */
class m210105_084715_migrate_sy_20210105_trigger_pendaftaran_inap extends Migration
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
        -- Kebutuhan SY Penanggung Biaya
        dataPenanggungBiaya VARCHAR;
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
        dataPenanggungBiaya := paramJson::json->>'penanggungbiaya';
    
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
                        carabayar_id,
                                                masaberlakukartu,
                                                nama_asuransi
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
                                                dataAsuransi::json->>'nama_asuransi'
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
                                                        ruangan_titipan_id,
                                                        limit_tagihan
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
                                                         (vadmisi::json->>'ruangan_titipan_id')::INTEGER,
                                                         (vadmisi::json->>'limit_tagihan')::float8
                    
                            
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

            IF (dataPenanggungBiaya::json->>'penanggungbiaya_id' IS NULL) THEN
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
                    dataPenanggungBiaya::json->>'carabayar_id',
                    dataPenanggungBiaya::json->>'penanggungbiaya_nama',
                    dataPenanggungBiaya::json->>'namabagian',
                    dataPenanggungBiaya::json->>'noindukkaryawan',
                    dataPenanggungBiaya::json->>'jpkm',
                    dataPenanggungBiaya::json->>'instansi'
                ) ;
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
  COST 100");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_084715_migrate_sy_20210105_trigger_pendaftaran_inap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_084715_migrate_sy_20210105_trigger_pendaftaran_inap cannot be reverted.\n";

        return false;
    }
    */
}
