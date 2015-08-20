
--
-- Table structure for records
--
DROP TABLE IF EXISTS `records`;
CREATE TABLE `records` (
  `email` varchar(255) NOT NULL,
  `result` TINYINT(1) default 0,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;