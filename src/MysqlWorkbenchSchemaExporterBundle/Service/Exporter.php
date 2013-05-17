<?php

namespace MysqlWorkbenchSchemaExporterBundle\Service;

use \MysqlWorkbenchSchemaExporterBundle\Core\Schema;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Description of Exporter
 *
 * @author marc
 */
class Exporter extends ContainerAware {

    /**
     * Schemas
     *
     * @var MysqlWorkbenchSchemasExporterBundle\Core\Schema[]
     */
    protected $schemas = array();

    /**
     * Constructure
     *
     * @param type $schemas
     */
    public function __construct($schemas = array())
    {
        $this->setSchemas($schemas);
    }

    /**
     * Set the schema
     *
     * @param array $schemas
     * @return \MysqlWorkbenchSchemasExporterBundle\Service\Exporter
     */
    public function setSchemas(array $schemas)
    {
        $this->schemas = $schemas;
        return $this;
    }

    /**
     * Get the current schemas
     *
     * @return MysqlWorkbenchSchemasExporterBundle\Core\Schema[]
     */
    public function getSchemas()
    {
        foreach ($this->schemas as $name => &$value)
        {
            if ($value instanceof Schema) {
                continue;
            }

            if (is_array($value)) {
                $value = new Schema($name, $value);
                $value->setContainer($this->container);
            }
        }

        return $this->schemas;
    }

    /**
     * Export
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function export(OutputInterface $output)
    {
        $bundles = array();
        foreach($this->getSchemas() as $schema) {
            $output->writeln(sprintf('Exporting "<info>%s</info>" schema', $schema->getName()));
            $location = $schema->export();
            $output->writeln(sprintf('Saved to "<info>%s</info>".', $location));
            $bundles[] = array(
                'name' => $schema->getOption('bundle'),
                'path' => $schema->getBundle()->getPath()
            );
        }

        // Use the Symfony generate doctrine entities
        foreach($bundles as $bundle) {
            $kernel = $this->container->get('kernel');
            $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
            $application->setAutoExit(false);
            $application->setCatchExceptions(false);

            $options = array(
                'command' => 'generate:doctrine:entities',
                'name' => $bundle['name'],
                '--path' => $bundle['path'],
                '--no-backup' => true
            );

            try {
                $application->run(new \Symfony\Component\Console\Input\ArrayInput($options), $output);

                // Bug where the new entities are created but the repository classes aren't generated
                $application->run(new \Symfony\Component\Console\Input\ArrayInput($options), $output);
            } catch (\Exception $ex) {
                $output->writeln('There were errors while running <info>generate:doctrine:entities</info>');
                $output->writeln(sprintf(
                    'Please run <info>app/console generate:doctrine:entities --path %s --no-backup %s</info>',
                     $bundle['path'],
                     $bundle['name']
                ));
            }

        }

    }
}
