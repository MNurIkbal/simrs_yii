<?php

use yii\db\Migration;

/**
 * Class m200308_125006_function_ubah_schema
 */
class m200308_125006_function_ubah_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
            CREATE TABLE public.deps_saved_ddl
            (
              deps_id serial NOT NULL,
              deps_view_schema character varying(255),
              deps_view_name character varying(255),
              deps_ddl_to_run text,
              CONSTRAINT deps_saved_ddl_pkey PRIMARY KEY (deps_id)
            );');

         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"deps_save_and_drop_dependencies\"(\"p_view_schema\" varchar, \"p_view_name\" varchar)
  RETURNS \"pg_catalog\".\"void\" AS \$BODY\$
declare
  v_curr record;
begin
for v_curr in 
(
  select obj_schema, obj_name, obj_type from
  (
  with recursive recursive_deps(obj_schema, obj_name, obj_type, depth) as 
  (
    select p_view_schema, p_view_name, null::varchar, 0
    union
    select dep_schema::varchar, dep_name::varchar, dep_type::varchar, recursive_deps.depth + 1 from 
    (
      select ref_nsp.nspname ref_schema, ref_cl.relname ref_name, 
      rwr_cl.relkind dep_type,
      rwr_nsp.nspname dep_schema,
      rwr_cl.relname dep_name
      from pg_depend dep
      join pg_class ref_cl on dep.refobjid = ref_cl.oid
      join pg_namespace ref_nsp on ref_cl.relnamespace = ref_nsp.oid
      join pg_rewrite rwr on dep.objid = rwr.oid
      join pg_class rwr_cl on rwr.ev_class = rwr_cl.oid
      join pg_namespace rwr_nsp on rwr_cl.relnamespace = rwr_nsp.oid
      where dep.deptype = 'n'
      and dep.classid = 'pg_rewrite'::regclass
    ) deps
    join recursive_deps on deps.ref_schema = recursive_deps.obj_schema and deps.ref_name = recursive_deps.obj_name
    where (deps.ref_schema != deps.dep_schema or deps.ref_name != deps.dep_name)
  )
  select obj_schema, obj_name, obj_type, depth
  from recursive_deps 
  where depth > 0
  ) t
  group by obj_schema, obj_name, obj_type
  order by max(depth) desc
) loop

  insert into public.deps_saved_ddl(deps_view_schema, deps_view_name, deps_ddl_to_run)
  select p_view_schema, p_view_name, 'COMMENT ON ' ||
  case
  when c.relkind = 'v' then 'VIEW'
  when c.relkind = 'm' then 'MATERIALIZED VIEW'
  else ''
  end
  || ' ' || n.nspname || '.' || c.relname || ' IS ''' || replace(d.description, '''', '''''') || ''';'
  from pg_class c
  join pg_namespace n on n.oid = c.relnamespace
  join pg_description d on d.objoid = c.oid and d.objsubid = 0
  where n.nspname = v_curr.obj_schema and c.relname = v_curr.obj_name and d.description is not null;

  insert into public.deps_saved_ddl(deps_view_schema, deps_view_name, deps_ddl_to_run)
  select p_view_schema, p_view_name, 'COMMENT ON COLUMN ' || n.nspname || '.' || c.relname || '.' || a.attname || ' IS ''' || replace(d.description, '''', '''''') || ''';'
  from pg_class c
  join pg_attribute a on c.oid = a.attrelid
  join pg_namespace n on n.oid = c.relnamespace
  join pg_description d on d.objoid = c.oid and d.objsubid = a.attnum
  where n.nspname = v_curr.obj_schema and c.relname = v_curr.obj_name and d.description is not null;

  insert into public.deps_saved_ddl(deps_view_schema, deps_view_name, deps_ddl_to_run)
  select p_view_schema, p_view_name, 'GRANT ' || privilege_type || ' ON ' || table_schema || '.' || table_name || ' TO ' || grantee
  from information_schema.role_table_grants
  where table_schema = v_curr.obj_schema and table_name = v_curr.obj_name;

  if v_curr.obj_type = 'v' then
    insert into public.deps_saved_ddl(deps_view_schema, deps_view_name, deps_ddl_to_run)
    select p_view_schema, p_view_name, 'CREATE VIEW ' || v_curr.obj_schema || '.' || v_curr.obj_name || ' AS ' || view_definition
    from information_schema.views
    where table_schema = v_curr.obj_schema and table_name = v_curr.obj_name;
  elsif v_curr.obj_type = 'm' then
    insert into public.deps_saved_ddl(deps_view_schema, deps_view_name, deps_ddl_to_run)
    select p_view_schema, p_view_name, 'CREATE MATERIALIZED VIEW ' || v_curr.obj_schema || '.' || v_curr.obj_name || ' AS ' || definition
    from pg_matviews
    where schemaname = v_curr.obj_schema and matviewname = v_curr.obj_name;
  end if;

  execute 'DROP ' ||
  case 
    when v_curr.obj_type = 'v' then 'VIEW'
    when v_curr.obj_type = 'm' then 'MATERIALIZED VIEW'
  end
  || ' ' || v_curr.obj_schema || '.' || v_curr.obj_name;

end loop;
end;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");

         $this->execute('ALTER FUNCTION "public"."deps_save_and_drop_dependencies"("p_view_schema" varchar, "p_view_name" varchar) OWNER TO "postgres";');
         
         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"deps_restore_dependencies\"(\"p_view_schema\" varchar, \"p_view_name\" varchar)
  RETURNS \"pg_catalog\".\"void\" AS \$BODY\$
declare
  v_curr record;
begin
for v_curr in 
(
  select deps_ddl_to_run 
  from public.deps_saved_ddl
  where deps_view_schema = p_view_schema and deps_view_name = p_view_name
  order by deps_id desc
) loop
  execute v_curr.deps_ddl_to_run;
end loop;
delete from public.deps_saved_ddl
where deps_view_schema = p_view_schema and deps_view_name = p_view_name;
end;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('ALTER FUNCTION "public"."deps_restore_dependencies"("p_view_schema" varchar, "p_view_name" varchar) OWNER TO "postgres";');
         
         $this->execute('DROP VIEW if EXISTS "public"."infotagihanpasiensudahbayarSALAH_v";');

         $this->execute('select public.deps_save_and_drop_dependencies(\'public\', \'obatalkes_m\');');

         $this->execute('ALTER TABLE "public"."obatalkes_m" 
  ALTER COLUMN "obatalkes_barcode" TYPE varchar(100) COLLATE "pg_catalog"."default",
  ALTER COLUMN "obatalkes_kode" TYPE varchar(100) COLLATE "pg_catalog"."default",
  ALTER COLUMN "obatalkes_namalain" TYPE varchar(100) COLLATE "pg_catalog"."default",
  ALTER COLUMN "obatalkes_nobatch" TYPE varchar(50) COLLATE "pg_catalog"."default",
  ALTER COLUMN "obatalkes_kategori" TYPE varchar(50) COLLATE "pg_catalog"."default",
  ALTER COLUMN "obatalkes_kadarobat" TYPE varchar(50) COLLATE "pg_catalog"."default";');

         $this->execute('select public.deps_restore_dependencies(\'public\', \'obatalkes_m\');');

         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author yaya

DECLARE
    paramJson VARCHAR;
    vDataPembayaranPelayanan VARCHAR;
    vDataPembayaranPenjamin VARCHAR;
    vDataJenisPembayaran VARCHAR;
    
BEGIN
    paramJson := NEW.additional_data;
    vDataPembayaranPelayanan := paramJson::json->>'pembayaran_pelayanan';
    vDataPembayaranPenjamin := paramJson::json->>'pembayaran_penjamin';
    -- vDataJenisPembayaran := paramJson::json->>'pembayaran_jenis_pembayaran';
    
    -- Insert ke  pembayaranpelayanan_t
    IF (json_array_length(vDataPembayaranPelayanan::json) > 0) THEN 
        INSERT INTO pembayaranpelayanan_t (
                            carabayar_id,
                            ruangan_id,
                            penjamin_id,
                            pendaftaran_id,
                            tandabuktibayar_id,
                            pasien_id,
                            pasienadmisi_id,
                            ruangan_pelakhir_id,
                            no_pembayaran,
                            tgl_pembayaran,
                            total_biayaoa,
                            total_biayatindakan,
                            total_biayapelayanan,
                            total_subsidiasuransi,
                            total_subsidipemerintah,
                            total_subsidirs,
                            total_iurbiaya,
                            total_bayartindakan,
                            total_discount,
                            total_pembebasan,
                            total_sisatagihan,
                            statusbayar,
                            penjualanresep_id,
                            biaya_administrasi,
                            e_collection,
                            no_rekening,
                            nama_pemrekening,
                            penggunaan_uangmuka,
                            total_terbayar,
                            pembulatan,
                            additional_data,
                            created_by
        ) VALUES (
                            NEW.carabayar_id,
                            NEW.ruangan_id,
                            NEW.penjamin_id,
                            NEW.pendaftaran_id,
                            NEW.tandabuktibayar_id,
                            NEW.pasien_id,
                            NEW.pasienadmisi_id,
                            NEW.ruangan_pelakhir_id,
                            NEW.no_pembayaran,
                            NEW.tgl_pembayaran,
                            NEW.total_biayaoa,
                            NEW.total_biayatindakan,
                            NEW.total_biayapelayanan,
                            NEW.total_subsidiasuransi,
                            NEW.total_subsidipemerintah,
                            NEW.total_subsidirs,
                            NEW.total_iurbiaya,
                            NEW.total_bayartindakan,
                            NEW.total_discount,
                            NEW.total_pembebasan,
                            NEW.total_sisatagihan,
                            NEW.statusbayar,
                            NEW.penjualanresep_id,
                            NEW.biaya_administrasi,
                            NEW.e_collection,
                            NEW.no_rekening,
                            NEW.nama_pemrekening,
                            NEW.penggunaan_uangmuka,
                            NEW.total_terbayar,
                            NEW.pembulatan,
                            NEW.additional_data,
                            NEW.created_by
        );
        
    END IF;
    
    
    -- Insert ke  pembayaranpenjamin_t
    IF (json_array_length(vDataPembayaranPenjamin::json) > 0) THEN
        INSERT INTO pembayaranpenjamin_t (
                        pembayaran_id,
                        penjamin_id,
                        penjamin_nama,
                        no_kartu,
                        total_dijamin,
                        created_by
        ) VALUES (
                        NEW.pembayaran_id,
                        NEW.penjamin_id,
                        NEW.penjamin_nama,
                        NEW.no_kartu,
                        NEW.total_dijamin,
                        NEW.created_by
        );
    
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

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200308_125006_function_ubah_schema cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200308_125006_function_ubah_schema cannot be reverted.\n";

        return false;
    }
    */
}
