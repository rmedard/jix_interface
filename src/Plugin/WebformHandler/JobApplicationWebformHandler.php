<?php
/**
 * Created by PhpStorm.
 * User: medar
 * Date: 16/09/2018
 * Time: 12:27
 */

namespace Drupal\jix_interface\Plugin\WebformHandler;


use Drupal;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\node\NodeInterface;
use Drupal\webform\Plugin\WebformHandler\EmailWebformHandler;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Class JobApplicationWebformHandler
 * @package Drupal\jix_interface\Plugin\WebformHandler
 * @WebformHandler(
 *   id = "job_application_email",
 *   label = @Translation("Job Application Email"),
 *   category = @Translation("Notification"),
 *   description = @Translation("Sends webform submission (job application) to a employer email address."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class JobApplicationWebformHandler extends EmailWebformHandler
{

    public function sendMessage(WebformSubmissionInterface $webform_submission, array $message)
    {
        $job = $webform_submission->getElementData('job_application_job');
        if (!is_null($job) and $job instanceof NodeInterface and $job->bundle() == 'job') {
            try {
                $employer = $job->get('field_job_company_name')->first()->get('entity')->getTarget()->getValue();
                MessengerInterface::addMessage('Send email to Jix and employer', MessengerInterface::TYPE_STATUS);
            } catch (MissingDataException $e) {
                Drupal::logger('jix_interface')
                    ->error(t('Missing Employer for job id: @jobId', ['@jobId' => $job->id()]));
            }
        } else {
            Drupal::logger('jix_interface')
                ->info(t('Empty job, might be unsolicited application'));
            MessengerInterface::addMessage('Send email to Jix only', MessengerInterface::TYPE_STATUS);
        }
        MessengerInterface::addMessage('Send handler called', MessengerInterface::TYPE_WARNING);
        return parent::sendMessage($webform_submission, $message);
    }
}