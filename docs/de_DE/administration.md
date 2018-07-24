Hier befindet sich die Mehrheit der Konfigurationsparameter. 
Obwohl viele standardmäßig vorkonfiguriert sind.

Die Seite ist über **Einstellungen → Konfiguration erreichbar**.

Allgemein 
=======

Diese Registerkarte enthält allgemeine Informationen zu NextDom :

-   **Namen von Ihrem NextDom** : Ermöglicht Ihr NextDom zu Identifiziert
    besonders auf dem Markt. Es kann in Szenarien wieder verwendet werden
    oder um ein Backup zu identifizieren.

-   **System** : Der Hardwaretyp, auf dem das System installiert ist, 
    auf dem Ihr NextDom ausgeführt wird.

-   **Installationsschlüssel** : Hardwareschlüssel Ihres NextDom auf
    dem Markt. Sollte Ihr NextDom nicht in der Liste Ihrer
    NextDom auf dem Markt erscheinen, ist es ratsam, auf den Button
    **Rücksetzen** zu klicken.

-   **Sprache** : Sprache, die in Ihrem NextDom verwendet wird.

-   **Übersetzungen generieren** : Ermöglicht es, Übersetzungen zu generieren,
    Achtung, das kann Ihr System verlangsamen. Besonders nützliche Option
    für die Entwickler.

-   **Cache Verfallszeit (Stunde)** : Lebensdauer der PHP-Sitzungen,
    es wird empfohlen, diesen Parameter nicht zu ändern.

-   **Datum und Uhrzeit** : Wählen Sie Ihre Zeitzone. Sie können auf 
    **Zeit-Synchronisierung erzwingen** klicken, um eine falsche Zeit wiederherzustellen,
    die oben rechts angezeigt wird.

-   **Zeitserver optional** : Gibt an, welcher Zeitserver verwendet 
    werden soll, wenn Sie auf **Zeitsynchronisierung 
    erzwingen** klicken. (für Experten reserviert)

-   **Die Überprüfung der Zeit ignorieren** : Weist NextDom an, nicht 
    zu überprüfen, ob die Zeit zwischen sich und dem System, 
    auf dem es ausgeführt wird, übereinstimmend ist. Dies kann zum Beispiel nützlich sein, 
    wenn Sie NextDom nicht mit dem Internet verbinden und auf der 
    verwendeten Hardware keinen RTC-Batterie haben.

API 
===

Hier finden Sie eine Liste der verschiedenen API-Schlüssel, die in
Ihrem NextDom verfügbar sind. Die Grundlage, der Kern verfügt über zwei Haupt-APIs :

-   Ein Allgemeiner : Soweit wie möglich muss man vermeiden, ihn zu benutzen.

-   Und ein weiteres für Profis : Wird für die Paketverwaltung 
    verwendet. Es kann leer sein.

-   Dann finden Sie bei Bedarf einen API-Schlüssel pro Plugin.

Für jeden Plugin API-Schlüssel, sowie für die http-, JsonRPC- und TTS-APIs
können Sie Ihren  Anwendungsbereich definieren :

-   **Deaktivieren** : Der API-Schlüssel kann nicht verwendet werden.

-   **White IP** : Nur eine Liste von IPs ist erlaubt (siehe
    Administration → Konfiguration → Netzwerke).

-   **Localhost** : nur Anfragen von dem System, auf dem NextDom 
    installiert ist, sind erlaubt.

-   **Aktiv** : keine Einschränkungen, jedes System mit Zugriff 
    auf Ihr NextDom kann auf diese API zugreifen.

> \ _OS / DB 
===========

In dieser Registerkarte befinden sich zwei für Experten reservierte Bereiche.

> **Wichtig**
>
> Achtung : Wenn Sie NextDom mit einer von diesen zwei Optionen ändern,
> kann der Support ablehnen, Ihnen zu helfen.

