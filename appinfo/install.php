<?php
declare(strict_types=1);

use OCA\Projects\Exception\InvalidConfigException;
use OCA\Projects\ProjectsRootPath;

$server = \OC::$server;
$projectsRootPath = $server->query(ProjectsRootPath::class);

if (!((string) $projectsRootPath)) {
    throw new InvalidConfigException('Default projects root path not defined. Add it to appinfo.xml.');
}

$baseUrl = getenv('SQUEEGEE_CONNECTOR_URL');
if (!$baseUrl) {
    throw new InvalidConfigException('Connector base url can\'t be empty. SQUEEGEE_CONNECTOR_URL env variable is required.');
}