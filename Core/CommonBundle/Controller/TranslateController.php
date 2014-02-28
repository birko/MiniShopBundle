<?php

namespace Core\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class TranslateController extends Controller
{
    protected function loadTranslations($entity, $cultures, $newobject = null)
    {
        if (count($cultures) > 0) {
            $em = $this->getDoctrine()->getManager();
            foreach ($cultures as $culture) {
                if ($newobject === null) {
                    $entity->setTranslatableLocale($culture);
                    $em->refresh($entity);
                    $translation = clone $entity;
                } else {
                    $translation = clone $newobject;
                }
                $translation->setTranslatableLocale($culture);
                $entity->addTranslation($translation);
            }
            if ($newobject === null) {
                $entity->setTranslatableLocale($cultures[0]);
                $em->refresh($entity);
            }
        }
    }

    protected function saveTranslations($entity, $cultures)
    {
        if (count($cultures) > 0) {
            $translations = $entity->getTranslations();
            $i = 0;
            foreach ($translations as $translation) {
                $this->saveTranslation($entity, $cultures[$i], $translation);
                $i++;
            }
        }
    }

    // MUST BE OVERRIDEN
    protected function saveTranslation($entity, $culture, $translation)
    {
    }
}
