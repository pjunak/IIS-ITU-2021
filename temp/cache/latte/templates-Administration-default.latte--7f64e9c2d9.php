<?php

use Latte\Runtime as LR;

/** source: /opt/lampp/htdocs/sandbox/app/CoreModule/templates/Administration/default.latte */
final class Template7f64e9c2d9 extends Latte\Runtime\Template
{
	protected const BLOCKS = [
		['title' => 'blockTitle', 'description' => 'blockDescription', 'content' => 'blockContent'],
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
		echo 'Administrace webu';
	}


	/** {block description} on line 2 */
	public function blockDescription(array $ʟ_args): void
	{
		echo 'Administrace webu.';
	}


	/** {block content} on line 3 */
	public function blockContent(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);
		echo '<p>Vítejte v administraci!</p>
<h2><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Article:editor")) /* line 5 */;
		echo '">Přidat článek</a></h2>
<h2><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Article:list")) /* line 6 */;
		echo '">Seznam článků</a></h2>';
	}

}
