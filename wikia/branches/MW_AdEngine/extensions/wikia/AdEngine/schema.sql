DROP TABLE IF EXISTS ad_slot;
CREATE TABLE ad_slot (
  id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  name VARCHAR(50) NOT NULL,
  skin VARCHAR(25) NOT NULL,
  size varchar(25),
  provider_id TINYINT UNSIGNED NOT NULL,
  enabled ENUM('Yes', 'No') DEFAULT 'Yes',
  PRIMARY KEY(id),
  UNIQUE KEY (name, skin)
) ENGINE=InnoDB;

INSERT INTO ad_slot VALUES (NULL, 'HOME_TOP_LEADERBOARD', 'monaco', '728x90', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'HOME_TOP_RIGHT_BOXAD', 'monaco', '300x250', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'HOME_LEFT_SKYSCRAPER_1', 'monaco', '160x600', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'HOME_LEFT_SKYSCRAPER_2', 'monaco', '160x600', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'TOP_LEADERBOARD', 'monaco', '728x90', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'TOP_RIGHT_BOXAD', 'monaco', '300x250', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SKYSCRAPER_1', 'monaco', '160x600', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SKYSCRAPER_2', 'monaco', '160x600', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_BOXAD', 'monaco', '300x250', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SPOTLIGHT_1', 'monaco', '200x75', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_SPOTLIGHT_LEFT', 'monaco', '200x75', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_SPOTLIGHT_MIDDLE', 'monaco', '200x75', 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_SPOTLIGHT_RIGHT', 'monaco', '200x75', 1, 'Yes');

DROP TABLE IF EXISTS ad_slot_override;
CREATE TABLE ad_slot_override (
  id SMALLINT UNSIGNED NOT NULL,
  city_id INT UNSIGNED NOT NULL,
  provider_id TINYINT UNSIGNED DEFAULT NULL,
  enabled ENUM('Yes', 'No') DEFAULT NULL,
  PRIMARY KEY(id, city_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS ad_provider_value;
CREATE TABLE ad_provider_value (
  id smallint unsigned auto_increment NOT NULL,
  provider_id TINYINT UNSIGNED NOT NULL,
  city_id INT UNSIGNED DEFAULT NULL,
  keyname VARCHAR(25),
  keyvalue VARCHAR(255),
  PRIMARY KEY (id),
  KEY (provider_id, city_id)
) ENGINE=InnoDB;

INSERT INTO ad_provider_value VALUES (NULL, 1, NULL, 'alldart', 'true');
--INSERT INTO ad_provider_value VALUES (NULL, 1, 490, 'wowwikidart', 'true');