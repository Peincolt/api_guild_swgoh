# Projet API HGamers II - Description du projet, guide d'installation et documentation de l'API
## I) Présentation du projet
### Description du besoin
Star Wars Galaxy of Heroes (swgoh) est un jeu mobile développé par Capital Games et EA Mobile. Disponible sous Android, iOS et iPadOS, le jeu permet aux joueurs de collectionner les héros/vaisseaux les plus emblématiques 
de la saga Star Wars et de les utiliser dans différents modes de jeu. L'accés à ces modes de jeu est en général restreint par le niveau d'équipement des personnages/vaisseaux et l'appartenance ou non à une guilde. Une fois le niveau d'équipement atteint, le joueur doit 
doit former une équipe de 5 héros/vaisseaux afin de venir à bout du contenu qui lui est proposé.  
L'objectif de ce projet est de concevoir une interface web pour les membres de ma guilde. Via cette interface, ils pourront créer des escouades de 5 héros/vaisseaux et voir le niveau d'équipement des héros/vaisseaux des membres de la guilde
pour l'escouade qu'ils ont créés. Cela nous permettra de :  
- Vérifier que les membres de la guilde farm bien les escouades demandées
- Mesurer l'efficacité des rosters (ensemble des héros/vaisseaux d'un joueur) des membres 
- Brainstormer sur les escouades à mettre en défense dans les contenus PVP 

### Réponse technique au besoin 
L'interface web qui a été dévelopée est divisée en deux parties :
1) Une API qui permet d'aller chercher l'ensemble des informations de ma guilde (a) et de visualiser/exporter/créer/modifier/supprimer les escouades (b)
2) Un frontend qui intéragit avec l'API

#### L'API
L'API a été développée via le framework **Symfony**.  
Afin que les officiers puissent créer les escouades et voir les unités (héros/vaisseaux) des membres de la guilde, il faut aller chercher ces informations dans les bases de données du jeu. La récupération de ces informations se fait via l'exécution de commande Symfony. Celle-ci consomment une API mise à disposition par le site [swgoh](https://swgoh.gg/) et sauvegarde les informations
récoltées dans la base de données (a).  
Les informations récoltées sont transmises aux utilisateurs via une API développée from scratch. L'API est multiguildes et multiscouades (b).  
#### Le frontend
Le frontend est fait en React JS. C'est l'IHM qui permet d'intéragir avec l'API

## II) Guide d'installation
### 1) Prérequis techniques
**Afin de pouvoir faire fonctionner ce projet, vous aurez besoin :**  
- D'une distribution Linux (Debian, Ubuntu, etc.)  
- Des paquets docker/docker compose  
- D'un reverse proxy (optionnel)  
