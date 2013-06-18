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
 
 
 
class Post_Form_Helper
{

  public static function getContentField($name, $options = array())
  {
    static $content_fields = null;
    
    if ($content_fields === null)
    {
      $content_fields = array(
      
        'title' => array(
                'Text',
                'title',
                array(
                  'label' => 'Title',
                )
              ),
        'max' => array(
                'Text',
                'max',
                array(
                  'label' => 'Max Posts',
                  'value' => 5,
                ),
              ),
        'user_type' => array(
                'Radio',
                'user_type',
                array(
                    'label' => 'User',
                    'multiOptions' => array(
                      'owner' => 'OWNER - post\'s created by owner of the current viewing page',
                      'viewer' => 'VIEWER - post\'s created by the current active logged in member',
                    ),
                    'value' => 'owner',
                  ),
                ),
        'user' => array(
                'Text',
                'user',
                array(
                  'label' => 'User',
                )
              ),
        'keyword' => array(
                'Text',
                'keyword',
                array(
                  'label' => 'Keywords',
                )
              ),
        'location' => array(
                'Text',
                'location',
                array(
                  'label' => 'Location',
                )
              ),
        'distance' => array(
                'Text',
                'distance',
                array(
                  'label' => 'Within Distance',
                )
              ),    
        'media' => array(
                'Select', 
                'media',
                array(
                  'label' => 'Media',
                  'multiOptions' => array(""=>"") + Post_Model_Post::getMediaTypes(),  
                )
              ),
        'category' => array(
                'Select', 
                'category',
                array(
                  'label' => 'Category',
                  'multiOptions' => array(""=>"") + Engine_Api::_()->getItemTable('post_category')->getMultiOptionsAssoc(),  
                )
              ),      
        'order' => array(
                'Select', 
                'order',
                array(
                  'label' => 'Sort By',
                  'multiOptions' => self::getOrderOptions() + array(
                    'random' => 'Randomized',
                  ),
                  'value' => 'recent',
                )
              ),
        'period' => array(
                'Select', 
                'period',
                array(
                  'label' => 'Time Period',
                  'multiOptions' => array(
                    'all' => 'All Time',
                    '24hrs' => 'Last 24 Hours',
                    'week' => '7 Days',
                    'month' => '30 Days',
                    'quarter' => '3 Months',
                    'year' => '12 Months',
                  ),
                )
              ),

        'display_style' => array(
                'Radio',
                'display_style',
                array(
                  'label' => 'Display Style',
                  'multiOptions' => array(
                    'wide' => "Wide (main middle column)",
                    'narrow' => "Narrow (left / side side column)",
                    'block' => "Block (main middle column)",  
                  ),
                  'value' => 'wide',
                )
              ),
        'show_keywords' => array(
                'Select', 
                'show_keywords',
                array(
                  'label' => 'Show Keywords',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'show_example' => array(
                'Select', 
                'show_example',
                array(
                  'label' => 'Show Example',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'show_photo' => array(
                'Select', 
                'show_photo',
                array(
                  'label' => 'Show Photo',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ),  
        'show_details' => array(
                'Select', 
                'show_details',
                array(
                  'label' => 'Show Details',
                  
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 0,
                )
              ), 
        'show_meta' =>  array(
                'Select', 
                'show_meta',
                array(
                  'label' => 'Show Meta',
                  
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'show_description' =>  array(
                'Select', 
                'show_description',
                array(
                  'label' => 'Show Description',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ),  
        'featured' => array(
                'Select', 
                'featured',
                array(
                  'label' => 'Show only featured posts?',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 0,
                )
              ),
        'sponsored' => array(
                'Select', 
                'sponsored',
                array(
                  'label' => 'Show only sponsored posts?',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 0,
                )
              ),     
        'submenu' => array(
                'Select', 
                'submenu',
                array(
                  'label' => 'Show Sub-Menu',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'showmemberitemlist' => array(
            'Select', 
            'showmemberitemlist',
            array(
              'label' => 'Show Member\'s Post Link',
              'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
              ),
              'value' => 1,
            )
          ),
        'showemptyresult' => array(
            'Select', 
            'showemptyresult',
            array(
              'label' => 'Show Empty Result',
              'multiOptions' => self::getYesNoOptions(),
              'value' => 0,
            )
          ), 
        'categorylinkto' => array(
                'Radio',
                'linkto',
                array(
                  'label' => 'Link To Page',
                  'multiOptions' => array(
                    'hot' => "Hot",
                    'new' => "New",
                    'top' => "Top",
                    'browse' => "Browse",
                  ),
                  'value' => 'hot',
                )
              ),
      );
    
    }
    
    if (array_key_exists($name, $content_fields)) {
      $field = $content_fields[$name];
    }
    else {
      $field = array(
        'Text',
        $name,
        array(
          'label' => $name
        ),
      );
    }
    
    $keys = array('value', 'label', 'multiOptions');
    foreach ($options as $key => $value) {
      if (in_array($key, $keys)) {
        $field[2][$key] = $value;
      }
    }
    
    return $field;
  }
  
  public static function getOrderOptions()
  {
    $options = array(
        'recent' => 'Most Recent',
      	'lastupdate' => 'Last Updated',
    
        'alphabet' => 'Alphabet',
    
        'hotness' => 'Hotness',
        
        'mostpoint' => 'Top Voted',
        'mosthelpful' => 'Up Votes',
        'mostnothelpful' => 'Down Votes',
        

    );
    
    return $options;
  }
  
  public static function getAlphabetOptions($key = null)
  {
    $options = array(
      '_' => '#',
      'A' => 'A',
      'B' => 'B',
      'C' => 'C',
      'D' => 'D',
      'E' => 'E',
      'F' => 'F',
      'G' => 'G',
      'H' => 'H',
      'I' => 'I',
      'J' => 'J',
      'K' => 'K',
      'L' => 'L',
      'M' => 'M',
      'N' => 'N',
      'O' => 'O',
      'P' => 'P',
      'Q' => 'Q',
      'R' => 'R',
      'S' => 'S',
      'T' => 'T',
      'U' => 'U',
      'V' => 'V',
      'W' => 'W',
      'X' => 'X',
      'Y' => 'Y',
      'Z' => 'Z',
    );
    
    if ($key !== null) {
      return (isset($options[$key])) ? $options[$key] : $key;
    }
    else {
      return $options;
    }
    
  } 
  
  public static function getYesNoOptions()
  {
    return array(
                    1 => 'Yes',
                    0 => 'No'
    );
  }  
  
  public static function getPeriodOptions()
  {
    return  array(
                    'all' => 'All Time',
                    '24hrs' => 'Last 24 Hours',
                    'week' => '7 Days',
                    'month' => '30 Days',
                    'quarter' => '3 Months',
                    'year' => '12 Months',
                  );
  }
}