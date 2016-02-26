<?php

namespace FlexModel\FlexModelBundle\Command;

use DOMDocument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XSLTProcessor;

/**
 * GenerateMappingCommand.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class GenerateMappingCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('flexmodel:generate')
            ->setDescription('Generates an ORM mapping for the loaded ORM from the FlexModel configuration.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $flexModel = $this->getContainer()->get('flexmodel');
        $domDocument = $flexModel->getDOMDocument();

        $xslDocument = new DOMDocument('1.0', 'UTF-8');
        $xslDocument->load(__DIR__.'/../Resources/xsl/doctrine-mapping.xsl');

        $processor = new XSLTProcessor();
        $processor->setParameter('', 'objectNamespace', 'AppBundle\\Entity\\'); // @todo Make configurable
        $processor->importStyleSheet($xslDocument);

        $bundle = $this->getApplication()->getKernel()->getBundle('AppBundle'); // @todo Make configurable

        $ormMappingDirectory = $bundle->getPath().'/Resources/config/doctrine'; // @todo Make configurable
        if (is_dir(dirname($ormMappingDirectory)) === false) {
            mkdir($ormMappingDirectory, 0755, true);
        }

        $objectNames = $flexModel->getObjectNames();
        foreach ($objectNames as $objectName) {
            $processor->setParameter('', 'objectName', $objectName);

            file_put_contents(sprintf('%s/%s.orm.xml', $ormMappingDirectory, $objectName), $processor->transformToXML($domDocument));

            $output->writeln(sprintf('Generating Doctrine entity mapping for object: <comment>%s</comment>', $objectName), OutputInterface::VERBOSITY_VERBOSE);
        }

        $io->success('Generated Doctrine mapping from FlexModel configuration.');

        $command = $this->getApplication()->find('generate:doctrine:entities');
        $command->run(new ArrayInput(array('name' => 'AppBundle')), $output);
    }
}
