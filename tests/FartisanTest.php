<?php

namespace SanthoshKorukonda\Fartisan\Tests;

use App\City;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\HtmlString;
use SanthoshKorukonda\Fartisan\Bootstrap\Fartisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SanthoshKorukonda\Fartisan\Bootstrap\FartisanServiceProvider;

class FartisanTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            FartisanServiceProvider::class,
            'Collective\Html\HtmlServiceProvider',
        ];
    }
    protected function getPackageAliases($app)
    {
        return [
            "Fartisan" => Fartisan::class,
            'Form' => 'Collective\Html\FormFacade',
            'Html' => 'Collective\Html\HtmlFacade',
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        # Setup cache store for Fartisan
        $app['config']->set('cache.stores.fartisan', [
            'driver' => 'file',
            'path' => storage_path('fartisan/cache'),
        ]);

        # Setup Filesystems disk for Fartisan
        $app['config']->set('filesystems.disks.fartisan', [
            'driver' => 'local',
            'root' => storage_path('fartisan/views'),
        ]);

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', __DIR__.'/../../../../database/database.sqlite'),
            'prefix'   => '',
        ]);
    }

    /** @test */
    public function it_should_return_an_array_with_cached_form_filename()
    {
        factory(City::class, 10)->create([
            "stateId" => 1403
        ]);

        $schema = json_decode($this->getDummyJson())[0];
        $data = Fartisan::build($schema);

        $this->assertArrayHasKey("filename", $data);
        $filename = $data["filename"] . ".php";
        $this->assertFileExists(storage_path("fartisan/views/$filename"));
    }

    /** @test */
    public function it_should_return_an_instance_of_illuminate_htmlstring()
    {
        factory(City::class, 10)->create([
            "stateId" => 1403
        ]);

        $schema = json_decode($this->getDummyJson())[0];
        $data = Fartisan::buildHtml($schema);

        $this->assertInstanceOf(HtmlString::class, $data);
    }

    /** @test */
    public function it_should_return_a_string_of_php_syntax()
    {
        $filename = sha1("hash") . ".php";

        $expected = '<?= Fartisan::buildSelectOptions($' . $filename . '); ?>';
        $actual = Fartisan::compileOptions($filename);

        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function it_should_return_a_string_of_html_syntax()
    {
        $options = ["Hyderabad", "Visakhapatnam", "Amaravati"];

        $htmlString = "";
        foreach ($options as $key => $option) {
            $htmlString .= "<option value='$key'>$option</option>\n";
        }
        $actual = Fartisan::buildSelectOptions($options);

        $this->assertSame($htmlString, $actual);
    }

    protected function getDummyJson()
    {
        return '[{"attributes":{"url":"foo/bar","method":"POST","files":true,"id":"Enquiry","class":"form"},"components":[{"name":"bsText","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Email","text":"Email*","attributes":[]},"input":{"name":"Email","value":null,"attributes":[]}}},{"name":"bsFile","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":{"name":"Image","value":null,"attributes":[]}}},{"name":"bsSelectWithDb","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Units","text":"Units*","attributes":[]},"input":{"name":"Units","value":null,"attributes":[],"database":{"table":"Cities","columns":["Id","Name"],"where":{"StateId":1403},"uid":"Cities"}}}},{"name":"bsCheckbox","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":[{"name":"Email","text":"Male","value":"Male","checked":false,"attributes":[]},{"name":"Email","text":"Female","value":"Female","checked":true,"attributes":[]}]}},{"name":"bsRadio","options":{"row":{"start":true,"end":true},"wrapper":{"class":"col-md-4","attributes":[]},"label":{"for":"Image","text":"Image*","attributes":[]},"input":[{"name":"Email","text":"Male","value":"Male","checked":false,"attributes":[]},{"name":"Email","text":"Female","value":"Female","checked":true,"attributes":[]}]}},{"name":"bsButton","options":{"row":{"start":true,"end":false},"wrapper":{"start":true,"end":false,"options":{"class":"col-md-4","attributes":[]}},"input":{"value":"Submit","attributes":{"name":"Submit","type":"submit","class":"btn btn-primary"}}}},{"name":"bsButton","options":{"row":{"start":false,"end":true},"wrapper":{"start":false,"end":true,"options":{"class":"col-md-4","attributes":[]}},"input":{"value":"Reset","attributes":{"name":"Reset","type":"reset","class":"btn btn-default"}}}}]}]';
    }
}