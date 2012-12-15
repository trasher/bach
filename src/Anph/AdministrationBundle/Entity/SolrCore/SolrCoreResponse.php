<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;
use DOMDocument;

class SolrCoreResponse
{
    private $status;
    private $code;
    private $message;
    private $trace;

    public function __construct($XMLresponse)
    {
        $doc = new DOMDocument();
        $doc->loadXML($XMLresponse);
        $lstList = $doc->getElementsByTagName('lst');
        foreach ($lstList as $l) {
            if ($l->hasAttribute('name')) {
                switch ($l->getAttribute('name')) {
                    case 'responseHeader':
                        $intList = $l->getElementsByTagName('int');
                        foreach ($intList as $i) {
                            if ($i->hasAttribute('name') && $i->getAttribute('name') === 'status') {
                                $this->status = $i->nodeValue;
                                break;
                            }
                        }
                        break;
                    case 'error':
                        $strList = $l->getElementsByTagName('str');
                        foreach ($strList as $s) {
                            if ($s->hasAttribute('name')) {
                                switch ($s->getAttribute('name')) {
                                    case 'msg':
                                        $this->message = $s->nodeValue;
                                        break;
                                    case 'trace':
                                        $this->trace = $s->nodeValue;
                                        break;
                                }
                            }
                        }
                        $intList = $l->getElementsByTagName('int');
                        foreach ($intList as $i) {
                            if ($i->hasAttribute('name') && $i->getAttribute('name') === 'code') {
                                $this->code = $i->nodeValue;
                                break;
                            }
                        }
                        break;
                }
            }
        }
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function getTrace()
    {
        return $this->trace;
    }
}