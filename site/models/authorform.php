<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class VipQuotesModelAuthorForm extends JModelAdmin
{
    protected $imagesFolder;

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Author', $prefix = 'VipQuotesTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication("Site");
        /** @var $app JApplicationSite */

        // Set state of the record id.
        $this->setState('authorform.id', $app->input->get("id"));

        // Load the parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML and then an event is fired
     * for users plugins to extend the form with extra fields.
     *
     * @param    array   $data     An optional array of data for the form to interrogate.
     * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm    A JForm object on success, false on failure
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.author', 'author', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.6
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $data = $app->getUserState($this->option . '.edit.author.data', array());
        /*if (!$data) {
            $data = $this->getItem();
        }*/

        return $data;
    }

    public function hasDuplication($name, $itemId = null)
    {
        $db = JFactory::getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("COUNT(*)")
            ->from($db->quoteName("#__vq_authors", "a"));

        if (!empty($itemId)) {
            $query->where("a.id != " . (int)$itemId);
        }

        $query->where("a.name SOUNDS LIKE " . $db->quote($name));

        $db->setQuery($query);
        $result = $db->loadResult();

        return (bool)$result;
    }

    /**
     * Method to save the form data.
     *
     * @param    array    $data    The form data.
     *
     * @return    mixed        The record id on success, null on failure.
     *
     * @throws Exception
     *
     * @since    1.6
     */
    public function save($data)
    {
        $name     = Joomla\Utilities\ArrayHelper::getValue($data, "name");
        $bio      = Joomla\Utilities\ArrayHelper::getValue($data, "bio");

        $alias    = JApplicationHelper::stringURLSafe($name);

        $keys = array(
            "alias" => $alias
        );

        // Load a record from the database
        $table = $this->getTable();
        $table->load($keys);

        // Check if alias exists.
        if ($table->get("id")) {
            throw new RuntimeException(JText::_("COM_VIPQUOTES_ERROR_AUTHOR_ALREADY_EXISTS"));
        }

        // If there is an ID, the item is NOT new.
        $isNew = true;

        $user = JFactory::getUser();
        $table->set("user_id", $user->get("id"));

        $params = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        if ($params instanceof Joomla\Registry\Registry) {
            if ($params->get("security_author_auto_publishing", 0)) {
                $table->set("published", 1);
            }
        }

        $table->set("name", $name);
        $table->set("alias", $alias);
        $table->set("bio", $bio);

        $this->prepareImage($table, $data);
        $this->prepareTable($table);

        $table->store();

        // Trigger the event
        $this->triggerEventOnAfterSave($table, $isNew);

        return $table->get("id");
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param JTable $table
     */
    protected function prepareTable($table)
    {
        // Verify for enabled magic quotes.
        if (get_magic_quotes_gpc()) {
            $table->set("bio", stripslashes($table->get("bio")));
        }

        // Set ordering to the last item if not set
        if (!$table->get("ordering")) {

            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select("MAX(a.ordering)")
                ->from($db->quoteName("#__vq_authors", "a"));

            $db->setQuery($query, 0, 1);
            $max = $db->loadResult();

            $table->set("ordering", $max + 1);
        }

        // If does not exist alias, I will generate the new one from the title
        $table->set("alias", JApplicationHelper::stringURLSafe($table->get("name")));
    }

    /**
     * Prepare the author image.
     *
     * @param JTable $table
     * @param array $data
     */
    protected function prepareImage($table, $data)
    {
        // Set the new image and delete old one
        if (!empty($data["image"])) {
            $table->set("image", $data["image"]);
            $table->set("thumb", $data["thumb"]);
        }
    }

    /**
     * @param JTable $row
     * @param bool $isNew
     *
     * @throws RuntimeException
     */
    protected function triggerEventOnAfterSave($row, $isNew)
    {
        // Get properties
        $item = $row->getProperties();
        $item = Joomla\Utilities\ArrayHelper::toObject($item);

        // Generate context
        $context = $this->option . '.author';

        // Include the content plugins for the change of state event.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');

        // Trigger the onContentAfterSave event.
        $results = $dispatcher->trigger($this->event_after_save, array($context, &$item, $isNew));
        if (in_array(false, $results, true)) {
            throw new RuntimeException(JText::_("COM_VIPQUOTES_ERROR_DURING_PROCESS_POSTING_AUTHOR"));
        }
    }

    public function uploadImage($image, $options)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $this->imagesFolder = Joomla\Utilities\ArrayHelper::getValue($options, 'image_folder');

        $uploadedFile = Joomla\Utilities\ArrayHelper::getValue($image, 'tmp_name');
        $uploadedName = Joomla\Utilities\ArrayHelper::getValue($image, 'name');
        $errorCode    = Joomla\Utilities\ArrayHelper::getValue($image, 'error');

        // Joomla! media extension parameters
        $mediaParams = JComponentHelper::getParams("com_media");
        /** @var  $mediaParams Joomla\Registry\Registry */

        $file = new Prism\File\File();

        // Prepare size validator.
        $KB            = 1024 * 1024;
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $mediaParams->get("upload_maxsize") * $KB;

        // Prepare file size validator
        $sizeValidator = new Prism\File\Validator\Size($fileSize, $uploadMaxSize);

        // Prepare server validator.
        $serverValidator = new Prism\File\Validator\Server($errorCode, array(UPLOAD_ERR_NO_FILE));

        // Prepare image validator.
        $imageValidator = new Prism\File\Validator\Image($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $imageValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $imageValidator->setImageExtensions($imageExtensions);

        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Generate temporary file name
        $ext = JString::strtolower(JFile::makeSafe(JFile::getExt($image['name'])));

        $generatedName = Prism\String\StringHelper::generateRandomString(6);

        $imageName   = "image_" . $generatedName . "." . $ext;
        $destination = $this->imagesFolder . DIRECTORY_SEPARATOR . $imageName;

        // Prepare uploader object.
        $uploader = new Prism\File\Uploader\Local($uploadedFile);
        $uploader->setDestination($destination);

        // Upload temporary file
        $file->setUploader($uploader);

        $file->upload();

        $source = $file->getFile();

        // Resize the image
        $source = $this->resizeImage($source, $options);

        // Create a thumbnail
        $thumbName = $this->createThumb($source, $generatedName, $options);

        return $names = array(
            "image" => $imageName,
            "thumb" => $thumbName
        );
    }

    /**
     * Resize image.
     *
     * @param string $source
     * @param array  $options
     *
     * @throws RuntimeException
     * @return string
     */
    protected function resizeImage($source, $options)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        // Get values from the user state
        $width = Joomla\Utilities\ArrayHelper::getValue($options, "image_width", 200, "uint");
        if ($width < 50) {
            $width = 200;
        }
        $app->setUserState($this->option . '.author.image_width', $width);

        $height = Joomla\Utilities\ArrayHelper::getValue($options, "image_height", 300, "uint");
        if ($height < 50) {
            $height = 300;
        }
        $app->setUserState($this->option . '.author.image_height', $height);

        // Resize image and generates a thumbnail.
        $image = new JImage();
        $image->loadFile($source);
        if (!$image->isLoaded()) {
            throw new RuntimeException(JText::sprintf('COM_VIPQUOTES_ERROR_FILE_NOT_FOUND', $source));
        }

        $fileName = basename($source);
        $ext      = JFile::makeSafe(JFile::getExt($fileName));

        switch ($ext) {

            case "bmp":
                $imageType = IMAGETYPE_BMP;
                break;

            case "gif":
                $imageType = IMAGETYPE_GIF;
                break;

            case "jpg":
                $imageType = IMAGETYPE_JPEG;
                break;

            default: // PNG
                $imageType = IMAGETYPE_PNG;
                break;
        }

        $destination = $this->imagesFolder . DIRECTORY_SEPARATOR . $fileName;

        // Resize the file
        $image->resize($width, $height, false);
        $image->toFile($destination, $imageType);

        return $destination;
    }

    /**
     * Generate a thumbnail.
     *
     * @param string $source
     * @param string $generatedName
     * @param array  $options
     *
     * @throws RuntimeException
     * @return string
     */
    protected function createThumb($source, $generatedName, $options)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        // Get values from the user state
        $thumbWidth = Joomla\Utilities\ArrayHelper::getValue($options, "thumb_width", 50, "uint");
        if ($thumbWidth < 50) {
            $thumbWidth = 50;
        }
        $app->setUserState($this->option . '.author.thumb_width', $thumbWidth);

        $thumbHeight = Joomla\Utilities\ArrayHelper::getValue($options, "thumb_height", 50, "uint");
        if ($thumbHeight < 50) {
            $thumbHeight = 50;
        }
        $app->setUserState($this->option . '.author.thumb_height', $thumbHeight);

        // Resize image and generates a thumbnail.
        $image = new JImage();
        $image->loadFile($source);
        if (!$image->isLoaded()) {
            throw new RuntimeException(JText::sprintf('COM_VIPQUOTES_ERROR_FILE_NOT_FOUND', $source));
        }

        $thumbName = "thumb_" . $generatedName . ".png";
        $thumbFile = $this->imagesFolder . DIRECTORY_SEPARATOR . $thumbName;

        // Resize the file
        $image->resize($thumbWidth, $thumbHeight, false);
        $image->toFile($thumbFile, IMAGETYPE_PNG);

        return $thumbName;
    }
}
