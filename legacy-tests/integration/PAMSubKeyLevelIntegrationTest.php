<?php

require_once('../../legacy/Pubnub.php');
require_once('../../legacy/PubnubPAM.php');
require_once('TestCase.php');

class PAMSubKeyLevelIntegrationTest extends TestCase
{
    /** @var  PubnubPAM */
    protected $pam;
    /** @var  Pubnub */
    protected $pubnub_secret;
    protected $channel;

    protected static $publish = 'pub-c-81d9633a-c5a0-4d6c-9600-fda148b61648';
    protected static $subscribe = 'sub-c-35ffee42-e763-11e3-afd8-02ee2ddab7fe';
    protected static $secret = 'sec-c-NDNlODA0ZmItNzZhMC00OTViLWI5NWMtM2M4MzA4ZWM2ZjIz';
    protected static $pnsdk = 'Pubnub-PHP/3.6.0';
    protected static $access_key = 'abcd';
    protected static $message = 'hello from grant() test';

    public function setUp()
    {
        parent::setUp();

        $this->channel = 'pubnub_php_test_pam_' . phpversion() . time();

        $this->pubnub_secret = new Pubnub(array(
            'subscribe_key' => self::$subscribe,
            'publish_key' => self::$publish,
            'secret_key' => self::$secret
        ));

        $this->pubnub_secret->revoke();

        sleep(5);
    }

    /**
     * @group pam
     * @group pam-sub-key
     */
    public function testGrantNoReadNoWrite()
    {
        $this->pubnub_secret->grant(0, 0);

        $response = $this->pubnub_secret->audit();

        $this->assertEquals('0', $response['payload']['r']);
        $this->assertEquals('0', $response['payload']['w']);
        $this->assertEquals('subkey', $response['payload']['level']);
    }

    /**
     * @group pam
     * @group pam-sub-key
     */
    public function testGrantReadNoWrite()
    {
        $this->pubnub_secret->grant(1, 0);

        $response = $this->pubnub_secret->audit();

        $this->assertEquals('1', $response['payload']['r']);
        $this->assertEquals('0', $response['payload']['w']);
        $this->assertEquals('subkey', $response['payload']['level']);
    }

    /**
     * @group pam
     * @group pam-sub-key
     */
    public function testGrantNoReadWrite()
    {
        $this->pubnub_secret->grant(0, 1);

        $response = $this->pubnub_secret->audit();

        $this->assertEquals('0', $response['payload']['r']);
        $this->assertEquals('1', $response['payload']['w']);
        $this->assertEquals('subkey', $response['payload']['level']);
    }

    /**
     * @group pam
     * @group pam-sub-key
     */
    public function testGrantReadWrite()
    {
        $this->pubnub_secret->grant(1, 1);

        $response = $this->pubnub_secret->audit();

        $this->assertEquals('1', $response['payload']['r']);
        $this->assertEquals('1', $response['payload']['w']);
        $this->assertEquals('subkey', $response['payload']['level']);
    }

    /**
     * @group pam
     * @group pam-sub-key
     */
    public function testRevoke()
    {
        $this->pubnub_secret->grant(1, 1);

        $response = $this->pubnub_secret->revoke();

        $this->assertEquals('0', $response['payload']['w']);
        $this->assertEquals('0', $response['payload']['r']);
        $this->assertEquals('subkey', $response['payload']['level']);
    }
}