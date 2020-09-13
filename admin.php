<?php namespace ProcessWire;
/**
 *  Translator tabs
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2020 kraetivan.net
 *  @link http://www.kraetivan.net
 *  @license http://www.gnu.org/licenses/gpl.html
 *
 *  @var Module $this_module
 *  @var string $page_name
 *  @var Module $helper
 *
 *
*/

if($this->user->hasPermission('translator')) {

  include("tabs.php");

  $lang = (!$this->input->get->lang) ? "default" : $this->input->get->lang;
  $json = "{$this_module->lngFolder}{$lang}.json";
  $json = file_get_contents($json);
  $json = json_decode($json);

  // Build form
  $form = $this->modules->get("InputfieldForm");
  $form->action = "./?lang={$this->input->get->lang}";
  $form->method = "post";
  $form->attr("id+name","translations-form");

  $f = $this->modules->get("InputfieldTextarea");
  $f->attr('name', "custom_strings");
  $f->label = "Custom";
  $f->value = !empty($json->custom_strings) ? $json->custom_strings : "";
  $f->rows = "10";
  $f->description = "Here you can add custom translations. For strings that are not picked up automatically (usually come from modules)...";
  $f->notes = "One per line in fomat: `Example=Example Translation`";
  if($this_module->getStringsArray()) $f->collapsed = "2";
  $form->append($f);

  if($this_module->getStringsArray()) {
    foreach($this_module->getStringsArray() as $key => $value) {
      $field_name = $this_module->encode($key);
      $f = $this->modules->get("InputfieldText");
      $f->attr('name', $field_name);
      $f->label = "$key";
      $f->value = !empty($json->{$key}) ? $json->{$key} : "";
      // $f->notes = $value;
      if($this_module->hide_populated == "1") $f->collapsed = "5";
      $form->append($f);
    }
  }

  // Submit
  $submit = $this->modules->get("InputfieldSubmit");
  $submit->attr("value","Save");
  $submit->attr("id+name","submit_save");
  $form->append($submit);

  echo $form->render();

} else {

  $this->error("You dont have permission to access this page");

}

?>

<style>
#translations-form .InputfieldStateCollapsed:not(.Inputfield_custom_strings) {
  display:none !important;
}
</style>
