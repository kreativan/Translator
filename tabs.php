<?php
/**
 *  Translator tabs
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2020 kraetivan.net
 *  @link http://www.kraetivan.net
 *  @license http://www.gnu.org/licenses/gpl.html
 *
*/

$lang_name = !$input->get->lang ? "default" : $input->get->lang;

?>

<ul class="uk-tab uk-position-relative">

  <li class="<?= (!$this->input->get->lang) ? "uk-active" : ""; ?>">
    <a href="<?= $page->url ?>">
      <?= !empty($user->language) ? ucfirst($user->language->name)." ({$user->language->title})" : "Default" ?>
    </a>
  </li>

  <?php if(!empty($languages) && count($languages) > 1) :?>
    <?php foreach($languages as $lang) :?>
      <?php if($lang->name != "default") :?>
        <li class="<?= ($input->get->lang == $lang->name) ? "uk-active" : ""; ?>">
          <a href="<?= $page->url ?>?lang=<?= $lang->name ?>">
            <?= ucfirst($lang->title) ?> (<?= $lang->name ?>)
          </a>
        </li>
      <?php endif;?>
    <?php endforeach;?>
  <?php endif;?>

  <li>
    <a href="#"
      data-form="#translations-form" data-action="submit_save"
      title="Save" uk-tooltip
      onclick="formSubmitConfirm('Are you sure?', 'Save translations for <b><?= $lang_name ?></b> language')"
    >
      <i class="fas fa-save"></i>
    </a>
  </li>

  <li>
    <a href="./?show_empty=1&lang=<?= $input->get->lang ?>"
      title="<?= ($this_module->hide_populated == "1") ? "Show all fields" : "Show only empty fields" ?>" uk-tooltip
    >
      <i class="fas fa-toggle-<?= ($this_module->hide_populated == "1") ? "off" : "on" ?>"></i>
    </a>
  </li>

  <li>
    <a href="./?scan=1&lang=<?= $input->get->lang ?>" title="Scan for files" uk-tooltip>
      <i class="fas fa-sync-alt" onclick="spinIcon()"></i>
    </a>
  </li>

  <?php if($user->isSuperuser()):?>
    <li>
      <a href="<?= $modules->getModuleEditUrl($this_module); ?>" title="Module Settings">
        <i class="fas fa-cog"></i>
      </a>
    </li>
  <?php endif;?>

</ul>
