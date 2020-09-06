-- --------------------------------------------------------
-- Хост:                         server
-- Версия сервера:               5.5.48 - MySQL Community Server (GPL)
-- Операционная система:         Win32
-- HeidiSQL Версия:              9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных ticket
CREATE DATABASE IF NOT EXISTS `ticket` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `ticket`;

-- Дамп структуры для таблица ticket.ticket_config
CREATE TABLE IF NOT EXISTS `ticket_config` (
  `user` char(12) NOT NULL,
  `pass` char(48) NOT NULL,
  `homepage` char(128) NOT NULL,
  `count` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы ticket.ticket_config: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `ticket_config` DISABLE KEYS */;
REPLACE INTO `ticket_config` (`user`, `pass`, `homepage`, `count`) VALUES
	('ticketman', '5d94a752dff94558f1dc44ad46706b75', 'http://qwut.ru/ticket/', 8);
/*!40000 ALTER TABLE `ticket_config` ENABLE KEYS */;

-- Дамп структуры для таблица ticket.ticket_people
CREATE TABLE IF NOT EXISTS `ticket_people` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fam` char(64) NOT NULL,
  `name` char(64) NOT NULL,
  `otch` char(64) NOT NULL,
  `month` tinyint(2) unsigned NOT NULL,
  `year` tinyint(2) unsigned NOT NULL,
  `barnum` char(13) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы ticket.ticket_people: ~10 rows (приблизительно)
/*!40000 ALTER TABLE `ticket_people` DISABLE KEYS */;
REPLACE INTO `ticket_people` (`id`, `fam`, `name`, `otch`, `month`, `year`, `barnum`) VALUES
	(1, 'Простаков', 'Степан', 'нет', 11, 25, '2000699001898'),
	(2, 'Костюшкин', 'Пантелей', 'Филимонович', 9, 26, '2000629000489'),
	(3, 'Трифонов', 'Карпат', 'Филиппинович', 2, 24, '2000839005465'),
	(4, 'Иванов', 'Иван', 'Иванович', 10, 23, '2000789022895'),
	(5, 'ыв', 'ввв', 'ввв', 10, 23, '2000789002262'),
	(6, 'Кто', 'Кто', 'Ктотович', 1, 18, '2000789000015'),
	(7, 'Ким', 'Ю', 'Чан', 9, 23, '2000789002644'),
	(8, 'Ким', 'Ю', 'Чан', 9, 23, '2000789002644'),
	(9, 'Лука', 'Лукич', 'Лукьянов', 0, 0, '2000789002644'),
	(10, 'Лука', 'Лукич', 'Лукьянов', 0, 0, '2000789002644');
/*!40000 ALTER TABLE `ticket_people` ENABLE KEYS */;

-- Дамп структуры для таблица ticket.ticket_region
CREATE TABLE IF NOT EXISTS `ticket_region` (
  `region` char(255) NOT NULL DEFAULT '0',
  `code` tinyint(2) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы ticket.ticket_region: ~104 rows (приблизительно)
/*!40000 ALTER TABLE `ticket_region` DISABLE KEYS */;
REPLACE INTO `ticket_region` (`region`, `code`) VALUES
	('Республика Адыгея', 1),
	('Республика Алтай', 4),
	('Республика Башкортостан', 2),
	('Республика Бурятия', 3),
	('Республика Дагестан', 5),
	('Республика Ингушетия', 6),
	('Кабардино-Балкарская республика', 7),
	('Республика Калмыкия', 8),
	('Карачаево-Черкесская республика', 9),
	('Республика Карелия', 10),
	('Республика Коми', 11),
	('Республика Крым', 91),
	('Республика Марий Эл', 12),
	('Республика Мордовия', 13),
	('Республика Саха (Якутия)', 14),
	('Республика Северная Осетия — Алания', 15),
	('Республика Татарстан', 16),
	('Республика Тыва', 17),
	('Удмуртская республика', 18),
	('Республика Хакасия', 19),
	('Чеченская республика', 20),
	('Чувашская республика', 21),
	('Алтайский край', 22),
	('Забайкальский край', 75),
	('Камчатский край', 41),
	('Краснодарский край', 23),
	('Красноярский край', 24),
	('Пермский край', 59),
	('Приморский край', 25),
	('Ставропольский край', 26),
	('Хабаровский край', 27),
	('Амурская область', 28),
	('Архангельская область', 29),
	('Астраханская область', 30),
	('Белгородская область', 31),
	('Брянская область', 32),
	('Владимирская область', 33),
	('Волгоградская область', 34),
	('Вологодская область', 35),
	('Воронежская область', 36),
	('Ивановская область', 37),
	('Иркутская область', 38),
	('Калининградская область', 39),
	('Калужская область', 40),
	('Кемеровская область', 42),
	('Кировская область', 43),
	('Костромская область', 44),
	('Курганская область', 45),
	('Курская область', 46),
	('Ленинградская область', 47),
	('Липецкая область', 48),
	('Магаданская область', 49),
	('Московская область', 50),
	('Мурманская область', 51),
	('Нижегородская область', 52),
	('Новгородская область', 53),
	('Новосибирская область', 54),
	('Омская область', 55),
	('Оренбургская область', 56),
	('Орловская область', 57),
	('Пензенская область', 58),
	('Псковская область', 60),
	('Ростовская область', 61),
	('Рязанская область', 62),
	('Самарская область', 63),
	('Саратовская область', 64),
	('Сахалинская область', 65),
	('Свердловская область', 66),
	('Смоленская область', 67),
	('Тамбовская область', 68),
	('Тверская область', 69),
	('Томская область', 70),
	('Тульская область', 71),
	('Тюменская область', 72),
	('Ульяновская область', 73),
	('Челябинская область', 74),
	('Ярославская область', 76),
	('Москва', 77),
	('Санкт-Петербург', 78),
	('Севастополь', 92),
	('Еврейская автономная область', 79),
	('Ненецкий автономный округ', 83),
	('Ханты-Мансийский автономный округ - Югра', 86),
	('Чукотский автономный округ', 87),
	('Ямало-Ненецкий автономный округ', 89);
/*!40000 ALTER TABLE `ticket_region` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
