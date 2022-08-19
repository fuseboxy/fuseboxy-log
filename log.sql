CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(200) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `sim_user` varchar(200) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `remark` longtext,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action` (`action`),
  KEY `datetime` (`datetime`),
  KEY `username` (`username`),
  KEY `sim_user` (`sim_user`),
  KEY `entity_type` (`entity_type`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;