-   **>_System** : Ermöglicht den Zugriff auf eine System
    Verwaltungsschnittstelle. Es ist eine Art Shell-Konsole, in der Sie die 
    nützlichsten Befehle starten können, insbesondere um Informationen 
    über das System zu erhalten.

-   **Datenbank** : Ermöglicht den Zugriff auf die NextDom Datenbank.
    Sie können dann Befehle im oberen Feld starten.
    Zwei Parameter werden unten zur Information angezeigt :

    -   **Benutzer** : Der Name des Benutzers, der von NextDom in der 
        Datenbank verwendet wird.

    -   **Passwort** : Passwort für den Zugriff auf die von NextDom 
        verwendete Datenbank.

Sicherheit 
========

LDAP 
----

-   **LDAP-Authentifizierung aktivieren** : aktiviert die Authentifizierung 
    über AD (LDAP)

-   **Host** : Server-Host des AD

-   **Domaine** : Ihr AD-Domaine

-   **Basis-DN** : Basis-DN ihres AD

-   **Benutzername** : Benutzernamen für die Verbindung von NextDom
    mit dem AD

-   **Passwort** : Passwort für die Verbindung von NextDom mit dem AD

-   **Champs recherche utilisateur** : champs de recherche du login utilisateur.
    login utilisateur. En général uid pour LDAP, samaccountname pour
    Windows AD

-    **Filter (optional)** : Filter auf dem AD (zum Beispiel für 
    Gruppenmanagement)

-   **Autoriser REMOTE\_USER** : Active le REMOTE\_USER (utilisé en SSO
    verwendet in SSO)

Verbindung 
---------

-   **Anzahl der tolerierten Fehler** : Legt die Anzahl der aufeinander folgenden Versuche fest, bevor die IP verboten wird.
    successives autorisées avant de bannir l’IP

-   **Maximale Zeit zwischen Fehlern (in Sekunden)** : maximale Zeit für 2 
    Versuche, die als aufeinanderfolgend betrachtet werden

-   **Dauer der Verbannung (in Sekunden), -1 für die Unendlichkeit** : IP-
    Verbannungs Zeit

-   **"white" IP** : Liste von IPs, die niemals gesperrt werden können.

-   **Verbannte IP löschen** : Wird verwendet, um die Liste der derzeit 
    gesperrten IPs zu löschen.

Die Liste der gesperrten IPs befindet sich am Ende dieser Seite. Sie finden
dort die IP, das Sperrdatum und das programmierte Enddatum des
Verbannung.

Netzwerke
=======

Es ist zwingend notwendig, diesen wichtigen Teil von NextDom richtig zu
konfigurieren, sonst funktionieren viele Plugins möglicherweise nicht. Es gibt
zwei Möglichkeiten, auf NextDom zuzugreifen : **Interner Zugriff** (vom selben
lokalen Netzwerk wie NextDom) und **externer Zugriff** (von einem anderen
Netzwerk, insbesondere vom Internet).

> **Wichtig**
>
> Dieser Teil ist nur dazu da um NextDom seine Umgebung zu erklären :
> Wenn Sie den Port oder die IP-Adresse in dieser Registerkarte ändern, 
> ändert sich der Port oder die IP von NextDom nicht. Dazu müssen Sie sich 
> mit SSH verbinden und die Dateien /etc/network/interfaces für die IP und
> etc/apache2/sites-available/default und
> etc/apache2/sites-available/default_ssl (für HTTPS) bearbeiten. Im Falle
> einer unsachgemäßen Bearbeitung Ihres NextDom wird das NextDom-Team
> jedoch nicht haftbar gemacht und kann jegliche Support-Anfrage
> ablehnen.

