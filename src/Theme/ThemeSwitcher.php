<?php
/**
 * Created by PhpStorm.
 * User: medard
 * Date: 14/09/2018
 * Time: 12:04
 */

namespace Drupal\jir_interface\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Symfony\Component\Routing\Route;

class ThemeSwitcher implements ThemeNegotiatorInterface {

    /**
     * Whether this theme negotiator should be used to set the theme.
     *
     * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
     *   The current route match object.
     *
     * @return bool
     *   TRUE if this negotiator should be used or FALSE to let other
     *   negotiators decide.
     */
    public function applies(RouteMatchInterface $route_match) {
        return $this->negotiateRoute($route_match) ? TRUE : FALSE;
    }

    /**
     * Determine the active theme for the request.
     *
     * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
     *   The current route match object.
     *
     * @return string|null
     *   The name of the theme, or NULL if other negotiators, like the
     *   configured default one, should be used instead.
     */
    public function determineActiveTheme(RouteMatchInterface $route_match) {
        return $this->negotiateRoute($route_match) ?: NULL;
    }


    private function negotiateRoute(RouteMatchInterface $routeMatch) {
        $route = $routeMatch->getRouteObject();
        \Drupal::logger('jix_interface')->warning('ThemeSwitcher launched!!');
        if ($route !== NULL and $route instanceof Route) {
            \Drupal::logger('jix_interface')
              ->warning('Path: ' . $route->getPath());
            if (preg_match('\/manage\/.*', $route->getPath())) {
                return 'seven';
            }
        }
        return FALSE;
    }
}