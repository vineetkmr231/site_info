<?php
/**
 * @file
 * Contains \Drupal\site_info\Controller\SiteController.
 */
 
namespace Drupal\site_info\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
 
class SiteController extends ControllerBase {
  public function content($json) {
  $node_data = \Drupal\node\Entity\Node::load($json);
  if(empty($node_data)){
   throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
  }
  if($node_data->getType()!="page"){
   throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
  }
  else{
    $db = \Drupal::database();
    $query = $db->select('node', 'n');
    $query->fields('n', array('nid','vid','type','uuid','langcode'));
    $query->fields('nb', array('status','title','uid','created','changed','promote'));
    $query->fields('nbt', array('entity_id','body_value','body_format'));
    $query->join('node_field_data','nb', 'n.nid=nb.nid');
    $query->join('node__body','nbt', 'n.nid=nbt.entity_id');
    $query->condition('n.nid', $json, "=");
    $result = $query->execute();
    foreach($result as $row){
      return new JsonResponse($row);
    }
  }
 }
}
