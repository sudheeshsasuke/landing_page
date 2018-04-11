<?php
namespace Drupal\landing_page_text_format\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormState;

/**
 * @Filter(
 *   id = "filter_landingpage",
 *   title = @Translation("Landing Page Filter"),
 *   description = @Translation("New text format created for landing page module."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class LandingpageFilter extends FilterBase {
  
  public function process($text, $langcode) {
    preg_match_all('/\\[([a-zA-z0-9_\-]+:[a-zA-z0-9_\-]+)+\\]/m', $text, $match);
    $matches = $match[0];
    foreach ($matches as $content_token) {
      $content_token_name = str_replace(array( '[', ']' ), '', $content_token);
      $type = explode(':', $content_token_name);
  
      // For view blocks, custom blocks, system blocks.
      // format [block:module_name:block_id].
      if ($type[0] == 'block') {
        $module = trim($type[1]);
        $bid = trim($type[2]);
        $block = module_invoke($module, 'block_view', $bid);
        $replacement = render($block['content']);
      }
  
      // For drupal webforms
      // Format [webform:node_id]
      // if ($type[0] == 'webform') {
      //   $node_id = trim($type[1]);
      //   $node = node_load($node_id);
      //   $contact_form = drupal_get_form('webform_client_form_' . $node_id, $node);
      //   $replacement = drupal_render($contact_form);
      // }
  
      // For drupal forms
      // Format [form:form_id]
      if ($type[0] == 'form') {
        $form_id = trim($type[1]);
        //$form_state = array();
        

        $form_state = new FormState();
        //$form_state['build_info']['args'] = array();
        //$form_state['build_info']['files']['menu'] = array();
        //$form = \Drupal::formBuilder()->getForm('\Drupal\resume\Form\ResumeForm');
        $form = \Drupal::formBuilder()->buildForm($form_id, $form_state);
        $replacement = render($form);
      }
      $text = str_replace($content_token, $replacement, $text);
    }
    return new FilterProcessResult($text);
  }
}

?>
