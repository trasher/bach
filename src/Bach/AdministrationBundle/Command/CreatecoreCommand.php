<?php

/**
 * Core creation command
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
Use Symfony\Component\HttpFoundation\File\File;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

/**
 * Core creation command
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * FIXME: remove duplicated code from CoreController :/
 */
class CreatecoreCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:create_core')
            ->setDescription('Solr core creation')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> creates a core with current configuration
EOF
            )->addArgument(
                'name',
                InputArgument::REQUIRED,
                _('Core name')
            )->addArgument(
                'type',
                InputArgument::OPTIONAL,
                _('Core type')
            );
    }

    /**
     * Executes the command
     *
     * @param InputInterface  $input  Stdin
     * @param OutputInterface $output Stdout
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();

        $core_name = $input->getArgument('name');
        $type = $input->getArgument('type');

        if ( !$type ) {
            $tye = 'EADUniversalFileFormat';
        }

        $configreader = $container->get('bach.administration.configreader');
        $sca = new SolrCoreAdmin($configreader);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $orm_name = 'Bach\IndexationBundle\Entity';
        switch ( $type ) {
        case 'EADUniversalFileFormat':
            $orm_name .= '\EADFileFormat';
            break;
        default:
            $orm_name .= '\UniversalFileFormat';
            break;
        }

        $db_params = $this->_getJDBCDatabaseParameters();
        $result = $sca->create(
            $core_name,
            $core_name,
            $type,
            $orm_name,
            $em,
            $db_params
        );

        $output->writeln($result);
    }

    /**
     * Get database parameters from current config,
     * to use values in newly created core
     *
     * @return array
     */
    private function _getJDBCDatabaseParameters()
    {
        $params = array();

        $driver = str_replace(
            'pdo_',
            '',
            $this->getContainer()->getParameter('database_driver')
        );
        if ( $driver == 'pgsql' ) {
            $driver = 'postgresql';
        }
        $host = $this->getContainer()->getParameter('database_host');
        $port = '';
        if ( $this->getContainer()->getParameter('database_port') !== null ) {
            $port = ':' . $this->getContainer()->getParameter('database_port');
        }
        $dbname = $this->getContainer()->getParameter('database_name');

        $dsn = 'jdbc:' . $driver . '://' . $host . $port . '/' . $dbname;

        $jdbc_driver = null;
        switch ( $driver ) {
        case 'mysql':
            $jdbc_driver = 'com.mysql.jdbc.Driver';
            break;
        case 'postgresql':
            $jdbc_driver = 'org.postgresql.Driver';
            break;
        default:
            throw new \RuntimeException('Unknown database driver ' . $driver);
        }

        $params['driver'] = $jdbc_driver;
        $params['url'] = $dsn;
        $params['user'] = $this->getContainer()->getParameter('database_user');
        $params['password'] = $this->getContainer()
            ->getParameter('database_password');

        return $params;
    }
}
