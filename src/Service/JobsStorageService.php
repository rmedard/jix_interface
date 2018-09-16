<?php
/**
 * Created by PhpStorm.
 * User: medar
 * Date: 14/09/2018
 * Time: 21:44
 */

namespace Drupal\jix_interface\Service;


use Drupal;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

class JobsStorageService
{
    protected $entityTypeManager;

    /**
     * JobsStorageService constructor.
     * @param $entityTypeManager
     */
    public function __construct(EntityTypeManager $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }

    public function unPublishJobsByEmployer(NodeInterface $employer)
    {
        try {
            $storage = $this->entityTypeManager->getStorage('node');
            $activeJobsIds = $storage->getQuery()
                ->condition('type', 'job')
                ->condition('status', Node::PUBLISHED)
                ->condition('field_job_company_name.target_id', $employer->id())
                ->execute();
            if (count($activeJobsIds) > 0) {
                $jobs = $storage->loadMultiple($activeJobsIds);
                foreach ($jobs as $job) {
                    $job->setPublished(false);
                    $job->save();
                }
            }
        } catch (InvalidPluginDefinitionException $e) {
            Drupal::logger('jix_interface')->error('Invalid plugin: ' . $e->getMessage());
        } catch (PluginNotFoundException $e) {
            Drupal::logger('jix_interface')->error('Plugin not found: ' . $e->getMessage());
        } catch (EntityStorageException $e) {
            Drupal::logger('jix_interface')->error('Storage error: ' . $e->getMessage());
        }
    }

}