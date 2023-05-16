<?php
require_once __DIR__.'/../vendor/autoload.php';

use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;
use Elastic\Elasticsearch\ClientBuilder;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Engine;


$schema = new Schema([
    'blog' => new Index('blog', [
        'id' => new Field\IdentifierField('id'),
        'title' => new Field\TextField('title'),
        'description' => new Field\TextField('description'),
        'tags' => new Field\TextField('tags', multiple: true, filterable: true),
    ]),
]);

$client = ClientBuilder::create()->setHosts([
    '127.0.0.1:9200'
])->build();

$engine = new Engine(
    new ElasticsearchAdapter($client),
    $schema,
);

function dropIndex() {
    global $engine;
    global $schema;
    $engine->dropIndex('blog');
    $engine->dropSchema([$schema]);
}

function createSchemaAndIndex()
{
    global $engine;
    //$engine->createSchema();
    // create specific index
    $engine->createIndex('blog');
    var_dump('schema created');
}


// saving docs
function saveDocs() {
    global $engine;
    $engine->saveDocument('blog', [
        'id' => 1,
        'title' => 'العربية تجمعنا',
        'description' => 'نعم العربية تجمعنا كلنا',
        'tags' => ['اللغة', 'العربية'],
    ]);

    $engine->saveDocument('blog', [
        'id' => 2,
        'title' => 'John Doe',
        'description' => 'This is the description of my first blog post',
        'tags' => ['UI', 'UX'],
    ]);

    $engine->saveDocument('blog', [
        'id' => 3,
        'title' => 'My fifth blog post',
        'content' => 'This is the description of my second blog post',
        'tags' => ['Tech', 'UX'],
    ]);

    $engine->saveDocument('blog', [
        'id' => 4,
        'title' => 'My sixth blog post',
        'content' => 'This is the description of my third blog post',
        'tags' => ['Tech', 'UI'],
    ]);
    var_dump('docs saved');
}


// search docs
function searchDocs() {
    global $engine;
    $result = $engine->createSearchBuilder()
        ->addIndex('blog')
//        ->addFilter(new \Schranz\Search\SEAL\Search\Condition\SearchCondition('john'))
        ->addFilter(new \Schranz\Search\SEAL\Search\Condition\SearchCondition('jon'))
        //->addFilter(new \Schranz\Search\SEAL\Search\Condition\SearchCondition('العربية'))
        //->addFilter(new \Schranz\Search\SEAL\Search\Condition\EqualCondition('tags', 'Tech'))
    ->getResult();

    foreach ($result as $document) {
        var_dump($document);
        // do something with the document
    }

    $total = $result->total();
    var_dump($total);
}
//dropIndex();
createSchemaAndIndex();
//saveDocs();
//searchDocs();
