<?php
/**
 * Bach core creation form
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
 * @license  Unknown http://unknown.com
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
