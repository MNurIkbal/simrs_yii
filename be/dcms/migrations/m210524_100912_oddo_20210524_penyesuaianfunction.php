<?php

use yii\db\Migration;

/**
 * Class m210524_100912_oddo_20210524_penyesuaianfunction
 */
class m210524_100912_oddo_20210524_penyesuaianfunction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute("
    CREATE OR REPLACE FUNCTION \"public\".\"ins_pembayaran\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
                
            DECLARE
                paramJson VARCHAR;
                vPembayaranpelayanan json;
                vPembayaranpenjamin json;
                vPembayaranmetode json;
                vPembayarandiskon json;
                vPembayaran_id INTEGER;
                vrow json;
                
                
            BEGIN
                paramJson := NEW.additional_data;
                vPembayaranpelayanan := paramJson::json->>'pembayaran_pelayanan';
                vPembayaranpenjamin := paramJson::json->>'pembayaran_penjamin';
                vPembayaranmetode := paramJson::json->>'pembayaran_jenis_pembayaran';
                vPembayarandiskon := paramJson::json->>'pembayaran_diskon';
                vPembayaran_id := NEW.pembayaran_id;


            -- insert ke pembayaranpelayanan_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpelayanan)
              LOOP
                    IF (vrow->>'penjamin_id' IS NOT NULL) THEN
                        INSERT INTO pembayaranpelayanan_t (
                                        pembayaran_id,
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
                                        additional_data

                     ) VALUES (
                                vPembayaran_id,
                                (vrow->>'carabayar_id')::INTEGER,
                                (vrow->>'ruangan_id')::INTEGER,
                                (vrow->>'penjamin_id')::INTEGER,
                                (vrow->>'pendaftaran_id')::INTEGER,
                                (vrow->>'tandabuktibayar_id')::INTEGER,
                                (vrow->>'pasien_id')::INTEGER,
                                (vrow->>'pasienadmisi_id')::INTEGER,
                                (vrow->>'ruangan_pelakhir_id')::INTEGER,
                                (vrow->>'no_pembayaran')::INTEGER,
                                (vrow->>'tgl_pembayaran')::TIMESTAMP,
                                (vrow->>'total_biayaoa')::FLOAT,
                                (vrow->>'total_biayatindakan')::FLOAT,
                                (vrow->>'total_biayapelayanan')::FLOAT,
                                (vrow->>'total_subsidiasuransi')::FLOAT,
                                (vrow->>'total_subsidipemerintah')::FLOAT,
                                (vrow->>'total_subsidirs')::FLOAT,
                                (vrow->>'total_iurbiaya')::FLOAT,
                                (vrow->>'total_bayartindakan')::FLOAT,
                                (vrow->>'total_discount')::FLOAT,
                                (vrow->>'total_pembebasan')::FLOAT,
                                (vrow->>'total_sisatagihan')::FLOAT,
                                (vrow->>'statusbayar')::VARCHAR,
                                CASE WHEN vrow->>'pendaftaran_id' IS NOT NULL THEN
                                                                    NULL
                                                                ELSE
                                                                    (vrow->>'penjualanresep_id')::INTEGER
                                                                END,
                                (vrow->>'biaya_administrasi')::FLOAT,
                                (vrow->>'e_collection')::BOOLEAN,
                                (vrow->>'no_rekening')::VARCHAR,
                                (vrow->>'nama_pemrekening')::VARCHAR,
                                (vrow->>'penggunaan_uangmuka')::FLOAT,
                                (vrow->>'total_terbayar')::FLOAT,
                                (vrow->>'pembulatan')::FLOAT,
                                (vrow->>'additional_data')::TEXT
                        );
                    END IF;
                END LOOP;
                
                    
            -- insert ke pembayaranpenjamin_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpenjamin)
              LOOP
                    IF (vrow->>'penjamin_id' IS NOT NULL) THEN
                        INSERT INTO pembayaranpenjamin_t (
                                        pembayaran_id,
                                        penjamin_id,
                                        penjamin_nama,
                                        no_kartu,
                                        total_dijamin

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'penjamin_id')::INTEGER,
                                        (vrow->>'penjamin_nama')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dijamin')::FLOAT                         
                                );
                    END IF;
                END LOOP;
                
                -- insert ke pembayaranmetode_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranmetode)
              LOOP
                    IF (vrow->>'metode_bayar' IS NOT NULL) THEN
                        INSERT INTO pembayaranmetode_t (
                                        pembayaran_id,
                                        metode_bayar,
                                        no_kartu,
                                        total_dibayar,
                                                                                jenisnontunai_id,
                                                                                edclist_id,
                                                                                nama_edc,
                                                                                created_by,
                                                                                pendaftaran_id

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'metode_bayar')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dibayar')::FLOAT,                     
                                        (vrow->>'jenisnontunai_id')::INTEGER,                     
                                        (vrow->>'edclist_id')::INTEGER,                     
                                        (vrow->>'label_edc')::VARCHAR,
                                                                                NEW.created_by,
                                                                                NEW.pendaftaran_id
                                );
                    END IF;
                END LOOP;


                    -- insert ke pembayarandiskon_t
                    FOR vrow IN SELECT * FROM json_array_elements(vPembayarandiskon)
                    LOOP 
                        IF (vrow->>'total_diskon' IS NOT NULL) THEN
                            INSERT INTO pembayarandiskon_t (
                                                    pembayaran_id,
                                                    pegawai_id,
                                                    komponentarif_id,
                                                    total_komponentarif,
                                                    total_diskon,
                                                                                                        alasan
                            ) VALUES (
                                                    vPembayaran_id,
                                                    (vrow->>'pegawai_id')::INTEGER,
                                                    (vrow->>'komponentarif_id')::INTEGER,
                                                    (vrow->>'total_komponentarif')::FLOAT,
                                                    (vrow->>'total_diskon')::FLOAT,
                                                    (vrow->>'alasan')::TEXT
                            );
                            END IF;
                    END LOOP;
                    
                    
                RETURN NEW;
            END
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

