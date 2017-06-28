CREATE TABLE IF NOT EXISTS organisation (
   id int(11) NOT NULL auto_increment,
   name varchar(100) NOT NULL,
   address varchar(100) NOT NULL,
   telephone varchar(20) default NULL,
   is_deleted tinyint(1) DEFAULT '0',
   Created timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   Updated timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (id),
   UNIQUE KEY (name)
);
INSERT INTO organisation (name,address,telephone)  VALUES  
	('Simcon GmbH', 'Leopoldstr. 230,D-80807,Munich,Germany','+49-89-99953947'),
	('DiIT AG', 'Neideckstr. 26,D-81248,Munich,Germany','+49-89-99953948');
INSERT INTO organisation (name, address)  VALUES  
	('Toomabumarkt GmbH', 'Bodenseestr. 210,D-81243,Munich,Germany'),
	('Burgerking', 'Bodenseestr. 319,D-81243,Munich,Germany'),
	('Edeka GmbH', 'Bodenseestr. 319,D-81243,Munich,Germany'),
	('Rewe GmbH', 'Limesstr. 120,D-81249,Munich,Germany');
