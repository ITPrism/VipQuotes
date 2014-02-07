<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * It is a quote model
 */
class VipQuotesModelAuthor extends JModelAdmin {
    
    /**
     * This is the folder where images are stored.
     * @var string
     */
    public $imagesFolder;
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Author', $prefix = 'VipQuotesTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
	
	
    /**
     * Method to get the form data.
     *
     * @param   array   $data       An optional array of data for the form to interogate.
     * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true){
        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Get the form.
        $form = $this->loadForm($this->option.'.author', 'author', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
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
    protected function loadFormData(){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.author.data', array());
        
        if(empty($data)){
            
            $data = $this->getItem();
            
            $data->image_width  = $app->getUserState($this->option.'.author.image_width', 200, 'uint');
            $data->image_height = $app->getUserState($this->option.'.author.image_height', 300, 'uint');
            $data->thumb_width  = $app->getUserState($this->option.'.author.thumb_width', 50, 'uint');
            $data->thumb_height = $app->getUserState($this->option.'.author.thumb_height', 50, 'uint');
            
            
        }
        
        return $data;
    }
    
    /**
     * Save data into the DB
     * 
     * @param $data   The data
     * @return     	  Item ID
     */
    public function save($data){
        
        $id        = JArrayHelper::getValue($data, "id");
        $name      = JArrayHelper::getValue($data, "name");
        $bio       = JString::trim( JArrayHelper::getValue($data, "bio") );
        $copyright = JString::trim( JArrayHelper::getValue($data, "copyright") );
        $alias     = JArrayHelper::getValue($data, "alias");
        $published = JArrayHelper::getValue($data, "published");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("name",      $name);
        $row->set("bio",       $bio);
        $row->set("copyright", $copyright);
        $row->set("alias",     $alias);
        $row->set("published", $published);
        
        $this->prepareTable($row, $data);
        
        $row->store();
        
        return $row->id;
    
    }
    
	/**
	 * Prepare and sanitise the table prior to saving.
	 * @since	1.6
	 */
	protected function prepareTable($table, $data) {
	    
	    // Set the new image and delete old one
        if(!empty($data["image"])){
            
            // Delete old image if I upload the new one
            if(!empty($table->image)){
                
                // Remove an image from the filesystem
                $fileImage = $this->imagesFolder .DIRECTORY_SEPARATOR. $table->image;
                $fileThumb = $this->imagesFolder .DIRECTORY_SEPARATOR. $table->thumb;
                
                if(is_file($fileImage)) {
                    JFile::delete($fileImage);
                }
                
                if(is_file($fileThumb)) {
                    JFile::delete($fileThumb);
                }
            
            }
            
            $table->set("image", $data["image"]);
            $table->set("thumb", $data["thumb"]);
        }
        
	    // Set ordering to the last item if not set
		if (empty($table->ordering)) {
			$db     = JFactory::getDbo();
			$query  = $db->getQuery(true);
			$query
			    ->select("MAX(a.ordering)")
			    ->from($db->quoteName("#__vq_authors", "a"));
			
		    $db->setQuery($query, 0, 1);
			$max = $db->loadResult();

			$table->ordering = $max+1;
		}
		
	    // If does not exist alias, I will generate the new one from the title
        if(!$table->alias) {
            $table->alias = $table->name;
        }
        $table->alias = JApplication::stringURLSafe($table->alias);
        
	}
	
    
    /**
     * Delete records
     * @param array $cids Rows Ids
     */
    public function delete($itemsIds){
        
        $db     = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);
        
        $query
            ->delete($db->quoteName('#__vq_authors'))
            ->where($db->quoteName('id') ." IN ( " . implode(",", $itemsIds) . " )");
        
