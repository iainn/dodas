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
 
 
 
class Post_Model_Vote extends Core_Model_Item_Abstract
{
  // Properties

  protected $_parent_type = 'post';
  
  protected $_owner_type = 'user';

  protected $_searchTriggers = false;
  
  protected $post;
  
  public function getPost()
  {
    if (!($this->post instanceof Post_Model_Post) || $this->post->getIdentity() != $this->post_id)
    {
      $post = Engine_Api::_()->getItemTable('post')->getPost($this->post_id);
      if (!($post instanceof Post_Model_Post))
      {
        $post = new Post_Model_Post(array());
      }
      $this->post = $post;
    }

    return $this->post;
  }
  
}
