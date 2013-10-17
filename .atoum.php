<?php
use \mageekguy\atoum;

$coverageField = new atoum\report\fields\runner\coverage\html(
    'Bach',
    __DIR__ . '/code-coverage'
);
$coverageField->setRootUrl('file://' . realpath(__DIR__ . '/code-coverage'));

$xunitWriter = new atoum\writers\file(__DIR__ . '/atoum.xunit.xml');

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

$xunitReport = new atoum\reports\asynchronous\xunit();
$xunitReport->addWriter($xunitWriter);

$runner->addReport($xunitReport);
$script
    ->addDefaultReport()
    ->addField($coverageField);
