# Flow d'authentification

Le flow d'authentification implémenté dans cette application utilise un service créé par le SIMDE basé sur OAuth. L'objectif de ce processus est d'authentifier les utilisateurs via un fournisseur d'identité externe et de générer un JSON Web Token (JWT) pour sécuriser les requêtes subséquentes.

## Middleware d'Authentification (Auth.php)

Le middleware `Auth` gère le processus d'authentification à chaque requête entrante. Voici un aperçu du fonctionnement du middleware:

1. **Vérification du Token dans le Cookie :** Le middleware vérifie d'abord la présence d'un JWT dans le cookie en utilisant le nom de cookie défini dans la configuration de l'application (`config('app.token_name')`).

2. **Redirection vers l'Authentification :** Si aucun token n'est trouvé, l'utilisateur est redirigé vers la route d'authentification (`auth_route`). La route actuelle est stockée dans un cookie pour permettre une redirection après l'authentification.

3. **Décodage du JWT :** Si un token est présent, le middleware tente de le décoder en utilisant une clé publique stockée localement. Les exceptions possibles, telles que l'expiration du token ou une signature invalide, sont gérées.

4. **Traitement des Erreurs :** En cas d'erreur lors du décryptage du token, des réponses JSON appropriées avec des codes d'erreur 401 sont renvoyées.

5. **Continuation de la Requête :** Si le token est valide, la requête est transmise au prochain middleware ou à la route suivante.

## Contrôleur d'Authentification (Connexion.php)

Le contrôleur `Connexion` gère le processus d'authentification avec le fournisseur d'identité externe (OAuth). Voici les étapes principales du processus:

1. **Initialisation du Fournisseur OAuth :** Un fournisseur OAuth générique est initialisé avec les informations nécessaires, telles que l'ID client, le secret client, les URI de redirection, et les URL d'autorisation et d'obtention de jeton.

2. **Obtention de l'URL d'Autorisation :** Si le code d'autorisation n'est pas présent dans la requête, l'utilisateur est redirigé vers l'URL d'autorisation du fournisseur OAuth pour démarrer le processus d'authentification.

3. **Échange du Code d'Autorisation :** Une fois que l'utilisateur est authentifié et autorise l'application, le code d'autorisation est échangé contre un jeton d'accès.

4. **Récupération des Informations Utilisateur :** Le jeton d'accès est utilisé pour obtenir des informations sur l'utilisateur, notamment ses associations avec des organisations.

5. **Traitement des Erreurs :** Les erreurs éventuelles, telles que l'échec de récupération des informations utilisateur, sont gérées avec des réponses JSON appropriées.

6. **Enregistrement de l'Utilisateur :** Si l'utilisateur est membre ou administrateur d'une organisation spécifique ('picasso' dans cet exemple), ses informations sont enregistrées localement dans la base de données.

7. **Création du Cookie JWT :** Un cookie contenant le jeton d'accès est créé et renvoyé avec une expiration de 24 heures.

8. **Redirection vers la Route d'Origine :** Si une route d'origine était stockée dans un cookie avant l'authentification, l'utilisateur est redirigé vers cette route après l'authentification.

