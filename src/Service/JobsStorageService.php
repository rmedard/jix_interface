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
use Drupal\Core\Datetime\DrupalDateTime;
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

    public function publishEmployerByJob(NodeInterface $job) {
        try {
            $storage = $this->entityTypeManager->getStorage('node');
            $employer_id = $storage->getQuery()
              ->condition('type', 'employer')
              ->condition('status', Node::NOT_PUBLISHED)
              ->condition('nid', $job->get('field_job_company_name')->value)
              ->execute();
            if (count($employer_id) == 1) {
                $employer = Node::load($employer_id);
                $employer->setPublished(true);
                $employer->save();
            }
        } catch (InvalidPluginDefinitionException $e) {
            Drupal::logger('jix_interface')->error('Invalid plugin: ' . $e->getMessage());
        } catch (PluginNotFoundException $e) {
            Drupal::logger('jix_interface')->error('Plugin not found: ' . $e->getMessage());
        } catch (EntityStorageException $e) {
            Drupal::logger('jix_interface')->error('Storage error: ' . $e->getMessage());
        }
    }

    public function unPublishExpiredJobs() {
        try {
            $storage = $this->entityTypeManager->getStorage('node');
            $expiredJobsIds = $storage->getQuery()
                ->condition('type', 'job')
                ->condition('status', Node::PUBLISHED)
                ->condition('field_job_appl_deadline', new DrupalDateTime(), '<')
                ->execute();
            if (isset($expiredJobsIds) && count($expiredJobsIds) > 0) {
                foreach ($storage->loadMultiple($expiredJobsIds) as $job){
                    $job->setPublished(FALSE);
                    $job->save();
                    Drupal::logger('jix_interface')
                        ->notice(t('Job ID: @job_id unpublished after expiration.', ['@job_id' => $job->id()]));
                }
            } else {
                Drupal::logger('jix_interface')->notice(t('No expired jobs to unPublish'));
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