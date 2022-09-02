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

class Translator extends Process implements WirePageEditor {

  // for WirePageEditor
  public function getPage() {
    return $this->page;
  }

  public function __construct() {
    $this->textFile = $this->config->paths->templates . "translator/files.text";
    $this->folder = $this->config->paths->siteModules . $this->className() . "/";
    $this->lngFolder = $this->config->paths->templates . "translator/";
    $this->exclude = ["errors", "less", "lib"];
    $this->encode_arr = [
      "." => "|_d_|",
      ":" => "|_dd_|",
      "!" => "|_e_|",
      "?" => "|_q_|",
      "," => "|_c_|",
      "'" => "|_s_|",
      '"' => "|_ds_|",
      "%" => "|_prc_|",
      "(" => "|_p1_|",
      ")" => "|_p2_|",
      "{" => "|_pl1_|",
      "}" => "|_pl2_|",
      "[" => "|_pxl1_|",
      "]" => "|_pxl2_|",
    ];
  }

  public function init() {
    parent::init(); // always remember to call the parent init

    if($this->translations_folder != "") {
      $this->lngFolder = $this->config->paths->root . $this->translations_folder;
      $this->lngFolder = str_replace("//", "/",  $this->lngFolder);
      $this->textFile = $this->config->paths->root . $this->translations_folder . "files.text";
      $this->textFile = str_replace("//", "/",  $this->textFile);
    }

    if(!is_dir($this->lngFolder)) $this->files->mkdir($this->lngFolder);

    if($this->input->scan) {
      $this->scan($this->config->paths->templates);
      $this->message("Scan complete");
      $this->session->redirect("./?lang={$this->input->get->lang}");
    }

    if($this->input->get->show_empty) {
      $new_data = [
        "hide_populated" => ($this->hide_populated == "1") ? "2" : "1",
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
          $key = $this->decode($key);
          $array[$key] = $value;
        }
      }

      // remove last item from array: token
      array_pop($array);

      // Conver to json with JSON_UNESCAPED_UNICODE, to support Cyrillic etc...
      $json = json_encode($array, JSON_UNESCAPED_UNICODE);

      // Let's save it in a file
      $file_name = (!$this->input->get->lang) ? "default" : $this->input->get->lang;
      $this->save("{$this->lngFolder}{$file_name}.json", $json);

      $this->session->redirect("./?lang={$this->input->get->lang}");

    }

  }

  /* ----------------------------------------------------------------
    Methods
  ------------------------------------------------------------------- */

  // Encode string to the valid field_name
  public function encode($string) {
    $field_name = str_replace(" ", "|_|", $string);
    foreach($this->encode_arr as $key => $value) {
      $field_name = str_replace("$key", "$value", $field_name);
    }
    return $field_name;
  }

  // decode field name back to the string
  public function decode($string) {
    $field_name = str_replace("|_|", " ", $string);
    foreach($this->encode_arr as $key => $value) {
      $field_name = str_replace("$value", "$key", $field_name);
    }
    return $field_name;
  }

  /**
   *  Get Translations Array
   *  @param $lang_name - current active language name: $user->language->name;
   *  @example $this->array($user->language->name);
   *  @return array
   */
  public function array($lang_name = "default") {

    // if json translation file doesnt exits return empty array
    $json_file = $this->lngFolder.$lang_name.".json";
    if(!file_exists($json_file)) return [];

    // get language json
    $json = file_get_contents($json_file);
    $array = json_decode($json, true);

    $custom_strings_array = $this->getCustomStringArray($lang_name);

    if (count($custom_strings_array) > 0) {
      array_shift($array);
      $array = array_merge($array, $custom_strings_array);
    }

    return $array;

  }

  // Get custom_string and convert it to array
  public function getCustomStringArray($lang_name = "default") {
    $array = [];
    $json = file_get_contents($this->lngFolder.$lang_name.".json");
    $json = json_decode($json);
    if($json && !empty($json->custom_strings)) {
    $split_arr = explode("\r\n",$json->custom_strings);
      foreach($split_arr as $item) {
        $a = explode("=",$item);
        $array[$a[0]] = $a[1];
      }
    }
    return $array;
  }

  // save translations to a json file
  public function save($file_name, $data) {
    file_put_contents($file_name, $data);
  }

  // get all translatable string
  public function getStringsArray() {
    $array = [];
    $files_array = $this->getFilesArray();
    foreach($files_array as $file) {
      $matches = $this->parseFile($file);
      if(!empty($matches[3]) && count($matches[3]) > 0) {
        foreach($matches[3] as $item) $array[$item] = $file;
      }
    }
    return count($array) > 0 ? $array : false;
  }

  // Get all files that contain translatable strings
  public function getFilesArray() {
    $arr = [];
    $string = file_get_contents($this->textFile);
    $string = str_replace("//", "/", $string);
    $array = explode("||", $string);
    foreach($array as $item) {
      if(substr($item, -4) === ".php") {
        $arr[] = $item;
      }
    }
    return $arr;
  }

  /**
   *  Find all files in a folde recursively
   *  and save paths in a text file
   *  @see $this->scanDirs();
   */
  public function scan($dir) {

    $current = file_get_contents($this->textFile);
    $empty = "";
    file_put_contents($this->textFile, $empty);

    $this->scanDirs($dir);

  }

  /**
   *  Use php DirectoryIterator class
   *  to find files and folders recursively
   *  @see https://www.php.net/manual/en/class.directoryiterator.php
   *  @param string $dir starting directory
   *
   */
  public function scanDirs($dir) {
    $iter = new DirectoryIterator($dir);
    foreach($iter as $item) {
      if ($item != '.' && $item != '..' && !in_array($item, $this->exclude) ) {
        if($item->isDir()) {
          $this->scanDirs("{$dir}/{$item}");
        } elseif($item->getExtension() == "php") {
          $data = $dir . "/" . $item->getFilename() . "||";
          $current = file_get_contents($this->textFile);
          $current .= $data;
          file_put_contents($this->textFile, $current);
        }
      }
    }
  }

  /**
   *  Parse Files
   *  Find all translatable strings in a file
   *  @param string $file
   *  @see /wire/modules/languageSupport/languageParser.php
   */

  public function parseFile($file) {
  	$matches = [];
  	if(!is_file($file)) return $matches;
  	$data = file_get_contents($file);
  	// Find __('text', textdomain) style matches
  	preg_match_all(	'/([\s.=(]__|^__)\(\s*' . 		// __(
  		'([\'"])(.+?)(?<!\\\\)\\2\s*' . 	// "text"
  		'(?:,\s*[^)]+)?\)+(.*)$/m', 		// , textdomain (optional) and everything else
  	$data, $matches);
  	return $matches;
  }

  // Check if multilanguage is installed
  public function isMultiLang($debug = false) {

    $errors = [];

    $lng_modules = [
      "FieldtypePageTitleLanguage",
      "FieldtypeTextLanguage",
      "FieldtypeTextareaLanguage",
      "LanguageSupport",
      "LanguageSupportPageNames",
      "LanguageSupportFields",
      "LanguageTabs",
    ];

    foreach($lng_modules as $m) {
      if($this->modules->isInstalled($m) === false) {
        $errors[] = $m . " is missing.";
      }
    }

    if($debug === true) {
      return count($errors) > 0 ? $errors : true;
    } else {
      return count($errors) > 0 ? false : true;
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
