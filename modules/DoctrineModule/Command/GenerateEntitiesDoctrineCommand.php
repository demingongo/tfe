<?php

namespace DoctrineModule\Command;

use Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntitiesDoctrineCommand extends GenerateEntitiesCommand
{
    protected function configure()
    {
		parent::configure();
        $this
        ->setName('doctrine:generate-entities')
        ->setAliases(array('doctrine:generate:entities'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		DoctrineCommandHelper::setApplicationEntityManager($this->getApplication());

        parent::execute($input, $output);
    }
}