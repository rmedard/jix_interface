<?php
/**
 * Created by PhpStorm.
 * User: medar
 * Date: 19/09/2018
 * Time: 19:59
 */

namespace Drupal\jix_interface\Plugin\Block;


use Drupal\Core\Block\BlockBase;

/**
 * Class JixServices
 * @package Drupal\jix_interface\Plugin\Block
 * @Block(
 *   id = "jix_services_block",
 *   admin_label = @Translation("JIX Services Block"),
 *   category = @Translation("Custom JIX Blocks")
 * )
 */
class JixServices extends BlockBase {

    /**
     * Builds and returns the renderable array for this block plugin.
     *
     * If a block should not be rendered because it has no content, then this
     * method must also ensure to return no content: it must then only return an
     * empty array, or an empty array with #cache set (with cacheability metadata
     * indicating the circumstances for it being empty).
     *
     * @return array
     *   A renderable array representing the content of the block.
     *
     * @see \Drupal\block\BlockViewBuilder
     */
    public function build()
    {
        return[
            '#theme' => 'jix_services',
        ];
    }
}