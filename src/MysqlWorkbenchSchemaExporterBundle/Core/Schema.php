<?php

namespace MysqlWorkbenchSchemaExporterBundle\Core;

use \Symfony\Component\DependencyInjection\ContainerAware;
use \MwbExporter\Bootstrap;

/**
 * Description of Schema
 *
 * @author Marc Easen <marc@easen.co.uk>
 */
class Schema extends ContainerAware
{
    /**
     * Bundle name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Options
     *
     * @var string[][]
     */
    protected $options = array();

    /**
     * Bundle reflection class
     *
     * @var \Symfony\Component\HttpKernel\Bundle\Bundle
     */
    protected $bundle = null;

    /**
     * Default formater params
     *
     * @var string[][]
     */
    protected $defaultFormatterParams = array(
        'indentation' => 4,
        'backupExistingFile' => false
    );

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options)
    {
        $this->setName($name);
        $this->setOptions($options);
    }

    /**
     * Set options
     *
     * @param array $options
     * @return \MysqlWorkbenchSchemaExporterBundle\Core\Schema
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get an option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }

    /**
     * Get the bunde name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the bundle name
     *
     * @param string $name
     * @return \MysqlWorkbenchSchemaExporterBundle\Core\Schema
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the MySQL Workbench file
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getMwbFile()
    {
        $file = $this->getBundle()->getPath() . DIRECTORY_SEPARATOR . sprintf($this->getOption('file'), $this->getName());

        if (!file_exists($file)) {
            throw new \RuntimeException(sprintf('Unable to locate the MySQL Workbench File %s', $file));
        }

        return $file;
    }

    /**
     * Get the bundle reflection class
     *
     * @return \Symfony\Component\HttpKernel\Bundle\Bundle
     */
    public function getBundle()
    {
        if (null === $this->bundle) {
            $bundle = $this->getOption('bundle');
            $kernel = $this->container->get('kernel');
            $this->bundle = $kernel->getBundle($bundle);
        }
        return $this->bundle;
    }

    /**
     * Get the output directory
     *
     * @return string
     */
    protected function getOutputDir()
    {
        return $this->getBundle()->getPath() .
               DIRECTORY_SEPARATOR .
               sprintf(
                    $this->getOption('output', 'Entity/%s/'),
                    ucfirst($this->getName())
               );
    }

    /**
     * Get the formatter params
     *
     * @return string[][]
     */
    protected function getFormatterParams()
    {
        $params = array_merge(
            $this->defaultFormatterParams,
            array(
                'bundleNamespace' => $this->getBundle()->getNamespace()
            ),
            $this->getOption('params', array())
        );
        return $params;
    }

    /**
     * Export
     *
     * @return string
     */
    public function export()
    {
        $bootstrap = new Bootstrap();

        // define a formatter and do configuration
        $formatter = $bootstrap->getFormatter($this->getOption('formatter'));
        $formatter->setup($this->getFormatterParams());

        // load document and export
        $document = $bootstrap->export(
            $formatter,
            $this->getMwbFile(),
            $this->getOutputDir()
        );

        // show the output
        return $document->getWriter()->getStorage()->getResult();
    }
}
