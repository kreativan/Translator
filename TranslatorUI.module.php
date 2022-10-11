<?php
/**
 *  Translator
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2020 kraetivan.net
 *  @link http://www.kraetivan.net
 *  @license http://www.gnu.org/licenses/gpl.html
 *
*/

class TranslatorUI extends Process implements WirePageEditor {

  // for WirePageEditor
  public function getPage() {
    return $this->page;
  }

  public function __construct() {
    $this->translator = wire("modules")->get("Translator");
    $this->lngFolder = $this->config->paths->assets . "translator/";
  }

  public function init() {
    parent::init(); // always remember to call the parent init

    if($this->input->scan) {
      $this->translator->scan($this->config->paths->templates);
      $this->message("Scan complete");
      $this->session->redirect("./?lang={$this->input->get->lang}");
    }

    if($this->input->get->show_empty) {
      $new_data = [
        "hide_populated" => ($this->translator->hide_populated == "1") ? "2" : "1",
      ];
      $old_data = $this->modules->getModuleConfigData($this->className());
      $data = array_merge($old_data, $new_data);
      $this->modules->saveConfig($this->className(), $data);
      $this->session->redirect("./?lang={$this->input->get->lang}");
    }

    if($this->input->post->submit_save || $this->input->post->translator_submit) {

      // Construct array from POST
      $data = $this->input->post;
      $array = [];
      foreach($data as $key => $value) {
        if($key != "submit_save" && $value != "") {
          $key = $this->translator->decode($key);
          $array[$key] = $value;
        }
      }

      // remove last item from array: token
      array_pop($array);

      // Conver to json with JSON_UNESCAPED_UNICODE, to support Cyrillic etc...
      $json = json_encode($array, JSON_UNESCAPED_UNICODE);

      // Let's save it in a file
      $file_name = (!$this->input->get->lang) ? "default" : $this->input->get->lang;
      $this->translator->save("{$this->translator->lngFolder}{$file_name}.json", $json);

      $this->session->redirect("./?lang={$this->input->get->lang}");

    }

  }

  /* ----------------------------------------------------------------
    Process
  ------------------------------------------------------------------- */
  
  /**
   *  Execute
   *  Module Page
   *  @method includeAdminFile()
   *
   */
  public function ___execute() {

    // set a new headline, replacing the one used by our page
    // this is optional as PW will auto-generate a headline
    $this->headline('Translator');

    // add a breadcrumb that returns to our main page
    // this is optional as PW will auto-generate breadcrumbs
    $this->breadcrumb('./', 'Translator');

    // include admin file
    return [
      "this_module" => $this,
      "page_name" => "main"
    ];
  }

}
