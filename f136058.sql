--
--	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
--	Datum: 5.12.2021
--	Autor: Kristián Heřman, xherma33
--	Autor: Dalibor Čásek, xcasek01
--	Autor: Petr Junák, xjunak01
--

-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Čtv 25. lis 2021, 19:03
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
  `ean` int(18) DEFAULT NULL,
  `nazev` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `ic` int(11) DEFAULT NULL,
  `dic` varchar(11) DEFAULT NULL,
  `web` varchar(64) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_czech_ci DEFAULT NULL,
  `datum_vytvoreni` date NOT NULL,
  `ulice` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `cislo_p` varchar(8) COLLATE utf8_czech_ci DEFAULT NULL,
  `cislo_o` varchar(8) COLLATE utf8_czech_ci DEFAULT NULL,
  `obec` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `psc` int(11) DEFAULT NULL,
  `predcisli` int(11) DEFAULT NULL,
  `cislo_uctu` int(32) NOT NULL,
  `kod_banky` varchar(4) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_firma`
--

INSERT INTO `iis_firma` (`rut_id`, `ean`, `nazev`, `ic`, `dic`, `web`, `email`, `datum_vytvoreni`, `ulice`, `cislo_p`, `cislo_o`, `obec`, `psc`, `predcisli`, `cislo_uctu`, `kod_banky`) VALUES(123456789, 88163, 'Firma TEST s.r.o. 1', 12345678, NULL, 'http://test.com', 'test@domena.cz', '2021-11-21', 'Testová', '1024', '8', 'Testov', 12345, NULL, 123456789, '0100');
INSERT INTO `iis_firma` (`rut_id`, `ean`, `nazev`, `ic`, `dic`, `web`, `email`, `datum_vytvoreni`, `ulice`, `cislo_p`, `cislo_o`, `obec`, `psc`, `predcisli`, `cislo_uctu`, `kod_banky`) VALUES(234567892, 12349, 'Ravoz spol s.r.o. 2', 62181825, 62181825, 'web.cz', 'mail@mail.cz', '2021-11-26', 'U Višně', '1', NULL, 'Višňová', 12345, 100, 100025242, '2260');
INSERT INTO `iis_firma` (`rut_id`, `ean`, `nazev`, `ic`, `dic`, `web`, `email`, `datum_vytvoreni`, `ulice`, `cislo_p`, `cislo_o`, `obec`, `psc`, `predcisli`, `cislo_uctu`, `kod_banky`) VALUES(518468138, 84687, 'SolarBaron a.s. 3', 55118844, 55118844, 'http://grukesh.tk', 'xcasek01@vutbr.cz', '2018-02-15', 'Smetanova', '985', '43', 'Olomouc', 77900, NULL, 562874695, '2010');


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
INSERT INTO `iis_firma_osoba` (`id`, `firma`, `osoba`) VALUES(2, 234567892, 1);
INSERT INTO `iis_firma_osoba` (`id`, `firma`, `osoba`) VALUES(3, 518468138, 5);
INSERT INTO `iis_firma_osoba` (`id`, `firma`, `osoba`) VALUES(4, 518468138, 6);

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_osoba`
--

