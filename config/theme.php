<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    |
    | fixed : header en position fixe (ajoute la classe body has-fixed-header)
    | align : alignement du menu (left, center, right)
    | lang  : sélecteur de langue Polylang
    | cta   : bouton d'appel à l'action, false ou ['label' => '…', 'url' => '…']
    |
    */

    'header' => [
        'fixed' => true,
        'align' => 'left',
        'lang' => false,
        'cta' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Menus
    |--------------------------------------------------------------------------
    |
    | Emplacements de menus : identifiant => libellé dans l'admin
    |
    */

    'menus' => [
        'primary_navigation' => 'Menu principal',
        'footer_navigation' => 'Menu footer',
        'legal_footer_navigation' => 'Menu mentions légales',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tailles d'images
    |--------------------------------------------------------------------------
    |
    | label : si renseigné, la taille est proposée dans l'éditeur
    |
    */

    'image_sizes' => [
        'very-large' => ['width' => 1920, 'height' => 1080, 'crop' => false, 'label' => 'Très large'],
        'lowres' => ['width' => 38, 'height' => 22, 'crop' => true], // Placeholder flou d'apparition
    ],

];
