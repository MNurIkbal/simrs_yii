<?php

use yii\db\Migration;

/**
 * Class m221018_033130_migrate_helper_function_sp_gabung_billing
 */
class m221018_033130_migrate_helper_function_sp_gabung_billing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_gabung_billing"("xno_pendaftaran1" varchar, "xno_pendaftaran2" varchar)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vpendaftaran_obat1 int4;
    vpendaftaran_tindakan1 int4;
    vpendaftaran_obat2 int4;
    vpendaftaran_tindakan2 int4;
    vpendaftaran_id1 int4;
    vpendaftaran_id2 int4;
    vpasien_id1 int4;
    vpasien_id2 int4;
    vtindakanpelayanan_id int4;
    vtindakanpelayanan_id_new int4;
    vtipe VARCHAR;
    vadditional_data TEXT;
    rec_tagihan RECORD;
    cur_tagihan CURSOR FOR 
     SELECT tindakanpelayanan_id , \'TINDAKAN\' AS tipe, additional_data
     FROM tindakanpelayanan_t
     WHERE pendaftaran_id IN (
        SELECT pendaftaran_id
        FROM pendaftaran_t
        WHERE no_pendaftaran = xno_pendaftaran1
     )
     AND is_deleted IS FALSE
     AND tindakansudahbayar_id IS NULL;
    
    
