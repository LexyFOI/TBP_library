DROP TABLE IF EXISTS tip_korisnika CASCADE;
DROP TABLE IF EXISTS korisnik CASCADE;
DROP TABLE IF EXISTS knjige CASCADE;
DROP TABLE IF EXISTS status CASCADE;
DROP TABLE IF EXISTS primjerak CASCADE;
DROP TABLE IF EXISTS rezervacija CASCADE;
DROP TABLE IF EXISTS posudba CASCADE;

-- -----------------------------------------------------
-- Table  tip_korisnika 
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS tip_korisnika(
   tip_id INT NOT NULL,
   naziv VARCHAR(20) NOT NULL,
   opis_prava VARCHAR(45) NOT NULL,
  PRIMARY KEY ( tip_id ) );

-- -----------------------------------------------------
-- Table mjesto
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS mjesto (
  mjesto_id VARCHAR(5) NOT NULL ,
  naziv VARCHAR(45) NULL ,
  PRIMARY KEY (mjesto_id) );
  
-- -----------------------------------------------------
-- Table  korisnik 
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS korisnik(
   kor_id SERIAL INT NOT NULL,
   tip_id INT NOT NULL,
   kor_id VARCHAR(45) NOT NULL,
   lozinka VARCHAR(45) NOT NULL,
   clanarina_do TIMESTAMP NOT NULL DEFAULT,
   ime VARCHAR(45) NOT NULL,
   prezime VARCHAR(45) NOT NULL,
   OIB VARCHAR(45) NOT NULL,
   email VARCHAR(45) NULL,
   adresa VARCHAR(45) NOT NULL,
   mjesto VARCHAR(5) NOT NULL ,
  PRIMARY KEY (kor_id),
  CONSTRAINT tip_id 
    FOREIGN KEY (tip_id)
    REFERENCES tip_korisnika (tip_id)
  CONSTRAINT mjesto_id 
    FOREIGN KEY (mjesto_id)
    REFERENCES mjesto (mjesto_id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table  knjige 
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS knjige(
   knjiga_id SERIAL INT NOT NULL,
   broj_primjeraka INT NOT NULL,
   naslov VARCHAR(45) NOT NULL,
   pisac VARCHAR(45) NOT NULL,
   sazetak VARCHAR(500) NOT NULL,
   kritike VARCHAR(500) NULL,
  PRIMARY KEY ( knjiga_id ) );


-- -----------------------------------------------------
-- Table  status 
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS  status  (
   status_id  INT NOT NULL,
   naziv  VARCHAR(20) NOT NULL,
  PRIMARY KEY ( status_id ) );


-- -----------------------------------------------------
-- Table  primjerak 
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS  primjerak  (
   primjerak_id SERIAL INT NOT NULL,
   knjiga_id  INT NOT NULL,
   status_id  INT ZEROFILL NOT NULL,
   izdavac  VARCHAR(45) NOT NULL,
   god_izdanja  VARCHAR(45) NOT NULL,
   broj_rezervacija INT ZEROFILL NOT NULL,
  PRIMARY KEY ( primjerak_id ),
  CONSTRAINT  knjiga_id 
    FOREIGN KEY ( knjiga_id )
    REFERENCES  knjige  ( knjiga_id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT  status_id 
    FOREIGN KEY ( status_id )
    REFERENCES  status  ( status_id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table  rezervacija 
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS  rezervacija  (
   rezervacija_id SERIAL INT NOT NULL,
   kor_id  INT NOT NULL,
   primjerak_id  INT NOT NULL,
   datum  TIMESTAMP NOT NULL,
   aktivna  INT NOT NULL,
  PRIMARY KEY ( rezervacija_id ),
  CONSTRAINT  kor_id 
    FOREIGN KEY ( kor_id )
    REFERENCES  korisnik  ( kor_id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT  primjerak_id 
    FOREIGN KEY ( primjerak_id )
    REFERENCES  primjerak  ( primjerak_id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table  posudba
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS  posudba (
  posudba_id SERIAL INT NOT NULL,
  primjerak_id INT NOT NULL,
  kor_ime INT NOT NULL,
  od_datum TIMESTAMP NOT NULL,
  do_datum TIMESTAMP NOT NULL,
  aktivna  INT NOT NULL,
  produzeno INT ZEROFILL NOT NULL COMMENT 'Max = 2',
  PRIMARY KEY (posudba_id),
  CONSTRAINT kor_id
    FOREIGN KEY (kor_ime)
    REFERENCES  korisnik (kor_id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT primjerak_id
    FOREIGN KEY (primjerak_id)
    REFERENCES  primjerak (primjerak_id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
	
-- -----------------------------------------------------
-- TRIGGERS
-- ----------------------------------------------------


-- -----------------------------------------------------
-- KORISNIK - PROVJERA MAIL-A
-- ----------------------------------------------------

CREATE OR REPLACE FUNCTION korisnik() RETURNS TRIGGER AS $$
DECLARE

BEGIN
    CONSTRAINT proper_email CHECK (email ~* '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$');
	IF (!propre_email) THEN
		RAISE NOTICE 'Navedeni e-mail nije ispravan: %',email;
	END IF;
END



$$ LANGUAGE plpgsql;
	
DROP TRIGGER IF EXISTS provjera_maila ON artikl;

CREATE TRIGGER provjera_maila
BEFORE INSERT OR UPDATE ON korisnik
FOR EACH ROW EXECUTE PROCEDURE korisnik();

-- -----------------------------------------------------
-- REZERVACIJE >> STANJE PRIMJERKA, ++POSUDBA
-- ----------------------------------------------------

CREATE OR REPLACE FUNCTION rezervacija() RETURNS TRIGGER AS $$
DECLARE
--na insert unosim u rezervacije i mjenjam stanje na primjerku
--na update unosim u posudbe i mjenjam stanje na primjerku
BEGIN
IF(TG_OP = 'INSERT') THEN
	SELECT broj_rezervacija INTO br_rez FROM rezervacija WHERE rezervacija.primjerak_id = primjerak.primjerak_id;
	IF(br_rez = 0)THEN
		UPDATE primjerak
		SET status = '2' 
		WHERE primjerak.primjerak_id = NEW.primjerak_id;
		UPDATE primjerak
		SET NEW.broj_rezervacija = br_rez + 1
		WHERE primjerak.primjerak_id = NEW.primjerak_id;
	ELSIF(br_erz > 0)THEN
		UPDATE primjerak
		SET NEW.broj_rezervacija = br_rez + 1
		WHERE primjerak.primjerak_id = NEW.primjerak_id;
ELSIF(TG_OP = 'UPDATE') THEN
	UPDATE primjerak
	SET status = '1'
	WHERE primjerak.primjerak_id = OLD.primjerak_id;
	INSERT INTO posudba(primjerak_id, kor_ime, od_datum, do_datum, aktivna, produzeno)
	VALUES(primjerak_id, kor_ime, now(), now() + integer '14',1 ,0) 
	WHERE NEW.primjerak_id = rezervacija.primjerak_id;
ELSIF(TG_OP = 'DELETE') THEN
	UPDATE primjerak
	SET status = '0'
	WHERE primjerak.primjerak_id = OLD.primjerak_id;
END IF;
RETURN NEW;
END;

$$ LANGUAGE plpgsql;
	
DROP TRIGGER IF EXISTS izmjene_rezervacija ON rezervacija;

CREATE TRIGGER izmjene_rezervacija
AFTER INSERT OR UPDATE ON rezervacija
BEFORE DELETE ON rezervacija
FOR EACH ROW EXECUTE PROCEDURE rezervacija();

-- -----------------------------------------------------
-- POSUDBA >> PRODUZENJE KNJIGE
-- ----------------------------------------------------

CREATE OR REPLACE FUNCTION produzi() RETURNS TRIGGER AS $$
DECLARE
posudba_stara INTEGER;
--moderator moze produziti posudbu max 2 puta po 7 dana
BEGIN
IF(TG_OP = 'UPDATE') THEN
	SELECT posudba.produzeno INTO posudba_stara FROM posudba WHERE posudba.primjerak_id = OLD.primjerak_id;
	IF(produzeno < 2) THEN
		UPDATE posudba
		SET produzeno = posudba_stara + 1
		WHERE posudba.primjerak_id = OLD.primjerak_id;
	ELSIF(produzeno = 2) THEN
		RAISE NOTICE 'Knjiga je veæ produžena 2 puta, mora se vratiti!';
	END IF
END IF;
RETURN NEW;
END;

$$ LANGUAGE plpgsql;
	
DROP TRIGGER IF EXISTS produzi_posudbu ON posudba;

CREATE TRIGGER produzi_posudbu
BEFORE UPDATE ON posudba
FOR EACH ROW EXECUTE PROCEDURE produzi();
-- -----------------------------------------------------
-- Insert data
-- -----------------------------------------------------
INSERT INTO tip_korisnika VALUES
('0','neprijavljeni korisnik','pregled popisa knjiga'),
('1','clan knjižnice','rezervacija i pregled posudbi'),
('2','moderatot(knjižnièar)','upis knjiga i odobrenje posudbe'),
('3','admin','dodavanje novih korisnika i ureðivanje podataka o korisnicima');

INSERT INTO korisnik VALUES
(DEFAULT,'3','apolak','12-12-2018','Aleksandra','Polak','34567891234','apolak@gmail.com','A. Starèeviæa 6, 10000 Zagreb'),
(DEFAULT,'2','atomic','29-09-2017','Antonijo','Tomiæ','23456789123','atomic@gmail.com','Tea Ludviga 10 0001 Zagreb'),
(DEFAULT,'1','korisnik','21-08-2017','Ema','Watson','12345678912','ewatson@gmail.com','Marka Maruliæa 6, 10000 Zagreb'),
(DEFAULT,'1','korisnik2','21-08-2017','Ela','Watson','12345678912','ewatson2@gmail.com','Marka Maruliæa 6, 10000 Zagreb');

INSERT INTO posudba VALUES --posudba_id, primjerak_id, kor_ime, od_datum, do_datum, aktivna, produzeno
(DEFAULT,'1','2','01-08-2017','24-08-2017','2'), 
(DEFAULT,'2','2','10-08-2017','29-08-2017','1'),
(DEFAULT,'3','2','17-08-2017','31-08-2017','0');

INSERT INTO rezervacija VALUES --kor_id, primjerak_id, dat, aktivna(0ili1)
(DEFAULT, 2, 0, '03-08-2017',1),
(DEFAULT, 2, 4, '04-08-2017',1),
(DEFAULT, 3, 5, '06-08-2017',1);

INSERT INTO knjiga VALUES 
(1, --broj primjeraka
'Zen i umjetnost održavanja motocikala',  -- naslov
'Robert M. Pirsig', -- pisac
'Trebate li malo više poticaja u svom životu? Proèitajte ovaj filozofski roman i Robert M. Pirsig pomoæi æe vam shvatiti koliko je zapravo važno da vam je zaista stalo do onoga što radite. Drugim rijeèima, ako popravljate motocikl, onda ga zaista popravite. Nemojte usput slušati glazbu ili paralelno raditi još nešto. Uèinite to što morate i uèinite to s ponosom.', --sažetak
NULL), --komentar
(1,
'Maèja kolijevka',
'Kurt Vonnegut',
'Od svih Vonnegutovih knjiga koje ste proèitali i koje æete tek proèitati, ova postavlja možda najviše pitanja, i to na sjajan naèin. Jonah, pripovjedaè, želi napisati roman o izumitelju atomske bombe, dr. Franku Hoenikkeru. Ova knjiga æe vas natjerati da se zapitate bi li potraga za znanjem trebala imati granica ili ne. Potaknut æe vas i da razmišljate o moæi oružja, kao i o tome da èak i najkompetentniji ljudi koristeæi ga èine pogreške. Plus, uza sva ta znanstvena propitivanja dolazi i istraživanje religije i njene uzaludnosti. Stvarno.',
NULL),
(2,
'Voljena',
'Toni Morrison',
'Uzbudljivi roman velike amerièke spisateljice Toni Morrison prati Sethe, osloboðenu od ropstva ali nikad zapravo sposobnu da odagna ružna sjeæanja. Rijeè je o beskompromisnom pogledu u užase ropstva, no u kojem æete, zaèuðujuæe, pronaæi i nadu.',
NULL),

(2,
'Silvija Plat',
'Stakleno Zvono',
'Opisuje život devojke koja kao nagradu dobija put u Njujork. Ima priliku da mesec dana provede u prestižnom modnom èasopisu i iz materijalnog obilja i glamura na kraju mora da se ponovo vrati u svoj rodni grad i prihvati svoj obièan život od kog je na kratko pobegla. Vraæena u realni svet, ona zapada u depresiju, a potom dospeva u ludnicu. Ukoliko oseæate da ste dovoljno hrabri da zajedno sa junakinjom ovog romana otkrijete dubine ljudskog uma i duše, poènite sa èitanjem.'.
NULL);


INSERT INTO primjerak VALUES --knjiga, status, izdavac,god izdanja
(DEFAULT,'0','0','Profil','2001',1), 
(DEFAULT,'1','1','Algoritam','2001',0),  
(DEFAULT,'2','1','Znanje','2002',0),  
(DEFAULT,'2','1','Znanje','2000',0),
(DEFAULT, '3','0','Mozaik knjiga','1961',1),
(DEFAULT, '3','0','Mozaik knjiga','1961',1); 

INSERT INTO status VALUES
('0','slobodna'),
('1','posuðena'),
('2','rezervirana');