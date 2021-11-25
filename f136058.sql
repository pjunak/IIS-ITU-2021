-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Čtv 25. lis 2021, 14:52
-- Verze serveru: 10.4.21-MariaDB
-- Verze PHP: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `f136058`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_firma`
--

DROP TABLE IF EXISTS `iis_firma`;
CREATE TABLE `iis_firma` (
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

--
-- Vypisuji data pro tabulku `iis_firma`
--

INSERT INTO `iis_firma` (`rut_id`, `ean`, `nazev`, `ic`, `dic`, `web`, `email`, `datum_vytvoreni`, `ulice`, `cislo_p`, `cislo_o`, `obec`, `psc`, `predcisli`, `cislo_uctu`, `kod_banky`) VALUES(123456789, 123456789, 'Firma TEST s.r.o.', 123456, NULL, 'http://test.com', 'test@domena.cz', '2021-11-21', 'Testová', '1024', '8', 'Testov', 12345, NULL, 123456789, '0100');
INSERT INTO `iis_firma` (`rut_id`, `ean`, `nazev`, `ic`, `dic`, `web`, `email`, `datum_vytvoreni`, `ulice`, `cislo_p`, `cislo_o`, `obec`, `psc`, `predcisli`, `cislo_uctu`, `kod_banky`) VALUES(234567892, 1234659, 'Ravoz spol s.r.o.', 111, 2222, 'web.cz', 'mail@mail.cz', '2021-11-26', 'ulice', '1', '2', 'Obec', 12345, 100, 100025242, '0100');

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_firma_osoba`
--

