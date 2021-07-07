<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707171856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE crianca (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(100) NOT NULL, dn DATE NOT NULL, nome_foto VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crianca_vinculo (usuario_id VARCHAR(100) NOT NULL, crianca_id INT NOT NULL, parentesco VARCHAR(10) NOT NULL, INDEX IDX_A3C1051FDB38439E (usuario_id), INDEX IDX_A3C1051F139C2F64 (crianca_id), PRIMARY KEY(usuario_id, crianca_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kb_artigo (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(100) NOT NULL, slug VARCHAR(50) NOT NULL, tags JSON NOT NULL, corpo LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mamadeira (id INT AUTO_INCREMENT NOT NULL, crianca_id INT NOT NULL, dh DATETIME NOT NULL, alimento VARCHAR(20) NOT NULL, volume INT NOT NULL, INDEX IDX_7E5D4F0B139C2F64 (crianca_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medico (id INT AUTO_INCREMENT NOT NULL, usuario_id VARCHAR(100) NOT NULL, especialidade VARCHAR(30) NOT NULL, crm VARCHAR(20) NOT NULL, uf_crm VARCHAR(2) NOT NULL, validade DATE NOT NULL, UNIQUE INDEX UNIQ_34E5914CDB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mensagem_chat (id INT AUTO_INCREMENT NOT NULL, autor_id VARCHAR(100) NOT NULL, destinatario_id VARCHAR(100) NOT NULL, dh DATETIME NOT NULL, lido JSON DEFAULT NULL, mensagem LONGTEXT NOT NULL, INDEX IDX_EBCE523014D45BBE (autor_id), INDEX IDX_EBCE5230B564FBC1 (destinatario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refeicao_solida (id INT AUTO_INCREMENT NOT NULL, crianca_id INT NOT NULL, dh DATETIME NOT NULL, volume INT NOT NULL, anotacao LONGTEXT DEFAULT NULL, INDEX IDX_7291E6EF139C2F64 (crianca_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relatorio (id INT AUTO_INCREMENT NOT NULL, crianca_id INT NOT NULL, autorizado JSON DEFAULT NULL, dh DATETIME NOT NULL, d_inicio DATE NOT NULL, d_fim DATE NOT NULL, nome_arquivo VARCHAR(100) DEFAULT NULL, INDEX IDX_7EB91A0F139C2F64 (crianca_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id VARCHAR(100) NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seio_materno (id INT AUTO_INCREMENT NOT NULL, crianca_id INT NOT NULL, dh_inicio DATETIME NOT NULL, dh_fim DATETIME NOT NULL, lado VARCHAR(1) NOT NULL, INDEX IDX_73C9198E139C2F64 (crianca_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (email VARCHAR(100) NOT NULL, senha VARCHAR(180) NOT NULL, nome VARCHAR(100) NOT NULL, nome_foto VARCHAR(100) DEFAULT NULL, data_cadastro DATETIME DEFAULT NULL, roles JSON DEFAULT NULL, is_verified TINYINT(1) NOT NULL, PRIMARY KEY(email)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crianca_vinculo ADD CONSTRAINT FK_A3C1051FDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (email)');
        $this->addSql('ALTER TABLE crianca_vinculo ADD CONSTRAINT FK_A3C1051F139C2F64 FOREIGN KEY (crianca_id) REFERENCES crianca (id)');
        $this->addSql('ALTER TABLE mamadeira ADD CONSTRAINT FK_7E5D4F0B139C2F64 FOREIGN KEY (crianca_id) REFERENCES crianca (id)');
        $this->addSql('ALTER TABLE medico ADD CONSTRAINT FK_34E5914CDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (email)');
        $this->addSql('ALTER TABLE mensagem_chat ADD CONSTRAINT FK_EBCE523014D45BBE FOREIGN KEY (autor_id) REFERENCES usuario (email)');
        $this->addSql('ALTER TABLE mensagem_chat ADD CONSTRAINT FK_EBCE5230B564FBC1 FOREIGN KEY (destinatario_id) REFERENCES usuario (email)');
        $this->addSql('ALTER TABLE refeicao_solida ADD CONSTRAINT FK_7291E6EF139C2F64 FOREIGN KEY (crianca_id) REFERENCES crianca (id)');
        $this->addSql('ALTER TABLE relatorio ADD CONSTRAINT FK_7EB91A0F139C2F64 FOREIGN KEY (crianca_id) REFERENCES crianca (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES usuario (email)');
        $this->addSql('ALTER TABLE seio_materno ADD CONSTRAINT FK_73C9198E139C2F64 FOREIGN KEY (crianca_id) REFERENCES crianca (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crianca_vinculo DROP FOREIGN KEY FK_A3C1051F139C2F64');
        $this->addSql('ALTER TABLE mamadeira DROP FOREIGN KEY FK_7E5D4F0B139C2F64');
        $this->addSql('ALTER TABLE refeicao_solida DROP FOREIGN KEY FK_7291E6EF139C2F64');
        $this->addSql('ALTER TABLE relatorio DROP FOREIGN KEY FK_7EB91A0F139C2F64');
        $this->addSql('ALTER TABLE seio_materno DROP FOREIGN KEY FK_73C9198E139C2F64');
        $this->addSql('ALTER TABLE crianca_vinculo DROP FOREIGN KEY FK_A3C1051FDB38439E');
        $this->addSql('ALTER TABLE medico DROP FOREIGN KEY FK_34E5914CDB38439E');
        $this->addSql('ALTER TABLE mensagem_chat DROP FOREIGN KEY FK_EBCE523014D45BBE');
        $this->addSql('ALTER TABLE mensagem_chat DROP FOREIGN KEY FK_EBCE5230B564FBC1');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE crianca');
        $this->addSql('DROP TABLE crianca_vinculo');
        $this->addSql('DROP TABLE kb_artigo');
        $this->addSql('DROP TABLE mamadeira');
        $this->addSql('DROP TABLE medico');
        $this->addSql('DROP TABLE mensagem_chat');
        $this->addSql('DROP TABLE refeicao_solida');
        $this->addSql('DROP TABLE relatorio');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE seio_materno');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
