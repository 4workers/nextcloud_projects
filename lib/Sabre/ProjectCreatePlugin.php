<?php

declare(strict_types=1);

namespace OCA\Projects\Sabre;

use OCA\Projects\ProjectsStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use Sabre\DAV\Exception\BadRequest;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;
use Sabre\HTTP\Request;
use Sabre\HTTP\Response;

class ProjectCreatePlugin extends ServerPlugin
{

    /**
     * @var Server
     */
    private $server;

    /**
     * @var ProjectsStorage
     */
    private $projectsStorage;

    public function __construct(
        ProjectsStorage $projectsStorage
    ) {
        $this->projectsStorage = $projectsStorage;
    }

    public function initialize(Server $server)
    {
        $this->server = $server;

        $this->server->on('method:POST', [$this, 'createProject']);
    }

    public function getHTTPMethods($path): array
    {
        if (strpos($path, 'projects/') === false) { return [];
        }
        return ['POST'];
    }

    public function createProject(Request $request, Response $response)
    {
        if (strpos($request->getPath(), 'projects/') === false) return;
        $stream = $request->getBody();
        $data = [];
        if (is_resource($stream)) {
            $data = json_decode(stream_get_contents($stream), true);
        }
        $uid = array_pop(explode('/', trim($request->getPath(), '/')));
        //TODO create in transaction
        $user = \OC::$server->getUserManager()->get($uid);
        $currentUser = \OC::$server->getUserSession()->getUser();
        //TODO: remove after close all todos in the method
        if ($currentUser->getUID() !== $user->getUID()) {
            throw new Forbidden();
        }
        if (!$user) {
            throw new NotFound();
        }
        $currentUserIsAdmin = \OC::$server->getGroupManager()->isAdmin($currentUser->getUID());
        if (!$currentUserIsAdmin) {
            throw new Forbidden();
        }
        if (!array_key_exists('name', $data)) {
            throw new BadRequest('Provide project name');
        }
        if (!array_key_exists('foreign-id', $data)) {
            throw new BadRequest('Provide foreign id of the project');
        }
        try {
            //TODO: what if this project not belong current user or shared(owner is someone else)
            $projectNode = $this->projectsStorage->findByForeignId($data['foreign-id']);
        } catch (DoesNotExistException $e) {
            $projectNode = $this->projectsStorage->createProject($user->getUID(), $data['name'], $data['foreign-id']);
        }
        $response->setStatus(201);
        $urlGenerator = \OC::$server->getURLGenerator();
        $response->setHeader('content-location', $urlGenerator->getAbsoluteURL($projectNode->getPath()));
        $response->setBody(
            json_encode(
                [
                'id' => $projectNode->getId(),
                'name' => $projectNode->getName(),
                'foreign-id' => $data['foreign-id']
                ]
            )
        );
        return false;
    }

}
