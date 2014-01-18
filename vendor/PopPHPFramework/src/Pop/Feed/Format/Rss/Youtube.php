<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Feed
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Feed\Format\Rss;

/**
 * Youtube RSS feed reader class
 *
 * @category   Pop
 * @package    Pop_Feed
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Youtube extends \Pop\Feed\Format\Rss
{

    /**
     * Feed URLs templates
     * @var array
     */
    protected $urls = array(
        'name' => 'http://gdata.youtube.com/feeds/base/users/[{name}]/uploads?v=2&alt=rss',
        'id'   => 'http://gdata.youtube.com/feeds/api/playlists/[{id}]?v=2&alt=rss'
    );

    /**
     * Method to create a Youtube RSS feed object
     *
     * @param  mixed  $options
     * @param  int    $limit
     * @return \Pop\Feed\Format\Rss\Youtube
     */
    public function __construct($options, $limit = 0)
    {
        // Attempt to get the correct URL to parse
        if (is_array($options)) {
            if (isset($options['name'])) {
                $this->url = str_replace('[{name}]', $options['name'], $this->urls['name']);
            } else if (isset($options['id'])) {
                $this->url = str_replace('[{id}]', $options['id'], $this->urls['id']);
            }
        }

        parent::__construct($options, $limit);
    }

    /**
     * Method to parse a Youtube RSS feed object
     *
     * @return void
     */
    public function parse()
    {
        parent::parse();

        $items = $this->feed['items'];
        foreach ($items as $key => $item) {
            $id = substr($item['link'], (strpos($item['link'], 'v=') + 2));
            if (strpos($id, '&') !== false) {
                $id = substr($id, 0, strpos($id, '&'));
            }
            $items[$key]['id'] = $id;
            $youtube = \Pop\Http\Response::parse('http://gdata.youtube.com/feeds/api/videos/' . $id . '?v=2&alt=json');
            if (!$youtube->isError()) {
                $info = json_decode($youtube->getBody(), true);
                $items[$key]['views'] = $info['entry']['yt$statistics']['viewCount'];
                $items[$key]['likes'] = $info['entry']['yt$rating']['numLikes'];
                $items[$key]['duration'] = $info['entry']['media$group']['yt$duration']['seconds'];
                $items[$key]['image_thumb']  = 'http://i.ytimg.com/vi/' . $id . '/default.jpg';
                $items[$key]['image_medium'] = 'http://i.ytimg.com/vi/' . $id . '/mqdefault.jpg';
                $items[$key]['image_large']  = 'http://i.ytimg.com/vi/' . $id . '/hqdefault.jpg';
                foreach ($info as $k => $v) {
                    if ($v != '') {
                        $items[$key][$k] = $v;
                    }
                }
            }
        }

        $this->feed['items'] = $items;
    }

}
