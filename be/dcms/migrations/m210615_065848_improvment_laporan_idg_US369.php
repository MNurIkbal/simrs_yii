<?php

use yii\db\Migration;

/**
 * Class m210615_065848_improvment_laporan_idg_US369
 */
class m210615_065848_improvment_laporan_idg_US369 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS "public"."laporanpelayananigd_fn"(date, date);
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."laporanpelayananigd_fn"("xfirstdate" date, "xlastdate" date)
            RETURNS TABLE("tgl_pendaftaran" date, "tgl_triase" date, "rujukan" int4, "jenis_pelayanan" varchar, "hasil_triase" varchar, "pasien_id" int4, "carakeluar" int4, "carakeluar_nama" varchar, "jenis_kelamin" varchar, "kunjungan" varchar, "pasienmasukrujukan" int4, "pasienmasuknonrujukan" int4, "triase_resusitasi" int4, "triase_emergent" int4, "triase_urgent" int4, "triase_nonurgent" int4, "triase_falseemergency" int4, "pasientindaklanjut_dipulangkan" int4, "pasientindaklanjut_dirujukrslain" int4, "pasientindaklanjut_pulangpaksa" int4, "pasientindaklanjut_meninggal" int4, "pasientindaklanjut_dirujukri" int4, "pasientindaklanjut_lainlain" int4, "pasientindaklanjut_melarikandiri" int4, "pasien_baru" int4, "pasien_lama" int4, "pasien_laki" int4, "pasien_perempuan" int4) AS $BODY$

            DECLARE 
            --jenis_ruangan int4;
            BEGIN

                    FOR tgl_pendaftaran, tgl_triase, rujukan, jenis_pelayanan, hasil_triase, pasien_id, carakeluar, carakeluar_nama, jenis_kelamin , kunjungan IN
            --              SELECT triase_t.trauma from triase_t WHERE is_active = TRUE AND is_deleted = FALSE
                            SELECT
                    (to_char(pendaftaran_t.tgl_pendaftaran, \'YYYY-MM-DD\'::text))::date AS tgl_pendaftaran,
                    (to_char(triase_t.tgl_triase, \'YYYY-MM-DD\'::text))::date AS tgl_triase,
                    --triase_t.tgl_triase, 
                                pendaftaran_t.rujukan_id AS rujukan,
                    triase_t.trauma AS jenis_pelayanan,
                    triase_t.hasil_triase,
                    pasien_m.pasien_id,
                    pasienpulang_t.carakeluar_id AS carakeluar,
                    carakeluar_m.carakeluar_nama,
                                pasien_m.jeniskelamin AS jenis_kelamin,
                                pendaftaran_t.kunjungan AS status_kunjungan
                    FROM pendaftaran_t
                    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                    JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
                    LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
                    LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
                    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE
                    AND triase_t.is_active = TRUE 
                    AND triase_t.is_deleted = FALSE
                                GROUP BY  (to_char(pendaftaran_t.tgl_pendaftaran, \'YYYY-MM-DD\'::text))::date,
                    (to_char(triase_t.tgl_triase, \'YYYY-MM-DD\'::text))::date,
                                pendaftaran_t.rujukan_id,
                    triase_t.trauma,
                    triase_t.hasil_triase,
                    pasien_m.pasien_id,
                    pasienpulang_t.carakeluar_id,
                    carakeluar_m.carakeluar_nama,
                                pasien_m.jeniskelamin,
                                pendaftaran_t.kunjungan
            --              LOOP 
            --              FOR carakeluar_id, carakeluar_nama IN
            --                  SELECT * FROM carakeluar_m WHERE is_active = TRUE AND is_deleted = FALSE
                        LOOP
                            SELECT * from f_getpasienmasukigdrujukan(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, rujukan) INTO pasienmasukrujukan;
                            
                            SELECT * from f_getpasienmasukigdnonrujukan(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, rujukan) INTO pasienmasuknonrujukan;

            --                  SELECT 0 INTO triase_resusitasi;
                            SELECT * from f_getpasienmasukigdtriaseresusitasi(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, hasil_triase) INTO triase_resusitasi;
                            
            --                  SELECT 0 INTO triase_emergent;
                            SELECT * from f_getpasienmasukigdtriaseemergent(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, hasil_triase) INTO triase_emergent;
                            
            --                  SELECT 0 INTO triase_urgent;
                            SELECT * from f_getpasienmasukigdtriaseurgent(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, hasil_triase) INTO triase_urgent;
                            
            --                  SELECT 0 INTO triase_nonurgent;
                            SELECT * from f_getpasienmasukigdtriasenonurgent(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, hasil_triase) INTO triase_nonurgent;
                            
                            SELECT * from f_getpasienmasukigdtriasefalseemergency(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, hasil_triase) INTO triase_falseemergency;
                            
            --                  SELECT * from f_getpasienmasukigdtindaklanjut(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, 1) INTO pasientindaklanjut;
                            
                            SELECT * from f_getpasienmasukigdtindaklanjut_dipulangkan(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, carakeluar) INTO pasientindaklanjut_dipulangkan;
                            
                            SELECT * from f_getpasienmasukigdtindaklanjut_dirujukrslain(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, carakeluar) INTO pasientindaklanjut_dirujukrslain;
                            
                            SELECT * from f_getpasienmasukigdtindaklanjut_pulangpaksa(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, carakeluar) INTO pasientindaklanjut_pulangpaksa;
                            
                            SELECT * from f_getpasienmasukigdtindaklanjut_meninggal(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, carakeluar) INTO pasientindaklanjut_meninggal;
                            
                            SELECT * from f_getpasienmasukigdtindaklanjut_dirujukri(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, carakeluar) INTO pasientindaklanjut_dirujukri;
                            
                            SELECT * from f_getpasienmasukigdtindaklanjut_lainlain(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, carakeluar) INTO pasientindaklanjut_lainlain;
                            
                            SELECT * from f_getpasienmasukigdtindaklanjut_melarikandiri(xfirstdate::DATE, xlastdate::DATE, jenis_pelayanan, carakeluar) INTO pasientindaklanjut_melarikandiri;
                            
                            SELECT CASE kunjungan WHEN \'180\' THEN 1 ELSE 0 END INTO pasien_baru;
                            SELECT CASE kunjungan WHEN \'181\' THEN 1 ELSE 0 END INTO pasien_lama;
                            SELECT CASE jenis_kelamin WHEN \'15\' THEN 1 ELSE 0 END INTO pasien_laki;
                            SELECT CASE jenis_kelamin WHEN \'16\' THEN 1 ELSE 0 END INTO pasien_perempuan;
                             RETURN NEXT;
            --              END LOOP;
                    END LOOP;
            END
            $BODY$
            LANGUAGE plpgsql IMMUTABLE
            COST 100
            ROWS 1000;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210615_065848_improvment_laporan_idg_US369 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210615_065848_improvment_laporan_idg_US369 cannot be reverted.\n";

        return false;
    }
    */
}
