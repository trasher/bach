<?php
/**
 * Bach core creation form
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Bach core creation form
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class CoreCreationForm extends AbstractType
{
    private $_tables;
    private $_doctrine;
    private $_dbname;

    /**
     * Main Constructor
     *
     * @param Doctrine $doctrine Doctrine instance
     * @param string   $dbname   Database name
     */
    public function __construct($doctrine, $dbname)
    {
        $this->_doctrine = $doctrine;
        $this->_dbname = $dbname;
    }

    /**
     * Build the form
     *
     * @param FormBuilderInterface $builder Builder interface
     * @param array                $options Options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_getTableNamesFromDataBase();
        $builder->add(
            'core',
            'choice',
            array(
                'required' => true,
                'choices'  => $this->_tables
            )
        )->add(
            'name',
            'text',
            array(
                'required'  => true
            )
        );
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver Resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bach\AdministrationBundle\Entity' .
                '\Helpers\FormObjects\CoreCreation',
            )
        );
    }

    /**
     * Retrieve *Format tables names form database
     *
     * @return void
     */
    private function _getTableNamesFromDataBase()
    {
        $sql = "SELECT table_name AS name FROM information_schema.tables " .
            "WHERE table_schema LIKE '" . $this->_dbname . "'";
        $connection = $this->_doctrine->getConnection();
        $result = $connection->query($sql);
        $res = array();
        while ( $row = $result->fetch() ) {
            $t = $row['name'];
            $subStr = substr($t, strlen($t) - 6);
            if ( $subStr === 'Format' ) {
                $res[$t] = $t;
            }
        }
        $this->_tables = $res;
    }

    /**
     * Get form name
     *
     * @return String
     */
    public function getName()
    {
        return 'coreCreationForm';
    }
}