BEGIN
    SELECT pendaftaran_id, pasien_id INTO vpendaftaran_id1, vpasien_id1
    FROM pendaftaran_t
    WHERE no_pendaftaran = xno_pendaftaran1;
    
    SELECT pendaftaran_id, pasien_id INTO vpendaftaran_id2, vpasien_id2
    FROM pendaftaran_t
    WHERE no_pendaftaran = xno_pendaftaran2;
    
    SELECT COUNT(*) INTO vpendaftaran_obat1
    FROM obatalkespasien_t
    WHERE pendaftaran_id = vpendaftaran_id1
    AND is_deleted IS FALSE 
    AND obatsudahbayar_id IS NULL;
    
    SELECT COUNT(*) INTO vpendaftaran_tindakan1
    FROM tindakanpelayanan_t
    WHERE pendaftaran_id = vpendaftaran_id1
    AND is_deleted IS FALSE 
    AND tindakansudahbayar_id IS NULL;
    
    SELECT COUNT(*) INTO vpendaftaran_obat2
    FROM obatalkespasien_t
    WHERE pendaftaran_id = vpendaftaran_id2
    AND is_deleted IS FALSE 
    AND obatsudahbayar_id IS NULL;
    
    SELECT COUNT(*) INTO vpendaftaran_tindakan2
    FROM tindakanpelayanan_t
    WHERE pendaftaran_id = vpendaftaran_id2
    AND is_deleted IS FALSE 
    AND tindakansudahbayar_id IS NULL;
    IF(vpasien_id1 = vpasien_id2)
    THEN
        IF(COALESCE(vpendaftaran_obat1,0) + COALESCE(vpendaftaran_tindakan1,0) = 0) 
        THEN
            vstatus := 1;
            vmessage := CONCAT(\'Pendaftaran \' , xno_pendaftaran1 , \' tidak memiliki tagihan atau sudah dibayarkan, mohon cek kembali Pendaftaran tersebut \') ;
        ELSEIF(COALESCE(vpendaftaran_obat2,0) + COALESCE(vpendaftaran_tindakan2,0) = 0) 
        THEN
            vstatus := 2;
            vmessage := CONCAT(\'Pendaftaran \' , xno_pendaftaran2 , \' tidak memiliki tagihan atau sudah dibayarkan, mohon cek kembali Pendaftaran tersebut \') ;
        ELSE 
            -- Open the cursor
            OPEN cur_tagihan;

            LOOP
            -- fetch row into the film
                FETCH cur_tagihan INTO rec_tagihan;
            -- exit when no more row to fetch
                EXIT WHEN NOT FOUND;
            
            vtindakanpelayanan_id := rec_tagihan.tindakanpelayanan_id;
            vtipe := rec_tagihan.tipe;
            vadditional_data := rec_tagihan.additional_data;
            
            
                INSERT INTO tindakanpelayanan_t(
                    shift_id, kelaspelayanan_id, kelastanggungan_id, pasien_id, rencanaoperasi_id, instalasi_id, daftartindakan_id, alatmedis_id, tipepaket_id, tindakansudahbayar_id, carabayar_id, pendaftaran_id, hasilpemeriksaanrad_id, jeniskasuspenyakit_id, hasilpemeriksaanrm_id, ruangan_id, konsulpoli_id, pasienmasukpenunjang_id, hasilpemeriksaanlabdetail_id, penjamin_id, pasienadmisi_id, verifikasitagihan_id, jurnalrekening_id, instruksitindakan_id, tgl_tindakan, tarif_rsakomodasi, tarif_medis, tarif_paramedis, tarif_bhp, tarif_satuan, tarif_tindakan, tarifcyto_tindakan, satuan_tindakan, qty_tindakan, cyto_tindakan, dokterpenanggungjawab_id, dokterpelaksana_id, dokteranastesi_id, dokterdelegasi_id, bidan1_id, bidan2_id, perawat1_id, perawat2_id, discount_tindakan, pembebasan_tindakan, subsidiasuransi_tindakan, subsidipemerintah_tindakan, subsisidirumahsakit_tindakan, uangditerima_tindakan, keterangantindakan, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, pembulatan, implementasi_id, is_dilakukan, pemakaianambulan_id, is_penatajasa, additional_riwayat, is_valid, kamarruangan_id, kamartempattidur_id, penyulit_tindakan, tarifpenyulit_tindakan, no_tindakanpelayanan, tarif_dijamin, tarif_dibayarkan, tarif_diskon, pembayaran_id, alasan_batal, is_overwrite, harga_origin, cyto_origin, penyulit_origin, tindakanpelayananasal_id, programterapidetail_id, programterapi_id, parent_id)
                SELECT
                    shift_id, kelaspelayanan_id, kelastanggungan_id, pasien_id, rencanaoperasi_id, instalasi_id, daftartindakan_id, alatmedis_id, tipepaket_id, tindakansudahbayar_id, carabayar_id, vpendaftaran_id2, hasilpemeriksaanrad_id, jeniskasuspenyakit_id, hasilpemeriksaanrm_id, ruangan_id, konsulpoli_id, pasienmasukpenunjang_id, hasilpemeriksaanlabdetail_id, penjamin_id, pasienadmisi_id, verifikasitagihan_id, jurnalrekening_id, instruksitindakan_id, tgl_tindakan, tarif_rsakomodasi, tarif_medis, tarif_paramedis, tarif_bhp, tarif_satuan, tarif_tindakan, tarifcyto_tindakan, satuan_tindakan, qty_tindakan, cyto_tindakan, dokterpenanggungjawab_id, dokterpelaksana_id, dokteranastesi_id, dokterdelegasi_id, bidan1_id, bidan2_id, perawat1_id, perawat2_id, discount_tindakan, pembebasan_tindakan, subsidiasuransi_tindakan, subsidipemerintah_tindakan, subsisidirumahsakit_tindakan, uangditerima_tindakan, keterangantindakan, NULL, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, pembulatan, implementasi_id, is_dilakukan, pemakaianambulan_id, is_penatajasa, additional_riwayat, is_valid, kamarruangan_id, kamartempattidur_id, penyulit_tindakan, tarifpenyulit_tindakan, no_tindakanpelayanan, tarif_dijamin, tarif_dibayarkan, tarif_diskon, pembayaran_id, alasan_batal, is_overwrite, harga_origin, cyto_origin, penyulit_origin, tindakanpelayananasal_id, programterapidetail_id, programterapi_id, parent_id
                FROM tindakanpelayanan_t
                WHERE pendaftaran_id = vpendaftaran_id1
                AND tindakanpelayanan_id = vtindakanpelayanan_id
                AND tindakansudahbayar_id IS NULL
                AND is_deleted IS FALSE
                ;
                
                SELECT MAX(tindakanpelayanan_id) INTO vtindakanpelayanan_id_new
                FROM tindakanpelayanan_t
                WHERE pendaftaran_id = vpendaftaran_id2 
                AND is_deleted IS FALSE;
                
                INSERT INTO public.tindakankomponen_t(
                    komponentarif_id, tindakanpelayanan_id, tarif_kompsatuan, tarif_tindakankomp, tarifcyto_tindakankomp, subsidiasuransikomp, subsidipemerintahkomp, subsidirumahsakitkomp, iurbiayakomp, pembayaranjasa_id, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, is_jurnal, discount_komponen, tarifpenyulit_komponen)
                
                SELECT
                    komponentarif_id, vtindakanpelayanan_id_new, tarif_kompsatuan, tarif_tindakankomp, tarifcyto_tindakankomp, subsidiasuransikomp, subsidipemerintahkomp, subsidirumahsakitkomp, iurbiayakomp, pembayaranjasa_id, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, is_jurnal, discount_komponen, tarifpenyulit_komponen
                FROM tindakankomponen_t
                WHERE tindakanpelayanan_id = vtindakanpelayanan_id;
                
                UPDATE tindakanpelayanan_t
                SET is_deleted = TRUE,
                        deleted_date = CURRENT_TIMESTAMP,
                        deleted_by = 0
                WHERE pendaftaran_id = vpendaftaran_id1
                AND tindakansudahbayar_id IS NULL
                AND is_deleted IS FALSE
                AND tindakanpelayanan_id = vtindakanpelayanan_id;
                
                UPDATE tindakanpelayanan_t
                SET additional_data = vadditional_data
                WHERE tindakansudahbayar_id IS NULL
                AND is_deleted IS FALSE
                AND tindakanpelayanan_id = vtindakanpelayanan_id_new;
        END LOOP;

    -- Close the cursor
        CLOSE cur_tagihan;
            
            INSERT INTO obatalkespasien_t(
                sumberdana_id, racikan_id, returresepdetail_id, tipepaket_id, ruangan_id, carabayar_id, pegawai_id, daftartindakan_id, tindakanpelayanan_id, satuankecil_id, shift_id, pendaftaran_id, obatalkes_id, pasien_id, penjamin_id, kelaspelayanan_id, pasienanastesi_id, pasienmasukpenunjang_id, pasienadmisi_id, obatsudahbayar_id, penjualanresep_id, tglpelayanan, r, rke, permintaan_oa, jmlkemasan_oa, kekuatan_oa, satuankekuatan_oa, qty_oa, hargasatuan_oa, signa_oa, harganetto_oa, hargajual_oa, etiket, jmlexposerad, kontrasrad, biayaservice, biayakonseling, jasadokterresep, biayakemasan, biayaadministrasi, tarifcyto, discount, subsidiasuransi, subsidipemerintah, subsidirs, iurbiaya, oa, pembulatan, verifikasitagihan_id, jurnalrekening_id, permohonanoadetail_id, persenppnjual, resepturdetail_id, nilaippnjual, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, perawat1_id, perawat2_id, instruksitindakanbmhp_id, implementasi_id, is_dilakukan, pemakaianambulan_id, is_jurnal, konfigmargindetail_id, qty_konversi, is_penatajasa, det, status_bmhp, det_konversi, signa, no_obatalkespasien, tarif_dijamin, tarif_dibayarkan, tarif_diskon, keterangan, pembayaran_id, alasan_batal, is_overwrite, harga_origin, udd_detail_id, is_uddterima, qty_medis, nama_racikan, det_medis, qty_racikan, satuan_racikan_id, cost, baseprice )
            SELECT 
                sumberdana_id, racikan_id, returresepdetail_id, tipepaket_id, ruangan_id, carabayar_id, pegawai_id, daftartindakan_id, tindakanpelayanan_id, satuankecil_id, shift_id, vpendaftaran_id2, obatalkes_id, pasien_id, penjamin_id, kelaspelayanan_id, pasienanastesi_id, pasienmasukpenunjang_id, pasienadmisi_id, obatsudahbayar_id, penjualanresep_id, tglpelayanan, r, rke, permintaan_oa, jmlkemasan_oa, kekuatan_oa, satuankekuatan_oa, qty_oa, hargasatuan_oa, signa_oa, harganetto_oa, hargajual_oa, etiket, jmlexposerad, kontrasrad, biayaservice, biayakonseling, jasadokterresep, biayakemasan, biayaadministrasi, tarifcyto, discount, subsidiasuransi, subsidipemerintah, subsidirs, iurbiaya, oa, pembulatan, verifikasitagihan_id, jurnalrekening_id, permohonanoadetail_id, persenppnjual, resepturdetail_id, nilaippnjual, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, perawat1_id, perawat2_id, instruksitindakanbmhp_id, implementasi_id, is_dilakukan, pemakaianambulan_id, is_jurnal, konfigmargindetail_id, qty_konversi, is_penatajasa, det, status_bmhp, det_konversi, signa, no_obatalkespasien, tarif_dijamin, tarif_dibayarkan, tarif_diskon, keterangan, pembayaran_id, alasan_batal, is_overwrite, harga_origin, udd_detail_id, is_uddterima, qty_medis, nama_racikan, det_medis, qty_racikan, satuan_racikan_id, cost, baseprice
            FROM obatalkespasien_t
            WHERE pendaftaran_id = vpendaftaran_id1
            AND obatsudahbayar_id IS NULL
            AND is_deleted IS FALSE;
            
            UPDATE obatalkespasien_t
            SET is_deleted = TRUE,
                    deleted_date = CURRENT_TIMESTAMP,
                    deleted_by = 0
            WHERE pendaftaran_id = vpendaftaran_id1
            AND obatsudahbayar_id IS NULL
            AND is_deleted IS FALSE;
            
            vstatus := 0;
            vmessage := \'Success\';
            
        END IF;
    ELSE
        vmessage := CONCAT(\'Rekam Medik/Pasien Harus Sama!!! \') ;
    END IF;
    
    RETURN QUERY 
    SELECT vstatus, vmessage;
        
END; $BODY$
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
        echo "m221018_033130_migrate_helper_function_sp_gabung_billing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_033130_migrate_helper_function_sp_gabung_billing cannot be reverted.\n";

        return false;
    }
    */
}