DROP TABLE IF EXISTS `iis_firma_osoba`;
CREATE TABLE `iis_firma_osoba` (
  `id` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `osoba` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_firma_osoba`
--

INSERT INTO `iis_firma_osoba` (`id`, `firma`, `osoba`) VALUES(1, 123456789, 1);
INSERT INTO `iis_firma_osoba` (`id`, `firma`, `osoba`) VALUES(2, 234567892, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_osoba`
--

DROP TABLE IF EXISTS `iis_osoba`;
CREATE TABLE `iis_osoba` (
  `id` int(11) NOT NULL,
  `id_ucastnika` int(11) NOT NULL,
  `typ_osoby` enum('disponent','urednik','reditel') COLLATE utf8_czech_ci NOT NULL,
  `jmeno` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `telefon` int(11) NOT NULL,
  `email` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `login` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `heslo` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `kancelar` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `pozice` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `plat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_osoba`
--

INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(1, 123, 'disponent', 'Testovič', 'Test', 987654321, 'disponent@domena.cz', 'disponent', '$2y$10$s7g4/iWFBXAYAv6pnF3l3OqElI728GdeCtQpDH/bQgskYO6IjDCt2', NULL, NULL, NULL);
INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(2, 2, 'urednik', 'Úředník', 'Přísný', 456789123, 'urednik@domena.cz', 'urednik', '$2y$10$nKEHyQbiqip.jqYYhU7dUeeFgpsH0VG5Wa1y4ifCHzJ8Sr.XasCQq', '1.20A', 'vedoucí sekce HR', 37000);

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_pozadavek`
--

DROP TABLE IF EXISTS `iis_pozadavek`;
CREATE TABLE `iis_pozadavek` (
  `id` int(11) NOT NULL,
  `id_osoby` int(11) NOT NULL,
  `datum_vytvoreni` date NOT NULL,
  `datum_uzavreni` date NOT NULL,
  `predmet` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `status` enum('podan','vyrizen','uzavren') COLLATE utf8_czech_ci NOT NULL,
  `obsah_pozadavku` text COLLATE utf8_czech_ci NOT NULL,
  `odpoved` text COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_pozadavek`
--

INSERT INTO `iis_pozadavek` (`id`, `id_osoby`, `datum_vytvoreni`, `datum_uzavreni`, `predmet`, `status`, `obsah_pozadavku`, `odpoved`) VALUES(1, 1, '2021-11-21', '2021-11-24', 'Věc: uzavření smlouvy', 'podan', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis bibendum, lectus ut viverra rhoncus, dolor nunc faucibus libero, eget facilisis enim ipsum id lacus. Maecenas sollicitudin. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Aenean id metus id velit ullamcorper pulvinar. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.', 'Sed elit dui, pellentesque a, faucibus vel, interdum nec, diam. Etiam bibendum elit eget erat. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut tempus purus at lorem. Etiam commodo dui eget wisi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam posuere lacus quis dolor. Aenean vel massa quis mauris vehicula lacinia.');

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_vykaz`
--

DROP TABLE IF EXISTS `iis_vykaz`;
CREATE TABLE `iis_vykaz` (
  `id` int(11) NOT NULL,
  `id_osoby` int(11) NOT NULL,
  `id_vyrobny` int(11) NOT NULL,
  `od` date NOT NULL,
  `do` date NOT NULL,
  `datum_cas_zadani_vykazu` datetime NOT NULL,
  `svorkova_vyroba_elektriny` int(11) NOT NULL,
  `vlastni_spotreba_elektriny` int(11) NOT NULL,
  `celkova_konecna_spotreba` int(11) NOT NULL,
  `spotreba_z_toho_lokalni` int(11) NOT NULL,
  `spotreba_z_toho_odber` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_vykaz`
--

INSERT INTO `iis_vykaz` (`id`, `id_osoby`, `id_vyrobny`, `od`, `do`, `datum_cas_zadani_vykazu`, `svorkova_vyroba_elektriny`, `vlastni_spotreba_elektriny`, `celkova_konecna_spotreba`, `spotreba_z_toho_lokalni`, `spotreba_z_toho_odber`) VALUES(1, 1, 1, '2021-10-01', '2021-10-31', '2021-11-01 08:15:23', 23, 10, 8, 2, 6);

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_vyrobna`
--

DROP TABLE IF EXISTS `iis_vyrobna`;
CREATE TABLE `iis_vyrobna` (
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

--
-- Vypisuji data pro tabulku `iis_vyrobna`
--

INSERT INTO `iis_vyrobna` (`id`, `id_vyrobniho_zdroje`, `id_site`, `kratky_nazev`, `ulice`, `cislo_p`, `cislo_o`, `kraj`, `okres`, `obec`, `psc`, `parcela`, `gps_n`, `gps_e`, `druh_vyrobny`, `vyrobni_EAN`, `EAN_vyrobny`, `vykon_zdroje`, `napetova_hladina`, `zpusob_pripojeni`, `vykaz_za_opm`, `druh_podpory`, `datum_prvniho_pripojeni`, `datum_uvedeni_do_provozu`) VALUES(1, 1, 1, 'výrobna 1', 'Rúžová', '1024', '8', 1, 1, 'Olomouc', 77900, '235/15', '18.45550', '27.00530', 'slunecni', 1001, 2002, 35, '110', 'primo', 'ano', 'bonus_rocni', '2021-09-01', '2021-09-02');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `iis_firma`
--
ALTER TABLE `iis_firma`
  ADD PRIMARY KEY (`rut_id`);

--
-- Indexy pro tabulku `iis_firma_osoba`
--
ALTER TABLE `iis_firma_osoba`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `iis_osoba`
--
ALTER TABLE `iis_osoba`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `iis_pozadavek`
--
ALTER TABLE `iis_pozadavek`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `iis_vykaz`
--
ALTER TABLE `iis_vykaz`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `iis_vyrobna`
--
ALTER TABLE `iis_vyrobna`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `iis_firma`
--
ALTER TABLE `iis_firma`
  MODIFY `rut_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234567893;

--
-- AUTO_INCREMENT pro tabulku `iis_firma_osoba`
--
ALTER TABLE `iis_firma_osoba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `iis_osoba`
--
ALTER TABLE `iis_osoba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `iis_pozadavek`
--
ALTER TABLE `iis_pozadavek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `iis_vykaz`
--
ALTER TABLE `iis_vykaz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `iis_vyrobna`
--
ALTER TABLE `iis_vyrobna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
