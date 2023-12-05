# Controller Top
Ce controller permet de connaitre les tops du semestre. Le fonctionnement est simple : 
 - On requête tout les achats à partir d'une date (celle du jour par exemple)
 - On detecte dans quel semestre nous sommes et donc on requête entre le premier jour du semestre et aujourd'hui
 - On trie à partir de l'id d'un article et on calcule les quantités d'achats de chacun
 - On garde les 10 avec la plus grosse quantité
 - On utilise l'API du simde qui lie les wallet_id et les users

**Vous pouvez trouver le code ici : app/Http/Controllers/TopController.php**




