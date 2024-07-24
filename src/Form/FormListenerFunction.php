<?php

namespace App\Form;

use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FormListenerFunction
{

  public function autoSlug(): callable
  {
    // pour faire un auto slug avant le traitement du formulaire : on récup les données brutes et on peux les utiliser avant qu'elles ne soient traitées par le traitement du formulaire : validator, ...
    return function (PreSubmitEvent $event) {
      // dd($event->getData());
      $data = $event->getData();

      if (empty($data['slug'])) {
        $slugger = new AsciiSlugger('fr', ['fr' => ['à' => 'à', 'â' => 'â', 'ä' => 'ä', 'é' => 'é', 'è' => 'è', 'ê' => 'ê', 'ë' => 'ë', 'î' => 'î', 'ï' => 'ï', 'ô' => 'ô', 'ö' => 'ö', 'ù' => 'ù']]);
        $data['slug'] = strtolower($slugger->slug($data['title']));

        $event->setData($data);
      }
    };
  }

  public function cleanInputText(string $fieldName): callable
  {
    // Avant la soumission, on échappe et clean les valeurs données par l'user
    return function (PreSubmitEvent $event) use ($fieldName) {
      // on récupère les données
      $data = $event->getData();

      // Nettoyage du champs : 
      if (isset($data[$fieldName])) {
        $data[$fieldName] = trim($data[$fieldName]);
        $data[$fieldName] = htmlspecialchars(strip_tags($data[$fieldName]), ENT_QUOTES, 'UTF-8');
      }

      $event->setData($data);
    };
  }

}
