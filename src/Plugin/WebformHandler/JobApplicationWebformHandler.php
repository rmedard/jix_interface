<?php
/**
 * Created by PhpStorm.
 * User: medar
 * Date: 16/09/2018
 * Time: 12:27
 */

namespace Drupal\jix_interface\Plugin\WebformHandler;


use Drupal;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
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

    /**
     * @param WebformSubmissionInterface $webform_submission
     * @param array $message
     */
    public function sendMessage(WebformSubmissionInterface $webform_submission, array $message)
    {
        $jobId = $webform_submission->getElementData('job_application_job');
        $firstName = $webform_submission->getElementData('job_application_prenom');
        $lastName = $webform_submission->getElementData('job_application_nom');

//        $file_two = File::create([
//            'uri' => $file_attachment,
//            'filename' => 'object-oriented-php-for-drupal-developers.pdf',
//            'filemime' => 'application/pdf',
//        ]);

//        $params['files'][] = $file_two;
        $cvFileId = $webform_submission->getElementData('job_application_cv_file');
        if (intval($cvFileId) > 0) {
//            try {
//                $file_storage = Drupal::entityTypeManager()->getStorage('file');
//                $cvFile = $file_storage->load($cvFileId);
//                $cvFile->getFileUri();
            $cvFileObject = File::load($cvFileId);
            Drupal::logger('jix_mailer')
                ->info(t('Wubmitted CV: fileUri => @uri, fileName => @name, mime => @mime',
                        array(
                            '@uri' => $cvFileObject->getFileUri(),
                            '@name' => $cvFileObject->getFilename(),
                            '@mime' => $cvFileObject->getMimeType())
                    )
                );
//            } catch (InvalidPluginDefinitionException $e) {
//                Drupal::logger('jix_interface')->error('Error retrieving file: ' . $e);
//            } catch (PluginNotFoundException $e) {
//                Drupal::logger('jix_interface')->error('Error retrieving file: ' . $e);
//            }
        }

        $message['subject'] = t('New job application from: @firstName @lastName', ['@firstName' => $firstName, '@lastName' => $lastName]);
        if (intval($jobId) > 0) {
            $job = Node::load($jobId);
            $applicationsEmail = $job->get('field_email_where_to_send_applic')->value;
            $otherApplicationEmail = $job->get('field_additional_email_where_to')->value;

            $message['to_mail'] = array($applicationsEmail, Drupal::config('system.site')->get('mail'));
//            $message['to_mail'] = Drupal::config('system.site')->get('mail');
            if (isset($otherApplicationEmail)) {
                $message['cc_mail'] = $otherApplicationEmail;
            }
            Drupal::logger('jix_interface')
                ->info('Job application (Emails sent to): ' . $applicationsEmail . ', ' . Drupal::config('system.site')->get('mail'));
        } else {
            $message['to_mail'] = Drupal::config('system.site')->get('mail');
            Drupal::logger('jix_interface')
                ->info('Job application (Email sent to): ' . Drupal::config('system.site')->get('mail'));
        }
        $this->messenger()->addStatus(t('Your job application has been sent successfully.'));
        Drupal::logger('jix_interface')->info('Job application sent: ' . $webform_submission->id());
        return parent::sendMessage($webform_submission, $message);
    }
}