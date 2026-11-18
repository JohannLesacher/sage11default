<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vidéo de formation
    |--------------------------------------------------------------------------
    |
    | Widget « Prendre en main votre site » du tableau de bord.
    | url      : fichier unique, partagé par tous les sites
    | chapters : time en secondes depuis le début de la vidéo
    |
    */

    'video' => [
        'url' => 'https://one.cerealconcept.com/formation/prise-en-main.mp4',
        'chapters' => [
            ['time' => 0, 'label' => 'Introduction'],
            ['time' => 30, 'label' => 'Se connecter'],
            ['time' => 90, 'label' => 'Se repérer dans l\'administration'],
            ['time' => 180, 'label' => 'Modifier un texte'],
            ['time' => 300, 'label' => 'Changer une image'],
            ['time' => 420, 'label' => 'Ajouter un élément de liste'],
            ['time' => 510, 'label' => 'Annuler une erreur'],
            ['time' => 570, 'label' => 'Ce qu\'il ne faut pas faire'],
            ['time' => 660, 'label' => 'Les messages du formulaire'],
            ['time' => 705, 'label' => 'Qui contacter'],
        ],
    ],

    'support_email' => 'support@cerealconcept.com',
];
