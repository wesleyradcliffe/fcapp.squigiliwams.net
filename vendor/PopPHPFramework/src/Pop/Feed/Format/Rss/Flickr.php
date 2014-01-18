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
 * Flickr RSS feed reader class
 *
 * @category   Pop
 * @package    Pop_Feed
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Flickr extends \Pop\Feed\Format\Rss
{

    /**
     * Feed URLs templates
     * @var array
     */
    protected $urls = array(
        'id'   => 'http://api.flickr.com/services/feeds/photos_public.gne?id=[{id}]&format=rss_100'
    );

    /**
     * Method to create a Flickr RSS feed object
     *
     * @param  mixed  $options
     * @param  int    $limit
     * @return \Pop\Feed\Format\Rss\Flickr
     */
    public function __construct($options, $limit = 0)
    {
        // Attempt to get the correct URL to parse
        if (is_array($options)) {
            if (isset($options['id'])) {
                $this->url = str_replace('[{id}]', $options['id'], $this->urls['id']);
            }
        }

        parent::__construct($options, $limit);
    }

    /**
     * Method to parse a Flickr RSS feed object
     *
     * @return void
     */
    public function parse()
    {
        parent::parse();

        if (null === $this->feed['author']) {
            $this->feed['author'] = str_replace('Uploads from ', '', $this->feed['title']);
        }

        if (null === $this->feed['date']) {
            $this->feed['date'] = date('D, d M Y H:i:s O');
        }

        if (null === $this->feed['generator']) {
            $this->feed['generator'] = 'Flickr';
        }

        $namespaces = $this->obj->getDocNamespaces(true);

        $items = $this->feed['items'];
        foreach ($items as $key => $item) {
            $entry = $this->obj->item[0];
            $dc = $entry->children($namespaces['dc']);

            $image = substr($item['content'], (strpos($item['content'], '<img src="') + 10));
            $image = substr($image, 0, strpos($image, '"'));
            $items[$key]['published'] = (string)$dc->{'date.Taken'};
            $items[$key]['time'] = self::calculateTime($items[$key]['published']);
            $items[$key]['image_thumb']  = str_replace('_m', '_s', $image);
            $items[$key]['image_medium'] = $image;
            $items[$key]['image_large']  = str_replace('_m', '', $image);
            $items[$key]['image_orig']  = str_replace('_m', '_b', $image);
        }

        $this->feed['items'] = $items;
    }

}
