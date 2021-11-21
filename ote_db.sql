-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Ned 21. lis 2021, 16:43
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
-- Databáze: `ote_db`
--
CREATE DATABASE IF NOT EXISTS `ote_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `ote_db`;

-- --------------------------------------------------------

--
-- Struktura tabulky `firma`
--

DROP TABLE IF EXISTS `firma`;
CREATE TABLE `firma` (
  `rut_id` int(11) NOT NULL,
  `ean` int(11) NOT NULL,
  `nazev` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `typ_firmy` enum('test') COLLATE utf8_czech_ci NOT NULL,
  `ic` int(11) NOT NULL,
  `web` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `datum_vytvoreni` date NOT NULL,
  `ulice` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `cislo_p` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `cislo_o` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `obec` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `psc` int(11) NOT NULL,
  `predcisli` int(32) NOT NULL,
  `cislo_uctu` int(11) NOT NULL,
  `kod_banky` enum('0100') COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `osoba`
--

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
  `ulice` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `cislo_p` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `cislo_o` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `obec` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `psc` int(11) NOT NULL,
  `kancelar` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `pozice` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `plat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `pozadavek`
--

DROP TABLE IF EXISTS `pozadavek`;
CREATE TABLE `pozadavek` (
  `id` int(11) NOT NULL,
  `datum_vytvoreni` date NOT NULL,
  `datum_uzavreni` date NOT NULL,
  `predmet` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `status` enum('test') COLLATE utf8_czech_ci NOT NULL,
  `obsah_pozadavku` text COLLATE utf8_czech_ci NOT NULL,
  `odpoved` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `vykaz`
--

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

-- --------------------------------------------------------

--
-- Struktura tabulky `vyrobna`
--

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
  `druh_vyrobny` enum('test') COLLATE utf8_czech_ci NOT NULL,
  `vyrobni_EAN` int(11) NOT NULL,
  `EAN_vyrobny` int(11) NOT NULL,
  `vykon_zdroje` int(11) NOT NULL,
  `napetova_hladina` int(11) NOT NULL,
  `zpusob_pripojeni` enum('test') COLLATE utf8_czech_ci NOT NULL,
  `vykaz_za_opm` int(11) NOT NULL,
  `druh_podpory` enum('test') COLLATE utf8_czech_ci NOT NULL,
  `datum_prvniho_pripojeni` date NOT NULL,
  `datum_uvedeni_do_provozu` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `firma`
--
ALTER TABLE `firma`
  ADD PRIMARY KEY (`rut_id`);

--
-- Indexy pro tabulku `osoba`
--
ALTER TABLE `osoba`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `pozadavek`
--
ALTER TABLE `pozadavek`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `vyrobna`
--
ALTER TABLE `vyrobna`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `firma`
--
ALTER TABLE `firma`
  MODIFY `rut_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `osoba`
--
ALTER TABLE `osoba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `pozadavek`
--
ALTER TABLE `pozadavek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `vyrobna`
--
ALTER TABLE `vyrobna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
