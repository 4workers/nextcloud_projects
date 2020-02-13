<?php

declare(strict_types=1);

namespace OCA\Projects\Sabre;

use Sabre\DAV\INode;
use Sabre\DAVACL\AbstractPrincipalCollection;
use Sabre\DAVACL\PrincipalBackend\BackendInterface;

class RootCollection extends AbstractPrincipalCollection {

	public function __construct(
		BackendInterface $principalBackend
	) {
		parent::__construct($principalBackend, 'principals/users');
	}

	/**
	 * This method returns a node for a principal.
	 *
	 * The passed array contains principal information, and is guaranteed to
	 * at least contain a uri item. Other properties may or may not be
	 * supplied by the authentication backend.
	 *
	 * @param array $principalInfo
	 * @return INode
	 */
	public function getChildForPrincipal(array $principalInfo): ProjectsHome {
		list(, $name) = \Sabre\Uri\split($principalInfo['uri']);
		$user = \OC::$server->getUserSession()->getUser();
		if (is_null($user) || $name !== $user->getUID()) {
			throw new \Sabre\DAV\Exception\Forbidden();
		}
		return new ProjectsHome($principalInfo, $user);
	}

	public function getName(): string {
		return 'projects';
	}

}
