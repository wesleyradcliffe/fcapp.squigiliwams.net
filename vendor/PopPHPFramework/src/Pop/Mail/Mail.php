<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Mail
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Mail;

/**
 * Mail class
 *
 * @category   Pop
 * @package    Pop_Mail
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Mail
{

    /**
     * CRLF EOL constant
     * @var string
     */
    const CRLF = "\r\n";

    /**
     * LF EOL constant
     * @var string
     */
    const LF = "\n";

    /**
     * Sending queue
     * @var \Pop\Mail\Queue
     */
    protected $queue = null;

    /**
     * Mail headers
     * @var array
     */
    protected $headers = array();

    /**
     * Subject
     * @var string
     */
    protected $subject = null;

    /**
     * Message body
     * @var \Pop\Mail\Message
     */
    protected $message = null;

    /**
     * Mail parameters
     * @var string
     */
    protected $params = null;

    /**
     * File attachments
     * @var array
     */
    protected $attachments = array();

    /**
     * Send as group flag
     * @var boolean
     */
    protected $group = false;

    /**
     * Constructor
     *
     * Instantiate the mail object.
     *
     * @param  string $subj
     * @param  mixed  $rcpts
     * @return \Pop\Mail\Mail
     */
    public function __construct($subj = null, $rcpts = null)
    {
        $this->subject = $subj;
        $this->queue = new Queue();
        $this->message = new Message($this);

        if (null !== $rcpts) {
            $this->addRecipients($rcpts);
        }
    }

    /**
     * Get the mail queue
     *
     * @return \Pop\Mail\Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Get the mail message
     *
     * @return \Pop\Mail\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the mail header
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the mail header
     *
     * @param  string $name
     * @return string
     */
    public function getHeader($name)
    {
        return (isset($this->headers[$name])) ? $this->headers[$name] : null;
    }

    /**
     * Get the subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get MIME boundary
     *
     * @return string
     */
    public function getBoundary()
    {
        return $this->message->getBoundary();
    }

    /**
     * Get EOL
     *
     * @return string
     */
    public function getEol()
    {
        return $this->message->getEol();
    }

    /**
     * Get character set
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->message->getCharset();
    }

    /**
     * Get text part of the message.
     *
     * @return string
     */
    public function getText()
    {
        return $this->message->getText();
    }

    /**
     * Get HTML part of the message.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->message->getHtml();
    }

    /**
     * Get attachments
     *
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Alias to add a recipient to the queue
     *
     * @param  string $email
     * @param  string $name
     * @return \Pop\Mail\Mail
     */
    public function to($email, $name = null)
    {
        $this->queue->add($email, $name);
        return $this;
    }

    /**
     * Add a recipient to the queue
     *
     * @param  string $email
     * @param  string $name
     * @return \Pop\Mail\Mail
     */
    public function add($email, $name = null)
    {
        $this->queue->add($email, $name);
        return $this;
    }

    /**
     * Add recipients to the queue
     *
     * @param  mixed $rcpts
     * @throws Exception
     * @return \Pop\Mail\Mail
     */
    public function addRecipients($rcpts)
    {
        $this->queue->addRecipients($rcpts);
        return $this;
    }

    /**
     * Alias to set the from and reply-to headers
     *
     * @param  string  $email
     * @param  string  $name
     * @param  boolean $replyTo
     * @return \Pop\Mail\Mail
     */
    public function from($email, $name = null, $replyTo = true)
    {
        $header = (null !== $name) ? $name . ' <' . $email . '>' : $email;
        $this->setHeader('From', $header);
        if ($replyTo) {
            $this->setHeader('Reply-To', $header);
        }

        return $this;
    }

    /**
     * Alias to set the reply-to and from headers
     *
     * @param  string  $email
     * @param  string  $name
     * @param  boolean $from
     * @return \Pop\Mail\Mail
     */
    public function replyTo($email, $name = null, $from = true)
    {
        $header = (null !== $name) ? $name . ' <' . $email . '>' : $email;
        $this->setHeader('Reply-To', $header);
        if ($from) {
            $this->setHeader('From', $header);
        }

        return $this;
    }

    /**
     * Alias to set the cc headers
     *
     * @param  string  $email
     * @param  string  $name
     * @return \Pop\Mail\Mail
     */
    public function cc($email, $name = null)
    {
        if (is_array($email)) {
            $ccQueue = new Queue($email);
            $header = (string)$ccQueue;
        } else {
            $header = (null !== $name) ? $name . ' <' . $email . '>' : $email;
        }
        $this->setHeader('Cc', $header);

        return $this;
    }

    /**
     * Alias to set the bcc headers
     *
     * @param  string  $email
     * @param  string  $name
     * @return \Pop\Mail\Mail
     */
    public function bcc($email, $name = null)
    {
        if (is_array($email)) {
            $bccQueue = new Queue($email);
            $header = (string)$bccQueue;
        } else {
            $header = (null !== $name) ? $name . ' <' . $email . '>' : $email;
        }
        $this->setHeader('Bcc', $header);

        return $this;
    }

    /**
     * Set a mail header
     *
     * @param  string $name
     * @param  string $value
     * @throws Exception
     * @return \Pop\Mail\Mail
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Set mail headers
     *
     * @param  array $headers
     * @throws Exception
     * @return \Pop\Mail\Mail
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     * Set the subject
     *
     * @param  string $subj
     * @return \Pop\Mail\Mail
     */
    public function setSubject($subj)
    {
        $this->subject = $subj;
        return $this;
    }

    /**
     * Set MIME boundary
     *
     * @param  string $bnd
     * @return \Pop\Mail\Mail
     */
    public function setBoundary($bnd = null)
    {
        $this->message->setBoundary($bnd);
        return $this;
    }

    /**
     * Set EOL
     *
     * @param  string $eol
     * @return \Pop\Mail\Mail
     */
    public function setEol($eol = Mail::CRLF)
    {
        $this->message->setEol($eol);
        return $this;
    }

    /**
     * Set character set
     *
     * @param  string $chr
     * @return \Pop\Mail\Mail
     */
    public function setCharset($chr)
    {
        $this->message->setCharset($chr);
        return $this;
    }

    /**
     * Set text part of the message.
     *
     * @param  string $text
     * @return \Pop\Mail\Mail
     */
    public function setText($text)
    {
        $this->message->setText($text);
        return $this;
    }

    /**
     * Set HTML part of the message.
     *
     * @param  string $html
     * @return \Pop\Mail\Mail
     */
    public function setHtml($html)
    {
        $this->message->setHtml($html);
        return $this;
    }

    /**
     * Set the send as group flag
     *
     * @param  boolean $group
     * @return \Pop\Mail\Mail
     */
    public function sendAsGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * Attach a file to the mail object.
     *
     * @param  string $file
     * @throws Exception
     * @return \Pop\Mail\Mail
     */
    public function attachFile($file)
    {
        $this->attachments[] = new Attachment($file);
        return $this;
    }

    /**
     * Set parameters
     *
     * @param mixed $params
     * @return \Pop\Mail\Mail
     */
    public function setParams($params = null)
    {
        if (null === $params) {
            $this->params = null;
        } else if (is_array($params)) {
            foreach ($params as $value) {
                $this->params .= $value;
            }
        } else {
            $this->params .= $params;
        }

        return $this;
    }

    /**
     * Send mail message or messages.
     *
     * This method depends on the server being set up correctly as an SMTP server
     * and sendmail being correctly defined in the php.ini file.
     *
     * @return void
     */
    public function send()
    {
        if (null === $this->message->getMessage()) {
            $this->message->init();
        }

        $messageBody = $this->message->getMessage();

        $headers = $this->buildHeaders() . $this->message->getEol() . $this->message->getEol();

        // Send as group message
        if ($this->group) {
            mail((string)$this->queue, $this->subject, $messageBody, $headers, $this->params);
        // Else, Iterate through the queue and send the mail messages.
        } else {
            foreach ($this->queue as $rcpt) {
                $subject = $this->subject;
                $message = $messageBody;

                // Set the recipient parameter.
                $to = (isset($rcpt['name'])) ? $rcpt['name'] . " <" . $rcpt['email'] . ">" : $rcpt['email'];

                // Replace any set placeholder content within the subject or message.
                foreach ($rcpt as $key => $value) {
                    $subject =  str_replace('[{' . $key . '}]', $value, $subject);
                    $message =  str_replace('[{' . $key . '}]', $value, $message);
                }

                // Send the email message.
                mail($to, $subject, $message, $headers, $this->params);
            }
        }
    }

    /**
     * Save mail message or messages in a folder to be sent at a later date.
     *
     * @param string $to
     * @param string $format
     * @return \Pop\Mail\Mail
     */
    public function saveTo($to = null, $format = null)
    {
        $dir = (null !== $to) ? $to : getcwd();

        if (null === $this->message->getMessage()) {
            $this->message->init();
        }

        $messageBody = $this->message->getMessage();

        $headers = $this->buildHeaders();

        // Send as group message
        if ($this->group) {
            $email = 'To: ' . (string)$this->queue . $this->message->getEol() .
                'Subject: ' . $this->subject . $this->message->getEol() .
                $headers . $this->message->getEol() . $this->message->getEol() . $messageBody;

            $emailFileName = (null !== $format) ? $format : $emailFileName = '0000000001-' . time() . '-popphpmail';

            // Save the email message.
            file_put_contents($dir . DIRECTORY_SEPARATOR . $emailFileName, array(), $email);
        } else {
            // Iterate through the queue and send the mail messages.
            $i = 1;
            foreach ($this->queue as $rcpt) {
                $fileFormat = null;
                $subject = $this->subject;
                $message = $messageBody;

                // Set the recipient parameter.
                $to = (isset($rcpt['name'])) ? $rcpt['name'] . " <" . $rcpt['email'] . ">" : $rcpt['email'];

                // Replace any set placeholder content within the subject or message.
                foreach ($rcpt as $key => $value) {
                    $subject =  str_replace('[{' . $key . '}]', $value, $subject);
                    $message =  str_replace('[{' . $key . '}]', $value, $message);
                    if (null !== $format) {
                        if (null !== $fileFormat) {
                            $fileFormat = str_replace('[{' . $key . '}]', $value, $fileFormat);
                        } else {
                            $fileFormat = str_replace('[{' . $key . '}]', $value, $format);
                        }
                    }
                }

                $email = 'To: ' . $to . $this->message->getEol() .
                         'Subject: ' . $subject . $this->message->getEol() .
                         $headers . $this->message->getEol() . $this->message->getEol() . $message;

                if (null !== $fileFormat) {
                    $emailFileName = sprintf('%09d', $i) . '-' . time() . '-' . $fileFormat;
                } else {
                    $emailFileName = sprintf('%09d', $i) . '-' . time() . '-popphpmail';
                }

                // Save the email message.
                file_put_contents($dir . DIRECTORY_SEPARATOR . $emailFileName, $email);
                $i++;
            }
        }

        return $this;
    }

    /**
     * Send mail message or messages that are saved in a folder.
     *
     * This method depends on the server being set up correctly as an SMTP server
     * and sendmail being correctly defined in the php.ini file.
     *
     * @param string  $from
     * @param boolean $delete
     * @return \Pop\Mail\Mail
     */
    public function sendFrom($from = null, $delete = false)
    {
        $dir = (null !== $from) ? $from : getcwd();
        $emailDir = new \Pop\File\Dir($dir, true);
        $emailFiles = $emailDir->getFiles();
        if (isset($emailFiles[0])) {
            foreach ($emailFiles as $email) {
                if (file_exists($email)) {
                    // Get the email data from the contents
                    $emailData = $this->getEmailFromFile($email);

                    // Send the email message.
                    mail($emailData['to'], $emailData['subject'], $emailData['message'], $emailData['headers'], $this->params);

                    // Delete the email file is the flag is passed
                    if ($delete) {
                        unlink($email);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Build headers
     *
     * @return string
     */
    protected function buildHeaders()
    {
        $headers = null;
        foreach ($this->headers as $key => $value) {
            $headers .= (is_array($value)) ? $key . ": " . $value[0] . " <" . $value[1] . ">" . $this->message->getEol() : $key . ": " . $value . $this->message->getEol();
        }

        return $headers;
    }

    /**
     * Get email data from file
     *
     * @param  string $filename
     * @throws Exception
     * @return array
     */
    protected function getEmailFromFile($filename)
    {
        $contents = file_get_contents($filename);
        $email = array(
            'to'      => null,
            'subject' => null,
            'headers' => null,
            'message' => null
        );

        $headers = substr($contents, 0, strpos($contents, $this->message->getEol() . $this->message->getEol()));
        $email['message'] = trim(str_replace($headers, '', $contents));
        $email['headers'] = trim($headers) . $this->message->getEol() . $this->message->getEol();

        if (strpos($email['headers'], 'Subject:') === false) {
            throw new Exception("Error: There is no subject in the email file '" . $filename . "'.");
        }

        if (strpos($email['headers'], 'To:') === false) {
            throw new Exception("Error: There is no recipient in the email file '" . $filename . "'.");
        }

        $subject = substr($contents, strpos($contents, 'Subject:'));
        $subject = substr($subject, 0, strpos($subject, $this->message->getEol()));
        $email['headers'] = str_replace($subject . $this->message->getEol(), '', $email['headers']);
        $email['subject'] = trim(substr($subject . $this->message->getEol(), (strpos($subject, ':') + 1)));

        $to = substr($contents, strpos($contents, 'To:'));
        $to = substr($to, 0, strpos($to, $this->message->getEol()));
        $email['headers'] = str_replace($to . $this->message->getEol(), '', $email['headers']);

        preg_match('/[a-zA-Z0-9\.\-\_+%]+@[a-zA-Z0-9\-\_\.]+\.[a-zA-Z]{2,4}/', $to, $result);

        if (!isset($result[0])) {
            throw new Exception("Error: An valid email could not be parsed from the email file '" . $filename . "'.");
        } else {
            $email['to'] = $result[0];
        }

        return $email;
    }

}
