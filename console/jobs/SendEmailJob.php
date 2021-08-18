<?php


namespace console\jobs;


use Exception;
use yii\base\BaseObject;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

/**
 * @property string		$view			The email's view is set as frontend\mail\{$view}.php
 * @property ?string	$params			Parameters for the view, serialized array
 * @property ?string	$to				Destination emails, imploded array, PHP_EOL separator
 * @property ?string	$cc				Carbon Copy emails, imploded array, PHP_EOL separator
 * @property ?string	$bcc			Blind Carbon Copy emails, imploded array, PHP_EOL separator
 * @property string		$from			The sender(s) of the email, imploded array, PHP_EOL separator
 * @property string		$subject		The subject of the email
 * @property ?string	$attachments	Any attachments, serialized 2D Array. Inner arrays must have 'content' and 'options' keys.
 */
class SendEmailJob extends BaseObject implements RetryableJobInterface
{
	/** The email's view is set as frontend\mail\{$view}.php */
	public string $view;

	/** Parameters for the view, serialized array */
	public ?string $params = null;

	/** Destination emails, imploded array, PHP_EOL separator */
	public ?string $to = null;

	/** Carbon Copy emails, imploded array, PHP_EOL separator */
	public ?string $cc = null;

	/** Blind Carbon Copy emails, imploded array, PHP_EOL separator */
	public ?string $bcc = null;

	/** The sender(s) of the email, imploded array, PHP_EOL separator */
	public string $from;

	/** The subject of the email */
	public string $subject;

	/** Any attachments, serialized 2D Array. Inner arrays must have 'content' and 'options' keys. */
	public ?string $attachments = null;

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function execute($queue)
	{
		if(is_null($this->to) && is_null($this->cc) && is_null($this->bcc))
		{
			throw new Exception('Email needs a destination.');
		}

		$mailer = \Yii::$app->mailer;
		$mailer->viewPath = '@frontend/views/mail';
		$mailer->getView()->theme = \Yii::$app->view->theme;
		$message = $mailer->compose(['html' => $this->view], unserialize($this->params))
			->setFrom(explode(separator: PHP_EOL, string: $this->from))
			->setSubject($this->subject);

		if (!is_null($this->to )) $message->setTo (explode(separator: PHP_EOL, string: $this->to ));
		if (!is_null($this->cc )) $message->setCc (explode(separator: PHP_EOL, string: $this->cc ));
		if (!is_null($this->bcc)) $message->setBcc(explode(separator: PHP_EOL, string: $this->bcc));

		if (!is_null($this->attachments)) {
			foreach (unserialize($this->attachments) as $attachment)
			{
				$message->attachContent(content: $attachment['content'], options: $attachment['options']);
			}
		}

		$message->send();
	}

	/**
	 * @inheritDoc
	 */
	public function getTtr()
	{
		return 5 * 60;
	}

	/**
	 * @inheritDoc
	 */
	public function canRetry($attempt, $error)
	{
		return ($attempt < 5);
	}
}