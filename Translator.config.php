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

    /* ----------------------------------------------------------------
    	Multi-language
    ------------------------------------------------------------------- */
		$multi_lang = $this->wire('modules')->get("InputfieldFieldset");
		$multi_lang->label = __("Multi-Language");
		$multi_lang->icon = "fa-language";
		$wrapper->add($multi_lang);

		//
		//	Setup Multilanguage
		//

		// include install file
		include("./install-multi-lang.php");

		$html = "
			<h3 class='uk-margin-remove' style='line-height:1;font-size:17px;'>
				Multi-language Setup
			</h3>
			<p> 1 click multi-language setup. This will install required modules, setup fields and rest of the multi-lang
			features...</p>
		";

		if($this->modules->get("Translator")->isMultiLang()) {
			$html .= "
				<a href='./edit?name=Translator&collapse_info=1&uninstall_multi_lang=1'
					class='uk-button uk-button-danger'
					onclick=\"modalConfirm('Uninstall Multi-Language?', 'This will uninstall and remove all multi-language related features...')\"
				>
				 <i class='fa fa-ban'></i>
					Uninstall
				</a>
			 ";
		} else {
			$html .= "
				<a href='./edit?name=Translator&collapse_info=1&install_multi_lang=1'
						class='uk-button uk-button-primary'
						onclick=\"modalConfirm('Setup Multi-Language?', 'This will install all required modules and setup related fields to multi-language...')\"
					>
						<i class='fa fa-cog'></i>
						Setup
				</a>
			";
		}

		$f = $this->wire('modules')->get("InputfieldMarkup");
		$f->value = $html;
		$multi_lang->add($f);

		//
		//	Fields to exclude from multi lang
		//

		$fields_array = [];
		$get_fields = $this->fields->find("type=FieldtypeText|FieldtypeTextLanguage|FieldtypeTextarea|FieldtypeTextareaLanguage");
		foreach($get_fields as $f) {
			$fields_array[$f->name] = !empty($f->label) ? $f->label : $f->name;
		}

		// exclude multi lang fields
		$f = $this->wire('modules')->get("InputfieldAsmSelect");
		$f->attr('name', 'exc_multilang_fields');
		$f->label = 'Exclude fields';
		$f->options = $fields_array;
		$f->columnWidth = "100%";
		$f->description = "Select fields that should not support multi-language...";
		$f->collapsed = $this->modules->get("Translator")->isMultiLang() ? "4" : "";
		$multi_lang->add($f);

    $inputfields->add($multi_lang);

		/* ----------------------------------------------------------------
			Info
		------------------------------------------------------------------- */
		$info = $this->wire('modules')->get("InputfieldFieldset");
		$info->label = __("Info");
		$info->icon = "fa-info";
		$wrapper->add($multi_lang);

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
