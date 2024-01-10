# Ocktopus

Le nouveau back-end du Pic'Asso.

## Installation

Assurez-vous d'avoir `composer` d'installé sur votre machine puis lancez :

```bash
git clone *url*
cd ocktopus
composer install
```

Demandez à une personne qualifiée (ancien responsable informatique) le fichier d'environnement du projet et placez le à la racine.

Pour utiliser une base de données locale, remplacez

```markdown
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

par
```markdown
DB_CONNECTION=sqlite
```

dans le fichier `.env`

Lancez ensuite la commande `php artisan migrate`, puis lancez le projet avec

```bash
php artisan serve
```

Le projet est lancé, bravo !
