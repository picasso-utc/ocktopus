@php
    use \Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Informations de la Perm</title>
</head>
<body>
<p>Helloooo hellooooo :))</p>

<p>Si vous lisez ce mail, c’est que vous avez franchi la première étape pour devenir de super permanencier.e.s du PIC ! 🎉 Votre perm <strong>{{ $record->nom }}</strong> a été sélectionnée !</p>

<p>Vous avez demandé à l'équipe animation du Pic'Asso de tenir des permanences ce semestre. Petit rappel : le Pic'Asso est un foyer étudiant, ouvert toute la journée, et tenir une perm signifie être présent matin, midi et soir. On a fait de notre mieux pour respecter vos préférences et voici les créneaux qui vous sont attribués :</p>

<h3>Votre journée : </h3>{{ Carbon::parse($creneaux->first()->date)->translatedFormat('d F Y') }}<br>



<h3>Quelques petits rappels pour que tout roule :</h3>

<ol>
    <li><strong>La bouffe, c’est banger :</strong> En vrai y’a pas d’obligation de préparer un repas pour les perms du midi (sauf si c’est un mercredi, jeudi) et/ou un gouter pour l’aprem. Maiiiis si vous êtes motivés faites vous plaisir ! Il faudra juste nous dire à l’avance ce que vous voulez qu’on puisse le commander 😉 Si vous êtes une asso, et que vous souhaitez récupérer les bénéfices de vos ventes c’est aussi possible : dans ce cas, vous gérez les courses ET la demande de CAT au SIMDE ! ⚠️<strong>IMPORTANT :</strong> il faut toujours TOUT cuisiner au Pic pour des questions d’hygiène !!⚠️</li>

    <li><strong>Perm du soir, point crucial :</strong> N'oubliez pas de ramener un chèque de caution de 200€ à l’ordre de PIC'ASSO le jour de votre perm.</li>

    <li><strong>On ne rigole pas avec ça :</strong> La vente d’alcool autre que celui fourni par le Pic'Asso est strictement interdite, sauf autorisation (et c’est rare !). Les bouteilles sont consignées et AUCUNE bière ou Ecocup ne doit sortir de l'enceinte du PICASSO. 🍻🚫</li>

    <li><strong>La ponctualité, c’est la clé :</strong> Votre présence est obligatoire pour le briefing de l'astreinteur ou de l’astreintrice, aux horaires suivants : 9h45 (perm du matin), 12h00 (perm du midi, ou à partir de 10h si vous préparez un repas (ça dépend du repas évidemment)), et 17h30-18h00 max (perm du soir). Soyez là à l'heure pour installer la déco et recevoir les consignes !</li>

    <li><strong>Comportement exemplaire :</strong> Un permanencier est toujours sobre et respectueux. Tout dérapage sera sévèrement sanctionné. On compte sur vous pour garder le Pic en bon état et préserver l'ambiance. ✌️</li>

    <li><strong>L’heure c’est l’heure :</strong> La vente d’alcool est autorisée de 18h30 à 21h30. À 21h56, on lance la Traviata et l’allumage des lumières, puis on évacue la salle et la terrasse. À 23h, tout doit être nettoyé et tout le monde dehors. Pendant l'évacuation du pic : gardez votre calme et n'oubliez pas le gilet jaune qui vous sera remis par l'astreinteur.ice. Les abords du Pic doivent être dégagés très rapidement (sur le trottoir tout le long et sur le parvis de BF) pour éviter le bruit et ainsi les plaintes des voisins.</li>

    <li><strong>Lâchez-vous sur la déco et les animations ! :</strong> Uniquement pour les groupes de potes, vous avez un budget de 20€ pour dynamiser notre cher foyer. N'oubliez pas de mettre la facture à l'ordre de PIC'ASSO. Que vous soyez une asso ou juste un groupe de potes, éclatez-vous, faites-nous rêver !</li>

    <li><strong>Concours de qualité :</strong> À la fin du semestre, les assos les plus créatives et engagées seront récompensées lors du repas des permanenciers (RDP). Des prix seront remis, alors faites chauffer la déco, les animations et le menu ! 🔥🏆</li>

    <li><strong>La collaboration avant tout !</strong>Beaucoup d’asso peuvent se rendre disponibles pour vous aider à proposer des bangers pendant vos perms (Décibels, Picsart, FSC, Larsen, Cinemut, les assos de danse, etc…) n’hésitez pas à les contacter si vous le souhaitez (au moins 2 semaines avant pour des questions logistiques). Et si vous ne savez pas comment faire, ou ce qui est possible ou non au Pic, n’hésitez pas à nous le dire dès la création du groupe messenger, on est là pour vous aider!</li>

    <li><strong>Jeudi soir uniquement :</strong>La safe zone stop VSS est mise en place tous les jeudi soirs à partir de 20h, ce qui signifie que les permancier.esformé.es devront assurer des perm Safe Zone dans la soirée.</li>
</ol>
    <h3>En plus, voici quelques conseils pratiques :</h3>

    <ul>
        <li><strong>Sécurité d’abord !:</strong> Toujours mettre 2 personnes en sécu pente et 2 en sécu escaliers tout au long de la soirée (les gens peuvent changer, pas de stress). ⚠️ Ne jamais boire en sécu !!</li>
        <li><strong>Traviata en vue :</strong> Après la Traviata, ajoutez 3-4 personnes en sécu trottoir, en fonction de l'affluence attendue.</li>
        <li><strong>Perm caisse :</strong> Gardez toujours un permanencier à la caisse pour ramasser les écocups et bouteilles.</li>
        <li><strong>Imprimez et partagez :</strong> Un planning vous a été mis en pièce jointe, celui-ci doit être rempli en tenant compte des remarques ci-dessus. Le planning doit être imprimé en 4 exemplaires (possible avec notre super imprimante !), allez vers nous pour l’imprimante et n'oubliez pas de le prendre en photo pour bien vous repérer. Partagez-le aussi dans votre groupe !</li>
        <li><strong>Nouveau :</strong>Un générateur de planning est désormais disponible sur le site (<a href="https://pic.assos.utc.fr/planning-generator">https://pic.assos.utc.fr/planning-generator</a>) pour faciliter la création de plannings (vérifiez quand même que le planning est good au cas où). Une fois le planning créé, vous pouvez le télécharger en Excel pour le modifier.</li>
    </ul>

    <p>N’oubliez pas qu’en cas de doute, vous pouvez demander de l’aide à l’astreinteur ou à l’astreintrice, on est là pour ça :)</p>

    <p><strong>Dernière chose ultra importante:</strong> CONFIRMEZ-NOUS par retour de mail que vous avez bien reçu et lu ce message !</p>

    <p>Voilà, c’est tout pour le moment. Si vous avez des questions, des requêtes ou des propositions, foncez et envoyez-nous un petit mail à <a href="mailto:team.anim.picasso@gmail.com">team.anim.picasso@gmail.com</a>.</p>

    <p>Le PIC’ASSO qui vous aime ❤️</p>
</body>
</html>
