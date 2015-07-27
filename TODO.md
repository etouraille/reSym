**TODO**

* application 

- une page d'accueil scindant en deux : search / Add
- permette de fonctionner en mode non connecté ( reverse geo, dernière adresse )
- faire un zoom sur les resources, avec déroullement sur la gauche
- permettre l'utilisation de plusieurs hashtag 
- proposition de hashtag ou creation
- mettre en place des room de chat
- soigner l'UX
- reevoir le process d'authentification.
- rajouter une zone de commentaire.
- one localization on loading the application.
- a database for unread messages , with message identification.
- prevoir une carte des resource non authentifiée
- prevoir un mode d'utilisation anonyme
- prevoir de logguer les connexion


* API 
- cf README.md pour l'ajout de nouvelles données.
- realiser l'authentification par token pour le web.
- authentification par clef d'api : et log des action par api
- prevoir un processus d'authentification anonyme
- when localization is given fire an event : and change the search 
- we could also log the last positions.
- when a new tag is localized to a place , add it to the place : now it's added to the
- refactor the ReceiverCommand : only three option : update, index, percolate, and refinement with the headers
- new message is store to database, with identifier : to permit unread messages, stored localy
- localy : a hash or read messages.
- new message is sent with the identification of conversation
#web

- realiser un site web
- generation de page wiki à chaque creation de hashtag
- les liens entre les page de wiki permette de realiser des proximité de hashtag
- une page par hashtag de wiki
- accueil : une carto
- une timeline : des hashtag
- dans le header : un search mot clé, localisation. 
- une pagination.
- une page de détail
- une page d'ajout.
- plus tard : les hashtag les plus populaires.
- page developpeur : creation des comptes avec creation de token
- chat / conversation.

#Archi 
- un fichier de conf non disponible sur le repos
- injection de dépendance
- renomage de UserBundle 
- mise en place de test
- mettre en place cron de clean for oldToken
- mettre en place bakup de la base au format json
- cron de repeuplement d'elastic
- check that we can inject the right security manager
#Prospectif 

- utilisation d'open street map : cf nominatis php surcouche 
- système de retribution.


