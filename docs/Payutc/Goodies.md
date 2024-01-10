# Les Goodies

## Les models

Le ggodies est un modèle assez simple, on lie juste un état (récuperer ou non) avec un nom de gagnant des goodies

### Goodies :

*Exemple* :
```{
{
        "name": "Constant Dassonville",
        "collected": True
    }
```

## Les méthodes

### generateWinners()

Fais une requête grâçe au service payutc, puis récupère la date du dernier résultat jusqu'à avoir requêter sur l'ensemble de la semaine indiqué

Ensuite on choisit au hasard parmi tout les achats 20 gagnants de goodies. Cela créer donc une liste de gagner (wallet_id) qu'on envoit dans le proxy du simde pour avoir les noms des gagnants


