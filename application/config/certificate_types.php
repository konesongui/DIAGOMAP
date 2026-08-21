<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['certificate_types'] = array(
    'work' => array(
        'name' => 'Certificat de Travail',
        'icon' => 'fa-briefcase',
        'fields' => array(
            'employee_name' => 'Nom de l\'employé',
            'position' => 'Poste',
            'start_date' => 'Date d\'embauche',
            'end_date' => 'Date de fin'
        ),
        'variables' => array(
            '{employee_name}', '{position}', '{start_date}', '{end_date}'
        ),
        'default_content' => 'Nous soussignés, attestons que {employee_name} a travaillé en tant que {position} du {start_date} au {end_date}.'
    ),
    'training' => array(
        'name' => 'Certificat de Fin de Formation',
        'icon' => 'fa-graduation-cap',
        'fields' => array(
            'participant_name' => 'Nom du participant',
            'training_name' => 'Intitulé de la formation',
            'duration' => 'Durée',
            'completion_date' => 'Date d\'obtention'
        ),
        'variables' => array(
            '{participant_name}', '{training_name}', '{duration}', '{completion_date}'
        ),
        'default_content' => 'Ce certificat atteste que {participant_name} a suivi avec succès la formation "{training_name}" d\'une durée de {duration} et obtenu son certificat le {completion_date}.'
    ),
    'internship' => array(
        'name' => 'Certificat de Stage',
        'icon' => 'fa-users',
        'fields' => array(
            'intern_name' => 'Nom du stagiaire',
            'department' => 'Département',
            'start_date' => 'Date de début',
            'end_date' => 'Date de fin'
        ),
        'variables' => array(
            '{intern_name}', '{department}', '{start_date}', '{end_date}'
        ),
        'default_content' => 'Nous attestons que {intern_name} a effectué un stage au sein du département {department} du {start_date} au {end_date}.'
    )
);