<?php

use Latte\Runtime as LR;

/** source: /opt/lampp/htdocs/sandbox/app/CoreModule/templates/Article/editor.latte */
final class Template6243db76f0 extends Latte\Runtime\Template
{
	protected const BLOCKS = [
		['title' => 'blockTitle', 'description' => 'blockDescription', 'content' => 'blockContent', 'scripts' => 'blockScripts'],
	];


	public function main(): array
	{
		extract($this->params);
		if ($this->getParentName()) {
			return get_defined_vars();
		}
		$this->renderBlock('title', get_defined_vars()) /* line 1 */;
		echo "\n";
		$this->renderBlock('description', get_defined_vars()) /* line 2 */;
		echo "\n";
		$this->renderBlock('content', get_defined_vars()) /* line 3 */;
		echo '

';
		$this->renderBlock('scripts', get_defined_vars()) /* line 8 */;
		return get_defined_vars();
	}


	public function prepare(): void
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	/** {block title} on line 1 */
	public function blockTitle(array $ʟ_args): void
	{
		echo 'Editor';
	}


	/** {block description} on line 2 */
	public function blockDescription(array $ʟ_args): void
	{
		echo 'Editor článků.';
	}


	/** {block content} on line 3 */
	public function blockContent(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);
		/* line 5 */ $_tmp = $this->global->uiControl->getComponent("editorForm");
		if ($_tmp instanceof Nette\Application\UI\Renderable) $_tmp->redrawControl(null, false);
		$_tmp->render();
		
	}


	/** {block scripts} on line 8 */
	public function blockScripts(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);
		$this->renderBlockParent($ʟ_nm = 'scripts', get_defined_vars()) /* line 9 */;
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.5.1/tinymce.min.js" integrity="sha512-rCSG4Ab3y6N79xYzoaCqt9gMHR0T9US5O5iBuB25LtIQ1Hsv3jKjREwEMeud8q7KRgPtxhmJesa1c9pl6upZvg==" crossorigin="anonymous"></script> <script type="text/javascript">
        tinymce.init({
            selector: \'textarea[name=content]\',
            plugins: [
                \'advlist autolink lists link image charmap print preview anchor\',
                \'searchreplace visualblocks code fullscreen\',
                \'insertdatetime media table contextmenu paste\'
            ],
            toolbar: \'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image\',
            entities: \'160,nbsp\',
            entity_encoding: \'raw\'
        });
    </script>
';
	}

}
