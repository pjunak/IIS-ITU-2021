SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `ote_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `ote_db`;

DROP TABLE IF EXISTS `firma`;
CREATE TABLE `firma` (
  `rut_id` int(11) NOT NULL,
  `ean` int(11) DEFAULT NULL,
  `nazev` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `ic` int(11) NOT NULL,
  `dic` int(11) DEFAULT NULL,
  `web` varchar(64) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_czech_ci DEFAULT NULL,
  `datum_vytvoreni` date NOT NULL,
  `ulice` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `cislo_p` varchar(8) COLLATE utf8_czech_ci DEFAULT NULL,
  `cislo_o` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `obec` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `psc` int(11) NOT NULL,
  `predcisli` int(11) DEFAULT NULL,
  `cislo_uctu` int(32) NOT NULL,
  `kod_banky` enum('0100','0300','0600','0710','0800','2010','2070','2100','2250','2260','2600','2700','3030','3040','3050','3500','4000','4300','5500','5800','6000','6100','6210','6300','6700','6800','7950','7960','7970','7980','7990','8060','8090','8211') COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `firma` (`rut_id`, `ean`, `nazev`, `ic`, `dic`, `web`, `email`, `datum_vytvoreni`, `ulice`, `cislo_p`, `cislo_o`, `obec`, `psc`, `predcisli`, `cislo_uctu`, `kod_banky`) VALUES(123456789, 123456789, 'Firma TEST s.r.o.', 123456, NULL, 'http://test.com', 'test@domena.cz', '2021-11-21', 'Testová', '1024', '8', 'Testov', 12345, NULL, 123456789, '0100');

DROP TABLE IF EXISTS `osoba`;
CREATE TABLE `osoba` (
  `id` int(11) NOT NULL,
  `id_ucastnika` int(11) NOT NULL,
  `typ_osoby` enum('disponent','urednik','reditel') COLLATE utf8_czech_ci NOT NULL,
  `jmeno` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `telefon` int(11) NOT NULL,
  `email` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `heslo` bigint(20) NOT NULL,
  `kancelar` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `pozice` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `plat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(1, 123, 'disponent', 'Testovič', 'Test', 987654321, 'disponent@domena.cz', 123, NULL, NULL, NULL);
