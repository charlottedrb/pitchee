# Pitchee 🍑

#### Envie de partager vos dernières idées ? Ou simplement besoin d'organisation pour les retrouver plus tard ? 

![image](https://user-images.githubusercontent.com/47384185/116794730-8f0edc00-aacf-11eb-8301-353989e0dd38.png)


Nous vous proposons notre toute dernière solution web vous permettant de découvrir, mais aussi de faire découvrir vos centres d'intérêt, vos envies du moment, vos inspirations... 

## Utilisation

Le principe est simple : vous créez des cards qui alimentent les données du site. Chaque card est **publique**, elle sera proposée aux différents utilisateurs leur permettant d'élargir leurs horizons d'idées. Ils pourront soit liker votre idée, ou la rejeter. 

S'il la like ✨, ils auront alors accès aux informations plus détaillées. Sinon, ils passent à autre chose et la card pourra réapparaître plus tard. 


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
