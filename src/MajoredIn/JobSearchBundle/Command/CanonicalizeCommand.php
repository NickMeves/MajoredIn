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
            
            $majorsSize = count($majors);
            for ($i = 0; $i < $majorsSize; ++$i) {
                for ($j = $i + 1; $j < $majorsSize; ++$j) {
                    if ($majors[$i]->getNameCanonical() === $majors[$j]->getNameCanonical()) {
                        if ($flush) {
                            $output->writeln("\n\nERROR:\n\n");
                            $flush = false;
                        }
                        $output->writeln($majors[$i]->getId() . $majors[$i]->getName() . " has the same canonical name as " . $majors[$j]->getId() . $majors[$j]->getName());
                    }
                }
            }
        }
        
        if ($table === 'majoralias' || $table === 'all') {
            $majorAliases = $majorAliasManager->findMajorAliases();
            
            $output->writeln("MajorAliases:");
            
            foreach ($majorAliases as $majorAlias) {
                if ($majorAlias->getNameCanonical() === $majorAliasManager->canonicalizeName($majorAlias->getName())) {
                    continue;
                }
                $majorAliasManager->updateMajorAlias($majorAlias, false);
                $output->writeln("\"" . $majorAlias->getName() . "\" canonicalized as \"" . $majorAlias->getNameCanonical() . "\".");
            }
            
            $majorAliasesSize = count($majorAliases);
            for ($i = 0; $i < $majorAliasesSize; ++$i) {
                for ($j = $i + 1; $j < $majorAliasesSize; ++$j) {
                    if ($majorAliases[$i]->getNameCanonical() === $majorAliases[$j]->getNameCanonical()) {
                        if ($flush) {
                            $output->writeln("\n\nERROR:\n\n");
                            $flush = false;
                        }
                        $output->writeln($majorAliases[$i]->getId() . $majorAliases[$i]->getName() . " has the same canonical name as " . $majorAliases[$j]->getId() . $majorAliases[$j]->getName());
                    }
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
            
            $locationsSize = count($locations);
            for ($i = 0; $i < $locationsSize; ++$i) {
                for ($j = $i + 1; $j < $locationsSize; ++$j) {
                    if ($locations[$i]->getNameCanonical() === $locations[$j]->getNameCanonical()) {
                        if ($flush) {
                            $output->writeln("\n\nERROR:\n\n");
                            $flush = false;
                        }
                        $output->writeln($locations[$i]->getId() . $locations[$i]->getName() . " has the same canonical name as " . $locations[$j]->getId() . $locations[$j]->getName());
                    }
                }
            }
        }
        if ($flush) {
            $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
            $output->writeln("\nChanges flushed to the database.");
        }
        else {
            $output->writeln("\nDue to the canonical name conflicts the proposed changes will not be commited to the database.");
        }
    }
}
