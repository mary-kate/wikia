-- Providers DROP TABLE IF EXISTS ad_provider;
DROP TABLE IF EXISTS ad_provider;
CREATE TABLE ad_provider (
        provider_id TINYINT UNSIGNED AUTO_INCREMENT NOT NULL,
        provider_name VARCHAR(25),
        PRIMARY KEY(provider_id)
) ENGINE=InnoDB;
INSERT INTO ad_provider VALUES (1, 'DART');
INSERT INTO ad_provider VALUES (2, 'OpenX');
INSERT INTO ad_provider VALUES (3, 'Google');
INSERT INTO ad_provider VALUES (4, 'GAM');
INSERT INTO ad_provider VALUES (5, 'PubMatic');
INSERT INTO ad_provider VALUES (6, 'Athena');
INSERT INTO ad_provider VALUES (7, 'ContextWeb');


-- Slots
DROP TABLE IF EXISTS ad_slot;
CREATE TABLE ad_slot (
  as_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  slot VARCHAR(50) NOT NULL, 
  skin VARCHAR(25) NOT NULL, -- monaco, quartz, blah
  size varchar(25),
  load_priority TINYINT UNSIGNED DEFAULT NULL,
  default_provider_id TINYINT UNSIGNED NOT NULL,
  default_enabled ENUM('Yes', 'No') DEFAULT 'Yes',
  PRIMARY KEY(as_id),
  UNIQUE KEY (slot, skin)
) ENGINE=InnoDB;

INSERT INTO ad_slot VALUES (NULL, 'HOME_TOP_LEADERBOARD', 'monaco', '728x90', 15, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'HOME_TOP_RIGHT_BOXAD', 'monaco', '300x250', 20, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'HOME_LEFT_SKYSCRAPER_1', 'monaco', '160x600', 10, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'HOME_LEFT_SKYSCRAPER_2', 'monaco', '160x600', 8, 1, 'No');
INSERT INTO ad_slot VALUES (NULL, 'TOP_LEADERBOARD', 'monaco', '728x90', 15, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'TOP_RIGHT_BOXAD', 'monaco', '300x250', 20, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SKYSCRAPER_1', 'monaco', '160x600', 10, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SKYSCRAPER_2', 'monaco', '160x600', 8, 2, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_BOXAD', 'monaco', '300x250', 4, 3, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SPOTLIGHT_1', 'monaco', '200x75', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_SPOTLIGHT_LEFT', 'monaco', '200x75', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_SPOTLIGHT_MIDDLE', 'monaco', '200x75', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'FOOTER_SPOTLIGHT_RIGHT', 'monaco', '200x75', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SKYSCRAPER_3', 'monaco', '160x600', 6, 3, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'RIGHT_SPOTLIGHT_1', 'monobook', '125x125', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'RIGHT_SKYSCRAPER_1', 'monobook', '120x600', 6, 3, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'RIGHT_SPOTLIGHT_2', 'monobook', '125x125', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_SPOTLIGHT_2', 'uncyclopedia', '125x125', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'TOP_RIGHT_BOXAD', 'quartz', '300x250', 20, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'RIGHT_SPOTLIGHT_1', 'quartz', '125x125', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'RIGHT_SPOTLIGHT_2', 'quartz', '125x125', 0, 4, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_NAVBOX_1', 'monaco', '200x200', 3, 1, 'Yes');
INSERT INTO ad_slot VALUES (NULL, 'LEFT_NAVBOX_2', 'monaco', '200x200', 3, 1, 'Yes');

-- Allow wikis to override slots
DROP TABLE IF EXISTS ad_slot_override;
CREATE TABLE ad_slot_override (
  as_id SMALLINT UNSIGNED NOT NULL,
  city_id INT UNSIGNED NOT NULL,
  provider_id TINYINT UNSIGNED DEFAULT NULL,
  enabled ENUM('Yes', 'No') DEFAULT NULL,
  comment text,
  PRIMARY KEY(as_id, city_id),
  KEY(city_id)
) ENGINE=InnoDB;

-- Store provider specific values like  key-values for DART
DROP TABLE IF EXISTS ad_provider_value;
CREATE TABLE ad_provider_value (
  apv_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  provider_id TINYINT UNSIGNED NOT NULL,
  city_id INT UNSIGNED DEFAULT NULL, -- null means that it is the default
  keyname VARCHAR(25),
  keyvalue VARCHAR(255),
  PRIMARY KEY (apv_id),
  UNIQUE KEY (city_id, keyname, keyvalue),
  KEY (provider_id, city_id)
) ENGINE=InnoDB;
