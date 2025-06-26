<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250227002046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix foreign key constraint issue while modifying evenement_id column';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Drop Foreign Key
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FD02F13');

        // Step 2: Modify Column
        $this->addSql('ALTER TABLE reservation CHANGE evenement_id evenement_id INT NOT NULL, CHANGE date_rev date_rev DATETIME NOT NULL, CHANGE status status VARCHAR(50) NOT NULL');

        // Step 3: Re-add Foreign Key Constraint
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement(id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Step 1: Drop Foreign Key
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FD02F13');

        // Step 2: Revert Column Changes
        $this->addSql('ALTER TABLE reservation CHANGE evenement_id evenement_id INT DEFAULT NULL, CHANGE date_rev date_rev DATE NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');

        // Step 3: Re-add Foreign Key Constraint
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement(id)');
    }
}
