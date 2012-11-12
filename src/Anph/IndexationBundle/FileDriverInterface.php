<?php

/*
* This file is part of the bach project.
*/

namespace Anph\IndexationBundle;

use Symfony\Component\Finder\SplFileInfo;

/**
* FileDriver interface
*
* @author Anaphore PI Team
*/
interface FileDriverInterface
{
    public function process(SplFileInfo $fileInfo);
    
    public function getFileFormatName();
}
