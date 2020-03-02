#!/bin/bash

# Helper script to run the acceptance tests, which test a running Nextcloud
# Talk instance from the point of view of a real user.
#
# It simply calls the main "run.sh" script from the Nextcloud server setting a
# specific acceptance tests directory (the one for the Talk app), and as such it
# is expected that the grandparent directory of the root directory of the Talk
# app is the root directory of the Nextcloud server.

set -o errexit

# Ensure working directory is script directory, as it is expected when the
# script from the server is called.
cd "$(dirname $0)"

../../../../tests/acceptance/run.sh --acceptance-tests-dir apps/projects/tests/acceptance/ "$@"
