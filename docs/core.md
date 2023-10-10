# Module Core

## Gestion de l'authentification:
Auth des membres du pic a travers connexion cas pour le site
Auth des membres du pic quand ils badge sur les bornes pour les activer
Auth des membres du pic quand ils se connecte en rentrant leurs login sur les bornes du pic

## Models:
Membres du pic
Personnes bloquées (on stock le cas mais faut bien renvoyé le badge_uid en passant par ginger) (A faire passer dans beethoven)
Semestre
Models de base pour la treso (jsp pk c'est la)
Les droits des utilisateurs

## Gestion des permissions:
Gérer les droits d'accès (on peut faire plus simple avec des middleware)

## Services:
Configurer un service ginger facilement utilisable dans tout le serveur (faire un controlleur modulable pour utiliser ginger)
Configurer un service payUTC facilement utilisable dans tout le serveur (faire un controlleur modulable pour utiliser payutc donc weezevent)
Generation de excel (jsp pourquoi on mets ca dans core quand c'est seulement utilisé dans des cas très précis)
Configurer un service portal facilement utilisable dans tout le serveur (faire un controlleur modulable pour utiliser portal donc le portail des assos)
Gestion des semestres (recupération du semestre actuel et d'un semestre spécifique sur demande)

## Templates
Templates de la convention, du mail des perms, du mail de reminder des perms, du planning (a envoyé avec le mail des perms) et du livre de recette (a envoyé avec le mail des perms) sont stocker dans core aussi (a changer si on passe sur laravel)