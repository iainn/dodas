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
 
class Post_Model_DbTable_Posts extends Engine_Db_Table
{
  protected $_rowClass = "Post_Model_Post";


  
  public function countPosts($params = array())
  {
    $paginator = $this->getPostPaginator($params);
    $total = $paginator->getTotalItemCount();
    /*
    $select = $this->getPostSelect($params);
    $rows = $this->fetchAll($select);
    $total= count($rows);
    */
    return $total;
  }
  
  
  public function getPost($post_id)
  {
    static $posts = array();
    
    if (!isset($posts[$post_id]))
    {
      $posts[$post_id] = $this->findRow($post_id);
    }
    
    return $posts[$post_id];
  }   
  
  
  /**
   * Gets a paginator for posts
   *
   * @return Zend_Paginator
   */
  public function getPostPaginator($params = array(), $options = null)
  {
    $paginator = Zend_Paginator::factory($this->getPostSelect($params, $options));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  
  /**
   * Gets a select object for the user's post entries
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getPostSelect($params = array(), $options = null)
  {
    $rName = $this->info('name');

    if (empty($params['order'])) {
      $params['order'] = 'recent';
    }    
    
    $select = $this->selectParamBuilder($params);
    
      // Process options
    $tmp = array();
    foreach( $params as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $params = $tmp; 
        
    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('post', $params);
    if (!empty($searchParts))
    {
      $searchTable = Engine_Api::_()->fields()->getTable('post', 'search')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($searchTable, "$searchTable.item_id = $rName.post_id")
        ->group("$rName.post_id");     
      foreach( $searchParts as $k => $v ) 
      {
        $select = $select->where("`{$searchTable}`.{$k}", $v);
      }
    }      
    
    if( !empty($params['tag']) )
    {          
      $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($tagTable, "$tagTable.resource_id = $rName.post_id")
        ->where($tagTable.'.resource_type = ?', 'post')
        ->where($tagTable.'.tag_id  IN (?)', $params['tag']);
        if (is_array($params['tag'])) {
          $select->group("$rName.post_id");
        }
    }    
    
    if (!empty($params['favorites']))
    {
      $user = Engine_Api::_()->user()->getUser($params['favorites']);
      
      $voteTable = Engine_Api::_()->getItemTable('post_vote')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($voteTable, "$voteTable.post_id = $rName.post_id")
        ->where($voteTable.'.helpful = ?', 1)
        ->where($voteTable.'.user_id = ?', $user->getIdentity());
      
    }    
    
    if (!empty($params['not_voted_by_user']))
    {
      $user = Engine_Api::_()->user()->getUser($params['not_voted_by_user']);
      if ($user->getIdentity()) {
        $voteTable = Engine_Api::_()->getItemTable('post_vote')->info('name');
        
        /*
        $select = $select
          ->setIntegrityCheck(false)
          ->from($rName)
          ->joinLeft($voteTable, "$voteTable.post_id = $rName.post_id", array())
          ->where("($voteTable.user_id <> ? OR $voteTable.user_id IS NULL)", $user->getIdentity())
          ;
				*/
          
        $voteSelect = new Zend_Db_Select($this->getAdapter());
        $voteSelect = $voteSelect->from($voteTable, array('post_id'))
          ->where("$voteTable.user_id = ?", $user->getIdentity()); 

        $select->where("$rName.post_id NOT IN (".$voteSelect->__toString().")");  

      }
    }
    
    //unset($params['user']);
    //Engine_Api::_()->radcodes()->varPrint($params, 'params');
   // echo $select->__toString();
    //exit;
    return $select;
  }
  
  
  public function selectParamBuilder($params = array(), $select = null)
  {
    $rName = $this->info('name');
    
    if ($select === null)
    {
      $select = $this->select();
    }
    
    if (isset($params['live']) && $params['live'])
    {
      $params['status'] = Post_Model_Post::STATUS_APPROVED;
      unset($params['live']);
    }
    
    if (isset($params['user']) && $params['user']) 
    {
      $user = Engine_Api::_()->user()->getUser($params['user']);
      $select->where($rName.'.user_id = ?', $user->getIdentity());
    }

    if (isset($params['category']) && $params['category'])
    {
      $category_id = ($params['category'] instanceof Core_Model_Item_Abstract) ? $params['category']->getIdentity() : (int) $params['category'];
      
      $category_ids = array($category_id);
      $categories = Engine_Api::_()->getItemTable('post_category')->getChildrenOfParent($category_id);
      foreach ($categories as $category) {
        $category_ids[] = $category->getIdentity();
      }

      $select->where($rName.'.category_id IN (?)', $category_ids);
    }    
    
    foreach (array('featured', 'sponsored', 'search') as $field)
    {
      if (isset($params[$field]))
      {
        $select->where($rName.".$field = ?", $params[$field] ? 1 : 0);
      }  
    }
    
    if (!empty($params['character']))
    {
      if ($params['character'] == '_') {
        $select->where($rName.".title NOT REGEXP ?", "^[[:alpha:]]");
      }
      else {
        $select->where($rName.".title LIKE ?", $params['character'].'%');
      }
    }
    
    if( !empty($params['keyword']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ? OR ".$rName.".keywords LIKE ?", '%'.$params['keyword'].'%');
    }   
    
    if (isset($params['previous_post'])) {
      $select->where($rName.".title < ?", $params['previous_post']);
    }
    
    if (isset($params['next_post'])) {
      $select->where($rName.".title > ?", $params['next_post']);
    }
    
    foreach (array('status','media','source') as $field)
    {
      if (isset($params[$field]) && $params[$field])
      {
        $select->where($rName.".$field = ?", $params[$field]);
      }
    }
       
    
    if( !empty($params['start_date']) )
    {
      $select->where($rName.".creation_date >= ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) )
    {
      $select->where($rName.".creation_date <= ?", date('Y-m-d', $params['end_date']));
    }
    
    if (isset($params['exclude_post_ids']) and !empty($params['exclude_post_ids']))
    {
    	$select->where($rName.".post_id NOT IN (?)", $params['exclude_post_ids']);
    }    
    
    if( !empty($params['period']))
    {
      $period_maps = array(
        '24hrs' => 1,
        'week' => 7,
        'month' => 30,
        'quarter' => 120,
        'year' => 365,
      );
      if (isset($period_maps[$params['period']]) && $period_maps[$params['period']])
      {
        $select->where($rName.".creation_date >= ?", date('Y-m-d H:i:s', time() - $period_maps[$params['period']] * 86400));
      }
    }   

    if (isset($params['order'])) 
    {
      switch ($params['order'])
      {
        case 'random':
          $order_expr = new Zend_Db_Expr('RAND()');
          break;
        case 'recent':
          $order_expr = $rName.".creation_date DESC";
          break;
        case 'lastupdated':
          $order_expr = $rName.".modified_date DESC";
          break;
        case 'mostcommented':
          $order_expr = $rName.".comment_count DESC";
          break;
        case 'mostliked':
          $order_expr = $rName.".like_count DESC";
          break;  
        case 'mostviewed':
          $order_expr = $rName.".view_count DESC";
          break;
          
        case 'mostvote':
          $order_expr = $rName.".vote_count DESC";
          break;
        case 'mostpoint':
          $order_expr = $rName.".point_count DESC";
          break;
        case 'mosthelpful':
          $order_expr = $rName.".helpful_count DESC";
          break; 
        case 'mostnothelpful':
          $order_expr = $rName.".nothelpful_count DESC";
          break;
        case 'mosthelpfulness':
          $order_expr = $rName.".helpfulness DESC";
          break;

        /*
         * once in a while 50
            a fews 40 
            dozens 30
            hundreds 20 
            thousands 10
         */  
        case 'hotness':
          //$base_time = 1364799600; //Mon, 01 Apr 2013 00:00:00 -0700
          //$ratio_offset = 8640 * (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.timefactor', 30);
          //$order_expr = "LOG10(ABS($rName.point_count) + 1) * SIGN($rName.point_count) + ((UNIX_TIMESTAMP($rName.creation_date) - $base_time) / $ratio_offset) DESC";
          $order_expr = $rName.".hotness DESC";
          break;
          
        case 'alphabet':
          $order_expr = $rName.".title ASC";
          break;

        default:
          $order_expr = !empty($params['order']) ? $params['order'] : $rName.'.creation_date DESC';
          
          if (!empty($params['order_direction'])) {
            $order_expr .= " " .$params['order_direction'];
          }
          
          if (!is_array($order_expr) && !($order_expr instanceof Zend_Db_Expr) and strpos($order_expr, '.') === false) {
            $order_expr = $rName.".".trim($order_expr);
          }
          break;
      }

      if (isset($params['preorder']) && $params['preorder'])
      {
	      $pre_orders = array(
	        1 => array("{$rName}.sponsored DESC"), // Sponsored listings, then user preference",
	        2 => array("{$rName}.sponsored DESC","{$rName}.featured DESC"), // "Sponsored listings, featured listings, then user preference",
	        3 => array("{$rName}.featured DESC"), // "Featured listings, then user preference",
	        4 => array("{$rName}.featured DESC","{$rName}.sponsored DESC"), // "Featured listings, sponsored listings, then user preference",
	    	);
	    	if (array_key_exists($params['preorder'], $pre_orders))
	    	{
	    		$order_expr = array_merge($pre_orders[$params['preorder']], array($order_expr));
	    	}
      }

      $select->order( $order_expr );
      unset($params['order']);
    }
    
    return $select;
  }
  
  
  public function getHotnessDbExpr()
  {
    $rName = $this->info('name');
    $base_time = 1364799600; //Mon, 01 Apr 2013 00:00:00 -0700
    $ratio_offset = 8640 * (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.timefactor', 30);
    $expr = "LOG10(ABS($rName.point_count) + 1) * SIGN($rName.point_count) + ((UNIX_TIMESTAMP($rName.approved_date) - $base_time) / $ratio_offset)";
    
    return new Zend_Db_Expr($expr);
  }
  
  public function updateHotness($where = array())
  {
    $this->update(array('hotness' => $this->getHotnessDbExpr()), $where);
  }
  
  
  public function getTopSubmitters($params = array())
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
    
    $select = $this->selectParamBuilder($params, $select);
    
    $rows = $select->query()->fetchAll();
    
    $result = array();
    foreach ($rows as $row) {
      $result[$row[$column]] = $row;
    }
    
    return $result;
  }  
  
}