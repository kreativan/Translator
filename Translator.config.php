<?php
class TranslatorConfig extends ModuleConfig {

	public function getInputfields() {
		$inputfields = parent::getInputfields();
    $wrapper = new InputfieldWrapper();

		/* ----------------------------------------------------------------
			Options
		------------------------------------------------------------------- */
		$options = $this->wire('modules')->get("InputfieldFieldset");
		$options->label = __("Translator Options");
		$options->icon = "fa-cog";
		$wrapper->add($options);

		// hide_admin
		$f = $this->wire('modules')->get("InputfieldRadios");
		$f->attr('name', 'hide_populated');
		$f->label = 'Hide populated fields';
		$f->options =['1' => "Yes",'2' => "No"];
		$f->required = true;
		$f->defaultValue = "2";
		$f->optionColumns = 1;
		$f->columnWidth = "100%";
		$options->add($f);

		$inputfields->add($options);

    // folder
		$f = $this->wire('modules')->get("InputfieldText");
		$f->attr('name', 'translations_folder');
		$f->label = 'Translator Folder';
		$f->required = false;
		$f->placeholder = "/site/templates/translator/";
		$f->optionColumns = 1;
		$f->columnWidth = "100%";
		$options->add($f);

		/* ----------------------------------------------------------------
			Info
		------------------------------------------------------------------- */
		$info = $this->wire('modules')->get("InputfieldFieldset");
		$info->label = __("Info");
		$info->icon = "fa-info";
		$wrapper->add($info);

		$f = $this->wire('modules')->get("InputfieldMarkup");
		$f->value = "
			<p>To use translator, you just need to pass translator array to <em>wireLangReplacements()</em> method.<br />
				Add this to your <em>_init.php</em> file:<br />
				<code>
					\$translator = \$modules->get('Translator'); <br />
					wireLangReplacements(\$translator->array(\$user->language->name));
				</code>
			</p>
		";
		$info->add($f);

		$inputfields->add($info);

		// Render fields
		// ========================================================================
		return $inputfields;


	}

}
