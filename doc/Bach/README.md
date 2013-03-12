Projet Bach
========================

Installation du projet sur une machine locale:

Si vous avez un dossier git dans votre home, ignorez cette étape :

    cd ~
    mkdir git

On se place dans notre dossier on et clone notre dépôt :

    cd ~/git
    git clone git@dev.anaphore.eu:bach.git bachdev
    sudo chmod -R 777 bachdev/
    cd bachdev/app/config
    cp parameters.yml.dist parameters.yml

Editez ensuite parameters.yml pour définir vos paramètres de connexion à la base de données.

On installe ensuite les dépendances à l'aide de composer, si vous ne le possèdez pas visitez le site getcomposer.org pour obtenir les instructions relatives à son installation. On suppose ici qu'il est dans votre dossier personnel :

    cd ~/git/bachdev
    ~/composer.phar udpate

Une fois que tout est terminé, exécutez la commande suivante pour supprimer le cache : 

    cd ~/git/bachdev
    sudo rm -R app/cache/*
    sudo chmod -R 777 .

Créez votre base de données sql puis exécutez : 
    
    cd ~/git/bachdev
    php app/console doctrine:schema:create

Passons ensuite à la configuration d'apache, on doit d'abord créer le lien symbolique pour y accèder :

    ln -s ~/git/bachdev /var/www/bachdev

Ensuite copiez la configuration de apache pour l'activer :

    sudo cp ~/git/bachdev/app/config/bachdev /etc/apache2/sites-available/
    sudo a2ensite bachdev
    sudo service apache2 restart

Actuellement il y a une erreur dans la version de twig installée pour y remédier éditez : ~/git/bachdev/vendor/symfony/symfony/src/Symfony/Bridge/Twig/NodeVisitor/Scope.php

A la ligne : 

    private $data;

Ajoutez : 

    private $data = array();

Vous pouvez maintenant vous rendre à l'adresse "http://localhost/bachdev".

Si vous rencontrez des erreurs, cela peut provenir des droits ou du cache de symfony, exécutez dans ce cas : 

    cd ~/git/bachdev
    sudo rm -R app/cache/*
    sudo chmod -R 777 .
    
Après la création du projet dans Eclipse, il arrive que git veuillent commiter plein de choses, dans ce cas, la seule solution trouvée actuellement est de faire (après avoir sauvegarder ses modifications) : 
    
    git fetch --all
    git reset --hard
    
Puis de remettre les fichiers que vous aviez modifié. A ce moment les commits suivant se passeront normalement.
