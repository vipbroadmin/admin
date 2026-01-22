<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table and seed default admin user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_users_username ON users (username)');

        $this->addSql("INSERT INTO users (username, roles, password) VALUES ('admin', '[\"ROLE_ADMIN\"]', '\$2y\$10\$I9s9XAblP.7pwWw4SRHKHuja8rKA5fOvJs7703BdxCBtnAfCsuDkC')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
