<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Log
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Log\Writer;

/**
 * Mail log writer class
 *
 * @category   Pop
 * @package    Pop_Log
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Mail implements WriterInterface
{

    /**
     * Array of emails in which to send the log messages
     * @var array
     */
    protected $emails = array();

    /**
     * Constructor
     *
     * Instantiate the Mail writer object.
     *
     * @param  array $emails
     * @throws Exception
     * @return \Pop\Log\Writer\Mail
     */
    public function __construct(array $emails)
    {
        if (count($emails) == 0) {
            throw new Exception('Error: There must be at least one email address passed.');
        }

        foreach ($emails as $key => $value) {
            if (!is_numeric($key)) {
                $this->emails[] = array(
                    'name'  => $key,
                    'email' => $value
                );
            } else {
                $this->emails[] = array(
                    'email' => $value
                );
            }
        }
    }

    /**
     * Method to write to the log
     *
     * @param  array $logEntry
     * @param  array $options
     * @return \Pop\Log\Writer\Mail
     */
    public function writeLog(array $logEntry, array $options = array())
    {
        $subject = (isset($options['subject'])) ?
            $options['subject'] :
            'Log Entry:';

        $subject .= ' ' . $logEntry['name'] . ' (' . $logEntry['priority'] . ')';

        $mail = new \Pop\Mail\Mail($subject, $this->emails);
        if (isset($options['headers'])) {
            $mail->setHeaders($options['headers']);
        }

        $entry = implode("\t", $logEntry) . PHP_EOL;

        $mail->setText($entry)
             ->send();

        return $this;
    }

}