-   **Interner Zugriff** : Daten zum verbinden von NextDom mit einem anderen 
    Gerät, welches in dem selben Netzwerk wie NextDom ist (LAN).

    -   **OK/NOK** : Zeigt an, ob die interne Netzwerkkonfiguration 
        richtig ist.

    -   **Protokolle** : Das zu verwendende Protokoll, meistens HTTP.

    -   **URL oder IP Adresse** : Die NextDom IP-Adresse eintragen.

    -   **Port** : Der Port des Webinterface von NextDom, allgemein 80.
        Attention changer le port ici ne change pas le port réel de
        NextDom qui restera le même

    -   **Complément** : le fragment d’URL complémentaire (exemple
        : /nextdom) pour accéder à NextDom.

-   **Accès externe** : informations pour joindre NextDom de l’extérieur
    du réseau local. À ne remplir que si vous n’utilisez pas le DNS
    NextDom

    -   **OK/NOK** : indique si la configuration réseau externe est
        correcte

    -   **Protokolle** : verwendetes Protokoll für den Zugriff von außen 

    -   **Adresse URL ou IP** : IP externe, si elle est fixe. Sinon,
        donnez l’URL pointant sur l’adresse IP externe de votre réseau.

    -   **Complément** : le fragment d’URL complémentaire (exemple
        : /nextdom) pour accéder à NextDom.

> **Tip**
>
> Si vous êtes en HTTPS le port est le 443 (par défaut) et en HTTP le
> port est le 80 (par défaut). Pour utiliser HTTPS depuis l’extérieur,
> un plugin letsencrypt est maintenant disponible sur le market.

> **Tip**
>
> Pour savoir si vous avez besoin de définir une valeur dans le champs
> **complément**, regardez, quand vous vous connectez à NextDom dans
> votre navigateur Internet, si vous devez ajouter /nextdom (ou autre
> chose) après l’IP.

-   **Gestion avancée** : Cette partie peut ne pas apparaitre, en
    fonction de la compatibilité avec votre matériel. Vous y trouverez
    la liste de vos interfaces réseaux. Vous pourrez indiquer à NextDom
    de ne pas monitorer le réseau en cliquant sur **désactiver la
    gestion du réseau par NextDom** (à cocher si NextDom n’est connecté à
    aucun réseau)

-   **Proxy market** : permet un accès distant à votre NextDom sans avoir
    besoin d’un DNS, d’une IP fixe ou d’ouvrir les ports de votre box
    Internet

    -   **Utiliser les DNS NextDom** : active les DNS NextDom (attention
        cela nécessite au moins un service pack)

    -   **Statut DNS** : statut du DNS HTTP

    -   **Gestion** : permet d’arrêter et relancer le service DNS

> **Important**
>
> Si vous n’arrivez pas à faire fonctionner le DNS NextDom, regardez la
> configuration du pare-feu et du filtre parental de votre box Internet
> (sur livebox il faut par exemple le pare-feu en moyen).

Couleurs 
========

La colorisation des widgets est effectuée en fonction de la catégorie à
laquelle appartient l’équipement. Parmi les catégories on retrouve le
chauffage, Sécurité, Energie, lumière, Automatisme, Multimedia, Autre…​

Pour chaque catégorie, on pourra différencier les couleurs de la version
desktop et de la version mobile. On peut alors changer :

-   la couleur du fond des widgets,

-   la couleur de la commande lorsque le widget est de type graduel (par
    exemple les lumières, les volets, les températures).

En cliquant sur la couleur une fenêtre s’ouvre, permettant de choisir sa
couleur. La croix à côté de la couleur permet de revenir au paramètre
par défaut.

En haut de page, vous pouvez aussi configurer la transparence des
widgets de manière globale (ce sera la valeur par défaut. Il est
possible ensuite de modifier cette valeur widget par widget). Pour ne
mettre aucune transparence, laissez 1.0 .

> **Tip**
>
> N’oubliez pas de sauvegarder après toute modification.

Commandes 
=========

De nombreuses commandes peuvent être historisées. Ainsi, dans
Analyse→Historique, vous obtenez des graphiques représentant leur
utilisation. Cet onglet permet de fixer des paramètres globaux à
l’historisation des commandes.

