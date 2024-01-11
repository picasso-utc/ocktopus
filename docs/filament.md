# Filament

Voir la documentation de [Filament](https://filamentphp.com) pour savoir comment développer avec.

### Nos différents boards

- Public :
  - Authorize les utilisateurs pouvant se connecter au CAS à faire des demandes de permanence
- Tréso :
  - Remplace l'ancien Picsous, n'est accessible que par les utilisateurs administrateurs
- Admin :
  - Panel de gestion principal, accessible par les utilisateurs membres du Pic'asso.

Vous pouvez gérer les différents panels dans `app/Providers/Filament/{nom}PanelProvider.php`
Vous pouvez ajouter des resources dans un panel via la commande `php artisan make:filament:resource {nom}`, ceci vous demandera le panel ou ajouter la ressource ensuite.
