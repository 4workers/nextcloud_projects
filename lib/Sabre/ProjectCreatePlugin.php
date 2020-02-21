<?php

declare(strict_types=1);

namespace OCA\Projects\Sabre;

use OCA\Projects\ProjectsStorage;
use Sabre\DAV\Exception\BadRequest;
use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;
use Sabre\HTTP\Request;
use Sabre\HTTP\Response;

class ProjectCreatePlugin extends ServerPlugin {

	const PROJECT_FOREIGN_ID = '{https://wuerth-it.com/ns}foreign-id';

	/** @var Server */
	private $server;

	/** @var ProjectsStorage */
	private $projectsStorage;

	public function __construct(
		ProjectsStorage $projectsStorage
	) {
		$this->projectsStorage = $projectsStorage;
	}

	public function initialize(Server $server) {
		$this->server = $server;

		$this->server->on('method:POST', [$this, 'createProject']);
	}

	public function getHTTPMethods($path): array
    {
        if (strpos($path, '/dav/projects/') === false) return [];
        return ['POST'];
    }

    public function createProject(Request $request, Response $response) {
        $stream = $request->getBody();
        $data = [];
        if (is_resource($stream)) {
            $data = json_decode(stream_get_contents($stream), true);
        }
        //TODO create in transaction
        $user = \OC::$server->getUserSession()->getUser();
        $uid = $user->getUID();
        if (!array_key_exists('name', $data)) {
            throw new BadRequest('Provide project name');
        }
        if (!array_key_exists('foreign-id', $data)) {
            throw new BadRequest('Provide foreign id of the project');
        }
        $projectNode = $this->projectsStorage->createProject('matchish', $data['name'], $data['foreign-id']);
        $response->setStatus(201);
        $response->setHeader('Location', $projectNode->getPath());
        $response->setBody(json_encode([
            'id' => $projectNode->getId()
        ]));
	}

}
