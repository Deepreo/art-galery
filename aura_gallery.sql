-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 04 Şub 2026, 16:42:31
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `aura_gallery`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `artwork_id`, `username`, `comment`, `created_at`) VALUES
(3, 1, 'admin', 'Başııı', '2026-01-23 08:43:47');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `profile_notes`
--

CREATE TABLE `profile_notes` (
  `id` int(11) NOT NULL,
  `profile_user_id` int(11) NOT NULL,
  `note_author` varchar(50) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `profile_notes`
--

INSERT INTO `profile_notes` (`id`, `profile_user_id`, `note_author`, `note`, `created_at`) VALUES
(1, 2, 'admin', 'HER SEY GORUNDUGU GIBI DEGIL', '2026-01-23 15:37:16'),
(2, 7, 'flagger', 'FLAG: DEEPREO{G1ZL1-<!--V3R1--!>-K4LM4D1}', '2026-01-26 16:09:27');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$7LuhceZdfct9GI4.DQgDcOTYHlaSMaIXWSzFlIKlWDMbtgISD.hsC', 'admin', '2026-01-23 07:00:32'),
(2, 'collector', '$2y$10$tJFlvqkVhsmS4/tCvYcSkOeZvYpn5A4u/lhPls4wy/02mzX7dtQyy', 'member', '2026-01-23 07:00:32'),
(3, 'artist', '$2y$10$zAIINIb2UjJlBRLjblpmZ.e9nxcJQU1h9j9PDHA/hThWaHN1Gx3qy', 'member', '2026-01-26 12:07:45'),
(7, 'flagger', '$2y$10$u4CTrrHqaWahTerft45OzOxKJwhJ6YiIeS4W37nbN9tAu6cnqEHUm', 'member', '2026-01-26 11:45:53');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `profile_notes`
--
ALTER TABLE `profile_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_user_id` (`profile_user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `profile_notes`
--
ALTER TABLE `profile_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `profile_notes`
--
ALTER TABLE `profile_notes`
  ADD CONSTRAINT `profile_notes_ibfk_1` FOREIGN KEY (`profile_user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
