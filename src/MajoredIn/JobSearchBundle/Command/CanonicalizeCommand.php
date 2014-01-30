<?php

namespace MajoredIn\JobSearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CanonicalizeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('database:canonicalize')
        ->setDescription('Canonicalize the canonicalizable fields in the major, majoralias and location tables.')
        ->addOption(
            'table',
            null,
            InputOption::VALUE_REQUIRED,
            'If set, only the listed table will be canonicalized (major, majoralias, location)',
            'all'
        );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $table = $input->getOption('table');
        }
        catch (\Exception $e) {
            $table = 'all';
        }
        
        $flush = true;
        
        $majorManager = $this->getContainer()->get('mi_search.major.manager');
        $majorAliasManager = $this->getContainer()->get('mi_search.major_alias.manager');
        $locationManager = $this->getContainer()->get('mi_search.location.manager');
        
        if ($table === 'major' || $table === 'all') {
            $majors = $majorManager->findMajors();
            
            $output->writeln("Majors:");
            
            foreach ($majors as $major) {
                if ($major->getNameCanonical() === $majorManager->canonicalizeName($major->getName())) {
                    continue;
                }
                $majorManager->updateMajor($major, false);
                $output->writeln("\"" . $major->getName() . "\" canonicalized as \"" . $major->getNameCanonical() . "\".");
            }
            
            foreach ($majors as $major) {
                if (isset($majorCollision[$major->getNameCanonical()])) {
                    $flush = false;
                    $output->writeln("ERROR: " . $major->getId() . " " . $major->getName() . " has the same canonical name as " . $majorCollision[$major->getNameCanonical()]->getId() . " " . $majorCollision[$major->getNameCanonical()]->getName());
                }
                else {
                    $majorCollision[$major->getNameCanonical()] = $major;
                }
            }
        }
        
        if ($table === 'majoralias' || $table === 'all') {
            $majorAliases = $majorAliasManager->findMajorAliases();
            
            $output->writeln("\nMajorAliases:");
            
            foreach ($majorAliases as $majorAlias) {
                if ($majorAlias->getNameCanonical() === $majorAliasManager->canonicalizeName($majorAlias->getName())) {
                    continue;
                }
                $majorAliasManager->updateMajorAlias($majorAlias, false);
                $output->writeln("\"" . $majorAlias->getName() . "\" canonicalized as \"" . $majorAlias->getNameCanonical() . "\".");
            }
            
            foreach ($majorAliases as $majorAlias) {
                if (isset($majorAliasCollision[$majorAlias->getNameCanonical()])) {
                    $flush = false;
                    $output->writeln("ERROR: " . $majorAlias->getId() . " " . $majorAlias->getName() . " has the same canonical name as " . $majorAliasCollision[$majorAlias->getNameCanonical()]->getId() . " " . $majorAliasCollision[$majorAlias->getNameCanonical()]->getName());
                }
                else {
                    $majorAliasCollision[$majorAlias->getNameCanonical()] = $majorAlias;
                }
            }
        }
        
        if ($table === 'location' || $table === 'all') {
            $locations = $locationManager->findLocations();
            
            $output->writeln("\nLocations:");
            
            foreach ($locations as $location) {
                if ($location->getNameCanonical() === $locationManager->canonicalizeName($location->getName())) {
                    continue;
                }
                $locationManager->updateLocation($location, false);
                $output->writeln("\"" . $location->getName() . "\" canonicalized as \"" . $location->getNameCanonical() . "\".");
            }
            
            foreach ($locations as $location) {
                if (isset($locationCollision[$location->getNameCanonical()])) {
                    $flush = false;
                    $output->writeln("ERROR: " . $location->getId() . " " . $location->getName() . " has the same canonical name as " . $locationCollision[$location->getNameCanonical()]->getId() . " " . $locationCollision[$location->getNameCanonical()]->getName());
                }
                else {
                    $locationCollision[$location->getNameCanonical()] = $location;
                }
            }
        }
        
        if ($flush) {
            try {
                $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
                $output->writeln("\nChanges flushed to the database.");
            }
            catch (\PDOException $e) {
                $output->writeln("\nAn error occured while attempting to flush the changes to the database.");
            }
        }
        else {
            $output->writeln("\nDue to the canonical name conflicts the proposed changes will not be commited to the database.");
        }
    }
}
