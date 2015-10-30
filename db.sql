/*AROP DATABASE `shirts4mike`;*/
DROP DATABASE `sitecontent`;
CREATE DATABASE `sitecontent` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE `sitecontent`;

CREATE TABLE IF NOT EXISTS `torrents`(
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(225) DEFAULT NULL,
`tags` varchar(225) DEFAULT NULL,
`url` varchar(255) DEFAULT NULL,
`md5` varchar(50) DEFAULT NULL,
PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

/*INSERT INTO `products` (`sku`,`name`,`img`,`price`,`paypal`) VALUES(NULL, 'Logo shirt, Blue', 'img\logo-shirt-blue.jpg', '12.07', '1451345345');*/
