# Facturation Symfony

Application de gestion de facturation pour indépendants et petites structures — clients, devis, factures, relances et export comptable, avec numérotation légale et génération PDF.

## Stack

- **Backend** : Symfony 8.1, PHP 8.4+
- **Base de données** : SQL / Doctrine ORM
- **PDF** : Gotenberg (Docker)
- **Mail (dev)** : Mailpit (Docker)
- **CSS** : Tailwind CSS (via AssetMapper)
- **Tests** : PHPUnit

## Fonctionnalités

- **Authentification et sécurité par entreprise**, via des Voters Symfony (chaque utilisateur n'accède qu'à ses propres données)
- **Gestion des clients** (CRUD)
- **Devis** : création, édition avec aperçu en direct, verrouillage automatique dès l'envoi au client
- **Conversion devis → facture** : une facture en brouillon est générée automatiquement à l'acceptation du devis, sans ressaisie
- **Facturation avec numérotation légale** : séquence continue sans trou, verrouillage d'une facture dès son émission (aucune suppression possible après validation, conformément à la réglementation)
- **Génération PDF** des devis et factures via Gotenberg
- **Relances** des factures en retard, déclenchées manuellement depuis la fiche facture
- **Export CSV** des factures, pour un usage comptable
- **Dashboard** avec indicateurs clés (CA du mois, factures impayées, devis en attente, factures en retard)

## Points d'attention métier

Ce projet ne se limite pas à un CRUD : plusieurs règles de gestion et de conformité ont guidé les choix techniques.

- **Numérotation séquentielle sans trou** : une facture émise ne peut plus être ni modifiée, ni supprimée.
- **Statuts distincts brouillon / envoyée**, contrôlés à la fois côté entité (`isEditable()`, `isDeletable()`) et côté autorisation (Voters), pour garantir la règle même en cas d'appel direct hors contrôleur.
- **Mentions légales obligatoires** intégrées aux documents générés (pénalités de retard, indemnité forfaitaire de recouvrement).

## Installation

> Seuls les services annexes (base de données, PDF, mail) sont dockerisés. L'application Symfony elle-même tourne en local.

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
