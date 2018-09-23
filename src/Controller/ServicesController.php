<?php
/**
 * Created by PhpStorm.
 * User: medar
 * Date: 23/09/2018
 * Time: 21:22
 */

namespace Drupal\jix_interface\Controller;


use Drupal\Core\Controller\ControllerBase;

/**
 * Class ServicesController
 * @package Drupal\jix_interface\Controller
 */
class ServicesController extends ControllerBase {

    public function content(){

        $renderable = [
            '#theme' => 'pages/jix_our_services',
            '#test_var' => 'test variable',
        ];
        return $renderable;
    }

}