        $db->setQuery($query);
        $db->execute();
    
    }
    
    public function uploadImage($image, $options) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $names           = array("image", "thumb");
        
        $uploadedFile    = JArrayHelper::getValue($image, 'tmp_name');
        $uploadedName    = JArrayHelper::getValue($image, 'name');
        
        // Joomla! media extension parameters
        $mediaParams     = JComponentHelper::getParams("com_media");
        
        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.image");
        
        $file           = new ITPrismFile();
        
        // Prepare size validator.
        $KB             = 1024 * 1024;
        $fileSize       = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize  = $mediaParams->get("upload_maxsize") * $KB;
        
        $sizeValidator  = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);
        
        
        // Prepare image validator.
        $imageValidator = new ITPrismFileValidatorImage($uploadedFile, $uploadedName);
        
        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $imageValidator->setMimeTypes($mimeTypes);
        
        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $imageValidator->setImageExtensions($imageExtensions);
        
        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator);
        
        // Validate the file
        $file->validate();
        
        // Generate temporary file name
        $ext   = JString::strtolower(JFile::makeSafe(JFile::getExt($image['name'])));
        
        jimport("itprism.string");
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString(6);
        
        $imageName     = "image_".$generatedName.".".$ext;
        $destination   = $this->imagesFolder .DIRECTORY_SEPARATOR. $imageName;
        
        // Prepare uploader object.
        $uploader      = new ITPrismFileUploaderLocal($image);
        $uploader->setDestination($destination);
        
        // Upload temporary file
        $file->setUploader($uploader);
        
        $file->upload();
        
        $source = $file->getFile();
        
        if(!empty($options["resize_image"])) {
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
    protected function resizeImage($source, $options) {
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Get values from the user state
        $width      = JArrayHelper::getValue($options, "image_width", 200, "uint");
        if($width < 50) {
            $width  = 200;
        }
        $app->setUserState($this->option.'.author.image_width', $width);
        
        $height     = JArrayHelper::getValue($options, "image_height", 300, "uint"); 
        if($height < 50) {
            $height  = 300;
        }
        $app->setUserState($this->option.'.author.image_height', $height);
        
        // Resize image and genearate thumbnail
        $image = new JImage();
        $image->loadFile($source);
        if (!$image->isLoaded()) {
            throw new RuntimeException(JText::sprintf('COM_VIPQUOTES_ERROR_FILE_NOT_FOUND', $source));
        }
        
        $fileName  = basename($source);
        $ext       = JFile::makeSafe(JFile::getExt($fileName));
        
        switch($ext){
            case "bmp":
                $imageType = IMAGETYPE_BMP;
                break;
            case "gif":
                $imageType = IMAGETYPE_GIF;
                break;
            case "png":
                $imageType = IMAGETYPE_PNG;
                break;
            case "jpg":
                $imageType = IMAGETYPE_JPEG;
                break;
                
        }
        
        $destination = $this->imagesFolder .DIRECTORY_SEPARATOR. $fileName;
        
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
    protected function createThumb($source, $generatedName, $options) {
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Get values from the user state
        $thumbWidth      = JArrayHelper::getValue($options, "thumb_width", 50, "uint");
        if($thumbWidth < 50) {
            $thumbWidth  = 50;
        }
        $app->setUserState($this->option.'.author.thumb_width', $thumbWidth);
        
        $thumbHeight     = JArrayHelper::getValue($options, "thumb_height", 50, "uint"); 
        if($thumbHeight < 50) {
            $thumbHeight  = 50;
        }
        $app->setUserState($this->option.'.author.thumb_height', $thumbHeight);
        
        // Resize image and genearate thumbnail
        $image = new JImage();
        $image->loadFile($source);
        if (!$image->isLoaded()) {
            throw new RuntimeException(JText::sprintf('COM_VIPQUOTES_ERROR_FILE_NOT_FOUND', $source));
        }
        
        $thumbName = "thumb_" .$generatedName. ".png";
        $thumbFile = $this->imagesFolder .DIRECTORY_SEPARATOR. $thumbName;
        
        // Resize the file
        $image->resize($thumbWidth, $thumbHeight, false);
        $image->toFile($thumbFile, IMAGETYPE_PNG);
        
        return $thumbName;
    }
    
	/**
     * Delete image only
     * @param integer Item id
     */
    public function removeImage($id){
        
        // Load category data
        $row = $this->getTable();
        $row->load($id);
        
        // Delete old image.
        if(!empty($row->image)){
            
            jimport('joomla.filesystem.file');
            
            // Remove an image from the filesystem
            $fileImage = $this->imagesFolder .DIRECTORY_SEPARATOR. $row->image;
            $fileThumb = $this->imagesFolder .DIRECTORY_SEPARATOR. $row->thumb;
            
            if(JFile::exists($fileImage)) {
                JFile::delete($fileImage);
            }
            
            if(JFile::exists($fileThumb)) {
                JFile::delete($fileThumb);
            }
        }
        
        $row->set("image", "");
        $row->set("thumb", "");
        $row->store();
    
    }
    
}