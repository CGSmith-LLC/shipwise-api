<?php


namespace console\jobs;


use Exception;
use yii\base\BaseObject;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

/**
 * @property string					$view			The email's view is set as frontend\mail\{$view}.php
 * @property ?array					$params			Parameters for the view
 * @property string[]|string		$to				Destination emails
 * @property string[]|string|null	$cc				Carbon Copy emails
 * @property string[]|string|null	$bcc			Blind Carbon Copy emails, imploded array, PHP_EOL separator
 * @property string[]|string		$from			The sender(s) of the email, imploded array, PHP_EOL separator
 * @property string					$subject		The subject of the email
 * @property ?array					$attachments	Any attachments, 2D Array. Inner arrays must have 'content' and 'options' keys.
 *
 * Example Call:
 *	\Yii::$app->queue->push(new SendEmailJob([
 *		'view' => 'layouts/html',
 *		'params' => [
 *			'content' => $content,
 *		],
 *		'to' => 'ceo@corporation.net',
 * 		'cc' => ['cfo@corporation.net', 'coo@corporation.net', 'cid@corporation.net']
 *		'bcc' => 'admin@company.com',
 *		'from' => 'sender@company.com',
 *		'subject' => 'Test CSV',
 *		'attachments' => [[
 *			'content' => 'data,data,data,data' . PHP_EOL . 'data,data,data,data',
 *			'options' => ['fileName' => "test.csv", 'contentType' => 'application/csv'],
 *		]],
 *	]));
 */
class SendEmailJob extends BaseObject implements RetryableJobInterface
{
	public string				$view;
	public ?array				$params = null;
	public array|string			$to;
	public array|string|null	$cc = null;
	public array|string|null	$bcc = null;
	public array|string			$from;
	public string				$subject;
	public ?array				$attachments = null;

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function execute($queue)
	{
		$mailer = \Yii::$app->mailer;
		$mailer->viewPath = '@frontend/views/mail';
		$mailer->getView()->theme = \Yii::$app->view->theme;
		$message = $mailer->compose(['html' => $this->view], $this->params)
			->setFrom($this->from)
			->setSubject($this->subject);

		if (!is_null($this->to )) $message->setTo ($this->to );
		if (!is_null($this->cc )) $message->setCc ($this->cc );
		if (!is_null($this->bcc)) $message->setBcc($this->bcc);

		if (!is_null($this->attachments)) {
			foreach ($this->attachments as $attachment)
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