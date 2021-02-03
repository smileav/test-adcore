CREATE SCHEMA `test-adcore` DEFAULT CHARACTER SET utf8 ;
CREATE TABLE `user` (
                        `user_id` int NOT NULL AUTO_INCREMENT,
                        `email` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                        PRIMARY KEY (`user_id`),
                        UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE `colors` (
                          `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                          `count` int NOT NULL,
                          `user_email` varchar(45) NOT NULL,
                          PRIMARY KEY (`user_email`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
