<?php

use Latte\Runtime as LR;

/** source: /opt/lampp/htdocs/sandbox/app/CoreModule/templates/Article/list.latte */
final class Template27fdda5a14 extends Latte\Runtime\Template
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
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			foreach (array_intersect_key(['article' => '5'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	/** {block title} on line 1 */
	public function blockTitle(array $ʟ_args): void
	{
		echo 'Výpis článků';
	}


	/** {block description} on line 2 */
	public function blockDescription(array $ʟ_args): void
	{
		echo 'Výpis všech článků.';
	}


	/** {block content} on line 3 */
	public function blockContent(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);
		echo '<table>
';
		$iterations = 0;
		foreach ($articles as $article) /* line 5 */ {
			echo '    <tr>
        <td>
            <h2><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Article:", [$article->url])) /* line 7 */;
			echo '">';
			echo LR\Filters::escapeHtmlText($article->title) /* line 7 */;
			echo '</a></h2>
            ';
			echo LR\Filters::escapeHtmlText($article->description) /* line 8 */;
			echo '
            <br>
            <a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("editor", [$article->url])) /* line 10 */;
			echo '">Editovat</a>
            <a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("remove", [$article->url])) /* line 11 */;
			echo '">Odstranit</a>
        </td>
    </tr>
';
			$iterations++;
		}
		echo '</table>';
	}

}
