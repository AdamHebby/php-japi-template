CREATE DATABASE IF NOT EXISTS `api`;
USE `api`;

CREATE TABLE IF NOT EXISTS `user` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `status` ENUM('active', 'inactive', 'disabled') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Basic API Secrets when OAuth2 is overkill
CREATE TABLE IF NOT EXISTS `api_client` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `api_key` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `api_usage` (
    `user_id` INT UNSIGNED NOT NULL,
    `api_client_id` INT UNSIGNED NOT NULL,
    `hits` INT UNSIGNED NOT NULL,
    `last_hit` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (api_client_id) REFERENCES api_client(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, api_client_id)
);

CREATE TABLE IF NOT EXISTS `api_usage_history` (
    `user_id` INT UNSIGNED NOT NULL,
    `api_client_id` INT UNSIGNED NOT NULL,
    `hits` INT UNSIGNED NOT NULL,
    `year` INT UNSIGNED NOT NULL,
    `month` INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (api_client_id) REFERENCES api_client(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, api_client_id, year, month)
);

CREATE TABLE IF NOT EXISTS `api_usage_limit` (
    `user_id` INT UNSIGNED NOT NULL,
    `limit` INT UNSIGNED NOT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id)
);

INSERT INTO `user` (`name`, `email`, `password`) VALUES ('admin', 'admin@localhost', '$2y$10$1J');
INSERT INTO `api_client` (`user_id`, `name`, `api_key`) VALUES (1, 'test', 'kabsdjabsdiubasdbiasbdhasvdjhbasjhbda');
INSERT INTO `api_usage_limit` (`user_id`, `limit`) VALUES (1, 99999);

-- OAuth2 Tables
-- CREATE TABLE IF NOT EXISTS `oauth_clients` (
--     `client_id` VARCHAR(80) NOT NULL,
--     `client_secret` VARCHAR(80) NOT NULL,
--     PRIMARY KEY (client_id)
-- );

-- CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
--     `access_token` VARCHAR(40) NOT NULL,
--     `refresh_token` VARCHAR(40),
--     `client_id` VARCHAR(80) NOT NULL,
--     `user_id` INT NOT NULL,
--     `expires` TIMESTAMP NOT NULL,
--     `scope` VARCHAR(2000),
--     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--     FOREIGN KEY (client_id) REFERENCES oauth_clients(client_id)
--     PRIMARY KEY (access_token)
-- );
