CREATE TABLE IF NOT EXISTS user (
   id int(11) NOT NULL auto_increment,
   name varchar(100) NOT NULL,
   address varchar(100) default NULL,
   email varchar(100) NOT NULL,
   password varchar(100) NOT NULL,
   employee_ID varchar(20) default NULL,
   role tinyint(1) NOT NULL,
   organisation_ID INT(11) default NULL,
   birthdate date default NULL,
   probation bool default false,
   telephone varchar(20) default NULL,
   is_deleted tinyint(1) DEFAULT '0',
   Created timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   Updated timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (id),
   UNIQUE KEY (email),
   FOREIGN KEY (organisation_ID) REFERENCES organisation(id) ON DELETE RESTRICT ON UPDATE RESTRICT
);
INSERT INTO user (name, email,password,role,probation)  VALUES  
	('admin', 'admin@mifon.tk', 'A1b2c#d0',1,false),
	('Usman Khalid', 'usmank@mifon.tk', 'A1b2c#d0',1,false),
	('Rizwan Khalid', 'rizwank@mifon.tk', 'A1b2c#d0',1,false),
	('Noman Khalid', 'nomank@mifon.tk', 'A1b2c#d0',1,false);
INSERT INTO user (name, email,password,role,probation,birthdate,address)  VALUES  
	('Sajid Mahmood', 'sajidm@mifon.tk', 'A1b2c#d0',1,false,'1974-02-20','Newport,UK,NP100BP');
INSERT INTO user (name, email,password,role,probation,organisation_ID)  VALUES
	('employer', 'employer@mifon.tk', 'A1b2c#d0',2,false,1),
	('employee', 'employee@mifon.tk', 'A1b2c#d0',3,false,1),
	('Karl-Heinz Oliv', 'kho@mifon.tk', 'A1b2c#d0',2,false,1),
	('Gerhard Shaub', 'gerhards@mifon.tk', 'A1b2c#d0',2,false,2);
