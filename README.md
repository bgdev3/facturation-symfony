# Facturation Symfony

Application de facturation en Symfony — gestion clients, devis, factures avec génération PDF et numérotation légale.

## Stack

- **Backend** : Symfony 8.1
- **Base de données** : MySQL / Doctrine ORM
- **PDF** : Gotenberg (Docker)
- **Mail (dev)** : Mailpit (Docker)
- **CSS** : Tailwind CSS (via AssetMapper)
- **Tests** : PHPUnit

## Fonctionnalités (MVP)

- Authentification et sécurité par entreprise (Voters)
- Gestion des clients (CRUD)
- Création de devis
- Génération de factures avec numérotation légale
- Export PDF
- Dashboard

## Roadmap v2

- Relances automatiques
- Export comptable
- API Platform

## Installation

```bash
git clone https://github.com/bgdev3/facturation-symfony.git
cd facturation-symfony
composer install
cp .env .env.local
# configurer DATABASE_URL et APP_SECRET dans .env.local

# Lancer les services annexes (PDF, mail)
docker compose up -d

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

## Tests

```bash
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:migrations:migrate
php bin/phpunit
```

## Licence

MIT

## Auteur

Guillaume — [bgdev.fr](https://bgdev.fr)
