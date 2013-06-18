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
 
 
 
class Post_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('posts', 'post');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'buzz post');
  }


  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      
      $user_id = $payload->getIdentity();
      
      // Delete posts
      $postTable = Engine_Api::_()->getItemTable('post');

      $postSelect = $postTable->select()->where('user_id = ?', $user_id);
      foreach( $postTable->fetchAll($postSelect) as $post ) {
        $post->delete();
      }

      $table = Engine_Api::_()->getItemTable('post_vote');
      $select = $table->select()->where("user_id = ?", $user_id);
      foreach ($table->fetchAll($select) as $vote)
      {
        $post = $vote->getPost();
        $vote->delete();
        $post->votes()->updatePreferenceCountKeys();
      }
      
    }
  }
}


