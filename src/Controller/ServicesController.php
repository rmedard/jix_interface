<?php
/**
 * Created by PhpStorm.
 * User: medar
 * Date: 23/09/2018
 * Time: 21:22
 */

namespace Drupal\jix_interface\Controller;


use Drupal\Core\Controller\ControllerBase;

class ServicesController extends ControllerBase {

    public function content(){
        return[
            '#theme' => 'pages/jix_services'
        ];
    }

}