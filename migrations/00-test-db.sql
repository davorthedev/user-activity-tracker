CREATE DATABASE IF NOT EXISTS db_user_activity_tracker_test
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

GRANT ALL PRIVILEGES ON db_user_activity_tracker_test.* TO 'app'@'%';
FLUSH PRIVILEGES;