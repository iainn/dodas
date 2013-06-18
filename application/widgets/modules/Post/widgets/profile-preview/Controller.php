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
class Post_Widget_ProfilePreviewController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->post = $subject = Engine_Api::_()->core()->getSubject('post');
    
    if( !($subject instanceof Post_Model_Post) ) {
      return $this->setNoRender();
    }    
    
    if (!in_array($subject->media, array('photo', 'video'))) {
      return $this->setNoRender();
    }
    
    if ($subject->isMedia('video')) {
      $video_data = Engine_Api::_()->post()->parseVideoDataFromUrl($subject->url);
      if (!$video_data) {
        return $this->setNoRender();
      }
      $this->view->video_data = $video_data;
    }
    
  }
}