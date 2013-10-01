<?php
use \mageekguy\atoum;

$coverageField = new atoum\report\fields\runner\coverage\html(
    'Bach',
    __DIR__ . '/code-coverage'
);
$coverageField->setRootUrl('file://' . realpath(__DIR__ . '/code-coverage'));


$coverageField->addSrcDirectory(
    __DIR__.'/src/Bach/HomeBundle/',
    function ($file, $key, $iterator) {
        // Pour continuer à descendre dans l'arborescence
        if ($file->isDir()) {
            return true;
        }

        if ($file->getExtension() === 'php') {
            return preg_match(
                "/(Extension|Bundle|CompilerPass|Configuration|Controller|Command|FeatureContext|Admin|Exception|ControllerTest)\.php/",
                $file->getFilename()
            ) < 1;
        }

        return false;
    }
);

$script
    ->addDefaultReport()
    ->addField($coverageField);
