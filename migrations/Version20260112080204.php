<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260112080204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jugador_eventos (jugadores_id INT NOT NULL, puntuaje_evento_id INT NOT NULL, INDEX IDX_38A4C8D4900BE3B3 (jugadores_id), INDEX IDX_38A4C8D4DC76ECD0 (puntuaje_evento_id), PRIMARY KEY (jugadores_id, puntuaje_evento_id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE jugador_eventos ADD CONSTRAINT FK_38A4C8D4900BE3B3 FOREIGN KEY (jugadores_id) REFERENCES jugadores (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE jugador_eventos ADD CONSTRAINT FK_38A4C8D4DC76ECD0 FOREIGN KEY (puntuaje_evento_id) REFERENCES puntuaje_evento (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jugador_eventos DROP FOREIGN KEY FK_38A4C8D4900BE3B3');
        $this->addSql('ALTER TABLE jugador_eventos DROP FOREIGN KEY FK_38A4C8D4DC76ECD0');
        $this->addSql('DROP TABLE jugador_eventos');
    }
}
