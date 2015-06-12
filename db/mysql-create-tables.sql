CREATE TABLE IF NOT EXISTS `secret` (
 `identifier` varchar(100) NOT NULL,
 `secret` varchar(255) NOT NULL,
 `counter` int(10) DEFAULT '0',
 `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
