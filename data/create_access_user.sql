CREATE USER 'apiuser'@'localhost' IDENTIFIED BY 'ap1us3r';
GRANT SELECT,INSERT,UPDATE,CREATE ON *.* TO 'apiuser'@'localhost';