Historique 
----------

-   **Afficher les statistiques sur les widgets** : Permet d’afficher
    les statistiques sur les widgets. Il faut que le widget soit
    compatible, ce qui est le cas pour la plupart. Il faut aussi que la
    commande soit de type numérique.

-   **Période de calcul pour min, max, moyenne (en heures)** : Période
    de calcul des statistiques (24h par défaut). Il n’est pas possible
    de mettre moins d’une heure.

-   **Période de calcul pour la tendance (en heures)** : Période de
    calcul des tendances (2h par défaut). Il n’est pas possible de
    mettre moins d’une heure.

-   **Délai avant archivage (en heures)** : Indique le délai avant que
    NextDom n’archive une donnée (24h par défaut). C’est-à-dire que les
    données historisées doivent avoir plus de 24h pour être archivées
    (pour rappel, l’archivage va soit moyenner, soit prendre le maximum
    ou le minimum de la donnée sur une période qui correspond à la
    taille des paquets).

-   **Archiver par paquet de (en heures)** : Ce paramètre donne
    justement la taille des paquets (1h par défaut). Cela signifie par
    exemple que NextDom va prendre des périodes de 1h, moyenner et
    stocker la nouvelle valeur calculée en supprimant les
    valeurs moyennées.

-   **Seuil de calcul de tendance basse** : Cette valeur indique la
    valeur à partir de laquelle NextDom indique que la tendance est à
    la baisse. Il doit être négatif (par défaut -0.1).

-   **Seuil de calcul de tendance haut** : Même chose pour la hausse.

-   **Période d’affichage des graphiques par défaut** : Période qui est
    utilisée par défaut lorsque vous voulez afficher l’historique
    d’une commande. Plus la période est courte, plus NextDom sera rapide
    pour afficher le graphique demandé.

