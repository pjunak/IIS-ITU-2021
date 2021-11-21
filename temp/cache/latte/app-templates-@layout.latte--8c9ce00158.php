<?php

use Latte\Runtime as LR;

/** source: /opt/lampp/htdocs/sandbox/app/templates/@layout.latte */
final class Template8c9ce00158 extends Latte\Runtime\Template
{
	protected const BLOCKS = [
		['head' => 'blockHead', 'scripts' => 'blockScripts'],
	];


	public function main(): array
	{
		extract($this->params);
		echo '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>';
		if ($this->hasBlock("title")) /* line 13 */ {
			$this->renderBlock($ʟ_nm = 'title', [], function ($s, $type) {
				$ʟ_fi = new LR\FilterInfo($type);
				return LR\Filters::convertTo($ʟ_fi, 'html', $this->filters->filterContent('striphtml', $ʟ_fi, $s));
			}) /* line 13 */;
			echo ' | ';
		}
		echo 'Nette Sandbox</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 17 */;
		echo '/css/style.css">
    ';
		if ($this->getParentName()) {
			return get_defined_vars();
		}
		$this->renderBlock('head', get_defined_vars()) /* line 18 */;
		echo '
</head>

<body>
    <header>
        <h1>Jednoduchý redakční systém v Nette</h1>
    </header>
    <div class=container>
';
		$iterations = 0;
		foreach ($flashes as $flash) /* line 27 */ {
			echo '        <div';
			echo ($ʟ_tmp = array_filter(['alert', 'alert-' . $flash->type])) ? ' class="' . LR\Filters::escapeHtmlAttr(implode(" ", array_unique($ʟ_tmp))) . '"' : "" /* line 27 */;
			echo '>';
			echo LR\Filters::escapeHtmlText($flash->message) /* line 27 */;
			echo '</div>
';
			$iterations++;
		}
		echo '
        <nav>
            <ul>
                <li><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(":Core:Article:")) /* line 31 */;
		echo '">Úvod</a></li>
                <li><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(":Core:Article:list")) /* line 32 */;
		echo '">Seznam článků</a></li>
                <li><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(":Core:Contact:")) /* line 33 */;
		echo '">Kontakt</a></li>
            </ul>
        </nav>

        <br style="clear: both;">
        <article>
            <header>
                <h1>';
		$this->renderBlock($ʟ_nm = 'title', [], 'html') /* line 40 */;
		echo '</h1>
            </header>
            <section>
                ';
		$this->renderBlock($ʟ_nm = 'content', [], 'html') /* line 43 */;
		echo ' 
            </section>
        </article>
    </div>

    <footer>
    <p>
        Ukázkový tutoriál pro jednoduchý redakční systém v Nette z programátorské sociální sítě
        <a href="http://www.itnetwork.cz" target="_blank">itnetwork.cz</a>.
        <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(":Core:Administration:")) /* line 52 */;
		echo '">Administrace</a>
    </p>
</footer>

';
		$this->renderBlock('scripts', get_defined_vars()) /* line 56 */;
		echo '
</body>
</html>';
		return get_defined_vars();
	}


	public function prepare(): void
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			foreach (array_intersect_key(['flash' => '27'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		$this->createTemplate('components/form.latte', $this->params, "import")->render() /* line 6 */;
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	/** {block head} on line 18 */
	public function blockHead(array $ʟ_args): void
	{
		
	}


	/** {block scripts} on line 56 */
	public function blockScripts(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);
		echo '        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://nette.github.io/resources/js/3/netteForms.min.js"></script>
        <script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 59 */;
		echo '/js/main.js"></script>
';
	}

}
