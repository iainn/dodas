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
 
 
 
class Post_Api_Core extends Core_Api_Abstract
{
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;

  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;
  
  public function parseVideoDataFromUrl($url, $live_check = true)
  {
    $sources = array(
      'youtube' => array(
      	'pattern' => '/http:\/\/(www.|uk.|br.|fr.|ie.|it.|jp.|nl.|pl.|es.)?youtube.com\/watch\?v=([0-9a-zA-Z-\_]+)(.*)/i',
        'code_index' => 2,
        'info_method' => 'getYouTubeVideo',
      ),
      'youtu.be' => array(
      	'pattern' => '/http:\/\/(www.)?youtu.be\/([0-9a-zA-Z-\_]+)/i',
        'code_index' => 2,
        'info_method' => 'getYouTubeVideo',
      ),
      'vimeo' => array(
      	'pattern' => '/http:\/\/(www.)?vimeo.com\/([0-9a-zA-Z-\_]+)/i',
        'code_index' => 2,
        'info_method' => 'getVimeoVideo',
      )
    );

    foreach ($sources as $type => $source) {
      if (preg_match($source['pattern'], $url, $matches)) {
        $code = $matches[$source['code_index']];
        $data = array(
        	'type' => $type,
          'code' => $code,
          'matches' => $matches
        );
        
        if ($live_check) {
          $method = $source['info_method'];
          $info = $this->$method($code);
          if (!$info) {
            return false;
          }
          
          $data['info'] = $info;
        }
        
        return $data;
      }
    }
    return false;
  }
  
  public function getYouTubeVideo($code)
  {
    try
    {
	    $yt = new Zend_Gdata_YouTube();
	    $yt->setMajorProtocolVersion(2);
	    $video = $yt->getVideoEntry($code);
	    
      $information = array();
      $information['title'] = $video->getVideoTitle();
      $information['description'] = $video->getVideoDescription();
      $information['thumbnail'] = "http://img.youtube.com/vi/$code/default.jpg";
      $information['player_url'] = "http://www.youtube.com/embed/$code";
      return $information;
    }
    catch (Exception $ex)
    {
      
    } 

    return false;  
  }
  
