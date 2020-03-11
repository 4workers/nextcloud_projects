# Projects
Place this app in **nextcloud/apps/**

Set env variables
```
SQUEEGEE_CONNECTOR_URL='localhost:8000'
SQUEEGEE_PROJECTS_ROOT='/projects'
```

## Building the app

The app can be built by using the provided Makefile by running:

    make

This requires the following things to be present:
* make
* which
* tar: for building the archive
* curl: used if phpunit and composer are not installed to fetch them from the web
* npm: for building and testing everything JS, only required if a package.json is placed inside the **js/** folder

The make command will install or update Composer dependencies if a composer.json is present and also **npm run build** if a package.json is present in the **js/** folder. The npm **build** script should use local paths for build systems and package managers, so people that simply want to build the app won't need to install npm libraries globally, e.g.:

**package.json**:
```json
"scripts": {
    "test": "node node_modules/gulp-cli/bin/gulp.js karma",
    "prebuild": "npm install && node_modules/bower/bin/bower install && node_modules/bower/bin/bower update",
    "build": "node node_modules/gulp-cli/bin/gulp.js"
}
```


## Publish to App Store

First get an account for the [App Store](http://apps.nextcloud.com/) then run:

    make && make appstore

The archive is located in build/artifacts/appstore and can then be uploaded to the App Store.

## Running tests
You can use the provided Makefile to run all tests by using:

    make test

This will run the PHP unit and integration tests and if a package.json is present in the **js/** folder will execute **npm run test**

Of course you can also install [PHPUnit](http://phpunit.de/getting-started.html) and use the configurations directly:

    phpunit -c phpunit.xml

or:

    phpunit -c phpunit.integration.xml

for integration tests

## Projects root API examples

#### Projects list
Curl example to get projects list for user `matchish`
```
curl --location --request PROPFIND 'http://localhost/remote.php/dav/projects/matchish' \
--data-raw '<?xml version="1.0"?>
<d:propfind  xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns" xmlns:nc="http://nextcloud.org/ns" xmlns:ocs="http://open-collaboration-services.org/ns">
  <d:prop>
    <d:getlastmodified />
    <d:getetag />
    <d:getcontenttype />
    <d:resourcetype />
    <oc:fileid />
    <oc:permissions />
    <oc:size />
    <d:getcontentlength />
    <nc:has-preview />
    <nc:mount-type />
    <nc:is-encrypted />
    <ocs:share-permissions />
    <oc:tags />
    <oc:favorite />
    <x:foreign-id xmlns:x="https://squeegee.com/ns" />
    <x:is-project xmlns:x="https://squeegee.com/ns" />
    <oc:comments-unread />
    <oc:owner-id />
    <oc:owner-display-name />
    <oc:share-types />
  </d:prop>
</d:propfind>'
```

#### Add project
Curl example to add new project `squeegee` with foreign id `uuid` for user `matchish`

```
curl --location --request POST 'http://localhost/remote.php/dav/projects/matchish/' \
--header 'Content-Type: application/json' \
--data-raw '{
	"name": "squeegee",
	"foreign-id": "uuid"
}'
```