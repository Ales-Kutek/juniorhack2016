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
 * FixturesCommand class
 */
class FixturesCommand extends Command
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
        $this->setName('app:fixtures')
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
        $this->truncateAllTables();
        
        /** musí se nahrávat v určitém pořádku kvůli asociacím (soubory bez asociací dávat z pravidla první) */
        $this->alice->load(array(
            __DIR__.'/../fixtures/Group.yml',
            __DIR__.'/../fixtures/User.yml',
            __DIR__.'/../fixtures/Element.yml',
            __DIR__.'/../fixtures/HeatSensor.yml',
            __DIR__.'/../fixtures/HeatSensorLog.yml',
            __DIR__.'/../fixtures/HumiditySensor.yml',
            __DIR__.'/../fixtures/HumiditySensorLog.yml',
        ));
    }
    
    public function truncateAllTables() {
        $query = $this->em->getConnection()->prepare("SELECT Concat('TRUNCATE TABLE `',TABLE_NAME, '`;') FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA in ('hackathon');");
        $query->execute();

        $result = $query->fetchAll(\PDO::FETCH_NUM);
        
        $query->closeCursor();
        
        $query = NULL;
        
        $sql = "";
        
        foreach($result as $key => $value) {
            $sql .= $value[0];
        }
        
        $query = $this->em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=0;" . $sql . "SET FOREIGN_KEY_CHECKS=1;");
        
        $query = NULL;
    }
}