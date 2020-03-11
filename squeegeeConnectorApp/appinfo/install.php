<?php
declare(strict_types=1);

use OCA\Projects\Exception\InvalidConfigException;

$projectsRootPath = getenv('SQUEEGEE_PROJECTS_ROOT');
if (!$projectsRootPath) {
    throw new InvalidConfigException('Projects root not defined. SQUEEGEE_PROJECTS_ROOT env variable is required.');
}
$baseUrl = getenv('SQUEEGEE_CONNECTOR_URL');
if (!$baseUrl) {
    throw new InvalidConfigException('Connector base url can\'t be empty. SQUEEGEE_CONNECTOR_URL env variable is required.');
}