DROP TABLE IF EXISTS `iis_osoba`;
CREATE TABLE `iis_osoba` (
  `id` int(11) NOT NULL,
  `id_ucastnika` int(11) DEFAULT NULL UNIQUE,
  `typ_osoby` enum('disponent','urednik','reditel') COLLATE utf8_czech_ci NOT NULL,
  `jmeno` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `telefon` int(11) DEFAULT NULL,
  `email` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `login` varchar(64) COLLATE utf8_czech_ci NOT NULL UNIQUE,
  `heslo` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `kancelar` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `pozice` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `plat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_osoba`
--

INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(1, 1230, 'disponent', 'Testovič', 'Test', 987654321, 'testovic@seznam.cz', 'disponent1', '$2y$10$s7g4/iWFBXAYAv6pnF3l3OqElI728GdeCtQpDH/bQgskYO6IjDCt2', NULL, NULL, NULL);
INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(5, 6185, 'disponent', 'Andrej', 'Babiš', 332584666, 'anton.bures@google.com', 'disponent2', '$2y$10$s7g4/iWFBXAYAv6pnF3l3OqElI728GdeCtQpDH/bQgskYO6IjDCt2', NULL, NULL, NULL);
INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(6, 4832, 'disponent', 'Ivan', 'Bartoš', 458693248, 'bartos@post.cz', 'disponent3', '$2y$10$s7g4/iWFBXAYAv6pnF3l3OqElI728GdeCtQpDH/bQgskYO6IjDCt2', NULL, NULL, NULL);
INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(3, 3482, 'urednik', 'Pavel', 'Novotný', 777777265, 'pavel.novotny@ote.cz', 'urednik1', '$2y$10$nKEHyQbiqip.jqYYhU7dUeeFgpsH0VG5Wa1y4ifCHzJ8Sr.XasCQq', '4.00C', 'technická podpora', 41200);
INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(2, 2000, 'urednik', 'Úředník', 'Přísný', 456789123, 'urednik.prisny@ote.cz', 'urednik2', '$2y$10$nKEHyQbiqip.jqYYhU7dUeeFgpsH0VG5Wa1y4ifCHzJ8Sr.XasCQq', '1.20A', 'vedoucí sekce HR', 37000);
INSERT INTO `iis_osoba` (`id`, `id_ucastnika`, `typ_osoby`, `jmeno`, `prijmeni`, `telefon`, `email`, `login`, `heslo`, `kancelar`, `pozice`, `plat`) VALUES(4, 1658, 'reditel', 'Alexander', 'Veliký', 775588446, 'alexander.veliky@ote.cz', 'reditel', '$2y$10$gDTIuzqXro9/Kn3xLZNjw.FqbRxtqVrqwPFDVsuAujn5fyeyR0uY.', '19.1B', 'Ředitel OTE', 69000);

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_pozadavek`
--

DROP TABLE IF EXISTS `iis_pozadavek`;
CREATE TABLE `iis_pozadavek` (
  `id` int(11) NOT NULL,
  `id_osoby` int(11) NOT NULL,
  `datum_vytvoreni` date NOT NULL,
  `datum_uzavreni` date DEFAULT NULL,
  `predmet` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `status` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `obsah_pozadavku` text COLLATE utf8_czech_ci NOT NULL,
  `odpoved` text COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_pozadavek`
--

INSERT INTO `iis_pozadavek` (`id`, `id_osoby`, `datum_vytvoreni`, `datum_uzavreni`, `predmet`, `status`, `obsah_pozadavku`, `odpoved`) VALUES(1, 1, '2021-11-21', '2021-11-23', 'Věc: uzavření smlouvy', 'vyrizen', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis bibendum, lectus ut viverra rhoncus, dolor nunc faucibus libero, eget facilisis enim ipsum id lacus. Maecenas sollicitudin. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Aenean id metus id velit ullamcorper pulvinar. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.', 'Běžte do háje');
INSERT INTO `iis_pozadavek` (`id`, `id_osoby`, `datum_vytvoreni`, `predmet`, `status`, `obsah_pozadavku`) VALUES(5, 5, '2021-11-21', 'Věc: uzavření další smlouvy', 'podan', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis bibendum, lectus ut viverra rhoncus, dolor nunc faucibus libero, eget facilisis enim ipsum id lacus. Maecenas sollicitudin. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Aenean id metus id velit ullamcorper pulvinar. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.');

-- --------------------------------------------------------

--
-- Struktura tabulky `iis_vykaz`
--

DROP TABLE IF EXISTS `iis_vykaz`;
CREATE TABLE `iis_vykaz` (
  `id` int(11) NOT NULL,
  `id_osoby` int(11) NOT NULL,
  `id_vyrobny` int(11) NOT NULL,
  `od` date DEFAULT NULL,
  `do` date DEFAULT NULL,
  `datum_cas_zadani_vykazu` datetime NOT NULL,
  `svorkova_vyroba_elektriny` int(11) NOT NULL,
  `vlastni_spotreba_elektriny` int(11) DEFAULT NULL,
  `celkova_konecna_spotreba` int(11) DEFAULT NULL,
  `spotreba_z_toho_lokalni` int(11) DEFAULT NULL,
  `spotreba_z_toho_odber` int(11) DEFAULT NULL
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
  `id_vyrobniho_zdroje` int(11) DEFAULT NULL,
  `id_site` int(11) DEFAULT NULL,
  `id_firmy` int(11) NOT NULL,
  `kratky_nazev` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `ulice` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `cislo_p` varchar(8) COLLATE utf8_czech_ci DEFAULT NULL,
  `cislo_o` varchar(8) COLLATE utf8_czech_ci DEFAULT NULL,
  `kraj` varchar(32) DEFAULT NULL,
  `okres` varchar(32) DEFAULT NULL,
  `obec` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `psc` int(11) DEFAULT NULL,
  `parcela` varchar(16) COLLATE utf8_czech_ci DEFAULT NULL,
  `gps_n` decimal(15,5) DEFAULT NULL,
  `gps_e` decimal(15,5) DEFAULT NULL,
  `druh_vyrobny` enum('sluneční','sluneční budova','vodní','přečerpávací','jaderná','plynová','geotermalní','větrná','biomasa') COLLATE utf8_czech_ci NOT NULL,
  `stav` enum('podano','neschvaleno','schvaleno') COLLATE utf8_czech_ci NOT NULL,
  `vyrobni_EAN` int(18) DEFAULT NULL,
  `EAN_vyrobny` int(18) DEFAULT NULL,
  `vykon_zdroje` int(11) DEFAULT NULL,
  `napetova_hladina` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `zpusob_pripojeni` enum('přímo','nepřímo','ostrovní výroba') COLLATE utf8_czech_ci NOT NULL,
  `vykaz_za_opm` enum('ano','ne') COLLATE utf8_czech_ci NOT NULL,
  `druh_podpory` enum('bonus roční','bonus hodinový','povinný nákup','bez podpory') COLLATE utf8_czech_ci NOT NULL,
  `datum_prvniho_pripojeni` date DEFAULT NULL,
  `datum_uvedeni_do_provozu` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `iis_vyrobna`
--

INSERT INTO `iis_vyrobna` (`id`, `id_vyrobniho_zdroje`, `id_site`, `id_firmy`, `kratky_nazev`, `ulice`, `cislo_p`, `cislo_o`, `kraj`, `okres`, `obec`, `psc`, `parcela`, `gps_n`, `gps_e`, `druh_vyrobny`,`stav`, `vyrobni_EAN`, `EAN_vyrobny`, `vykon_zdroje`, `napetova_hladina`, `zpusob_pripojeni`, `vykaz_za_opm`, `druh_podpory`, `datum_prvniho_pripojeni`, `datum_uvedeni_do_provozu`) VALUES(1, 1, 1, 123456789,'Výrobna 1', 'Rúžová', '1024', '8', 'Olomoucký kraj', 'Olomouc', 'Olomouc', 77900, '235/15', '18.45550', '27.00530', 'sluneční', 'schvaleno', 1001, 2002, 35, '110', 'přímo', 'ano', 'bonus roční', '2021-09-01', '2021-09-02');
INSERT INTO `iis_vyrobna` (`id`, `id_vyrobniho_zdroje`, `id_site`, `id_firmy`, `kratky_nazev`, `ulice`, `cislo_p`, `cislo_o`, `kraj`, `okres`, `obec`, `psc`, `parcela`, `gps_n`, `gps_e`, `druh_vyrobny`,`stav`, `vyrobni_EAN`, `EAN_vyrobny`, `vykon_zdroje`, `napetova_hladina`, `zpusob_pripojeni`, `vykaz_za_opm`, `druh_podpory`, `datum_prvniho_pripojeni`, `datum_uvedeni_do_provozu`) VALUES(2, 2, 2, 123456789,'Výrobna 2', 'Modrá', '1024', '8', 'Olomoucký kraj', 'Olomouc', 'Olomouc', 77900, '235/16', '18.45899', '27.00530', 'sluneční', 'podano', 1001, 2002, 35, '110', 'přímo', 'ano', 'bonus roční', '2021-09-04', '2021-09-08');
INSERT INTO `iis_vyrobna` (`id`, `id_vyrobniho_zdroje`, `id_site`, `id_firmy`, `kratky_nazev`, `ulice`, `cislo_p`, `cislo_o`, `kraj`, `okres`, `obec`, `psc`, `parcela`, `gps_n`, `gps_e`, `druh_vyrobny`,`stav`, `vyrobni_EAN`, `EAN_vyrobny`, `vykon_zdroje`, `napetova_hladina`, `zpusob_pripojeni`, `vykaz_za_opm`, `druh_podpory`, `datum_prvniho_pripojeni`, `datum_uvedeni_do_provozu`) VALUES(3, 3, 3, 234567892,'Vodnice 3', 'Mokrá', '420', '8', 'Jihomoravský kraj', 'Brno-město  ', 'Brno', 77900, '235/16', '18.45899', '27.00530', 'vodní', 'schvaleno', 1001, 2002, 35, '110', 'přímo', 'ano', 'bonus roční', '2021-09-04', '2021-09-08');
INSERT INTO `iis_vyrobna` (`id`, `id_vyrobniho_zdroje`, `id_site`, `id_firmy`, `kratky_nazev`, `ulice`, `cislo_p`, `cislo_o`, `kraj`, `okres`, `obec`, `psc`, `parcela`, `gps_n`, `gps_e`, `druh_vyrobny`,`stav`, `vyrobni_EAN`, `EAN_vyrobny`, `vykon_zdroje`, `napetova_hladina`, `zpusob_pripojeni`, `vykaz_za_opm`, `druh_podpory`, `datum_prvniho_pripojeni`, `datum_uvedeni_do_provozu`) VALUES(4, 4, 4, 518468138,'Vodnice 4', 'Mokrá', '240', '5', 'Jihomoravský kraj', 'Brno-město  ', 'Brno', 77900, '235/16', '18.45899', '27.00530', 'vodní', 'podano', 1001, 2002, 35, '110', 'přímo', 'ano', 'bonus roční', '2021-09-04', '2021-09-08');

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
