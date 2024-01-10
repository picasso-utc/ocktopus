# Controller TodayConsumption

Prends en argument un article

Ce controller permet de connaitre la quantité d'article vendu dans la journée pour l'utiliser par exemple dans des formats de comparaison de ventes sur les écrans du pic. Le fonctionnement est simple : 
 - On requête tout les achats de la journée
 - On vérifie si les achats contiennent le nom de l'article
 - On compte les quantités

**Vous pouvez trouver le code ici : app/Http/Controllers/TodayConsumptionController.php**

Le code n'est pas utilisé encore, comme dans kraken il est utilisé spécifiquement dans des cas de comparaison précis. 

A implémenter le semestre prochain un moyen de l'utiliser et de créer des visus automatiquement en fonction des article que l'on veut comparer. 
