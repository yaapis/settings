<?php namespace Settings;

use Yaap\Settings\Settings;


class SettingsTest extends SettingsTestCase {

	protected $useDatabase = true;

	public $settings;


	public function setUp() {

		parent::setUp();
        $this->settings = new Settings('settings');
        $this->settings->clear();

	}

	public function testPut()
    {
        $this->settings->put('testCase.foo', 'bar');
        $this->assertTrue($this->settings->has('testCase.foo'));
        $this->assertEquals('bar', $this->settings->get('testCase.foo'));
        $this->assertEquals(array('foo' => 'bar'), $this->settings->get('testCase'));

        $this->settings->put('a.b', 'c');
        $this->assertTrue($this->settings->has('a'));
        $this->assertEquals(array('b' => 'c'), $this->settings->get('a'));

        $this->settings->clear();
        $this->settings->put('', 'FOOBAR');
        $this->assertEquals('FOOBAR',$this->settings->get(''));

        $this->settings->put('1.2.3.4.5.6.7.8', 'f');
        $this->assertTrue($this->settings->has('1.2.3.4'));

        $this->settings->put('1.2.3.4.5.6.7.8.', 'f');
        $this->assertTrue($this->settings->has('1.2.3.4.5.6.7.8.'));
        $this->assertEquals('f',$this->settings->get('1.2.3.4.5.6.7.8.'));


	    //save and reload settings

	    $this->settings->save();
	    $this->settings->load();
	    $this->assertTrue($this->settings->has('1.2.3.4.5.6.7.8.'));


    }

	public function testSet()
    {

	    /*$app = $this->createApplication();

	    $app['config']->set('', 'FOOBAR');
	    $app['config']->set('1.2.3.4.5.6.7.8', 'f');
	    $app['config']->set('1.2.3.4.5.6.7.8.', 'f');

	    var_dump($app['config']->has('1.2.3.4')); //true
	    var_dump($app['config']->get('')); //FOOBAR
	    var_dump($app['config']->has('1.2.3.4.5.6.7.8.')); // true
	    var_dump($app['config']->get('1.2.3.4.5.6.7.8.')); // f
	    */




	    $this->settings->set('testCase.foo', 'bar');
	    $this->settings->set('a.b', 'c');

	    $this->settings->load();

	    $this->assertTrue($this->settings->has('testCase.foo'));
        $this->assertEquals('bar', $this->settings->get('testCase.foo'));
        $this->assertEquals(array('foo' => 'bar'), $this->settings->get('testCase'));

        $this->assertTrue($this->settings->has('a'));
        $this->assertEquals(array('b' => 'c'), $this->settings->get('a'));

        $this->settings->clear();

        $this->settings->set('', 'FOOBAR');
        $this->settings->set('1.2.3.4.5.6.7.8', 'f');
	    $this->settings->set('1.2.3.4.5.6.7.8.', 'f');

	    $this->settings->load();

        $this->assertTrue($this->settings->has('1.2.3.4'));
	    $this->assertEquals('FOOBAR',$this->settings->get(''));
        $this->assertTrue($this->settings->has('1.2.3.4.5.6.7.8.'));
        $this->assertEquals('f',$this->settings->get('1.2.3.4.5.6.7.8.'));

    }

	public function testForget()
    {
        $this->settings->set('a.b.c.d.e', 'f');
        $this->settings->forget('a.b.c');
        $this->assertFalse($this->settings->has('a.b.c'));

        $this->settings->set('1.2.3.4.5.6', 'f');
        $this->settings->forget('1.2.3.4.5');
        $this->assertFalse($this->settings->has('1.2.3.4.5.6'));
        $this->assertTrue($this->settings->has('1.2.3.4'));

        $this->settings->set('1.2.3.4.5.6.', 'f');
        $this->settings->forget('1.2.3.4.5.6.');
        $this->assertFalse($this->settings->has('1.2.3.4.5.6.'));
        $this->assertTrue($this->settings->has('1.2.3.4.5'));
    }

    public function testUnicode()
    {
        $this->settings->set('a', 'Hälfte');
        $this->settings->set('b', 'Höfe');
        $this->settings->set('c', 'Hüfte');
        $this->settings->set('d', 'saß');

	    $this->settings->load();

        $this->assertEquals('Hälfte', $this->settings->get('a'));
        $this->assertEquals('Höfe', $this->settings->get('b'));
        $this->assertEquals('Hüfte', $this->settings->get('c'));
        $this->assertEquals('saß', $this->settings->get('d'));
    }

    public function testSetArray(){
	    $this->settings->clear();
        $array = array(
            'id' => "foo",
            'user_info' => array(
                'username' => "bar",
                'recently_viewed' => 1
            )
        );
        $this->settings->setArray($array);
	    $this->settings->load();

        $this->assertEquals($array, $this->settings->get());

    }



}