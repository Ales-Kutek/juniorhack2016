<?php
/**
 * Přidá příkaz.
 */

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * CronCommand class
 */
class CronCommand extends Command
{
    /**
     * @var \Zenify\DoctrineFixtures\Alice\AliceLoader
     */
    private $alice;
    
    /**
     * @var \Kdyby\Doctrine\EntityManager $em
     */
    private $em;
    
    /**
     * Injectnutí alice a entity manager
     * @param \Zenify\DoctrineFixtures\Alice\AliceLoader $alice
     */
    public function injectAlice(\Zenify\DoctrineFixtures\Alice\AliceLoader $alice, \Kdyby\Doctrine\EntityManager $em)
    {
        $this->alice = $alice;
        $this->em = $em;
    }

    /**
     * Přidání samotného příkazu.
     */
    protected function configure()
    {
        $this->setName('app:cron')
            ->setDescription('Apllying fixtures.');
    }

    /**
     * Nahrání složky s yml, neon soubory.
     * soubory se nahrávají postupně, podle závislostí.. nutno takhle ručně řešit
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
       

        $url = "http://localhost/homepage/default/0?do=getArdu";
        
        file_get_contents($url);

    }
}