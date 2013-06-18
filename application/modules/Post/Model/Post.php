<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Post
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Post_Model_Post extends Core_Model_Item_Abstract
{
  const MEDIA_TOPIC = 'topic';
  const MEDIA_PHOTO = 'photo';
  const MEDIA_VIDEO = 'video';
  const MEDIA_LINK  = 'link';  
  
  // Posts
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_PENDING = 'pending';
  
  
  protected $_parent_type = 'user';
  
  protected $_owner_type = 'user';

  protected $_searchTriggers = array('search', 'title', 'description');

  protected $_modifiedTriggers = array('search', 'title', 'description');
  
  protected $category;
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = $this->getSlug();
    
    if (isset($params['action']))
    {
      $params = array_merge(array(
        'route' => 'post_specific',
        'reset' => true,
        'post_id' => $this->post_id,
      ), $params);
    }
    else
    {
      $params = array_merge(array(
        'route' => 'post_profile',
        'reset' => true,
        'post_id' => $this->post_id,
        'slug' => $slug,
      ), $params);
    }    
    
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getActionHref($action, $params = array())
  {
    $params = array_merge(array(
      'route' => 'post_specific',
      'reset' => true,
      'post_id' => $this->post_id,
      'action' => $action
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  
  public function getEditHref($params = array())
  {
    return $this->getActionHref('edit', $params);
  }
  
  public function getDeleteHref($params = array())
  {
    return $this->getActionHref('delete', $params);
  }  

  public function getCategory()
  {
    if (!($this->category instanceof Post_Model_Category) || $this->category->getIdentity() != $this->category_id)
    {
      $category = Engine_Api::_()->getItemTable('post_category')->getCategory($this->category_id);
      if (!($category instanceof Post_Model_Category))
      {
        $category = new Post_Model_Category(array());
      }
      $this->category = $category;
    }

    return $this->category;
  }  
  
  // Interfaces
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  

  /**
   * Gets a proxy object for the vote handler
   *
   * @return Engine_ProxyObject
   **/
  public function votes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('votes', 'post'));
  }
  
  
  public function setPhotoFromUrl($source)
  {
    $file = Engine_Api::_()->post()->downloadImage($source);
    if ($file) {
      $this->setPhoto($file);
      @unlink($file);
      $this->thumb = $source;
    }
    else {
      throw new Core_Model_Item_Exception('Unable to download image: "'.$source.'"');
    }
  }
  // setPhotoFromUrl  
  
  public function setPhoto($photo)
  {  
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $file = Engine_Api::_()->getItem('storage_file', $photo->file_id)->temporary();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Post_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();
      
    // Resize image (showcase)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(320, 200)
      ->write($path.'/sc_'.$name)
      ->destroy();
          /*
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 320, 200)
      ->write($path.'/sc_'.$name)
      ->destroy();
      */
      
    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);
    $iShowcase = $storage->create($path.'/sc_'.$name, $params);
    
    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');
    $iMain->bridge($iShowcase, 'thumb.showcase');
    
    
    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);
    @unlink($path.'/sc_'.$name);
    
    // Update row
    $this->photo_id = $iMain->file_id;
    $this->save();
    
    return $this;  
  }
  
  public function removePhoto()
  {
    if (empty($this->photo_id))
    {
      return;
    }
    
    $types = array(null, 'thumb.profile', 'thumb.normal', 'thumb.icon', 'thumb.showcase');
    foreach ($types as $type)
    {
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->photo_id, $type);
      if ($file)
      {
        $file->remove();
      } 
    }
    
    $this->photo_id = 0;
  }
  
  
  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    // Delete all field values
    $values = Engine_Api::_()->fields()->getFieldsValues($this);
    foreach ($values as $value)
    {
      $value->delete();
    }
    
    // Delete search row
    $search = Engine_Api::_()->fields()->getFieldsSearch($this);
    if ($search)
    {
      $search->delete();
    }

    // Delete all tags
    $tagmaps = $this->tags()->getTagMaps();
    foreach ($tagmaps as $tagmap)
    {
      $tagmap->delete();
    }
    
    // Delete all likes
    $likes = $this->likes()->getAllLikes();
    foreach ($likes as $like)
    {
    	$like->delete();
    }
    
    
    // Delete all votes
    $votes = $this->votes()->getAllVotes();
    foreach ($votes as $vote)
    {
      $vote->delete();
    }
    
    
    $this->removePhoto();
    
    parent::_delete();
  }
 
  
  protected function _insert()
  {
    $this->updateSourceFromUrl();
    parent::_insert();
  }
  
  protected function _update()
  {
    $this->updateSourceFromUrl();
    parent::_update();
  }
  
  public function updateHotness()
  {
    $this->getTable()->updateHotness(array('post_id = ?'=>$this->getIdentity()));
  }
  
  protected function updateSourceFromUrl()
  {
    if ($this->url) {
      $host = @parse_url($this->url, PHP_URL_HOST);  
      $this->source = str_replace('www.','',strtolower($host));
    }
    else {
      $this->source = '';
    }
  }
  
  public function updateStatus($status)
  {
    $this->status = $status;
    $this->status_date = date('Y-m-d H:i:s');
    
    if ($status == self::STATUS_APPROVED && !$this->approved_date) {
      $this->approved_date = date('Y-m-d H:i:s');
    }
    
  }
  
  public function isApprovedStatus()
  {
    return $this->isStatus(self::STATUS_APPROVED);  
  }
  
  public function isRejectedStatus()
  {
    return $this->isStatus(self::STATUS_REJECTED);  
  }
  
  public function isPendingStatus()
  {
    return $this->isStatus(self::STATUS_PENDING);  
  } 
  
  public function isStatus($status)
  {
    return $this->status == $status;
  }  

  public function isMedia($type)
  {
    return $this->media == $type;
  }
  
  public function getStatusText()
  {
    return $this->getStatusTypes($this->status);
  }
  
  public function getMediaText()
  {
    return $this->getMediaTypes($this->media);
  }
  
  static public function getMediaTypes($key=null)
  {
    $types = array(
      self::MEDIA_TOPIC => 'Topic',
      self::MEDIA_LINK  => 'Link',
      self::MEDIA_PHOTO => 'Photo',
      self::MEDIA_VIDEO => 'Video',
        
    );
    
    if ($key !== null) {
      return (isset($types[$key])) ? $types[$key] : $key;
    }
    
    return $types;
  }
  
  static public function getStatusTypes($key=null)
  {
    $types = array(
      self::STATUS_APPROVED => 'Approved',
      self::STATUS_REJECTED => 'Rejected',
      self::STATUS_PENDING => 'Pending',
    );
    
    if ($key !== null) {
      return (isset($types[$key])) ? $types[$key] : 'pending';
    }
    
    return $types;
  }  
  
  public function getKeywordsArray()
  {
    $keywords = $this->getKeywords();
    $tags = preg_split('/[,]+/', $keywords);
    $tags = array_filter(array_map("trim", $tags));
    return $tags;
  }
  
  public function getRelatedPosts($params = array())
  {
    // related posts
    $tag_ids = array();
    foreach ($this->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!empty($tag->text)) {
        $tag_ids[] = $tag->tag_id;
      }
    }
        
    if (empty($tag_ids)) {
      return null;
    }
    
    $values = array(
      'tag' => $tag_ids,
      'order' => 'random',
      'limit' => 5,
      'exclude_post_ids' => array($this->getIdentity())
    );

    $params = array_merge($values, $params);
    
    $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($params);
    
    if ($paginator->getTotalItemCount() == 0) {
      return null;
    }
    
    return $paginator;
  }

}