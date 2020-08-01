<?php
/**
 *  Multi-Lang
 *
 */

$helper = $this->modules->get("KreativanHelper");

$input = $this->input;
$modules = $this->modules;
$fields = $this->fields;
$languages = $this->languages;
$session = $this->session;

// Array of field names to exclude
$exclude_fields = $this->exc_multilang_fields;

/* ----------------------------------------------------------------
  Install
------------------------------------------------------------------- */

if($this->input->get->install_multi_lang) {

  $lng_modules_install = [
    "LanguageSupportFields",
    "LanguageSupportPageNames",
    "LanguageTabs",
  ];

  // Install Modules
  foreach($lng_modules_install as $m) {
    if(!$this->modules->isInstalled($m)) $this->modules->install($m);
  }

  //
  //  Text
  //

  $text_fields = $fields->find("type=FieldtypeText|FieldtypeTextLanguage");
  foreach($text_fields as $f) {
    if(!in_array($f->name, $exclude_fields)) {
      $f->setFieldtype("FieldtypeTextLanguage");
      $f->save();
    }
  }

  foreach($text_fields as $f) {
    $f->textformatters = ["TextformatterHannaCode"];
    $f->save();
  }


  //
  // Textarea Fields
  //

  $textarea_fields = $fields->find("type=FieldtypeTextarea|FieldtypeTextareaLanguage");
  foreach($textarea_fields as $f) {
    if(!in_array($f->name, $exclude_fields)) {
      $f->textformatters = ["TextformatterNewlineBR", "TextformatterHannaCode"];
      $f->save();
    }
  }

  foreach($textarea_fields as $f) {
    $f->textformatters = ["TextformatterNewlineBR", "TextformatterHannaCode"];
    $f->save();
  }


  //
  //  Title
  //

  $title = $fields->get("title");
  $title->setFieldtype("FieldtypePageTitleLanguage");
  $title->save();
  $title->textformatters = ["TextformatterHannaCode"];
  $title->save();


  //
  //  body field
  //

  $toolbar = "Format, Styles, -, Bold, Italic, -, RemoveFormat\r\nJustifyLeft,JustifyCenter,JustifyRight\r\nNumberedList, BulletedList, -, Blockquote\r\nPWLink, Unlink, Anchor\r\nPWImage, Table, HorizontalRule, SpecialChar\r\nPasteText, PasteFromWord\r\nScayt, -, Sourcedialog";

  $f = $fields->get("body");
  $f->textformatters = ["TextformatterHannaCode", "TextformatterVideoOrSocialPostEmbed"];
  $f->inputfieldClass = "InputfieldCKEditor";
  $f->toolbar = $toolbar;
  $f->rows = "10";
  $f->save();

  $f = $fields->get("body_small");
  if($f && !empty($f) && $f != "") {
    $f->textformatters = ["TextformatterHannaCode", "TextformatterVideoOrSocialPostEmbed"];
    $f->inputfieldClass = "InputfieldCKEditor";
    $f->toolbar = $toolbar;
    $f->rows = "5";
    $f->save();
  }

  // Redirect
  $session->redirect("./edit?name=Translator&collapse_info=1");

}


/* ----------------------------------------------------------------
  Uninstall
------------------------------------------------------------------- */

if($this->input->get->uninstall_multi_lang) {

  // delete all languages except default one
  if(!empty($languages)) {
    foreach($languages as $lng) {
      if($lng->name != "default") $lng->delete();
    }
  }


  //
  // Text
  //

  $text_fields = $fields->find("type=FieldtypeTextLanguage");
  foreach($text_fields as $f) {
    $f->setFieldtype("FieldtypeText");
    $f->save();
  }

  foreach($text_fields as $f) {
    $f->textformatters = ["TextformatterHannaCode"];
    $f->save();
  }

  //
  // Textarea
  //

  $textarea_fields = $fields->find("type=FieldtypeTextareaLanguage");
  foreach($textarea_fields as $f) {
    $f->setFieldtype("FieldtypeTextarea");
    $f->save();
  }

  foreach($textarea_fields as $f) {
    $f->textformatters = ["TextformatterHannaCode"];
    $f->save();
  }

  //
  // Title
  //

  $title = $fields->get("title");
  $title->setFieldtype("FieldtypePageTitle");
  $title->save();
  $title->textformatters = ["TextformatterHannaCode"];
  $title->save();


  //
  // body
  //

  $toolbar = "Format, Styles, -, Bold, Italic, -, RemoveFormat\r\nJustifyLeft,JustifyCenter,JustifyRight\r\nNumberedList, BulletedList, -, Blockquote\r\nPWLink, Unlink, Anchor\r\nPWImage, Table, HorizontalRule, SpecialChar\r\nPasteText, PasteFromWord\r\nScayt, -, Sourcedialog";

  $f = $fields->get("body");
  $f->textformatters = ["TextformatterHannaCode", "TextformatterAutoLinks"];
  $f->inputfieldClass = "InputfieldCKEditor";
  $f->toolbar = $toolbar;
  $f->save();


  //
  // Uninstall Modules
  //

  // NOTE: DO NOT CHANGE ORDER OF THE MODULES
  $lng_modules_uninstall = [
    "FieldtypePageTitleLanguage",
    "FieldtypeTextLanguage",
    "FieldtypeTextareaLanguage",
    "LanguageSupportPageNames",
    "LanguageSupportFields",
    "LanguageTabs",
  ];

  foreach($lng_modules_uninstall as $m) {
    if($this->modules->isInstalled($m)) $this->modules->uninstall($m);
  }

  // Redirect
  $session->redirect("./edit?name=Translator&collapse_info=1");

}
