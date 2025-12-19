<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251219085653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE disputas (id INT AUTO_INCREMENT NOT NULL, resultado VARCHAR(255) NOT NULL, equipo1_id INT NOT NULL, equipo2_id INT NOT NULL, torneo_id INT NOT NULL, UNIQUE INDEX UNIQ_E13A0DFC8D588AD (equipo1_id), INDEX IDX_E13A0DFC1A602743 (equipo2_id), INDEX IDX_E13A0DFCA0139802 (torneo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE equipo_fantasy (id INT AUTO_INCREMENT NOT NULL, puntos INT NOT NULL, ligafantasy_id INT DEFAULT NULL, entrenador_id INT DEFAULT NULL, jugadores_id INT DEFAULT NULL, INDEX IDX_5D0D50218B2F69BF (ligafantasy_id), INDEX IDX_5D0D50214FE90CDB (entrenador_id), INDEX IDX_5D0D5021900BE3B3 (jugadores_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE equipos (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, puntos INT NOT NULL, torneo_id INT NOT NULL, INDEX IDX_8C188AD0A0139802 (torneo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE jugadores (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, estadisticas INT NOT NULL, equipo_id INT DEFAULT NULL, INDEX IDX_CF491B7623BFBED (equipo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE liga_fantasy (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, puntuaje INT NOT NULL, torneo_id INT DEFAULT NULL, INDEX IDX_47E3DA2A0139802 (torneo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE puntuaje_evento (id INT AUTO_INCREMENT NOT NULL, puntos INT NOT NULL, evento VARCHAR(255) NOT NULL, disputa_id INT DEFAULT NULL, jugador_id INT DEFAULT NULL, INDEX IDX_AFD56FE448D6EBE2 (disputa_id), INDEX IDX_AFD56FE4B8A54D43 (jugador_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE torneo (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, tipo VARCHAR(255) NOT NULL, seguidores INT NOT NULL, imagen VARCHAR(255) DEFAULT NULL, organizador_id INT NOT NULL, INDEX IDX_7CEB63FEE3445778 (organizador_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, foto VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE user_torneo (user_id INT NOT NULL, torneo_id INT NOT NULL, INDEX IDX_19B978C3A76ED395 (user_id), INDEX IDX_19B978C3A0139802 (torneo_id), PRIMARY KEY (user_id, torneo_id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE disputas ADD CONSTRAINT FK_E13A0DFC8D588AD FOREIGN KEY (equipo1_id) REFERENCES equipos (id)');
        $this->addSql('ALTER TABLE disputas ADD CONSTRAINT FK_E13A0DFC1A602743 FOREIGN KEY (equipo2_id) REFERENCES equipos (id)');
        $this->addSql('ALTER TABLE disputas ADD CONSTRAINT FK_E13A0DFCA0139802 FOREIGN KEY (torneo_id) REFERENCES torneo (id)');
        $this->addSql('ALTER TABLE equipo_fantasy ADD CONSTRAINT FK_5D0D50218B2F69BF FOREIGN KEY (ligafantasy_id) REFERENCES liga_fantasy (id)');
        $this->addSql('ALTER TABLE equipo_fantasy ADD CONSTRAINT FK_5D0D50214FE90CDB FOREIGN KEY (entrenador_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE equipo_fantasy ADD CONSTRAINT FK_5D0D5021900BE3B3 FOREIGN KEY (jugadores_id) REFERENCES jugadores (id)');
        $this->addSql('ALTER TABLE equipos ADD CONSTRAINT FK_8C188AD0A0139802 FOREIGN KEY (torneo_id) REFERENCES torneo (id)');
        $this->addSql('ALTER TABLE jugadores ADD CONSTRAINT FK_CF491B7623BFBED FOREIGN KEY (equipo_id) REFERENCES equipos (id)');
        $this->addSql('ALTER TABLE liga_fantasy ADD CONSTRAINT FK_47E3DA2A0139802 FOREIGN KEY (torneo_id) REFERENCES torneo (id)');
        $this->addSql('ALTER TABLE puntuaje_evento ADD CONSTRAINT FK_AFD56FE448D6EBE2 FOREIGN KEY (disputa_id) REFERENCES disputas (id)');
        $this->addSql('ALTER TABLE puntuaje_evento ADD CONSTRAINT FK_AFD56FE4B8A54D43 FOREIGN KEY (jugador_id) REFERENCES jugadores (id)');
        $this->addSql('ALTER TABLE torneo ADD CONSTRAINT FK_7CEB63FEE3445778 FOREIGN KEY (organizador_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_torneo ADD CONSTRAINT FK_19B978C3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_torneo ADD CONSTRAINT FK_19B978C3A0139802 FOREIGN KEY (torneo_id) REFERENCES torneo (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE disputas DROP FOREIGN KEY FK_E13A0DFC8D588AD');
        $this->addSql('ALTER TABLE disputas DROP FOREIGN KEY FK_E13A0DFC1A602743');
        $this->addSql('ALTER TABLE disputas DROP FOREIGN KEY FK_E13A0DFCA0139802');
        $this->addSql('ALTER TABLE equipo_fantasy DROP FOREIGN KEY FK_5D0D50218B2F69BF');
        $this->addSql('ALTER TABLE equipo_fantasy DROP FOREIGN KEY FK_5D0D50214FE90CDB');
        $this->addSql('ALTER TABLE equipo_fantasy DROP FOREIGN KEY FK_5D0D5021900BE3B3');
        $this->addSql('ALTER TABLE equipos DROP FOREIGN KEY FK_8C188AD0A0139802');
        $this->addSql('ALTER TABLE jugadores DROP FOREIGN KEY FK_CF491B7623BFBED');
        $this->addSql('ALTER TABLE liga_fantasy DROP FOREIGN KEY FK_47E3DA2A0139802');
        $this->addSql('ALTER TABLE puntuaje_evento DROP FOREIGN KEY FK_AFD56FE448D6EBE2');
        $this->addSql('ALTER TABLE puntuaje_evento DROP FOREIGN KEY FK_AFD56FE4B8A54D43');
        $this->addSql('ALTER TABLE torneo DROP FOREIGN KEY FK_7CEB63FEE3445778');
        $this->addSql('ALTER TABLE user_torneo DROP FOREIGN KEY FK_19B978C3A76ED395');
        $this->addSql('ALTER TABLE user_torneo DROP FOREIGN KEY FK_19B978C3A0139802');
        $this->addSql('DROP TABLE disputas');
        $this->addSql('DROP TABLE equipo_fantasy');
        $this->addSql('DROP TABLE equipos');
        $this->addSql('DROP TABLE jugadores');
        $this->addSql('DROP TABLE liga_fantasy');
        $this->addSql('DROP TABLE puntuaje_evento');
        $this->addSql('DROP TABLE torneo');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_torneo');
    }
}
