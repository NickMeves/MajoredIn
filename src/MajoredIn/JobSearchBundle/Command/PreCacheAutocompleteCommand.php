<?php

namespace MajoredIn\JobSearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class PreCacheAutocompleteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('precache:autocomplete')
        ->setDescription('Precache the autocomplete memory cache.')
        ->addOption(
            'table',
            null,
            InputOption::VALUE_REQUIRED,
            'If set, only the listed table results will be cached (major or location)',
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
        $locationManager = $this->getContainer()->get('mi_search.location.manager');
        $router = $this->getContainer()->get('router');
        
        if ($table === 'major' || $table === 'all') {
            $baseUri = $router->generate('mi_autocomplete_major') . '?term=';
            $limit = $this->getContainer()->getParameter('mi_search.autocomplete.limit');
            $majors = $majorManager->findMajors();
            
            $output->writeln('Precaching majors:');
            
            $terms = array();
            foreach ($majors as $major) {
                $majorCanon = $major->getNameCanonical();
                $majorCanonLength = min(strlen($majorCanon), 8);
                for ($i = 1; $i <= $majorCanonLength; ++$i) {
                    $terms[substr($majorCanon, 0, $i)] = 1;
                }
            }
            
            $terms = array_keys($terms);
            $count = 0;
            foreach ($terms as $term) {
                $majorNames= array();
                $majors = $majorManager->findMajorsLike($term, $limit);
                foreach ($majors as $major) {
                    $majorNames[] = $major->getName();
                }
                
                $response = new JsonResponse();
                $response->setData(array('term' => isset($queryString['term']) ? $queryString['term'] : '', 'data' => $majorNames));
                $response->setCallback('majoredin.autocomplete_major_' . preg_replace('/[^\w]/', '_', $term));
                $this->getContainer()->get('mi_search.cache')->save($baseUri . $term . '&callback=majoredin.autocomplete_major_' . preg_replace('/[^\w]/', '_', $term), $response->getContent());
                $count++;
            }
            
            $output->writeln($count . ' terms precached.');
        }
        
        if ($table === 'location' || $table === 'all') {
            $baseUri = $router->generate('mi_autocomplete_location') . '?term=';
            $limit = $this->getContainer()->getParameter('mi_search.autocomplete.limit');
            $locations = $locationManager->findLocations();
        
            $output->writeln('Precaching locations:');
            
            $terms = array();
            foreach ($locations as $location) {
                $locationCanon = $location->getNameCanonical();
                $locationCanonLength = min(strlen($locationCanon), 8);
                for ($i = 1; $i <= $locationCanonLength; ++$i) {
                    $terms[substr($locationCanon, 0, $i)] = 1;
                }
            }
            
            $terms = array_keys($terms);
            $count = 0;
            foreach ($terms as $term) {
                $locationNames = array();
                $locations = $locationManager->findLocationsLike($term, $limit);
                foreach ($locations as $location) {
                    $locationNames[] = $location->getName();
                }
                
                $response = new JsonResponse();
                $response->setData(array('term' => isset($queryString['term']) ? $queryString['term'] : '', 'data' => $locationNames));
                $response->setCallback('majoredin.autocomplete_location_' . preg_replace('/[^\w]/', '_', $term));
                $this->getContainer()->get('mi_search.cache')->save($baseUri . $term . '&callback=majoredin.autocomplete_location_' . preg_replace('/[^\w]/', '_', $term), $response->getContent());
                $count++;
            }

            $output->writeln($count . ' terms precached.');
        }
    }
}
