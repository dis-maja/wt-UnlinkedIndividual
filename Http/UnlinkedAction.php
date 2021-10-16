<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace DISMaja\Webtrees\Module\UnlinkedIndividual\Http;

use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function preg_match_all;
use function redirect;
use function route;

/**
 * Create a new unlinked individual.
 */
class UnlinkedAction implements RequestHandlerInterface
{
    /** @var GedcomEditService */
    private $gedcom_edit_service;

    /**
     * AddChildToFamilyAction constructor.
     *
     * @param GedcomEditService $gedcom_edit_service
     */
    public function __construct(GedcomEditService $gedcom_edit_service)
    {
        $this->gedcom_edit_service = $gedcom_edit_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
	$tree = $request->getAttribute('tree');
	assert($tree instanceof Tree);

	$url = route(UnlinkedAction::class, ['tree' => $tree->name()]);
	$url = str_replace('%2Funlinked-individual','', $url);
	
	Log::addMediaLog(serialize($request));

	$params = (array) $request->getParsedBody();

	$gedrec = array();
	$gedrec[] = '0 @@ INDI';
	$gedrec[] = '1 NAME ' . $params['GIVN'] . ' /' . $params['SURN'] .'/';
	$gedrec[] = '2 GIVN ' . $params['GIVN'];
	$gedrec[] = '2 SURN ' . $params['SURN'];
	$gedrec[] = '1 SEX ' . $params['SEX'];

	$gedcom = implode(chr(10), $gedrec);

#	Log::addMediaLog($gedrec);
	 
	$foo = $tree->createRecord($gedcom);
#	Log::addMediaLog(serialize($foo));
	 
#	FunctionsImport::importRecord($gedrec, $tree, false);

#        if (($params['goto'] ?? '') === 'new') {
#            return redirect($new_indi->url());
#        }

        return redirect($url);
    }
}
