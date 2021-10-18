<?php

/**
 *
 * Copyright (c) 2021, Mats O Jansson <maja@dis-maja.se>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

declare(strict_types=1);

namespace DISMaja\Webtrees\Module\UnlinkedIndividual;

use Aura\Router\Map;
use Aura\Router\RouterContainer;
use DISMaja\Webtrees\Module\UnlinkedIndividual\Http\UnlinkedAction;
use DISMaja\Webtrees\Module\UnlinkedIndividual\Http\UnlinkedPage;
use Fisharebest\Localization\Translation;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleBlockTrait;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
use Illuminate\Support\Str;

class UnlinkedIndividualModule extends AbstractModule implements ModuleCustomInterface, ModuleBlockInterface {

    use ModuleCustomTrait;
    use ModuleBlockTrait;

    /**
     * Constructor.  The constructor is called on *all* modules, even ones that are disabled.
     * This is a good place to load business logic ("services").  Type-hint the parameters and
     * they will be injected automatically.
     */
    public function __construct()
    {
	// NOTE:  If your module is dependent on any of the business logic ("services"),
	// then you would type-hint them in the constructor and let webtrees inject them
	// for you.  However, we can't use dependency injection on anonymous classes like
	// this one. For an example of this, see the example-server-configuration module.
    }

    /**
     * Bootstrap.  This function is called on *enabled* modules.
     * It is a good place to register routes and views.
     *
     * @return void
     */
    public function boot(): void
    {
	$router_container = app(RouterContainer::class);
	assert($router_container instanceof RouterContainer);
	$router = $router_container->getMap();

	$router->get(UnlinkedPage::class, '/tree/{tree}/unlinked-individual');
	$router->post(UnlinkedAction::class, '/tree/{tree}/unlinked-individual');

	// Register a namespace for our views.
	View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
    }

    /**
     * Where does this module store its resources
     *
     * @return string
     */
    public function resourcesFolder(): string
    {
	return __DIR__ . '/resources/';
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
	return I18N::translate('Unlinked individual');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
	return I18N::translate('Create an unlinked individual');
    }

    /**
     * The person or organisation who created this module.
     *
     * @return string
     */
    public function customModuleAuthorName(): string
    {
	return 'Mats O Jansson';
    }

    /**
     * The version of this module.
     *
     * @return string
     */
    public function customModuleVersion(): string
    {
	return '2.0.0';
    }

    /**
     * A URL that will provide the latest version of this module.
     *
     * @return string
     */
    public function customModuleLatestVersionUrl(): string
    {
	return 'https://raw.githubusercontent.com/dis-maja/wt-UnlinkedIndividual/main/latest-version.txt';
    }

    /**
     * Where to get support for this module.  Perhaps a github repository?
     *
     * @return string
     */
    public function customModuleSupportUrl(): string
    {
	return 'https://github.com/dis-maja/wt-UnlinkedIndividual/issues';
    }

    /*
     * Additional/updated translations.
     *
     * @param string $language
     *
     * @return array<string>
     */
    public function customTranslations(string $language): array
    {
	switch ($language) {
	    case 'sv':
		return $this->swedishTranslations();

	    default:
		return [];
	}
    }

    /**
     * @return array<string,string>
     */
    protected function swedishTranslations(): array
    {
	return [
	    'Create an unlinked individual' => 'Skapa en olänkad person',
	    'Created individual %s' => 'Skapade personen %s',
	    'Unlinked individual' => 'Olänkad person',
	];
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree	$tree
     * @param int	$block_id
     * @param string	$context
     * @param string[]	$config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {

        // You need to be at least Editor to add unlinked individual
	if (!(Auth::isAdmin() || Auth::isManager($tree) || Auth::isModerator($tree) || Auth::isEditor($tree))) {
	    return '';
	}

	$url = route(UnlinkedAction::class, ['tree' => $tree->name()]);

	$content = I18N::translate("Unlinked individual");
	$content =view($this->name() . '::unlinked-individual', [
			'tree' => $tree,
			'title' => I18N::translate('Create an unlinked individual'),
			'individual' => null,
			'family' => null,
			'name_fact' => null,
			'famtag' => '',
			'gender' => 'U',
		   ]);
	$content  = '<a class="btn btn-primary" href="' . $url . '">';
	$content .= I18N::translate('Create an unlinked individual');
	$content .= '</a>';

	if ($context !== self::CONTEXT_EMBED) {
	    return view('modules/block-template', [
		'block'		=> Str::kebab($this->name()),
		'id'		=> $block_id,
		'config_url'	=> '',
		'title'		=> $this->title(),
		'content'	=> $content,
	    ]);
	}

	return $content;
    }

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more complex ones can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool
    {
	return true;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isTreeBlock(): bool
    {
	return true;
    }

};

