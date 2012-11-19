<?php
// src/Anph/IndexationBundle/Entity/UniversalFileFormat.php
namespace Anph\IndexationBundle\Entity;

class UniversalFileFormat
{
    public function __construct($data){
    	file_put_contents(__DIR__.'../../../../../test.txt',var_export($data,true));
    }
}
