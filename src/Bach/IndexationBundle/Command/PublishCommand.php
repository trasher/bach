<?php

/**
 * Publication command
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/*use Bach\IndexationBundle\Generator\FileDriverGenerator;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;*/

/**
 * Publication command
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class PublishCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:publish')
            ->setDescription('File publication')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> launches whole publishing process
(pre-processing, conversion, indexation)
EOF
            )->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Documents type.'
            )->addArgument(
                'document',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Document(s) name(s) to proceed'
            )->addOption(
                'assume-yes',
                null,
                InputOption::VALUE_NONE,
                'Assume yes for all questions'
            )->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Do not really publish.'
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
        $type = null;
        $container = $this->getContainer();
        $known_types = $container->getParameter('bach.types');

        if ( $input->getArgument('type')) {
            $type = $input->getArgument('type');

            if ( !in_array($type, $known_types) ) {
                $msg = _('Unknown type! Please choose one of:');
                $output->writeln(
                    '<error>' . $msg . "\n -" .
                    implode("\n -", $known_types)  . '</error>'
                );
                die();
            }
        }

        $output->writeln(
            $msg = str_replace(
                '%type',
                $type,
                _('Publishing "%type" documents.')
            )
        );

        //let's check if files exists
        $to_publish = $input->getArgument('document');

        //let's proceed
        $tf = $container->get('bach.indexation.typesfiles');
        $files_to_publish = $tf->getExistingFiles($type, $to_publish);

        $output->writeln(
            "\n" .  _('Following files are about to be published: ')
        );
        $output->writeln(
            implode("\n", $files_to_publish[$type])
        );

        $confirm = null;
        if ( $input->getOption('assume-yes') ) {
            $confirm = 'yes';
        } else {
            $choices = array(_('yes'), _('no'));
            $dialog = $this->getHelperSet()->get('dialog');
            $confirm = $dialog->ask(
                $output,
                "\n" . _('Are you ok (y/n)?'),
                null,
                $choices
            );
        }

        if ( $confirm === 'yes' || $confirm === 'y' ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Publication begins...') .
                '</fg=green;options=bold>'
            );
            $progress = $this->getHelperSet()->get('progress');
            $progress->start($output, count($files_to_publish[$type]));
            foreach ( $files_to_publish[$type] as $ftp ) {
                /*$integrationService = $this->getContainer()
                    ->get('bach.indexation.process.arch_file_integration');*/
                /*$return = $integrationService->proceedQueue();*/
                /*$output->writeln($return);*/

                $progress->advance();
            }
            $progress->finish();
        } else {
            $output->writeln(
                '<fg=red;options=bold>' .
                _('Command canceled by user.') .
                '</fg=red;options=bold>'
            );

        }
    }
}
