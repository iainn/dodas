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
 
 
class Post_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init()
  {

    if (!Engine_Api::_()->radcodes()->validateLicense('post')) {
      return $this->_redirectCustom(array('route'=>'radcodes_general', 'action'=>'license', 'type'=>'post'));
    }
    
    
    if( !$this->_helper->requireAuth()->setAuthParams('post', null, 'view')->isValid() ) return;
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
          null !== ($post = Engine_Api::_()->getItem('post', $post_id)) )
      {
        Engine_Api::_()->core()->setSubject($post);
      }
      else if( 0 !== ($user_id = (int) $this->_getParam('user_id')) &&
          null !== ($user = Engine_Api::_()->getItem('user', $user_id)) )
      {
        Engine_Api::_()->core()->setSubject($user);
      }
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'create',
      'manage',
      'favorites',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'view' => 'post',
    ));
    
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('post', Engine_Api::_()->user()->getViewer(), 'create');
  }
  
  
  public function indexAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }
  

  public function manageAction()
  {   
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Post_Form_Filter_Manage();    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values); 
    
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    } 

    if (!empty($values['category']))
    {
      $this->view->categoryObject = Engine_Api::_()->getItemTable('post_category')->getCategory($values['category']);  
    }     
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.postperpage', 20);
    $values['user'] = $viewer;
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();
    
  }
  
  
  public function favoritesAction()
  {   
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Post_Form_Filter_Favorites();    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values); 
    
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    } 
    
    if (!empty($values['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($values['user']);
    }    

    if (!empty($values['category']))
    {
      $this->view->categoryObject = Engine_Api::_()->getItemTable('post_category')->getCategory($values['category']);  
    }   
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.postperpage', 20);
    $values['favorites'] = $viewer;
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();
    
  }
    
  
  public function createAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('post', null, 'create')->isValid()) return;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'post', 'max');
    $this->view->current_count = Engine_Api::_()->getItemTable('post')->countPosts(array('user'=>$viewer));

    if ($this->_getParam('format') != 'smoothbox') {
      $this->_helper->content->setEnabled();
    }
    
    $media = $this->_getParam('media');
    if (!array_key_exists($media, Post_Model_Post::getMediaTypes())) {
      $media = null;
      $this->_setParam('media', null);
    }
    $this->view->media = $media;
    
    if (!$media) {
      return;
    }
    
    $form_class = 'Post_Form_Post_Create_'.ucfirst($media);
    
    $this->view->form = $form = new $form_class();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'create'),'post_general',true));
    
    if (!$this->getRequest()->isPost())
    {
      if ($term = $this->_getParam('term')) {
        $form->title->setValue($term);
      }
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    
    $table = Engine_Api::_()->getItemTable('post');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {     
      $values = $form->getValues();
      $values['user_id'] = $viewer->getIdentity();
                         
      $post = $table->createRow();
      $post->setFromArray($values);
      
      $status = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'post', 'approval') 
      	? Post_Model_Post::STATUS_PENDING
      	: Post_Model_Post::STATUS_APPROVED;
      $post->updateStatus($status);
      
      $post->save();
      
      
      // Add tags
      $tags = preg_split('/[,]+/', $values['keywords']);
      $tags = array_filter(array_map("trim", $tags));
      $post->tags()->addTagMaps($viewer, $tags);

      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($post);
      $customfieldform->saveValues();

      // Set photo
      if( !empty($values['photo']) ) 
      {
        $post->setPhoto($form->photo);
      }      
      else if (!empty($values['thumb']))
      {
        $post->setPhotoFromUrl($values['thumb']);
      }
      
      
      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;  
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      $auth_keys = array(
        'view' => 'everyone',
        'comment' => 'registered',
      );
      
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);
        
        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($post, $role, $auth_key, ($i <= $authMax));
        }
      }

      if ($post->isApprovedStatus())
      {
        $post->updateHotness();
        
        // Add activity
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $post, 'post_new_'.$post->media);
        if ($action != null)
        {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $post);
        }
      }
      // Commit
      $db->commit();
      
      $this->_redirectCustom(array('route' => 'post_specific', 'action' => 'success', 'post_id' => $post->getIdentity()));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    
  }

  
  public function popularAction()
  {
    $this->_helper->content->setEnabled();
    
  	$this->view->character = $character = $this->_getParam('character');
  	$limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.termperpage', 150);
  	$params = array(
  		'character' => $character,
  	  'order' => 'total_score desc',
  		'limit' => $limit,
  	);
  	
  	//$this->view->terms = Engine_Api::_()->getItemTable('post')->getPopularTerms($params);
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getTermPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    
    
  }
  

  public function browseAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Post_Form_Filter_Browse();    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values); 
    
            
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    } 
    
    if (!empty($values['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($values['user']);
    }    

    if (!empty($values['category']))
    {
      $this->view->categoryObject = Engine_Api::_()->getItemTable('post_category')->getCategory($values['category']);  
    }      
    
    $values['live'] = true;
    $values['search'] = 1;
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.postperpage', 20);
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();
  }
  // browse
  
  
  public function hotAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Post_Form_Filter_Hot();    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values); 
    
            
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    } 
    
    if (!empty($values['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($values['user']);
    }    

    if (!empty($values['category']))
    {
      $this->view->categoryObject = Engine_Api::_()->getItemTable('post_category')->getCategory($values['category']);  
    }   
    
    $values['order'] = 'hotness';
    $values['live'] = true;
    $values['search'] = 1;
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.postperpage', 20);
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();
  }
  // hot
  
  public function newAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Post_Form_Filter_New();    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values); 
    
            
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    } 
    
    if (!empty($values['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($values['user']);
    }    

    if (!empty($values['category']))
    {
      $this->view->categoryObject = Engine_Api::_()->getItemTable('post_category')->getCategory($values['category']);  
    }   
    
    $values['order'] = 'recent';
    $values['live'] = true;
    $values['search'] = 1;
    $values['not_voted_by_user'] = $viewer;
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.postperpage', 20);
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();
  }
  // new
  
  public function topAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Post_Form_Filter_Top();    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values); 
    
            
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    } 
    
    if (!empty($values['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($values['user']);
    }    

    if (!empty($values['category']))
    {
      $this->view->categoryObject = Engine_Api::_()->getItemTable('post_category')->getCategory($values['category']);  
    }   
    
    $values['order'] = 'mostpoint';
    $values['live'] = true;
    $values['search'] = 1;
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('post.postperpage', 20);
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();
  }
  // top
  
  
  public function suggestFindImagesAction()
  {
    $uri = strip_tags($this->_getParam('uri'));
    
    try
    {
      $client = new Zend_Http_Client($uri, array(
        'maxredirects' => 2,
        'timeout'      => 15,
      ));

      // Try to mimic the requesting user's UA
      $client->setHeaders(array(
        'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
        'X-Powered-By' => 'Zend Framework'
      ));

      $response = $client->request();

      // Get content-type
      list($contentType) = explode(';', $response->getHeader('content-type'));
      $this->view->contentType = $contentType;

      // Prepare
      $this->view->title = null;
      $this->view->description = null;
      $this->view->keywords = null;
      
      // Handling based on content-type
      switch( strtolower($contentType) ) {
        // Images
        case 'image/gif':
        case 'image/jpeg':
        case 'image/jpg':
        case 'image/tif': // Might not work
        case 'image/xbm':
        case 'image/xpm':
        case 'image/png':
        case 'image/bmp': // Might not work
          $this->_suggestImage($uri, $response);
          break;
        
        // HTML
        case '':
        case 'text/html':
          $this->_suggestHtml($uri, $response);
          break;

        // Plain text
        case 'text/plain':
          $this->_suggestText($uri, $response);
          break;

        // Unknown
        default:
          break;
      }
    }

    catch( Exception $e )
    {
      $this->view->error = true;
      $this->view->message = $this->view->translate('Could not load this page.');
      $this->view->exception = $e->getMessage();
      //throw $e;
    }
    
  }

  protected function _suggestImage($uri, Zend_Http_Response $response)
  {
    $this->view->images = array($uri);
    $this->view->media = Post_Model_Post::MEDIA_PHOTO;
  }
  
  
  protected function _suggestText($uri, Zend_Http_Response $response)
  {
    $body = $response->getBody();
    if( preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
        preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches) ) {
      $charset = trim($matches[1]);
    } else {
      $charset = 'UTF-8';
    }
    if( function_exists('mb_convert_encoding') ) {
      $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
    }

    // Reduce whitespace
    $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);

    $this->view->title = substr($body, 0, 63);
    $this->view->description = substr($body, 0, 255);
  }

  protected function _suggestHtml($uri, Zend_Http_Response $response)
  {
    $video_data = Engine_Api::_()->post()->parseVideoDataFromUrl($uri);
    if ($video_data) {
      $this->view->title = $video_data['info']['title'];
      $this->view->description = $video_data['info']['description'];
      $this->view->images = array($video_data['info']['thumbnail']);
      return;
    }
    
    
    $body = $response->getBody();
    if( preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
        preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches) ) {
      $charset = trim($matches[1]);
    } else {
      $charset = 'UTF-8';
    }
    if( function_exists('mb_convert_encoding') ) {
      $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
    }

    // Get DOM
    if( class_exists('DOMDocument') ) {
      $dom = new Zend_Dom_Query($body);
    } else {
      $dom = null; // Maybe add b/c later
    }

    $title = null;
    if( $dom ) {
      $titleList = $dom->query('title');
      if( count($titleList) > 0 ) {
        $title = $titleList->current()->textContent;
        $title = substr($title, 0, 255);
      }
    }
    $this->view->title = $title;

    $description = null;
    if( $dom ) {
      $descriptionList = $dom->queryXpath("//meta[@name='description']");
      // Why are they using caps? -_-
      if( count($descriptionList) == 0 ) {
        $descriptionList = $dom->queryXpath("//meta[@name='Description']");
      }
      if( count($descriptionList) > 0 ) {
        $description = $descriptionList->current()->getAttribute('content');
        $description = substr($description, 0, 255);
      }
    }
    $this->view->description = $description;
    
    
    $keywords = null;
    if( $dom ) {
      $keywordsList = $dom->queryXpath("//meta[@name='keywords']");
      // Why are they using caps? -_-
      if( count($keywordsList) == 0 ) {
        $keywordsList = $dom->queryXpath("//meta[@name='Keywords']");
      }
      if( count($keywordsList) > 0 ) {
        $keywords = $keywordsList->current()->getAttribute('content');
      }
    }
    $this->view->keywords = $keywords;    
    
    ////////////////
    
    $thumb = null;
    if( $dom ) {
      $thumbList = $dom->queryXpath("//link[@rel='image_src']");
      if( count($thumbList) > 0 ) {
        $thumb = $thumbList->current()->getAttribute('href');
      }
    }

    // Get baseUrl and baseHref to parse . paths
    $baseUrlInfo = parse_url($uri);
    $baseUrl = null;
    $baseHostUrl = null;
    if( $dom ) {
      $baseUrlList = $dom->query('base');
      if( $baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href') ) {
        $baseUrl = $baseUrlList->current()->getAttribute('href');
        $baseUrlInfo = parse_url($baseUrl);
        $baseHostUrl = $baseUrlInfo['scheme'].'://'.$baseUrlInfo['host'].'/';
      }
    }
    if( !$baseUrl ) {
      $baseHostUrl = $baseUrlInfo['scheme'].'://'.$baseUrlInfo['host'].'/';
      if( empty($baseUrlInfo['path']) ) {
        $baseUrl = $baseHostUrl;
      } else {
        $baseUrl = explode('/', $baseUrlInfo['path']);
        array_pop($baseUrl);
        $baseUrl = join('/', $baseUrl);
        $baseUrl = trim($baseUrl, '/');
        $baseUrl = $baseUrlInfo['scheme'].'://'.$baseUrlInfo['host'].'/'.$baseUrl.'/';
      }
    }

    $images = array();
    if( $thumb ) {
      $images[] = $thumb;
    }
    if( $dom ) {
      $imageQuery = $dom->query('img');
      foreach( $imageQuery as $image )
      {
        $src = $image->getAttribute('src');

        // Ignore images that don't have a src
        if( !$src || false === ($srcInfo = @parse_url($src)) ) {
          continue;
        }
        $ext = ltrim(strrchr($src, '.'), '.');
        // Detect absolute url
        if (strpos($src, '//') === 0) {
          $src = $baseUrlInfo['scheme'] . ":" . $src;
        }
        else if( strpos($src, '/') === 0 ) {
          // If relative to root, add host
          $src = $baseHostUrl . ltrim($src, '/');
        } else if( strpos($src, './') === 0 ) {
          // If relative to current path, add baseUrl
          $src = $baseUrl . substr($src, 2);
        } else if( !empty($srcInfo['scheme']) && !empty($srcInfo['host']) ) {
          // Contians host and scheme, do nothing
        } else if( empty($srcInfo['scheme']) && empty($srcInfo['host']) ) {
          // if not contains scheme or host, add base
          $src = $baseUrl . ltrim($src, '/');
        } else if( empty($srcInfo['scheme']) && !empty($srcInfo['host']) ) {
          // if contains host, but not scheme, add scheme?
          $src = $baseUrlInfo['scheme'] . ltrim($src, '/');
        } else {
          // Just add base
          $src = $baseUrl . ltrim($src, '/');
        }
        // Ignore images that don't come from the same domain
        //if( strpos($src, $srcInfo['host']) === false ) {
          // @todo should we do this? disabled for now
          //continue;
        //}
        // Ignore images that don't end in an image extension
        if( !in_array($ext, array('jpg', 'jpeg', 'gif', 'png')) ) {
          // @todo should we do this? disabled for now
          //continue;
        }
        if( !in_array($src, $images) ) {
          $images[] = $src;
        }
      }
    }

    // Unique
    $images = array_values(array_unique($images));
    
    $this->view->images = $images;
  }
  

  public function licenseAction()
  {
    
  }
  
}