  public function getVimeoVideo($code)
  {
    try
    {
      
      $config = array('timeout' => 120);
      
      $enableCURL = Engine_Api::_()->getApi('map', 'radcodes')->useCURL();
      if ($enableCURL) {
        $config = array_merge($config, array(
          'adapter'   => 'Zend_Http_Client_Adapter_Curl',
          'curloptions' => array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
          ),  
        ));
      }
      
      $remoteUrl = "http://vimeo.com/api/v2/video/".$code.".php";
      $client = new Zend_Http_Client($remoteUrl, $config);      
      $client->setParameterGet($params);
      $response = $client->request(Zend_Http_Client::GET);
          
      if ($response->isSuccessful())
      {
        $raw_data = $response->getBody();    
        
        $packed_data = unserialize($raw_data);
        $data = $packed_data[0];
        $information = array();
        $information['title'] = $data['title'];
        $information['description'] = strip_tags($data['description']);
        $information['thumbnail'] = $data['thumbnail_large'];
        if (!$information['thumbnail']) {
          $information['thumbnail'] = $data['thumbnail_medium'];
        }
        if (!$information['thumbnail']) {
          $information['thumbnail'] = $data['thumbnail_small'];
        }
        $information['player_url'] = "http://player.vimeo.com/video/$code";
        return $information;
      }     

      
    }
    catch (Exception $ex)
    {
      
    }
    return false;
  }
  
  public function downloadImage($url)
  {
    // Now try to create thumbnail
    $thumbnail = (string) $url;
    $thumbnail_parsed = @parse_url($thumbnail);    
    
    if( $thumbnail && $thumbnail_parsed ) {
      $tmp_path = APPLICATION_PATH . '/temporary/post';
      $tmp_file = $tmp_path . '/' . md5($thumbnail);
  
      if( !is_dir($tmp_path) && !mkdir($tmp_path, 0777, true) ) {
        throw new Core_Model_Exception('Unable to create tmp post folder: ' . $tmp_path);
      }

      $src_fh = fopen($thumbnail, 'r');
      $tmp_fh = fopen($tmp_file, 'w');
      stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
      fclose($src_fh);
      fclose($tmp_fh);
      
      if( ($info = getimagesize($tmp_file)) && !empty($info[2]) ) {
        $ext = Engine_Image::image_type_to_extension($info[2]);
        
        $new_tmp_file = $tmp_file.'.'.$ext;
        rename($tmp_file, $new_tmp_file);
        $tmp_file = $new_tmp_file;
      }

      return $tmp_file;
    }
    else {
      return false;
    }
  }
  
  
  public function checkLicense()
  {
    $license = Engine_Api::_()->getApi('settings', 'core')->getSetting('post.license');
    return (trim($license) && $license != 'XXXX-XXXX-XXXX-XXXX');
  }  
  

  
  public function getPopularTags($options = array())
  {
    $resource_type = 'post';
    
    $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
    $tagmap_table = $tag_table->getMapTable();
    
    $tName = $tag_table->info('name');
    $tmName = $tagmap_table->info('name');
    
    if (isset($options['order']))
    {
      $order = $options['order'];
    }
    else
    {
      $order = 'text';
    }
    
    if (isset($options['sort']))
    {
      $sort = $options['sort'];
    }
    else
    {
      $sort = $order == 'total' ? SORT_DESC : SORT_ASC;
    }
    
    $limit = isset($options['limit']) ? $options['limit'] : 50;
    
    $select = $tag_table->select()
        ->setIntegrityCheck(false)
        ->from($tmName, array('total' => "COUNT(*)"))
        ->join($tName, "$tName.tag_id = $tmName.tag_id")
        ->where($tmName.'.resource_type = ?', $resource_type)
        ->where($tmName.'.tag_type = ?', 'core_tag')
        ->group("$tName.tag_id")
        ->order("total desc")
        ->limit("$limit");

    $params = array('live' => true); 
    $post_table = Engine_Api::_()->getItemTable('post');
    $rName = $post_table->info('name');
    
            
    
    $select->setIntegrityCheck(false)
        ->join($rName, "$tmName.resource_id = $rName.post_id");
    $select = $post_table->selectParamBuilder($params, $select);    
    //echo $select;
    
    $tags = $tag_table->fetchAll($select);   
    
    $records = array();
    
    $columns = array();
    if (!empty($tags))
    {
      foreach ($tags as $k => $tag)
      {
        $records[$k] = $tag;
        $columns[$k] = $order == 'total' ? $tag->total : $tag->text; 
      }
    }

    $tags = array();
    if (count($columns))
    {
      if ($order == 'text') {
        natcasesort($columns);
      }
      else {
        arsort($columns);
      }

      foreach ($columns as $k => $name)
      {
        $tags[$k] = $records[$k];
      }
    }

    return $tags;
  }  
  
  
  public function pushStatusUpdateNotification(Post_Model_Post $post)
  {
    $owner = $post->getOwner();
    $viewer = Engine_Api::_()->user()->getViewer();
    
  	$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notifyApi->addNotification($owner, $viewer, $post, 'post_status_update', array(
      'status' => Zend_Registry::get('Zend_Translate')->translate($post->getStatusText())
    ));
  }
  
  public function pushNewPostActivity(Post_Model_Post $post)
  {
    // Add activity only if post is published
    $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($post);
    if (count($action->toArray())<=0)
    {
      $owner = $post->getOwner();
      $media = $post->media;
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $post, 'post_new_'.$media);
      if($action!=null){
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $post);
      }
      
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($post) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
    }
  }  
}