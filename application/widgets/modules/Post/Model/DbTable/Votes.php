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
 
 
 
class Post_Model_DbTable_Votes extends Engine_Db_Table
{
  protected $_rowClass = "Post_Model_Vote";
  

  public function updatePreferenceCountKeys(Post_Model_Post $post)
  {
    $select = new Zend_Db_Select($this->getAdapter());
    $select
      ->from($this->info('name'), array(
      'vote_count' => new Zend_Db_Expr('COUNT(*)'), 
      'helpful_count' => new Zend_Db_Expr('SUM(helpful)')
    ));

    $select->where('post_id = ?', $post->getIdentity());

    $row = $select->query()->fetch();
    
    if (is_array($row))
    {
      $post->vote_count = $row['vote_count'] ? $row['vote_count'] : 0;
      $post->helpful_count = $row['helpful_count'] ? $row['helpful_count'] : 0;
      $post->nothelpful_count = $post->vote_count - $post->helpful_count;
      $post->point_count = $post->helpful_count - $post->nothelpful_count;
    }
    else 
    {
      $post->vote_count = 0;
      $post->helpful_count = 0;
      $post->nothelpful_count = 0;
      $post->point_count = 0;
    }
    
    if ($post->vote_count) {
      $post->helpfulness = (int) ($post->helpful_count / $post->vote_count * 100);
    }
    else {
      $post->helpfulness = 0;
    }
    
    $post->save();
    $post->updateHotness();
  }
  
  
  public function addVote(Post_Model_Post $post, User_Model_User $user, $helpful)
  {
    $row = $this->getVote($post, $user);
    if( null !== $row )
    {
      throw new Post_Model_Exception('Already voted');
    }

    $row = $this->createRow();

    $row->post_id = $post->getIdentity();
    $row->user_id = $user->getIdentity();
    $row->helpful = $helpful ? 1 : 0;
    $row->save();

    $this->updatePreferenceCountKeys($post);
    
    return $row;
  }

  public function removeVote(Post_Model_Post $post, User_Model_User $user)
  {
    $row = $this->getVote($post, $user);
    if( null === $row )
    {
      throw new Post_Model_Exception('No vote to remove');
    }
    
    $row->delete();

    $this->updatePreferenceCountKeys($post);
    
    return $this;
  }

  
  
  public function isVote(Post_Model_Post $post, User_Model_User $user)
  {
    return ( null !== $this->getVote($post, $user) );
  }

  public function getVote(Post_Model_Post $post, User_Model_User $user)
  {
    $select = $this->getVoteSelect($post)
      ->where('user_id = ?', $user->getIdentity())
      ->limit(1);

    return $this->fetchRow($select);
  }

  public function getVoteSelect(Post_Model_Post $post)
  {
    $select = $this->select();

    $select
      ->where('post_id = ?', $post->getIdentity())
      ->order('vote_id ASC');

    return $select;
  }

  public function getVotePaginator(Post_Model_Post $post)
  {
    $paginator = Zend_Paginator::factory($this->getVoteSelect($post));
    $paginator->setItemCountPerPage(3);
    $paginator->count();
    $pages = $paginator->getPageRange();
    $paginator->setCurrentPageNumber($pages);
    return $paginator;
  }

  
  public function getAllVotes(Post_Model_Post $post)
  {
    return $this->fetchAll($this->getVoteSelect($post));
  }

  
  public function getAllVotesUsers(Post_Model_Post $post, $helpful = null)
  {
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from($this->info('name'), array('user_id', 'vote'));

    $select->where('post_id = ?', $post->getIdentity());
    if ($helpful !== null)
    {
      $select->where('helpful = ?', $helpful ? 1 : 0);
    }
    
    $users = array();
    foreach( $select->query()->fetchAll() as $data )
    {
      $users[] = $data['user_id'];
    }
    $users = array_values(array_unique($users));

    return Engine_Api::_()->getItemMulti('user', $users);
  }
  
  public function getTopVoters($params = array())
  {
    $column = 'user_id';
    
    $rName = $this->info('name');
    
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from($this->info('name'), array(
      'user_id' => $column,
      'total' => new Zend_Db_Expr('COUNT(*)'),
    ));
    $select->group($column);

    $select->order('total desc');
    
    if (isset($params['limit'])) {
      $select->limit($params['limit']);
      unset($params['limit']);
    }
        
    $rows = $select->query()->fetchAll();
    
    $result = array();
    foreach ($rows as $row) {
      $result[$row[$column]] = $row;
    }
    
    return $result;
  }
}