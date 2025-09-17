<?php

/**
 * Written By Davod Saraei
 */

namespace App\Services\SMS;

use App\Jobs\ClearMessageJob;
use App\Models\ShortMessage;
use App\Services\SMS\Contracts\SMSDriverContract;
use Illuminate\Container\Container;

/**
 * AbstractDriver class
 */
abstract class AbstractSMSDriver implements SMSDriverContract
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * driver configurations
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     */
    public function __construct(Container $app, $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Send a message to the phone
     *
     * @param  int  $phone
     * @param  string  $message
     * @return void
     */
    abstract public function send($phone, $message, $sms_pattern = null);

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(string $phone)
    {
        return ShortMessage::where('to', '=', $phone)->get();
    }

    /**
     * Summary of save
     *
     * @param  mixed  $phone
     * @param  mixed  $message
     * @param  mixed  $pattern
     * @return void
     */
    public function save($phone, $message, $pattern = null)
    {
        ShortMessage::create([
            'content' => $message,
            'to' => $phone,
            'handler' => $this->getDirverName(),
            'pattern' => $pattern,
            'meta' => [
                'pattern' => $pattern,
            ],
        ]);
    }

    public function clear($to)
    {
        dispatch_sync(new ClearMessageJob($to));
    }

    public function getDirverName()
    {
        return explode('\\', get_class($this))[1];
    }
}