$this->execute("
    CREATE OR REPLACE FUNCTION \"public\".\"int_billing_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
-- INSERT table history pembayaran_r--
    INSERT INTO int_billing_r (     
        pembayaran_id,
        pendaftaran_id,
        pasienadmisi_id,
        total_tagihan,
        total_dibayar,
        total_dijamin,
        total_sisatagihan,
        total_kembalian,
        total_administrasi,
        total_pembulatan,
        total_pembebasan,
        penggunaan_uangmuka,
        pemberianpiutang_id,
        total_ditagihkan,
        total_tunai,
        total_nontunai,
        total_discount,
        total_discountpembayaran,
        catatan,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan  
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        NEW.pasienadmisi_id ,
        NEW.total_tagihan ,
        NEW.total_dibayar ,
        NEW.total_dijamin,
        NEW.total_sisatagihan ,
        NEW.total_kembalian ,
        NEW.total_administrasi ,
        NEW.total_pembulatan ,
        NEW.total_pembebasan ,
        NEW.penggunaan_uangmuka ,
        NEW.pemberianpiutang_id ,
        NEW.total_ditagihkan ,
        NEW.total_tunai ,
        NEW.total_nontunai,
        NEW.total_discount ,
        NEW.total_discountpembayaran ,
        NEW.catatan ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'RECEIPT'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

$this->execute("
    CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_delete\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ DECLARE v_daftartindakan_id int;
v_ruangan_id int;
v_penjamin_id int;
v_instalasi_id int;
v_pegawai_id int;
v_pasienadmisi_id int;
v_kelaspelayanan_id int;
v_total_diskon float;
v_total_discountpembayaran float;
v_totaltunai float;
v_totaldijamin float;
v_adm_dijamin float;
v_adm_dibayar float;
v_total_dibayar float;
v_total_sisatagihan float;
v_total_kembalian float;
v_total_administrasi float; 
v_total_pembulatan float; 
v_total_pembebasan float; 
v_penggunaan_uangmuka float; 
v_pemberianpiutang_id float; 
v_total_ditagihkan float; 
v_total_tunai float; 
v_total_discount float; 
v_total_tagihan float; 
v_catatan text;
BEGIN 


----------------------------- SETUP META DATA PEMBAYARAN -----------------------------
  SELECT 
    pembayaran_t.total_dijamin,
    pembayaran_t.total_discount,
    pembayaran_t.total_discountpembayaran,
    (
      (
        pembayaran_t.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'dijamin'
    ):: float,
    (
      (
        pembayaran_t.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'harusbayar'
    ):: float,
    pembayaran_t.pasienadmisi_id,
    pembayaran_t.total_tagihan,
    pembayaran_t.total_dibayar,
    pembayaran_t.total_sisatagihan,
    pembayaran_t.total_kembalian,
    pembayaran_t.total_administrasi, 
    pembayaran_t.total_pembulatan, 
    pembayaran_t.total_pembebasan, 
    pembayaran_t.penggunaan_uangmuka, 
    pembayaran_t.pemberianpiutang_id, 
    pembayaran_t.total_ditagihkan, 
    pembayaran_t.total_tunai, 
    pembayaran_t.total_discount, 
    pembayaran_t.catatan
  INTO 
    v_totaldijamin,
    v_total_diskon,
    v_total_discountpembayaran,
    v_adm_dijamin,
    v_adm_dibayar,
    v_pasienadmisi_id,
    v_total_tagihan,
    v_total_dibayar,
    v_total_sisatagihan,
    v_total_kembalian,
    v_total_administrasi, 
    v_total_pembulatan, 
    v_total_pembebasan, 
    v_penggunaan_uangmuka, 
    v_pemberianpiutang_id, 
    v_total_ditagihkan, 
    v_total_tunai, 
    v_total_discount, 
    v_catatan
  FROM 
    pembayaran_t 
  WHERE 
    pembayaran_t.pembayaran_id = NEW.pembayaran_id;

------------------------- END SETUP ----------------------------------------------------------


---------------------------------- INSERT PEMBAYARAN TUNAI --------------------------------------
IF(
  NEW.is_deleted IS TRUE 
  and v_total_tunai <> 0
) THEN -- INSERT table history pembayaran_r, menjadi Deposit Refund
INSERT INTO pembayaran_r (
  pembayaran_id, 
  pendaftaran_id, 
  pasienadmisi_id, 
  total_tagihan, 
  total_dibayar, 
  total_dijamin, 
  total_sisatagihan, 
  total_kembalian, 
  total_administrasi, 
  total_pembulatan, 
  total_pembebasan, 
  penggunaan_uangmuka, 
  pemberianpiutang_id, 
  total_ditagihkan, 
  total_tunai, 
  total_nontunai, 
  total_discount, 
  total_discountpembayaran, 
  catatan, 
  tipe_pembayaran, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  keterangan
) VALUES  (
  NEW.pembayaran_id,
  NEW.pendaftaran_id,
  v_pasienadmisi_id,
  -1 * v_total_tagihan, 
  -1 * v_total_dibayar, 
  0, 
  -1 * v_total_sisatagihan, 
  -1 * v_total_kembalian, 
  -1 * v_total_administrasi, 
  -1 * v_total_pembulatan, 
  -1 * v_total_pembebasan, 
  v_penggunaan_uangmuka, 
  v_pemberianpiutang_id, 
  -1 * v_total_ditagihkan, 
  -1 * v_total_tunai, 
  0, 
  -1 * v_total_discount, 
  -1 * v_total_discountpembayaran, 
  v_catatan, 
  682, 
  NEW.additional_data, 
  NEW.created_date, 
  NEW.created_by, 
  NEW.modified_count, 
  NEW.last_modified_date, 
  NEW.last_modified_by, 
  NEW.is_deleted, 
  NEW.is_active, 
  NEW.deleted_date, 
  NEW.deleted_by, 
  'REFUND'
);

ELSEIF(
  NEW.is_deleted IS TRUE 
  and v_totaldijamin <> 0
) THEN -- INSERT table history pembayaran_r, menjadi Deposit Refund
INSERT INTO pembayaran_r (
  pembayaran_id, 
  pendaftaran_id, 
  pasienadmisi_id, 
  total_tagihan, 
  total_dibayar, 
  total_dijamin, 
  total_sisatagihan, 
  total_kembalian, 
  total_administrasi, 
  total_pembulatan, 
  total_pembebasan, 
  penggunaan_uangmuka, 
  pemberianpiutang_id, 
  total_ditagihkan, 
  total_tunai, 
  total_nontunai, 
  total_discount, 
  total_discountpembayaran, 
  catatan, 
  tipe_pembayaran, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  keterangan
) VALUES  (
  NEW.pembayaran_id,
  NEW.pendaftaran_id,
  v_pasienadmisi_id,
  -1 * v_total_tagihan, 
  -1 * v_total_dibayar, 
  0, 
  -1 * v_total_sisatagihan, 
  -1 * v_total_kembalian, 
  -1 * v_total_administrasi, 
  -1 * v_total_pembulatan, 
  -1 * v_total_pembebasan, 
  v_penggunaan_uangmuka, 
  v_pemberianpiutang_id, 
  -1 * v_total_ditagihkan, 
  -1 * v_total_tunai, 
  0, 
  -1 * v_total_discount, 
  -1 * v_total_discountpembayaran, 
  v_catatan, 
  682, 
  NEW.additional_data, 
  NEW.created_date, 
  NEW.created_by, 
  NEW.modified_count, 
  NEW.last_modified_date, 
  NEW.last_modified_by, 
  NEW.is_deleted, 
  NEW.is_active, 
  NEW.deleted_date, 
  NEW.deleted_by, 
  'REFUND'
);

END IF;
------------------------- END INSERT -----------------------------------------------------------------------


-------------------------------- SETUP METADATA PENDAFTARAN -----------------------------------------------
IF (NEW.is_deleted IS TRUE AND (
    v_total_pembulatan <> 0 OR 
    (COALESCE(v_total_diskon, 0) + COALESCE(v_total_discountpembayaran, 0) <> 0) OR 
    v_total_administrasi <> 0 )) THEN
  IF (v_pasienadmisi_id IS NOT NULL) THEN
    SELECT 
        ruangan_id, 
        penjamin_id, 
        3 as instalasi_id, 
        pegawai_id, 
        kelaspelayanan_id 
    INTO 
        v_ruangan_id, 
        v_penjamin_id, 
        v_instalasi_id, 
        v_pegawai_id, 
        v_kelaspelayanan_id 
    FROM 
      pasienadmisi_t 
    WHERE 
      pasienadmisi_t.pasienadmisi_id = v_pasienadmisi_id;
  ELSE 
    SELECT 
      pendaftaran_t.ruangan_id, 
      pendaftaran_t.penjamin_id, 
      pendaftaran_t.instalasi_id, 
      pendaftaran_t.pegawai_id,
      kelaspelayanan_id 
    INTO 
      v_ruangan_id, 
      v_penjamin_id, 
      v_instalasi_id, 
      v_pegawai_id,
      v_kelaspelayanan_id 
    FROM 
      pendaftaran_t 
    WHERE 
      pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
  END IF;
END IF;

-------------------------------- END SETUP ----------------------------------------------------------------


----------------------------> insert pembulatan ke tindakanpelayanan_r <---------------------------------   

IF(
  NEW.is_deleted IS TRUE 
  and v_total_pembulatan <> 0
) THEN 


v_daftartindakan_id := 99990;
-- daftartindakan_id untuk pembulatan
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, 
  daftartindakan_id, 
  tarif_satuan, 
  tarif_tindakan, 
  qty_tindakan, 
  keterangan, 
  ruangan_id, 
  instalasi_id, 
  penjamin_id, 
  dokterpenanggungjawab_id, 
  tgl_tindakan, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  tgl_proses, 
  pembayaran_id, 
  tarif_diskon, 
  tarif_dijamin, 
  tarif_dibayarkan,
  kelaspelayanan_id
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    v_total_pembulatan, 
    -1 * v_total_pembulatan, 
    '-1', 
    'BILLING CANCEL', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_pegawai_id, 
    NEW.created_date, 
    NEW.additional_data, 
    NEW.created_date, 
    NEW.created_by, 
    NEW.modified_count, 
    NEW.last_modified_date, 
    NEW.last_modified_by, 
    NEW.is_deleted, 
    NEW.is_active, 
    NEW.deleted_date, 
    NEW.deleted_by, 
    NEW.created_date, 
    NEW.pembayaran_id, 
    0, 
    0, 
    --tarif_dijamin
    -1 * v_total_pembulatan,
    v_kelaspelayanan_id
    );
END IF;


---------------------------> insert diskon ke tindakanpelayanan_r <-------------------------
IF(
  NEW.is_deleted IS TRUE 
  and (
    COALESCE(v_total_diskon, 0) + COALESCE(v_total_discountpembayaran, 0) <> 0
  )
) THEN 

v_daftartindakan_id := 99991;
-- daftartindakan_id untuk diskon
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, 
  daftartindakan_id, 
  tarif_satuan, 
  tarif_tindakan, 
  qty_tindakan, 
  keterangan, 
  ruangan_id, 
  instalasi_id, 
  penjamin_id, 
  dokterpenanggungjawab_id, 
  tgl_tindakan, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  tgl_proses, 
  pembayaran_id, 
  tarif_diskon, 
  tarif_dijamin, 
  tarif_dibayarkan,
  kelaspelayanan_id
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    COALESCE(-1 * v_total_diskon, 0)+ COALESCE(
      -1 * v_total_discountpembayaran, 0
    ), 
    COALESCE(v_total_diskon, 0)+ COALESCE(v_total_discountpembayaran, 0), 
    '-1', 
    'DISCOUNT CANCEL', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_pegawai_id, 
    NEW.created_date, 
    NEW.additional_data, 
    NEW.created_date, 
    NEW.created_by, 
    NEW.modified_count, 
    NEW.last_modified_date, 
    NEW.last_modified_by, 
    NEW.is_deleted, 
    NEW.is_active, 
    NEW.deleted_date, 
    NEW.deleted_by, 
    NEW.created_date, 
    NEW.pembayaran_id, 
    0, 
    CASE WHEN v_totaldijamin <> 0 THEN COALESCE(v_total_diskon, 0)+ COALESCE(v_total_discountpembayaran, 0) ELSE 0 END, 
    CASE WHEN v_totaldijamin = 0 THEN COALESCE(v_total_diskon, 0)+ COALESCE(v_total_discountpembayaran, 0) ELSE 0 END,
    v_kelaspelayanan_id
    );
END IF;


-------------------------------------> insert administrasi ke tindakanpelayanan_r <--------------------------------------------
IF(
  NEW.is_deleted IS TRUE 
  and v_total_administrasi <> 0
) THEN 

v_daftartindakan_id := 99992;
-- daftartindakan_id untuk administrasi
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, 
  daftartindakan_id, 
  tarif_satuan, 
  tarif_tindakan, 
  qty_tindakan, 
  keterangan, 
  ruangan_id, 
  instalasi_id, 
  penjamin_id, 
  dokterpenanggungjawab_id, 
  tgl_tindakan, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  tgl_proses, 
  pembayaran_id, 
  tarif_diskon, 
  tarif_dijamin, 
  tarif_dibayarkan,
  kelaspelayanan_id
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    v_total_administrasi, 
    -1 * v_total_administrasi, 
    '-1', 
    'BILLING CANCEL', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_pegawai_id, 
    NEW.created_date, 
    NEW.additional_data, 
    NEW.created_date, 
    NEW.created_by, 
    NEW.modified_count, 
    NEW.last_modified_date, 
    NEW.last_modified_by, 
    NEW.is_deleted, 
    NEW.is_active, 
    NEW.deleted_date, 
    NEW.deleted_by, 
    NEW.created_date, 
    NEW.pembayaran_id, 
    0, 
    -1 * v_adm_dijamin, 
    -1 * v_adm_dibayar,
    v_kelaspelayanan_id
    );
END IF;
RETURN NEW;
END \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

$this->execute("
    CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
-- INSERT table history pembayaran_r untuk case tunai --
    IF(NEW.total_tunai <> 0)
        THEN
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        pasienadmisi_id,
        total_tagihan,
        total_dibayar,
        total_dijamin,
        total_sisatagihan,
        total_kembalian,
        total_administrasi,
        total_pembulatan,
        total_pembebasan,
        penggunaan_uangmuka,
        pemberianpiutang_id,
        total_ditagihkan,
        total_tunai,
        total_nontunai,
        total_discount,
        total_discountpembayaran,
        catatan,
        tipe_pembayaran,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan  
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        NEW.pasienadmisi_id ,
        NEW.total_tagihan ,
        NEW.total_dibayar ,
        0, -- total_dijamin
        NEW.total_sisatagihan ,
        NEW.total_kembalian ,
        NEW.total_administrasi ,
        NEW.total_pembulatan ,
        NEW.total_pembebasan ,
        NEW.penggunaan_uangmuka ,
        NEW.pemberianpiutang_id ,
        NEW.total_ditagihkan ,
        NEW.total_tunai ,
        0 , --total_nontunai
        NEW.total_discount ,
        NEW.total_discountpembayaran ,
        NEW.catatan ,
        682,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'RECEIPT'
        );
END IF;

-- INSERT table history pembayaran_r untuk case dijamin --
IF(NEW.total_dijamin <> 0)
        THEN
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        pasienadmisi_id,
        total_tagihan,
        total_dibayar,
        total_dijamin,
        total_sisatagihan,
        total_kembalian,
        total_administrasi,
        total_pembulatan,
        total_pembebasan,
        penggunaan_uangmuka,
        pemberianpiutang_id,
        total_ditagihkan,
        total_tunai,
        total_nontunai,
        total_discount,
        total_discountpembayaran,
        catatan,
        tipe_pembayaran,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan  
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        NEW.pasienadmisi_id ,
        NEW.total_tagihan ,
        NEW.total_dibayar ,
        NEW.total_dijamin ,
        NEW.total_sisatagihan ,
        NEW.total_kembalian ,
        NEW.total_administrasi ,
        NEW.total_pembulatan ,
        NEW.total_pembebasan ,
        NEW.penggunaan_uangmuka ,
        NEW.pemberianpiutang_id ,
        NEW.total_ditagihkan ,
        0 , --total_tunai
        0 , --total_nontunai
        NEW.total_discount ,
        NEW.total_discountpembayaran ,
        NEW.catatan ,
        682,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'RECEIPT'
        );
END IF;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

$this->execute("
    CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_nontunai\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE  
        v_tipe_pembayaran INT;
        
BEGIN
    
            SELECT 
                jenisnontunai_m.tipe_pembayaran
            INTO
                v_tipe_pembayaran
            FROM jenisnontunai_m
            WHERE jenisnontunai_m.jenisnontunai_id = NEW.jenisnontunai_id;

        -- INSERT table history pembayaran_r untuk case non tunai
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        total_tunai,
        total_nontunai,
        total_dijamin,
        nama_edc,
        tipe_pembayaran,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        0, --total_tunai
        NEW.total_dibayar ,
        0, --total dijamin
        NEW.nama_edc,
        v_tipe_pembayaran,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'RECEIPT'
        );

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
        echo "m210524_100912_oddo_20210524_penyesuaianfunction cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210524_100912_oddo_20210524_penyesuaianfunction cannot be reverted.\n";

        return false;
    }
    */
}
