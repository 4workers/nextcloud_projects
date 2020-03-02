#!/bin/bash

# Helper script to install and configure the Nextcloud server as expected by the
# acceptance tests.
#
# This script is not meant to be called manually; it is called when needed by
# the acceptance tests launchers.
#
# It simply extends the default script by enabling the Projects app so it is already
# available when the acceptance tests are run.

set -o errexit

tests/acceptance/installAndConfigureServer.sh "$@"

php occ app:enable projects
php occ app:enable nextcloud_connector_sync
