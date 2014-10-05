<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

/**
 * Abstract class defining methods that can be
 * implemented by an Observer class of a JTable class (which is an Observable).
 * Attaches $this Observer to the $table in the constructor.
 * The classes extending this class should not be instanciated directly, as they
 * are automatically instanciated by the JObserverMapper
 *
 * @package      VipQuotes
 * @subpackage   Component
 * @link         http://docs.joomla.org/JTableObserver
 * @since        3.1.2
 */
class VipQuotesObserverAuthor extends JTableObserver
{
    /**
     * The pattern for this table's TypeAlias
     *
     * @var    string
     * @since  3.1.2
     */
    protected $typeAliasPattern = null;

    /**
     * Creates the associated observer instance and attaches it to the $observableObject
     * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
     *
     * @param   JObservableInterface $observableObject The subject object to be observed
     * @param   array                $params           ( 'typeAlias' => $typeAlias )
     *
     * @return  VipQuotesObserverAuthor
     *
     * @since   3.1.2
     */
    public static function createObserver(JObservableInterface $observableObject, $params = array())
    {
        $observer = new self($observableObject);

        $observer->typeAliasPattern = JArrayHelper::getValue($params, 'typeAlias');

        return $observer;
    }

    /**
     * Pre-processor for $table->delete($pk)
     *
     * @param   mixed $pk An optional primary key value to delete.  If not set the instance property value is used.
     *
     * @return  void
     *
     * @since   3.1.2
     * @throws  UnexpectedValueException
     */
    public function onBeforeDelete($pk)
    {
        $db = $this->table->getDbo();

        // Remove the image and the thumbnail.
        if (!empty($this->table->image) or !empty($this->table->thumb)) {

            // Load parameters.
            $params              = JComponentHelper::getParams("com_vipquotes");
            /** @var  $params Joomla\Registry\Registry */

            $imagesFolder = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get("images_directory", "images/authors"));

            jimport('joomla.filesystem.file');

            // Remove an image from the filesystem
            $fileImage = $imagesFolder . DIRECTORY_SEPARATOR . $this->table->image;
            $fileThumb = $imagesFolder . DIRECTORY_SEPARATOR . $this->table->thumb;

            if (JFile::exists($fileImage)) {
                JFile::delete($fileImage);
            }

            if (JFile::exists($fileThumb)) {
                JFile::delete($fileThumb);
            }
        }

        // Change author ID to 0 for the quotes assigned to its ID.

        $query = $db->getQuery(true);

        $query
            ->update($db->quoteName("#__vq_quotes"))
            ->set($db->quoteName("author_id") ." = 0")
            ->where($db->quoteName("author_id") . "=" . (int)$this->table->id);

        $db->setQuery($query);
        $db->execute();
    }
}
