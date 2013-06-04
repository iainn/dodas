<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Poll_Widget_ListPopularPollsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Should we consider views or comments popular?
    $popularType = $this->_getParam('popularType', 'vote');
    if( !in_array($popularType, array('comment', 'view', 'vote')) ) {
      $popularType = 'vote';
    }
    $this->view->popularType = $popularType;
    $this->view->popularCol = $popularCol = $popularType . '_count';

    // Get paginator
    $table = Engine_Api::_()->getItemTable('poll');
    $select = $table->select()
      ->where('search = ?', 1)
      ->order($popularCol . ' DESC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Hide if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
}