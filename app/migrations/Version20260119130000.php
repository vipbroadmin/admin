<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_blocked flag to users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD is_blocked BOOLEAN DEFAULT FALSE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP COLUMN is_blocked');
    }
}
