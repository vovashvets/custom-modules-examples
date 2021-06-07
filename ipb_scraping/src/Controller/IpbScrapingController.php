<?php

namespace Drupal\ipb_scraping\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\http_client_manager\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;


/**
 * Class IpbScraperController.
 *
 * @package Drupal\testing_services\Controller
 */
class IpbScrapingController extends ControllerBase {


  /**
   * IpbScraper route callback.
   */
  public function mainScraper() {

    // Loads initial values for processing.
    $sourceURL = "https://canitbeallsosimple.com/";
    $content = file_get_contents($sourceURL);
    $links_content = strip_tags($content, "<a>");
    $sub_string = preg_split("/<\/a>/",$links_content);
    $links_to_print = [];

    // Initial cleaning of the returned values.
    // Only allows links with <a> tags.
    foreach ( $sub_string as $val ){
      if( strpos($val, "<a href=") !== FALSE ){
        $val = preg_replace("/.*<a\s+href=\"/sm","",$val);
        $val = preg_replace("/\".*/","",$val);
        $links_to_print[] = $val;
      }
    }

    // Purgues the array of results. 
    $purgued_links = array_filter($links_to_print, 
      function ($value) {
        $response = false;
        // Purgues strings without year in link.
        if(preg_match("/[12][0-9]{3}/", $value) == 1) {
          $response = true;
        }
        return $response;
      }, 0);

    // Proccessing the item links for internal paths. 
    $paths = [];
    $i = 0;
    foreach($purgued_links as $purgued_link) {
      $key = rtrim(substr($purgued_link, 42), "/");
      $paths['values'][$i]['link'] =  Url::fromRoute('ipb_scraping.item', ['id' => $key])->toString();
      $paths['values'][$i]['naming'] = ucwords(str_replace('-', ' ', $key));
      $i++;
    }
    $paths['count'] = $i;

    // Builds info blocks for the returned render array.
    $build['welcome'] = [
      '#type' => 'markup',
      '#markup' => '<br><hr>' . $this->t('Web Scraping from Drupal!') . '<hr> <br>',
    ];

    $build['resume'] = [
       '#type' => 'markup',
       '#markup' => '<br><hr>' . $this->t('Now we got: ') . 
                    count($purgued_links)  . ' Elements for scraping.' . 
                    '<hr><br>',
    ];

    $build['received'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $purgued_links,
    ];

    $build['links'] = [
      '#theme' => ['item-link-results'],
      '#items' => $paths,
      '#empty' => [
        '#markup' => '<h3>' . $this->t('Your search yielded no results.') . '</h3>',
      ],
    ];
    return $build;
  }

 /**
   * IpbScraper secondary route callback.
   */
  public function secondaryScraper($id) {

     // Loads initial values for processing.
    $sourceURL = "https://canitbeallsosimple.com/";
    $content = file_get_contents($sourceURL);
    $links_content = strip_tags($content, "<a>");
    $sub_string = preg_split("/<\/a>/",$links_content);
    $links_to_print = [];

    // Initial cleaning of the returned values.
    // Only allows links with <a> tags.
    foreach ( $sub_string as $val ){
      if( strpos($val, "<a href=") !== FALSE ){
        $val = preg_replace("/.*<a\s+href=\"/sm","",$val);
        $val = preg_replace("/\".*/","",$val);
        $links_to_print[] = $val;
      }
    }

    // Purgues the array of results. 
    $purgued_links = array_filter($links_to_print, 
      function ($value) {
        $response = false;
        // Purgues strings without year in link.
        if(preg_match("/[12][0-9]{3}/", $value) == 1) {
          $response = true;
        }
        return $response;
      }, 0);

      foreach($purgued_links as $purgued_link) {
        if(str_contains($purgued_link, $id)) {
          $my_post = file_get_contents($purgued_link);
        }
      }

     $build['welcome'] = [
      '#type' => 'markup',
      '#markup' => '<br><hr>' . $this->t('Web Scraping from Drupal!') . 
                   '<hr> <br>' . $id,
    ];

    return $build;

  }

}
