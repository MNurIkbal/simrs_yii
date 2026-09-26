<?php

use yii\db\Migration;

/**
 * Class m230926_074830_migrate_GLBJ268_upd_stokkuotadokter_from_antrian_try
 */
class m230926_074830_migrate_GLBJ268_upd_stokkuotadokter_from_antrian_try extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.upd_stokkuotadokter_from_antrian_try()
        RETURNS trigger
        LANGUAGE plpgsql
       AS \$function\$DECLARE
              vAntrianId integer;
              vPegawaiId integer;
              vJadwalId integer;
              vJadwalPoliId integer;
              vJadwalExist integer;
              vMaxAntrian integer;
              vAsalStok integer;       
              vOldAsalStok integer;
              vAsalStokOnline integer;
              vOldAsalStokOnline Integer;
              idKonsulPoli INTEGER;
              isBpjs BOOLEAN;
              vGroupCarabayar INTEGER;
              
              
              
              BEGIN
                      IF(TG_OP = 'DELETE') THEN
                              DELETE FROM stokkuotadokter_t WHERE antrian_id = OLD.antrian_id;
                              RETURN NULL;
                      END IF;
              
                      vAntrianId = NEW.antrian_id;
                      vPegawaiId = NEW.pegawai_id;
                      vJadwalId = NEW.jadwaldokter_id;
                      vJadwalPoliId = NEW.jadwalbukapoli_id;
                      vGroupCarabayar = NEW.groupcarabayar_id;
                      isBpjs = FALSE;
              
                      IF (vPegawaiId IS NOT NULL AND vJadwalId IS NOT NULL) THEN
                              SELECT stokkuotadokter_id
                              INTO vAsalStok
                              FROM stokkuotadokter_t
                              WHERE jadwaldokter_id = vJadwalId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0;
                              
                              IF (vGroupCarabayar IS NOT NULL) THEN
                                  IF (vGroupCarabayar = '418') THEN
                                      isBpjs = TRUE;
                                  ELSE
                                      isBpjs = FALSE;
                                  END IF;
                              END IF;
                              
                              IF (TG_OP = 'INSERT') THEN
                                      IF (NEW.jenisantrian_id = '312') THEN
                                              SELECT stokkuotadokter_id
                                              INTO vAsalStokOnline
                                              FROM stokkuotadokter_t
                                              WHERE jadwaldokter_id = vJadwalId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0 AND is_online = TRUE;
                                              
                                              IF (NEW.is_online) THEN
                                                  IF (isBpjs = TRUE) THEN
                                                         INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                                          VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online,1);
                                                  ELSE
                                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                                          VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online,1);
                                                  END IF;
              --                                       INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
              --                                       VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online);
                                              END IF;
                                      END IF;
                                      /*SELECT jadwaldokter_id
                                      INTO vJadwalExist
                                      FROM stokkuotadokter_t
                                      WHERE jadwaldokter_id = vJadwalId
                                      LIMIT 1;*/
              
                                      IF (vAsalStok IS NULL) THEN
                                              SELECT maximumantrian
                                              INTO vMaxAntrian
                                              FROM jadwaldokter_m
                                              WHERE jadwaldokter_id = vJadwalId;
                                              INSERT INTO stokkuotadokter_t (jadwaldokter_id,flag,tgltransaksi_in,kuota_in,kuota_out)
                                              VALUES (vJadwalId,true,now(),vMaxAntrian,0);
                                      END IF;
                          
                                      IF (NEW.is_konsulpoli = TRUE) THEN
                                          IF (isBpjs = TRUE ) THEN
                                              SELECT konsulpoli_id INTO idKonsulPoli FROM konsulpoli_t  WHERE antrian_id = vAntrianId;
                                                  INSERT INTO stokkuotadokter_t(konsulpoli_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online, kuota_out_bpjs)
                                                  VALUES (idKonsulPoli,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                                          ELSE
                                              SELECT konsulpoli_id INTO idKonsulPoli FROM konsulpoli_t  WHERE antrian_id = vAntrianId;
                                                  INSERT INTO stokkuotadokter_t(konsulpoli_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online, kuota_out_nonbpjs)
                                                  VALUES (idKonsulPoli,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                                      END IF;
              --                               SELECT konsulpoli_id INTO idKonsulPoli FROM konsulpoli_t  WHERE antrian_id = vAntrianId;
              --                               INSERT INTO stokkuotadokter_t(konsulpoli_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
              --                               VALUES (idKonsulPoli,true,vJadwalId,vAsalStok,now(),1,NEW.is_online);
                                      ELSE
                                                                       IF(NEW.jenisantrian_id = 312)
                                                                       THEN
                                          IF (isBpjs = TRUE ) THEN
                                              INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                                  VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                                          ELSE 
                                              INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                                  VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                                                                        END IF;
                                      END IF;
              --                               INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
              --                               VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online);
                                      END IF;
              
                                      RETURN NEW;
                              ELSIF (TG_OP = 'UPDATE') THEN
                                      IF(NEW.status_antrian = 4) THEN
                                              RETURN NEW;
                                      END IF;
                                     
                                      IF (NEW.jenisantrian_id = '312' and OLD.jadwaldokter_id <> NEW.jadwaldokter_id) then
                                              IF (NEW.is_online) then
            --                                    Get Total Stok Dokter sebelumnya
                                                     SELECT stokkuotadokter_id
                                                  INTO vOldAsalStokOnline
                                                  FROM stokkuotadokter_t
                                                  WHERE jadwaldokter_id = OLD.jadwaldokter_id AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0 AND is_online = TRUE;
                                                 
           --                                     Get Total Stok Dokter Online sesudahnya                                  
                                                  SELECT stokkuotadokter_id
                                                  INTO vAsalStokOnline
                                                  FROM stokkuotadokter_t
                                                  WHERE jadwaldokter_id = vJadwalId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0 AND is_online = TRUE;
                                             
                                                  IF (isBpjs = TRUE) then
       --                                             Kembaliin stok kuota dokter sebelumnya
                                                         INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldoktertambahan_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                                          VALUES (vAntrianId,true,OLD.jadwaldokter_id,vOldAsalStokOnline,now(),-1,NEW.is_online,-1);
       --                                             Kurangin stok kuota dokter yang baru
                                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                                          VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online,1);
                                                  ELSE
       --                                             Kembaliin stok kuota dokter sebelumnya
                                                         INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldoktertambahan_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                                          VALUES (vAntrianId,true,OLD.jadwaldokter_id,vOldAsalStokOnline,now(),-1,NEW.is_online,-1);
                                                         
       --                                             Kurangin stok kuota dokter yang baru
                                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                                          VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online,1);
                                                  END IF;
              --                                       INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
              --                                       VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online);
                                             ELSE
            --                                    Get Total Stok Dokter offline sebelumnya
                                                     SELECT stokkuotadokter_id
                                                  INTO vOldAsalStok
                                                  FROM stokkuotadokter_t
                                                  WHERE jadwaldokter_id = OLD.jadwaldokter_id AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0;
                                                 
                                                  IF (isBpjs = TRUE ) then
       --		                                   	   Kembaliin Stok Kuota Dokter offline sebelumnya
                                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                                          VALUES (vAntrianId,true, OLD.jadwaldokter_id,vOldAsalStok,now(),-1,NEW.is_online,-1);
                                                      
       --		                                       Kurangin stok kuota dokter yang baru
                                                        INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                                          VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                                                  ELSE 
       --		                                   	   Kembaliin Stok Kuota Dokter offline sebelumnya
                                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                                          VALUES (vAntrianId,true, OLD.jadwaldokter_id,vOldAsalStok,now(),-1,NEW.is_online,-1);
                                                         
       --		                                   	   Kurangin Stok kuota dokter yang baru
                                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                                          VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                                                 END IF;
                                            END IF;
                                      END IF;
              
                                      RETURN NEW;
                              END IF;
                      ELSE
                              SELECT stokkuotadokter_id
                              INTO vAsalStok
                              FROM stokkuotadokter_t
                              WHERE jadwalbukapoli_id = vJadwalPoliId AND is_online = FALSE AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0;
                      
                              IF (TG_OP = 'INSERT') THEN
                                      IF (NEW.jenisantrian_id = '177') THEN
                                              SELECT stokkuotadokter_id
                                              INTO vAsalStokOnline
                                              FROM stokkuotadokter_t
                                              WHERE jadwalbukapoli_id = vJadwalPoliId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0 AND is_online = TRUE;
                                              
                                              IF (NEW.is_online) THEN
                                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwalbukapoli_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
                                                      VALUES (vAntrianId,true,vJadwalPoliId,vAsalStokOnline,now(),1,NEW.is_online);
                                              END IF;
                                              RETURN NEW;
                                      END IF;
              
                                      IF (vAsalStok IS NULL) THEN
                                              SELECT maxantrian_poli
                                              INTO vMaxAntrian
                                              FROM jadwalbukapoli_m
                                              WHERE jadwalbukapoli_id = vJadwalPoliId;
              
                                              INSERT INTO stokkuotadokter_t (jadwalbukapoli_id, flag, tgltransaksi_in, kuota_in, kuota_out)
                                              VALUES (vJadwalPoliId, true, now(), vMaxAntrian, 0);
                                      ELSE
                                              INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwalbukapoli_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
                                              VALUES (vAntrianId,false,vJadwalPoliId,vAsalStok,now(),1,NEW.is_online);
                                      END IF;
              
                                      RETURN NEW;
                              ELSIF (TG_OP = 'UPDATE') THEN
                                      IF(NEW.status_antrian = 4) THEN
                                              RETURN NEW;
                                      END IF;
                                      RETURN NEW;
                              END IF;
                      END IF;
              END\$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230926_074830_migrate_GLBJ268_upd_stokkuotadokter_from_antrian_try cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_074830_migrate_GLBJ268_upd_stokkuotadokter_from_antrian_try cannot be reverted.\n";

        return false;
    }
    */
}
