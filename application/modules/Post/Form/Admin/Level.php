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
 
 
 
class Post_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract
{
  
  public function init()
  {
    parent::init();
    
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");
      
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Posts?',
      'description' => 'Do you want to let members view posts? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all posts, even private ones.',
        1 => 'Yes, allow viewing of posts.',
        0 => 'No, do not allow posts to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }
    
    if( !$this->isPublic() ) 
    {
      
	    $this->addElement('Radio', 'create', array(
	      'label' => 'Allow Creation of Posts?',
	      'description' => 'Do you want to let members view posts? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        1 => 'Yes, allow creation of posts.',
	        0 => 'No, do not allow posts to be created.'
	      ),
	      'value' => 1,
	    ));    
	    
	    $this->addElement('Radio', 'edit', array(
	      'label' => 'Allow Editing of Posts?',
	      'description' => 'Do you want to let members edit posts? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to edit all posts.',
	        1 => 'Yes, allow members to edit their own posts.',
	        0 => 'No, do not allow members to edit their posts.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }
      
	    $this->addElement('Radio', 'delete', array(
	      'label' => 'Allow Deletion of Posts?',
	      'description' => 'Do you want to let members delete posts? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to delete all posts.',
	        1 => 'Yes, allow members to delete their own posts.',
	        0 => 'No, do not allow members to delete their posts.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }
	
      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Posts?',
        'description' => 'Do you want to let members of this level comment on posts?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all posts, including private ones.',
          1 => 'Yes, allow members to comment on posts.',
          0 => 'No, do not allow members to comment on posts.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      
	    $this->addElement('Radio', 'approval', array(
	      'label' => 'Require Approval?',
	      'description' => 'Do you want to manually approve all new posts before they are published?',
	      'multiOptions' => array(
	        1 => 'Yes, manually approve new posts.',
	        0 => 'No, auto publish new posts.'
	      ),
	      'value' => 0,
	    ));
	    
	    // PRIVACY ELEMENTS
	    
	    $this->addElement('MultiCheckbox', 'auth_view', array(
	      'label' => 'Posts View Privacy',
	      'description' => 'Your users can choose from any of the options checked below when they decide who can see their posts. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
	        'multiOptions' => array(
	          'everyone'            => 'Everyone',
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('everyone', 'registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));
	    
      
	    $this->addElement('MultiCheckbox', 'auth_comment', array(
	      'label' => 'Post Comment Options',
	      'description' => 'Your users can choose from any of the options checked below when they decide who can comment on their posts. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
	        'multiOptions' => array(
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));	    

      $this->addElement('Text', 'max_posts', array(
        'label' => 'Maximum Allowed Posts',
        'description' => 'Enter the maximum number of allowed posts. The field must contain an integer, use zero for unlimited.',
        'class' => 'short',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      ));
      
    } // end isPublic()
  }

}