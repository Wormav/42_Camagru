-- Force UTF-8 charset (with full emoji support)
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS images;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id                  INT             AUTO_INCREMENT PRIMARY KEY,
    username            VARCHAR(50)     UNIQUE NOT NULL,
    email               VARCHAR(255)    UNIQUE NOT NULL,
    password            VARCHAR(255)    NOT NULL,
    is_verified         BOOLEAN         NOT NULL DEFAULT FALSE,
    verification_token  VARCHAR(255)    DEFAULT NULL,
    reset_token         VARCHAR(255)    DEFAULT NULL,
    reset_token_expires DATETIME        DEFAULT NULL,
    notify_comments     BOOLEAN         NOT NULL DEFAULT TRUE,
    avatar_path         VARCHAR(255)    DEFAULT NULL,
    created_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                 ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE images (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    user_id         INT             NOT NULL,
    image_path      VARCHAR(255)    NOT NULL,
    overlay_used    VARCHAR(100)    DEFAULT NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_images_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_images_user_id     (user_id),
    INDEX idx_images_created_at  (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE comments (
    id          INT         AUTO_INCREMENT PRIMARY KEY,
    image_id    INT         NOT NULL,
    user_id     INT         NOT NULL,
    content     TEXT        NOT NULL,
    created_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_comments_image
        FOREIGN KEY (image_id) REFERENCES images(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_comments_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_comments_image_id    (image_id),
    INDEX idx_comments_created_at  (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE likes (
    id          INT         AUTO_INCREMENT PRIMARY KEY,
    image_id    INT         NOT NULL,
    user_id     INT         NOT NULL,
    created_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_likes_image
        FOREIGN KEY (image_id) REFERENCES images(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_likes_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT uniq_likes_image_user UNIQUE (image_id, user_id),

    INDEX idx_likes_image_id (image_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
