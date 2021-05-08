# Pitchee 🍑

#### Envie de partager vos dernières idées ? Ou simplement besoin d'organisation pour les retrouver plus tard ? 

![image](https://user-images.githubusercontent.com/47384185/116794730-8f0edc00-aacf-11eb-8301-353989e0dd38.png)


Nous vous proposons notre toute dernière solution web vous permettant de découvrir, mais aussi de faire découvrir vos centres d'intérêt, vos envies du moment, vos inspirations... 

## Utilisation

Le principe est simple : vous créez des cards qui alimentent les données du site. Chaque card est **publique**, elle sera proposée aux différents utilisateurs leur permettant d'élargir leurs horizons d'idées. Ils pourront soit liker votre idée, ou la rejeter. 

S'il la like ✨, ils auront alors accès aux informations plus détaillées. Sinon, ils passent à autre chose et la card pourra réapparaître plus tard. 

Sur tout le site, on retrouve une sidebar avec les dernières cards likées. 

### Les listes 

![image](https://user-images.githubusercontent.com/47384185/117547793-de09c380-b031-11eb-80cc-c0a714c76dd0.png)

Vous pouvez créez des listes/sous-listes pour trier vos propres cards (la fonctionnalité d'ajout des cards likées étant en cours de développement). Ici, on voit une liste enfant qui a elle-même des enfants. N'hésitez pas à utiliser le breadcrumb pour naviguer entre les différents parents !

⚠️ Pour les liens Youtube mettre un lien selon ce modèle : https://www.youtube.com/watch?v=tWnO4kn5284 (pris directemnt dans l'url mais sans timestamp)


# Côté technique et mise en place du projet

## Pré-requis 
* PHP 8.0
* Symfony 5.2
* Composer 
* npm 

## Installation des bundles nécessaires
* ``composer install`` pour la première utilisation
* ``composer update``

### Yarn 

Si vous possédez déjà Yarn vous pouvez directement faire : 
``yarn install`` pour installer les packages requis. 

Sinon : 
``npm install --global yarn``
``yarn install``

#### Pour actualiser les scripts et feuilles de styles modifiées depuis */assets* :
``yarn encore dev``

⚠️ N'oubliez pas de créeer un *.env.local* pour lancer le projet 
