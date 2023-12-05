# Controller Goodies
Ce controller permet de tirer les goodies de la semaine. Le fonctionnement est simple : 
 - On requête tout les achats entre deux dates (idéalement une semaine)
 - On choisit au hasard 20 gagnants (qui ne sont pas dans le pic)
 - On lie avec leur nom utilisant l'API du simde (car nous n'avons pas les droits pour lié les wallet_id avec le user)

**Vous pouvez trouver le code ici : app/Http/Controllers/GoodiesController.php**
