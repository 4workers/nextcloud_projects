<?php
declare(strict_types=1);


namespace OCA\Projects\Sabre;


use OC\Files\SimpleFS\SimpleFolder;
use OCP\Files\Folder;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\Exception\NotImplemented;
use Sabre\DAV\ICollection;
use Sabre\DAV\INode;

class ProjectSymlink implements ICollection
{

    /**
     * @var Folder
     */
    private $folder;

    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Creates a new file in the directory
     *
     * Data will either be supplied as a stream resource, or in certain cases
     * as a string. Keep in mind that you may have to support either.
     *
     * After successful creation of the file, you may choose to return the ETag
     * of the new file here.
     *
     * The returned ETag must be surrounded by double-quotes (The quotes should
     * be part of the actual string).
     *
     * If you cannot accurately determine the ETag, you should not return it.
     * If you don't store the file exactly as-is (you're transforming it
     * somehow) you should also not return an ETag.
     *
     * This means that if a subsequent GET to this new file does not exactly
     * return the same contents of what was submitted here, you are strongly
     * recommended to omit the ETag.
     *
     * @param  string          $name Name of the file
     * @param  resource|string $data Initial payload
     * @return null|string
     */
    function createFile($name, $data = null)
    {
        throw new Forbidden('You can\'t create file in project root');
    }

    /**
     * Creates a new subdirectory
     *
     * @param  string $name
     * @return void
     */
    function createDirectory($name)
    {
        $this->folder->newFolder($name);
    }

    /**
     * Returns a specific child node, referenced by its name
     *
     * This method must throw Sabre\DAV\Exception\NotFound if the node does not
     * exist.
     *
     * @param  string $name
     * @return INode
     */
    function getChild($name)
    {
        $this->folder->get($this->folder->getPath() . '/' . $name);
    }

    /**
     * Returns an array with all the child nodes
     *
     * @return INode[]
     */
    function getChildren()
    {
        $this->folder->getDirectoryListing();
    }

    /**
     * Checks if a child-node with the specified name exists
     *
     * @param  string $name
     * @return bool
     */
    function childExists($name)
    {
        $this->folder->nodeExists($name);
    }

    /**
     * Deleted the current node
     *
     * @return void
     */
    function delete()
    {
        $this->folder->delete();
    }

    /**
     * Returns the name of the node.
     *
     * This is used to generate the url.
     *
     * @return string
     */
    function getName()
    {
        return $this->folder->getName();
    }

    /**
     * Renames the node
     *
     * @param  string $name The new name
     * @return void
     */
    function setName($name): void
    {
        // TODO: Implement setName() method.
        throw new NotImplemented('TODO: Implement setName() method.');
    }

    /**
     * Returns the last modification time, as a unix timestamp. Return null
     * if the information is not available.
     *
     * @return int|null
     */
    function getLastModified()
    {
        $this->folder->getUploadTime();
    }

}