> **Note**
>
> Le premier paramètre **Afficher les statistiques sur les widgets** est
> possible mais désactivé par défaut car il rallonge sensiblement le
> temps d’affichage du dashboard. Si vous activez cette option, par
> défaut, NextDom se fonde sur les données des dernières 24h pour
> calculer ces statistiques. La méthode de calcul de tendance est fondée
> sur le calcul des moindres carrés (voir
> [ici](https://fr.wikipedia.org/wiki/M%C3%A9thode_des_moindres_carr%C3%A9s)
> pour le détail).

Push 
----

**URL de push globale** : permet de rajouter une URL à appeler en cas de
mise à jour d’une commande. Vous pouvez utiliser les tags suivants :
**\#value\#**pour la valeur de la commande,**\#cmd\_name\#** pour le
nom de la commande, **\#cmd\_id\#** pour l’identifiant unique de la
commande, **\#humanname\#** pour le nom complet de la commande (ex :
\#\[Salle de bain\]\[Hydrometrie\]\[Humidité\]\#)

Cache 
=====

Permet de surveiller et d’agir sur le cache de NextDom :

-   **Statistiques** : Nombre d’objets actuellement en cache

-   **Nettoyer le cache** : Force la suppression des objets qui ne sont
    plus utiles. NextDom le fait automatiquement toutes les nuits.

-   **Vider toutes les données en cache** : Vide complètement le cache.
    Attention cela peut faire perdre des données !

-   **Temps de pause pour le long polling** : Fréquence à laquelle
    NextDom vérifie si il y a des événements en attente pour les clients
    (interface web, application mobile…​). Plus ce temps est court, plus
    l’interface se mettra à jour rapidement, en contre partie cela
    utilise plus de ressources et peut donc ralentir NextDom.

Interactions 
============

Cet onglet permet de fixer des paramètres globaux concernant les
interactions que vous trouverez dans Outils→Interactions.

> **Tip**
>
> Pour activer le log des interactions, il faut aller dans l’onglet
> Administration→Configuration→Logs, puis cocher **Debug** dans la liste
> du bas. Attention : les logs seront alors très verbeux !

Général 
-------

Vous avez ici trois paramètres :

-   **Sensibilité** : il y a 4 niveaux de correspondance (La sensibilité
    va de 1 (correspond exactement) à 99)

    -   pour 1 mot : le niveau de correspondance pour les interactions à
        un seul mot

    -   2 mots : le niveau de correspondance pour les interactions à
        deux mots

    -   3 mots : le niveau de correspondance pour les interactions à
        trois mots

    -   + de 3 mots : le niveau de correspondance pour les interactions
        à plus de trois mots

-   **Ne pas répondre si l’interaction n’est pas comprise** : par défaut
    NextDom répond "je n’ai pas compris" si aucune interaction
    ne correspond. Il est possible de désactiver ce fonctionnement pour
    que NextDom ne réponde rien. Cochez la case pour désactiver
    la réponse.

-   **Regex général d’exclusion pour les interactions** : permet de
    définir une regexp qui, si elle correspond à une interaction,
    supprimera automatiquement cette phrase de la génération (réservé
    aux experts). Pour plus d’informations voir les explications dans le
    chapitre **Regexp d’exclusion** de la documentation sur
    les interactions.

Interaction automatique, contextuelle & avertissement 
-----------------------------------------------------

-   Les **interactions automatiques** permettent à NextDom de tenter de
    comprendre une demande d’interaction même si il n’y en a aucune
    de définie. Il va alors chercher un nom d’objet et/ou d’équipement
    et/ou de commande pour essayer de répondre au mieux.

-   Les **interactions contextuelles** vous permettent d’enchainer
    plusieurs demandes sans tout répéter, par exemple :

    -   *NextDom gardant le contexte :*

        -   *Vous* : Combien fait-il dans la chambre ?

        -   *NextDom* : Température 25.2 °C

        -   *Vous* : et dans le salon ?

        -   *NextDom* : Température 27.2 °C

    -   *Poser deux questions en une :*

        -   *Vous* : Combien fait-il dans la chambre et dans le salon ?

        -   *NextDom* : Température 23.6 °C, Température 27.2 °C

-   Les interactions de type **Préviens-moi** permettent de demander à
    NextDom de vous prévenir si une commande dépasse/descend ou vaut une
    certaine valeur.

    -   *Vous* : Préviens-moi si la température du salon dépasse 25°C ?

    -   *NextDom* : OK (*Dès que la température du salon dépassera 25°C,
        NextDom vous le dira, une seule fois*)

> **Note**
>
> Par défaut NextDom vous répondra par le même canal que celui que vous
> avez utilisé pour lui demander de vous prévenir. Si il n’en trouve
> pas, il utilisera alors la commande par défaut spécifiée dans cet
> onglet : **Commande de retour par défaut**.

Voici donc les différentes options disponibles :

-   **Activer les interactions automatiques** : Cochez pour activer les
    interactions automatiques.

-   **Activer les réponses contextuelles** : Cochez pour activer les
    interactions contextuelles.

-   **Réponse contextuelle prioritaire si la phrase commence par** : Si
    la phrase commence par le mot que vous renseignez ici, NextDom va
    alors prioritiser une réponse contextuelle (vous pouvez mettre
    plusieurs mots en les séparant par des **;** ).

-   **Découper une interaction en 2 si elle contient** : Même chose pour
    le découpage d’une interaction contenant plusieurs questions. Vous
    donnez ici les mots qui séparent les différentes questions.

-   **Activer les interactions "Préviens-moi"** : Cochez pour activer
    les interactions de type **Préviens-moi**.

-   **Réponse de type "Préviens-moi" si la phrase commence par** : Si la
    phrase commence par ce/ces mot(s) alors NextDom cherchera à faire une
    interaction de type **Préviens-moi** (vous pouvez mettre plusieurs
    mots en les séparant par des **;** ).

-   **Commande de retour par défaut** : Commande de retour par défaut
    pour une interaction de type **Préviens-moi** (utilisée, notamment,
    si vous avez programmé l’alerte par l’interface mobile)

-   **Synonyme pour les objets** : Liste des synonymes pour les objets
    (ex : rdc|rez de chaussé|sous sol|bas;sdb|salle de bain).

-   **Synonyme pour les équipements** : Liste des synonymes pour
    les équipements.

-   **Synonyme pour les commandes** : Liste des synonymes pour
    les commandes.

-   **Synonyme pour les résumé** : Liste des synonymes pour les résumés.

-   **Synonyme commande slider maximum** : Synonyme pour mettre une
    commande de type slider au maximum (ex ouvre pour ouvre le volet de
    la chambre ⇒ volet chambre à 100%).

-   **Synonyme commande slider minimum** : Synonyme pour mettre une
    commande de type slider au minimu (ex ferme pour fermer le volet de
    la chambre ⇒ volet chambre à 0%).

Couleurs 
--------

Cette partie permet de définir les couleurs que NextDom associera aux
mots rouge/bleu/noir…​ Pour ajouter une couleur :

-   Cliquez sur le bouton **+**, à droite,

-   Donnez un nom à votre couleur,

-   Choisissez la couleur associée en cliquant sur la case de droite.

Rapports 
========

Permet de configurer la génération et la gestion des rapports

-   **Délai d’attente après génération de la page (en ms)** : Délai
    d’attente après chargement du rapport pour faire la "photo", à
    changer si votre rapport est incomplet par exemple.

-   **Nettoyer les rapports plus anciens de (jours)** : Définit le
    nombre de jours avant de supprimer un rapport (les rapports prennent
    un peu de place donc attention à ne pas mettre trop
    de conservation).

Liens 
=====

Permet de configurer les graphiques de liens. Ces liens permettent de
voir, sous forme d’un graphique, les relations entre les objets, les
équipements, les objets, etc.

-   **Profondeur pour les scénarios** : Permet de définir, lors de
    l’affichage d’un graphique de liens d’un scénario, le nombre
    d’éléments maximum à afficher (plus il y a d’éléments plus le
    graphique sera lent à générer et plus il sera difficile à lire).

-   **Profondeur pour les objets** : Idem pour les objets.

-   **Profondeur pour les équipements** : Idem pour les équipements.

-   **Profondeur pour les commandes** : Idem pour les commandes.

-   **Profondeur pour les variables** : Idem pour les variables.

-   **Paramètre de prerender** : Permet d’agir sur la disposition
    du graphique.

-   **Paramètre de render** : Idem.

Résumés 
=======

Permet d’ajouter des résumés d’objets. Cette information est affichée
tout en haut, à droite, dans la barre de menu NextDom, ou à côté des
objets :

-   **Clef** : Clé du résumé, à ne surtout pas toucher.

-   **Nom** : Nom du résumé.

-   **Calcul** : Méthode de calcul, peut être de type :

    -   **Somme** : fait la somme des différentes valeurs,

    -   **Moyenne** : fait la moyenne des valeurs,

    -   **Texte** : affiche textuellement la valeur (surtout pour celles
        de type chaine de caractères).

-   **Icone** : Icône du résumé.

-   **Unité** : Unité du résumé.

-   **Méthode de comptage** : Si vous comptez une donnée binaire alors
    il faut mettre cette valeur à binaire, exemple si vous comptez le
    nombre de lampes allumées mais que vous avez juste la valeur du
    variateur (0 à 100), alors il faut mettre binaire, comme cela NextDom
    considéra que si la valeur est supérieure à 1, alors la lampe
    est allumée.

-   **Afficher si valeur égale 0** : Cochez cette case pour afficher la
    valeur, même quand elle vaut 0.

-   **Lier à un virtuel** : Lance la création de commandes virtuelles
    ayant pour valeur celles du résumé.

-   **Supprimer le résumé** : Le dernier bouton, tout à droite, permet
    de supprimer le résumé de la ligne.

Logs 
====

Timeline 
--------

-   **Nombre maximum d’évènements** : Définit le nombre maximum à
    afficher dans la timeline.

-   **Supprimer tous les évènements** : Permet de vider la timeline de
    tous ses évènements enregistrés.

Messages 
--------

-   **Ajouter un message à chaque erreur dans les logs** : si un plugin
    ou NextDom écrit un message d’erreur dans un log, NextDom ajoute
    automatiquement un message dans le centre des messages (au moins
    vous êtes sûr de ne pas le manquer).

-   **Commande d’information utilisateur** : Permet de sélectionner une
    ou plusieurs commandes (à séparer par des **&&** ) de type
    **message** qui seront utilisées lors de l’émission de
    nouveaux messages.

Alertes 
-------

-   **Ajouter un message à chaque Timeout** : Ajoute un message dans le
    centre de message si un équipement tombe en **timeout**.

-   **Commande sur Timeout**: Commande de type**message** à utiliser
    si un équipement est en **timeout**.

-   **Ajouter un message à chaque Batterie en Warning** : Ajoute un
    message dans le centre de messages si un équipement a son niveau de
    batterie en **warning**.

-   **Commande sur Batterie en Warning**: Commande de type**message**
    à utiliser si un équipement à son niveau de batterie en **warning**.

-   **Ajouter un message à chaque Batterie en Danger** : Ajoute un
    message dans le centre de messages si un équipement à son niveau de
    batterie en **danger**.

-   **Commande sur Batterie en Danger**: Commande de type**message** à
    utiliser si un équipement à son niveau de batterie en **danger**.

-   **Ajouter un message à chaque Warning** : Ajoute un message dans le
    centre de messages si une commande passe en alerte **warning**.

-   **Commande sur Warning**: Commande de type**message** à utiliser
    si une commande passe en alerte **warning**.

-   **Ajouter un message à chaque Danger** : Ajoute un message dans le
    centre de messages si une commande passe en alerte **danger**.

-   **Commande sur Danger**: Commande de type**message** à utiliser si
    une commande passe en alerte **danger**.

Log 
---

-   **Moteur de log** : Permet de changer le moteur de log pour, par
    exemple, les envoyer à un demon syslog(d).

-   **Format des logs** : Format de log à utiliser (Attention : ça
    n’affecte pas les logs des démons).

-   **Nombre de lignes maximum dans un fichier de log** : Définit le
    nombre maximum de lignes dans un fichier de log. Il est recommandé
    de ne pas toucher cette valeur, car une valeur trop grande pourrait
    remplir le système de fichiers et/ou rendre NextDom incapable
    d’afficher le log.

-   **Niveau de log par défaut** : Quand vous sélectionnez "Défaut",
    pour le niveau d’un log dans NextDom, c’est celui-ci qui sera
    alors utilisé.

En dessous vous retrouvez un tableau permettant de gérer finement le
niveau de log des éléments essentiels de NextDom ainsi que celui des
plugins.

Equipements 
===========

-   **Nombre d’échecs avant désactivation de l’équipement** : Nombre
    d’échecs de communication avec l’équipement avant désactivation de
    celui-ci (un message vous préviendra si cela arrive).

-   **Seuils des piles** : Permet de gérer les seuils d’alertes globaux
    sur les piles.

Mise à jour et fichiers 
=======================

Mise à jour de NextDom 
---------------------

-   **Source de mise à jour** : Choisissez la source de mise à jour du
    core de NextDom.

-   **Version du core** : Version du core à récupérer.

-   **Vérifier automatiquement s’il y a des mises à jour** : Indique si
    il faut chercher automatiquement si il y a de nouvelles mises à jour
    (attention pour éviter de surcharger le market, l’heure de
    vérification peut changer).

Les dépôts 
----------

Les dépôts sont des espaces de stockage (et de service) pour pouvoir
déplacer des sauvegardes, récupérer des plugins, récupérer le core de
NextDom, etc.

### Fichier 

Dépôt servant à activer l’envoi de plugins par des fichiers.

### Github 

Dépôt servant à relier NextDom à Github.

-   **Token** : Token pour l’accès au dépôt privé.

-   **Utilisateur ou organisation du dépôt pour le core NextDom** : Nom
    de l’utilisateur ou de l’organisation sur github pour le core.

-   **Nom du dépôt pour le core NextDom** : Nom du dépôt pour le core.

-   **Branche pour le core NextDom** : Branche du dépôt pour le core.

### Market 

Dépôt servant à relier NextDom au market, il est vivement conseillé
d’utiliser ce dépôt. Attention : toute demande de support pourra être
refusée si vous utilisez un autre dépôt que celui-ci.

-   **Adresse** : Adresse du Market.

-   **Nom d’utilisateur** : Votre nom d’utilisateur sur le Market.

-   **Mot de passe** : Votre mot de passe du Market.

### Samba 

Dépôt permettant d’envoyer automatiquement une sauvegarde de NextDom sur
un partage Samba (ex : NAS Synology).

-   **\[Backup\] IP** : IP du serveur Samba.

-   **\[Backup\] Utilisateur** : Nom d’utilisateur pour la connexion
    (les connexions anonymes ne sont pas possibles). Il faut forcément
    que l’utilisateur ait les droits en lecture ET en écriture sur le
    répertoire de destination.

-   **\[Backup\] Mot de passe** : Mot de passe de l’utilisateur.

-   **\[Backup\] Partage** : Chemin du partage (attention à bien
    s’arrêter au niveau du partage).

-   **\[Backup\] Chemin** : Chemin dans le partage (à mettre en
    relatif), celui-ci doit exister.

> **Note**
>
> Si le chemin d’accès à votre dossier de sauvegarde samba est :
> \\\\192.168.0.1\\Sauvegardes\\Domotique\\NextDom Alors IP = 192.168.0.1
> , Partage = //192.168.0.1/Sauvegardes , Chemin = Domotique/NextDom

> **Note**
>
> Lors de la validation du partage Samba, tel que décrit précédemment,
> une nouvelle forme de sauvegarde apparait dans la partie
> Administration→Sauvegardes de NextDom. En l’activant, NextDom procèdera
> à son envoi automatique lors de la prochaine sauvegarde. Un test est
> possible en effectuant une sauvegarde manuelle.

> **Important**
>
> Il vous faudra peut-être installer le package smbclient pour que le
> dépôt fonctionne.

> **Important**
>
> Le protocole Samba comporte plusieurs versions, la v1 est compromise niveau 
> sécurité et sur certains NAS vous pouvez obliger le client à utiliser la v2
> ou la v3 pour se connecter. Donc si vous avez une erreur protocol negotiation
> failed: NT_STATUS_INVAID_NETWORK_RESPONSE il y a de forte chance que coté NAS
> la restriction soit en place. Vous devez alors modifier sur l'OS de votre NextDom
> le fichier /etc/samba/smb.conf et y ajouter ces deux lignes :
> client max protocol = SMB3
> client min protocol = SMB2
> Le smbclient coté NextDom utilisera alors v2 où v3 et en mettant SMB3 aux 2 uniquement
> SMB3. A vous donc d'adapter en fonction des restrictions côté NAS ou autre serveur Samba

> **Important**
>
> NextDom doit être le seul à écrire dans ce dossier et il doit être vide
> par défaut (c’est-à-dire qu’avant la configuration et l’envoi de la
> première sauvegarde, le dossier ne doit contenir aucun fichier ou
> dossier).

### URL 

-   **URL NextDom Kern**

-   **URL NextDom Kern Version**


