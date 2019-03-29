<?php

use mrcrmn\Http2Push\Http2Push;
use mrcrmn\Http2Push\Http2PushServiceProvider;

class Http2PushTest extends Orchestra\Testbench\TestCase
{
    /**
     * Assert that blade markup and view data render HTML markup.
     *
     * @param string $expectedHtml
     * @param string $viewContent blade markup
     * @return void
     */
    protected function assertBladeRenders($expectedHtml, $viewContent)
    {
        $blade = $this->app->get('blade.compiler');
        $this->assertEquals($expectedHtml, $blade->compileString($viewContent));
    }

    protected function getPackageProviders($app)
    {
        return [
            Http2PushServiceProvider::class
        ];
    }

    public function test_it_is_registered()
    {
        $this->assertInstanceOf(Http2Push::class, $this->app->get('http2push'));
    }

    public function test_resources_can_be_added()
    {
        $http2push = $this->app->get('http2push');

        $http2push->add('/css/app.css');

        $this->assertTrue($http2push->any());
    }

    public function test_the_header_gets_constructed_correctly()
    {
        $http2push = $this->app->get('http2push');

        $http2push->add('/css/app.css');
        $this->assertEquals('</css/app.css>; rel=preload; as=style', $http2push->generateHeader());

        $http2push->add('/js/app.js');
        $this->assertEquals('</css/app.css>; rel=preload; as=style, </js/app.js>; rel=preload; as=script', $http2push->generateHeader());
    }

    public function test_it_ignores_duplicates()
    {
        $http2push = $this->app->get('http2push');

        $http2push->add('/css/app.css');
        $http2push->add('/css/app.css');
        $this->assertEquals('</css/app.css>; rel=preload; as=style', $http2push->generateHeader());
    }

    public function test_the_blade_directive_works()
    {
        $http2push = $this->app->get('http2push');

        $this->assertBladeRenders(
            '<?php echo preload(asset("css/app.css"), false); ?>',
            '@preload(asset("css/app.css"))'
        );

        $this->assertEquals(config('app.url') . '/css/app.css', preload(asset("css/app.css"), false));
        $this->assertEquals('</css/app.css>; rel=preload; as=style', $http2push->generateHeader());
    }
}
