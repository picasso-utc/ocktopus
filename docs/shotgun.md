# Le Shotgun

## Les services

Pouvoir assurer le shotgun des différentes places pour les dégustations, environ une 20aine de connections en simultanées
pour chaque dégustation (10 normalement)

Actuellement ce service peut être géré par woolly car ce n'est pas vraiment un shotgun mais une vente de place. Ce sujet
n'est donc pas prioritaire 

## Models

- Les articles
    - les attributs : 
      - int id{primary key}(required)
      - string name(required)
      - string description
      - int available_stock(required)
      - datetime shotgun_date (pas convaincu de la possibilité d'intégrer cette fonctionnalité et de l'intérêt, à discuter en review)
      - datetime article_date
      - float price(required) 

    - Exemple :
    ```json
    {
        "id": 13,
        "description": "place pour la dégustation dubuisson du 19/12/2024 à 14h",
        "available_stock": 20,
        "name": "dubuisson",
        "shotgun_date": "2023-12-12 14:00:00",
        "article_date": "2023-12-19 14:00:00", 
        "price": 1
    }
    ```
  
## Le fonctionnement :

Page sur le site web qui peut assumer un nombre de connection (shotgun), modulable par l'appro biere ou par tout les membres du pic.

Le principe est qu'on pourra en mode administrateur définir un article et remplir donc les éléments 
(nom de l'article, description, nombres d'articles disponible etc...) puis le mettre en ligne (discuter de la possibilité de définir une date de shotgun)

Le client pourra donc aller sur le site du pic et accéder au paiement de l'article (service de paiement), redirection vers payutc
une fois l'article payé, on devrait pouvoir accéder aux données du client en lien avec l'article pour envoyer un mail
avec plus d'informations. 

## Lien avec le site du Pic'asso:

penser à integrer le lien du shotgun ici dans la doc quand c'est fait

Modulable par l'appro bière ou autre