<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class BanWordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        //vérification que la contrainte est bien une instance des mots bannis
        if (!$constraint instanceof BanWord) {
            throw new UnexpectedTypeException($constraint, BanWord::class);
        }

        // si la valeur est null ou vide, pas de soucis
        if (null === $value || '' === $value) {
            return;
        }

        // vérification que la valeur est bien un string
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        //Récupération de la liste des mots bannis
        $banWords = $constraint->banWords;

        foreach ($banWords as $banWord) {
            // pattern pour éviter une correspondance des mots partiels (\b) et la sensibilité à la casse(i) tout en prenant les caractères spéciaux de manière litérale
            $pattern = '/\b' . preg_quote($banWord, '/') . '\b/i';

            //fontion pour vérifier si notre mot(pattern) est présent dans l'input envoyé
            if (preg_match($pattern, $value)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ banWord }}', $banWord)
                    ->addViolation();

                // Log du délit
                // error_log("BanWord violation: " . $banWord);

                break; // Stop dès la 1ère violation
            }
        }
    }
}
