<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BanWord extends Constraint
{
    // Pour créer ma propre liste de mots interdits pour mes formulaires (injections SQL, Js, etc)

    public function __construct(
        public string $message = "The caracter/word \'{{ banWord }}\' is not allowed ! If it's a verb, you can still use its past tense.",
        public array $banWords = ['select', 'create', 'update', 'delete', 'add', 'remove', 'insert into', 'drop', 'alter',  '--', '<', '>', '<script>', 'script', 
        '</script>', 'iframe', 'img', '<iframe>', '<img>', 'href', 'src', 'onclick', 'onload', '<object>', '<embed>', '<link>', 'fuck', 'bastard', 'motherfucker', 'dickhead', 'slut', 'whore', 'dick', 'pussy', 'negro', 'twat', 'queer', 'fagot', 'fag', 'cunt', 'asshole', 'assole', 'bitch', 'fucker', 'fucking', 'motherfucking', 'dickweed', 'spam', 'investment', 'risk-free', 'no obligation', 'Security alert', 'satisfaction guaranteed', 'Order now', 'http://', 'click here'],
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
