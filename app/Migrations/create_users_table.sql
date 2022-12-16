CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `createdAt` TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    `updatedAt` TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
)