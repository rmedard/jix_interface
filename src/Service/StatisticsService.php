<?php
/**
 * Created by PhpStorm.
 * User: medar
 * Date: 15/07/2018
 * Time: 21:10
 */

namespace Drupal\jir_interface\Service;


use Drupal;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityTypeManager;

class StatisticsService
{
    protected $entityTypeManager;

    /**
     * StatisticsService constructor.
     * @param $entityTypeManager
     */
    public function __construct(EntityTypeManager $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }

    public function countContentEntities($entityType) {
        try {
            $storage = $this->entityTypeManager->getStorage('node');
            return $storage->getQuery()->condition('type', $entityType)->count()->execute();
        } catch (InvalidPluginDefinitionException $e) {
            Drupal::logger('jir_interface')->error('Invalid plugin: ' . $e->getMessage());
        } catch (PluginNotFoundException $e) {
            Drupal::logger('jir_interface')->error('Plugin not found: ' . $e->getMessage());
        }
        return 0;
    }

}