INSERT INTO `osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(2, 2, 'urednik', 'Úředník', 'Přísný', 456789123, 'urednik@domena.cz', 321, '1.20A', 'vedoucí sekce HR', 37000);

DROP TABLE IF EXISTS `pozadavek`;
CREATE TABLE `pozadavek` (
  `id` int(11) NOT NULL,
  `id_osoby` int(11) NOT NULL,
  `datum_vytvoreni` date NOT NULL,
  `datum_uzavreni` date NOT NULL,
  `predmet` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `status` enum('podan','vyrizen','uzavren') COLLATE utf8_czech_ci NOT NULL,
  `obsah_pozadavku` text COLLATE utf8_czech_ci NOT NULL,
  `odpoved` text COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `pozadavek` (`id`, `id_osoby`, `datum_vytvoreni`, `datum_uzavreni`, `predmet`, `status`, `obsah_pozadavku`, `odpoved`) VALUES(1, 1, '2021-11-21', '2021-11-24', 'Věc: uzavření smlouvy', 'vyrizen', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis bibendum, lectus ut viverra rhoncus, dolor nunc faucibus libero, eget facilisis enim ipsum id lacus. Maecenas sollicitudin. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Aenean id metus id velit ullamcorper pulvinar. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.', 'Sed elit dui, pellentesque a, faucibus vel, interdum nec, diam. Etiam bibendum elit eget erat. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut tempus purus at lorem. Etiam commodo dui eget wisi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam posuere lacus quis dolor. Aenean vel massa quis mauris vehicula lacinia.');

DROP TABLE IF EXISTS `vykaz`;
CREATE TABLE `vykaz` (
  `id` int(11) NOT NULL,
  `od` date NOT NULL,
  `do` date NOT NULL,
  `datum_cas_zadani_vykazu` datetime NOT NULL,
  `svorkova_vyroba_elektriny` int(11) NOT NULL,
  `vlastni_spotreba_elektriny` int(11) NOT NULL,
  `celkova_konecna_spotreba` int(11) NOT NULL,
  `spotreba_z_toho_lokalni` int(11) NOT NULL,
  `spotreba_z_toho_odber` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `vykaz` (`id`, `od`, `do`, `datum_cas_zadani_vykazu`, `svorkova_vyroba_elektriny`, `vlastni_spotreba_elektriny`, `celkova_konecna_spotreba`, `spotreba_z_toho_lokalni`, `spotreba_z_toho_odber`) VALUES(1, '2021-10-01', '2021-10-31', '2021-11-01 08:15:23', 23, 10, 8, 2, 6);

DROP TABLE IF EXISTS `vyrobna`;
CREATE TABLE `vyrobna` (
  `id` int(11) NOT NULL,
  `id_vyrobniho_zdroje` int(11) NOT NULL,
  `id_site` int(11) NOT NULL,
  `kratky_nazev` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `ulice` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `cislo_p` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `cislo_o` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `kraj` int(11) NOT NULL,
  `okres` int(11) NOT NULL,
  `obec` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `psc` int(11) NOT NULL,
  `parcela` varchar(16) COLLATE utf8_czech_ci NOT NULL,
  `gps_n` decimal(15,5) NOT NULL,
  `gps_e` decimal(15,5) NOT NULL,
  `druh_vyrobny` enum('slunecni','slunecni_budova','vodni','precerpavaci','jaderna','plyn','geotermalni','vetrna','biomasa') COLLATE utf8_czech_ci NOT NULL,
  `vyrobni_EAN` int(11) NOT NULL,
  `EAN_vyrobny` int(11) NOT NULL,
  `vykon_zdroje` int(11) NOT NULL,
  `napetova_hladina` enum('0,4','3','6','10','22','35','110','220','400','ostatni') COLLATE utf8_czech_ci NOT NULL,
  `zpusob_pripojeni` enum('primo','neprimo','ostrovni_vyroba') COLLATE utf8_czech_ci NOT NULL,
  `vykaz_za_opm` enum('ano','ne') COLLATE utf8_czech_ci NOT NULL,
  `druh_podpory` enum('bonus_rocni','bonus_hodinovy','povinny_vykup','bez_podpory') COLLATE utf8_czech_ci NOT NULL,
  `datum_prvniho_pripojeni` date NOT NULL,
  `datum_uvedeni_do_provozu` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `vyrobna` (`id`, `id_vyrobniho_zdroje`, `id_site`, `kratky_nazev`, `ulice`, `cislo_p`, `cislo_o`, `kraj`, `okres`, `obec`, `psc`, `parcela`, `gps_n`, `gps_e`, `druh_vyrobny`, `vyrobni_EAN`, `EAN_vyrobny`, `vykon_zdroje`, `napetova_hladina`, `zpusob_pripojeni`, `vykaz_za_opm`, `druh_podpory`, `datum_prvniho_pripojeni`, `datum_uvedeni_do_provozu`) VALUES(1, 1, 1, 'výrobna 1', 'Rúžová', '1024', '8', 1, 1, 'Olomouc', 77900, '235/15', '18.45550', '27.00530', 'slunecni', 1001, 2002, 35, '110', 'primo', 'ano', 'bonus_rocni', '2021-09-01', '2021-09-02');


ALTER TABLE `firma`
  ADD PRIMARY KEY (`rut_id`);

ALTER TABLE `osoba`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pozadavek`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vykaz`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vyrobna`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `firma`
  MODIFY `rut_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234567892;

ALTER TABLE `osoba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `pozadavek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `vykaz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `vyrobna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
