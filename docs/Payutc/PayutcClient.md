# Service Payutc

**Il est important de lire tout la doc payutc pour comprendre comment marche le service et quels sont les problèmes rencontrés (pour ne pas les rerecontrés)**

Le service PayutcClient permet d'interagir avec l'API Payutc de weez pour obtenir des informations sur les utilisateurs et les badges. Cette documentation explique les méthodes disponibles dans ce service, ainsi que la manière de l'utiliser dans des contrôleurs.

**Vous pouvez trouver le code ici : app/Services/PayutcClient.php**
### Initialisation du Service
Le service nécessite l'initialisation avec les informations APP_KEY, SYSTEM_ID, PASSWORD, LOGIN, FUNDATION_ID, API_URL. Ces informations sont récupérées à partir des variables d'environnement. Assurez-vous que ces variables d'environnement sont définies dans votre configuration (fichier .env).

```php
use App\Services\PayUtcClient;

// Initialisation du service PayUtcClient

    private PayUtcClient $client;

    public function __construct(PayUtcClient $client)
    {
        $this->client = $client;
    }
```

### Méthodes du Service PayutcClient
**1. `makePayutcRequest($method, $endpoint, $options = []): qui renvoit un json`**

Cette méthode permet de faire n'importe quel requête vers la nouvelle API de weezpay (documentation ici : https://docapi.weezevent.com/openapi.html?weezpay).

Paramètres :

`$method` : la méthode que l'on souhaite utiliser dans notre requête (POST, GET etc...) à priori presque toujours GET.

`$endpoint` : la terminaison de l'URL de la requête que l'on souhaite utiliser (voir sur la doc). 

`$option` : pour ajouter des querys paremeters que l'on peut voir dans la doc, par exemple ajouter des dates pour faire une requête plus précise.

**Attention il y a parfois des path parameters, ceux la ne vont pas dans option mais dans endpoint**

Exemple d'utilisation :

```php
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => "2023-11-15T07:15:00.000000Z",
            'created__lt' => "2023-11-15T16:10:00.000000Z",
        ]);
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
```

**2. `getSession(): string`**

Cette méthode permet de récupérer un id de session qui aura les droits pour faire certaines requêtes à weez, cette méthode n'a pas besoin d'être utilisé.
En effet elle est intégré à la méthode précédente makePayutcRequest. 

Pour avoir cet id de session on utilise actuellement encore l'ancienne API (documentation ici : https://apidoc.nemopay.net/Base_Service/#loginApp)

Exemple d'utilisation :
```php
$session_id = $this->getSession();
```

### Utilisation dans un Contrôleur
Voici comment vous pouvez utiliser le service PayutcClient dans un contrôleur Laravel.

```php
use App\Services\PayUtcClient;

class GoodiesController extends Controller
{
    private PayUtcClient $client;

    public function __construct(PayUtcClient $client)
    {
        $this->client = $client;
    }

    public function getWinner()
    {
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => "2023-11-15T07:15:00.000000Z",
            'created__lt' => "2023-11-15T16:10:00.000000Z",
        ]);
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
    }
}
```
Dans l'exemple ci dessus $jsonData contient donc les données de réponses de la requête. 
Cet exemple ne sert qu'à faire une requête précises il peut être intéressant de mettre les options en paramêtres. 
Assurez-vous d'ajouter les routes correspondantes dans le fichier web.php ou api.php pour ces méthodes du contrôleur.

### Problèmes rencontrés

## Documentation de l'API
 
La nouvelle API n'est pas "terminée", il est donc vraiment compliqué de se fier à la doc dans l'état actuelle des choses. J'espère que cela sera réglé avec le temps. 

En attendant je me suis contenté d'essayer en suivant la doc même si certaines choses ne marchaient pas, comme avec un token de session, c'est pour ça que nous utilisons encore l'ancienne API pour avoir le token de session. 

L'objectif étant de diminuer l'utilisation de l'ancienne API nemopay pour ne plus en dépendre et utiliser la nouvelle weez.  

## Accès

Nous n'avons plus les mêmes droits sur la nouvelle API, chaque achat est relié à un wallet_id qui est relié à un user pour avoir les noms et prénoms des gagnants. Et pour le RGDP de nos clients c'est un proxy géré par le SIMDE qui fait le lien entre les deux (wallet_id et user)

Voici un exemple d'utilisation : 

```php
$user = Http::withHeaders([
            'X-Return-Structure' => 'array',
            'Content-Type' => 'application/json',
            'X-API-KEY' => env('PROXY_KEY')
        ])->post(env('PROXY_URL'), [
            'wallets' => $winners,
        ]);
```
ici donc nous allons envoyé une liste de wallet_id ($winner) et $user contient tout les users à la fin
