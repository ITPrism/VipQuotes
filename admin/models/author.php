<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is a quote model
 */
class VipQuotesModelAuthor extends JModelAdmin
{
    /**
     * This is the folder where images are stored.
     * @var string
     */
    public $imagesFolder;

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
     * Method to get the form data.
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
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
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.author.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            $data->image_width  = $app->getUserState($this->option . '.author.image_width', 200, 'uint');
            $data->image_height = $app->getUserState($this->option . '.author.image_height', 300, 'uint');
            $data->thumb_width  = $app->getUserState($this->option . '.author.thumb_width', 50, 'uint');
            $data->thumb_height = $app->getUserState($this->option . '.author.thumb_height', 50, 'uint');
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data   The data
     *
     * @return   int       Item ID
     */
    public function save($data)
    {
        $id        = Joomla\Utilities\ArrayHelper::getValue($data, "id");
        $name      = Joomla\Utilities\ArrayHelper::getValue($data, "name");
        $bio       = Joomla\Utilities\ArrayHelper::getValue($data, "bio");
        $copyright = Joomla\Utilities\ArrayHelper::getValue($data, "copyright");
        $alias     = Joomla\Utilities\ArrayHelper::getValue($data, "alias");
        $published = Joomla\Utilities\ArrayHelper::getValue($data, "published");

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set("name", $name);
        $row->set("bio", $bio);
        $row->set("copyright", $copyright);
        $row->set("alias", $alias);
        $row->set("published", $published);

        $this->prepareImage($row, $data);
        $this->prepareTable($row, $data);

        $row->store();

        return $row->get("id");
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

            // Delete old image if I upload the new one
            if (!empty($table->image)) {

                // Remove an image from the filesystem
                $fileImage = $this->imagesFolder . DIRECTORY_SEPARATOR . $table->get("image");
                $fileThumb = $this->imagesFolder . DIRECTORY_SEPARATOR . $table->get("thumb");

                if (is_file($fileImage)) {
                    JFile::delete($fileImage);
                }

                if (is_file($fileThumb)) {
                    JFile::delete($fileThumb);
                }

            }

            $table->set("image", $data["image"]);
            $table->set("thumb", $data["thumb"]);
        }

    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param JTable $table
     */
    protected function prepareTable($table)
    {
        // Verify for enabled magic quotes
        if (get_magic_quotes_gpc()) {
            $table->set("name", stripcslashes($table->get("name")));
            $table->set("bio", stripcslashes($table->get("bio")));
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
        if (!$table->get("alias")) {
            $table->set("alias", $table->get("name"));
        }
        $table->set("alias", JApplicationHelper::stringURLSafe($table->get("alias")));
    }

    public function uploadImage($image, $options)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

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

        if (!empty($options["resize_image"])) {
            $source = $this->resizeImage($source, $options);
        }

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

    /**
     * Delete image only
     *
     * @param int $id Item ID
     */
    public function removeImage($id)
    {
        // Load category data
        $row = $this->getTable();
        $row->load($id);

        // Delete old image.
        if (!empty($row->image)) {

            jimport('joomla.filesystem.file');

            // Remove an image from the filesystem
            $fileImage = $this->imagesFolder . DIRECTORY_SEPARATOR . $row->get("image");
            $fileThumb = $this->imagesFolder . DIRECTORY_SEPARATOR . $row->get("thumb");

            if (JFile::exists($fileImage)) {
                JFile::delete($fileImage);
            }

            if (JFile::exists($fileThumb)) {
                JFile::delete($fileThumb);
            }
        }

        $row->set("image", "");
        $row->set("thumb", "");
        $row->store();
    }
}
