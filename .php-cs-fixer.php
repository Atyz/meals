<?php

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
        'array_syntax' => false,
        'no_alternative_syntax' => false,
    ))
;
